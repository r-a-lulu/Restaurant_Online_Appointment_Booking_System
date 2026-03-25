<?php
/**
 * Database Migration: Update Views for Seating Preference
 */
require_once __DIR__ . '/../includes/security.php';

try {
    $pdo = db();
    echo "Updating database views...\n";

    $statements = [
        "CREATE OR REPLACE VIEW vw_appointments_detail AS
        SELECT
          a.appointment_id,
          a.user_id,
          CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
          u.email AS customer_email,
          a.service_id,
          s.service_name,
          a.event_package_id,
          ep.package_name,
          COALESCE(a.zone_id, t.zone_id) AS zone_id,
          dz.zone_name,
          a.table_id,
          t.seating_preference AS table_label,
          a.appointment_date,
          a.start_time,
          a.end_time,
          a.party_size,
          a.status_id,
          st.status_name AS status_name,
          a.special_requests,
          a.created_at,
          a.updated_at
        FROM appointments a
        JOIN users u ON u.user_id = a.user_id
        JOIN appointment_status st ON st.status_id = a.status_id
        LEFT JOIN services s ON s.service_id = a.service_id
        LEFT JOIN event_packages ep ON ep.package_id = a.event_package_id
        LEFT JOIN `tables` t ON t.table_id = a.table_id
        LEFT JOIN dining_zones dz ON dz.zone_id = COALESCE(a.zone_id, t.zone_id)",
        "CREATE OR REPLACE VIEW vw_admin_appointments AS
        SELECT
          a.appointment_id,
          a.user_id,
          CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
          u.email AS customer_email,
          COALESCE(a.zone_id, t.zone_id) AS zone_id,
          dz.zone_name,
          a.table_id,
          t.seating_preference AS table_label,
          a.appointment_date,
          a.start_time,
          a.end_time,
          a.party_size,
          a.status_id,
          st.status_name AS status_name,
          a.special_requests,
          a.created_at,
          a.updated_at
        FROM appointments a
        JOIN users u ON u.user_id = a.user_id
        JOIN appointment_status st ON st.status_id = a.status_id
        LEFT JOIN `tables` t ON t.table_id = a.table_id
        LEFT JOIN dining_zones dz ON dz.zone_id = COALESCE(a.zone_id, t.zone_id)
        ORDER BY appointment_date DESC, start_time DESC",
        "CREATE OR REPLACE VIEW vw_upcoming_appointments AS
        SELECT *
        FROM vw_appointments_detail
        WHERE appointment_date >= CURDATE()
          AND status_name NOT IN ('completed', 'cancelled', 'no_show')"
    ];

    foreach ($statements as $stmt) {
        $pdo->exec($stmt);
    }
    echo "Views updated successfully.\n";
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
