-- =========================================================
-- Restaurant Online Appointment Booking System
-- Triggers Script (MariaDB/MySQL compatible)
-- Database: restaurant_booking_v1
-- Total triggers: 16
-- =========================================================

USE restaurant_booking_v1;

-- NOTE:
-- Run database/functions.sql before this script so helper
-- functions (fn_*) are available to triggers.

DELIMITER $$

-- ---------------------------------------------------------
-- CATEGORY 1: AUTO-TIMESTAMP TRIGGERS (2 triggers)
-- ---------------------------------------------------------

DROP TRIGGER IF EXISTS trg_appointments_before_update_timestamp$$
CREATE TRIGGER trg_appointments_before_update_timestamp
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  SET NEW.updated_at = NOW();
END$$

DROP TRIGGER IF EXISTS trg_users_before_update_timestamp$$
CREATE TRIGGER trg_users_before_update_timestamp
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
  SET NEW.updated_at = NOW();
END$$

-- ---------------------------------------------------------
-- CATEGORY 2: BOOKING VALIDATION TRIGGERS (8 triggers)
-- ---------------------------------------------------------

DROP TRIGGER IF EXISTS trg_appointments_before_insert_capacity$$
CREATE TRIGGER trg_appointments_before_insert_capacity
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  IF NEW.table_id IS NOT NULL THEN
    IF fn_party_fits_table(NEW.table_id, NEW.party_size) = 0 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'The selected table cannot fit this party size. Please choose another table or reduce the number of guests.';
    END IF;
  END IF;
END$$

DROP TRIGGER IF EXISTS trg_appointments_before_update_capacity$$
CREATE TRIGGER trg_appointments_before_update_capacity
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  IF NEW.table_id IS NOT NULL
     AND (NEW.party_size <> OLD.party_size OR NOT (NEW.table_id <=> OLD.table_id))
  THEN
    IF fn_party_fits_table(NEW.table_id, NEW.party_size) = 0 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'The selected table cannot fit this party size. Please choose another table or reduce the number of guests.';
    END IF;
  END IF;
END$$

DROP TRIGGER IF EXISTS trg_appointments_before_insert_overlap$$
CREATE TRIGGER trg_appointments_before_insert_overlap
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  IF NEW.table_id IS NOT NULL THEN
    IF fn_table_has_conflict(NEW.table_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NULL) = 1 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'That time is already booked for the selected table. Please choose another time.';
    END IF;
  END IF;
END$$

DROP TRIGGER IF EXISTS trg_appointments_before_update_overlap$$
CREATE TRIGGER trg_appointments_before_update_overlap
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  IF NOT (NEW.table_id <=> OLD.table_id)
     OR NOT (NEW.zone_id <=> OLD.zone_id)
     OR NEW.appointment_date <> OLD.appointment_date
     OR NEW.start_time <> OLD.start_time
     OR NEW.end_time <> OLD.end_time
  THEN
    IF NEW.table_id IS NOT NULL THEN
      IF fn_table_has_conflict(NEW.table_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NEW.appointment_id) = 1 THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'That time is already booked for the selected table. Please choose another time.';
      END IF;
    END IF;
  END IF;
END$$

DROP TRIGGER IF EXISTS trg_appointments_before_insert_past_date$$
CREATE TRIGGER trg_appointments_before_insert_past_date
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  IF fn_is_past_datetime(NEW.appointment_date, NEW.start_time) = 1 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Reservations cannot be made in the past. Please choose a future date and time.';
  END IF;
END$$

DROP TRIGGER IF EXISTS trg_appointments_before_update_past_date$$
CREATE TRIGGER trg_appointments_before_update_past_date
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  IF NEW.appointment_date <> OLD.appointment_date
     OR NEW.start_time <> OLD.start_time
  THEN
    IF fn_is_past_datetime(NEW.appointment_date, NEW.start_time) = 1 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Reservations cannot be moved to a past date. Please choose a future date and time.';
    END IF;
  END IF;
END$$

DROP TRIGGER IF EXISTS trg_appointments_before_insert_service_package$$
CREATE TRIGGER trg_appointments_before_insert_service_package
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  IF (NEW.service_id IS NULL AND NEW.event_package_id IS NULL)
     OR (NEW.service_id IS NOT NULL AND NEW.event_package_id IS NOT NULL)
  THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Please choose either a service or an event package, not both.';
  END IF;
END$$

DROP TRIGGER IF EXISTS trg_appointments_before_update_service_package$$
CREATE TRIGGER trg_appointments_before_update_service_package
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  IF (NEW.service_id IS NULL AND NEW.event_package_id IS NULL)
     OR (NEW.service_id IS NOT NULL AND NEW.event_package_id IS NOT NULL)
  THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Please choose either a service or an event package, not both.';
  END IF;
END$$

-- ---------------------------------------------------------
-- CATEGORY 3: USER LIFECYCLE TRIGGERS (1 trigger)
-- ---------------------------------------------------------

DROP TRIGGER IF EXISTS trg_users_before_update_login$$
CREATE TRIGGER trg_users_before_update_login
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
  IF NOT (NEW.last_login <=> OLD.last_login) THEN
    SET NEW.updated_at = NOW();
  END IF;
END$$

-- ---------------------------------------------------------
-- CATEGORY 4: APPOINTMENT STATE & ADD-ON GUARDS (3 triggers)
-- ---------------------------------------------------------

DROP TRIGGER IF EXISTS trg_appointments_before_update_status_flow$$
CREATE TRIGGER trg_appointments_before_update_status_flow
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  IF OLD.status_id <> NEW.status_id THEN
    IF fn_is_valid_status_transition(OLD.status_id, NEW.status_id) = 0 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid status transition.';
    END IF;
  END IF;
END$$

DROP TRIGGER IF EXISTS trg_appt_add_ons_before_insert_status_check$$
CREATE TRIGGER trg_appt_add_ons_before_insert_status_check
BEFORE INSERT ON appointment_add_ons
FOR EACH ROW
BEGIN
  DECLARE v_status_id INT;

  SELECT status_id INTO v_status_id
  FROM appointments
  WHERE appointment_id = NEW.appointment_id;

  IF fn_is_terminal_status(v_status_id) = 1 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Cannot add add-ons to a cancelled, completed, or no-show appointment.';
  END IF;
END$$

DROP TRIGGER IF EXISTS trg_appointments_before_delete_guard$$
CREATE TRIGGER trg_appointments_before_delete_guard
BEFORE DELETE ON appointments
FOR EACH ROW
BEGIN
  IF fn_status_name(OLD.status_id) = 'confirmed' THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Cannot delete a confirmed appointment. Cancel it first.';
  END IF;
END$$

-- ---------------------------------------------------------
-- CATEGORY 5: MAX ACTIVE BOOKINGS PER USER (2 triggers)
-- ---------------------------------------------------------

DROP TRIGGER IF EXISTS trg_appointments_before_insert_max_active$$
CREATE TRIGGER trg_appointments_before_insert_max_active
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  DECLARE v_max_active INT DEFAULT 12;
  DECLARE v_message VARCHAR(255);
  SET v_message = CONCAT('You already have ', v_max_active, ' active reservations. Please complete or cancel one before creating a new booking.');

  IF fn_can_book_more(NEW.user_id, v_max_active) = 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = v_message;
  END IF;
END$$

DROP TRIGGER IF EXISTS trg_appointments_before_update_max_active$$
CREATE TRIGGER trg_appointments_before_update_max_active
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  DECLARE v_max_active INT DEFAULT 12;
  DECLARE v_message VARCHAR(255);
  SET v_message = CONCAT('You already have ', v_max_active, ' active reservations. Please complete or cancel one before creating a new booking.');

  IF NEW.user_id <> OLD.user_id OR OLD.status_id <> NEW.status_id THEN
    IF fn_is_active_status(NEW.status_id) = 1 THEN
      IF fn_user_active_booking_count(NEW.user_id) >= v_max_active THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = v_message;
      END IF;
    END IF;
  END IF;
END$$

DELIMITER ;

SELECT
  TRIGGER_NAME,
  EVENT_MANIPULATION AS event,
  EVENT_OBJECT_TABLE AS `table`,
  ACTION_TIMING AS timing
FROM information_schema.triggers
WHERE TRIGGER_SCHEMA = 'restaurant_booking_v1'
ORDER BY EVENT_OBJECT_TABLE, ACTION_TIMING, EVENT_MANIPULATION;

SELECT COUNT(*) AS total_triggers
FROM information_schema.triggers
WHERE TRIGGER_SCHEMA = 'restaurant_booking_v1';
