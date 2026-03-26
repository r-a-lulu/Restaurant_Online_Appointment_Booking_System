-- =========================================================
-- Restaurant Online Appointment Booking System
-- Database Events (MariaDB/MySQL compatible)
-- Database: restaurant_booking_v1
-- =========================================================
--
-- Event Index
-- Event #1:  ev_auto_cancel_past_pending
-- Event #2:  ev_auto_complete_finished_confirmed
-- Event #3:  ev_purge_appointment_audit_logs
-- Event #4:  ev_purge_general_audit_logs
-- Event #5:  ev_clear_expired_manual_occupied_tables
--
-- NOTE:
-- Ensure the event scheduler is enabled:
--   SET GLOBAL event_scheduler = ON;

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
-- EVENT #2: Auto-complete confirmed appointments after end time
-- Note: this keeps the dashboard history moving automatically.
-- If you need no-show tracking, add a separate attendance/check-in signal.
-- ---------------------------------------------------------
DROP EVENT IF EXISTS ev_auto_complete_finished_confirmed$$
CREATE EVENT ev_auto_complete_finished_confirmed
ON SCHEDULE EVERY 30 MINUTE
DO
BEGIN
  UPDATE appointments
  SET status_id = (SELECT status_id FROM appointment_status WHERE status_name = 'completed' LIMIT 1)
  WHERE status_id = (SELECT status_id FROM appointment_status WHERE status_name = 'confirmed' LIMIT 1)
    AND (
      appointment_date < CURDATE()
      OR (appointment_date = CURDATE() AND end_time <= CURTIME())
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
-- EVENT #5: Clear expired manual occupied table flags
-- ---------------------------------------------------------
DROP EVENT IF EXISTS ev_clear_expired_manual_occupied_tables$$
CREATE EVENT ev_clear_expired_manual_occupied_tables
ON SCHEDULE EVERY 10 MINUTE
DO
BEGIN
  UPDATE `tables` t
  SET t.current_status = 'available',
      t.manual_status_until = NULL
  WHERE t.current_status = 'occupied'
    AND (t.manual_status_until IS NULL OR t.manual_status_until <= NOW())
    AND NOT EXISTS (
      SELECT 1
      FROM appointments a
      JOIN appointment_status s ON s.status_id = a.status_id
      WHERE a.table_id = t.table_id
        AND a.appointment_date = CURDATE()
        AND s.status_name = 'confirmed'
        AND a.start_time <= CURTIME()
        AND a.end_time > CURTIME()
    );
END$$

DELIMITER ;
