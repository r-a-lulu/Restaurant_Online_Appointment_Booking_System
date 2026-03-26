-- =========================================================
-- Archived / Optional Events
-- Restaurant Online Appointment Booking System
-- =========================================================

USE restaurant_booking_v1;

DELIMITER $$

DROP EVENT IF EXISTS ev_purge_appointment_audit_logs$$
CREATE EVENT ev_purge_appointment_audit_logs
ON SCHEDULE EVERY 1 DAY
STARTS (TIMESTAMP(CURRENT_DATE, '02:00:00'))
DO
BEGIN
  DELETE FROM appointment_audit_logs
  WHERE changed_at < DATE_SUB(NOW(), INTERVAL 365 DAY);
END$$

DROP EVENT IF EXISTS ev_purge_general_audit_logs$$
CREATE EVENT ev_purge_general_audit_logs
ON SCHEDULE EVERY 1 DAY
STARTS (TIMESTAMP(CURRENT_DATE, '02:05:00'))
DO
BEGIN
  DELETE FROM general_audit_logs
  WHERE changed_at < DATE_SUB(NOW(), INTERVAL 365 DAY);
END$$

DELIMITER ;
