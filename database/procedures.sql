-- =========================================================
-- Restaurant Online Appointment Booking System
-- Stored Procedures (MariaDB/MySQL compatible)
-- Database: restaurant_booking_v1
-- =========================================================
--
-- Procedure Index
-- Procedure #1:  sp_user_create
-- Procedure #2:  sp_user_get_by_email
-- Procedure #3:  sp_services_create
-- Procedure #4:  sp_services_update
-- Procedure #5:  sp_services_delete
-- Procedure #6:  sp_add_ons_create
-- Procedure #7:  sp_add_ons_update
-- Procedure #8:  sp_add_ons_delete
-- Procedure #9:  sp_dining_zones_create
-- Procedure #10: sp_dining_zones_update
-- Procedure #11: sp_dining_zones_delete
-- Procedure #12: sp_tables_create
-- Procedure #13: sp_tables_update
-- Procedure #14: sp_tables_delete
-- Procedure #15: sp_find_available_table
-- Procedure #16: sp_validate_table
-- Procedure #17: sp_appointment_create
-- Procedure #18: sp_appointment_add_on_add
-- Procedure #19: sp_reports_daily_summary
-- Procedure #20: sp_update_appointment_status

USE restaurant_booking_v1;

DELIMITER $$

-- ---------------------------------------------------------
-- USERS / AUTH
-- ---------------------------------------------------------

DROP PROCEDURE IF EXISTS sp_user_create$$
CREATE PROCEDURE sp_user_create(
  IN p_role_id INT,
  IN p_first_name VARCHAR(100),
  IN p_last_name VARCHAR(100),
  IN p_email VARCHAR(255),
  IN p_phone VARCHAR(30),
  IN p_password_hash VARCHAR(255),
  IN p_created_by INT
)
BEGIN
  INSERT INTO users (role_id, first_name, last_name, email, phone, password_hash, created_by)
  VALUES (p_role_id, p_first_name, p_last_name, p_email, p_phone, p_password_hash, p_created_by);

  SELECT LAST_INSERT_ID() AS user_id;
END$$

DROP PROCEDURE IF EXISTS sp_user_get_by_email$$
CREATE PROCEDURE sp_user_get_by_email(IN p_email VARCHAR(255))
BEGIN
  SELECT u.*
  FROM users u
  WHERE u.email = p_email;
END$$

-- ---------------------------------------------------------
-- MASTER DATA: SERVICES
-- ---------------------------------------------------------

DROP PROCEDURE IF EXISTS sp_services_create$$
CREATE PROCEDURE sp_services_create(
  IN p_service_name VARCHAR(100),
  IN p_price DECIMAL(10,2)
)
BEGIN
  INSERT INTO services (service_name, price)
  VALUES (p_service_name, p_price);

  SELECT LAST_INSERT_ID() AS service_id;
END$$

DROP PROCEDURE IF EXISTS sp_services_update$$
CREATE PROCEDURE sp_services_update(
  IN p_service_id INT,
  IN p_service_name VARCHAR(100),
  IN p_price DECIMAL(10,2)
)
BEGIN
  UPDATE services
  SET service_name = p_service_name,
      price = p_price
  WHERE service_id = p_service_id;

  SELECT ROW_COUNT() AS rows_affected;
END$$

DROP PROCEDURE IF EXISTS sp_services_delete$$
CREATE PROCEDURE sp_services_delete(IN p_service_id INT)
BEGIN
  DELETE FROM services WHERE service_id = p_service_id;
  SELECT ROW_COUNT() AS rows_affected;
END$$

-- ---------------------------------------------------------
-- MASTER DATA: ADD-ONS
-- ---------------------------------------------------------

DROP PROCEDURE IF EXISTS sp_add_ons_create$$
CREATE PROCEDURE sp_add_ons_create(
  IN p_category VARCHAR(80),
  IN p_name VARCHAR(120),
  IN p_description VARCHAR(500),
  IN p_price DECIMAL(10,2)
)
BEGIN
  INSERT INTO add_ons (category, name, description, price)
  VALUES (p_category, p_name, p_description, p_price);

  SELECT LAST_INSERT_ID() AS add_on_id;
END$$

DROP PROCEDURE IF EXISTS sp_add_ons_update$$
CREATE PROCEDURE sp_add_ons_update(
  IN p_add_on_id INT,
  IN p_category VARCHAR(80),
  IN p_name VARCHAR(120),
  IN p_description VARCHAR(500),
  IN p_price DECIMAL(10,2)
)
BEGIN
  UPDATE add_ons
  SET category = p_category,
      name = p_name,
      description = p_description,
      price = p_price
  WHERE add_on_id = p_add_on_id;

  SELECT ROW_COUNT() AS rows_affected;
END$$

DROP PROCEDURE IF EXISTS sp_add_ons_delete$$
CREATE PROCEDURE sp_add_ons_delete(IN p_add_on_id INT)
BEGIN
  DELETE FROM add_ons WHERE add_on_id = p_add_on_id;
  SELECT ROW_COUNT() AS rows_affected;
END$$

-- ---------------------------------------------------------
-- MASTER DATA: DINING ZONES
-- ---------------------------------------------------------

DROP PROCEDURE IF EXISTS sp_dining_zones_create$$
CREATE PROCEDURE sp_dining_zones_create(
  IN p_zone_name VARCHAR(100)
)
BEGIN
  INSERT INTO dining_zones (zone_name)
  VALUES (p_zone_name);

  SELECT LAST_INSERT_ID() AS zone_id;
END$$

DROP PROCEDURE IF EXISTS sp_dining_zones_update$$
CREATE PROCEDURE sp_dining_zones_update(
  IN p_zone_id INT,
  IN p_zone_name VARCHAR(100)
)
BEGIN
  UPDATE dining_zones
  SET zone_name = p_zone_name
  WHERE zone_id = p_zone_id;

  SELECT ROW_COUNT() AS rows_affected;
END$$

DROP PROCEDURE IF EXISTS sp_dining_zones_delete$$
CREATE PROCEDURE sp_dining_zones_delete(IN p_zone_id INT)
BEGIN
  DELETE FROM dining_zones WHERE zone_id = p_zone_id;
  SELECT ROW_COUNT() AS rows_affected;
END$$

-- ---------------------------------------------------------
-- MASTER DATA: TABLES
-- ---------------------------------------------------------

DROP PROCEDURE IF EXISTS sp_tables_create$$
CREATE PROCEDURE sp_tables_create(
  IN p_zone_id INT,
  IN p_capacity INT,
  IN p_seating_preference VARCHAR(100)
)
BEGIN
  INSERT INTO `tables` (zone_id, capacity, seating_preference)
  VALUES (p_zone_id, p_capacity, p_seating_preference);

  SELECT LAST_INSERT_ID() AS table_id;
END$$

DROP PROCEDURE IF EXISTS sp_tables_update$$
CREATE PROCEDURE sp_tables_update(
  IN p_table_id INT,
  IN p_zone_id INT,
  IN p_capacity INT,
  IN p_seating_preference VARCHAR(100)
)
BEGIN
  UPDATE `tables`
  SET zone_id = p_zone_id,
      capacity = p_capacity,
      seating_preference = p_seating_preference
  WHERE table_id = p_table_id;

  SELECT ROW_COUNT() AS rows_affected;
END$$

DROP PROCEDURE IF EXISTS sp_tables_delete$$
CREATE PROCEDURE sp_tables_delete(IN p_table_id INT)
BEGIN
  DELETE FROM `tables` WHERE table_id = p_table_id;
  SELECT ROW_COUNT() AS rows_affected;
END$$

DROP PROCEDURE IF EXISTS sp_find_available_table$$
CREATE PROCEDURE sp_find_available_table(
  IN p_zone_id INT,
  IN p_party_size INT,
  IN p_seating_preference VARCHAR(100),
  IN p_appointment_date DATE,
  IN p_start_time TIME,
  IN p_end_time TIME
)
BEGIN
  SELECT
    t.table_id,
    t.zone_id,
    t.capacity,
    t.seating_preference,
    CASE
      WHEN t.current_status = 'occupied'
        AND t.manual_status_until IS NOT NULL
        AND t.manual_status_until > NOW()
      THEN 'occupied'
      ELSE 'available'
    END AS current_status
  FROM `tables` t
  WHERE t.zone_id = p_zone_id
    AND t.capacity >= p_party_size
    AND NOT (
      t.current_status = 'occupied'
      AND t.manual_status_until IS NOT NULL
      AND t.manual_status_until > NOW()
    )
    AND (p_seating_preference = '' OR t.seating_preference = p_seating_preference)
    AND fn_is_slot_available(p_appointment_date, p_start_time, p_end_time, t.table_id, p_zone_id, NULL) = 1
  ORDER BY t.capacity ASC, t.seating_preference ASC, t.table_id ASC
  LIMIT 1;
END$$

DROP PROCEDURE IF EXISTS sp_validate_table$$
CREATE PROCEDURE sp_validate_table(
  IN p_table_id INT,
  IN p_zone_id INT,
  IN p_seating_preference VARCHAR(100),
  IN p_party_size INT
)
BEGIN
  DECLARE v_zone_id INT DEFAULT NULL;
  DECLARE v_capacity INT DEFAULT NULL;
  DECLARE v_seating_preference VARCHAR(100) DEFAULT NULL;
  DECLARE v_current_status VARCHAR(20) DEFAULT NULL;

  SELECT
    zone_id,
    capacity,
    seating_preference,
    CASE
      WHEN current_status = 'occupied'
        AND manual_status_until IS NOT NULL
        AND manual_status_until > NOW()
      THEN 'occupied'
      ELSE 'available'
    END
  INTO v_zone_id, v_capacity, v_seating_preference, v_current_status
  FROM `tables`
  WHERE table_id = p_table_id
  LIMIT 1;

  IF v_zone_id IS NULL THEN
    SELECT
      0 AS is_valid,
      'TABLE_NOT_FOUND' AS error_code,
      'The selected table could not be found.' AS error_message,
      NULL AS table_id,
      NULL AS zone_id,
      NULL AS seating_preference,
      NULL AS capacity,
      NULL AS current_status;
  ELSEIF v_zone_id <> p_zone_id THEN
    SELECT
      0 AS is_valid,
      'ZONE_MISMATCH' AS error_code,
      'The selected table does not belong to the chosen dining zone.' AS error_message,
      p_table_id AS table_id,
      v_zone_id AS zone_id,
      v_seating_preference AS seating_preference,
      v_capacity AS capacity,
      v_current_status AS current_status;
  ELSEIF p_seating_preference <> '' AND LOWER(TRIM(v_seating_preference)) <> LOWER(TRIM(p_seating_preference)) THEN
    SELECT
      0 AS is_valid,
      'SEATING_MISMATCH' AS error_code,
      'The selected table does not match the selected seating preference.' AS error_message,
      p_table_id AS table_id,
      v_zone_id AS zone_id,
      v_seating_preference AS seating_preference,
      v_capacity AS capacity,
      v_current_status AS current_status;
  ELSEIF v_capacity < p_party_size THEN
    SELECT
      0 AS is_valid,
      'CAPACITY_TOO_SMALL' AS error_code,
      'The selected seating preference cannot fit this party size. Please choose another one or reduce the guest count.' AS error_message,
      p_table_id AS table_id,
      v_zone_id AS zone_id,
      v_seating_preference AS seating_preference,
      v_capacity AS capacity,
      v_current_status AS current_status;
  ELSEIF v_current_status = 'occupied' THEN
    SELECT
      0 AS is_valid,
      'TABLE_UNAVAILABLE' AS error_code,
      'That table is no longer available. Please choose another seating preference or time.' AS error_message,
      p_table_id AS table_id,
      v_zone_id AS zone_id,
      v_seating_preference AS seating_preference,
      v_capacity AS capacity,
      v_current_status AS current_status;
  ELSE
    SELECT
      1 AS is_valid,
      NULL AS error_code,
      NULL AS error_message,
      p_table_id AS table_id,
      v_zone_id AS zone_id,
      v_seating_preference AS seating_preference,
      v_capacity AS capacity,
      v_current_status AS current_status;
  END IF;
END$$

-- ---------------------------------------------------------
-- APPOINTMENTS
-- ---------------------------------------------------------

DROP PROCEDURE IF EXISTS sp_appointment_create$$
CREATE PROCEDURE sp_appointment_create(
  IN p_user_id INT,
  IN p_service_id INT,
  IN p_table_id INT,
  IN p_zone_id INT,
  IN p_event_package_id INT,
  IN p_appointment_date DATE,
  IN p_start_time TIME,
  IN p_end_time TIME,
  IN p_party_size INT,
  IN p_status_id INT,
  IN p_special_requests TEXT
)
BEGIN
  START TRANSACTION;

  INSERT INTO appointments (
    user_id, service_id, table_id, zone_id, event_package_id,
    appointment_date, start_time, end_time, party_size, status_id, special_requests
  )
  VALUES (
    p_user_id, p_service_id, p_table_id, p_zone_id, p_event_package_id,
    p_appointment_date, p_start_time, p_end_time, p_party_size, p_status_id, p_special_requests
  );

  COMMIT;

  SELECT LAST_INSERT_ID() AS appointment_id;
END$$

-- ---------------------------------------------------------
-- APPOINTMENT ADD-ONS
-- ---------------------------------------------------------

DROP PROCEDURE IF EXISTS sp_appointment_add_on_add$$
CREATE PROCEDURE sp_appointment_add_on_add(
  IN p_appointment_id INT,
  IN p_add_on_id INT,
  IN p_quantity INT
)
BEGIN
  INSERT INTO appointment_add_ons (appointment_id, add_on_id, quantity)
  VALUES (p_appointment_id, p_add_on_id, p_quantity);

  SELECT ROW_COUNT() AS rows_affected;
END$$

-- ---------------------------------------------------------
-- REPORTING
-- ---------------------------------------------------------

DROP PROCEDURE IF EXISTS sp_reports_daily_summary$$
CREATE PROCEDURE sp_reports_daily_summary(IN p_date DATE)
BEGIN
  SELECT
    p_date AS report_date,
    fn_daily_booking_count(p_date) AS total_bookings,
    fn_daily_revenue(p_date) AS total_revenue;
END$$

DROP PROCEDURE IF EXISTS sp_update_appointment_status$$
CREATE PROCEDURE sp_update_appointment_status(
  IN p_appointment_id INT,
  IN p_status_name VARCHAR(30)
)
BEGIN
  DECLARE v_status_id INT;

  SELECT status_id INTO v_status_id
  FROM appointment_status
  WHERE status_name = p_status_name
  LIMIT 1;

  IF v_status_id IS NULL THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Invalid appointment status.';
  END IF;

  UPDATE appointments
  SET status_id = v_status_id
  WHERE appointment_id = p_appointment_id;

  SELECT ROW_COUNT() AS rows_affected;
END$$

DELIMITER ;
