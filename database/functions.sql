-- =========================================================
-- Restaurant Online Appointment Booking System
-- Database Functions (MariaDB/MySQL compatible)
-- Database: restaurant_booking_v1
-- =========================================================
--
-- Function Index
-- Function #1:  fn_status_name(status_id)
-- Function #2:  fn_status_id_by_name(status_name)
-- Function #3:  fn_is_terminal_status(status_id)
-- Function #4:  fn_is_active_status(status_id)
-- Function #5:  fn_user_full_name(user_id)
-- Function #6:  fn_user_active_booking_count(user_id)
-- Function #7:  fn_can_book_more(user_id, max_active)
-- Function #8:  fn_is_past_datetime(date, time)
-- Function #9:  fn_overlaps(start1, end1, start2, end2)
-- Function #10: fn_table_capacity(table_id)
-- Function #11: fn_party_fits_table(table_id, party_size)
-- Function #12: fn_table_has_conflict(table_id, date, start, end, exclude_appt_id)
-- Function #13: fn_zone_has_conflict(zone_id, date, start, end, exclude_appt_id)
-- Function #14: fn_is_slot_available(date, start, end, table_id, zone_id, exclude_appt_id)
-- Function #15: fn_service_price(service_id)
-- Function #16: fn_package_price(package_id)
-- Function #17: fn_add_on_price(add_on_id)
-- Function #18: fn_appointment_subtotal(appointment_id)
-- Function #19: fn_appointment_total(appointment_id)
-- Function #20: fn_daily_booking_count(date)
-- Function #21: fn_daily_revenue(date)
-- Function #22: fn_zone_booking_count(zone_id, date_from, date_to)
-- Function #23: fn_service_booking_count(service_id, date_from, date_to)
-- Function #24: fn_is_valid_status_transition(old_status_id, new_status_id)

USE restaurant_booking_v1;

DELIMITER $$

-- ---------------------------------------------------------
-- STATUS / USER HELPERS
-- ---------------------------------------------------------

DROP FUNCTION IF EXISTS fn_status_name$$
CREATE FUNCTION fn_status_name(p_status_id INT)
RETURNS VARCHAR(30)
READS SQL DATA
BEGIN
  DECLARE v_name VARCHAR(30);
  SELECT status_name INTO v_name
  FROM appointment_status
  WHERE status_id = p_status_id;
  RETURN v_name;
END$$

DROP FUNCTION IF EXISTS fn_status_id_by_name$$
CREATE FUNCTION fn_status_id_by_name(p_status_name VARCHAR(30))
RETURNS INT
READS SQL DATA
BEGIN
  DECLARE v_status_id INT DEFAULT NULL;
  SELECT status_id INTO v_status_id
  FROM appointment_status
  WHERE status_name = p_status_name
  LIMIT 1;
  RETURN v_status_id;
END$$

DROP FUNCTION IF EXISTS fn_is_terminal_status$$
CREATE FUNCTION fn_is_terminal_status(p_status_id INT)
RETURNS TINYINT
READS SQL DATA
BEGIN
  DECLARE v_name VARCHAR(30);
  SET v_name = fn_status_name(p_status_id);
  RETURN IF(v_name IN ('completed','cancelled','no_show'), 1, 0);
END$$

DROP FUNCTION IF EXISTS fn_is_active_status$$
CREATE FUNCTION fn_is_active_status(p_status_id INT)
RETURNS TINYINT
READS SQL DATA
BEGIN
  DECLARE v_name VARCHAR(30);
  SET v_name = fn_status_name(p_status_id);
  RETURN IF(v_name IN ('pending','confirmed'), 1, 0);
END$$

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

DROP FUNCTION IF EXISTS fn_user_active_booking_count$$
CREATE FUNCTION fn_user_active_booking_count(p_user_id INT)
RETURNS INT
READS SQL DATA
BEGIN
  DECLARE v_count INT DEFAULT 0;
  SELECT COUNT(*) INTO v_count
  FROM appointments a
  WHERE a.user_id = p_user_id
    AND fn_is_active_status(a.status_id) = 1;
  RETURN v_count;
END$$

DROP FUNCTION IF EXISTS fn_can_book_more$$
CREATE FUNCTION fn_can_book_more(p_user_id INT, p_max_active INT)
RETURNS TINYINT
READS SQL DATA
BEGIN
  RETURN IF(fn_user_active_booking_count(p_user_id) < p_max_active, 1, 0);
END$$

-- ---------------------------------------------------------
-- TIME / OVERLAP HELPERS
-- ---------------------------------------------------------

DROP FUNCTION IF EXISTS fn_is_past_datetime$$
CREATE FUNCTION fn_is_past_datetime(p_date DATE, p_time TIME)
RETURNS TINYINT
NOT DETERMINISTIC
BEGIN
  RETURN IF(TIMESTAMP(p_date, p_time) < NOW(), 1, 0);
END$$

DROP FUNCTION IF EXISTS fn_overlaps$$
CREATE FUNCTION fn_overlaps(p_start1 TIME, p_end1 TIME, p_start2 TIME, p_end2 TIME)
RETURNS TINYINT
DETERMINISTIC
BEGIN
  RETURN IF(p_start1 < p_end2 AND p_end1 > p_start2, 1, 0);
END$$

-- ---------------------------------------------------------
-- AVAILABILITY / CAPACITY HELPERS
-- ---------------------------------------------------------

DROP FUNCTION IF EXISTS fn_table_capacity$$
CREATE FUNCTION fn_table_capacity(p_table_id INT)
RETURNS INT
READS SQL DATA
BEGIN
  DECLARE v_capacity INT;
  SELECT capacity INTO v_capacity FROM `tables` WHERE table_id = p_table_id;
  RETURN v_capacity;
END$$

DROP FUNCTION IF EXISTS fn_party_fits_table$$
CREATE FUNCTION fn_party_fits_table(p_table_id INT, p_party_size INT)
RETURNS TINYINT
READS SQL DATA
BEGIN
  DECLARE v_capacity INT;
  SET v_capacity = fn_table_capacity(p_table_id);
  IF v_capacity IS NULL THEN
    RETURN 0;
  END IF;
  RETURN IF(p_party_size <= v_capacity, 1, 0);
END$$

DROP FUNCTION IF EXISTS fn_table_has_conflict$$
CREATE FUNCTION fn_table_has_conflict(
  p_table_id INT,
  p_date DATE,
  p_start TIME,
  p_end TIME,
  p_exclude_appt_id INT
)
RETURNS TINYINT
READS SQL DATA
BEGIN
  DECLARE v_count INT DEFAULT 0;
  SELECT COUNT(*) INTO v_count
  FROM appointments a
  WHERE a.table_id = p_table_id
    AND a.appointment_date = p_date
    AND fn_overlaps(a.start_time, a.end_time, p_start, p_end) = 1
    AND fn_is_active_status(a.status_id) = 1
    AND (p_exclude_appt_id IS NULL OR a.appointment_id <> p_exclude_appt_id);
  RETURN IF(v_count > 0, 1, 0);
END$$

DROP FUNCTION IF EXISTS fn_zone_has_conflict$$
CREATE FUNCTION fn_zone_has_conflict(
  p_zone_id INT,
  p_date DATE,
  p_start TIME,
  p_end TIME,
  p_exclude_appt_id INT
)
RETURNS TINYINT
READS SQL DATA
BEGIN
  DECLARE v_count INT DEFAULT 0;
  SELECT COUNT(*) INTO v_count
  FROM appointments a
  WHERE a.zone_id = p_zone_id
    AND a.appointment_date = p_date
    AND fn_overlaps(a.start_time, a.end_time, p_start, p_end) = 1
    AND fn_is_active_status(a.status_id) = 1
    AND (p_exclude_appt_id IS NULL OR a.appointment_id <> p_exclude_appt_id);
  RETURN IF(v_count > 0, 1, 0);
END$$

DROP FUNCTION IF EXISTS fn_is_slot_available$$
CREATE FUNCTION fn_is_slot_available(
  p_date DATE,
  p_start TIME,
  p_end TIME,
  p_table_id INT,
  p_zone_id INT,
  p_exclude_appt_id INT
)
RETURNS TINYINT
READS SQL DATA
BEGIN
  IF p_table_id IS NOT NULL THEN
    RETURN IF(fn_table_has_conflict(p_table_id, p_date, p_start, p_end, p_exclude_appt_id) = 1, 0, 1);
  END IF;

  IF p_zone_id IS NOT NULL THEN
    RETURN IF(fn_zone_has_conflict(p_zone_id, p_date, p_start, p_end, p_exclude_appt_id) = 1, 0, 1);
  END IF;

  RETURN 0;
END$$

-- ---------------------------------------------------------
-- PRICING HELPERS
-- ---------------------------------------------------------

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

DROP FUNCTION IF EXISTS fn_appointment_subtotal$$
CREATE FUNCTION fn_appointment_subtotal(p_appointment_id INT)
RETURNS DECIMAL(10,2)
READS SQL DATA
BEGIN
  DECLARE v_base DECIMAL(10,2);
  DECLARE v_add_ons DECIMAL(10,2);

  SELECT COALESCE(s.price, ep.base_price, 0)
  INTO v_base
  FROM appointments a
  LEFT JOIN services s ON s.service_id = a.service_id
  LEFT JOIN event_packages ep ON ep.package_id = a.event_package_id
  WHERE a.appointment_id = p_appointment_id;

  SELECT COALESCE(SUM(ao.price * aa.quantity), 0)
  INTO v_add_ons
  FROM appointment_add_ons aa
  JOIN add_ons ao ON ao.add_on_id = aa.add_on_id
  WHERE aa.appointment_id = p_appointment_id;

  RETURN COALESCE(v_base, 0) + COALESCE(v_add_ons, 0);
END$$

DROP FUNCTION IF EXISTS fn_appointment_total$$
CREATE FUNCTION fn_appointment_total(p_appointment_id INT)
RETURNS DECIMAL(10,2)
READS SQL DATA
BEGIN
  RETURN fn_appointment_subtotal(p_appointment_id);
END$$

-- ---------------------------------------------------------
-- REPORTING HELPERS
-- ---------------------------------------------------------

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

-- ---------------------------------------------------------
-- STATUS FLOW
-- ---------------------------------------------------------

DROP FUNCTION IF EXISTS fn_is_valid_status_transition$$
CREATE FUNCTION fn_is_valid_status_transition(p_old_status_id INT, p_new_status_id INT)
RETURNS TINYINT
READS SQL DATA
BEGIN
  DECLARE v_old VARCHAR(30);
  DECLARE v_new VARCHAR(30);

  SET v_old = fn_status_name(p_old_status_id);
  SET v_new = fn_status_name(p_new_status_id);

  IF v_old = 'pending' AND v_new IN ('confirmed','cancelled') THEN
    RETURN 1;
  ELSEIF v_old = 'confirmed' AND v_new IN ('completed','cancelled','no_show') THEN
    RETURN 1;
  ELSEIF v_old IN ('completed','cancelled','no_show') THEN
    RETURN 0;
  ELSEIF v_old = v_new THEN
    RETURN 1;
  END IF;

  RETURN 0;
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
  JOIN appointment_status s ON s.status_id = a.status_id
  WHERE a.table_id = p_table_id
    AND s.status_name IN ('pending', 'confirmed')
    AND a.appointment_date = CURDATE()
    AND a.start_time <= CURTIME()
    AND a.end_time > CURTIME();

  IF v_count > 0 THEN
    RETURN 'occupied';
  END IF;

  RETURN 'available';
END$$

DELIMITER ;
