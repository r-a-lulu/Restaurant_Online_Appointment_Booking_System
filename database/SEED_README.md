# Seed Data - `restaurant_booking_v1`

**Script:** [`seed.sql`](seed.sql)

---

## What This Seed Includes

1. **Roles**
   - `admin`, `staff`, `guest`

2. **Appointment Statuses**
   - `pending`, `confirmed`, `completed`, `cancelled`, `no_show`

3. **Services**
   - `Table Reservation`, `Private Dining Service`, `Celebration Setup`

4. **Event Packages**
   - `Birthday`, `Anniversary`, `Corporate` packages

5. **Add-ons**
   - Decor, Catering, Tech, Service add-ons

6. **Dining Zones**
   - `Main Dining Room`, `The Patio`, `The Bar`

7. **Tables**
   - Predefined tables linked to the seeded zones

8. **Users**
   - 1 admin account
   - 3 guest accounts

---

## Important Notes

- Replace all placeholder bcrypt hashes in `seed.sql` before running.
- This seed is **idempotent**: uses `ON DUPLICATE KEY UPDATE` to avoid duplicates.

---

## How To Run

```sql
SOURCE seed.sql;
```
