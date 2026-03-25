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
$globalBasePath = '';

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
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data:; font-src 'self' data: https://fonts.gstatic.com; connect-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
}

function safe_error_message(\Exception $e) {
    return $e->getMessage();
}

function guest_friendly_error_message(\Exception $e, string $fallback = 'Something went wrong. Please try again.'): string {
    $message = trim((string) $e->getMessage());
    $normalized = strtolower($message);

    if ($message === '') {
        return $fallback;
    }

    if (strpos($normalized, 'sqlstate') !== false || $e instanceof PDOException) {
        if (strpos($normalized, 'party size exceeds table capacity') !== false) {
            return 'The selected table cannot fit this party size. Please choose another table or reduce the number of guests.';
        }
        if (strpos($normalized, 'time slot conflicts with an existing table booking') !== false) {
            return 'That time is already booked for the selected table. Please choose another time.';
        }
        if (strpos($normalized, 'time slot conflicts with an existing zone booking') !== false) {
            return 'That time is already booked in the selected dining zone. Please choose another time or zone.';
        }
        if (strpos($normalized, 'cannot create an appointment in the past') !== false) {
            return 'Reservations cannot be made in the past. Please choose a future date and time.';
        }
        if (strpos($normalized, 'cannot reschedule an appointment to a past date') !== false) {
            return 'Reservations cannot be moved to a past date. Please choose a future date and time.';
        }
        if (strpos($normalized, 'select either a service or an event package') !== false) {
            return 'Please choose either a service or an event package, not both.';
        }
        if (strpos($normalized, 'invalid status transition') !== false) {
            return 'That reservation status change is not allowed. Please choose a valid next status.';
        }
        if (strpos($normalized, 'cannot delete a confirmed appointment') !== false) {
            return 'Confirmed reservations cannot be deleted. Please cancel it first.';
        }
        if (strpos($normalized, 'maximum active bookings') !== false || strpos($normalized, 'limit: 5') !== false) {
            return 'You already have 5 active reservations. Please complete or cancel one before creating a new booking.';
        }
        if (strpos($normalized, 'time slot conflicts') !== false || strpos($normalized, 'conflicts with an existing') !== false) {
            return 'That time is already booked. Please choose another time.';
        }
        if (strpos($normalized, 'duplicate') !== false) {
            return 'This reservation already exists or was just submitted. Please review your bookings and try another time if needed.';
        }
        if (strpos($normalized, 'foreign key') !== false || strpos($normalized, 'constraint') !== false) {
            return 'One of the reservation details is no longer available. Please review your selection and try again.';
        }
        if (strpos($normalized, 'lock wait timeout') !== false || strpos($normalized, 'deadlock') !== false) {
            return 'The reservation system is busy right now. Please try again in a moment.';
        }
        return $fallback;
    }

    return $message;
}

function booking_error_message(\Exception $e): string {
    return guest_friendly_error_message($e, 'We could not complete your reservation right now. Please try again or choose another time.');
}

function admin_booking_error_message(\Exception $e): string {
    return guest_friendly_error_message($e, 'We could not complete this reservation right now. Please review the details and try again.');
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

/**
 * Get a system setting value from the database
 * @param string $key The setting key
 * @param string|null $default Default value if setting not found
 * @return string The setting value or default
 */
function get_setting(string $key, ?string $default = null): string {
    static $cache = [];
    
    if (array_key_exists($key, $cache)) {
        return $cache[$key] ?? $default ?? '';
    }
    
    try {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        
        if ($value !== false) {
            $cache[$key] = $value;
            return $value;
        }
    } catch (PDOException $e) {
        // Table may not exist or other DB error
    }
    
    return $default ?? '';
}

/**
 * Check if maintenance mode is enabled
 * @return bool True if maintenance mode is on
 */
function is_maintenance_mode(): bool {
    return get_setting('maintenance_mode', '0') === '1';
}
