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
          t.table_number,
          t.seating_preference,
          a.appointment_date,
          a.start_time,
          a.end_time,
          a.party_size,
          a.status_id,
          fn_status_name(a.status_id) AS status_name,
          fn_appointment_total(a.appointment_id) AS total_amount,
          a.special_requests,
          a.created_at,
          a.updated_at
        FROM appointments a
        JOIN users u ON u.user_id = a.user_id
        LEFT JOIN services s ON s.service_id = a.service_id
        LEFT JOIN event_packages ep ON ep.package_id = a.event_package_id
        LEFT JOIN `tables` t ON t.table_id = a.table_id
        LEFT JOIN dining_zones dz ON dz.zone_id = COALESCE(a.zone_id, t.zone_id)"
    ];

    foreach ($statements as $stmt) {
        $pdo->exec($stmt);
    }
    echo "Views updated successfully.\n";
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
