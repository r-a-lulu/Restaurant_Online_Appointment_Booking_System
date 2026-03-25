# Database Functions - `restaurant_booking_v1`

**Total: 23 functions** | **Script:** [`functions.sql`](functions.sql)

---

## Category 1: Status and User Helpers (6 functions)

**Function #1:** `fn_status_name(status_id)`
- Resolves a status id to its `status_name` value.

**Function #2:** `fn_is_terminal_status(status_id)`
- Returns 1 if the status is terminal (`completed`, `cancelled`, `no_show`), else 0.

**Function #3:** `fn_is_active_status(status_id)`
- Returns 1 if the status is active (`pending`, `confirmed`), else 0.

**Function #4:** `fn_user_full_name(user_id)`
- Returns `first_name + last_name` for display usage.

**Function #5:** `fn_user_active_booking_count(user_id)`
- Counts active appointments (pending/confirmed) for the given user.

**Function #6:** `fn_can_book_more(user_id, max_active)`
- Returns 1 if the user has fewer than `max_active` active bookings.

---

## Category 2: Time and Overlap Helpers (2 functions)

**Function #7:** `fn_is_past_datetime(date, time)`
- Returns 1 if the given date/time is in the past (server time), else 0.

**Function #8:** `fn_overlaps(start1, end1, start2, end2)`
- Returns 1 if the time ranges overlap, else 0.

---

## Category 3: Availability and Capacity (5 functions)

**Function #9:** `fn_table_capacity(table_id)`
- Returns the seating capacity for a table.

**Function #10:** `fn_party_fits_table(table_id, party_size)`
- Returns 1 if the party size fits the table’s capacity.

**Function #11:** `fn_table_has_conflict(table_id, date, start, end, exclude_appt_id)`
- Returns 1 if an overlapping active booking exists for the table (optional exclusion).

**Function #12:** `fn_zone_has_conflict(zone_id, date, start, end, exclude_appt_id)`
- Returns 1 if an overlapping active booking exists for the zone (optional exclusion).

**Function #13:** `fn_is_slot_available(date, start, end, table_id, zone_id, exclude_appt_id)`
- Returns 1 if the requested slot is free for the table or zone.

---

## Category 4: Pricing (5 functions)

**Function #14:** `fn_service_price(service_id)`
- Returns the price of a service.

**Function #15:** `fn_package_price(package_id)`
- Returns the base price of an event package.

**Function #16:** `fn_add_on_price(add_on_id)`
- Returns the price of an add-on.

**Function #17:** `fn_appointment_subtotal(appointment_id)`
- Returns base price plus add-on totals for a booking.

**Function #18:** `fn_appointment_total(appointment_id)`
- Returns the final total (currently equals subtotal; extend for taxes/fees).

---

## Category 5: Reporting (4 functions)

**Function #19:** `fn_daily_booking_count(date)`
- Returns total appointments for a given date.

**Function #20:** `fn_daily_revenue(date)`
- Sums totals for confirmed/completed appointments on a given date.

**Function #21:** `fn_zone_booking_count(zone_id, date_from, date_to)`
- Counts bookings by zone within a date range.

**Function #22:** `fn_service_booking_count(service_id, date_from, date_to)`
- Counts bookings for a service within a date range.

---

## Category 6: Status Flow (1 function)

**Function #23:** `fn_is_valid_status_transition(old_status_id, new_status_id)`
- Returns 1 if the status transition is valid, else 0.

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
-- Expected: 23
```
