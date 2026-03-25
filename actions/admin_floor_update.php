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
$status = clean_string($_POST['status'] ?? '');
$floorDate = trim((string) ($_POST['floor_date'] ?? date('Y-m-d')));
$allowed = ['available', 'occupied'];

if ($tableId <= 0 || !in_array($status, $allowed, true)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid input.']);
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

if ($floorDate !== date('Y-m-d')) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Manual occupied updates only apply to today\'s floor.']);
    exit();
}

try {
    $pdo = db();
    if ($status === 'occupied') {
        $manualMinutes = floor_manual_occupied_minutes();
        $manualUntil = (new DateTimeImmutable('now'))->modify('+' . $manualMinutes . ' minutes')->format('Y-m-d H:i:s');
        $stmt = $pdo->prepare('UPDATE `tables` SET current_status = :status, manual_status_until = :manual_status_until WHERE table_id = :id');
        $stmt->execute([
            ':status' => $status,
            ':manual_status_until' => $manualUntil,
            ':id' => $tableId,
        ]);
    } else {
        $stmt = $pdo->prepare('UPDATE `tables` SET current_status = :status, manual_status_until = NULL WHERE table_id = :id');
        $stmt->execute([':status' => $status, ':id' => $tableId]);
    }
    $stmt->closeCursor();

    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => admin_booking_error_message($e)]);
}
