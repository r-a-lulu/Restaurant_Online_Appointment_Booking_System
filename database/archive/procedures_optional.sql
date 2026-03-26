-- =========================================================
-- Archived / Optional Stored Procedures
-- Restaurant Online Appointment Booking System
-- =========================================================

USE restaurant_booking_v1;

DELIMITER $$

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

DROP PROCEDURE IF EXISTS sp_roles_list$$
CREATE PROCEDURE sp_roles_list()
BEGIN
  SELECT role_id, role_name, permissions_description
  FROM roles
  ORDER BY role_name;
END$$

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

DELIMITER ;
