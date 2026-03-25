<?php
/**
 * Database Migration: Refresh table audit triggers after removing the legacy label column
 */
require_once __DIR__ . '/../includes/security.php';

try {
    $pdo = db();
    echo "Updating table triggers...\n";

    $statements = [
        "DROP TRIGGER IF EXISTS trg_tables_after_insert",
        "DROP TRIGGER IF EXISTS trg_tables_after_update",
        "DROP TRIGGER IF EXISTS trg_tables_after_delete",
        "CREATE TRIGGER trg_tables_after_insert
         AFTER INSERT ON `tables`
         FOR EACH ROW
         BEGIN
           INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
           VALUES ('tables', NEW.table_id, 'INSERT', NULL,
             JSON_OBJECT('zone_id', NEW.zone_id, 'table_label', NEW.seating_preference, 'capacity', NEW.capacity));
         END",
        "CREATE TRIGGER trg_tables_after_update
         AFTER UPDATE ON `tables`
         FOR EACH ROW
         BEGIN
           INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
           VALUES ('tables', NEW.table_id, 'UPDATE',
             JSON_OBJECT('zone_id', OLD.zone_id, 'table_label', OLD.seating_preference, 'capacity', OLD.capacity),
             JSON_OBJECT('zone_id', NEW.zone_id, 'table_label', NEW.seating_preference, 'capacity', NEW.capacity));
         END",
        "CREATE TRIGGER trg_tables_after_delete
         AFTER DELETE ON `tables`
         FOR EACH ROW
         BEGIN
           INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
           VALUES ('tables', OLD.table_id, 'DELETE',
             JSON_OBJECT('zone_id', OLD.zone_id, 'table_label', OLD.seating_preference, 'capacity', OLD.capacity), NULL);
         END",
    ];

    foreach ($statements as $stmt) {
        $pdo->exec($stmt);
    }

    echo "Table triggers updated successfully.\n";
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
