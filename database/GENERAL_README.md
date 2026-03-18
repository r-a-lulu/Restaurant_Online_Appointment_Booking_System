# Database Setup - General Reminders (`restaurant_booking_v1`)

This file summarizes the key things you must remember before running the SQL scripts.

---

## Must-Do Reminders

1. **Replace bcrypt placeholders in `seed.sql`**
   - The user passwords are placeholders like:
     - `REPLACE_WITH_BCRYPT_HASH_ADMIN`
     - `REPLACE_WITH_BCRYPT_HASH_CUST1` … `CUST3`

2. **Replace passwords in `security.sql`**
   - Update `REPLACE_ME_*` before running in production.

3. **Enable the event scheduler**
   - Required for `events.sql` to run:
     ```sql
     SET GLOBAL event_scheduler = ON;
     ```

---

## Recommended Run Order

1. `setup_xampp_sqlyog.sql`
2. `functions.sql`
3. `procedures.sql`
4. `triggers.sql`
5. `events.sql`
6. `security.sql`
7. `seed.sql`

---

## File Notes

- `setup_xampp_sqlyog.sql` already includes:
  - base schema
  - audit tables
  - `updated_at` columns required by triggers
- Triggers, functions, procedures, and events are in separate files by design.

---