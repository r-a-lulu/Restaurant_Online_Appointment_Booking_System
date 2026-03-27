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
    echo json_encode(['ok' => false, 'error' => 'Invalid CSRF token.']);
    exit();
}

$tableId = isset($_POST['table_id']) ? (int) $_POST['table_id'] : 0;
$appointmentId = isset($_POST['appointment_id']) && $_POST['appointment_id'] !== '' ? (int) $_POST['appointment_id'] : 0;
$floorDate = trim((string) ($_POST['floor_date'] ?? date('Y-m-d')));
$manualOverride = isset($_POST['manual_override']) && $_POST['manual_override'] === '1';

if ($tableId <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Invalid table selection.']);
    exit();
}

$dateObj = DateTime::createFromFormat('Y-m-d', $floorDate);
$dateErrors = DateTime::getLastErrors();
if (!$dateObj || (is_array($dateErrors) && (($dateErrors['warning_count'] ?? 0) > 0 || ($dateErrors['error_count'] ?? 0) > 0))) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Invalid floor date.']);
    exit();
}
$floorDate = $dateObj->format('Y-m-d');

try {
    $pdo = db();
    $dbClock = database_clock($pdo);

    if ($manualOverride) {
        if ($floorDate !== $dbClock['date']) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Manual occupied overrides can only be cleared for today.']);
            exit();
        }

        $stmt = $pdo->prepare('UPDATE `tables` SET current_status = :status, manual_status_until = NULL WHERE table_id = :id');
        $stmt->execute([
            ':status' => 'available',
            ':id' => $tableId,
        ]);
        $stmt->closeCursor();

        header('Content-Type: application/json');
        echo json_encode([
            'ok' => true,
            'mode' => 'manual',
            'message' => 'Manual occupied table has been cleared and is now available.',
        ]);
        exit();
    }

    if ($appointmentId <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'No reservation is attached to this occupied table.']);
        exit();
    }

    $lookup = $pdo->prepare('SELECT a.appointment_id, a.table_id, a.appointment_date, a.start_time, s.status_name
        FROM appointments a
        JOIN appointment_status s ON s.status_id = a.status_id
        WHERE a.appointment_id = :appointment_id
          AND a.table_id = :table_id
        LIMIT 1');
    $lookup->execute([
        ':appointment_id' => $appointmentId,
        ':table_id' => $tableId,
    ]);
    $appointment = $lookup->fetch(PDO::FETCH_ASSOC) ?: [];
    $lookup->closeCursor();

    if (empty($appointment)) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'The linked reservation could not be found.']);
        exit();
    }

    if (($appointment['status_name'] ?? '') !== 'confirmed') {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Only confirmed occupied reservations can be marked as no show.']);
        exit();
    }

    $appointmentStart = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', (string) $appointment['appointment_date'] . ' ' . (string) $appointment['start_time']);
    $now = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', (string) $dbClock['datetime']);
    if (!$appointmentStart || $appointmentStart > $now) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'You can only mark a reservation as no show after its start time has passed.']);
        exit();
    }

    $stmt = $pdo->prepare('CALL sp_update_appointment_status(:appointment_id, :status_name)');
    $stmt->execute([
        ':appointment_id' => $appointmentId,
        ':status_name' => 'no_show',
    ]);
    $stmt->closeCursor();

    header('Content-Type: application/json');
    echo json_encode([
        'ok' => true,
        'mode' => 'reservation',
        'message' => 'The occupied reservation has been marked as no show.',
        'appointment_id' => $appointmentId,
    ]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => admin_booking_error_message($e)]);
}
