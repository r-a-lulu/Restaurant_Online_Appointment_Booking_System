# Database Triggers - `restaurant_booking_v1`

**Total: 16 triggers** | **Script:** [`triggers.sql`](../triggers.sql)

---

## Category 1: Auto-Timestamp (2 triggers)

**Trigger #1:** `trg_appointments_before_update_timestamp`
- Sets `appointments.updated_at = NOW()` on every update.

**Trigger #2:** `trg_users_before_update_timestamp`
- Sets `users.updated_at = NOW()` on every update.

---

## Category 2: Booking Validation (8 triggers)

**Trigger #3:** `trg_appointments_before_insert_capacity`
- Prevents inserts where `party_size` exceeds the selected table capacity.

**Trigger #4:** `trg_appointments_before_update_capacity`
- Re-validates table capacity when `party_size` or `table_id` changes.

**Trigger #5:** `trg_appointments_before_insert_overlap`
- Prevents overlapping table bookings on insert.

**Trigger #6:** `trg_appointments_before_update_overlap`
- Prevents overlapping table bookings on update, excluding the current appointment.

**Trigger #7:** `trg_appointments_before_insert_past_date`
- Blocks creating reservations in the past.

**Trigger #8:** `trg_appointments_before_update_past_date`
- Blocks moving reservations to a past date/time.

**Trigger #9:** `trg_appointments_before_insert_service_package`
- Requires exactly one of `service_id` or `event_package_id` on insert.

**Trigger #10:** `trg_appointments_before_update_service_package`
- Requires exactly one of `service_id` or `event_package_id` on update.

---

## Category 3: User Lifecycle (1 trigger)

**Trigger #11:** `trg_users_before_update_login`
- Updates `users.updated_at` when `last_login` changes.

---

## Category 4: Appointment State and Add-On Guards (3 triggers)

**Trigger #12:** `trg_appointments_before_update_status_flow`
- Enforces valid reservation status transitions:
  `pending -> confirmed/cancelled`
  `confirmed -> completed/cancelled/no_show`
- Blocks transitions out of terminal statuses.

**Trigger #13:** `trg_appt_add_ons_before_insert_status_check`
- Prevents adding add-ons to `cancelled`, `completed`, or `no_show` reservations.

**Trigger #14:** `trg_appointments_before_delete_guard`
- Prevents deleting confirmed reservations until they are cancelled first.

---

## Category 5: Max Active Bookings (2 triggers)

**Trigger #15:** `trg_appointments_before_insert_max_active`
- Blocks new bookings when a user already has `12` active reservations (`pending` or `confirmed`).

**Trigger #16:** `trg_appointments_before_update_max_active`
- Re-checks the `12` active reservation limit when `user_id` changes or a reservation is moved into an active status.

---

## Verification

```sql
SELECT COUNT(*) AS total_triggers
FROM information_schema.triggers
WHERE TRIGGER_SCHEMA = 'restaurant_booking_v1';
-- Expected: 16
```
