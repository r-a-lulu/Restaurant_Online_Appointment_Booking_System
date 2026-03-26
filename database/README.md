# Database Files

This folder is now organized into two parts:

- Core runtime SQL in the root
- Documentation in `support/`
- Archived non-core objects in `archive/`

## Core Files

- `functions.sql` - Stored functions used by triggers, procedures, and app queries
- `procedures.sql` - Stored procedures used by the app
- `triggers.sql` - Business rules and audit triggers
- `views.sql` - Read-only views used by the app and reporting
- `events.sql` - Scheduled maintenance jobs
- `schema.sql` - Base tables, indexes, and required schema additions
- `seed.sql` - Sample data for local setup
- `security.sql` - Grants and masked reporting setup

## Recommended Load Order

1. `schema.sql`
2. `functions.sql`
3. `views.sql`
4. `procedures.sql`
5. `triggers.sql`
6. `events.sql`
7. `security.sql` if you want the role/grant setup
8. `seed.sql` if you want sample records

## Notes

- The root files are kept to the runnable database set.
- Archived or optional database objects are stored in `archive/`.
- Readme files stay in `support/`.
- If you want a full fresh install, run the schema first, then the module files, then seed and security if needed.
