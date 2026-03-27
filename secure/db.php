<?php
/**
 * Database connection logic.
 */

function env_or_default(string $key, string $default): string {
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }
    return (string) $value;
}

function get_db() {
    static $pdo = null;
    if ($pdo === null) {
        $host = env_or_default('DB_HOST', 'localhost');
        $db   = env_or_default('DB_NAME', 'restaurant_booking_v1');
        $user = env_or_default('DB_USER', 'root');
        $pass = getenv('DB_PASS');
        if ($pass === false) {
            $pass = ''; // Default XAMPP
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
