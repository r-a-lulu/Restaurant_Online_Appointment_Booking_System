-- =========================================================
-- Archived / Optional Functions
-- Restaurant Online Appointment Booking System
-- =========================================================

USE restaurant_booking_v1;

DELIMITER $$

DROP FUNCTION IF EXISTS fn_user_full_name$$
CREATE FUNCTION fn_user_full_name(p_user_id INT)
RETURNS VARCHAR(201)
READS SQL DATA
BEGIN
  DECLARE v_full VARCHAR(201);
  SELECT CONCAT(first_name, ' ', last_name) INTO v_full
  FROM users WHERE user_id = p_user_id;
  RETURN v_full;
END$$

DROP FUNCTION IF EXISTS fn_service_price$$
CREATE FUNCTION fn_service_price(p_service_id INT)
RETURNS DECIMAL(10,2)
READS SQL DATA
BEGIN
  DECLARE v_price DECIMAL(10,2);
  SELECT price INTO v_price FROM services WHERE service_id = p_service_id;
  RETURN IFNULL(v_price, 0);
END$$

DROP FUNCTION IF EXISTS fn_package_price$$
CREATE FUNCTION fn_package_price(p_package_id INT)
RETURNS DECIMAL(10,2)
READS SQL DATA
BEGIN
  DECLARE v_price DECIMAL(10,2);
  SELECT base_price INTO v_price FROM event_packages WHERE package_id = p_package_id;
  RETURN IFNULL(v_price, 0);
END$$

DROP FUNCTION IF EXISTS fn_add_on_price$$
CREATE FUNCTION fn_add_on_price(p_add_on_id INT)
RETURNS DECIMAL(10,2)
READS SQL DATA
BEGIN
  DECLARE v_price DECIMAL(10,2);
  SELECT price INTO v_price FROM add_ons WHERE add_on_id = p_add_on_id;
  RETURN IFNULL(v_price, 0);
END$$

DROP FUNCTION IF EXISTS fn_daily_booking_count$$
CREATE FUNCTION fn_daily_booking_count(p_date DATE)
RETURNS INT
READS SQL DATA
BEGIN
  DECLARE v_count INT DEFAULT 0;
  SELECT COUNT(*) INTO v_count
  FROM appointments
  WHERE appointment_date = p_date;
  RETURN v_count;
END$$

DROP FUNCTION IF EXISTS fn_daily_revenue$$
CREATE FUNCTION fn_daily_revenue(p_date DATE)
RETURNS DECIMAL(12,2)
READS SQL DATA
BEGIN
  DECLARE v_total DECIMAL(12,2) DEFAULT 0;
  SELECT COALESCE(SUM(fn_appointment_total(a.appointment_id)), 0)
  INTO v_total
  FROM appointments a
  WHERE a.appointment_date = p_date
    AND fn_status_name(a.status_id) IN ('confirmed','completed');
  RETURN v_total;
END$$

DROP FUNCTION IF EXISTS fn_zone_booking_count$$
CREATE FUNCTION fn_zone_booking_count(p_zone_id INT, p_date_from DATE, p_date_to DATE)
RETURNS INT
READS SQL DATA
BEGIN
  DECLARE v_count INT DEFAULT 0;
  SELECT COUNT(*) INTO v_count
  FROM appointments
  WHERE zone_id = p_zone_id
    AND appointment_date BETWEEN p_date_from AND p_date_to;
  RETURN v_count;
END$$

DROP FUNCTION IF EXISTS fn_service_booking_count$$
CREATE FUNCTION fn_service_booking_count(p_service_id INT, p_date_from DATE, p_date_to DATE)
RETURNS INT
READS SQL DATA
BEGIN
  DECLARE v_count INT DEFAULT 0;
  SELECT COUNT(*) INTO v_count
  FROM appointments
  WHERE service_id = p_service_id
    AND appointment_date BETWEEN p_date_from AND p_date_to;
  RETURN v_count;
END$$

DROP FUNCTION IF EXISTS fn_table_current_status$$
CREATE FUNCTION fn_table_current_status(p_table_id INT)
RETURNS VARCHAR(20)
READS SQL DATA
BEGIN
  DECLARE v_count INT DEFAULT 0;

  IF p_table_id IS NULL OR p_table_id <= 0 THEN
    RETURN 'available';
  END IF;

  SELECT COUNT(*) INTO v_count
  FROM appointments a
  WHERE a.table_id = p_table_id
    AND a.appointment_date = CURDATE()
    AND fn_status_name(a.status_id) = 'confirmed'
    AND a.start_time <= CURTIME()
    AND a.end_time > CURTIME();

  IF v_count > 0 THEN
    RETURN 'occupied';
  END IF;

  RETURN 'available';
END$$

DELIMITER ;
