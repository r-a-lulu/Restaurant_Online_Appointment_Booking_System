-- =========================================================
-- Database Security Roles and Users
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

-- MariaDB uses FOR user@host here; MySQL uses TO.
SET DEFAULT ROLE app_readonly FOR 'app_reader'@'%';
SET DEFAULT ROLE app_readwrite FOR 'app_writer'@'%';
SET DEFAULT ROLE app_admin FOR 'app_admin'@'%';
SET DEFAULT ROLE app_events FOR 'app_events'@'%';

FLUSH PRIVILEGES;
