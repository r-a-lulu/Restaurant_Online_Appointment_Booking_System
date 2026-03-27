<?php
/**
 * Database connection logic.
 */

function load_env_file_if_present(): void {
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $loaded = true;

    $envPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
    if (!is_file($envPath) || !is_readable($envPath)) {
        return;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        $parts = explode('=', $trimmed, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key = trim($parts[0]);
        $value = trim($parts[1]);
        if ($key === '') {
            continue;
        }

        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"'))
            || (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        if (getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

function env_or_default(string $key, string $default): string {
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }
    return (string) $value;
}

function db_is_local_runtime(): bool {
    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $remoteAddr = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));
    $serverAddr = trim((string) ($_SERVER['SERVER_ADDR'] ?? ''));
    $isCli = PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg';

    if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
        return true;
    }

    if (in_array($remoteAddr, ['127.0.0.1', '::1'], true) || in_array($serverAddr, ['127.0.0.1', '::1'], true)) {
        return true;
    }

    if ($isCli) {
        return true;
    }

    return strpos($host, '.local') !== false;
}

function env_or_fail(string $key): string {
    $value = getenv($key);
    if ($value === false || $value === '') {
        throw new RuntimeException('Missing required database environment variable: ' . $key);
    }
    return (string) $value;
}

function get_db() {
    static $pdo = null;
    if ($pdo === null) {
        load_env_file_if_present();
        $isLocalRuntime = db_is_local_runtime();
        if ($isLocalRuntime) {
            $host = env_or_default('DB_HOST', 'localhost');
            $db   = env_or_default('DB_NAME', 'restaurant_booking_v1');
            $user = env_or_default('DB_USER', 'root');
            $pass = getenv('DB_PASS');
            if ($pass === false) {
                $pass = ''; // Local XAMPP fallback only
            }
        } else {
            $host = env_or_fail('DB_HOST');
            $db   = env_or_fail('DB_NAME');
            $user = env_or_fail('DB_USER');
            $pass = env_or_fail('DB_PASS');
        }
        $charset = env_or_default('DB_CHARSET', 'utf8mb4');
        $timeZone = env_or_default('DB_TIMEZONE', '+08:00');

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
            $timeZoneStmt = $pdo->prepare('SET time_zone = ?');
            $timeZoneStmt->execute([$timeZone]);
        } catch (\PDOException $e) {
            // Re-throw so callers can handle or show an error screen
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    return $pdo;
}

// Backward-compatible alias used across pages/actions
function db() {
    return get_db();
}

/**
 * Call a stored procedure and optionally pass parameters.
 * Returns the executed PDOStatement.
 */
function call_procedure($proc_name, $params = []) {
    $db = get_db();
    
    $placeholders = [];
    foreach ($params as $key => $val) {
        $placeholders[] = '?';
    }
    $placeholder_str = implode(', ', $placeholders);
    
    $sql = "CALL $proc_name($placeholder_str)";
    $stmt = $db->prepare($sql);
    
    $i = 1;
    foreach ($params as $val) {
        $stmt->bindValue($i++, $val);
    }
    
    $stmt->execute();
    return $stmt;
}
