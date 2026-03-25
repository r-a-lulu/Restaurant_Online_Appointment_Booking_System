<?php
if (!defined('FRONT_CONTROLLER')) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}
require_once __DIR__ . '/../includes/security.php';
start_secure_session();
require_booking_open(true);
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

    $tableSql = "SELECT t.table_id
      FROM `tables` t
      WHERE t.capacity >= :party_size
        AND (t.current_status IS NULL OR t.current_status = 'available')";
    $tableParams = [
        ':party_size' => $partySize > 0 ? $partySize : 1,
    ];

    if ($tableId) {
        $tableSql .= ' AND t.table_id = :table_id';
        $tableParams[':table_id'] = $tableId;
    } else {
        $tableSql .= ' AND t.zone_id = :zone_id';
        $tableParams[':zone_id'] = $zoneId;
    }

    if ($seatingPref !== '') {
        $tableSql .= ' AND t.seating_preference = :seating_pref';
        $tableParams[':seating_pref'] = $seatingPref;
    }

    $tableSql .= ' ORDER BY t.capacity ASC, t.seating_preference ASC';

    $tableStmt = $pdo->prepare($tableSql);
    $tableStmt->execute($tableParams);
    $candidateTables = $tableStmt->fetchAll(PDO::FETCH_COLUMN);
    $tableStmt->closeCursor();

    $availability = [];
    $assignedTables = [];

    if (empty($candidateTables)) {
        foreach ($timeSlots as $time) {
            $availability[$time] = false;
            $assignedTables[$time] = null;
        }

        header('Content-Type: application/json');
        echo json_encode(['availability' => $availability, 'assigned_tables' => $assignedTables]);
        exit();
    }

    $placeholders = [];
    $conflictParams = [':appointment_date' => $appointmentDate];
    foreach ($candidateTables as $index => $candidateTableId) {
        $ph = ':table_' . $index;
        $placeholders[] = $ph;
        $conflictParams[$ph] = (int) $candidateTableId;
    }

    $conflictSql = "SELECT a.table_id, a.start_time, a.end_time
      FROM appointments a
      WHERE a.appointment_date = :appointment_date
        AND a.table_id IN (" . implode(', ', $placeholders) . ")
        AND a.status_id IN (
          SELECT status_id FROM appointment_status WHERE status_name IN ('pending', 'confirmed')
        )";

    $conflictStmt = $pdo->prepare($conflictSql);
    $conflictStmt->execute($conflictParams);
    $conflicts = $conflictStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    $conflictStmt->closeCursor();

    $conflictsByTable = [];
    foreach ($conflicts as $conflict) {
        $conflictsByTable[(int) $conflict['table_id']][] = [
            'start' => substr((string) $conflict['start_time'], 0, 8),
            'end' => substr((string) $conflict['end_time'], 0, 8),
        ];
    }

    foreach ($timeSlots as $time) {
        $slotStart = $time . ':00';
        $slotEnd = date('H:i:s', strtotime($slotStart . ' +2 hours'));
        $matchedTableId = null;

        foreach ($candidateTables as $candidateTableId) {
            $candidateTableId = (int) $candidateTableId;
            $hasConflict = false;
            foreach ($conflictsByTable[$candidateTableId] ?? [] as $conflict) {
                if ($conflict['start'] < $slotEnd && $conflict['end'] > $slotStart) {
                    $hasConflict = true;
                    break;
                }
            }
            if (!$hasConflict) {
                $matchedTableId = $candidateTableId;
                break;
            }
        }

        $availability[$time] = $matchedTableId !== null;
        $assignedTables[$time] = $matchedTableId;
    }

    header('Content-Type: application/json');
    echo json_encode(['availability' => $availability, 'assigned_tables' => $assignedTables]);
} catch (PDOException $e) {

    header('Content-Type: application/json');
    echo json_encode(['error' => booking_error_message($e)]);
}

