# USM PANTAS — Library Management System

Laravel application for cataloging, circulation, patron registration, attendance scanning, room reservations, e-resources, and staff administration.

**Repository:** [github.com/borskenetic/pantas-v2.5](https://github.com/borskenetic/pantas-v2.5)

## Requirements

- PHP 8.2+
- Composer
- MySQL 8+ (or MariaDB)
- Node.js (optional, if you build front-end assets with Vite)

## Quick start (local)

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create a MySQL database (example name `demo_2`), then set in `.env`:

```env
APP_URL=http://localhost
DB_DATABASE=demo_2
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and seed MARC catalog framework:

```bash
php artisan migrate
php artisan db:seed --class=MarcFrameworkSeeder
php artisan storage:link
```

Optional — 10 sample students for QR/attendance testing (`S-00000001` … `S-00000010`):

```bash
php artisan db:seed --class=StudentSampleSeeder
```

Serve the app:

```bash
php artisan serve
```

Sign in with an **admin** or **staff** user created in the database or via **Create Account** in the admin UI.

## Environment notes

| Variable | Purpose |
|----------|---------|
| `BRANDING_CSS` | Per-school stylesheet under `public/branding/` |
| `SMS_MODEM_URL` / `SMS_MODEM_API_KEY` | Local Flask SMS bridge (optional) |
| `GOOGLE_BOOKS_API_KEY` | ISBN lookup quota for cataloging (optional) |
| `BACKUP_NAME` | Spatie backup name; defaults to `pantas-db` |
| `BACKUP_ARCHIVE_PASSWORD` | Password used to encrypt backup zip files |
| `MYSQL_DUMP_BINARY_PATH` | Folder containing `mysqldump`, for example `C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin` |

Copy `.env.example` — **never commit** your real `.env` file.

## Database backups

This app uses `spatie/laravel-backup` for database-only backups. Backup archives are stored on the Laravel `local` disk under private storage:

```text
storage/app/private/pantas-db/
```

Required `.env` values:

```env
BACKUP_NAME=pantas-db
BACKUP_ARCHIVE_PASSWORD=change-this-to-a-strong-secret
MYSQL_DUMP_BINARY_PATH=C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin
```

Notes:

- `MYSQL_DUMP_BINARY_PATH` must point to the folder containing `mysqldump.exe`, not the executable file itself.
- On Windows, use forward slashes in `.env` paths. Example: `C:/xampp/mysql/bin`.
- Use a MySQL client version compatible with the database server. For MySQL 8 users with `caching_sha2_password`, the XAMPP MariaDB `mysqldump` may fail; use a MySQL 8 client instead.
- Keep `BACKUP_ARCHIVE_PASSWORD` safe. Encrypted backup zips cannot be restored without it.

Manual backup and checks:

```bash
php artisan config:clear
php artisan backup:run --only-db
php artisan backup:list
php artisan backup:clean
```

Scheduled backups run daily via Laravel scheduler:

- `backup:run --only-db` at `01:00` Asia/Manila
- `backup:clean` at `01:30` Asia/Manila

Production servers must run Laravel's scheduler, for example:

```bash
* * * * * cd /path/to/pantas-v2.5 && php artisan schedule:run >> /dev/null 2>&1
```

## Uploading to GitHub

From the project root:

```bash
git status
git add -A
git commit -m "Your message describing this release"
git push origin main
```

Before pushing, confirm `git status` does **not** list `.env` or files under `public/images/student_signatures/`.

## Fresh clone on another machine

```bash
git clone https://github.com/borskenetic/pantas-v2.5.git
cd pantas-v2.5
composer install
cp .env.example .env
# edit .env for DB credentials
php artisan key:generate
php artisan migrate
php artisan db:seed --class=MarcFrameworkSeeder
php artisan storage:link
```

Ensure writable directories: `storage/`, `bootstrap/cache/`, and upload folders under `public/images/` (see `.gitkeep` files).

**Videos:** MP4 files under `public/videos/` are not stored on GitHub (too large). After cloning, copy your slideshow/background videos into `public/videos/` on the server.

## Main features

- **Catalog** — MARC-based books, programs, circulation, fines, trash/archive
- **E-Resources** — `/ebooks` digital collection with program/subject filters
- **Patrons** — student registration, ID cards, pending approvals
- **Attendance** — QR scan in/out, reports, optional logout feedback
- **Rooms** — reservations, schedule, pending queue, logs
- **Admin** — user accounts (admin/staff/faculty/student roles)

## License

Application code follows your project license. Laravel framework components are [MIT](https://opensource.org/licenses/MIT).
