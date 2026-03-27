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
      EXISTS(
        SELECT 1 FROM appointments a
        JOIN appointment_status s ON s.status_id = a.status_id
        WHERE a.table_id = t.table_id 
        AND a.appointment_date = :floor_date
        AND :is_today = 1
        AND a.start_time <= CURTIME() 
        AND a.end_time > CURTIME()
        AND s.status_name = 'confirmed'
      ) AS is_active_now
      FROM `tables` t");
    $stmt->execute([
        ':floor_date' => $floorDate,
        ':is_today' => $isToday,
    ]);
    $rows = $stmt->fetchAll() ?: [];
    $stmt->closeCursor();

    $scheduleStmt = $pdo->prepare("SELECT
        a.appointment_id,
        CONCAT(u.first_name, ' ', u.last_name) AS guest_name,
        dz.zone_name,
        COALESCE(t.seating_preference, 'Unassigned') AS seating_preference,
        a.party_size,
        a.start_time,
        a.end_time,
        DATE_FORMAT(a.start_time, '%l:%i %p') AS start_time_label,
        DATE_FORMAT(a.end_time, '%l:%i %p') AS end_time_label,
        st.status_name
      FROM appointments a
      JOIN users u ON u.user_id = a.user_id
      JOIN appointment_status st ON st.status_id = a.status_id
      LEFT JOIN `tables` t ON t.table_id = a.table_id
      LEFT JOIN dining_zones dz ON dz.zone_id = COALESCE(a.zone_id, t.zone_id)
      WHERE a.appointment_date = :schedule_date
        AND st.status_name IN ('pending', 'confirmed')
      ORDER BY a.start_time ASC, dz.zone_name ASC, seating_preference ASC, guest_name ASC");
    $scheduleStmt->execute([':schedule_date' => $floorDate]);
    $schedule = $scheduleStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    $scheduleStmt->closeCursor();

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
        } else {
            $status = 'available';
        }

        $tables[] = [
            'table_id' => (int) $r['table_id'],
            'zone_id' => (int) $r['zone_id'],
            'status' => $status,
        ];

        if (!isset($zoneStats[$r['zone_id']])) {
            $zoneStats[$r['zone_id']] = ['available' => 0, 'occupied' => 0];
        }
        $zoneStats[$r['zone_id']][$status] += 1;
    }

    header('Content-Type: application/json');
    echo json_encode(['tables' => $tables, 'zones' => $zoneStats, 'schedule' => $schedule]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => admin_booking_error_message($e)]);
}


