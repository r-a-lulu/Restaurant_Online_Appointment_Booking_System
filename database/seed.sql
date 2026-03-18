-- Restaurant Online Appointment Booking System
-- Seed Data (MySQL 8+)
-- Run after setup_xampp_sqlyog.sql

USE restaurant_booking_v1;

-- =========================================================
-- ROLES
-- =========================================================

INSERT INTO roles (role_name, permissions_description)
VALUES
  ('admin', 'Full access to system administration and management'),
  ('staff', 'Manage reservations and customer details'),
  ('guest', 'Customer account access')
ON DUPLICATE KEY UPDATE permissions_description = VALUES(permissions_description);

-- =========================================================
-- APPOINTMENT STATUS
-- =========================================================

INSERT INTO appointment_status (status_name)
VALUES
  ('pending'),
  ('confirmed'),
  ('completed'),
  ('cancelled'),
  ('no_show')
ON DUPLICATE KEY UPDATE status_name = VALUES(status_name);

-- =========================================================
-- USERS (1 admin, 3 customers)
-- Passwords are placeholder hashes; replace with real bcrypt hashes.
-- =========================================================

INSERT INTO users (role_id, first_name, last_name, email, password_hash, is_active)
VALUES
  ((SELECT role_id FROM roles WHERE role_name = 'admin' LIMIT 1), 'System', 'Admin', 'admin@eudaimonia.com', '$2y$10$REPLACE_WITH_BCRYPT_HASH_ADMIN', TRUE),
  ((SELECT role_id FROM roles WHERE role_name = 'guest' LIMIT 1), 'Liam', 'Cruz', 'liam.cruz@example.com', '$2y$10$REPLACE_WITH_BCRYPT_HASH_CUST1', TRUE),
  ((SELECT role_id FROM roles WHERE role_name = 'guest' LIMIT 1), 'Mia', 'Santos', 'mia.santos@example.com', '$2y$10$REPLACE_WITH_BCRYPT_HASH_CUST2', TRUE),
  ((SELECT role_id FROM roles WHERE role_name = 'guest' LIMIT 1), 'Noah', 'Reyes', 'noah.reyes@example.com', '$2y$10$REPLACE_WITH_BCRYPT_HASH_CUST3', TRUE)
ON DUPLICATE KEY UPDATE
  role_id = VALUES(role_id),
  first_name = VALUES(first_name),
  last_name = VALUES(last_name),
  password_hash = VALUES(password_hash),
  is_active = VALUES(is_active);

-- =========================================================
-- SERVICES
-- =========================================================

INSERT INTO services (service_name, price)
VALUES
  ('Table Reservation', 0.00),
  ('Private Dining Service', 1500.00),
  ('Celebration Setup', 850.00)
ON DUPLICATE KEY UPDATE price = VALUES(price);

-- =========================================================
-- EVENT PACKAGES
-- =========================================================

INSERT INTO event_packages (package_name, description, base_price)
VALUES
  ('Birthday Package', 'Decor setup and celebration assistance', 5000.00),
  ('Anniversary Package', 'Premium table styling and curated dining experience', 6500.00),
  ('Corporate Package', 'Group seating layout and service coordination', 8000.00)
ON DUPLICATE KEY UPDATE
  description = VALUES(description),
  base_price = VALUES(base_price);

-- =========================================================
-- ADD-ONS
-- =========================================================

INSERT INTO add_ons (category, name, description, price)
VALUES
  ('Decor', 'Floral Table Setup', 'Fresh floral arrangement for the reserved table', 1200.00),
  ('Catering', 'Custom Dessert Platter', 'Chef-prepared dessert set for the party', 1800.00),
  ('Tech', 'AV Presentation Setup', 'Audio and display support for events', 2500.00),
  ('Service', 'Priority Host Assistance', 'Dedicated host support during service window', 900.00)
ON DUPLICATE KEY UPDATE
  description = VALUES(description),
  price = VALUES(price);

-- =========================================================
-- TABLE LAYOUT (aligned with mock data in app/admin/floor/page.tsx)
-- =========================================================

INSERT INTO dining_zones (zone_name)
VALUES
  ('Main Dining Room'),
  ('The Patio'),
  ('The Bar')
ON DUPLICATE KEY UPDATE zone_name = VALUES(zone_name);

INSERT INTO tables (zone_id, table_number, capacity)
SELECT dz.zone_id, t.table_number, t.capacity
FROM (
  SELECT 'Main Dining Room' AS zone_name, 'Table 1' AS table_number, 2 AS capacity UNION ALL
  SELECT 'Main Dining Room', 'Table 2', 2 UNION ALL
  SELECT 'Main Dining Room', 'Table 3', 4 UNION ALL
  SELECT 'Main Dining Room', 'Table 4', 4 UNION ALL
  SELECT 'Main Dining Room', 'Table 5', 6 UNION ALL
  SELECT 'Main Dining Room', 'Table 6', 4 UNION ALL
  SELECT 'Main Dining Room', 'Table 7', 2 UNION ALL
  SELECT 'Main Dining Room', 'Table 8', 8 UNION ALL
  SELECT 'Main Dining Room', 'Table 9', 4 UNION ALL
  SELECT 'Main Dining Room', 'Table 10', 2 UNION ALL
  SELECT 'The Patio', 'Garden 1', 2 UNION ALL
  SELECT 'The Patio', 'Garden 2', 4 UNION ALL
  SELECT 'The Patio', 'Fountain', 4 UNION ALL
  SELECT 'The Patio', 'Pergola', 6 UNION ALL
  SELECT 'The Patio', 'Corner', 4 UNION ALL
  SELECT 'The Bar', 'Bar 1', 2 UNION ALL
  SELECT 'The Bar', 'Bar 2', 2 UNION ALL
  SELECT 'The Bar', 'High Top 1', 4 UNION ALL
  SELECT 'The Bar', 'High Top 2', 4 UNION ALL
  SELECT 'The Bar', 'Lounge', 6
) AS t
JOIN dining_zones dz ON dz.zone_name = t.zone_name
ON DUPLICATE KEY UPDATE capacity = VALUES(capacity);
