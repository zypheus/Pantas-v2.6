# MySQL schema migrations

These 42 migrations mirror the production MySQL tables (excluding Laravel’s own `migrations` table).

## Files

| Migration | Table |
|-----------|-------|
| `2026_05_21_000001` | `users` |
| `2026_05_21_000002` | `password_reset_tokens` |
| `2026_05_21_000003` | `sessions` |
| `2026_05_21_000004` | `cache` |
| `2026_05_21_000005` | `cache_locks` |
| `2026_05_21_000006` | `jobs` |
| `2026_05_21_000007` | `job_batches` |
| `2026_05_21_000008` | `failed_jobs` |
| `2026_05_21_000009` | `roles` |
| `2026_05_21_000010` | `permissions` |
| `2026_05_21_000011` | `role_has_permissions` |
| `2026_05_21_000012` | `model_has_permissions` |
| `2026_05_21_000013` | `model_has_roles` |
| `2026_05_21_000014` | `courses` |
| `2026_05_21_000015` | `years` |
| `2026_05_21_000016` | `programs` |
| `2026_05_21_000017` | `program_years` |
| `2026_05_21_000018` | `program_courses` |
| `2026_05_21_000019` | `catalog_frameworks` |
| `2026_05_21_000020` | `marc_fields` |
| `2026_05_21_000021` | `catalog_framework_fields` |
| `2026_05_21_000022` | `students` |
| `2026_05_21_000023` | `employees` |
| `2026_05_21_000024` | `books` |
| `2026_05_21_000025` | `book_marc_fields` |
| `2026_05_21_000026` | `book_program` |
| `2026_05_21_000027` | `book_logs` |
| `2026_05_21_000028` | `fine_settings` |
| `2026_05_21_000029` | `ebooks` |
| `2026_05_21_000030` | `attendance_logs` |
| `2026_05_21_000031` | `rooms` |
| `2026_05_21_000032` | `room_reservations` |
| `2026_05_21_000033` | `reservation_students` |
| `2026_05_21_000034` | `reservation_logs` |
| `2026_05_21_000035` | `pending_employees` |
| `2026_05_21_000036` | `pending_students` |
| `2026_05_21_000037` | `student_edit_requests` |
| `2026_05_21_000038` | `feedback` |
| `2026_05_21_000039` | `files` |
| `2026_05_21_000040` | `settings` |
| `2026_05_21_000041` | `zendy_logs` |
| `2026_05_21_000042` | `prospectuses` |

## Fresh database

1. Archive or remove older partial migrations in `database/migrations/` (e.g. `2025_03_*`, `2025_04_14_103945_create_permission_tables.php`) so table names are not created twice.
2. Move these files into `database/migrations/`, or run:

   ```bash
   php artisan migrate --path=database/migrations/schema_from_mysql
   ```

## Existing database

Do **not** run these on a DB that already has the tables. Use them as reference, or baseline with `php artisan migrate:status` first.

## Notes

- Column types and nullability match your MySQL dump; foreign keys from the live DB are **not** declared (your listing did not include them).
- `roles` uses `description` (your schema), not Spatie’s `name` / `guard_name` pair on `roles`.
- `books.deleted_at` uses Laravel `softDeletes()`; `prospectuses.id` uses `increments()` (`int`) as in MySQL.
- `attendance_logs.student_id` is `varchar`, not a foreign key to `students.id`.
