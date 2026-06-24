# Operations and Runbooks

## Local Development

```bash
composer setup
composer dev
```

`composer dev` starts the PHP server, database queue listener, Laravel Pail and Vite. Stop the combined process to stop all children.

## Database

SQLite is the default in `.env.example`. Other Laravel-supported connections can be configured through the standard `DB_*` variables.

Database-backed sessions, queues and cache are enabled by default, so migrations must be current:

```bash
php artisan migrate
```

Bootstrap local data and reference tables with:

```bash
php artisan project:bootstrap-data
```

## Queue and Scheduler

Local queue processing is included in `composer dev`. Production requires a supervised queue worker or Horizon process.

The scheduler in `routes/console.php` currently runs:

| Schedule | Command |
|---|---|
| Hourly | `project:db-health --fail-on-empty --quiet-ok` |
| Every ten minutes | `project:db-backup` |

Production must call `php artisan schedule:run` every minute or run Laravel's scheduler worker.

## Backups and Safe Testing

Create a project database backup:

```bash
php artisan project:db-backup
```

Run the test suite while protecting the configured database:

```bash
composer test
```

This delegates to `project:test-safe --with-lint`, which backs up the database, runs checks and restores the database afterward. Confirm backup storage and database credentials before using it with important data.

## Build and Deployment Checklist

1. Install production Composer dependencies and frontend dependencies.
2. Configure `.env`, Flux credentials and application secrets.
3. Run migrations.
4. Build frontend assets with `npm run build`.
5. Run `php artisan project:db-health --fail-on-empty`.
6. Start or restart queue/Horizon processes.
7. Ensure the scheduler is active.
8. Run relevant application and translation checks.
9. Refresh framework caches according to the deployment environment.

The project-wide maintenance pipeline is available through:

```bash
php artisan project:build
```

Inspect `php artisan project:build --help` and the command implementation before using it as an unattended production deployment script.

## Translation Operations

Normal workflow:

```bash
php artisan project:translations
```

Backfill missing translation-history baselines:

```bash
php artisan translations:backfill-audit-discovered-events --dry-run
php artisan translations:backfill-audit-discovered-events
```

Backfilled baselines are explicitly marked as historically incomplete because their field and usage details cannot be reconstructed perfectly.

## Generated Documentation

```bash
composer docs:check
composer docs:phpdoc:public
```

`docs:check` validates maintained Markdown links and the Artisan command reference. PHPDoc output is generated locally and ignored by Git.

## Troubleshooting

- Use `php artisan project:db-health` for core table diagnostics.
- Inspect the Activity Log administration page for user and command activity.
- Use Laravel Pail, application logs, Telescope and Horizon according to the environment.
- Run individual commands with `--help` before applying maintenance or translation changes.
