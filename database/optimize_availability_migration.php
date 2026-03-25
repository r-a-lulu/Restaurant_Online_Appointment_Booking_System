<?php
/**
 * Database Migration: Optimize Availability Logic
 * Purpose: Improve accuracy and performance of booking availability checks.
 */
require_once __DIR__ . '/../includes/security.php';

try {
    $pdo = db();
    
    echo "Starting migration...\n";

    // 1. Remove restrictive unique index if it exists
    echo "Updating schema indices...\n";
    $pdo->exec("ALTER TABLE appointments DROP INDEX IF EXISTS uq_appointments_exact_zone_slot;");

    // 2. Update Functions
    echo "Updating database functions...\n";
    $sqlFunctions = "
    USE restaurant_booking_v1;
    DELIMITER $$

    -- Improved overlaps helper (returns 1 if overlaps)
    DROP FUNCTION IF EXISTS fn_overlaps$$
    CREATE FUNCTION fn_overlaps(p_start1 TIME, p_end1 TIME, p_start2 TIME, p_end2 TIME)
    RETURNS TINYINT
    DETERMINISTIC
    BEGIN
      -- Standard overlap check: (StartA < EndB) AND (EndA > StartB)
      RETURN IF(p_start1 < p_end2 AND p_end1 > p_start2, 1, 0);
    END$$

    -- Helper to get total number of tables in a zone for a given party size
    DROP FUNCTION IF EXISTS fn_get_zone_table_count$$
    CREATE FUNCTION fn_get_zone_table_count(p_zone_id INT, p_party_size INT, p_seating_pref VARCHAR(100))
    RETURNS INT
    READS SQL DATA
    BEGIN
      DECLARE v_count INT DEFAULT 0;
      SELECT COUNT(*) INTO v_count
      FROM `tables`
      WHERE zone_id = p_zone_id
        AND capacity >= p_party_size
        AND (p_seating_pref IS NULL OR p_seating_pref = '' OR seating_preference = p_seating_pref);
      RETURN v_count;
    END$$

    -- Helper to count overlapping bookings in a zone
    DROP FUNCTION IF EXISTS fn_get_zone_occupied_count$$
    CREATE FUNCTION fn_get_zone_occupied_count(p_zone_id INT, p_date DATE, p_start TIME, p_end TIME)
    RETURNS INT
    READS SQL DATA
    BEGIN
      DECLARE v_count INT DEFAULT 0;
      SELECT COUNT(*) INTO v_count
      FROM appointments a
      WHERE a.zone_id = p_zone_id
        AND a.appointment_date = p_date
        AND fn_overlaps(a.start_time, a.end_time, p_start, p_end) = 1
        AND fn_is_active_status(a.status_id) = 1;
      RETURN v_count;
    END$$

    -- Improved slot availability function
    DROP FUNCTION IF EXISTS fn_is_slot_available$$
    CREATE FUNCTION fn_is_slot_available(
      p_date DATE,
      p_start TIME,
      p_end TIME,
      p_table_id INT,
      p_zone_id INT,
      p_party_size INT,
      p_seating_pref VARCHAR(100)
    )
    RETURNS TINYINT
    READS SQL DATA
    BEGIN
      DECLARE v_total_tables INT;
      DECLARE v_occupied_tables INT;

      -- If a specific table is requested
      IF p_table_id IS NOT NULL THEN
        -- Check if this specific table is booked
        IF fn_table_has_conflict(p_table_id, p_date, p_start, p_end, NULL) = 1 THEN
          RETURN 0;
        END IF;
        -- Also check if the zone it belongs to is full (redundancy for safety)
        -- (Optional: based on restaurant policy)
        RETURN 1;
      END IF;

      -- If checking by zone
      IF p_zone_id IS NOT NULL THEN
        SET v_total_tables = fn_get_zone_table_count(p_zone_id, p_party_size, p_seating_pref);
        IF v_total_tables = 0 THEN
          RETURN 0;
        END IF;
        
        SET v_occupied_tables = fn_get_zone_occupied_count(p_zone_id, p_date, p_start, p_end);
        
        -- Available if occupied count < total suitable tables
        RETURN IF(v_occupied_tables < v_total_tables, 1, 0);
      END IF;

      RETURN 0;
    END$$

    DELIMITER ;
    ";
    
    // Note: PDO exec() doesn't support DELIMITER or multiple statements easily
    // We'll split the script or run it as a broad string if possible
    // Since this is for a local MYSQL/MariaDB, we'll try to execute it in blocks
    
    // Helper to run multi-query strings if possible, or just run them one by one
    // We'll use a simpler approach for the PHP script execution
    
    echo "Migration script prepared. Please run functions.sql and sp_get_available_slots.sql in your SQLyog.\n";
    echo "Wait, I will try to apply the core changes via direct PDO calls.\n";
    
    // Applying function updates manually (without DELIMITER syntax needed by CLI)
    $statements = [
        "DROP FUNCTION IF EXISTS fn_get_zone_table_count",
        "CREATE FUNCTION fn_get_zone_table_count(p_zone_id INT, p_party_size INT, p_seating_pref VARCHAR(100))
         RETURNS INT READS SQL DATA
         BEGIN
           DECLARE v_count INT DEFAULT 0;
           SELECT COUNT(*) INTO v_count FROM `tables`
           WHERE zone_id = p_zone_id AND capacity >= p_party_size
             AND (p_seating_pref IS NULL OR p_seating_pref = '' OR seating_preference = p_seating_pref);
           RETURN v_count;
         END",
        "DROP FUNCTION IF EXISTS fn_get_zone_occupied_count",
        "CREATE FUNCTION fn_get_zone_occupied_count(p_zone_id INT, p_date DATE, p_start TIME, p_end TIME)
         RETURNS INT READS SQL DATA
         BEGIN
           DECLARE v_count INT DEFAULT 0;
           SELECT COUNT(*) INTO v_count FROM appointments a
           WHERE a.zone_id = p_zone_id AND a.appointment_date = p_date
             AND (a.start_time < p_end AND a.end_time > p_start)
             AND (SELECT status_name FROM appointment_status WHERE status_id = a.status_id) IN ('pending','confirmed');
           RETURN v_count;
         END",
        "DROP FUNCTION IF EXISTS fn_is_slot_available",
        "CREATE FUNCTION fn_is_slot_available(p_date DATE, p_start TIME, p_end TIME, p_table_id INT, p_zone_id INT, p_party_size INT, p_seating_pref VARCHAR(100))
         RETURNS TINYINT READS SQL DATA
         BEGIN
           DECLARE v_total_tables INT;
           DECLARE v_occupied_tables INT;
           IF p_table_id IS NOT NULL THEN
             IF (SELECT COUNT(*) FROM appointments a WHERE a.table_id = p_table_id AND a.appointment_date = p_date
                 AND (a.start_time < p_end AND a.end_time > p_start)
                 AND (SELECT status_name FROM appointment_status WHERE status_id = a.status_id) IN ('pending','confirmed')) > 0 THEN
               RETURN 0;
             END IF;
             RETURN 1;
           END IF;
           IF p_zone_id IS NOT NULL THEN
             SET v_total_tables = fn_get_zone_table_count(p_zone_id, p_party_size, p_seating_pref);
             IF v_total_tables = 0 THEN RETURN 0; END IF;
             SET v_occupied_tables = fn_get_zone_occupied_count(p_zone_id, p_date, p_start, p_end);
             RETURN IF(v_occupied_tables < v_total_tables, 1, 0);
           END IF;
           RETURN 0;
         END",
        "DROP PROCEDURE IF EXISTS sp_get_available_slots",
        "CREATE PROCEDURE sp_get_available_slots(
           IN p_date DATE,
           IN p_zone_id INT,
           IN p_party_size INT,
           IN p_seating_pref VARCHAR(100)
         )
         BEGIN
           -- Create a temporary table for the time slots we want to check
           CREATE TEMPORARY TABLE IF NOT EXISTS tmp_slots (
             slot_time TIME
           );
           DELETE FROM tmp_slots;
           INSERT INTO tmp_slots (slot_time) VALUES 
           ('17:00:00'),('17:30:00'),('18:00:00'),('18:30:00'),
           ('19:00:00'),('19:30:00'),('20:00:00'),('20:30:00'),
           ('21:00:00'),('21:30:00'),('22:00:00');

           SELECT 
             slot_time,
             fn_is_slot_available(p_date, slot_time, ADDTIME(slot_time, '02:00:00'), NULL, p_zone_id, p_party_size, p_seating_pref) AS is_available
           FROM tmp_slots;
           
           DROP TEMPORARY TABLE tmp_slots;
         END"
    ];

    
    foreach ($statements as $stmt) {
        $pdo->exec($stmt);
    }
    
    echo "Migration completed successfully.\n";

} catch (Exception $e) {
    die("Migration failed: " . $e->getMessage());
}
