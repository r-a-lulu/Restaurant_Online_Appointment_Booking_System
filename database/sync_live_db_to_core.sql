-- =========================================================
-- Sync Live Database To Clean Core Set
-- Restaurant Online Appointment Booking System
-- =========================================================
--
-- Purpose:
-- Drop archived / non-core database objects from the live database
-- so it matches the cleaned root files in database/.
--
-- Run this only if you want the live database to match the
-- current core SQL files exactly.

USE restaurant_booking_v1;

-- ---------------------------------------------------------
-- OPTIONAL PROCEDURES
-- ---------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_user_update;
DROP PROCEDURE IF EXISTS sp_user_deactivate;
DROP PROCEDURE IF EXISTS sp_user_get_by_id;
DROP PROCEDURE IF EXISTS sp_roles_list;
DROP PROCEDURE IF EXISTS sp_event_packages_create;
DROP PROCEDURE IF EXISTS sp_event_packages_update;
DROP PROCEDURE IF EXISTS sp_event_packages_delete;
DROP PROCEDURE IF EXISTS sp_appointment_update;
DROP PROCEDURE IF EXISTS sp_appointment_cancel;
DROP PROCEDURE IF EXISTS sp_appointment_get_by_id;
DROP PROCEDURE IF EXISTS sp_appointment_list_by_user;
DROP PROCEDURE IF EXISTS sp_appointment_list_admin;
DROP PROCEDURE IF EXISTS sp_appointment_add_on_update;
DROP PROCEDURE IF EXISTS sp_appointment_add_on_remove;
DROP PROCEDURE IF EXISTS sp_audit_appointment_log;
DROP PROCEDURE IF EXISTS sp_audit_general_log;
DROP PROCEDURE IF EXISTS sp_status_list;
DROP PROCEDURE IF EXISTS sp_seed_default_statuses;

-- ---------------------------------------------------------
-- OPTIONAL FUNCTIONS
-- ---------------------------------------------------------
DROP FUNCTION IF EXISTS fn_user_full_name;
DROP FUNCTION IF EXISTS fn_service_price;
DROP FUNCTION IF EXISTS fn_package_price;
DROP FUNCTION IF EXISTS fn_add_on_price;
DROP FUNCTION IF EXISTS fn_zone_booking_count;
DROP FUNCTION IF EXISTS fn_service_booking_count;
DROP FUNCTION IF EXISTS fn_table_current_status;

-- ---------------------------------------------------------
-- OPTIONAL VIEWS
-- ---------------------------------------------------------
DROP VIEW IF EXISTS vw_available_tables;
DROP VIEW IF EXISTS vw_users_masked;

-- ---------------------------------------------------------
-- OPTIONAL EVENTS
-- ---------------------------------------------------------
DROP EVENT IF EXISTS ev_purge_appointment_audit_logs;
DROP EVENT IF EXISTS ev_purge_general_audit_logs;

-- ---------------------------------------------------------
-- OPTIONAL AUDIT TRIGGERS
-- ---------------------------------------------------------
DROP TRIGGER IF EXISTS trg_services_after_insert;
DROP TRIGGER IF EXISTS trg_services_after_update;
DROP TRIGGER IF EXISTS trg_services_after_delete;
DROP TRIGGER IF EXISTS trg_event_packages_after_insert;
DROP TRIGGER IF EXISTS trg_event_packages_after_update;
DROP TRIGGER IF EXISTS trg_event_packages_after_delete;
DROP TRIGGER IF EXISTS trg_add_ons_after_insert;
DROP TRIGGER IF EXISTS trg_add_ons_after_update;
DROP TRIGGER IF EXISTS trg_add_ons_after_delete;
DROP TRIGGER IF EXISTS trg_tables_after_insert;
DROP TRIGGER IF EXISTS trg_tables_after_update;
DROP TRIGGER IF EXISTS trg_tables_after_delete;
DROP TRIGGER IF EXISTS trg_dining_zones_after_insert;
DROP TRIGGER IF EXISTS trg_dining_zones_after_update;
DROP TRIGGER IF EXISTS trg_dining_zones_after_delete;
DROP TRIGGER IF EXISTS trg_roles_after_insert;
DROP TRIGGER IF EXISTS trg_roles_after_update;
DROP TRIGGER IF EXISTS trg_roles_after_delete;
DROP TRIGGER IF EXISTS trg_appt_status_after_insert;
DROP TRIGGER IF EXISTS trg_appt_status_after_update;
DROP TRIGGER IF EXISTS trg_appt_status_after_delete;
DROP TRIGGER IF EXISTS trg_appt_add_ons_after_insert;
DROP TRIGGER IF EXISTS trg_appt_add_ons_after_update;
DROP TRIGGER IF EXISTS trg_appt_add_ons_after_delete;
DROP TRIGGER IF EXISTS trg_appointments_after_insert;
DROP TRIGGER IF EXISTS trg_appointments_after_update;
DROP TRIGGER IF EXISTS trg_appointments_after_delete;

-- ---------------------------------------------------------
-- VERIFICATION
-- Expected counts after cleanup:
-- procedures = 20
-- functions  = 18
-- views      = 6
-- triggers   = 16
-- events     = 3
-- ---------------------------------------------------------
SELECT 'procedures' AS object_type, COUNT(*) AS total
FROM information_schema.routines
WHERE routine_schema = 'restaurant_booking_v1' AND routine_type = 'PROCEDURE'
UNION ALL
SELECT 'functions' AS object_type, COUNT(*) AS total
FROM information_schema.routines
WHERE routine_schema = 'restaurant_booking_v1' AND routine_type = 'FUNCTION'
UNION ALL
SELECT 'views' AS object_type, COUNT(*) AS total
FROM information_schema.views
WHERE table_schema = 'restaurant_booking_v1'
UNION ALL
SELECT 'triggers' AS object_type, COUNT(*) AS total
FROM information_schema.triggers
WHERE trigger_schema = 'restaurant_booking_v1'
UNION ALL
SELECT 'events' AS object_type, COUNT(*) AS total
FROM information_schema.events
WHERE event_schema = 'restaurant_booking_v1';
