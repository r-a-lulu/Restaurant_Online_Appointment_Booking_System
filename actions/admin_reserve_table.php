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

    $userStmt = $pdo->prepare('SELECT first_name, last_name FROM users WHERE user_id = :id LIMIT 1');
    $userStmt->execute([':id' => $actualUserId]);
    $userRow = $userStmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $userStmt->closeCursor();
    
    // Calculate end time (2 hours default)
    $startTime = $time;
    $endTime = date('H:i:s', strtotime($time . ' +2 hours'));

    $tableRow = null;
    if ($tableId > 0) {
        $tableStmt = $pdo->prepare(
            "SELECT table_id, zone_id, seating_preference, capacity, current_status
             FROM `tables`
             WHERE table_id = :table_id
             LIMIT 1"
        );
        $tableStmt->execute([':table_id' => $tableId]);
        $tableRow = $tableStmt->fetch(PDO::FETCH_ASSOC) ?: null;
        $tableStmt->closeCursor();

        if (!$tableRow) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'The selected table could not be found.']);
                exit();
            }
            set_flash('admin_error', 'The selected table could not be found.');
            header('Location: ../pages/admin/floor.php');
            exit();
        }

        if ((int) $tableRow['zone_id'] !== $zoneId) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'The selected table does not belong to the chosen dining zone.']);
                exit();
            }
            set_flash('admin_error', 'The selected table does not belong to the chosen dining zone.');
            header('Location: ../pages/admin/floor.php');
            exit();
        }

        $tablePref = clean_string($tableRow['seating_preference'] ?? '');
        if ($tablePref !== '' && strcasecmp($tablePref, $seatingPref) !== 0) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'The selected table does not match the selected seating preference.']);
                exit();
            }
            set_flash('admin_error', 'The selected table does not match the selected seating preference.');
            header('Location: ../pages/admin/floor.php');
            exit();
        }

        if (!empty($tableRow['current_status']) && $tableRow['current_status'] !== 'available') {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'That table is no longer available. Please choose another seating preference or time.']);
                exit();
            }
            set_flash('admin_error', 'That table is no longer available. Please choose another seating preference or time.');
            header('Location: ../pages/admin/floor.php');
            exit();
        }

        if ((int) $tableRow['capacity'] < $partySize) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'The selected seating preference cannot fit this party size. Please choose another one or reduce the guest count.']);
                exit();
            }
            set_flash('admin_error', 'The selected seating preference cannot fit this party size. Please choose another one or reduce the guest count.');
            header('Location: ../pages/admin/floor.php');
            exit();
        }
    } else {
        $tableStmt = $pdo->prepare(
            "SELECT t.table_id, t.capacity
             FROM `tables` t
             WHERE t.zone_id = :zone_id
               AND t.capacity >= :party_size
               AND (t.current_status IS NULL OR t.current_status = 'available')
               AND t.seating_preference = :seating_pref
               AND fn_is_slot_available(:appointment_date, :start_time, :end_time, t.table_id, :zone_id_fn, NULL) = 1
             ORDER BY t.capacity ASC, t.table_id ASC
             LIMIT 1"
        );
        $tableStmt->execute([
            ':zone_id' => $zoneId,
            ':party_size' => $partySize,
            ':seating_pref' => $seatingPref,
            ':appointment_date' => $date,
            ':start_time' => $startTime,
            ':end_time' => $endTime,
            ':zone_id_fn' => $zoneId,
        ]);
        $tableRow = $tableStmt->fetch(PDO::FETCH_ASSOC) ?: null;
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

    // Get status_id for 'pending'
    $stmt = $pdo->prepare("SELECT status_id FROM appointment_status WHERE status_name = 'pending' LIMIT 1");
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
    $newTableStatus = $statusStmt->fetchColumn() ?: 'available';
    $statusStmt->closeCursor();
    
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
