<?php
/**
 * Security, Session Management, and Helpers
 */
require_once __DIR__ . '/../secure/db.php';

function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        // Enforce cookie-based sessions only
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        ini_set('session.cookie_secure', $isHttps ? 1 : 0);
        ini_set('session.cookie_samesite', 'Strict');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
        apply_security_headers();
        enforce_session_timeout();
    }
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function action_token(string $action): string {
    if (empty($_SESSION['action_tokens']) || !is_array($_SESSION['action_tokens'])) {
        $_SESSION['action_tokens'] = [];
    }
    if (empty($_SESSION['action_tokens'][$action])) {
        $_SESSION['action_tokens'][$action] = bin2hex(random_bytes(32));
    }
    return $_SESSION['action_tokens'][$action];
}

function verify_action_token(string $action, ?string $token = null): bool {
    if (!$action) {
        return false;
    }
    $token = $token ?? ($_POST['action_token'] ?? '');
    if (!$token || empty($_SESSION['action_tokens'][$action])) {
        return false;
    }
    return hash_equals($_SESSION['action_tokens'][$action], $token);
}

function verify_csrf(?string $token = null): bool {
    $token = $token ?? ($_POST['csrf_token'] ?? '');
    if (!$token || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function e($string) {
    if ($string === null) return '';
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function clean_string(?string $value): string {
    $value = $value ?? '';
    $value = trim($value);
    return preg_replace('/\\s+/', ' ', $value);
}

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

function set_flash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function get_flash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

// Global project base path for redirects
$globalBasePath = '/Restaurant_Online_Appointment_Booking_System';

function require_login() {
    global $globalBasePath;
    if (empty($_SESSION['user_id'])) {
        set_flash('error', 'Please log in to access that page.');
        header("Location: $globalBasePath/pages/login.php");
        exit;
    }
}

function require_role($role) {
    global $globalBasePath;
    require_login();
    if (($_SESSION['role_name'] ?? '') !== $role) {
        if (($_SESSION['role_name'] ?? '') === 'admin') {
            header("Location: $globalBasePath/pages/admin/index.php");
        } else {
            header("Location: $globalBasePath/pages/dashboard/index.php");
        }
        exit;
    }
}

function require_admin(): void {
    if (($_SESSION['role_name'] ?? '') !== 'admin') {
        header('HTTP/1.1 403 Forbidden');
        exit;
    }
}

function apply_security_headers(): void {
    if (headers_sent()) {
        return;
    }
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=()');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
}

function safe_error_message(\Exception $e) {
    return $e->getMessage();
}

function enforce_session_timeout(): void {
    $timeout = 1800; // 30 minutes
    if (!empty($_SESSION['last_activity']) && (time() - (int) $_SESSION['last_activity']) > $timeout) {
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }
    $_SESSION['last_activity'] = time();
}

function rate_limit_dir(): string {
    $dir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'restaurant_booking_rate_limits';
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }
    return $dir;
}

function rate_limit_path(string $key): string {
    return rate_limit_dir() . DIRECTORY_SEPARATOR . hash('sha256', $key) . '.json';
}

function rate_limit_read(string $key, int $window): array {
    $path = rate_limit_path($key);
    if (!is_file($path)) {
        return [];
    }
    $raw = @file_get_contents($path);
    if ($raw === false) {
        return [];
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return [];
    }
    $now = time();
    return array_values(array_filter($data, static function ($ts) use ($now, $window) {
        return is_int($ts) && ($now - $ts) <= $window;
    }));
}

function rate_limit_hit(string $key, int $window): int {
    $entries = rate_limit_read($key, $window);
    $entries[] = time();
    @file_put_contents(rate_limit_path($key), json_encode($entries), LOCK_EX);
    return count($entries);
}

function rate_limit_exceeded(string $key, int $max, int $window): bool {
    return count(rate_limit_read($key, $window)) >= $max;
}

function rate_limit_clear(string $key): void {
    $path = rate_limit_path($key);
    if (is_file($path)) {
        @unlink($path);
    }
}
