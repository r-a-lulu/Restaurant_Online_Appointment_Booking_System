<?php
/**
 * Database Migration: Remove the legacy table label column from tables
 *
 * Copies any existing legacy labels into seating_preference when needed,
 * then drops the legacy column and refreshes the seating_preference column
 * so the app can use the tables table as seating-preference driven data.
 */
require_once __DIR__ . '/../includes/security.php';

try {
    $pdo = db();
    echo "Checking tables schema...\n";

    $hasTableNumber = false;
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'tables'
          AND COLUMN_NAME = 'table_number'
    ");
    $stmt->execute();
    $hasTableNumber = (int) $stmt->fetchColumn() > 0;
    $stmt->closeCursor();

    if (!$hasTableNumber) {
        echo "Legacy label column is already removed.\n";
        exit(0);
    }

    echo "Migrating legacy labels into seating_preference...\n";
    $pdo->exec("
        UPDATE `tables`
        SET seating_preference = CASE
          WHEN seating_preference IS NULL OR seating_preference = '' THEN table_number
          ELSE seating_preference
        END
    ");

    echo "Dropping legacy index if present...\n";
    try {
        $pdo->exec("ALTER TABLE `tables` DROP INDEX uq_tables_zone_table_number");
    } catch (PDOException $e) {
        // Ignore if the index is already gone or the DB naming differs.
    }

    echo "Dropping legacy label column...\n";
    $pdo->exec("ALTER TABLE `tables` DROP COLUMN table_number");

    echo "Normalizing seating_preference column...\n";
    $pdo->exec("ALTER TABLE `tables` MODIFY seating_preference VARCHAR(100) NOT NULL");
    $pdo->exec("CREATE INDEX idx_tables_seating_preference ON `tables` (seating_preference)");

    echo "Migration completed successfully.\n";
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
