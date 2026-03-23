-- =========================================================
-- Restaurant Online Appointment Booking System
-- Database Views (MariaDB/MySQL compatible)
-- Database: restaurant_booking_v1
-- =========================================================
--
-- View Index
-- View #1:  vw_appointments_detail
-- View #2:  vw_upcoming_appointments
-- View #3:  vw_admin_appointments

USE restaurant_booking_v1;

-- ---------------------------------------------------------
-- View #1: Full appointment detail with joins and totals
-- ---------------------------------------------------------
CREATE OR REPLACE VIEW vw_appointments_detail AS
SELECT
  a.appointment_id,
  a.user_id,
  CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
  u.email AS customer_email,
  a.service_id,
  s.service_name,
  a.event_package_id,
  ep.package_name,
  a.zone_id,
  dz.zone_name,
  a.table_id,
  t.table_number,
  a.appointment_date,
  a.start_time,
  a.end_time,
  a.party_size,
  a.status_id,
  fn_status_name(a.status_id) AS status_name,
  fn_appointment_total(a.appointment_id) AS total_amount,
  a.special_requests,
  a.created_at,
  a.updated_at
FROM appointments a
JOIN users u ON u.user_id = a.user_id
LEFT JOIN services s ON s.service_id = a.service_id
LEFT JOIN event_packages ep ON ep.package_id = a.event_package_id
LEFT JOIN dining_zones dz ON dz.zone_id = a.zone_id
LEFT JOIN `tables` t ON t.table_id = a.table_id;

-- ---------------------------------------------------------
-- View #2: Upcoming appointments (non-terminal)
-- ---------------------------------------------------------
CREATE OR REPLACE VIEW vw_upcoming_appointments AS
SELECT *
FROM vw_appointments_detail
WHERE appointment_date >= CURDATE()
  AND fn_is_terminal_status(status_id) = 0;

-- ---------------------------------------------------------
-- View #3: Admin list (latest first)
-- ---------------------------------------------------------
CREATE OR REPLACE VIEW vw_admin_appointments AS
SELECT *
FROM vw_appointments_detail
ORDER BY appointment_date DESC, start_time DESC;
