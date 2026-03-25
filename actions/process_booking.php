<?php
if (!defined('FRONT_CONTROLLER')) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}
require_once __DIR__ . '/../includes/security.php';
start_secure_session();
sync_booking_debug_mode();
require_booking_open();
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit();
}

$csrf = $_POST['csrf_token'] ?? '';
$source = $_POST['source'] ?? '';
$errorRedirect = 'pages/book.php';
if (!verify_csrf($csrf)) {
    set_flash('booking_error', 'Invalid security token. Please try again.');
    redirect('pages/book.php');
}

$serviceId = isset($_POST['service_id']) && $_POST['service_id'] !== '' ? (int) $_POST['service_id'] : null;
$packageId = isset($_POST['event_package_id']) && $_POST['event_package_id'] !== '' ? (int) $_POST['event_package_id'] : null;
$zoneId = isset($_POST['zone_id']) && $_POST['zone_id'] !== '' ? (int) $_POST['zone_id'] : null;
$tableId = isset($_POST['table_id']) && $_POST['table_id'] !== '' ? (int) $_POST['table_id'] : null;
$seatingPref = clean_string($_POST['seating_preference'] ?? '');
$appointmentDate = trim($_POST['appointment_date'] ?? '');
$startTime = trim($_POST['start_time'] ?? '');
$partySize = isset($_POST['party_size']) ? (int) $_POST['party_size'] : 0;
$specialRequests = clean_string($_POST['special_requests'] ?? '');
$addOnIds = $_POST['add_on_ids'] ?? [];
$addOnQty = $_POST['add_on_qty'] ?? [];

if (($serviceId && $packageId) || (!$serviceId && !$packageId)) {
    set_flash('booking_error', 'Please choose either a service or a package.');
    redirect('pages/book.php');
}

if (!$zoneId && $tableId) {
    try {
        $zoneLookupPdo = db();
        $zoneLookupStmt = $zoneLookupPdo->prepare('SELECT zone_id FROM `tables` WHERE table_id = :table_id LIMIT 1');
        $zoneLookupStmt->execute([':table_id' => $tableId]);
        $zoneId = (int) $zoneLookupStmt->fetchColumn();
        $zoneLookupStmt->closeCursor();
    } catch (PDOException $e) {
        $zoneId = null;
    }
}

if (!$zoneId) {
    set_flash('booking_error', 'Please select a dining zone.');
    redirect('pages/book.php');
}

if (!$appointmentDate || !$startTime || $partySize <= 0) {
    set_flash('booking_error', 'Please complete all required booking details.');
    redirect('pages/book.php');
}

$dateObj = DateTime::createFromFormat('Y-m-d', $appointmentDate);
$dateErrors = DateTime::getLastErrors();
if (!$dateObj || (is_array($dateErrors) && (($dateErrors['warning_count'] ?? 0) > 0 || ($dateErrors['error_count'] ?? 0) > 0))) {
    set_flash('booking_error', 'Invalid reservation date.');
    redirect('pages/book.php');
}
$appointmentDate = $dateObj->format('Y-m-d');
$minDate = new DateTime('today');
$minDate->modify('+1 day');
if ($dateObj < $minDate) {
    set_flash('booking_error', 'Reservations must be made at least 24 hours in advance.');
    redirect('pages/book.php');
}
if ((int) $dateObj->format('N') === 1) {
    set_flash('booking_error', 'We are closed on Mondays. Please choose another date.');
    redirect('pages/book.php');
}

$startDt = DateTime::createFromFormat('H:i:s', $startTime) ?: DateTime::createFromFormat('H:i', $startTime);
$timeErrors = DateTime::getLastErrors();
if (!$startDt || (is_array($timeErrors) && (($timeErrors['warning_count'] ?? 0) > 0 || ($timeErrors['error_count'] ?? 0) > 0))) {
    set_flash('booking_error', 'Invalid start time selected.');
    redirect('pages/book.php');
}
$startTime = $startDt->format('H:i:s');

$endDt = clone $startDt;
$endDt->modify('+2 hours');
$endTime = $endDt->format('H:i:s');

$redirectToConfirmation = static function (int $appointmentId) use ($source): void {
    $query = 'appointment_id=' . $appointmentId;
    if ($source === 'dashboard') {
        $query .= '&source=dashboard';
    }
    redirect('pages/book-confirmation.php?' . $query);
};

try {
    $pdo = db();
    $findExistingBookingId = static function (int $userId, int $tableId, string $date, string $time, ?int $serviceId, ?int $packageId) use ($pdo): int {
        $sql = 'SELECT appointment_id
            FROM appointments
            WHERE user_id = :user_id
              AND table_id = :table_id
              AND appointment_date = :appointment_date
              AND start_time = :start_time';
        $params = [
            ':user_id' => $userId,
            ':table_id' => $tableId,
            ':appointment_date' => $date,
            ':start_time' => $time,
        ];

        if ($serviceId !== null) {
            $sql .= ' AND service_id = :service_id';
            $params[':service_id'] = $serviceId;
        } else {
            $sql .= ' AND service_id IS NULL';
        }

        if ($packageId !== null) {
            $sql .= ' AND event_package_id = :event_package_id';
            $params[':event_package_id'] = $packageId;
        } else {
            $sql .= ' AND event_package_id IS NULL';
        }

        $sql .= ' ORDER BY appointment_id DESC LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $appointmentId = (int) $stmt->fetchColumn();
        $stmt->closeCursor();

        return $appointmentId;
    };

    $zoneCapacityStmt = $pdo->prepare('SELECT COALESCE(MAX(capacity), 0) FROM `tables` WHERE zone_id = :zone_id');
    $zoneCapacityStmt->execute([':zone_id' => $zoneId]);
    $zoneMaxCapacity = (int) $zoneCapacityStmt->fetchColumn();
    $zoneCapacityStmt->closeCursor();

    if ($zoneMaxCapacity <= 0) {
        set_flash('booking_error', 'The selected dining zone is unavailable right now. Please choose another zone.');
        redirect('pages/book.php');
    }

    if ($partySize > $zoneMaxCapacity) {
        set_flash('booking_error', 'This dining zone supports up to ' . $zoneMaxCapacity . ' guests. Please choose a smaller party size or another zone.');
        redirect('pages/book.php');
    }

    if (!$tableId) {
        $autoStmt = $pdo->prepare('CALL sp_find_available_table(:zone_id, :party_size, :seating_preference, :appointment_date, :start_time, :end_time)');
        $autoStmt->execute([
            ':zone_id' => $zoneId,
            ':party_size' => $partySize,
            ':seating_preference' => $seatingPref,
            ':appointment_date' => $appointmentDate,
            ':start_time' => $startTime,
            ':end_time' => $endTime,
        ]);
        $tableRow = $autoStmt->fetch(PDO::FETCH_ASSOC) ?: null;
        $autoStmt->closeCursor();

        $tableId = (int) ($tableRow['table_id'] ?? 0);
        if ($tableId <= 0) {
            set_flash('booking_error', 'No available seats in the selected zone for this time. Please choose another time.');
            redirect('pages/book.php');
        }
    } else {
        $validateStmt = $pdo->prepare('CALL sp_validate_table(:table_id, :zone_id, :seating_preference, :party_size)');
        $validateStmt->execute([
            ':table_id' => $tableId,
            ':zone_id' => $zoneId,
            ':seating_preference' => $seatingPref,
            ':party_size' => $partySize,
        ]);
        $tableRow = $validateStmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $validateStmt->closeCursor();

        if (!(int) ($tableRow['is_valid'] ?? 0)) {
            set_flash('booking_error', (string) ($tableRow['error_message'] ?? 'The selected table is not available.'));
            redirect('pages/book.php');
        }
    }

    $slotStmt = $pdo->prepare(
        'SELECT
            fn_is_slot_available(:slot_appointment_date, :slot_start_time, :slot_end_time, :slot_table_id, NULL, NULL) AS is_available,
            fn_table_has_conflict(:conflict_table_id, :conflict_appointment_date, :conflict_start_time, :conflict_end_time, NULL) AS table_conflict'
    );
    $slotStmt->execute([
        ':slot_appointment_date' => $appointmentDate,
        ':slot_start_time' => $startTime,
        ':slot_end_time' => $endTime,
        ':slot_table_id' => $tableId,
        ':conflict_table_id' => $tableId,
        ':conflict_appointment_date' => $appointmentDate,
        ':conflict_start_time' => $startTime,
        ':conflict_end_time' => $endTime,
    ]);
    $slotRow = $slotStmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $slotStmt->closeCursor();

    if (!(int) ($slotRow['is_available'] ?? 0)) {
        if ((int) ($slotRow['table_conflict'] ?? 0) === 1) {
            set_flash('booking_error', 'That time is already booked for the selected table. Please choose another time.');
        } else {
            set_flash('booking_error', 'That time is no longer available. Please choose another time.');
        }
        redirect('pages/book.php');
    }

    $statusStmt = $pdo->prepare('SELECT fn_status_id_by_name(:name) AS status_id');
    $statusStmt->execute([':name' => 'pending']);
    $statusId = $statusStmt->fetchColumn();
    $statusStmt->closeCursor();

    if (!$statusId) {
        set_flash('booking_error', 'Unable to determine default status. Please contact support.');
        redirect('pages/book.php');
    }

    $existingAppointmentId = $findExistingBookingId(
        (int) $_SESSION['user_id'],
        $tableId,
        $appointmentDate,
        $startTime,
        $serviceId,
        $packageId
    );
    if ($existingAppointmentId > 0) {
        $redirectToConfirmation($existingAppointmentId);
    }

    $proc = $pdo->prepare('CALL sp_appointment_create(:user_id, :service_id, :table_id, :zone_id, :event_package_id, :appointment_date, :start_time, :end_time, :party_size, :status_id, :special_requests)');
    $proc->execute([
        ':user_id' => (int) $_SESSION['user_id'],
        ':service_id' => $serviceId,
        ':table_id' => $tableId,
        ':zone_id' => $zoneId,
        ':event_package_id' => $packageId,
        ':appointment_date' => $appointmentDate,
        ':start_time' => $startTime,
        ':end_time' => $endTime,
        ':party_size' => $partySize,
        ':status_id' => (int) $statusId,
        ':special_requests' => $specialRequests !== '' ? $specialRequests : null,
    ]);
    $result = $proc->fetch();
    $proc->closeCursor();

    $appointmentId = $result['appointment_id'] ?? null;

    if ($appointmentId && is_array($addOnIds)) {
        try {
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
                    ':appointment_id' => (int) $appointmentId,
                    ':add_on_id' => $addOnId,
                    ':quantity' => $qty,
                ]);
                $addProc->closeCursor();
            }
        } catch (PDOException $addonError) {
            error_log('Guest reservation add-ons failed for appointment ' . $appointmentId . ': ' . $addonError->getMessage());
        }
    }

    $userStmt = $pdo->prepare('SELECT first_name, last_name, email FROM users WHERE user_id = :id LIMIT 1');
    $userStmt->execute([':id' => (int) $_SESSION['user_id']]);
    $user = $userStmt->fetch() ?: [];
    $userStmt->closeCursor();

    $totalStmt = $pdo->prepare('SELECT fn_appointment_total(:appointment_id) AS total_amount');
    $totalStmt->execute([':appointment_id' => (int) $appointmentId]);
    $totalAmount = $totalStmt->fetchColumn();
    $totalStmt->closeCursor();

    $ref = 'EUD-' . str_pad((string) $appointmentId, 8, '0', STR_PAD_LEFT);
    $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
    $email = $user['email'] ?? '';
    $zoneLabel = clean_string($_POST['zone_label'] ?? '');
    $dateLabel = clean_string($_POST['date_label'] ?? '');
    $timeLabel = clean_string($_POST['time_label'] ?? '');

    $params = http_build_query([
        'appointment_id' => (int) $appointmentId,
        'name' => $name,
        'email' => $email,
        'guests' => $partySize,
        'zone' => $zoneLabel,
        'date' => $dateLabel,
        'time' => $timeLabel,
        'ref' => $ref,
        'total' => number_format((float) $totalAmount, 2, '.', ''),
    ]);

    if ($source === 'dashboard') {
        redirect('pages/book-confirmation.php?' . $params . '&source=dashboard');
    } else {
        redirect('pages/book-confirmation.php?' . $params);
    }
} catch (PDOException $e) {
    $normalizedMessage = strtolower(trim((string) $e->getMessage()));
    if (strpos($normalizedMessage, 'duplicate entry') !== false && $tableId > 0) {
        try {
            $existingAppointmentStmt = db()->prepare(
                'SELECT appointment_id
                 FROM appointments
                 WHERE user_id = :user_id
                   AND table_id = :table_id
                   AND appointment_date = :appointment_date
                   AND start_time = :start_time
                 ORDER BY appointment_id DESC
                 LIMIT 1'
            );
            $existingAppointmentStmt->execute([
                ':user_id' => (int) $_SESSION['user_id'],
                ':table_id' => $tableId,
                ':appointment_date' => $appointmentDate,
                ':start_time' => $startTime,
            ]);
            $existingAppointmentId = (int) $existingAppointmentStmt->fetchColumn();
            $existingAppointmentStmt->closeCursor();
            if ($existingAppointmentId > 0) {
                $redirectToConfirmation($existingAppointmentId);
            }
        } catch (PDOException $lookupError) {
            error_log('Booking duplicate lookup failed: ' . $lookupError->getMessage());
        }
    }
    set_flash('booking_error', booking_error_message($e));
    redirect('pages/book.php');
}

