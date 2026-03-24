<?php
require_once __DIR__ . '/includes/security.php';

start_secure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit;
}

$action = $_GET['action'] ?? '';
$routes = [
    'login' => __DIR__ . '/actions/process_login.php',
    'register' => __DIR__ . '/actions/process_register.php',
    'logout' => __DIR__ . '/actions/logout.php',
    'process_booking' => __DIR__ . '/actions/process_booking.php',
    'check_availability' => __DIR__ . '/actions/check_availability.php',
    'user_cancel_booking' => __DIR__ . '/actions/user_cancel_booking.php',
    'admin_update_status' => __DIR__ . '/actions/admin_update_status.php',
    'admin_master_data' => __DIR__ . '/actions/admin_master_data.php',
];

if (!$action || empty($routes[$action])) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

if (!verify_action_token($action, $_POST['action_token'] ?? '')) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

define('FRONT_CONTROLLER', true);
require $routes[$action];
