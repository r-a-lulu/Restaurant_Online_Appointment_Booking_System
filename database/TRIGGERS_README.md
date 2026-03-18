# Database Triggers — `restaurant_booking_v1`

**Total: 41 triggers** | **Script:** [`triggers.sql`](triggers.sql)

---

## Category 1: Appointment Audit (3 triggers)

**Trigger #1:** `trg_appointments_after_insert`
- Logs a new appointment into `appointment_audit_logs` with all booking details as JSON.

**Trigger #2:** `trg_appointments_after_update`
- Logs every appointment modification. Marks the action as `STATUS_CHANGE` if only the status changed, or `UPDATE` otherwise. Captures before/after values.

**Trigger #3:** `trg_appointments_after_delete`
- Logs the full appointment record into `appointment_audit_logs` before it is permanently removed.

---

## Category 2: Master-Data & Lookup Audit (21 triggers)

All follow the same pattern: log INSERT/UPDATE/DELETE into `general_audit_logs` with old and new values as JSON.

**Triggers #4–6:** `trg_services_after_insert / update / delete`
- Audits changes to `services` (service_name, price).

**Triggers #7–9:** `trg_event_packages_after_insert / update / delete`
- Audits changes to `event_packages` (package_name, description, base_price).

**Triggers #10–12:** `trg_add_ons_after_insert / update / delete`
- Audits changes to `add_ons` (category, name, description, price).

**Triggers #13–15:** `trg_tables_after_insert / update / delete`
- Audits changes to `tables` (zone_id, table_number, capacity).

**Triggers #16–18:** `trg_dining_zones_after_insert / update / delete`
- Audits changes to `dining_zones` (zone_name).

**Triggers #19–21:** `trg_roles_after_insert / update / delete`
- Audits changes to `roles` (role_name, permissions_description).

**Triggers #22–24:** `trg_appt_status_after_insert / update / delete`
- Audits changes to `appointment_status` (status_name).

---

## Category 3: Auto-Timestamp (2 triggers)

**Trigger #25:** `trg_appointments_before_update_timestamp`
- Auto-sets `appointments.updated_at = NOW()` on every update.

**Trigger #26:** `trg_users_before_update_timestamp`
- Auto-sets `users.updated_at = NOW()` on every update.

---

## Category 4: Business Rule Enforcement (6 triggers)

**Trigger #27:** `trg_appointments_before_insert_capacity`
- Validates that `party_size` does not exceed the booked table's `capacity` on insert.

**Trigger #28:** `trg_appointments_before_update_capacity`
- Re-validates capacity if `party_size` or `table_id` changes on update.

**Trigger #29:** `trg_appointments_before_insert_overlap`
- Prevents double-booking by checking for overlapping time slots on the same table or zone. Excludes cancelled/no-show appointments.

**Trigger #30:** `trg_appointments_before_update_overlap`
- Re-checks for time slot conflicts when date, time, table, or zone changes. Excludes the record itself.

**Trigger #31:** `trg_appointments_before_insert_past_date`
- Blocks creating appointments with a past date or a start time that has already passed today.

**Trigger #32:** `trg_appointments_before_update_past_date`
- Blocks rescheduling appointments to a past date or a start time that has already passed today.

---

## Category 5: User Lifecycle (1 trigger)

**Trigger #33:** `trg_users_before_update_login`
- Updates `users.updated_at` whenever `last_login` changes, keeping the two timestamps in sync.

---

## Category 6: Appointment Add-Ons Audit (3 triggers)

**Trigger #34:** `trg_appt_add_ons_after_insert`
- Logs when an add-on is attached to an appointment (appointment_id, add_on_id, quantity).

**Trigger #35:** `trg_appt_add_ons_after_update`
- Logs when an add-on's quantity is modified on an appointment.

**Trigger #36:** `trg_appt_add_ons_after_delete`
- Logs when an add-on is removed from an appointment.

---

## Category 7: Additional Business Rules (3 triggers)

**Trigger #37:** `trg_appointments_before_update_status_flow`
- Enforces valid status transitions: `pending → confirmed/cancelled`, `confirmed → completed/cancelled/no_show`. Blocks all transitions out of terminal statuses (`completed`, `cancelled`, `no_show`).

**Trigger #38:** `trg_appt_add_ons_before_insert_status_check`
- Prevents adding add-ons to appointments that are `cancelled`, `completed`, or `no_show`.

**Trigger #39:** `trg_appointments_before_delete_guard`
- Prevents deleting confirmed appointments. Must cancel first before deletion.

---

## Category 8: Max Active Bookings (2 triggers)

**Trigger #40:** `trg_appointments_before_insert_max_active`
- Blocks new bookings if the user already has 5 active (pending/confirmed) appointments.

**Trigger #41:** `trg_appointments_before_update_max_active`
- Re-checks the active booking limit when `user_id` or `status_id` changes to an active status. Limit: 5.

---

## Verification

```sql
SELECT COUNT(*) AS total_triggers
FROM information_schema.triggers
WHERE TRIGGER_SCHEMA = 'restaurant_booking_v1';
-- Expected: 41
```
