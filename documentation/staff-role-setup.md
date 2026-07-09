# Staff Role Setup

PANTAS uses module-aware staff roles.

## Roles

| Role | Access |
| --- | --- |
| `super_admin` | All modules and staff management |
| `library_admin` | Library administration |
| `library_staff` | Library staff workflows |
| `attendance_admin` | Attendance administration |
| `attendance_staff` | Attendance staff workflows |

## Rules

- Spatie roles are the authority for access checks.
- `users.role` is kept as a compatibility bridge during migration.
- `super_admin` can access Library, Attendance, and Super Admin routes.
- Library-only users should not see or access Attendance module routes.
- Attendance-only users should not see or access Library module routes.
- Inactive users with `is_active = false` are logged out or blocked.

## Useful Commands

```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=SuperAdminSeeder
php artisan test --filter=ModuleAccessFoundationTest
php artisan route:list --except-vendor
```

## Staff Creation

Create staff from the Super Admin staff-management screen or seeders. When creating staff manually, keep `users.role` and Spatie roles aligned until the compatibility bridge is removed.
