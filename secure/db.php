<?php
/**
 * Database connection logic.
 */

function get_db() {
    static $pdo = null;
    if ($pdo === null) {
        $host = 'localhost';
        $db   = 'restaurant_booking_v1';
        $user = 'root';
        $pass = ''; // Default XAMPP
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
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
