# TASK.MD

## Current Task

Track and execute the migration of `pantas-v2.5` toward a separated Attendance + Library architecture and a module-aware role system.

Primary objective:

- Adopt independent Library and Attendance domains.
- Keep Library patrons separate from Attendance patrons.
- Give Library its own independent attendance/visit scanner and logs.
- Keep school-wide Attendance separate and important.
- Add module-aware staff roles with `super_admin` access to all modules.
- Keep `/api/mobile` working by migrating it to the new Library domain tables.

Reference plan:

- `documentation/architecture-migration-plan.md`

## Active Work

- [x] Compare current `pantas-v2.5` behavior with the target separated-domain architecture.
- [x] Identify major feature overlap and architecture gaps.
- [x] Confirm separated Attendance + Library architecture direction.
- [x] Create detailed migration plan.
- [x] Create this task tracker.
- [x] Clarify remaining product/technical decisions before coding.
- [x] Start Milestone 1: auth/module foundation.

## Confirmed Decisions

- Existing `pantas-v2.5` data does not need to be preserved.
- Fresh migrations/schema changes are acceptable.
- Library patrons and Attendance patrons should be independent records.
- Library must have its own attendance/visit logs based on registered Library patrons.
- School Attendance must use separate Attendance patrons and logs.
- Add `super_admin` with access to all modules.
- `/api/mobile` must remain available and be migrated to the new Library domain tables.
- Plan first before implementation.

## Open Questions

- [x] Frontend direction: keep `pantas-v2.5` Blade + Tailwind/Flowbite/daisyUI for Milestone 1.
- [x] Module switching: staff with multiple roles can switch active modules from the application shell.
- [x] User roles: Spatie roles are authoritative; keep `users.role` temporarily as a compatibility bridge during migration.
- [x] Default `super_admin` seed credentials for local development: `super_admin@pantas.test` / `password`.
- [ ] Route naming: preserve current `pantas-v2.5` route names where possible, or switch to new module-prefixed names?
- [x] Milestone 1 scope: auth/module foundation only.

## Milestones

### Milestone 1: Auth And Module Foundation

- [x] Add or standardize Spatie role usage.
- [x] Seed roles:
  - `super_admin`
  - `library_admin`
  - `library_staff`
  - `attendance_admin`
  - `attendance_staff`
- [x] Seed a default `super_admin`.
- [x] Add `users.is_active`.
- [x] Add module access service.
- [x] Add module middleware:
  - `super-admin`
  - `library.access`
  - `library.admin`
  - `attendance.access`
  - `attendance.admin`
- [x] Add role-aware login redirect behavior.
- [x] Add dashboard routes:
  - Super Admin
  - Library Admin
  - Library Staff
  - Attendance Admin
  - Attendance Staff
- [x] Add tests for role access, login redirects, and inactive staff behavior.

### Milestone 2: Fresh Separated Domain Schema

- [x] Create Library domain tables.
- [x] Create Attendance domain tables.
- [x] Create or update shared/system tables.
- [x] Ensure Library attendance logs reference only Library patrons.
- [x] Ensure school Attendance logs reference only Attendance patrons.
- [x] Add migration/schema tests.

### Milestone 3: Route Split

- [ ] Keep `routes/web.php` for public/shared routes.
- [ ] Add `routes/library.php`.
- [ ] Add `routes/attendance.php`.
- [ ] Add `routes/super-admin.php`.
- [ ] Keep `routes/api.php` for `/api/mobile`.
- [ ] Verify public routes still resolve.
- [ ] Verify role-gated routes return expected access behavior.

### Milestone 4: Models, Controllers, And Services

- [ ] Move Library workflows to Library-prefixed models/tables.
- [ ] Add Attendance-prefixed models/tables for school attendance.
- [ ] Keep existing behavior where possible.
- [ ] Use services for circulation, attendance scanning, reports, SMS, and patron lookup where workflow complexity grows.
- [ ] Add relationship and workflow tests.

### Milestone 5: Mobile API Migration

- [ ] Update catalog endpoints to use Library books/e-books.
- [ ] Update borrowing endpoints to use Library circulation logs.
- [ ] Update room endpoints to use Library rooms/reservations.
- [ ] Update feedback endpoints to use Library feedback records.
- [ ] Confirm mobile user/profile linkage to Library patrons.
- [ ] Run existing mobile API tests.
- [ ] Review Pantas-UI API expectations before changing response shapes.

### Milestone 6: Dashboards And Module Switching

- [ ] Build module-aware dashboards.
- [ ] Implement module switching if confirmed.
- [ ] Verify `super_admin` can access all modules.
- [ ] Verify single-module users land on the correct dashboard.
- [ ] Add dashboard/module-switch tests.

### Milestone 7: Library Attendance System

- [ ] Add Library visit scanner.
- [ ] Resolve scans against `library_students` and `library_employees`.
- [ ] Write Library visits to `library_attendance_logs`.
- [ ] Add Library visit logs/reports.
- [ ] Add Library visit feedback/settings.
- [ ] Add tests proving Library visit logs and school Attendance logs remain isolated.

### Milestone 8: Public And Patron Workflows

- [ ] Add `/register` module choice.
- [ ] Add Library student/employee registration.
- [ ] Add Attendance student/employee registration.
- [ ] Add approval flows for both domains.
- [ ] Add OPAC and Library reservation workflows against Library tables.
- [ ] Add Library kiosk lookup.
- [ ] Add patron profile edit requests.
- [ ] Keep public room booking and schedule.
- [ ] Add end-to-end workflow tests.

### Milestone 9: Admin Activity And Audit Trail

- [ ] Add module-aware `admin_activities`.
- [ ] Log staff account changes.
- [ ] Log patron approval/rejection.
- [ ] Log catalog and circulation actions.
- [ ] Log room approval/rejection.
- [ ] Log fine clearance.
- [ ] Add tests for key audit events.

### Milestone 10: Verification And Hardening

- [ ] Create role access tests.
- [ ] Create login redirect tests.
- [ ] Create patron module isolation tests.
- [ ] Create registration flow tests.
- [ ] Create Library kiosk lookup tests.
- [ ] Create OPAC tests.
- [ ] Add Attendance scanner tests.
- [ ] Add Library attendance scanner tests.
- [ ] Keep mobile API tests passing.
- [ ] Run `php artisan route:list`.
- [ ] Run focused PHPUnit tests after each milestone.
- [ ] Run `npm run build` when frontend assets change.
- [ ] Run Pint before finalizing larger PHP changes.

## Backlog

- [ ] Decide whether to keep a compatibility layer for old route names.
- [ ] Decide whether old Blade views should be renamed by domain or reused with new data contracts.
- [ ] Decide whether legacy root static files should be removed later.
- [ ] Review Pantas-UI services before mobile API migration.
- [ ] Add documentation for staff role setup.
- [ ] Add documentation for fresh local seed accounts.
- [ ] Add documentation for Library vs Attendance patron separation.

## Discovered During Planning

- `pantas-v2.5` already has most target Library features.
- The largest gap is architectural separation, not feature count.
- Current `pantas-v2.5` uses shared `students` and `employees` tables.
- Current `pantas-v2.5` has `attendance_logs.student_id` as a string field.
- Current `pantas-v2.5` uses simple `users.role` values such as `admin`, `staff`, `student`, and `faculty`.
- `pantas-v2.5` owns `/api/mobile`, so schema changes must account for Pantas-UI.
- Library attendance and school Attendance must be explicitly isolated in schema, routes, controllers, and tests.
- Spatie permission config/migrations existed, but the package dependency was missing and the existing role migration was not Spatie-compatible.
- The test suite was configured to use the local MySQL database. It now uses SQLite in-memory for safer repeatable tests.
- Full-suite testing still exposes older Breeze/profile/password-reset tests for routes not currently registered by this app.
- Milestone 2 keeps legacy tables in place for compatibility, while adding fresh `library_*` and `attendance_*` domain tables.
- School attendance logs now reference `attendance_students` and `attendance_employees`.
- Library visit logs now live in `library_attendance_logs` and reference `library_students` and `library_employees`.
