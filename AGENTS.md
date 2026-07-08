# AGENTS.md — pantas-v2.5

Laravel 12 library management backend (Pantas v2.6 line). **Mobile API owner** for Pantas-UI.

Remote: `https://github.com/zypheus/Pantas-v2.6.git`

Run all commands from this directory (`pantas-v2.5/`).

## Stack

- PHP 8.2+, Laravel 12, Sanctum
- Blade + Tailwind 4 + Flowbite + Alpine
- Vite for `resources/js` and `resources/css`
- **New UI:** use daisyUI skill (`.agents/skills/daisyui/`)

## Key Paths

- `app/Http/Controllers/` — request handling
- `app/Models/` — Eloquent models
- `database/migrations/` — schema changes
- `database/seeders/` — seed data (including MARC/catalog)
- `resources/views/` — Blade templates
- `routes/web.php` — web routes
- `routes/api.php` — mobile API under `/api/mobile`
- `tests/` — PHPUnit tests

Legacy static files (`about.html`, `style.css`, etc.) exist at repo root. Prefer Laravel routes, Blade, and Vite assets for new work.

## Mobile API

Pantas-UI consumes `/api/mobile` (Sanctum auth). When changing API contracts, check:

- `routes/api.php`
- `app/Http/Controllers/Api/Mobile/` (or equivalent mobile controllers)
- Pantas-UI `lib/services/` and `lib/core/config/api_config.dart`

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=MarcFrameworkSeeder
php artisan storage:link
```

## Common Commands

```bash
php artisan serve
npm run dev
composer run dev
npm run build
php artisan test --filter=SomeTest
./vendor/bin/pint
php artisan route:list
```

## Frontend Guidance

- Match existing Blade + Tailwind + Flowbite style before introducing new patterns.
- Reuse `resources/views/components/sidebar-nav.blade.php` in the authenticated shell.
- Brand colors: `brand-navy`, `brand-gold`, `brand-green`.
- For new Tailwind UI, follow the **daisyUI** skill. Do not use shadcn here.

## Coding Standards

- Prefer Eloquent, Form Requests, policies, named routes, and migrations.
- Keep controllers thin. Use transactions for circulation, fines, registrations, and reservations.
- Preserve existing route names and view contracts unless a breaking change is requested.
- Do not add packages without strong reason.

## Verification

```bash
php -l path/to/file.php
php artisan test --filter=RelevantTest
php artisan route:list
./vendor/bin/pint
npm run build
```
