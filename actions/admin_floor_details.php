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
    echo json_encode(['error' => 'Invalid CSRF token.']);
    exit();
}

$tableId = isset($_POST['table_id']) ? (int) $_POST['table_id'] : 0;
$floorDate = trim((string) ($_POST['floor_date'] ?? date('Y-m-d')));

if ($tableId <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid table selection.']);
    exit();
}

$dateObj = DateTime::createFromFormat('Y-m-d', $floorDate);
$dateErrors = DateTime::getLastErrors();
if (!$dateObj || (is_array($dateErrors) && (($dateErrors['warning_count'] ?? 0) > 0 || ($dateErrors['error_count'] ?? 0) > 0))) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid floor date.']);
    exit();
}
$floorDate = $dateObj->format('Y-m-d');
$isToday = ($floorDate === date('Y-m-d'));

try {
    $pdo = db();

    $tableStmt = $pdo->prepare('SELECT t.table_id, t.capacity, t.seating_preference, t.current_status, t.manual_status_until, dz.zone_name
        FROM `tables` t
        JOIN dining_zones dz ON dz.zone_id = t.zone_id
        WHERE t.table_id = :table_id
        LIMIT 1');
    $tableStmt->execute([':table_id' => $tableId]);
    $table = $tableStmt->fetch();
    $tableStmt->closeCursor();

    if (!$table) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'That table could not be found.']);
        exit();
    }

    $detailStmt = $pdo->prepare("SELECT
            appointment_id,
            customer_name,
            customer_email,
            service_name,
            package_name,
            appointment_date,
            start_time,
            end_time,
            party_size,
            status_name,
            special_requests,
            created_at
        FROM vw_appointments_detail
        WHERE table_id = :table_id
          AND appointment_date = :floor_date
          AND status_name IN ('pending', 'confirmed')
        ORDER BY
          CASE
            WHEN :is_today_priority = 1
              AND status_name = 'confirmed'
              AND start_time <= CURTIME()
              AND end_time > CURTIME()
            THEN 0
            ELSE 1
          END,
          start_time ASC,
          appointment_id DESC");
    $detailStmt->execute([
        ':table_id' => $tableId,
        ':floor_date' => $floorDate,
        ':is_today_priority' => $isToday ? 1 : 0,
    ]);
    $detailRows = $detailStmt->fetchAll() ?: [];
    $detailStmt->closeCursor();

    $manualOverrideActive = $isToday
        && ($table['current_status'] ?? '') === 'occupied'
        && (
            empty($table['manual_status_until'])
            || strtotime((string) $table['manual_status_until']) > time()
        );

    $response = [
        'ok' => true,
        'table' => [
            'table_id' => (int) $table['table_id'],
            'label' => trim((string) ($table['seating_preference'] ?? '')) !== '' ? $table['seating_preference'] : 'Unassigned',
            'zone_name' => (string) ($table['zone_name'] ?? ''),
            'capacity' => (int) ($table['capacity'] ?? 0),
        ],
        'detail' => null,
        'details' => [],
    ];

    if ($detailRows) {
        foreach ($detailRows as $detail) {
            $statusName = strtolower((string) ($detail['status_name'] ?? ''));
            $statusLabel = ucfirst($statusName ?: 'reserved');
            $statusBadge = $statusName === 'confirmed'
                ? 'badge-confirmed'
                : ($statusName === 'pending' ? 'badge-pending' : 'badge-cancelled');

            $detailPayload = [
                'type' => ($isToday && $statusName === 'confirmed'
                    && strtotime($floorDate . ' ' . (string) $detail['start_time']) <= time()
                    && strtotime($floorDate . ' ' . (string) $detail['end_time']) > time())
                    ? 'occupied'
                    : 'reserved',
                'appointment_id' => (int) $detail['appointment_id'],
                'guest_name' => (string) ($detail['customer_name'] ?? 'Guest'),
                'guest_email' => (string) ($detail['customer_email'] ?? 'No email provided'),
                'party_size' => (int) ($detail['party_size'] ?? 0),
                'date_label' => date('F j, Y', strtotime((string) $detail['appointment_date'])),
                'time_label' => date('g:i A', strtotime((string) $detail['start_time'])) . ' - ' . date('g:i A', strtotime((string) $detail['end_time'])),
                'status_label' => $statusLabel,
                'status_badge' => $statusBadge,
                'service_label' => (string) (($detail['service_name'] ?: $detail['package_name']) ?: 'Standard reservation'),
                'special_requests' => trim((string) ($detail['special_requests'] ?? '')),
                'created_label' => date('M j, Y g:i A', strtotime((string) $detail['created_at'])),
                'manual_override' => false,
                'time_sort' => (string) ($detail['start_time'] ?? ''),
            ];

            $response['details'][] = $detailPayload;
        }
        $response['detail'] = $response['details'][0];
    } elseif ($manualOverrideActive) {
        $manualUntil = !empty($table['manual_status_until'])
            ? date('g:i A', strtotime((string) $table['manual_status_until']))
            : 'later today';

        $response['detail'] = [
            'type' => 'occupied',
            'appointment_id' => null,
            'guest_name' => 'Manual floor override',
            'guest_email' => 'No active reservation is attached to this table.',
            'party_size' => 0,
            'date_label' => date('F j, Y', strtotime($floorDate)),
            'time_label' => 'Marked occupied until ' . $manualUntil,
            'status_label' => 'Occupied',
            'status_badge' => 'badge-confirmed',
            'service_label' => 'Manual floor override',
            'special_requests' => '',
            'created_label' => 'Updated from Floor Management',
            'manual_override' => true,
            'time_sort' => '',
        ];
        $response['details'][] = $response['detail'];
    }

    if ($response['detail'] === null) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No reservation details are available for this table on the selected date.']);
        exit();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => admin_booking_error_message($e)]);
}
