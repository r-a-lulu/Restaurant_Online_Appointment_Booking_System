# Database Security Setup - `restaurant_booking_v1`

**Script:** [`security.sql`](security.sql)

---

## What This Script Does

1. Creates database roles:
   - `app_readonly`
   - `app_readwrite`
   - `app_admin`
   - `app_events`

2. Grants schema-wide permissions per role (short form).

3. Creates a masked PII view:
   - `vw_users_masked` (safe for reporting and non‑production access)

4. Creates DB users and assigns roles:
   - `app_reader`
   - `app_writer`
   - `app_admin`
   - `app_events`

---

## Before You Run

- Replace all placeholder passwords (`REPLACE_ME_*`) in `security.sql`.
- Ensure the server supports roles (MariaDB 10.0+ / MySQL 8.0+).

---

## How To Run

```sql
SOURCE security.sql;
```

---

## Notes

- Password hashing (`bcrypt`) must be done in the application layer.
- Encryption (TLS, disk-at-rest) is infrastructure-level and not defined in SQL.
- For reporting, always use `vw_users_masked` instead of `users`.
