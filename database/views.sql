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
-- View #4:  vw_active_services
-- View #5:  vw_available_tables
-- View #6:  vw_active_event_packages
-- View #7:  vw_active_add_ons

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
  COALESCE(a.zone_id, t.zone_id) AS zone_id,
  dz.zone_name,
  a.table_id,
  t.seating_preference AS table_label,
  a.appointment_date,
  a.start_time,
  a.end_time,
  a.party_size,
  a.status_id,
  st.status_name AS status_name,
  a.special_requests,
  a.created_at,
  a.updated_at
FROM appointments a
JOIN users u ON u.user_id = a.user_id
JOIN appointment_status st ON st.status_id = a.status_id
LEFT JOIN services s ON s.service_id = a.service_id
LEFT JOIN event_packages ep ON ep.package_id = a.event_package_id
LEFT JOIN `tables` t ON t.table_id = a.table_id
LEFT JOIN dining_zones dz ON dz.zone_id = COALESCE(a.zone_id, t.zone_id);

-- ---------------------------------------------------------
-- View #2: Upcoming appointments (non-terminal)
-- ---------------------------------------------------------
CREATE OR REPLACE VIEW vw_upcoming_appointments AS
SELECT *
FROM vw_appointments_detail
WHERE appointment_date >= CURDATE()
  AND status_name NOT IN ('completed', 'cancelled', 'no_show');

-- ---------------------------------------------------------
-- View #3: Admin list (latest first)
-- ---------------------------------------------------------
CREATE OR REPLACE VIEW vw_admin_appointments AS
SELECT
  a.appointment_id,
  a.user_id,
  CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
  u.email AS customer_email,
  COALESCE(a.zone_id, t.zone_id) AS zone_id,
  dz.zone_name,
  a.table_id,
  t.seating_preference AS table_label,
  a.appointment_date,
  a.start_time,
  a.end_time,
  a.party_size,
  a.status_id,
  st.status_name AS status_name,
  a.special_requests,
  a.created_at,
  a.updated_at
FROM appointments a
JOIN users u ON u.user_id = a.user_id
JOIN appointment_status st ON st.status_id = a.status_id
LEFT JOIN `tables` t ON t.table_id = a.table_id
LEFT JOIN dining_zones dz ON dz.zone_id = COALESCE(a.zone_id, t.zone_id)
ORDER BY appointment_date DESC, start_time DESC;

-- ---------------------------------------------------------
-- View #4: Active services (no soft delete in schema)
-- ---------------------------------------------------------
CREATE OR REPLACE VIEW vw_active_services AS
SELECT service_id, service_name, price
FROM services
ORDER BY service_name;

-- ---------------------------------------------------------
-- View #5: Available tables (static list; availability enforced by triggers)
-- ---------------------------------------------------------
CREATE OR REPLACE VIEW vw_available_tables AS
SELECT
  t.table_id,
  t.capacity,
  t.seating_preference AS table_label,
  dz.zone_id,
  dz.zone_name
FROM `tables` t
JOIN dining_zones dz ON dz.zone_id = t.zone_id
ORDER BY dz.zone_name, t.seating_preference, t.capacity;

-- ---------------------------------------------------------
-- View #6: Active event packages
-- ---------------------------------------------------------
CREATE OR REPLACE VIEW vw_active_event_packages AS
SELECT package_id, package_name, base_price, description
FROM event_packages
ORDER BY package_name;

-- ---------------------------------------------------------
-- View #7: Active add-ons
-- ---------------------------------------------------------
CREATE OR REPLACE VIEW vw_active_add_ons AS
SELECT add_on_id, category, name, description, price
FROM add_ons
ORDER BY category, name;

-- ---------------------------------------------------------
-- View #8: Masked users for reporting
-- ---------------------------------------------------------
CREATE OR REPLACE VIEW vw_users_masked AS
SELECT
  user_id,
  role_id,
  CONCAT(LEFT(first_name, 1), REPEAT('*', 2)) AS first_name_masked,
  CONCAT(LEFT(last_name, 1), REPEAT('*', 2)) AS last_name_masked,
  CONCAT(LEFT(email, 2), '***', SUBSTRING(email, LOCATE('@', email))) AS email_masked,
  is_active,
  created_by,
  created_at,
  last_login
FROM users;
