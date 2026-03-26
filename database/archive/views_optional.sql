-- =========================================================
-- Archived / Optional Views
-- Restaurant Online Appointment Booking System
-- =========================================================

USE restaurant_booking_v1;

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
