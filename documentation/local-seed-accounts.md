# Local Seed Accounts

## Super Admin

The local development super admin account is:

```text
Email: super_admin@pantas.test
Password: password
Role: super_admin
```

Seed it with:

```bash
php artisan db:seed --class=SuperAdminSeeder
```

## Roles

Seed module roles with:

```bash
php artisan db:seed --class=RoleSeeder
```

## Notes

- These credentials are for local development only.
- Do not reuse local seed credentials in production.
- If permissions behave unexpectedly, rerun role and super admin seeders, then clear caches if needed.
