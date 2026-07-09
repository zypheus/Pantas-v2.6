# PANTAS Architecture Migration Plan

## Goal

Adopt a separated Attendance + Library architecture and a module-aware role system inside `pantas-v2.5`, while keeping `pantas-v2.5` as the mobile API owner for Pantas-UI.

The migration is allowed to use fresh database structure. Existing `pantas-v2.5` data does not need to be preserved.

## Confirmed Decisions

- `pantas-v2.5` should adopt a separated domain architecture.
- Library patrons and Attendance patrons should be independent records.
- Library must have its own independent attendance/visit system using registered Library patrons.
- School-wide Attendance must remain separate from Library attendance and is important.
- Fresh migrations/schema changes are acceptable.
- Add a `super_admin` role with access to all modules.
- Keep `/api/mobile` working, but migrate it to the new Library domain tables.
- Plan first before implementation.

## Key Architecture Rule

Library activity and school attendance must never share the same patron/log tables.

Library module:

- Uses Library patrons.
- Owns OPAC, catalog, circulation, rooms, fines, repository, library feedback, and library visit logs.
- Library visit scanner logs to `library_attendance_logs`.

Attendance module:

- Uses Attendance patrons.
- Owns school attendance scanning, school attendance logs, attendance reports, attendance feedback, and attendance settings.
- School attendance scanner logs to `attendance_logs`.

Shared system:

- Uses shared staff users.
- Uses module-aware roles.
- Allows `super_admin` to access all modules.

## Current pantas-v2.5 State

`pantas-v2.5` is currently a library-first Laravel app with some attendance features.

Already present:

- Catalog and MARC tools
- Books, copies, archive, trash
- E-books
- OPAC
- Checkout and circulation logs
- Fines and fine clearance
- Students and employees
- Pending patron registration
- Student profile edit requests
- ID cards
- Room reservations and approvals
- Attendance scanner and attendance reports
- Feedback and attendance feedback
- SMS tools
- File repository
- Staff/user management
- Mobile API under `/api/mobile`

Important current limitations:

- Uses shared `students` and `employees` tables instead of separate Library and Attendance patron tables.
- `attendance_logs` currently stores a string `student_id`, rather than referencing separate Attendance patron records.
- Uses simple `users.role` values such as `admin`, `staff`, `student`, and `faculty`.
- Does not have a complete module split for routes, roles, dashboards, and middleware.
- Does not clearly separate school attendance from library visit attendance.

## Target Roles

Create or migrate to module-aware staff roles:

- `super_admin`
- `library_admin`
- `library_staff`
- `attendance_admin`
- `attendance_staff`

Expected access:

- `super_admin`: all modules and staff management.
- `library_admin`: all Library module administration.
- `library_staff`: Library staff workflows without admin-only controls.
- `attendance_admin`: all Attendance module administration.
- `attendance_staff`: Attendance operational workflows without admin-only controls.

Open decision:

- Confirm whether `users.role` should be removed entirely in favor of Spatie roles, or kept temporarily for compatibility during migration.

## Phase 1: Foundation

Build the shared authorization and module foundation.

Tasks:

- Add or standardize Spatie permission roles.
- Add seeders for the target roles.
- Add a default `super_admin` seed account.
- Add `users.is_active`.
- Add a module access service that centralizes role and module checks.
- Add middleware:
  - `super-admin`
  - `library.access`
  - `library.admin`
  - `attendance.access`
  - `attendance.admin`
- Add login redirect behavior based on role/module access.
- Add dashboard routes for:
  - Super Admin
  - Library Admin
  - Library Staff
  - Attendance Admin
  - Attendance Staff

Verification:

- Feature tests for role access.
- Feature tests for login redirects.
- Feature tests ensuring inactive users cannot proceed.

## Phase 2: Fresh Domain Schema

Create separated Library and Attendance schema.

Recommended Library tables:

- `library_roles`
- `library_programs`
- `library_program_courses`
- `library_catalog_frameworks`
- `library_marc_fields`
- `library_catalog_framework_fields`
- `library_pending_students`
- `library_students`
- `library_pending_employees`
- `library_employees`
- `library_settings`
- `library_holidays`
- `library_fine_settings`
- `library_books`
- `library_book_marc_fields`
- `library_book_program`
- `library_book_logs`
- `library_book_reservations`
- `library_ebooks`
- `library_rooms`
- `library_room_reservations`
- `library_prospectuses`
- `library_student_edit_requests`
- `library_employee_edit_requests`
- `library_reservation_students`
- `library_reservation_logs`
- `library_files`
- `library_attendance_settings`
- `library_attendance_feedbacks`
- `library_attendance_videos`
- `library_attendance_logs`

Recommended Attendance tables:

- `attendance_programs`
- `attendance_program_years`
- `attendance_program_courses`
- `attendance_pending_students`
- `attendance_students`
- `attendance_pending_employees`
- `attendance_employees`
- `attendance_settings`
- `attendance_logs`
- `attendance_feedback`

Shared/system tables:

- `users`
- Spatie permission tables
- `admin_activities`
- jobs/cache/session/password reset tables
- Sanctum personal access tokens

Verification:

- Migration test on a fresh database.
- Schema assertions for key foreign keys.
- Tests proving `attendance_logs` does not reference Library patrons.
- Tests proving `library_attendance_logs` does not reference Attendance patrons.

## Phase 3: Route Split

Split the current large `routes/web.php` into module route files.

Recommended route files:

- `routes/web.php`: public/shared routes, login, logout, dashboard redirect, account profile.
- `routes/library.php`: Library routes.
- `routes/attendance.php`: Attendance routes.
- `routes/super-admin.php`: staff/system administration routes.
- `routes/api.php`: mobile API under `/api/mobile`.

Expected public routes:

- `/`
- `/login`
- `/logout`
- `/dashboard`
- `/register`
- `/register/library`
- `/register/library/employee`
- `/register/attendance`
- `/register/attendance/employee`
- `/attendance`
- `/opac`
- `/kiosk/scan`
- `/rooms/book`
- `/rooms/schedule`

Verification:

- Route list review.
- Existing public links still resolve.
- Role-gated routes return expected 403 or redirect behavior.

## Phase 4: Models, Controllers, And Services

Port the current `pantas-v2.5` functionality into the separated domain model.

Recommended approach:

- Use the target domain rules in this document as the implementation reference.
- Preserve current `pantas-v2.5` behavior where possible.
- Avoid blindly copying UI if the frontend decision remains Blade/daisyUI.
- Prefer services for circulation, attendance scanning, SMS, reports, and patron lookup.

Likely model mapping:

- `Student` -> `LibraryStudent`
- `Employee` -> `LibraryEmployee`
- `PendingStudent` -> `LibraryPendingStudent`
- `PendingEmployee` -> `LibraryPendingEmployee`
- `Book` -> `LibraryBook`
- `BookLog` -> `LibraryBookLog`
- `Room` -> `LibraryRoom`
- `RoomReservation` -> `LibraryRoomReservation`
- `AttendanceLog` -> either `AttendanceLog` for school attendance or `LibraryAttendanceLog` for library visits, depending on workflow.

New Attendance models:

- `AttendanceStudent`
- `AttendanceEmployee`
- `AttendancePendingStudent`
- `AttendancePendingEmployee`
- `AttendanceLog`
- `AttendanceSetting`
- `AttendanceFeedback`

Verification:

- Unit or feature tests around model relationships.
- Tests for circulation workflows.
- Tests for attendance scanner workflows.
- Tests for library visit scanner workflows.

## Phase 5: Mobile API Migration

Keep `/api/mobile` available while changing its backend data source to the Library domain.

Expected mobile API ownership:

- Catalog uses `library_books` and `library_ebooks`.
- Borrowing uses `library_book_logs`.
- Room reservations use `library_rooms` and `library_room_reservations`.
- Feedback uses Library feedback records.
- Mobile user profile links to Library patron records.

Risks:

- Pantas-UI depends on `/api/mobile`.
- Response shape changes can break the mobile app.
- Authentication and patron linkage must be handled carefully.

Verification:

- Existing mobile API feature tests.
- New tests for the migrated data sources.
- Manual contract review against Pantas-UI service code.

## Phase 6: Dashboards And Module Switching

Create module-aware dashboards and navigation.

Dashboards:

- `/dashboard/super-admin`
- `/dashboard/library-admin`
- `/dashboard/library-staff`
- `/dashboard/attendance-admin`
- `/dashboard/attendance-staff`

Recommended behavior:

- `super_admin` defaults to Super Admin dashboard.
- Attendance-only users default to Attendance dashboard.
- Library-only users default to Library dashboard.
- Users with multiple module roles can switch active modules if module switching is confirmed.

Open decision:

- Confirm whether staff with multiple module roles should switch active modules from the application shell.

Verification:

- Login redirect tests.
- Module-switch authorization tests.
- Dashboard access tests.

## Phase 7: Library Attendance System

Implement a Library-specific attendance/visit system.

Required behavior:

- Library scanner resolves `library_students` and `library_employees`.
- Library scanner writes to `library_attendance_logs`.
- Library visit reports use only Library visit logs.
- Library visit feedback/settings are independent from school attendance feedback/settings.
- Library visit scanner must not write to `attendance_logs`.

Likely routes:

- `/library/attendance/scanner`
- `/library/attendance/logs`
- `/library/attendance/logs/reports`
- `/library/attendance/feedback`
- `/library/attendance/logout-feedback`
- `/library/attendance/video`

Verification:

- Tests that Library patrons can scan into Library attendance.
- Tests that Attendance patrons cannot accidentally be logged as Library patrons unless separately registered.
- Tests that Library logs and school Attendance logs remain isolated.

## Phase 8: Public And Patron Workflows

Rebuild public workflows around the separated domains.

Registration:

- `/register` shows module choice.
- Library registration creates pending Library patrons.
- Attendance registration creates pending Attendance patrons.
- Approvals move records into the correct active domain table.

Library self-service:

- OPAC search and book detail.
- Book reservation.
- Library kiosk patron lookup.
- Patron profile request.
- Room booking and schedule.

Attendance self-service:

- School attendance scan.
- Optional section picker.
- Attendance feedback.

Verification:

- Registration flow tests.
- OPAC tests.
- Kiosk lookup tests.
- Profile edit request tests.
- Room booking tests.
- Attendance scan tests.

## Phase 9: Admin Activity And Audit Trail

Add module-aware admin activity logging.

Recommended table:

- `admin_activities`

Recommended fields:

- `user_id`
- `module`
- `type`
- `title`
- `body`
- `action_url`
- `icon`
- polymorphic subject fields
- timestamps

Use cases:

- Staff account changes.
- Patron approvals/rejections.
- Catalog changes.
- Circulation actions.
- Room approvals/rejections.
- Fine clearances.

Verification:

- Tests for key activity events.
- Tests that module-specific activities can be filtered.

## Phase 10: Test Plan

Create or adapt tests for the new architecture:

- Role access tests
- Login redirect tests
- Staff user management tests
- Patron module isolation tests
- Registration flow tests
- Library kiosk lookup tests
- OPAC tests
- Library navigation/status tests
- Attendance scanner tests
- Library attendance scanner tests
- Mobile API tests

Minimum quality checks after each implementation milestone:

- `php artisan test --filter=RelevantTest`
- `php artisan route:list`
- `php -l path/to/changed/file.php`
- `npm run build` when frontend assets change
- `./vendor/bin/pint` before finalizing larger PHP changes

## Recommended First Milestone

Start with the foundation instead of jumping directly into feature screens.

Milestone 1 scope:

- Fresh role seeders.
- `super_admin` account.
- Module access service.
- Module middleware.
- `users.is_active`.
- Dashboard routes and redirects.
- Route split scaffolding.
- Initial tests for role/module access.

Why this first:

- Every later feature depends on correct module boundaries.
- It gives a safe way to verify access before moving patron data and workflows.
- It reduces risk when migrating the mobile API and attendance workflows.

## Open Questions

These must be clarified before coding starts:

1. Frontend direction: should `pantas-v2.5` keep Blade + Tailwind/Flowbite/daisyUI, or should it migrate to a new frontend architecture?
2. Module switching: should staff with multiple roles switch active modules from the application shell?
3. Should `users.role` be removed entirely, or kept temporarily during migration?
4. What default `super_admin` credentials should be seeded for local development?
5. Should route names preserve current `pantas-v2.5` names where possible, or can they be changed to new module-prefixed names?
6. Should the first implementation milestone include only auth/module foundation, or also the first separated domain tables?
