-- Restaurant Online Appointment Booking System
-- Seed Data (MySQL 8+)
-- Run after schema.sql

USE restaurant_booking_v1;

-- =========================================================
-- ROLES
-- =========================================================

INSERT INTO roles (role_name, permissions_description)
VALUES
  ('admin', 'Full access to system administration and management'),
  ('staff', 'Manage reservations and customer details'),
  ('guest', 'Customer account access'),
  ('customer', 'Customer account access')
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
-- SYSTEM SETTINGS
-- =========================================================

INSERT INTO system_settings (setting_key, setting_value)
VALUES
  ('restaurant_name', 'Eudaimonia'),
  ('restaurant_email', 'hello@eudaimonia.com'),
  ('restaurant_phone', '+1 (555) 000-1234'),
  ('restaurant_address', '12 Harmony Lane, New York, NY'),
  ('restaurant_description', 'A contemporary dining experience rooted in timeless hospitality.'),
  ('reservation_confirmation_note', 'A confirmation email will be sent to your address within 1 hour. If you have any questions, please call us at {phone} or email {email}.'),
  ('notify_new_reservation', '1'),
  ('notify_cancellation', '1'),
  ('notify_daily_summary', '0'),
  ('floor_manual_occupied_minutes', '120'),
  ('maintenance_mode', '0'),
  ('maintenance_message', 'We''re temporarily offline for maintenance. Please check back shortly.')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- =========================================================
-- USERS (1 admin, 3 customers)
-- Passwords are placeholder hashes; replace with real bcrypt hashes.
-- =========================================================

INSERT INTO users (role_id, first_name, last_name, email, phone, password_hash, is_active, created_by)
VALUES
  ((SELECT role_id FROM roles WHERE role_name = 'admin' LIMIT 1), 'System', 'Admin', 'admin@eudaimonia.com', '0912-345-6789', '$2y$10$.BRPXTux9KN4ETwkCyjIauKPvM2atTbjZEdo2ZhPAYVx6Nfd8kjg6', TRUE, NULL)
ON DUPLICATE KEY UPDATE
  role_id = VALUES(role_id),
  first_name = VALUES(first_name),
  last_name = VALUES(last_name),
  phone = VALUES(phone),
  password_hash = VALUES(password_hash),
  is_active = VALUES(is_active),
  created_by = VALUES(created_by);

INSERT INTO users (role_id, first_name, last_name, email, phone, password_hash, is_active, created_by)
VALUES
  ((SELECT role_id FROM roles WHERE role_name = 'guest' LIMIT 1), 'Liam', 'Cruz', 'liam.cruz@example.com', '0913-456-7890', '$2y$10$C9xPahEsgo4vGWOxA9H5AeDwIHOoDD5LtJP/zy8k5a8uk8ESnKfxu', TRUE, 1),
  ((SELECT role_id FROM roles WHERE role_name = 'guest' LIMIT 1), 'Mia', 'Santos', 'mia.santos@example.com', '0914-567-8901', '$2y$10$b/NO7SE82wW5BZDXnt3qy.AgAJ5piuDTaWShlew/dHsV.CKw7fbja', TRUE, 1),
  ((SELECT role_id FROM roles WHERE role_name = 'guest' LIMIT 1), 'Noah', 'Reyes', 'noah.reyes@example.com', '0915-678-9012', '$2y$10$gUZNd2JRMePwGcH0ErPr4uMgLIK83V5rhdXV.x9Qu9NNgEa/FE0MC', TRUE, 1)
ON DUPLICATE KEY UPDATE
  role_id = VALUES(role_id),
  first_name = VALUES(first_name),
  last_name = VALUES(last_name),
  phone = VALUES(phone),
  password_hash = VALUES(password_hash),
  is_active = VALUES(is_active),
  created_by = VALUES(created_by);

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

INSERT INTO tables (zone_id, capacity, seating_preference)
SELECT dz.zone_id, t.capacity, t.seating_preference
FROM (
  SELECT 'Main Dining Room' AS zone_name, 2 AS capacity, 'Window Table' AS seating_preference UNION ALL
  SELECT 'Main Dining Room', 2, 'Window Table' UNION ALL
  SELECT 'Main Dining Room', 4, 'Banquette' UNION ALL
  SELECT 'Main Dining Room', 4, 'Banquette' UNION ALL
  SELECT 'Main Dining Room', 6, 'Fireplace' UNION ALL
  SELECT 'Main Dining Room', 4, 'Chef''s View' UNION ALL
  SELECT 'Main Dining Room', 2, 'Private Alcove' UNION ALL
  SELECT 'Main Dining Room', 8, 'Chandelier' UNION ALL
  SELECT 'Main Dining Room', 4, 'Window Table' UNION ALL
  SELECT 'Main Dining Room', 2, 'Banquette' UNION ALL
  SELECT 'The Patio', 2, 'Garden View' UNION ALL
  SELECT 'The Patio', 4, 'Garden View' UNION ALL
  SELECT 'The Patio', 4, 'Fountain Side' UNION ALL
  SELECT 'The Patio', 6, 'Pergola' UNION ALL
  SELECT 'The Patio', 4, 'Corner Alcove' UNION ALL
  SELECT 'The Bar', 2, 'Bar Counter' UNION ALL
  SELECT 'The Bar', 2, 'Bar Counter' UNION ALL
  SELECT 'The Bar', 4, 'High Tops' UNION ALL
  SELECT 'The Bar', 4, 'High Tops' UNION ALL
  SELECT 'The Bar', 6, 'Lounge Booths'
) AS t
JOIN dining_zones dz ON dz.zone_name = t.zone_name
ON DUPLICATE KEY UPDATE capacity = VALUES(capacity), seating_preference = VALUES(seating_preference);
