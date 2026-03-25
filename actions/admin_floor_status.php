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

try {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT t.table_id, t.zone_id, t.current_status,
      -- Check if there's an active appointment right now
      EXISTS(
        SELECT 1 FROM appointments a 
        WHERE a.table_id = t.table_id 
        AND a.appointment_date = CURDATE()
        AND a.start_time <= CURTIME() 
        AND a.end_time > CURTIME()
        AND a.status_id IN (SELECT status_id FROM appointment_status WHERE status_name IN ('pending','confirmed'))
      ) AS is_active_now,
      -- Check if table has any future reservation today
      EXISTS(
        SELECT 1 FROM appointments a2 
        WHERE a2.table_id = t.table_id 
        AND a2.appointment_date = CURDATE()
        AND a2.start_time > CURTIME()
        AND a2.status_id IN (SELECT status_id FROM appointment_status WHERE status_name IN ('pending','confirmed'))
      ) AS is_reserved_later
      FROM `tables` t");
    $stmt->execute();
    $rows = $stmt->fetchAll() ?: [];
    $stmt->closeCursor();

    $tables = [];
    $zoneStats = [];
    foreach ($rows as $r) {
        // Same logic as floor.php: active now > manual status > reserved later > available
        if ((int) $r['is_active_now'] === 1) {
            $status = 'occupied';
        } elseif (!empty($r['current_status']) && $r['current_status'] !== 'available') {
            $status = $r['current_status'];
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
    echo json_encode(['error' => safe_error_message($e)]);
}
