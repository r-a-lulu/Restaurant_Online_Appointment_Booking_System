-- =========================================================
-- Restaurant Online Appointment Booking System
-- Triggers Script (MariaDB/MySQL compatible — XAMPP + SQLyog)
-- Database: restaurant_booking_v1
-- Total triggers: 43
-- =========================================================

USE restaurant_booking_v1;

-- NOTE:
-- Run database/functions.sql before this script so helper
-- functions (fn_*) are available to triggers.

-- =========================================================
-- SECTION B: TRIGGER DEFINITIONS
-- =========================================================

DELIMITER $$

-- ---------------------------------------------------------
-- CATEGORY 1: APPOINTMENT AUDIT TRIGGERS (3 triggers)
-- Logs every INSERT, UPDATE, and DELETE on appointments
-- into appointment_audit_logs
-- ---------------------------------------------------------

-- Trigger #1: Log new appointment creation
DROP TRIGGER IF EXISTS trg_appointments_after_insert$$
CREATE TRIGGER trg_appointments_after_insert
AFTER INSERT ON appointments
FOR EACH ROW
BEGIN
  INSERT INTO appointment_audit_logs (appointment_id, action_type, old_values, new_values)
  VALUES (
    NEW.appointment_id,
    'INSERT',
    NULL,
    JSON_OBJECT(
      'user_id',          NEW.user_id,
      'service_id',       NEW.service_id,
      'table_id',         NEW.table_id,
      'zone_id',          NEW.zone_id,
      'event_package_id', NEW.event_package_id,
      'appointment_date', NEW.appointment_date,
      'start_time',       NEW.start_time,
      'end_time',         NEW.end_time,
      'party_size',       NEW.party_size,
      'status_id',        NEW.status_id
    )
  );
END$$

-- Trigger #2: Log appointment updates (including status changes)
DROP TRIGGER IF EXISTS trg_appointments_after_update$$
CREATE TRIGGER trg_appointments_after_update
AFTER UPDATE ON appointments
FOR EACH ROW
BEGIN
  DECLARE v_action VARCHAR(30);

  -- Distinguish status-only changes from general updates
  IF OLD.status_id <> NEW.status_id
     AND OLD.user_id = NEW.user_id
     AND OLD.service_id <=> NEW.service_id
     AND OLD.table_id <=> NEW.table_id
     AND OLD.zone_id <=> NEW.zone_id
     AND OLD.event_package_id <=> NEW.event_package_id
     AND OLD.appointment_date = NEW.appointment_date
     AND OLD.start_time = NEW.start_time
     AND OLD.end_time = NEW.end_time
     AND OLD.party_size = NEW.party_size
  THEN
    SET v_action = 'STATUS_CHANGE';
  ELSE
    SET v_action = 'UPDATE';
  END IF;

  INSERT INTO appointment_audit_logs (appointment_id, action_type, old_values, new_values)
  VALUES (
    NEW.appointment_id,
    v_action,
    JSON_OBJECT(
      'user_id',          OLD.user_id,
      'service_id',       OLD.service_id,
      'table_id',         OLD.table_id,
      'zone_id',          OLD.zone_id,
      'event_package_id', OLD.event_package_id,
      'appointment_date', OLD.appointment_date,
      'start_time',       OLD.start_time,
      'end_time',         OLD.end_time,
      'party_size',       OLD.party_size,
      'status_id',        OLD.status_id
    ),
    JSON_OBJECT(
      'user_id',          NEW.user_id,
      'service_id',       NEW.service_id,
      'table_id',         NEW.table_id,
      'zone_id',          NEW.zone_id,
      'event_package_id', NEW.event_package_id,
      'appointment_date', NEW.appointment_date,
      'start_time',       NEW.start_time,
      'end_time',         NEW.end_time,
      'party_size',       NEW.party_size,
      'status_id',        NEW.status_id
    )
  );
END$$

-- Trigger #3: Log appointment deletion
DROP TRIGGER IF EXISTS trg_appointments_after_delete$$
CREATE TRIGGER trg_appointments_after_delete
AFTER DELETE ON appointments
FOR EACH ROW
BEGIN
  INSERT INTO appointment_audit_logs (appointment_id, action_type, old_values, new_values)
  VALUES (
    OLD.appointment_id,
    'DELETE',
    JSON_OBJECT(
      'user_id',          OLD.user_id,
      'service_id',       OLD.service_id,
      'table_id',         OLD.table_id,
      'zone_id',          OLD.zone_id,
      'event_package_id', OLD.event_package_id,
      'appointment_date', OLD.appointment_date,
      'start_time',       OLD.start_time,
      'end_time',         OLD.end_time,
      'party_size',       OLD.party_size,
      'status_id',        OLD.status_id
    ),
    NULL
  );
END$$


-- ---------------------------------------------------------
-- CATEGORY 2: MASTER-DATA & LOOKUP AUDIT TRIGGERS (21 triggers)
-- Logs INSERT/UPDATE/DELETE on all master and lookup tables
-- into general_audit_logs
-- ---------------------------------------------------------

-- == services (Triggers #4, #5, #6) ==

DROP TRIGGER IF EXISTS trg_services_after_insert$$
CREATE TRIGGER trg_services_after_insert
AFTER INSERT ON services
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('services', NEW.service_id, 'INSERT', NULL,
    JSON_OBJECT('service_name', NEW.service_name, 'price', NEW.price));
END$$

DROP TRIGGER IF EXISTS trg_services_after_update$$
CREATE TRIGGER trg_services_after_update
AFTER UPDATE ON services
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('services', NEW.service_id, 'UPDATE',
    JSON_OBJECT('service_name', OLD.service_name, 'price', OLD.price),
    JSON_OBJECT('service_name', NEW.service_name, 'price', NEW.price));
END$$

DROP TRIGGER IF EXISTS trg_services_after_delete$$
CREATE TRIGGER trg_services_after_delete
AFTER DELETE ON services
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('services', OLD.service_id, 'DELETE',
    JSON_OBJECT('service_name', OLD.service_name, 'price', OLD.price), NULL);
END$$

-- == event_packages (Triggers #7, #8, #9) ==

DROP TRIGGER IF EXISTS trg_event_packages_after_insert$$
CREATE TRIGGER trg_event_packages_after_insert
AFTER INSERT ON event_packages
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('event_packages', NEW.package_id, 'INSERT', NULL,
    JSON_OBJECT('package_name', NEW.package_name, 'description', NEW.description, 'base_price', NEW.base_price));
END$$

DROP TRIGGER IF EXISTS trg_event_packages_after_update$$
CREATE TRIGGER trg_event_packages_after_update
AFTER UPDATE ON event_packages
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('event_packages', NEW.package_id, 'UPDATE',
    JSON_OBJECT('package_name', OLD.package_name, 'description', OLD.description, 'base_price', OLD.base_price),
    JSON_OBJECT('package_name', NEW.package_name, 'description', NEW.description, 'base_price', NEW.base_price));
END$$

DROP TRIGGER IF EXISTS trg_event_packages_after_delete$$
CREATE TRIGGER trg_event_packages_after_delete
AFTER DELETE ON event_packages
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('event_packages', OLD.package_id, 'DELETE',
    JSON_OBJECT('package_name', OLD.package_name, 'description', OLD.description, 'base_price', OLD.base_price), NULL);
END$$

-- == add_ons (Triggers #10, #11, #12) ==

DROP TRIGGER IF EXISTS trg_add_ons_after_insert$$
CREATE TRIGGER trg_add_ons_after_insert
AFTER INSERT ON add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('add_ons', NEW.add_on_id, 'INSERT', NULL,
    JSON_OBJECT('category', NEW.category, 'name', NEW.name, 'description', NEW.description, 'price', NEW.price));
END$$

DROP TRIGGER IF EXISTS trg_add_ons_after_update$$
CREATE TRIGGER trg_add_ons_after_update
AFTER UPDATE ON add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('add_ons', NEW.add_on_id, 'UPDATE',
    JSON_OBJECT('category', OLD.category, 'name', OLD.name, 'description', OLD.description, 'price', OLD.price),
    JSON_OBJECT('category', NEW.category, 'name', NEW.name, 'description', NEW.description, 'price', NEW.price));
END$$

DROP TRIGGER IF EXISTS trg_add_ons_after_delete$$
CREATE TRIGGER trg_add_ons_after_delete
AFTER DELETE ON add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('add_ons', OLD.add_on_id, 'DELETE',
    JSON_OBJECT('category', OLD.category, 'name', OLD.name, 'description', OLD.description, 'price', OLD.price), NULL);
END$$

-- == tables (Triggers #13, #14, #15) ==

DROP TRIGGER IF EXISTS trg_tables_after_insert$$
CREATE TRIGGER trg_tables_after_insert
AFTER INSERT ON `tables`
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('tables', NEW.table_id, 'INSERT', NULL,
    JSON_OBJECT('zone_id', NEW.zone_id, 'table_label', NEW.seating_preference, 'capacity', NEW.capacity));
END$$

DROP TRIGGER IF EXISTS trg_tables_after_update$$
CREATE TRIGGER trg_tables_after_update
AFTER UPDATE ON `tables`
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('tables', NEW.table_id, 'UPDATE',
    JSON_OBJECT('zone_id', OLD.zone_id, 'table_label', OLD.seating_preference, 'capacity', OLD.capacity),
    JSON_OBJECT('zone_id', NEW.zone_id, 'table_label', NEW.seating_preference, 'capacity', NEW.capacity));
END$$

DROP TRIGGER IF EXISTS trg_tables_after_delete$$
CREATE TRIGGER trg_tables_after_delete
AFTER DELETE ON `tables`
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('tables', OLD.table_id, 'DELETE',
    JSON_OBJECT('zone_id', OLD.zone_id, 'table_label', OLD.seating_preference, 'capacity', OLD.capacity), NULL);
END$$

-- == dining_zones (Triggers #16, #17, #18) ==

DROP TRIGGER IF EXISTS trg_dining_zones_after_insert$$
CREATE TRIGGER trg_dining_zones_after_insert
AFTER INSERT ON dining_zones
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('dining_zones', NEW.zone_id, 'INSERT', NULL,
    JSON_OBJECT('zone_name', NEW.zone_name));
END$$

DROP TRIGGER IF EXISTS trg_dining_zones_after_update$$
CREATE TRIGGER trg_dining_zones_after_update
AFTER UPDATE ON dining_zones
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('dining_zones', NEW.zone_id, 'UPDATE',
    JSON_OBJECT('zone_name', OLD.zone_name),
    JSON_OBJECT('zone_name', NEW.zone_name));
END$$

DROP TRIGGER IF EXISTS trg_dining_zones_after_delete$$
CREATE TRIGGER trg_dining_zones_after_delete
AFTER DELETE ON dining_zones
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('dining_zones', OLD.zone_id, 'DELETE',
    JSON_OBJECT('zone_name', OLD.zone_name), NULL);
END$$

-- == roles (Triggers #19, #20, #21) ==

DROP TRIGGER IF EXISTS trg_roles_after_insert$$
CREATE TRIGGER trg_roles_after_insert
AFTER INSERT ON roles
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('roles', NEW.role_id, 'INSERT', NULL,
    JSON_OBJECT('role_name', NEW.role_name, 'permissions_description', NEW.permissions_description));
END$$

DROP TRIGGER IF EXISTS trg_roles_after_update$$
CREATE TRIGGER trg_roles_after_update
AFTER UPDATE ON roles
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('roles', NEW.role_id, 'UPDATE',
    JSON_OBJECT('role_name', OLD.role_name, 'permissions_description', OLD.permissions_description),
    JSON_OBJECT('role_name', NEW.role_name, 'permissions_description', NEW.permissions_description));
END$$

DROP TRIGGER IF EXISTS trg_roles_after_delete$$
CREATE TRIGGER trg_roles_after_delete
AFTER DELETE ON roles
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('roles', OLD.role_id, 'DELETE',
    JSON_OBJECT('role_name', OLD.role_name, 'permissions_description', OLD.permissions_description), NULL);
END$$

-- == appointment_status (Triggers #22, #23, #24) ==

DROP TRIGGER IF EXISTS trg_appt_status_after_insert$$
CREATE TRIGGER trg_appt_status_after_insert
AFTER INSERT ON appointment_status
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_status', NEW.status_id, 'INSERT', NULL,
    JSON_OBJECT('status_name', NEW.status_name));
END$$

DROP TRIGGER IF EXISTS trg_appt_status_after_update$$
CREATE TRIGGER trg_appt_status_after_update
AFTER UPDATE ON appointment_status
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_status', NEW.status_id, 'UPDATE',
    JSON_OBJECT('status_name', OLD.status_name),
    JSON_OBJECT('status_name', NEW.status_name));
END$$

DROP TRIGGER IF EXISTS trg_appt_status_after_delete$$
CREATE TRIGGER trg_appt_status_after_delete
AFTER DELETE ON appointment_status
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_status', OLD.status_id, 'DELETE',
    JSON_OBJECT('status_name', OLD.status_name), NULL);
END$$


-- ---------------------------------------------------------
-- CATEGORY 3: AUTO-TIMESTAMP TRIGGERS (2 triggers)
-- Auto-set updated_at = NOW() on every UPDATE
-- ---------------------------------------------------------

-- Trigger #25: Auto-timestamp on appointments update
DROP TRIGGER IF EXISTS trg_appointments_before_update_timestamp$$
CREATE TRIGGER trg_appointments_before_update_timestamp
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  SET NEW.updated_at = NOW();
END$$

-- Trigger #26: Auto-timestamp on users update
DROP TRIGGER IF EXISTS trg_users_before_update_timestamp$$
CREATE TRIGGER trg_users_before_update_timestamp
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
  SET NEW.updated_at = NOW();
END$$


-- ---------------------------------------------------------
-- CATEGORY 4: BUSINESS RULE ENFORCEMENT TRIGGERS (8 triggers)
-- Enforce data integrity rules at the database level
-- ---------------------------------------------------------

-- Trigger #27: Validate party_size <= table capacity on INSERT
DROP TRIGGER IF EXISTS trg_appointments_before_insert_capacity$$
CREATE TRIGGER trg_appointments_before_insert_capacity
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  -- Only validate when a specific table is booked (table_id IS NOT NULL)
  IF NEW.table_id IS NOT NULL THEN
    IF fn_party_fits_table(NEW.table_id, NEW.party_size) = 0 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'The selected table cannot fit this party size. Please choose another table or reduce the number of guests.';
    END IF;
  END IF;
END$$

-- Trigger #28: Validate party_size <= table capacity on UPDATE
DROP TRIGGER IF EXISTS trg_appointments_before_update_capacity$$
CREATE TRIGGER trg_appointments_before_update_capacity
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  -- Re-validate only if party_size or table_id changed
  IF NEW.table_id IS NOT NULL
     AND (NEW.party_size <> OLD.party_size OR NOT (NEW.table_id <=> OLD.table_id))
  THEN
    IF fn_party_fits_table(NEW.table_id, NEW.party_size) = 0 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'The selected table cannot fit this party size. Please choose another table or reduce the number of guests.';
    END IF;
  END IF;
END$$

-- Trigger #29: Prevent double-booking (overlapping time slots) on INSERT
DROP TRIGGER IF EXISTS trg_appointments_before_insert_overlap$$
CREATE TRIGGER trg_appointments_before_insert_overlap
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  -- Check table-level overlap
  IF NEW.table_id IS NOT NULL THEN
    IF fn_table_has_conflict(NEW.table_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NULL) = 1 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'That time is already booked for the selected table. Please choose another time.';
    END IF;
  END IF;

  -- Check zone-level overlap
  IF NEW.zone_id IS NOT NULL THEN
    IF fn_zone_has_conflict(NEW.zone_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NULL) = 1 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'That time is already booked in the selected dining zone. Please choose another time or zone.';
    END IF;
  END IF;
END$$

-- Trigger #30: Prevent double-booking on UPDATE
DROP TRIGGER IF EXISTS trg_appointments_before_update_overlap$$
CREATE TRIGGER trg_appointments_before_update_overlap
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  -- Only re-check if scheduling fields or location changed
  IF NOT (NEW.table_id <=> OLD.table_id)
     OR NOT (NEW.zone_id <=> OLD.zone_id)
     OR NEW.appointment_date <> OLD.appointment_date
     OR NEW.start_time <> OLD.start_time
     OR NEW.end_time <> OLD.end_time
  THEN
    -- Check table-level overlap (exclude self)
    IF NEW.table_id IS NOT NULL THEN
      IF fn_table_has_conflict(NEW.table_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NEW.appointment_id) = 1 THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'That time is already booked for the selected table. Please choose another time.';
      END IF;
    END IF;

    -- Check zone-level overlap (exclude self)
    IF NEW.zone_id IS NOT NULL THEN
      IF fn_zone_has_conflict(NEW.zone_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NEW.appointment_id) = 1 THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'That time is already booked in the selected dining zone. Please choose another time or zone.';
      END IF;
    END IF;
  END IF;
END$$

-- Trigger #31: Prevent booking in the past on INSERT
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

-- Trigger #32: Prevent modification to a past date on UPDATE
DROP TRIGGER IF EXISTS trg_appointments_before_update_past_date$$
CREATE TRIGGER trg_appointments_before_update_past_date
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  -- Only enforce if the date/time fields are being changed
  IF NEW.appointment_date <> OLD.appointment_date
     OR NEW.start_time <> OLD.start_time
  THEN
    IF fn_is_past_datetime(NEW.appointment_date, NEW.start_time) = 1 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Reservations cannot be moved to a past date. Please choose a future date and time.';
    END IF;
  END IF;
END$$

-- Trigger #33: Enforce service/package exclusivity on INSERT
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

-- Trigger #34: Enforce service/package exclusivity on UPDATE
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
-- CATEGORY 5: USER LIFECYCLE TRIGGER (1 trigger)
-- ---------------------------------------------------------

-- Trigger #35: Track last_login changes
DROP TRIGGER IF EXISTS trg_users_before_update_login$$
CREATE TRIGGER trg_users_before_update_login
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
  -- Auto-record when last_login transitions from NULL to a value
  -- or when last_login changes (the application sets last_login;
  -- this trigger ensures updated_at reflects that change)
  IF NOT (NEW.last_login <=> OLD.last_login) THEN
    SET NEW.updated_at = NOW();
  END IF;
END$$


-- ---------------------------------------------------------
-- CATEGORY 6: APPOINTMENT ADD-ONS AUDIT TRIGGERS (3 triggers)
-- Logs INSERT/UPDATE/DELETE on appointment_add_ons
-- into general_audit_logs
-- ---------------------------------------------------------

-- Trigger #36: Log add-on attachment to appointment
DROP TRIGGER IF EXISTS trg_appt_add_ons_after_insert$$
CREATE TRIGGER trg_appt_add_ons_after_insert
AFTER INSERT ON appointment_add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_add_ons', NEW.appointment_id, 'INSERT', NULL,
    JSON_OBJECT('appointment_id', NEW.appointment_id, 'add_on_id', NEW.add_on_id, 'quantity', NEW.quantity));
END$$

-- Trigger #37: Log add-on quantity change on appointment
DROP TRIGGER IF EXISTS trg_appt_add_ons_after_update$$
CREATE TRIGGER trg_appt_add_ons_after_update
AFTER UPDATE ON appointment_add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_add_ons', NEW.appointment_id, 'UPDATE',
    JSON_OBJECT('appointment_id', OLD.appointment_id, 'add_on_id', OLD.add_on_id, 'quantity', OLD.quantity),
    JSON_OBJECT('appointment_id', NEW.appointment_id, 'add_on_id', NEW.add_on_id, 'quantity', NEW.quantity));
END$$

-- Trigger #38: Log add-on removal from appointment
DROP TRIGGER IF EXISTS trg_appt_add_ons_after_delete$$
CREATE TRIGGER trg_appt_add_ons_after_delete
AFTER DELETE ON appointment_add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_add_ons', OLD.appointment_id, 'DELETE',
    JSON_OBJECT('appointment_id', OLD.appointment_id, 'add_on_id', OLD.add_on_id, 'quantity', OLD.quantity), NULL);
END$$


-- ---------------------------------------------------------
-- CATEGORY 7: ADDITIONAL BUSINESS RULE TRIGGERS (3 triggers)
-- State machine, add-on guards, and delete protection
-- ---------------------------------------------------------

-- Trigger #39: Enforce valid appointment status transitions
-- Valid flows:
--   pending   -> confirmed | cancelled
--   confirmed -> completed | cancelled | no_show
--   completed -> (terminal, no transitions allowed)
--   cancelled -> (terminal, no transitions allowed)
--   no_show   -> (terminal, no transitions allowed)
DROP TRIGGER IF EXISTS trg_appointments_before_update_status_flow$$
CREATE TRIGGER trg_appointments_before_update_status_flow
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  -- Only check when status_id actually changes
  IF OLD.status_id <> NEW.status_id THEN
    IF fn_is_valid_status_transition(OLD.status_id, NEW.status_id) = 0 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid status transition.';
    END IF;
  END IF;
END$$

-- Trigger #40: Prevent adding add-ons to cancelled/completed/no_show appointments
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

-- Trigger #41: Prevent deleting confirmed appointments (must cancel first)
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
-- CATEGORY 8: MAX ACTIVE BOOKINGS PER USER (2 triggers)
-- Limits each user to a maximum of 5 active (pending or
-- confirmed) appointments at any time.
-- Adjust @v_max_active below to change the limit.
-- ---------------------------------------------------------

-- Trigger #42: Enforce max active bookings on INSERT
DROP TRIGGER IF EXISTS trg_appointments_before_insert_max_active$$
CREATE TRIGGER trg_appointments_before_insert_max_active
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  DECLARE v_max_active INT DEFAULT 5;
  DECLARE v_message VARCHAR(255);
  SET v_message = CONCAT('You already have ', v_max_active, ' active reservations. Please complete or cancel one before creating a new booking.');

  IF fn_can_book_more(NEW.user_id, v_max_active) = 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = v_message;
  END IF;
END$$

-- Trigger #43: Enforce max active bookings on UPDATE (when user_id or status changes)
DROP TRIGGER IF EXISTS trg_appointments_before_update_max_active$$
CREATE TRIGGER trg_appointments_before_update_max_active
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  DECLARE v_max_active INT DEFAULT 5;
  DECLARE v_message VARCHAR(255);
  SET v_message = CONCAT('You already have ', v_max_active, ' active reservations. Please complete or cancel one before creating a new booking.');

  -- Only check when user_id changes OR status is changing TO an active status
  IF NEW.user_id <> OLD.user_id OR OLD.status_id <> NEW.status_id THEN
    -- Only enforce if the new status is active (pending/confirmed)
    IF fn_is_active_status(NEW.status_id) = 1 THEN
      IF fn_user_active_booking_count(NEW.user_id) >= v_max_active THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = v_message;
      END IF;
    END IF;
  END IF;
END$$


DELIMITER ;

-- =========================================================
-- VERIFICATION QUERY
-- Run this after executing the script to confirm all 43
-- triggers were created successfully.
-- =========================================================

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

