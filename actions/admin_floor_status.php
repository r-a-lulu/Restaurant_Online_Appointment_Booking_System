<?php
if (!defined('FRONT_CONTROLLER')) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}
require_once __DIR__ . '/../includes/security.php';
start_secure_session();
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit();
}

$csrf = $_POST['csrf_token'] ?? '';
if (!verify_csrf($csrf)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid CSRF token.']);
    exit();
}

$floorDate = trim((string) ($_POST['floor_date'] ?? date('Y-m-d')));
$dateObj = DateTime::createFromFormat('Y-m-d', $floorDate);
$dateErrors = DateTime::getLastErrors();
if (!$dateObj || (is_array($dateErrors) && (($dateErrors['warning_count'] ?? 0) > 0 || ($dateErrors['error_count'] ?? 0) > 0))) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid floor date.']);
    exit();
}
$floorDate = $dateObj->format('Y-m-d');
$isToday = ($floorDate === date('Y-m-d')) ? 1 : 0;

try {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT t.table_id, t.zone_id, t.current_status, t.manual_status_until,
      -- Check if there's an active appointment right now
      EXISTS(
        SELECT 1 FROM appointments a
        JOIN appointment_status s ON s.status_id = a.status_id
        WHERE a.table_id = t.table_id 
        AND a.appointment_date = :floor_date
        AND :is_today = 1
        AND a.start_time <= CURTIME() 
        AND a.end_time > CURTIME()
        AND s.status_name = 'confirmed'
      ) AS is_active_now,
      -- Check if table has a reservation on the selected floor date
      EXISTS(
        SELECT 1 FROM appointments a2
        JOIN appointment_status s2 ON s2.status_id = a2.status_id
        WHERE a2.table_id = t.table_id 
        AND a2.appointment_date = :floor_date_reserved
        AND s2.status_name IN ('pending','confirmed')
        AND (:is_today_reserved = 0 OR a2.start_time > CURTIME())
      ) AS is_reserved_later
      FROM `tables` t");
    $stmt->execute([
        ':floor_date' => $floorDate,
        ':is_today' => $isToday,
        ':floor_date_reserved' => $floorDate,
        ':is_today_reserved' => $isToday,
    ]);
    $rows = $stmt->fetchAll() ?: [];
    $stmt->closeCursor();

    $tables = [];
    $zoneStats = [];
    foreach ($rows as $r) {
        $manualOccupiedActive = $isToday === 1
            && ($r['current_status'] ?? '') === 'occupied'
            && !empty($r['manual_status_until'])
            && strtotime((string) $r['manual_status_until']) > time();

        if ((int) $r['is_active_now'] === 1) {
            $status = 'occupied';
        } elseif ($manualOccupiedActive) {
            $status = 'occupied';
        } elseif ((int) $r['is_reserved_later'] === 1) {
            $status = 'reserved';
        } else {
            $status = 'available';
        }

        $tables[] = [
            'table_id' => (int) $r['table_id'],
            'zone_id' => (int) $r['zone_id'],
            'status' => $status,
        ];

        if (!isset($zoneStats[$r['zone_id']])) {
            $zoneStats[$r['zone_id']] = ['available' => 0, 'reserved' => 0, 'occupied' => 0];
        }
        $zoneStats[$r['zone_id']][$status] += 1;
    }

    header('Content-Type: application/json');
    echo json_encode(['tables' => $tables, 'zones' => $zoneStats]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => admin_booking_error_message($e)]);
}
