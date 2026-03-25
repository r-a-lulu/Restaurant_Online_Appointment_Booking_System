# Stored Procedures - `restaurant_booking_v1`

**Total: 36 procedures** | **Script:** [`procedures.sql`](procedures.sql)

---

## Category 1: Users and Auth (6 procedures)

**Procedure #1:** `sp_user_create`
- Creates a user (includes phone) and returns the new `user_id`.

**Procedure #2:** `sp_user_update`
- Updates user profile fields (including phone) and returns affected rows.

**Procedure #3:** `sp_user_deactivate`
- Sets `is_active = FALSE` for a user.

**Procedure #4:** `sp_user_get_by_id`
- Returns full user record for a given `user_id`.

**Procedure #5:** `sp_user_get_by_email`
- Returns full user record for a given email.

**Procedure #6:** `sp_roles_list`
- Returns all roles for admin UI.

---

## Category 2: Services (3 procedures)

**Procedure #7:** `sp_services_create`
- Inserts a new service and returns `service_id`.

**Procedure #8:** `sp_services_update`
- Updates service name/price.

**Procedure #9:** `sp_services_delete`
- Deletes a service by id.

---

## Category 3: Event Packages (3 procedures)

**Procedure #10:** `sp_event_packages_create`
- Inserts a new event package and returns `package_id`.

**Procedure #11:** `sp_event_packages_update`
- Updates event package fields.

**Procedure #12:** `sp_event_packages_delete`
- Deletes an event package by id.

---

## Category 4: Add-Ons (3 procedures)

**Procedure #13:** `sp_add_ons_create`
- Inserts an add-on and returns `add_on_id`.

**Procedure #14:** `sp_add_ons_update`
- Updates add-on fields.

**Procedure #15:** `sp_add_ons_delete`
- Deletes an add-on by id.

---

## Category 5: Dining Zones (3 procedures)

**Procedure #16:** `sp_dining_zones_create`
- Inserts a dining zone and returns `zone_id`.

**Procedure #17:** `sp_dining_zones_update`
- Updates a dining zone name.

**Procedure #18:** `sp_dining_zones_delete`
- Deletes a dining zone by id.

---

## Category 6: Tables (3 procedures)

**Procedure #19:** `sp_tables_create`
- Inserts a table and returns `table_id`.

**Procedure #20:** `sp_tables_update`
- Updates table zone, number, and capacity.

**Procedure #21:** `sp_tables_delete`
- Deletes a table by id.

---

## Category 7: Appointments (6 procedures)

**Procedure #22:** `sp_appointment_create`
- Creates an appointment (includes special requests; triggers enforce capacity, overlap, status flow).

**Procedure #23:** `sp_appointment_update`
- Updates appointment fields (triggers re-validate rules).

**Procedure #24:** `sp_appointment_cancel`
- Sets appointment status to cancelled status id.

**Procedure #25:** `sp_appointment_get_by_id`
- Returns appointment + status name + total amount.

**Procedure #26:** `sp_appointment_list_by_user`
- Lists appointments for a user, newest first.

**Procedure #27:** `sp_appointment_list_admin`
- Lists appointments with admin filters (date/status/zone/service).

---

## Category 8: Appointment Add-Ons (3 procedures)

**Procedure #28:** `sp_appointment_add_on_add`
- Attaches an add-on to an appointment.

**Procedure #29:** `sp_appointment_add_on_update`
- Updates add-on quantity for an appointment.

**Procedure #30:** `sp_appointment_add_on_remove`
- Removes an add-on from an appointment.

---

## Category 9: Reporting and Audit (3 procedures)

**Procedure #31:** `sp_reports_daily_summary`
- Returns daily totals and revenue.

**Procedure #32:** `sp_audit_appointment_log`
- Returns audit history for one appointment.

**Procedure #33:** `sp_audit_general_log`
- Returns audit history for a record in a master/lookup table.

---

## Category 10: Status Utilities (3 procedures)

**Procedure #34:** `sp_status_list`
- Lists available appointment statuses.

**Procedure #35:** `sp_seed_default_statuses`
- Inserts default statuses if missing.

**Procedure #36:** `sp_update_appointment_status`
- Updates an appointment's status by status name (validated).

---

## Execution Order

1. Run `functions.sql`
2. Run `procedures.sql`
3. Run `triggers.sql`

---

## Verification

```sql
SELECT COUNT(*) AS total_procedures
FROM information_schema.routines
WHERE routine_schema = 'restaurant_booking_v1'
  AND routine_type = 'PROCEDURE';
-- Expected: 35
-- Expected: 36
```
