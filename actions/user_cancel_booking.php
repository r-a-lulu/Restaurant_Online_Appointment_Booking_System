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
    set_flash('dash_error', 'Invalid security token.');
    redirect('../pages/dashboard/reservations.php');
}

$appointmentId = isset($_POST['appointment_id']) ? (int) $_POST['appointment_id'] : 0;
if ($appointmentId <= 0) {
    set_flash('dash_error', 'Invalid reservation.');
    redirect('../pages/dashboard/reservations.php');
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT appointment_id, status_id FROM appointments WHERE appointment_id = :id AND user_id = :user_id LIMIT 1');
    $stmt->execute([':id' => $appointmentId, ':user_id' => (int) $_SESSION['user_id']]);
    $appt = $stmt->fetch();
    $stmt->closeCursor();

    if (!$appt) {
        set_flash('dash_error', 'Reservation not found.');
        redirect('../pages/dashboard/reservations.php');
    }

    $statusStmt = $pdo->prepare('SELECT status_name FROM appointment_status WHERE status_id = :id LIMIT 1');
    $statusStmt->execute([':id' => (int) $appt['status_id']]);
    $statusName = $statusStmt->fetchColumn();
    $statusStmt->closeCursor();

    if (!in_array($statusName, ['pending', 'confirmed'], true)) {
        set_flash('dash_error', 'This reservation cannot be cancelled.');
        redirect('../pages/dashboard/reservations.php');
    }

    $proc = $pdo->prepare('CALL sp_update_appointment_status(:appointment_id, :status_name)');
    $proc->execute([':appointment_id' => $appointmentId, ':status_name' => 'cancelled']);
    $proc->closeCursor();

    set_flash('dash_success', 'Reservation cancelled.');
    redirect('../pages/dashboard/reservations.php');
} catch (PDOException $e) {
    set_flash('dash_error', safe_error_message($e));
    redirect('../pages/dashboard/reservations.php');
}

