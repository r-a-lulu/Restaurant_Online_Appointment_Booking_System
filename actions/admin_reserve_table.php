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
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Invalid security token.']);
        exit();
    }
    set_flash('admin_error', 'Invalid security token.');
    header('Location: ../pages/admin/floor.php');
    exit();
}

// Get form data
$userId = $_POST['user_id'] ?? '';
$zoneId = isset($_POST['zone_id']) && $_POST['zone_id'] !== '' ? (int) $_POST['zone_id'] : 0;
$tableId = isset($_POST['table_id']) && $_POST['table_id'] !== '' ? (int) $_POST['table_id'] : 0;
$seatingPref = clean_string($_POST['seating_preference'] ?? '');
$serviceId = isset($_POST['service_id']) && $_POST['service_id'] !== '' ? (int) $_POST['service_id'] : null;
$packageId = isset($_POST['event_package_id']) && $_POST['event_package_id'] !== '' ? (int) $_POST['event_package_id'] : null;
$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');
$partySize = (int) ($_POST['party_size'] ?? 2);
$specialRequests = clean_string($_POST['special_requests'] ?? '');
$addOnIds = $_POST['add_on_ids'] ?? [];
$addOnQty = $_POST['add_on_qty'] ?? [];

// Validation
$errors = [];
if (!is_numeric($userId) || (int) $userId <= 0) $errors[] = 'Please select a guest.';
if ($zoneId <= 0) $errors[] = 'Please select a dining zone.';
if ($seatingPref === '') $errors[] = 'Please select a seating preference.';
if (empty($date)) $errors[] = 'Date is required.';
if (empty($time)) $errors[] = 'Time is required.';
if ($partySize < 1 || $partySize > 20) $errors[] = 'Party size must be between 1-20.';
if (($serviceId && $packageId) || (!$serviceId && !$packageId)) {
    $errors[] = 'Please choose either a service or an event package, not both.';
}
if ($date !== '') {
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    $dateErrors = DateTime::getLastErrors();
    if (!$dateObj || (is_array($dateErrors) && (($dateErrors['warning_count'] ?? 0) > 0 || ($dateErrors['error_count'] ?? 0) > 0))) {
        $errors[] = 'Please enter a valid reservation date.';
    } else {
        $date = $dateObj->format('Y-m-d');
        if ($date < date('Y-m-d')) {
            $errors[] = 'Reservations cannot be created for a past date.';
        }
    }
}
if ($time !== '') {
    $timeObj = DateTime::createFromFormat('H:i:s', $time) ?: DateTime::createFromFormat('H:i', $time);
    $timeErrors = DateTime::getLastErrors();
    if (!$timeObj || (is_array($timeErrors) && (($timeErrors['warning_count'] ?? 0) > 0 || ($timeErrors['error_count'] ?? 0) > 0))) {
        $errors[] = 'Please enter a valid reservation time.';
    } else {
        $time = $timeObj->format('H:i:s');
    }
}

if (!empty($errors)) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => implode(' ', $errors)]);
        exit();
    }
    set_flash('admin_error', implode(' ', $errors));
    header('Location: ../pages/admin/floor.php');
    exit();
}

try {
    $pdo = db();
    $userId = (int) $userId;
    $actualUserId = $userId;

    $userStmt = $pdo->prepare("SELECT u.first_name, u.last_name
        FROM users u
        JOIN roles r ON r.role_id = u.role_id
        WHERE u.user_id = :id
          AND u.is_active = 1
          AND r.role_name IN ('guest', 'customer')
        LIMIT 1");
    $userStmt->execute([':id' => $actualUserId]);
    $userRow = $userStmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $userStmt->closeCursor();

    if (empty($userRow)) {
        $errorMessage = 'Please select an active guest account.';
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => $errorMessage]);
            exit();
        }
        set_flash('admin_error', $errorMessage);
        header('Location: ../pages/admin/floor.php');
        exit();
    }
    
    // Calculate end time from booking settings
    $startTime = $time;
    $reservationDurationMinutes = reservation_duration_minutes();
    $endTime = date('H:i:s', strtotime($time . ' +' . $reservationDurationMinutes . ' minutes'));

    $tableRow = null;
    if ($tableId > 0) {
        $tableStmt = $pdo->prepare('CALL sp_validate_table(:table_id, :zone_id, :seating_preference, :party_size)');
        $tableStmt->execute([
            ':table_id' => $tableId,
            ':zone_id' => $zoneId,
            ':seating_preference' => $seatingPref,
            ':party_size' => $partySize,
        ]);
        $tableRow = $tableStmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $tableStmt->closeCursor();

        if (!(int) ($tableRow['is_valid'] ?? 0)) {
            $errorMessage = (string) ($tableRow['error_message'] ?? 'The selected table is not available.');
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => $errorMessage]);
                exit();
            }
            set_flash('admin_error', $errorMessage);
            header('Location: ../pages/admin/floor.php');
            exit();
        }
    } else {
        $tableStmt = $pdo->prepare('CALL sp_find_available_table(:zone_id, :party_size, :seating_preference, :appointment_date, :start_time, :end_time)');
        $tableStmt->execute([
            ':zone_id' => $zoneId,
            ':party_size' => $partySize,
            ':seating_preference' => $seatingPref,
            ':appointment_date' => $date,
            ':start_time' => $startTime,
            ':end_time' => $endTime,
        ]);
        $tableRow = $tableStmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $tableStmt->closeCursor();
        $tableId = (int) ($tableRow['table_id'] ?? 0);

        if ($tableId <= 0) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'No available tables were found for that seating preference and time. Please choose another one.']);
                exit();
            }
            set_flash('admin_error', 'No available tables were found for that seating preference and time. Please choose another one.');
            header('Location: ../pages/admin/floor.php');
            exit();
        }
    }

    $slotStmt = $pdo->prepare(
        'SELECT
            fn_is_slot_available(:slot_appointment_date, :slot_start_time, :slot_end_time, :slot_table_id, NULL, NULL) AS is_available,
            fn_table_has_conflict(:conflict_table_id, :conflict_appointment_date, :conflict_start_time, :conflict_end_time, NULL) AS table_conflict'
    );
    $slotStmt->execute([
        ':slot_appointment_date' => $date,
        ':slot_start_time' => $startTime,
        ':slot_end_time' => $endTime,
        ':slot_table_id' => $tableId,
        ':conflict_table_id' => $tableId,
        ':conflict_appointment_date' => $date,
        ':conflict_start_time' => $startTime,
        ':conflict_end_time' => $endTime,
    ]);
    $slotRow = $slotStmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $slotStmt->closeCursor();

    if (!(int) ($slotRow['is_available'] ?? 0)) {
        $errorMessage = 'That time is no longer available. Please choose another time.';
        if ((int) ($slotRow['table_conflict'] ?? 0) === 1) {
            $errorMessage = 'That time is already booked for the selected table. Please choose another time.';
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => $errorMessage]);
            exit();
        }
        set_flash('admin_error', $errorMessage);
        header('Location: ../pages/admin/floor.php');
        exit();
    }

    // Get status_id for 'pending'
    $stmt = $pdo->prepare("SELECT fn_status_id_by_name('pending') AS status_id");
    $stmt->execute();
    $statusId = $stmt->fetchColumn();
    $stmt->closeCursor();
    
    if (!$statusId) {
        $statusId = 1; // Fallback
    }
    
    // Create the appointment
    $proc = $pdo->prepare('CALL sp_appointment_create(:user_id, :service_id, :table_id, :zone_id, :event_package_id, :appointment_date, :start_time, :end_time, :party_size, :status_id, :special_requests)');
    $proc->execute([
        ':user_id' => $actualUserId,
        ':service_id' => $serviceId,
        ':table_id' => $tableId,
        ':zone_id' => $zoneId,
        ':event_package_id' => $packageId,
        ':appointment_date' => $date,
        ':start_time' => $startTime,
        ':end_time' => $endTime,
        ':party_size' => $partySize,
        ':status_id' => (int) $statusId,
        ':special_requests' => $specialRequests !== '' ? $specialRequests : null,
    ]);
    $result = $proc->fetch(PDO::FETCH_ASSOC) ?: [];
    $proc->closeCursor();
    
    $appointmentId = (int) ($result['appointment_id'] ?? $pdo->lastInsertId());

    $addonWarning = '';
    if ($appointmentId > 0 && is_array($addOnIds)) {
        $addonTransactionStarted = false;
        try {
            if (!$pdo->inTransaction()) {
                $pdo->beginTransaction();
                $addonTransactionStarted = true;
            }
            foreach ($addOnIds as $addOnId) {
                $addOnId = (int) $addOnId;
                if ($addOnId <= 0) {
                    continue;
                }
                $qty = isset($addOnQty[$addOnId]) ? (int) $addOnQty[$addOnId] : 1;
                if ($qty < 1) {
                    $qty = 1;
                } elseif ($qty > 20) {
                    $qty = 20;
                }
                $addProc = $pdo->prepare('CALL sp_appointment_add_on_add(:appointment_id, :add_on_id, :quantity)');
                $addProc->execute([
                    ':appointment_id' => $appointmentId,
                    ':add_on_id' => $addOnId,
                    ':quantity' => $qty,
                ]);
                $addProc->closeCursor();
            }
            if ($addonTransactionStarted && $pdo->inTransaction()) {
                $pdo->commit();
            }
        } catch (PDOException $addonError) {
            if ($addonTransactionStarted && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Admin reservation add-ons failed for appointment ' . $appointmentId . ': ' . $addonError->getMessage());
            $addonWarning = ' Add-ons were not fully saved.';
        }
    }
    
    $statusStmt = $pdo->prepare('SELECT current_status FROM `tables` WHERE table_id = :table_id LIMIT 1');
    $statusStmt->execute([':table_id' => $tableId]);
    $newTableStatus = $statusStmt->fetchColumn();
    $statusStmt->closeCursor();
    $newTableStatus = ($newTableStatus === 'occupied') ? 'occupied' : 'available';
    
    $guestName = trim(($userRow['first_name'] ?? '') . ' ' . ($userRow['last_name'] ?? ''));
    $successMessage = $guestName !== ''
        ? 'Reservation created successfully for ' . $guestName . '.'
        : 'Reservation created successfully.';
    if ($addonWarning !== '') {
        $successMessage .= $addonWarning;
    }
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode([
            'ok' => true,
            'appointment_id' => $appointmentId,
            'table_id' => $tableId,
            'zone_id' => $zoneId,
            'seating_preference' => $seatingPref,
            'status' => $newTableStatus,
            'message' => $successMessage,
        ]);
        exit();
    }
    set_flash('admin_success', $successMessage);
    
} catch (PDOException $e) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => admin_booking_error_message($e)]);
        exit();
    }
    set_flash('admin_error', admin_booking_error_message($e));
}

header('Location: ../pages/admin/floor.php');
exit();
