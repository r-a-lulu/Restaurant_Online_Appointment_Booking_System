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
$seatingPref = clean_string($_POST['seating_preference'] ?? '');
$partySize = isset($_POST['party_size']) && $_POST['party_size'] !== '' ? (int) $_POST['party_size'] : 0;

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
    
    // Call the stored procedure to get availability for all slots at once
    $stmt = $pdo->prepare('CALL sp_get_available_slots(:date, :zone_id, :party_size, :seating_pref)');
    $stmt->execute([
        ':date' => $appointmentDate,
        ':zone_id' => $zoneId,
        ':party_size' => $partySize,
        ':seating_pref' => $seatingPref
    ]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    $availability = [];
    foreach ($results as $row) {
        $time = substr($row['slot_time'], 0, 5); // Format 17:00:00 to 17:00
        $availability[$time] = (int)$row['is_available'] === 1;
    }

    header('Content-Type: application/json');
    echo json_encode(['availability' => $availability]);
} catch (PDOException $e) {

    header('Content-Type: application/json');
    echo json_encode(['error' => safe_error_message($e)]);
}

