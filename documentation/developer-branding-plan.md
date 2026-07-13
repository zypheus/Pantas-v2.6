# Developer Branding Role Plan

## Goal

Add a separate `developer` staff role that exclusively manages the system-wide Pantas banner, sidebar logo, and colors used by the `pantas-default` theme.

The role boundary is strict:

- Only `developer` can access Developer pages and branding actions.
- `super_admin` cannot access Developer pages or branding actions.
- Developer cannot access Super Admin, Library, or Attendance staff modules.
- Unauthorized access to `/developer/*` returns `403 Forbidden`.

## Branding Scope

Developer can:

- View a Developer dashboard.
- Upload or replace the application banner.
- Upload or replace the sidebar logo.
- Change the primary, secondary, accent, sidebar background, sidebar text, active-navigation, and primary-button colors.
- Preview current and original branding.
- Restore one customized value or restore all original Pantas values.

Custom colors affect only `pantas-default`. Other selectable themes and existing per-user theme preferences remain unchanged.

## Original Values

Define permanent original values in `config/branding.php`, including the original banner and sidebar logo paths and every customizable color. Original assets remain under `public/images` and must never be overwritten or deleted.

Database values are overrides. A null override means the application uses the corresponding original configuration value.

## Data Model

Create a singleton-style `branding_settings` table with nullable override columns:

- `banner_path`
- `sidebar_logo_path`
- `primary_color`
- `secondary_color`
- `accent_color`
- `sidebar_background_color`
- `sidebar_text_color`
- `sidebar_active_color`
- `button_color`
- `updated_by`
- timestamps

Add a `BrandingSetting` model and a centralized `BrandingService`. The service loads defaults, merges overrides, resolves public URLs, caches active branding, handles safe file replacement, restores defaults, and falls back to original assets when custom files are missing.

## Authorization And Routing

Add `developer` to the Spatie role seeder and compatibility role handling. Create exact-role `developer` middleware using `hasRole('developer')`; it must not include a Super Admin bypass.

Create `routes/developer.php` with authenticated Developer-only routes:

- `GET /developer/dashboard` (`developer.dashboard`)
- `GET /developer/branding` (`developer.branding.edit`)
- `PUT /developer/branding` (`developer.branding.update`)
- `POST /developer/branding/restore` (`developer.branding.restore`)

Developer login redirects to `/developer/dashboard`. Developer navigation contains only Dashboard, Branding Settings, Profile, and Logout. The module switcher and all Library, Attendance, and Super Admin links are hidden. Super Admin navigation contains no Developer link.

## User Interface

Create a Developer dashboard showing the current banner, sidebar logo, color palette, Original/Customized status, last update information, and a link to Branding Settings.

Create a Branding Settings page containing:

- Current and original banner previews.
- Current and original sidebar logo previews.
- Banner and logo upload controls.
- Color picker and `#RRGGBB` input for every supported color.
- Current and original value labels.
- Live sidebar/banner preview.
- Save Changes action.
- Per-field restore actions.
- Restore to Default action with confirmation.

## Upload And Color Safety

Use a Form Request to validate PNG, JPG/JPEG, and WebP images, MIME type, file size, dimensions, and successful image decoding. Store generated filenames under:

- `storage/app/public/branding/banners`
- `storage/app/public/branding/logos`

Store a new image and commit its database path before deleting the previous custom image. Never delete original files.

Accept colors only in six-digit `#RRGGBB` form and normalize them to uppercase. Reject named colors, alpha values, CSS expressions, URLs, variables, and incomplete hexadecimal values.

## Application Integration

Expose active branding to shared Blade views through a view composer, service provider, or shared component. Render validated colors as CSS custom properties consumed by `pantas-default`.

Replace relevant hard-coded references to `images/Bannernew.jpg`, `images/pantasLogo-box.png`, and `images/pantasLogo.png` in authenticated, public, guest, Library, Attendance, and scanner layouts. Images unrelated to the application banner or sidebar identity remain unchanged.

## Restore To Default

Full restoration must:

1. Verify the exact `developer` role.
2. Capture previous override values for audit logging.
3. Clear every custom database value inside a transaction.
4. Commit the restored state.
5. Clear the branding cache.
6. Delete only previous custom banner and logo files.
7. Preserve every original Pantas asset.
8. Record the action.
9. Redirect with a success message.

If the database operation fails, custom files remain untouched. Partial restore clears only the selected override and deletes only its replaced custom file when applicable.

## Audit And Error Handling

Use the existing admin activity infrastructure to record the acting Developer, action type, changed fields, previous/new values, custom file paths, and full or partial restoration. Developer may see branding-related activity only, not the general Super Admin activity feed.

Handle validation, storage, database, cache, missing-file, and authorization failures. Missing custom images automatically fall back to original assets without breaking the page.

## Verification

Automated coverage must prove:

- Developer can access, update, and restore branding.
- Super Admin and every other role receive `403` on Developer routes.
- Developer receives `403` on Super Admin, Library, and Attendance staff routes.
- Developer login and navigation remain isolated.
- Image and color validation rejects unsafe input.
- File replacement deletes only obsolete custom files.
- Original assets are never deleted.
- Overrides affect `pantas-default` only.
- Existing theme preferences continue to work.
- Full and partial restoration return the correct original values.
- Cache invalidation, missing-file fallback, and activity logging work.

Run focused role, branding, theme-preference, and module-access tests, followed by `php artisan route:list`, `npm run build`, and Pint. Visually verify desktop, collapsed, and mobile navigation; banner responsiveness; theme isolation; restoration; fallback behavior; and color contrast.

## Implementation Tasks

### Task 1: Role And Access Isolation

Add the Developer role, exact-role middleware, login redirect, isolated navigation, and `403` enforcement between Developer and every other staff module.

### Task 2: Branding Defaults And Persistence

Define permanent original values, create the branding settings table and model, and implement the centralized branding service, caching, and missing-file fallback.

### Task 3: Developer Routes And Interface

Create the Developer routes, dashboard, Branding Settings form, current/original previews, color controls, live preview, save action, and restore controls.

### Task 4: Upload And Color Safety

Implement safe image validation and replacement, protected original assets, generated storage filenames, and strict normalized hexadecimal color validation.

### Task 5: Application Branding Integration

Share branding with Blade layouts, expose CSS variables to `pantas-default`, preserve other themes, and replace relevant hard-coded banner and logo references.

### Task 6: Restoration And Audit

Implement full and partial restoration, transactional updates, safe custom-file cleanup, cache clearing, activity logging, and Developer-only branding activity display.

### Task 7: Tests And Verification

Test role isolation, navigation, uploads, colors, theme isolation, restoration, original-asset protection, fallbacks, cache invalidation, and audit records; then run route, build, formatting, and visual checks.

## Completion Criteria

The work is complete when branding is editable only by Developer, Super Admin cannot access any Developer capability, Developer cannot access other modules, banner/logo/default colors update consistently, other themes remain isolated, full and partial restoration preserve original assets, failures fall back safely, audit records are created, and all relevant checks pass.
