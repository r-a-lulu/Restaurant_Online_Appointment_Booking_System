# Database Functions - `restaurant_booking_v1`

**Total: 18 functions** | **Script:** [`functions.sql`](../functions.sql)

---

## Category 1: Status and User Helpers (6 functions)

**Function #1:** `fn_status_name(status_id)`
- Resolves a `status_id` to its `status_name`.

**Function #2:** `fn_status_id_by_name(status_name)`
- Resolves a status name to its `status_id`.

**Function #3:** `fn_is_terminal_status(status_id)`
- Returns `1` for `completed`, `cancelled`, or `no_show`.

**Function #4:** `fn_is_active_status(status_id)`
- Returns `1` for `pending` or `confirmed`.

**Function #5:** `fn_user_active_booking_count(user_id)`
- Counts a user's active reservations.

**Function #6:** `fn_can_book_more(user_id, max_active)`
- Returns `1` if the user has fewer than `max_active` active reservations.

---

## Category 2: Time and Overlap Helpers (2 functions)

**Function #7:** `fn_is_past_datetime(date, time)`
- Returns `1` if the supplied date/time is already in the past.

**Function #8:** `fn_overlaps(start1, end1, start2, end2)`
- Returns `1` when two time ranges overlap.

---

## Category 3: Availability and Capacity (5 functions)

**Function #9:** `fn_table_capacity(table_id)`
- Returns the seating capacity for a table.

**Function #10:** `fn_party_fits_table(table_id, party_size)`
- Returns `1` when the party size fits the selected table.

**Function #11:** `fn_table_has_conflict(table_id, date, start, end, exclude_appt_id)`
- Returns `1` if an overlapping active booking exists for the table.

**Function #12:** `fn_zone_has_conflict(zone_id, date, start, end, exclude_appt_id)`
- Returns `1` if an overlapping active booking exists for the zone.

**Function #13:** `fn_is_slot_available(date, start, end, table_id, zone_id, exclude_appt_id)`
- Returns `1` when the requested slot is available.

---

## Category 4: Pricing (2 functions)

**Function #14:** `fn_appointment_subtotal(appointment_id)`
- Returns the base price plus add-on totals for a reservation.

**Function #15:** `fn_appointment_total(appointment_id)`
- Returns the final reservation total.

---

## Category 5: Reporting (2 functions)

**Function #16:** `fn_daily_booking_count(date)`
- Returns the total number of reservations for a given date.

**Function #17:** `fn_daily_revenue(date)`
- Returns revenue from `confirmed` and `completed` reservations for a given date.

---

## Category 6: Status Flow (1 function)

**Function #18:** `fn_is_valid_status_transition(old_status_id, new_status_id)`
- Returns `1` if the status transition is valid.

---

## Execution Order

1. Run `functions.sql`
2. Then run `triggers.sql`

---

## Verification

```sql
SELECT COUNT(*) AS total_functions
FROM information_schema.routines
WHERE routine_schema = 'restaurant_booking_v1'
  AND routine_type = 'FUNCTION';
-- Expected: 18
```
