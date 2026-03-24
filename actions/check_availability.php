<?php
if (!defined('FRONT_CONTROLLER')) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}
require_once __DIR__ . '/../includes/security.php';
start_secure_session();
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit();
}

$csrf = $_POST['csrf_token'] ?? '';
if (!verify_csrf($csrf)) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

$appointmentDate = clean_string($_POST['appointment_date'] ?? '');
$zoneId = isset($_POST['zone_id']) && $_POST['zone_id'] !== '' ? (int) $_POST['zone_id'] : null;
$tableId = isset($_POST['table_id']) && $_POST['table_id'] !== '' ? (int) $_POST['table_id'] : null;

if (!$appointmentDate || (!$zoneId && !$tableId)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing required data.']);
    exit();
}

$timeSlots = [
    '17:00', '17:30', '18:00', '18:30', '19:00', '19:30',
    '20:00', '20:30', '21:00', '21:30', '22:00'
];

try {
    $pdo = db();
    $availability = [];

    foreach ($timeSlots as $time) {
        $startDt = DateTime::createFromFormat('H:i', $time);
        if (!$startDt) {
            $availability[$time] = false;
            continue;
        }
        $endDt = clone $startDt;
        $endDt->modify('+2 hours');

        $stmt = $pdo->prepare('SELECT fn_is_slot_available(:date, :start_time, :end_time, :table_id, :zone_id, NULL) AS available');
        $stmt->execute([
            ':date' => $appointmentDate,
            ':start_time' => $startDt->format('H:i:s'),
            ':end_time' => $endDt->format('H:i:s'),
            ':table_id' => $tableId,
            ':zone_id' => $tableId ? null : $zoneId,
        ]);
        $row = $stmt->fetch();
        $stmt->closeCursor();

        $availability[$time] = isset($row['available']) ? (int) $row['available'] === 1 : false;
    }

    header('Content-Type: application/json');
    echo json_encode(['availability' => $availability]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => safe_error_message($e)]);
}

