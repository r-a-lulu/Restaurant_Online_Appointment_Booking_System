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
    set_flash('admin_error', 'Invalid security token.');
    redirect('pages/admin/reservations.php');
}

$appointmentId = isset($_POST['appointment_id']) ? (int) $_POST['appointment_id'] : 0;
$action = clean_string($_POST['action'] ?? '');

$map = [
    'approve' => 'confirmed',
    'reject' => 'cancelled',
    'cancel' => 'cancelled',
    'complete' => 'completed',
];

if ($appointmentId <= 0 || !isset($map[$action])) {
    set_flash('admin_error', 'Invalid request.');
    redirect('pages/admin/reservations.php');
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('CALL sp_update_appointment_status(:appointment_id, :status_name)');
    $stmt->execute([
        ':appointment_id' => $appointmentId,
        ':status_name' => $map[$action],
    ]);
    $stmt->closeCursor();

    set_flash('admin_success', 'Appointment updated successfully.');
    redirect('pages/admin/reservations.php');
} catch (PDOException $e) {
    set_flash('admin_error', admin_booking_error_message($e));
    redirect('pages/admin/reservations.php');
}

