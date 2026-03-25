<?php
/**
 * Admin Reserve Table — actions/admin_reserve_table.php
 */
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
    header('Location: ../pages/admin/floor.php');
    exit();
}

// Get form data
$userId = $_POST['user_id'] ?? '';
$firstName = clean_string($_POST['first_name'] ?? '');
$lastName = clean_string($_POST['last_name'] ?? '');
$email = clean_string($_POST['email'] ?? '');
$phone = clean_string($_POST['phone'] ?? '');
$tableId = (int) ($_POST['table_id'] ?? 0);
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$partySize = (int) ($_POST['party_size'] ?? 2);
$seatingPref = clean_string($_POST['seating_preference'] ?? '');

// Validation
$errors = [];
if (empty($firstName)) $errors[] = 'First name is required.';
if (empty($lastName)) $errors[] = 'Last name is required.';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if ($tableId <= 0) $errors[] = 'Please select a table.';
if (empty($date)) $errors[] = 'Date is required.';
if (empty($time)) $errors[] = 'Time is required.';
if ($partySize < 1 || $partySize > 20) $errors[] = 'Party size must be between 1-20.';

if (!empty($errors)) {
    set_flash('admin_error', implode(' ', $errors));
    header('Location: ../pages/admin/floor.php');
    exit();
}

try {
    $pdo = db();
    
    // If user_id is a number, use it; otherwise create as guest (user_id = NULL)
    $actualUserId = is_numeric($userId) ? (int) $userId : null;
    
    // Calculate end time (2 hours default)
    $startTime = $time;
    $endTime = date('H:i:s', strtotime($time . ' +2 hours'));
    
    // Get status_id for 'pending'
    $stmt = $pdo->prepare("SELECT status_id FROM appointment_status WHERE status_name = 'pending' LIMIT 1");
    $stmt->execute();
    $statusId = $stmt->fetchColumn();
    $stmt->closeCursor();
    
    if (!$statusId) {
        $statusId = 1; // Fallback
    }
    
    // Create the appointment
    $stmt = $pdo->prepare("INSERT INTO appointments 
        (user_id, table_id, appointment_date, start_time, end_time, party_size, seating_preference, status_id, guest_first_name, guest_last_name, guest_email, guest_phone, created_by_admin) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
    
    $stmt->execute([
        $actualUserId,
        $tableId,
        $date,
        $startTime,
        $endTime,
        $partySize,
        $seatingPref,
        $statusId,
        $firstName,
        $lastName,
        $email,
        $phone
    ]);
    
    $appointmentId = $pdo->lastInsertId();
    
    // Update table status to reserved
    $stmt = $pdo->prepare("UPDATE `tables` SET current_status = 'reserved' WHERE table_id = ?");
    $stmt->execute([$tableId]);
    
    set_flash('admin_success', 'Reservation created successfully for ' . e($firstName) . ' ' . e($lastName) . '.');
    
} catch (PDOException $e) {
    set_flash('admin_error', 'Error creating reservation: ' . safe_error_message($e));
}

header('Location: ../pages/admin/floor.php');
exit();
