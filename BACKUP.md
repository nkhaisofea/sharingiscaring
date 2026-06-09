# Backup and Restore Notes

Use backups before changing migrations, seeders, imports, or production data.

## Create a Database Backup

Run:

```bash
php artisan db:backup
```

The command creates `backup.sql` in the project root. If `backup.sql` already exists, it creates a timestamped file like `backup-20260609-153000.sql` instead of overwriting the existing backup.

## Restore Sample Data

The project includes safe, repeatable seeders:

```bash
php artisan db:seed
```

`EquipmentSeeder` uses `updateOrCreate`, so it restores sample equipment without deleting existing equipment.

## Safety Rules

- Do not run destructive commands such as `migrate:fresh`, `db:wipe`, `truncate`, or manual deletes unless you have a fresh backup and intentionally want to reset data.
- Prefer `php artisan migrate` over reset commands.
- Keep generated backup files outside version control when they contain real user data.
