-- =========================================================
-- Archived / Optional Audit Triggers
-- Restaurant Online Appointment Booking System
-- =========================================================

USE restaurant_booking_v1;

DELIMITER $$

DROP TRIGGER IF EXISTS trg_services_after_insert$$
CREATE TRIGGER trg_services_after_insert
AFTER INSERT ON services
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('services', NEW.service_id, 'INSERT', NULL,
    JSON_OBJECT('service_name', NEW.service_name, 'price', NEW.price));
END$$

DROP TRIGGER IF EXISTS trg_services_after_update$$
CREATE TRIGGER trg_services_after_update
AFTER UPDATE ON services
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('services', NEW.service_id, 'UPDATE',
    JSON_OBJECT('service_name', OLD.service_name, 'price', OLD.price),
    JSON_OBJECT('service_name', NEW.service_name, 'price', NEW.price));
END$$

DROP TRIGGER IF EXISTS trg_services_after_delete$$
CREATE TRIGGER trg_services_after_delete
AFTER DELETE ON services
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('services', OLD.service_id, 'DELETE',
    JSON_OBJECT('service_name', OLD.service_name, 'price', OLD.price), NULL);
END$$

DROP TRIGGER IF EXISTS trg_event_packages_after_insert$$
CREATE TRIGGER trg_event_packages_after_insert
AFTER INSERT ON event_packages
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('event_packages', NEW.package_id, 'INSERT', NULL,
    JSON_OBJECT('package_name', NEW.package_name, 'description', NEW.description, 'base_price', NEW.base_price));
END$$

DROP TRIGGER IF EXISTS trg_event_packages_after_update$$
CREATE TRIGGER trg_event_packages_after_update
AFTER UPDATE ON event_packages
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('event_packages', NEW.package_id, 'UPDATE',
    JSON_OBJECT('package_name', OLD.package_name, 'description', OLD.description, 'base_price', OLD.base_price),
    JSON_OBJECT('package_name', NEW.package_name, 'description', NEW.description, 'base_price', NEW.base_price));
END$$

DROP TRIGGER IF EXISTS trg_event_packages_after_delete$$
CREATE TRIGGER trg_event_packages_after_delete
AFTER DELETE ON event_packages
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('event_packages', OLD.package_id, 'DELETE',
    JSON_OBJECT('package_name', OLD.package_name, 'description', OLD.description, 'base_price', OLD.base_price), NULL);
END$$

DROP TRIGGER IF EXISTS trg_add_ons_after_insert$$
CREATE TRIGGER trg_add_ons_after_insert
AFTER INSERT ON add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('add_ons', NEW.add_on_id, 'INSERT', NULL,
    JSON_OBJECT('category', NEW.category, 'name', NEW.name, 'description', NEW.description, 'price', NEW.price));
END$$

DROP TRIGGER IF EXISTS trg_add_ons_after_update$$
CREATE TRIGGER trg_add_ons_after_update
AFTER UPDATE ON add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('add_ons', NEW.add_on_id, 'UPDATE',
    JSON_OBJECT('category', OLD.category, 'name', OLD.name, 'description', OLD.description, 'price', OLD.price),
    JSON_OBJECT('category', NEW.category, 'name', NEW.name, 'description', NEW.description, 'price', NEW.price));
END$$

DROP TRIGGER IF EXISTS trg_add_ons_after_delete$$
CREATE TRIGGER trg_add_ons_after_delete
AFTER DELETE ON add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('add_ons', OLD.add_on_id, 'DELETE',
    JSON_OBJECT('category', OLD.category, 'name', OLD.name, 'description', OLD.description, 'price', OLD.price), NULL);
END$$

DROP TRIGGER IF EXISTS trg_tables_after_insert$$
CREATE TRIGGER trg_tables_after_insert
AFTER INSERT ON `tables`
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('tables', NEW.table_id, 'INSERT', NULL,
    JSON_OBJECT('zone_id', NEW.zone_id, 'table_label', NEW.seating_preference, 'capacity', NEW.capacity));
END$$

DROP TRIGGER IF EXISTS trg_tables_after_update$$
CREATE TRIGGER trg_tables_after_update
AFTER UPDATE ON `tables`
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('tables', NEW.table_id, 'UPDATE',
    JSON_OBJECT('zone_id', OLD.zone_id, 'table_label', OLD.seating_preference, 'capacity', OLD.capacity),
    JSON_OBJECT('zone_id', NEW.zone_id, 'table_label', NEW.seating_preference, 'capacity', NEW.capacity));
END$$

DROP TRIGGER IF EXISTS trg_tables_after_delete$$
CREATE TRIGGER trg_tables_after_delete
AFTER DELETE ON `tables`
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('tables', OLD.table_id, 'DELETE',
    JSON_OBJECT('zone_id', OLD.zone_id, 'table_label', OLD.seating_preference, 'capacity', OLD.capacity), NULL);
END$$

DROP TRIGGER IF EXISTS trg_dining_zones_after_insert$$
CREATE TRIGGER trg_dining_zones_after_insert
AFTER INSERT ON dining_zones
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('dining_zones', NEW.zone_id, 'INSERT', NULL,
    JSON_OBJECT('zone_name', NEW.zone_name));
END$$

DROP TRIGGER IF EXISTS trg_dining_zones_after_update$$
CREATE TRIGGER trg_dining_zones_after_update
AFTER UPDATE ON dining_zones
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('dining_zones', NEW.zone_id, 'UPDATE',
    JSON_OBJECT('zone_name', OLD.zone_name),
    JSON_OBJECT('zone_name', NEW.zone_name));
END$$

DROP TRIGGER IF EXISTS trg_dining_zones_after_delete$$
CREATE TRIGGER trg_dining_zones_after_delete
AFTER DELETE ON dining_zones
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('dining_zones', OLD.zone_id, 'DELETE',
    JSON_OBJECT('zone_name', OLD.zone_name), NULL);
END$$

DROP TRIGGER IF EXISTS trg_roles_after_insert$$
CREATE TRIGGER trg_roles_after_insert
AFTER INSERT ON roles
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('roles', NEW.role_id, 'INSERT', NULL,
    JSON_OBJECT('role_name', NEW.role_name, 'permissions_description', NEW.permissions_description));
END$$

DROP TRIGGER IF EXISTS trg_roles_after_update$$
CREATE TRIGGER trg_roles_after_update
AFTER UPDATE ON roles
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('roles', NEW.role_id, 'UPDATE',
    JSON_OBJECT('role_name', OLD.role_name, 'permissions_description', OLD.permissions_description),
    JSON_OBJECT('role_name', NEW.role_name, 'permissions_description', NEW.permissions_description));
END$$

DROP TRIGGER IF EXISTS trg_roles_after_delete$$
CREATE TRIGGER trg_roles_after_delete
AFTER DELETE ON roles
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('roles', OLD.role_id, 'DELETE',
    JSON_OBJECT('role_name', OLD.role_name, 'permissions_description', OLD.permissions_description), NULL);
END$$

DROP TRIGGER IF EXISTS trg_appt_status_after_insert$$
CREATE TRIGGER trg_appt_status_after_insert
AFTER INSERT ON appointment_status
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_status', NEW.status_id, 'INSERT', NULL,
    JSON_OBJECT('status_name', NEW.status_name));
END$$

DROP TRIGGER IF EXISTS trg_appt_status_after_update$$
CREATE TRIGGER trg_appt_status_after_update
AFTER UPDATE ON appointment_status
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_status', NEW.status_id, 'UPDATE',
    JSON_OBJECT('status_name', OLD.status_name),
    JSON_OBJECT('status_name', NEW.status_name));
END$$

DROP TRIGGER IF EXISTS trg_appt_status_after_delete$$
CREATE TRIGGER trg_appt_status_after_delete
AFTER DELETE ON appointment_status
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_status', OLD.status_id, 'DELETE',
    JSON_OBJECT('status_name', OLD.status_name), NULL);
END$$

DELIMITER ;
