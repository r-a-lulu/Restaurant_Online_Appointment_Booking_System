<?php
if (!defined('FRONT_CONTROLLER')) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}
require_once __DIR__ . '/../includes/security.php';

start_secure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pages/login.php");
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!verify_csrf($csrf)) {
    set_flash('error', 'Invalid security token. Please try again.');
    header("Location: pages/login.php");
    exit;
}

$_SESSION = [];
session_destroy();

// Redirect back to login
header("Location: pages/login.php");
exit;
