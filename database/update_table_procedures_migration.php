<?php
/**
 * Database Migration: Update Table Procedures for Seating Preference
 */
require_once __DIR__ . '/../includes/security.php';

try {
    $pdo = db();
    echo "Updating stored procedures...\n";

    $statements = [
        "DROP PROCEDURE IF EXISTS sp_tables_create",
        "CREATE PROCEDURE sp_tables_create(
           IN p_zone_id INT,
           IN p_table_number VARCHAR(30),
           IN p_capacity INT,
           IN p_seating_preference VARCHAR(100)
         )
         BEGIN
           INSERT INTO `tables` (zone_id, table_number, capacity, seating_preference)
           VALUES (p_zone_id, p_table_number, p_capacity, p_seating_preference);
           SELECT LAST_INSERT_ID() AS table_id;
         END",
        "DROP PROCEDURE IF EXISTS sp_tables_update",
        "CREATE PROCEDURE sp_tables_update(
           IN p_table_id INT,
           IN p_zone_id INT,
           IN p_table_number VARCHAR(30),
           IN p_capacity INT,
           IN p_seating_preference VARCHAR(100)
         )
         BEGIN
           UPDATE `tables`
           SET zone_id = p_zone_id,
               table_number = p_table_number,
               capacity = p_capacity,
               seating_preference = p_seating_preference
           WHERE table_id = p_table_id;
           SELECT ROW_COUNT() AS rows_affected;
         END"
    ];

    foreach ($statements as $stmt) {
        $pdo->exec($stmt);
    }
    echo "Migration completed successfully.\n";
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
