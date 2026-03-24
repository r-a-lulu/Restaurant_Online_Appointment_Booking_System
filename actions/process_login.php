<?php
if (!defined('FRONT_CONTROLLER')) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}
require_once __DIR__ . '/../secure/db.php';
require_once __DIR__ . '/../includes/security.php';

start_secure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/login.php");
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!verify_csrf($csrf)) {
    set_flash('error', 'Invalid security token. Please try again.');
    header("Location: ../pages/login.php");
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    set_flash('error', 'Please enter your email and password.');
    header("Location: ../pages/login.php");
    exit;
}

// Rate limit by IP + email (5 attempts / 15 minutes)
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateKey = 'login:' . $ip . ':' . strtolower($email);
if (rate_limit_exceeded($rateKey, 5, 900)) {
    set_flash('error', 'Too many login attempts. Please try again in 15 minutes.');
    header("Location: ../pages/login.php");
    exit;
}

try {
    $db = get_db();
    
    // Call stored procedure
    $stmt = call_procedure('sp_user_get_by_email', [$email]);
    $user = $stmt->fetch();
    $stmt->closeCursor(); // Free up connection for the next query
    
    if ($user && password_verify($password, $user['password_hash'])) {
        if (!$user['is_active']) {
            set_flash('error', 'Your account has been deactivated. Please contact support.');
            header("Location: ../pages/login.php");
            exit;
        }

        // Fetch role_name to store in session
        $roleStmt = $db->prepare("SELECT role_name FROM roles WHERE role_id = ?");
        $roleStmt->execute([$user['role_id']]);
        $roleRow = $roleStmt->fetch();
        $roleName = $roleRow ? $roleRow['role_name'] : 'guest';

        // Clear rate limit on success and regenerate session to prevent fixation
        rate_limit_clear($rateKey);
        session_regenerate_id(true);

        // Set session variables
        $_SESSION['user_id']    = $user['user_id'];
        $_SESSION['role_name']  = $roleName;
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name']  = $user['last_name'];
        $_SESSION['email']      = $user['email'];

        // Redirect based on role
        if ($roleName === 'admin') {
            header("Location: ../pages/admin/index.php");
        } else {
            header("Location: ../pages/dashboard/index.php");
        }
        exit;
        
    } else {
        rate_limit_hit($rateKey, 900);
        set_flash('error', 'Invalid email or password.');
        header("Location: ../pages/login.php");
        exit;
    }

} catch (PDOException $e) {
    set_flash('error', 'Authentication service unavailable: ' . safe_error_message($e));
    header("Location: ../pages/login.php");
    exit;
}
