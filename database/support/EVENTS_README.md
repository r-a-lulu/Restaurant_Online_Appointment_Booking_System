# Database Events - `restaurant_booking_v1`

**Total: 4 events** | **Script:** [`events.sql`](../events.sql)

---

## Event #1: `ev_auto_cancel_past_pending`
- Auto-cancels pending reservations that are already in the past.
- Runs every 15 minutes.

## Event #2: `ev_auto_complete_finished_confirmed`
- Marks confirmed reservations as `completed` after their end time passes.
- Runs every 30 minutes.
- This is the automatic path that moves finished reservations into guest history.
- `no_show` should remain a staff-driven attendance decision, not an automatic event.

## Event #3: `ev_purge_appointment_audit_logs`
- Deletes rows from `appointment_audit_logs` older than 365 days.
- Runs daily at 02:00.

## Event #4: `ev_purge_general_audit_logs`
- Deletes rows from `general_audit_logs` older than 365 days.
- Runs daily at 02:05.

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
-- Expected: 4
```
