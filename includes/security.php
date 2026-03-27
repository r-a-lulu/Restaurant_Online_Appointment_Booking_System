<?php
/**
 * Security, Session Management, and Helpers
 */
require_once __DIR__ . '/../secure/db.php';

if (!ini_get('date.timezone')) {
    date_default_timezone_set('Asia/Manila');
} else {
    date_default_timezone_set((string) ini_get('date.timezone'));
}

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

function is_local_development(): bool {
    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $remoteAddr = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));

    if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
        return true;
    }

    if (in_array($remoteAddr, ['127.0.0.1', '::1'], true)) {
        return true;
    }

    return strpos($host, '.local') !== false;
}

function sync_booking_debug_mode(): void {
    if (!is_local_development()) {
        return;
    }

    $flag = $_GET['debug_booking'] ?? $_POST['debug_booking'] ?? null;
    if ($flag === '1') {
        $_SESSION['booking_debug'] = true;
    } elseif ($flag === '0') {
        unset($_SESSION['booking_debug']);
    }
}

function booking_debug_enabled(): bool {
    return is_local_development() && !empty($_SESSION['booking_debug']);
}

function resolve_post_login_redirect(string $requestUri): string {
    $requestUri = trim($requestUri);
    if ($requestUri === '') {
        return '';
    }

    $parts = parse_url($requestUri);
    $path = $parts['path'] ?? '';
    $query = $parts['query'] ?? '';

    $action = '';
    if ($query !== '') {
        parse_str($query, $queryParams);
        if (!empty($queryParams['action']) && is_string($queryParams['action'])) {
            $action = $queryParams['action'];
        }
    }

    if (substr($path, -11) === 'actions.php') {
        switch ($action) {
            case 'process_booking':
            case 'check_availability':
                return 'pages/book.php';
            case 'user_cancel_booking':
                return 'pages/dashboard/reservations.php';
            case 'update_profile':
            case 'update_password':
                return 'pages/dashboard/profile.php';
            case 'admin_update_status':
                return 'pages/admin/reservations.php';
            case 'admin_reserve_table':
                return 'pages/admin/floor.php';
            case 'save_settings':
                return 'pages/admin/settings.php';
            default:
                return $requestUri;
        }
    }

    if (strpos($requestUri, '/actions/process_booking.php') !== false) {
        return 'pages/book.php';
    }
    if (strpos($requestUri, '/actions/check_availability.php') !== false) {
        return 'pages/book.php';
    }
    if (strpos($requestUri, '/actions/user_cancel_booking.php') !== false) {
        return 'pages/dashboard/reservations.php';
    }
    if (strpos($requestUri, '/actions/update_profile.php') !== false || strpos($requestUri, '/actions/update_password.php') !== false) {
        return 'pages/dashboard/profile.php';
    }
    if (strpos($requestUri, '/actions/admin_update_status.php') !== false) {
        return 'pages/admin/reservations.php';
    }
    if (strpos($requestUri, '/actions/admin_reserve_table.php') !== false) {
        return 'pages/admin/floor.php';
    }

    return $requestUri;
}

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

function database_clock(?PDO $pdo = null): array {
    static $cached = null;

    if ($cached !== null) {
        return $cached;
    }

    $pdo = $pdo ?? db();
    $stmt = $pdo->query("SELECT CURDATE() AS db_date, CURTIME() AS db_time, NOW() AS db_datetime, UNIX_TIMESTAMP(NOW()) AS db_unix");
    $row = $stmt ? ($stmt->fetch(PDO::FETCH_ASSOC) ?: []) : [];
    if ($stmt) {
        $stmt->closeCursor();
    }

    $cached = [
        'date' => (string) ($row['db_date'] ?? date('Y-m-d')),
        'time' => (string) ($row['db_time'] ?? date('H:i:s')),
        'datetime' => (string) ($row['db_datetime'] ?? date('Y-m-d H:i:s')),
        'unix' => isset($row['db_unix']) ? (int) $row['db_unix'] : time(),
    ];

    return $cached;
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
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if (is_string($requestUri) && $requestUri !== '') {
            $redirectTarget = resolve_post_login_redirect($requestUri);
            if ($redirectTarget !== '') {
                $_SESSION['post_login_redirect'] = $redirectTarget;
            }
        }
        set_flash('error', 'Sign in to reserve your table and continue your booking.');
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
    if ($e instanceof PDOException) {
        return 'Something went wrong while loading this page. Please try again.';
    }
    $message = trim((string) $e->getMessage());
    return $message !== '' ? $message : 'Something went wrong while loading this page. Please try again.';
}

function guest_friendly_error_message(\Exception $e, string $fallback = 'Something went wrong. Please try again.'): string {
    $message = trim((string) $e->getMessage());
    $normalized = strtolower($message);

    if ($message === '') {
        return $fallback;
    }

    if (strpos($normalized, 'sqlstate') !== false || $e instanceof PDOException) {
        if (strpos($normalized, 'party size exceeds table capacity') !== false
            || strpos($normalized, 'cannot fit this party size') !== false
            || strpos($normalized, 'reduce the number of guests') !== false) {
            return 'The selected table cannot fit this party size. Please choose another table or reduce the number of guests.';
        }
        if (strpos($normalized, 'the selected table is no longer available') !== false
            || strpos($normalized, 'choose another seating preference or time') !== false) {
            return 'That table is no longer available. Please choose another seating preference or time.';
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
        if (strpos($normalized, 'maximum active bookings') !== false || strpos($normalized, 'limit: 12') !== false) {
            return 'You already have 12 active reservations. Please complete or cancel one before creating a new booking.';
        }
        if (strpos($normalized, 'you already have 12 active reservations') !== false) {
            return 'You already have 12 active reservations. Please complete or cancel one before creating a new booking.';
        }
        if (strpos($normalized, 'time slot conflicts') !== false || strpos($normalized, 'conflicts with an existing') !== false) {
            return 'That time is already booked. Please choose another time.';
        }
        if (strpos($normalized, 'uq_appointments_exact_table_slot') !== false
            || strpos($normalized, 'uq_appointments_exact_zone_slot') !== false) {
            return 'That reservation slot was just booked. Please review your reservations or choose another time.';
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
    $message = guest_friendly_error_message($e, 'We could not complete your reservation right now. Please try again or choose another time.');
    if (booking_debug_enabled()) {
        $debugMessage = trim((string) $e->getMessage());
        $debugCode = trim((string) $e->getCode());
        return $message . "\n\nDebug: " . ($debugCode !== '' ? '[' . $debugCode . '] ' : '') . $debugMessage;
    }
    return $message;
}

function admin_booking_error_message(\Exception $e): string {
    $message = guest_friendly_error_message($e, 'We could not complete this reservation right now. Please review the details and try again.');
    if (booking_debug_enabled()) {
        $debugMessage = trim((string) $e->getMessage());
        $debugCode = trim((string) $e->getCode());
        return $message . "\n\nDebug: " . ($debugCode !== '' ? '[' . $debugCode . '] ' : '') . $debugMessage;
    }
    return $message;
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

function maintenance_message(): string {
    return get_setting('maintenance_message', "We're temporarily offline for maintenance. Please check back shortly.");
}

function booking_is_open(): bool {
    return !is_maintenance_mode();
}

function floor_manual_occupied_minutes(): int {
    $value = (int) get_setting('floor_manual_occupied_minutes', '120');
    if ($value < 5) {
        return 5;
    }
    if ($value > 720) {
        return 720;
    }
    return $value;
}

function reservation_duration_minutes(): int {
    $value = (int) get_setting('reservation_duration_minutes', '90');
    if ($value < 30) {
        return 30;
    }
    if ($value > 240) {
        return 240;
    }
    return $value;
}

function reservation_duration_label(): string {
    $minutes = reservation_duration_minutes();
    $hours = intdiv($minutes, 60);
    $remainingMinutes = $minutes % 60;

    if ($hours > 0 && $remainingMinutes > 0) {
        return $hours . ' hour' . ($hours === 1 ? '' : 's') . ' ' . $remainingMinutes . ' minutes';
    }
    if ($hours > 0) {
        return $hours . ' hour' . ($hours === 1 ? '' : 's');
    }
    return $minutes . ' minutes';
}

function require_booking_open(bool $jsonResponse = false, bool $allowAdmin = true): void {
    if (booking_is_open() || ($allowAdmin && (($_SESSION['role_name'] ?? '') === 'admin'))) {
        return;
    }

    $message = maintenance_message();
    if ($jsonResponse) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
        exit;
    }

    set_flash('booking_error', $message);
    redirect('pages/book.php');
}
