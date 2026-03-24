<?php
require_once __DIR__ . '/includes/security.php';

try {
    $pdo = db();
    
    // 1. Add column if it doesn't exist
    // MySQL 8+ doesn't have IF NOT EXISTS for columns in ALTER TABLE generally, but we can catch the exception.
    try {
        $pdo->exec("ALTER TABLE `tables` ADD COLUMN seating_preference VARCHAR(100) NULL");
        echo "Added column seating_preference to tables.\n";
    } catch (PDOException $e) {
        // SQLSTATE 42S21 means Duplicate column name
        if ($e->getCode() == '42S21') {
            echo "Column seating_preference already exists.\n";
        } else {
            throw $e;
        }
    }

    // 2. Re-create the View
    $sqlView = "CREATE OR REPLACE VIEW vw_available_tables AS
SELECT
  t.table_id,
  t.table_number,
  t.capacity,
  t.seating_preference,
  dz.zone_id,
  dz.zone_name
FROM `tables` t
JOIN dining_zones dz ON dz.zone_id = t.zone_id
ORDER BY dz.zone_name, t.table_number;";
    $pdo->exec($sqlView);
    echo "View vw_available_tables updated.\n";
    
    // 3. Re-seed tables
    $seedSql = "INSERT INTO tables (zone_id, table_number, capacity, seating_preference)
SELECT dz.zone_id, t.table_number, t.capacity, t.seating_preference
FROM (
  SELECT 'Main Dining Room' AS zone_name, 'Table 1' AS table_number, 2 AS capacity, 'Window Table' AS seating_preference UNION ALL
  SELECT 'Main Dining Room', 'Table 2', 2, 'Window Table' UNION ALL
  SELECT 'Main Dining Room', 'Table 3', 4, 'Banquette' UNION ALL
  SELECT 'Main Dining Room', 'Table 4', 4, 'Banquette' UNION ALL
  SELECT 'Main Dining Room', 'Table 5', 6, 'Fireplace' UNION ALL
  SELECT 'Main Dining Room', 'Table 6', 4, 'Chef\'s View' UNION ALL
  SELECT 'Main Dining Room', 'Table 7', 2, 'Private Alcove' UNION ALL
  SELECT 'Main Dining Room', 'Table 8', 8, 'Chandelier' UNION ALL
  SELECT 'Main Dining Room', 'Table 9', 4, 'Window Table' UNION ALL
  SELECT 'Main Dining Room', 'Table 10', 2, 'Banquette' UNION ALL
  SELECT 'The Patio', 'Garden 1', 2, 'Garden View' UNION ALL
  SELECT 'The Patio', 'Garden 2', 4, 'Garden View' UNION ALL
  SELECT 'The Patio', 'Fountain', 4, 'Fountain Side' UNION ALL
  SELECT 'The Patio', 'Pergola', 6, 'Pergola' UNION ALL
  SELECT 'The Patio', 'Corner', 4, 'Corner Alcove' UNION ALL
  SELECT 'The Bar', 'Bar 1', 2, 'Bar Counter' UNION ALL
  SELECT 'The Bar', 'Bar 2', 2, 'Bar Counter' UNION ALL
  SELECT 'The Bar', 'High Top 1', 4, 'High Tops' UNION ALL
  SELECT 'The Bar', 'High Top 2', 4, 'High Tops' UNION ALL
  SELECT 'The Bar', 'Lounge', 6, 'Lounge Booths'
) AS t
JOIN dining_zones dz ON dz.zone_name = t.zone_name
ON DUPLICATE KEY UPDATE capacity = VALUES(capacity), seating_preference = VALUES(seating_preference);";
    $pdo->exec($seedSql);
    echo "Seed data for seating_preference executed.\n";

} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage() . "\n";
}
