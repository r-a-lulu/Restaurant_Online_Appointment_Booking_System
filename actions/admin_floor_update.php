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
$allowed = ['available', 'reserved', 'occupied'];

if ($tableId <= 0 || !in_array($status, $allowed, true)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid input.']);
    exit();
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('UPDATE `tables` SET current_status = :status WHERE table_id = :id');
    $stmt->execute([':status' => $status, ':id' => $tableId]);
    $stmt->closeCursor();

    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => safe_error_message($e)]);
}
