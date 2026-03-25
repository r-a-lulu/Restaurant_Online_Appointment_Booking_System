-- =========================================================
-- Restaurant Online Appointment Booking System
-- Stored Procedures (MariaDB/MySQL compatible)
-- Database: restaurant_booking_v1
-- =========================================================
--
-- Procedure Index
-- Procedure #1:  sp_user_create
-- Procedure #2:  sp_user_update
-- Procedure #3:  sp_user_deactivate
-- Procedure #4:  sp_user_get_by_id
-- Procedure #5:  sp_user_get_by_email
-- Procedure #6:  sp_roles_list
-- Procedure #7:  sp_services_create
-- Procedure #8:  sp_services_update
-- Procedure #9:  sp_services_delete
-- Procedure #10: sp_event_packages_create
-- Procedure #11: sp_event_packages_update
-- Procedure #12: sp_event_packages_delete
-- Procedure #13: sp_add_ons_create
-- Procedure #14: sp_add_ons_update
-- Procedure #15: sp_add_ons_delete
-- Procedure #16: sp_dining_zones_create
-- Procedure #17: sp_dining_zones_update
-- Procedure #18: sp_dining_zones_delete
-- Procedure #19: sp_tables_create
-- Procedure #20: sp_tables_update
-- Procedure #21: sp_tables_delete
-- Procedure #22: sp_appointment_create
-- Procedure #23: sp_appointment_update
-- Procedure #24: sp_appointment_cancel
-- Procedure #25: sp_appointment_get_by_id
-- Procedure #26: sp_appointment_list_by_user
-- Procedure #27: sp_appointment_list_admin
-- Procedure #28: sp_appointment_add_on_add
-- Procedure #29: sp_appointment_add_on_update
-- Procedure #30: sp_appointment_add_on_remove
-- Procedure #31: sp_reports_daily_summary
-- Procedure #32: sp_audit_appointment_log
-- Procedure #33: sp_audit_general_log
-- Procedure #34: sp_status_list
-- Procedure #35: sp_seed_default_statuses
-- Procedure #36: sp_update_appointment_status

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

DROP PROCEDURE IF EXISTS sp_user_update$$
CREATE PROCEDURE sp_user_update(
  IN p_user_id INT,
  IN p_role_id INT,
  IN p_first_name VARCHAR(100),
  IN p_last_name VARCHAR(100),
  IN p_email VARCHAR(255),
  IN p_phone VARCHAR(30),
  IN p_is_active BOOLEAN
)
BEGIN
  UPDATE users
  SET role_id = p_role_id,
      first_name = p_first_name,
      last_name = p_last_name,
      email = p_email,
      phone = p_phone,
      is_active = p_is_active
  WHERE user_id = p_user_id;

  SELECT ROW_COUNT() AS rows_affected;
END$$

DROP PROCEDURE IF EXISTS sp_user_deactivate$$
CREATE PROCEDURE sp_user_deactivate(IN p_user_id INT)
BEGIN
  UPDATE users
  SET is_active = FALSE
  WHERE user_id = p_user_id;

  SELECT ROW_COUNT() AS rows_affected;
END$$

DROP PROCEDURE IF EXISTS sp_user_get_by_id$$
CREATE PROCEDURE sp_user_get_by_id(IN p_user_id INT)
BEGIN
  SELECT u.*
  FROM users u
  WHERE u.user_id = p_user_id;
END$$

DROP PROCEDURE IF EXISTS sp_user_get_by_email$$
CREATE PROCEDURE sp_user_get_by_email(IN p_email VARCHAR(255))
BEGIN
  SELECT u.*
  FROM users u
  WHERE u.email = p_email;
END$$

DROP PROCEDURE IF EXISTS sp_roles_list$$
CREATE PROCEDURE sp_roles_list()
BEGIN
  SELECT role_id, role_name, permissions_description
  FROM roles
  ORDER BY role_name;
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
-- MASTER DATA: EVENT PACKAGES
-- ---------------------------------------------------------

DROP PROCEDURE IF EXISTS sp_event_packages_create$$
CREATE PROCEDURE sp_event_packages_create(
  IN p_package_name VARCHAR(120),
  IN p_description VARCHAR(500),
  IN p_base_price DECIMAL(10,2)
)
BEGIN
  INSERT INTO event_packages (package_name, description, base_price)
  VALUES (p_package_name, p_description, p_base_price);

  SELECT LAST_INSERT_ID() AS package_id;
END$$

DROP PROCEDURE IF EXISTS sp_event_packages_update$$
CREATE PROCEDURE sp_event_packages_update(
  IN p_package_id INT,
  IN p_package_name VARCHAR(120),
  IN p_description VARCHAR(500),
  IN p_base_price DECIMAL(10,2)
)
BEGIN
  UPDATE event_packages
  SET package_name = p_package_name,
      description = p_description,
      base_price = p_base_price
  WHERE package_id = p_package_id;

  SELECT ROW_COUNT() AS rows_affected;
END$$

DROP PROCEDURE IF EXISTS sp_event_packages_delete$$
CREATE PROCEDURE sp_event_packages_delete(IN p_package_id INT)
BEGIN
  DELETE FROM event_packages WHERE package_id = p_package_id;
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
  IN p_table_number VARCHAR(30),
  IN p_capacity INT,
  IN p_seating_preference VARCHAR(100)
)
BEGIN
  INSERT INTO `tables` (zone_id, table_number, capacity, seating_preference)
  VALUES (p_zone_id, p_table_number, p_capacity, p_seating_preference);

  SELECT LAST_INSERT_ID() AS table_id;
END$$

DROP PROCEDURE IF EXISTS sp_tables_update$$
CREATE PROCEDURE sp_tables_update(
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
END$$

DROP PROCEDURE IF EXISTS sp_tables_delete$$
CREATE PROCEDURE sp_tables_delete(IN p_table_id INT)
BEGIN
  DELETE FROM `tables` WHERE table_id = p_table_id;
  SELECT ROW_COUNT() AS rows_affected;
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

DROP PROCEDURE IF EXISTS sp_appointment_update$$
CREATE PROCEDURE sp_appointment_update(
  IN p_appointment_id INT,
  IN p_service_id INT,
  IN p_table_id INT,
  IN p_zone_id INT,
  IN p_event_package_id INT,
  IN p_appointment_date DATE,
  IN p_start_time TIME,
  IN p_end_time TIME,
  IN p_party_size INT,
  IN p_status_id INT
)
BEGIN
  UPDATE appointments
  SET service_id = p_service_id,
      table_id = p_table_id,
      zone_id = p_zone_id,
      event_package_id = p_event_package_id,
      appointment_date = p_appointment_date,
      start_time = p_start_time,
      end_time = p_end_time,
      party_size = p_party_size,
      status_id = p_status_id
  WHERE appointment_id = p_appointment_id;

  SELECT ROW_COUNT() AS rows_affected;
END$$

DROP PROCEDURE IF EXISTS sp_appointment_cancel$$
CREATE PROCEDURE sp_appointment_cancel(
  IN p_appointment_id INT,
  IN p_cancelled_status_id INT
)
BEGIN
  UPDATE appointments
  SET status_id = p_cancelled_status_id
  WHERE appointment_id = p_appointment_id;

  SELECT ROW_COUNT() AS rows_affected;
END$$

DROP PROCEDURE IF EXISTS sp_appointment_get_by_id$$
CREATE PROCEDURE sp_appointment_get_by_id(IN p_appointment_id INT)
BEGIN
  SELECT a.*,
         fn_status_name(a.status_id) AS status_name,
         fn_appointment_total(a.appointment_id) AS total_amount
  FROM appointments a
  WHERE a.appointment_id = p_appointment_id;
END$$

DROP PROCEDURE IF EXISTS sp_appointment_list_by_user$$
CREATE PROCEDURE sp_appointment_list_by_user(IN p_user_id INT)
BEGIN
  SELECT a.*,
         fn_status_name(a.status_id) AS status_name,
         fn_appointment_total(a.appointment_id) AS total_amount
  FROM appointments a
  WHERE a.user_id = p_user_id
  ORDER BY a.appointment_date DESC, a.start_time DESC;
END$$

DROP PROCEDURE IF EXISTS sp_appointment_list_admin$$
CREATE PROCEDURE sp_appointment_list_admin(
  IN p_date_from DATE,
  IN p_date_to DATE,
  IN p_status_id INT,
  IN p_zone_id INT,
  IN p_service_id INT
)
BEGIN
  SELECT a.*,
         fn_status_name(a.status_id) AS status_name,
         fn_appointment_total(a.appointment_id) AS total_amount
  FROM appointments a
  WHERE (p_date_from IS NULL OR a.appointment_date >= p_date_from)
    AND (p_date_to IS NULL OR a.appointment_date <= p_date_to)
    AND (p_status_id IS NULL OR a.status_id = p_status_id)
    AND (p_zone_id IS NULL OR a.zone_id = p_zone_id)
    AND (p_service_id IS NULL OR a.service_id = p_service_id)
  ORDER BY a.appointment_date DESC, a.start_time DESC;
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

DROP PROCEDURE IF EXISTS sp_appointment_add_on_update$$
CREATE PROCEDURE sp_appointment_add_on_update(
  IN p_appointment_id INT,
  IN p_add_on_id INT,
  IN p_quantity INT
)
BEGIN
  UPDATE appointment_add_ons
  SET quantity = p_quantity
  WHERE appointment_id = p_appointment_id AND add_on_id = p_add_on_id;

  SELECT ROW_COUNT() AS rows_affected;
END$$

DROP PROCEDURE IF EXISTS sp_appointment_add_on_remove$$
CREATE PROCEDURE sp_appointment_add_on_remove(
  IN p_appointment_id INT,
  IN p_add_on_id INT
)
BEGIN
  DELETE FROM appointment_add_ons
  WHERE appointment_id = p_appointment_id AND add_on_id = p_add_on_id;

  SELECT ROW_COUNT() AS rows_affected;
END$$

-- ---------------------------------------------------------
-- REPORTING / AUDIT
-- ---------------------------------------------------------

DROP PROCEDURE IF EXISTS sp_reports_daily_summary$$
CREATE PROCEDURE sp_reports_daily_summary(IN p_date DATE)
BEGIN
  SELECT
    p_date AS report_date,
    fn_daily_booking_count(p_date) AS total_bookings,
    fn_daily_revenue(p_date) AS total_revenue;
END$$

DROP PROCEDURE IF EXISTS sp_audit_appointment_log$$
CREATE PROCEDURE sp_audit_appointment_log(IN p_appointment_id INT)
BEGIN
  SELECT *
  FROM appointment_audit_logs
  WHERE appointment_id = p_appointment_id
  ORDER BY changed_at DESC;
END$$

DROP PROCEDURE IF EXISTS sp_audit_general_log$$
CREATE PROCEDURE sp_audit_general_log(
  IN p_table_name VARCHAR(64),
  IN p_record_id INT
)
BEGIN
  SELECT *
  FROM general_audit_logs
  WHERE table_name = p_table_name
    AND record_id = p_record_id
  ORDER BY changed_at DESC;
END$$

-- ---------------------------------------------------------
-- STATUS SEED / LIST
-- ---------------------------------------------------------

DROP PROCEDURE IF EXISTS sp_status_list$$
CREATE PROCEDURE sp_status_list()
BEGIN
  SELECT status_id, status_name
  FROM appointment_status
  ORDER BY status_id;
END$$

DROP PROCEDURE IF EXISTS sp_seed_default_statuses$$
CREATE PROCEDURE sp_seed_default_statuses()
BEGIN
  INSERT IGNORE INTO appointment_status (status_name)
  VALUES ('pending'), ('confirmed'), ('completed'), ('cancelled'), ('no_show');
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
