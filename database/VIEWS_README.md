# Database Views - `restaurant_booking_v1`

**Script:** [`views.sql`](views.sql)

---

## View #1: `vw_appointments_detail`
- Full appointment detail with customer info, service/package, zone/table, status name, and total amount.

## View #2: `vw_upcoming_appointments`
- Upcoming appointments only (non-terminal statuses, date >= today).

## View #3: `vw_admin_appointments`
- Admin-ready list of appointments, newest first.

---

## Execution Order

1. Run `functions.sql`
2. Run `views.sql`

---

## Verification

```sql
SELECT COUNT(*) AS total_views
FROM information_schema.views
WHERE table_schema = 'restaurant_booking_v1';
-- Expected: 3
```
