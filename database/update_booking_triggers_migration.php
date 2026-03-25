<?php
/**
 * Database Migration: Refresh appointment business-rule triggers and error messages
 */
require_once __DIR__ . '/../includes/security.php';

try {
    $pdo = db();
    echo "Updating booking triggers...\n";

    $statements = [
        "DROP TRIGGER IF EXISTS trg_appointments_before_insert_capacity",
        "CREATE TRIGGER trg_appointments_before_insert_capacity
         BEFORE INSERT ON appointments
         FOR EACH ROW
         BEGIN
           IF NEW.table_id IS NOT NULL THEN
             IF fn_party_fits_table(NEW.table_id, NEW.party_size) = 0 THEN
               SIGNAL SQLSTATE '45000'
                 SET MESSAGE_TEXT = 'The selected table cannot fit this party size. Please choose another table or reduce the number of guests.';
             END IF;
           END IF;
         END",
        "DROP TRIGGER IF EXISTS trg_appointments_before_update_capacity",
        "CREATE TRIGGER trg_appointments_before_update_capacity
         BEFORE UPDATE ON appointments
         FOR EACH ROW
         BEGIN
           IF NEW.table_id IS NOT NULL
              AND (NEW.party_size <> OLD.party_size OR NOT (NEW.table_id <=> OLD.table_id))
           THEN
             IF fn_party_fits_table(NEW.table_id, NEW.party_size) = 0 THEN
               SIGNAL SQLSTATE '45000'
                 SET MESSAGE_TEXT = 'The selected table cannot fit this party size. Please choose another table or reduce the number of guests.';
             END IF;
           END IF;
         END",
        "DROP TRIGGER IF EXISTS trg_appointments_before_insert_overlap",
        "CREATE TRIGGER trg_appointments_before_insert_overlap
         BEFORE INSERT ON appointments
         FOR EACH ROW
         BEGIN
           IF NEW.table_id IS NOT NULL THEN
             IF fn_table_has_conflict(NEW.table_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NULL) = 1 THEN
               SIGNAL SQLSTATE '45000'
                 SET MESSAGE_TEXT = 'That time is already booked for the selected table. Please choose another time.';
             END IF;
           END IF;

           IF NEW.zone_id IS NOT NULL THEN
             IF fn_zone_has_conflict(NEW.zone_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NULL) = 1 THEN
               SIGNAL SQLSTATE '45000'
                 SET MESSAGE_TEXT = 'That time is already booked in the selected dining zone. Please choose another time or zone.';
             END IF;
           END IF;
         END",
        "DROP TRIGGER IF EXISTS trg_appointments_before_update_overlap",
        "CREATE TRIGGER trg_appointments_before_update_overlap
         BEFORE UPDATE ON appointments
         FOR EACH ROW
         BEGIN
           IF NOT (NEW.table_id <=> OLD.table_id)
              OR NOT (NEW.zone_id <=> OLD.zone_id)
              OR NEW.appointment_date <> OLD.appointment_date
              OR NEW.start_time <> OLD.start_time
              OR NEW.end_time <> OLD.end_time
           THEN
             IF NEW.table_id IS NOT NULL THEN
               IF fn_table_has_conflict(NEW.table_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NEW.appointment_id) = 1 THEN
                 SIGNAL SQLSTATE '45000'
                   SET MESSAGE_TEXT = 'That time is already booked for the selected table. Please choose another time.';
               END IF;
             END IF;

             IF NEW.zone_id IS NOT NULL THEN
               IF fn_zone_has_conflict(NEW.zone_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NEW.appointment_id) = 1 THEN
                 SIGNAL SQLSTATE '45000'
                   SET MESSAGE_TEXT = 'That time is already booked in the selected dining zone. Please choose another time or zone.';
               END IF;
             END IF;
           END IF;
         END",
        "DROP TRIGGER IF EXISTS trg_appointments_before_insert_past_date",
        "CREATE TRIGGER trg_appointments_before_insert_past_date
         BEFORE INSERT ON appointments
         FOR EACH ROW
         BEGIN
           IF fn_is_past_datetime(NEW.appointment_date, NEW.start_time) = 1 THEN
             SIGNAL SQLSTATE '45000'
               SET MESSAGE_TEXT = 'Reservations cannot be made in the past. Please choose a future date and time.';
           END IF;
         END",
        "DROP TRIGGER IF EXISTS trg_appointments_before_update_past_date",
        "CREATE TRIGGER trg_appointments_before_update_past_date
         BEFORE UPDATE ON appointments
         FOR EACH ROW
         BEGIN
           IF NEW.appointment_date <> OLD.appointment_date
              OR NEW.start_time <> OLD.start_time
           THEN
             IF fn_is_past_datetime(NEW.appointment_date, NEW.start_time) = 1 THEN
               SIGNAL SQLSTATE '45000'
                 SET MESSAGE_TEXT = 'Reservations cannot be moved to a past date. Please choose a future date and time.';
             END IF;
           END IF;
         END",
        "DROP TRIGGER IF EXISTS trg_appointments_before_insert_service_package",
        "CREATE TRIGGER trg_appointments_before_insert_service_package
         BEFORE INSERT ON appointments
         FOR EACH ROW
         BEGIN
           IF (NEW.service_id IS NULL AND NEW.event_package_id IS NULL)
              OR (NEW.service_id IS NOT NULL AND NEW.event_package_id IS NOT NULL)
           THEN
             SIGNAL SQLSTATE '45000'
               SET MESSAGE_TEXT = 'Please choose either a service or an event package, not both.';
           END IF;
         END",
        "DROP TRIGGER IF EXISTS trg_appointments_before_update_service_package",
        "CREATE TRIGGER trg_appointments_before_update_service_package
         BEFORE UPDATE ON appointments
         FOR EACH ROW
         BEGIN
           IF (NEW.service_id IS NULL AND NEW.event_package_id IS NULL)
              OR (NEW.service_id IS NOT NULL AND NEW.event_package_id IS NOT NULL)
           THEN
             SIGNAL SQLSTATE '45000'
               SET MESSAGE_TEXT = 'Please choose either a service or an event package, not both.';
           END IF;
         END",
        "DROP TRIGGER IF EXISTS trg_appointments_before_update_status_flow",
        "CREATE TRIGGER trg_appointments_before_update_status_flow
         BEFORE UPDATE ON appointments
         FOR EACH ROW
         BEGIN
           IF OLD.status_id <> NEW.status_id THEN
             IF fn_is_valid_status_transition(OLD.status_id, NEW.status_id) = 0 THEN
               SIGNAL SQLSTATE '45000'
                 SET MESSAGE_TEXT = 'That reservation status change is not allowed. Please choose a valid next status.';
             END IF;
           END IF;
         END",
        "DROP TRIGGER IF EXISTS trg_appointments_before_delete_guard",
        "CREATE TRIGGER trg_appointments_before_delete_guard
         BEFORE DELETE ON appointments
         FOR EACH ROW
         BEGIN
           IF fn_status_name(OLD.status_id) = 'confirmed' THEN
             SIGNAL SQLSTATE '45000'
               SET MESSAGE_TEXT = 'Confirmed reservations cannot be deleted. Please cancel it first.';
           END IF;
         END",
        "DROP TRIGGER IF EXISTS trg_appointments_before_insert_max_active",
        "CREATE TRIGGER trg_appointments_before_insert_max_active
         BEFORE INSERT ON appointments
         FOR EACH ROW
         BEGIN
           DECLARE v_max_active INT DEFAULT 5;
           DECLARE v_message VARCHAR(255);
           SET v_message = CONCAT('You already have ', v_max_active, ' active reservations. Please complete or cancel one before creating a new booking.');

           IF fn_can_book_more(NEW.user_id, v_max_active) = 0 THEN
             SIGNAL SQLSTATE '45000'
               SET MESSAGE_TEXT = v_message;
           END IF;
         END",
        "DROP TRIGGER IF EXISTS trg_appointments_before_update_max_active",
        "CREATE TRIGGER trg_appointments_before_update_max_active
         BEFORE UPDATE ON appointments
         FOR EACH ROW
         BEGIN
           DECLARE v_max_active INT DEFAULT 5;
           DECLARE v_message VARCHAR(255);
           SET v_message = CONCAT('You already have ', v_max_active, ' active reservations. Please complete or cancel one before creating a new booking.');

           IF NEW.user_id <> OLD.user_id OR OLD.status_id <> NEW.status_id THEN
             IF fn_is_active_status(NEW.status_id) = 1 THEN
               IF fn_user_active_booking_count(NEW.user_id) >= v_max_active THEN
                 SIGNAL SQLSTATE '45000'
                   SET MESSAGE_TEXT = v_message;
               END IF;
             END IF;
           END IF;
         END",
    ];

    foreach ($statements as $stmt) {
        $pdo->exec($stmt);
    }

    echo "Booking triggers updated successfully.\n";
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
