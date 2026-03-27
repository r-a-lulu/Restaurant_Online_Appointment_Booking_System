<?php
require_once __DIR__ . '/includes/security.php';

start_secure_session();

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($action === 'logout') {
        header('Location: pages/login.php');
        exit;
    }
    header('HTTP/1.1 405 Method Not Allowed');
    exit;
}

$routes = [
    'login' => __DIR__ . '/actions/process_login.php',
    'register' => __DIR__ . '/actions/process_register.php',
    'logout' => __DIR__ . '/actions/logout.php',
    'process_booking' => __DIR__ . '/actions/process_booking.php',
    'check_availability' => __DIR__ . '/actions/check_availability.php',
    'user_cancel_booking' => __DIR__ . '/actions/user_cancel_booking.php',
    'admin_update_status' => __DIR__ . '/actions/admin_update_status.php',
    'admin_master_data' => __DIR__ . '/actions/admin_master_data.php',
    'admin_floor_update' => __DIR__ . '/actions/admin_floor_update.php',
    'admin_floor_status' => __DIR__ . '/actions/admin_floor_status.php',
    'admin_floor_details' => __DIR__ . '/actions/admin_floor_details.php',
    'admin_floor_cancel' => __DIR__ . '/actions/admin_floor_cancel.php',
    'admin_reserve_table' => __DIR__ . '/actions/admin_reserve_table.php',
    'update_profile' => __DIR__ . '/actions/update_profile.php',
    'update_password' => __DIR__ . '/actions/update_password.php',
    'save_settings' => __DIR__ . '/actions/process_settings.php',
];

if (!$action || empty($routes[$action])) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

if (!verify_action_token($action, $_POST['action_token'] ?? '')) {
    if ($action === 'logout') {
        header('Location: pages/login.php');
        exit;
    }
    header('HTTP/1.1 403 Forbidden');
    exit;
}

define('FRONT_CONTROLLER', true);
require $routes[$action];

