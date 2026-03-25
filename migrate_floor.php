<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/security.php';

try {
    $pdo = db();
    echo "Connected to DB successfully.\n";
    $pdo->exec("ALTER TABLE `tables` ADD COLUMN `current_status` ENUM('available', 'reserved', 'occupied') NOT NULL DEFAULT 'available'");
    echo "Migration completed successfully. current_status added.\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
