<?php
if (!defined('FRONT_CONTROLLER')) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}
require_once __DIR__ . '/../secure/db.php';
require_once __DIR__ . '/../includes/security.php';

start_secure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pages/register.php");
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!verify_csrf($csrf)) {
    set_flash('error', 'Invalid security token. Please try again.');
    header("Location: pages/register.php");
    exit;
}

$firstName = trim($_POST['first_name'] ?? '');
$lastName  = trim($_POST['last_name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$password  = $_POST['password'] ?? '';
$confirm   = $_POST['confirm_password'] ?? '';

if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
    set_flash('error', 'All fields are required.');
    set_flash('form_data', [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone
    ]);
    header("Location: pages/register.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('error', 'Please enter a valid email address.');
    set_flash('form_data', [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone
    ]);
    header("Location: pages/register.php");
    exit;
}

if (strlen($password) < 8) {
    set_flash('error', 'Password must be at least 8 characters long.');
    set_flash('form_data', [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone
    ]);
    header("Location: pages/register.php");
    exit;
}

if ($password !== $confirm) {
    set_flash('error', 'Passwords do not match.');
    set_flash('form_data', [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone
    ]);
    header("Location: pages/register.php");
    exit;
}

// Rate limit by IP (5 attempts / 1 hour)
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateKey = 'register:' . $ip;
if (rate_limit_exceeded($rateKey, 5, 3600)) {
    set_flash('error', 'Too many registration attempts. Please try again later.');
    header("Location: pages/register.php");
    exit;
}

try {
    $db = get_db();
    
    // Check if email already exists
    $stmt = call_procedure('sp_user_get_by_email', [$email]);
    if ($stmt->fetch()) {
        rate_limit_hit($rateKey, 3600);
        set_flash('error', 'An account with that email already exists.');
        set_flash('form_data', [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone
        ]);
        header("Location: pages/register.php");
        exit;
    }
    $stmt->closeCursor();

    // Fetch the customer role ID (fallback to guest if missing)
    $roleStmt = $db->prepare("SELECT role_id FROM roles WHERE role_name = 'customer' LIMIT 1");
    $roleStmt->execute();
    $roleRow = $roleStmt->fetch();
    $roleId = $roleRow ? $roleRow['role_id'] : null;
    if (!$roleId) {
        $roleStmt = $db->prepare("SELECT role_id FROM roles WHERE role_name = 'guest' LIMIT 1");
        $roleStmt->execute();
        $roleRow = $roleStmt->fetch();
        $roleId = $roleRow ? $roleRow['role_id'] : null;
    }
    if (!$roleId) {
        set_flash('error', 'Default role not found. Please contact support.');
        set_flash('form_data', [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone
        ]);
        header("Location: pages/register.php");
        exit;
    }

    // Hash password
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // Call stored procedure to create user
    $phone = $phone !== '' ? $phone : null;
    call_procedure('sp_user_create', [$roleId, $firstName, $lastName, $email, $phone, $hash, null]);
    rate_limit_clear($rateKey);

    set_flash('success', 'Registration successful! You may now sign in.');
    header("Location: pages/login.php");
    exit;

} catch (PDOException $e) {
    set_flash('error', 'Database error: ' . safe_error_message($e));
    set_flash('form_data', [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone
    ]);
    header("Location: pages/register.php");
    exit;
}
