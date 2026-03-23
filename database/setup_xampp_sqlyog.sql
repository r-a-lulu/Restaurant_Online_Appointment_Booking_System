-- XAMPP + SQLyog setup (MariaDB/MySQL compatible)
-- To change database name in SQLyog, replace both occurrences of `restaurant_booking_v1` below.

CREATE DATABASE IF NOT EXISTS restaurant_booking_v1
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE restaurant_booking_v1;

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS roles (
  role_id INT AUTO_INCREMENT PRIMARY KEY,
  role_name VARCHAR(50) NOT NULL,
  permissions_description VARCHAR(255) NULL,
  CONSTRAINT uq_roles_role_name UNIQUE (role_name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  role_id INT NOT NULL,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_by INT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login TIMESTAMP NULL,
  CONSTRAINT uq_users_email UNIQUE (email),
  CONSTRAINT fk_users_role
    FOREIGN KEY (role_id) REFERENCES roles (role_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_users_created_by
    FOREIGN KEY (created_by) REFERENCES users (user_id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS user_audit_logs (
  log_id BIGINT AUTO_INCREMENT PRIMARY KEY,
  target_user_id INT NOT NULL,
  actor_user_id INT NOT NULL,
  action_type VARCHAR(30) NOT NULL,
  old_values JSON NULL,
  new_values JSON NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT ck_user_audit_logs_action_type
    CHECK (action_type IN ('CREATE', 'UPDATE_ROLE', 'DEACTIVATE')),
  CONSTRAINT fk_user_audit_logs_target
    FOREIGN KEY (target_user_id) REFERENCES users (user_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_user_audit_logs_actor
    FOREIGN KEY (actor_user_id) REFERENCES users (user_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS services (
  service_id INT AUTO_INCREMENT PRIMARY KEY,
  service_name VARCHAR(100) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  CONSTRAINT ck_services_price_nonnegative CHECK (price >= 0),
  CONSTRAINT uq_services_name UNIQUE (service_name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS event_packages (
  package_id INT AUTO_INCREMENT PRIMARY KEY,
  package_name VARCHAR(120) NOT NULL,
  description VARCHAR(500) NULL,
  base_price DECIMAL(10,2) NOT NULL,
  CONSTRAINT ck_event_packages_base_price_nonnegative CHECK (base_price >= 0),
  CONSTRAINT uq_event_packages_name UNIQUE (package_name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS dining_zones (
  zone_id INT AUTO_INCREMENT PRIMARY KEY,
  zone_name VARCHAR(100) NOT NULL,
  CONSTRAINT uq_dining_zones_name UNIQUE (zone_name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `tables` (
  table_id INT AUTO_INCREMENT PRIMARY KEY,
  zone_id INT NOT NULL,
  table_number VARCHAR(30) NOT NULL,
  capacity INT NOT NULL,
  CONSTRAINT ck_tables_capacity_positive CHECK (capacity > 0),
  CONSTRAINT uq_tables_zone_table_number UNIQUE (zone_id, table_number),
  CONSTRAINT fk_tables_zone
    FOREIGN KEY (zone_id) REFERENCES dining_zones (zone_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS appointment_status (
  status_id INT AUTO_INCREMENT PRIMARY KEY,
  status_name VARCHAR(30) NOT NULL,
  CONSTRAINT uq_appointment_status_name UNIQUE (status_name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS add_ons (
  add_on_id INT AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(80) NOT NULL,
  name VARCHAR(120) NOT NULL,
  description VARCHAR(500) NULL,
  price DECIMAL(10,2) NOT NULL,
  CONSTRAINT ck_add_ons_price_nonnegative CHECK (price >= 0),
  CONSTRAINT uq_add_ons_category_name UNIQUE (category, name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS appointments (
  appointment_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  service_id INT NULL,
  table_id INT NULL,
  zone_id INT NULL,
  event_package_id INT NULL,
  appointment_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  party_size INT NOT NULL,
  special_requests TEXT NULL,
  status_id INT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT ck_appointments_time_order CHECK (end_time > start_time),
  CONSTRAINT ck_appointments_party_size_positive CHECK (party_size > 0),
  CONSTRAINT ck_appointments_target_scope
    CHECK (
      (table_id IS NOT NULL AND zone_id IS NULL) OR
      (table_id IS NULL AND zone_id IS NOT NULL)
    ),
  CONSTRAINT ck_appointments_bookable_item
    CHECK (
      (service_id IS NOT NULL AND event_package_id IS NULL)
      OR (service_id IS NULL AND event_package_id IS NOT NULL)
    ),
  CONSTRAINT fk_appointments_user
    FOREIGN KEY (user_id) REFERENCES users (user_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_appointments_service
    FOREIGN KEY (service_id) REFERENCES services (service_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_appointments_table
    FOREIGN KEY (table_id) REFERENCES `tables` (table_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_appointments_zone
    FOREIGN KEY (zone_id) REFERENCES dining_zones (zone_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_appointments_event_package
    FOREIGN KEY (event_package_id) REFERENCES event_packages (package_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_appointments_status
    FOREIGN KEY (status_id) REFERENCES appointment_status (status_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS appointment_add_ons (
  appointment_id INT NOT NULL,
  add_on_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  PRIMARY KEY (appointment_id, add_on_id),
  CONSTRAINT ck_appointment_add_ons_quantity_positive CHECK (quantity > 0),
  CONSTRAINT fk_appointment_add_ons_appointment
    FOREIGN KEY (appointment_id) REFERENCES appointments (appointment_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_appointment_add_ons_add_on
    FOREIGN KEY (add_on_id) REFERENCES add_ons (add_on_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_users_role_active ON users (role_id, is_active);
CREATE INDEX idx_user_audit_logs_target_created ON user_audit_logs (target_user_id, created_at);
CREATE INDEX idx_user_audit_logs_actor_created ON user_audit_logs (actor_user_id, created_at);
CREATE INDEX idx_tables_zone_capacity ON `tables` (zone_id, capacity);
CREATE INDEX idx_appointments_user_date ON appointments (user_id, appointment_date);
CREATE INDEX idx_appointments_date_status ON appointments (appointment_date, status_id);
CREATE INDEX idx_appointments_table_datetime ON appointments (table_id, appointment_date, start_time, end_time);
CREATE INDEX idx_appointments_zone_datetime ON appointments (zone_id, appointment_date, start_time, end_time);
CREATE INDEX idx_appointments_created_at ON appointments (created_at);
CREATE INDEX idx_appointments_service_id ON appointments (service_id);
CREATE INDEX idx_appointments_event_package_id ON appointments (event_package_id);
CREATE INDEX idx_appointment_add_ons_add_on ON appointment_add_ons (add_on_id);
CREATE UNIQUE INDEX uq_appointments_exact_table_slot ON appointments (table_id, appointment_date, start_time);
CREATE UNIQUE INDEX uq_appointments_exact_zone_slot ON appointments (zone_id, appointment_date, start_time);

-- =========================================================
-- Schema Additions Required by Triggers
-- =========================================================

CREATE TABLE IF NOT EXISTS appointment_audit_logs (
  log_id         BIGINT AUTO_INCREMENT PRIMARY KEY,
  appointment_id INT NOT NULL,
  action_type    VARCHAR(30) NOT NULL,
  old_values     JSON NULL,
  new_values     JSON NULL,
  changed_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT ck_appt_audit_action CHECK (
    action_type IN ('INSERT','UPDATE','STATUS_CHANGE','DELETE')
  ),
  INDEX idx_appt_audit_appt_id (appointment_id, changed_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS general_audit_logs (
  log_id       BIGINT AUTO_INCREMENT PRIMARY KEY,
  table_name   VARCHAR(64)  NOT NULL,
  record_id    INT          NOT NULL,
  action_type  VARCHAR(30)  NOT NULL,
  old_values   JSON NULL,
  new_values   JSON NULL,
  changed_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT ck_gen_audit_action CHECK (
    action_type IN ('INSERT','UPDATE','DELETE')
  ),
  INDEX idx_gen_audit_table_record (table_name, record_id, changed_at)
) ENGINE=InnoDB;

ALTER TABLE appointments
  ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL AFTER created_at;

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL AFTER last_login;
