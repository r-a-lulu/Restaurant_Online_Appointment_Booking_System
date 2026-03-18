-- =========================================================
-- Restaurant Online Appointment Booking System
-- Database Events (MariaDB/MySQL compatible)
-- Database: restaurant_booking_v1
-- =========================================================
--
-- Event Index
-- Event #1:  ev_auto_cancel_past_pending
-- Event #2:  ev_mark_no_show_confirmed
-- Event #3:  ev_purge_appointment_audit_logs
-- Event #4:  ev_purge_general_audit_logs
-- Event #5:  ev_purge_user_audit_logs
--
-- NOTE:
-- Ensure the event scheduler is enabled:
--   SET GLOBAL event_scheduler = ON;
--
-- Ensure default appointment statuses exist:
--   CALL sp_seed_default_statuses();

USE restaurant_booking_v1;

DELIMITER $$

-- ---------------------------------------------------------
-- EVENT #1: Auto-cancel past pending appointments
-- ---------------------------------------------------------
DROP EVENT IF EXISTS ev_auto_cancel_past_pending$$
CREATE EVENT ev_auto_cancel_past_pending
ON SCHEDULE EVERY 15 MINUTE
DO
BEGIN
  UPDATE appointments
  SET status_id = (SELECT status_id FROM appointment_status WHERE status_name = 'cancelled' LIMIT 1)
  WHERE status_id = (SELECT status_id FROM appointment_status WHERE status_name = 'pending' LIMIT 1)
    AND (
      appointment_date < CURDATE()
      OR (appointment_date = CURDATE() AND start_time < DATE_SUB(CURTIME(), INTERVAL 30 MINUTE))
    );
END$$

-- ---------------------------------------------------------
-- EVENT #2: Mark confirmed appointments as no-show after end time
-- ---------------------------------------------------------
DROP EVENT IF EXISTS ev_mark_no_show_confirmed$$
CREATE EVENT ev_mark_no_show_confirmed
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  UPDATE appointments
  SET status_id = (SELECT status_id FROM appointment_status WHERE status_name = 'no_show' LIMIT 1)
  WHERE status_id = (SELECT status_id FROM appointment_status WHERE status_name = 'confirmed' LIMIT 1)
    AND (
      appointment_date < CURDATE()
      OR (appointment_date = CURDATE() AND end_time < CURTIME())
    );
END$$

-- ---------------------------------------------------------
-- EVENT #3: Purge appointment audit logs older than 365 days
-- ---------------------------------------------------------
DROP EVENT IF EXISTS ev_purge_appointment_audit_logs$$
CREATE EVENT ev_purge_appointment_audit_logs
ON SCHEDULE EVERY 1 DAY
STARTS (TIMESTAMP(CURRENT_DATE, '02:00:00'))
DO
BEGIN
  DELETE FROM appointment_audit_logs
  WHERE changed_at < DATE_SUB(NOW(), INTERVAL 365 DAY);
END$$

-- ---------------------------------------------------------
-- EVENT #4: Purge general audit logs older than 365 days
-- ---------------------------------------------------------
DROP EVENT IF EXISTS ev_purge_general_audit_logs$$
CREATE EVENT ev_purge_general_audit_logs
ON SCHEDULE EVERY 1 DAY
STARTS (TIMESTAMP(CURRENT_DATE, '02:05:00'))
DO
BEGIN
  DELETE FROM general_audit_logs
  WHERE changed_at < DATE_SUB(NOW(), INTERVAL 365 DAY);
END$$

-- ---------------------------------------------------------
-- EVENT #5: Purge user audit logs older than 365 days
-- ---------------------------------------------------------
DROP EVENT IF EXISTS ev_purge_user_audit_logs$$
CREATE EVENT ev_purge_user_audit_logs
ON SCHEDULE EVERY 1 DAY
STARTS (TIMESTAMP(CURRENT_DATE, '02:10:00'))
DO
BEGIN
  DELETE FROM user_audit_logs
  WHERE created_at < DATE_SUB(NOW(), INTERVAL 365 DAY);
END$$

DELIMITER ;
