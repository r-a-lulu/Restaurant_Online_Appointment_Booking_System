# Database Setup - General Reminders (`restaurant_booking_v1`)

This file summarizes the key things you must remember before running the SQL scripts.

---

## Must-Do Reminders

1. **Replace bcrypt placeholders in `seed.sql`**
   - The user passwords are placeholders like:
     - `REPLACE_WITH_BCRYPT_HASH_ADMIN`
     - `REPLACE_WITH_BCRYPT_HASH_CUST1` through `CUST3`

2. **Replace passwords in `security.sql`**
   - Update `REPLACE_ME_*` before running in production.

3. **Enable the event scheduler**
   - Required for `events.sql` to run:
     ```sql
     SET GLOBAL event_scheduler = ON;
     ```

---

## Recommended Run Order

1. `schema.sql`
2. `functions.sql`
3. `views.sql`
4. `procedures.sql`
5. `triggers.sql`
6. `events.sql`
7. `security.sql`
8. `seed.sql`

---

## File Notes

- `schema.sql` already includes:
  - base schema
  - audit tables
  - `updated_at` columns required by triggers
- Triggers, functions, procedures, views, and events are in separate files by design.

---

If you want this converted into a checklist or a single "master setup" script, say the word.
