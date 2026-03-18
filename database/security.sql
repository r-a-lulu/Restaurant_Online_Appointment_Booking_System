-- =========================================================
-- Database Security Roles, Users, and Masking
-- Database: restaurant_booking_v1
-- =========================================================
--
-- Replace the passwords below before running in production.
-- Requires: MariaDB/MySQL with ROLE support.

USE restaurant_booking_v1;

-- ---------------------------
-- Roles
-- ---------------------------
CREATE ROLE IF NOT EXISTS app_readonly;
CREATE ROLE IF NOT EXISTS app_readwrite;
CREATE ROLE IF NOT EXISTS app_admin;
CREATE ROLE IF NOT EXISTS app_events;

-- ---------------------------
-- Base Grants (Short Form)
-- ---------------------------
-- Read-only: SELECT on entire schema
GRANT SELECT ON restaurant_booking_v1.* TO app_readonly;

-- Read/write for application
GRANT SELECT, INSERT, UPDATE, DELETE ON restaurant_booking_v1.* TO app_readwrite;

-- Admin: full privileges on schema
GRANT ALL PRIVILEGES ON restaurant_booking_v1.* TO app_admin;

-- Events: can execute and maintain events
GRANT EVENT ON restaurant_booking_v1.* TO app_events;
GRANT EXECUTE ON restaurant_booking_v1.* TO app_events;
GRANT SELECT, UPDATE, DELETE ON restaurant_booking_v1.* TO app_events;

-- ---------------------------
-- Masked View for PII (for reports / readonly access)
-- ---------------------------
CREATE OR REPLACE VIEW vw_users_masked AS
SELECT
  user_id,
  role_id,
  CONCAT(LEFT(first_name, 1), REPEAT('*', 2)) AS first_name_masked,
  CONCAT(LEFT(last_name, 1), REPEAT('*', 2)) AS last_name_masked,
  CONCAT(LEFT(email, 2), '***', SUBSTRING(email, LOCATE('@', email))) AS email_masked,
  is_active,
  created_at,
  last_login
FROM users;

GRANT SELECT ON restaurant_booking_v1.vw_users_masked TO app_readonly;

-- ---------------------------
-- Users (replace passwords)
-- ---------------------------
CREATE USER IF NOT EXISTS 'app_reader'@'%' IDENTIFIED BY 'REPLACE_ME_READONLY';
CREATE USER IF NOT EXISTS 'app_writer'@'%' IDENTIFIED BY 'REPLACE_ME_READWRITE';
CREATE USER IF NOT EXISTS 'app_admin'@'%' IDENTIFIED BY 'REPLACE_ME_ADMIN';
CREATE USER IF NOT EXISTS 'app_events'@'%' IDENTIFIED BY 'REPLACE_ME_EVENTS';

GRANT app_readonly TO 'app_reader'@'%';
GRANT app_readwrite TO 'app_writer'@'%';
GRANT app_admin TO 'app_admin'@'%';
GRANT app_events TO 'app_events'@'%';

SET DEFAULT ROLE app_readonly TO 'app_reader'@'%';
SET DEFAULT ROLE app_readwrite TO 'app_writer'@'%';
SET DEFAULT ROLE app_admin TO 'app_admin'@'%';
SET DEFAULT ROLE app_events TO 'app_events'@'%';

FLUSH PRIVILEGES;
