# Database Events - `restaurant_booking_v1`

**Total: 5 events** | **Script:** [`events.sql`](events.sql)

---

## Event #1: `ev_auto_cancel_past_pending`
- Auto-cancels pending appointments that are in the past.
- Runs every 15 minutes.

## Event #2: `ev_mark_no_show_confirmed`
- Marks confirmed appointments as `no_show` after end time.
- Runs every 30 minutes.

## Event #3: `ev_purge_appointment_audit_logs`
- Deletes appointment audit logs older than 365 days.
- Runs daily at 02:00.

## Event #4: `ev_purge_general_audit_logs`
- Deletes general audit logs older than 365 days.
- Runs daily at 02:05.

## Event #5: `ev_purge_user_audit_logs`
- Deletes user audit logs older than 365 days.
- Runs daily at 02:10.

---

## Execution Order

1. Run `functions.sql`
2. Run `procedures.sql`
3. Run `triggers.sql`
4. Run `events.sql`

---

## Verification

```sql
SELECT COUNT(*) AS total_events
FROM information_schema.events
WHERE event_schema = 'restaurant_booking_v1';
-- Expected: 5
```
