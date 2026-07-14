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

- [ ] Start Milestone 12: Developer-managed login modal customization.
- [x] Start Milestone 11: isolated Developer branding role and system-wide `pantas-default` customization.
- [x] Compare current `pantas-v2.5` behavior with the target separated-domain architecture.
- [x] Identify major feature overlap and architecture gaps.
- [x] Confirm separated Attendance + Library architecture direction.
- [x] Create detailed migration plan.
- [x] Create this task tracker.
- [x] Clarify remaining product/technical decisions before coding.
- [x] Start Milestone 1: auth/module foundation.
- [x] Start Milestone 3: route split.
- [x] Complete Milestone 3: route split.
- [x] Start Milestone 4: separated domain model foundation.
- [x] Complete Milestone 4: models/controllers/services foundation.
- [x] Complete Milestone 5: mobile API migration to Library domain tables.
- [x] Complete Milestone 6: dashboards and module switching.
- [x] Complete Milestone 7: Library attendance system.
- [x] Complete Milestone 8: public and patron workflow split.
- [x] Complete Milestone 9: admin activity/audit trail foundation.
- [x] Complete Milestone 10: focused verification and hardening pass.
- [x] Review Pantas-UI services against `/api/mobile` response contracts.
- [x] Add operational documentation for staff roles, seed accounts, and patron domain separation.
- [x] Align Super Admin navigation with active module switching.
- [x] Implement workflow-grouped sidebar navigation and role-specific dashboard pages.
- [x] Add richer demo workflow seed data for manual testing.

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
- [x] Route naming: preserve current `pantas-v2.5` route names where possible; use module-prefixed names for new module-only routes.
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

- [x] Keep `routes/web.php` for public/shared routes.
- [x] Add `routes/library.php`.
- [x] Add `routes/attendance.php`.
- [x] Add `routes/super-admin.php`.
- [x] Keep `routes/api.php` for `/api/mobile`.
- [x] Verify public routes still resolve.
- [x] Verify role-gated routes return expected access behavior.

### Milestone 4: Models, Controllers, And Services

- [x] Move Library workflows to Library-prefixed models/tables.
- [x] Add Attendance-prefixed models/tables for school attendance.
- [x] Keep existing behavior where possible for route access and school attendance scanning.
- [x] Use services for attendance scanning and attendance reports where workflow complexity grows.
- [x] Add relationship and workflow tests for route split, Attendance scanner workflow, Attendance domain models, and Library domain model relationships.

### Milestone 5: Mobile API Migration

- [x] Update catalog endpoints to use Library books/e-books.
- [x] Update borrowing endpoints to use Library circulation logs.
- [x] Update room endpoints to use Library rooms/reservations.
- [x] Update feedback endpoints to use Library feedback records.
- [x] Confirm mobile user/profile linkage to Library patrons.
- [x] Run existing mobile API tests.
- [x] Review Pantas-UI API expectations before changing response shapes.

### Milestone 6: Dashboards And Module Switching

- [x] Build module-aware dashboards.
- [x] Implement module switching if confirmed.
- [x] Verify `super_admin` can access all modules.
- [x] Verify single-module users land on the correct dashboard.
- [x] Add dashboard/module-switch tests.
- [x] Add role-specific dashboard metrics, quick actions, recent activity, and charts.

### Milestone 7: Library Attendance System

- [x] Add Library visit scanner.
- [x] Resolve scans against `library_students` and `library_employees`.
- [x] Write Library visits to `library_attendance_logs`.
- [x] Add Library visit logs/reports.
- [x] Add Library visit feedback/settings.
- [x] Add tests proving Library visit logs and school Attendance logs remain isolated.

### Milestone 8: Public And Patron Workflows

- [x] Add `/register` module choice.
- [x] Add Library student/employee registration.
- [x] Add Attendance student/employee registration.
- [x] Add approval flows for both domains.
- [x] Add OPAC and Library reservation workflows against Library tables.
- [x] Add Library kiosk lookup.
- [x] Add patron profile edit requests.
- [x] Keep public room booking and schedule.
- [x] Add end-to-end workflow tests.

### Milestone 9: Admin Activity And Audit Trail

- [x] Add module-aware `admin_activities`.
- [x] Log staff account changes.
- [x] Log patron approval/rejection.
- [x] Log catalog actions.
- [x] Log room approval/rejection.
- [x] Log fine clearance.
- [x] Add tests for key audit events.

### Milestone 10: Verification And Hardening

- [x] Create role access tests.
- [x] Create login redirect tests.
- [x] Create patron module isolation tests.
- [x] Create registration flow tests.
- [x] Create Library kiosk lookup tests.
- [x] Create OPAC tests.
- [x] Add Attendance scanner tests.
- [x] Add Library attendance scanner tests.
- [x] Keep mobile API tests passing.
- [x] Run `php artisan route:list`.
- [x] Run focused PHPUnit tests after each milestone.
- [x] Run `npm run build` when frontend assets change.
- [x] Run Pint before finalizing larger PHP changes.

### Milestone 11: Developer Branding Role

Plan reference: `documentation/developer-branding-plan.md`

#### Task 1: Role And Access Isolation

- [x] Add the `developer` Spatie role and compatibility role handling.
- [x] Add exact-role `developer` middleware with no Super Admin bypass.
- [x] Add Developer login redirect and dashboard route.
- [x] Prevent Developer access to Super Admin, Library, and Attendance staff modules.
- [x] Ensure Super Admin and all other roles receive `403` on `/developer/*`.
- [x] Limit Developer navigation to Dashboard, Branding Settings, Profile, and Logout.
- [x] Hide Developer navigation from Super Admin and all other roles.
- [x] Remove the Library/Attendance module switcher from the Developer shell.

#### Task 2: Branding Defaults And Persistence

- [x] Define permanent original asset paths and colors in `config/branding.php`.
- [x] Create the `branding_settings` migration with nullable override fields and `updated_by`.
- [x] Add the `BrandingSetting` model and updater relationship.
- [x] Create `BrandingService` to merge defaults and overrides.
- [x] Add active-branding caching and cache invalidation.
- [x] Add missing-custom-file fallback to original assets.

#### Task 3: Developer Routes And Interface

- [x] Create and load `routes/developer.php`.
- [x] Add Developer dashboard controller and view.
- [x] Add Branding Settings controller, Form Request, and view.
- [x] Show current and original banner/logo previews.
- [x] Add color pickers and `#RRGGBB` inputs with current/original values.
- [x] Add live banner, sidebar, and palette preview.
- [x] Add Save Changes, per-field restore, and Restore to Default actions.
- [x] Add a confirmation dialog for full restoration.

#### Task 4: Upload And Color Safety

- [x] Validate banner and logo MIME types, sizes, dimensions, and image decoding.
- [x] Store generated filenames under public branding storage directories.
- [x] Make image replacement safe so the old custom file is deleted only after success.
- [x] Prevent original Pantas assets from being overwritten or deleted.
- [x] Validate and normalize colors to uppercase six-digit `#RRGGBB` values.
- [x] Reject CSS expressions, URLs, variables, named colors, alpha colors, and incomplete values.

#### Task 5: Application Branding Integration

- [x] Share active branding with Blade layouts through one centralized integration point.
- [x] Expose validated active colors as CSS custom properties.
- [x] Connect customizable colors only to `pantas-default`.
- [x] Preserve all other themes and per-user theme preferences.
- [x] Replace relevant hard-coded banner references in shared layouts.
- [x] Replace relevant hard-coded sidebar/application logo references.
- [x] Apply branding consistently to authenticated, public, guest, Library, Attendance, and scanner layouts.

#### Task 6: Restoration And Audit

- [x] Implement transactional full Restore to Default.
- [x] Implement partial restore for individual assets and colors.
- [x] Clear custom overrides and return to configuration defaults.
- [x] Delete only obsolete custom files after a successful restore.
- [x] Clear branding cache after update or restoration.
- [x] Log branding updates, previous/new values, and file paths.
- [x] Log full and partial restoration with the acting Developer.
- [x] Restrict Developer activity display to branding-related records.

#### Task 7: Tests And Verification

- [x] Test Developer-only route access and exact-role enforcement.
- [x] Test that Super Admin and other roles receive `403` on Developer routes.
- [x] Test that Developer receives `403` on other staff modules.
- [x] Test Developer login redirect, isolated navigation, and hidden module switcher.
- [x] Test valid and invalid image uploads, limits, and safe replacement.
- [x] Test valid and invalid color values and normalization.
- [x] Test `pantas-default` customization and isolation from other themes.
- [x] Test full and partial restoration of assets and colors.
- [x] Test that original Pantas assets are never deleted.
- [x] Test missing custom-file fallback and cache invalidation.
- [x] Test branding update and restoration activity records.
- [x] Run focused Branding, Developer Access, Theme Preference, and Module Access tests.
- [x] Run `php artisan route:list`, `npm run build`, and Pint.
- [ ] Visually verify responsive layouts, theme isolation, restoration, fallback, and color contrast.

### Milestone 12: Developer-Managed Login Modal

Goal: allow the isolated Developer role to customize the public login modal from Branding Settings without changing authentication, registration, or role-redirect behavior.

#### Task 1: Customization Scope And Defaults

- [ ] Add permanent defaults for the login modal logo, welcome label, portal name, description, sign-in heading, and field placeholders.
- [ ] Add permanent defaults for the left-panel background, modal background, modal text, and sign-in button colors.
- [ ] Keep Attendance and Library registration wording and workflows unchanged unless separately approved.
- [ ] Ensure every new setting falls back safely to its original Pantas value.

#### Task 2: Persistence And Branding Service

- [ ] Add nullable login-modal override fields to `branding_settings` through a new migration.
- [ ] Add the new fields to `BrandingSetting`, `config/branding.php`, and `BrandingService`.
- [ ] Add the login modal logo to centralized asset URL, upload, replacement, missing-file fallback, and cleanup handling.
- [ ] Include the new settings in branding caching and cache invalidation.
- [ ] Include the new settings in individual and full restoration.
- [ ] Record login-modal changes and restorations in the existing branding activity log.

#### Task 3: Validation And Upload Safety

- [ ] Add text length limits and normalize submitted modal text.
- [ ] Validate colors as strict uppercase six-digit `#RRGGBB` values.
- [ ] Reject CSS expressions, URLs, variables, named colors, alpha colors, and incomplete color values.
- [ ] Validate the modal logo MIME type, file size, dimensions, and image decoding.
- [ ] Store the logo under generated public branding storage paths.
- [ ] Delete an old custom logo only after its replacement or restoration succeeds.
- [ ] Ensure original Pantas assets can never be overwritten or deleted.

#### Task 4: Developer Branding Interface

- [ ] Add a dedicated Login Modal section to Developer Branding Settings.
- [ ] Add text inputs, color controls, and a modal logo uploader with current and original values.
- [ ] Add a responsive live preview for desktop and mobile modal layouts.
- [ ] Preserve submitted values and show validation errors when saving fails.
- [ ] Add per-field restore actions and a Restore Login Modal Defaults action.
- [ ] Include the new fields in the existing full Restore to Default action and confirmation flow.

#### Task 5: Public Modal Integration

- [ ] Replace hard-coded login logo and text in `resources/views/partials/auth-modal.blade.php` with active branding values.
- [ ] Expose validated modal colors through CSS custom properties scoped to the authentication modal.
- [ ] Update `public/css/auth-modal.css` to consume the scoped variables without affecting other application surfaces or themes.
- [ ] Update `public/js/auth-modal.js` so Login, Attendance Registration, and Library Registration switching does not overwrite customized login content.
- [ ] Preserve modal opening, closing, keyboard, focus, error, session-expiry, and responsive behavior.
- [ ] Keep the login form action, CSRF protection, remember-me option, validation, inactive-account handling, and role-aware redirects unchanged.

#### Task 6: Direct Login Consistency

- [ ] Replace or adapt the legacy `/login` page so it uses the same branded modal experience as the landing page.
- [ ] Preserve direct `/login` access for password reset, expired sessions, authentication errors, and bookmarked links.
- [ ] Verify the modal automatically opens with the correct login view and retains old email/remember values after validation errors.

#### Task 7: Tests And Verification

- [ ] Test default login-modal values and customized rendering.
- [ ] Test exact Developer-only update and restore authorization.
- [ ] Test valid and invalid modal logo uploads, text limits, and color values.
- [ ] Test safe logo replacement, missing-file fallback, and protection of original assets.
- [ ] Test individual login-modal restoration and full branding restoration.
- [ ] Test branding activity records for login-modal updates and restorations.
- [ ] Test the landing-page modal and direct `/login` page for consistent customized values.
- [ ] Keep existing authentication, inactive-account, registration, modal, and role-redirect tests passing.
- [ ] Run focused `DeveloperBrandingSettingsTest`, `AuthenticationTest`, and `PublicRegistrationSplitTest` coverage.
- [ ] Run `php artisan route:list`, `npm run build`, Pint, and responsive visual verification.

#### Acceptance Criteria

- [ ] A Developer can customize and restore the login modal entirely from Branding Settings.
- [ ] Customized values appear on both the landing-page modal and direct `/login` experience.
- [ ] Attendance and Library registration content and behavior remain unchanged.
- [ ] Authentication security, validation, account status checks, and role-based redirects remain unchanged.
- [ ] Invalid or missing custom values safely fall back to permanent Pantas defaults.

## Backlog

- [ ] Decide whether to keep a compatibility layer for old route names.
- [ ] Decide whether old Blade views should be renamed by domain or reused with new data contracts.
- [ ] Decide whether legacy root static files should be removed later.
- [ ] Decide whether Attendance patron management should expand beyond the new pending-registration index into full student/employee index pages.
- [x] Add demo seed coverage for dashboards, Attendance patrons/logs, Library visits, circulation, room reservations, feedback, pending approvals, and audit activity.
- [x] Review Pantas-UI services before mobile API migration.
- [x] Add documentation for staff role setup.
- [x] Add documentation for fresh local seed accounts.
- [x] Add documentation for Library vs Attendance patron separation.

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
- Milestone 3 moved public/shared routes into `web.php`, Library routes into `routes/library.php`, Attendance routes into `routes/attendance.php`, and staff-management routes into `routes/super-admin.php`.
- Existing route names are preserved where possible so current Blade links and redirects continue to resolve.
- Milestone 4 now has separated Eloquent models for the new `attendance_*` and `library_*` tables.
- School attendance scanner, logs, reports, and feedback now use `AttendanceStudent` / `AttendanceEmployee` domain tables.
- Existing Library workflow model names now point to `library_*` tables so controllers keep their current route/view contracts while storage moves into the separated Library domain.
- Library validation rules and raw query joins now use Library-prefixed tables for books, circulation, programs, rooms, reservations, files, fines, holidays, patrons, e-books, and mobile feedback.
- The Library schema includes compatibility columns for current cataloging, prospectus, room reservation, file repository, fine, holiday, and e-book workflows.
- Mobile API catalog, borrowing, room reservation, and feedback flows now read/write Library domain tables while preserving response shapes.
- Dashboard navigation is module-aware: Attendance-only staff no longer see Library navigation, Library-only staff no longer see Attendance navigation, and Super Admin keeps system/module access.
- Library attendance now has its own public scanner, feedback endpoint, admin logs, reports, and feedback settings, all backed by `library_attendance_*` tables.
- Library scanner lookup accepts Library patron QR codes and ID numbers only; Attendance-only patrons are rejected by the Library scanner.
- Public `/register` now routes users to a module choice page; `/register/library` keeps Library pending registration and `/register/attendance` writes to Attendance pending registration.
- Attendance patron approval moves records from `attendance_pending_*` tables into `attendance_*` patron tables and writes module-aware activity logs.
- Admin activities now capture module, type, actor, optional subject, body, action URL, and icon. Staff changes, patron approval/rejection, catalog changes, room approval/rejection, and fine clearance write audit events.
- Laravel's default table naming required an explicit `$table` on `LibraryAttendanceFeedback` because the migration uses `library_attendance_feedbacks`.
- Focused M7-M10 tests were added for Library attendance isolation, public registration split, audit activity, and public Library workflow surfaces.
- Pantas-UI service endpoints still match the `/api/mobile` route/controller contracts after the backend moved mobile data sources to Library-domain tables.
- Mobile registration currently appears UI-local in Pantas-UI; there is no mobile registration API submission yet.
- Pantas-UI profile can parse QR fields, but `/api/mobile/profile` does not currently return a QR code field; the current profile screen only displays a placeholder QR icon plus student number.
- Pantas-UI `ApiConfig.androidEmulator` uses `127.0.0.1`; normal Android emulator testing may need `10.0.2.2` or an active tunnel/reverse proxy.
- Super Admin access remains cross-module, but sidebar navigation now shows only the active module menu: System, Library, or Attendance.
- Sidebar navigation is now grouped by workflow per active module. Staff sidebars intentionally show operational routes only, while admin/report/settings links remain admin-only.
- Role dashboards now render summary cards, quick actions, recent activity, and Chart.js-powered charts using existing Blade and Bootstrap styling.
- A read-only Super Admin activity index was added so the System Activity sidebar has a concrete audit-trail destination.
- Attendance Admin now has a read-only pending-registration page with existing approval actions; full Attendance student/employee management remains a future product decision.
- `DemoWorkflowSeeder` now fills manual-testing gaps left by the base sample seeders, including role accounts, dashboard metrics, Library circulation, Library visits, school Attendance records, pending approvals, room workflow examples, feedback, and audit activity.
- Developer Task 1 added an exact-role isolated dashboard and middleware. Super Admin cannot list, create, assign, edit, delete, or enter Developer accounts/features; Developer cannot enter other staff modules.
- A data migration now provisions `developer.admin@pantas.text` with the `developer` role on fresh databases while preserving the password of an existing matching account.
- `DeveloperAccessTest` passes all role-isolation checks. `RoleDashboardExperienceTest` still has five pre-existing label-visibility failures because global command-palette data contains routes hidden from the visible role sidebar.
- Developer Tasks 2 and 3 added permanent configuration defaults, singleton branding overrides, caching/fallback service behavior, four protected Developer routes, a current-state dashboard, and a functional settings interface with uploads, color controls, live previews, and restore actions.
- `DeveloperBrandingSettingsTest` passes seven tests covering original values, exact-role route isolation, normalized colors, uploads, dimension/input rejection, safe replacement, and partial/full restoration.
- Developer Task 4 hardened image type, size, dimension, and decoding rules; strict hexadecimal colors; generated storage paths; transactional updates; failed-upload cleanup; and post-commit replacement cleanup that cannot target original assets.
- Developer Task 5 now shares branding centrally with Blade, scopes dynamic color variables to `pantas-default`, and replaces relevant banner/sidebar-logo references across authenticated, public, guest, Library, Attendance, OPAC, kiosk, and scanner surfaces.
- Developer table branding now includes header background, header text, border, and row-hover colors, with defaults, live preview, persistence, restoration, and `pantas-default`-only CSS integration.
- Developer branding now includes a dedicated Sidebar Logout Area Background color for the footer section containing Logout; the button retains its standard danger styling. The setting appears in the live preview and supports individual/full restoration under `pantas-default`.
- Developer branding now includes Sidebar Hover Background and Sidebar Hover Text colors for non-active navigation links, with a small movement effect, live preview, persistence, and restoration under `pantas-default`.
- Developer branding now allows editing the sidebar brand name (`Pantas`), subtitle (`Admin Portal`), and brand text color alongside the existing logo upload, with live preview and individual/full restoration.
- Added documentation:
  - `documentation/mobile-api-contract-review.md`
  - `documentation/staff-role-setup.md`
  - `documentation/local-seed-accounts.md`
  - `documentation/library-vs-attendance-patrons.md`
