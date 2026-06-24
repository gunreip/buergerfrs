# buergerfrs

`buergerfrs` is a Laravel application for administrative and management workflows. The project includes role- and permission-based administration, person and reference-data management, activity logging, and a comprehensive translation audit and review workflow.

## Technology

- PHP 8.3–8.5 and Laravel 13
- Livewire 4 with Flux UI
- Pest for automated tests
- Vite and Tailwind CSS 4
- Database-backed queues, sessions and cache by default

Exact locally installed versions are recorded in [VERSIONS.md](VERSIONS.md).

## Requirements

- PHP 8.3 or newer
- Composer 2
- Node.js 22 and npm
- A supported database; SQLite is the default in `.env.example`
- Flux UI credentials for installing the private Composer packages

Configure Flux access before the first Composer install when it is not already available globally:

```bash
composer config http-basic.composer.fluxui.dev "$FLUX_USERNAME" "$FLUX_LICENSE_KEY"
```

## Local Setup

```bash
composer setup
```

This installs dependencies, creates `.env` when missing, generates the application key, runs migrations and builds frontend assets.

Review `.env` before running the application. Database-backed sessions, queues and cache require migrated tables.

Start the local development processes:

```bash
composer dev
```

This starts the application server, queue listener, log viewer and Vite development server together.

## Tests and Quality Checks

```bash
composer test
composer lint:check
npm run build
composer docs:check
```

`composer test` uses the project-safe test command, which backs up and restores the configured database. See [Operations](docs/operations.md) before running it against a valuable local dataset.

## Common Workflows

Full project maintenance/build flow:

```bash
php artisan project:build
```

Translation workflow:

```bash
php artisan project:translations
```

Bootstrap reference and application data:

```bash
composer bootstrap:data
```

All project-specific commands and their purpose are listed in the [Artisan Commands Reference](docs/artisan-commands.md).

## Documentation

The canonical documentation index is [docs/index.md](docs/index.md). Important starting points:

- [Architecture](docs/architecture.md)
- [Operations and Runbooks](docs/operations.md)
- [Translation State Matrix](docs/state-matrix.md)
- [Translation Key Rules](docs/key-rules.md)
- [Translation History and Audit Events](docs/translation-history.md)
- [Artisan Commands Reference](docs/artisan-commands.md)

Generated PHP API documentation can be created locally with:

```bash
composer docs:phpdoc:public
```

The generated `docs/phpdoc` and `public/docs/phpdoc` directories are intentionally ignored by Git.
