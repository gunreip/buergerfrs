# Architecture

## Overview

The application is a Laravel 13 monolith with server-rendered Livewire administration interfaces. Flux provides the UI component layer, while Eloquent models and dedicated support classes contain domain and infrastructure behavior.

## Application Layers

| Area | Primary location | Responsibility |
|---|---|---|
| Routes | `routes/` | Web, administration, management, settings and scheduled command entry points |
| Livewire UI | `app/Livewire/`, `resources/views/` | Interactive pages, filters, modals and workflows |
| Domain data | `app/Models/` | Eloquent entities and relationships |
| Domain support | `app/Support/` | Locale, audit, activity-log, icon, avatar and form behavior |
| Console workflows | `app/Console/Commands/` | Builds, audits, imports, backups and maintenance orchestration |
| Configuration | `config/`, `app/Settings/` | Environment configuration and database-backed application settings |
| Persistence | `database/` | Migrations, seeders, settings migrations and reference data |
| Tests | `tests/` | Pest feature and integration coverage |

## Main Functional Areas

### Administration

Administration routes live in `routes/admin.php`. They cover users, people, clients, roles, permissions, translations, application settings, activity logs and reference/audit views.

Access is enforced through authentication, roles, permissions and policies. Role and permission behavior is based on Spatie Laravel Permission.

### People Management

People workflows combine Livewire forms, related Eloquent records and protected document delivery. The main creation workflow is implemented by `App\Livewire\Management\People\CreatePerson`.

### Translation Management

The translation domain treats `translation_keys` as persistent work items. Code and language-file audits produce machine-readable files under `storage/audits/translations`; `translations:sync-audits` synchronizes those findings into database tables.

The central entities are:

- `TranslationKey`: persistent work item and machine/workflow state
- `TranslationUsage`: source-code occurrence, fingerprint and original call
- `TranslationValue`: locale-specific text and status
- `translation_audit_events`: immutable change-history records

See [Translation History](translation-history.md) and [Translation Key Rules](key-rules.md).

### Activity Logging

User and command actions are recorded through Spatie Activitylog. Console commands should use the conventions in [Artisan Commands](artisan-commands.md), including domain-specific event names and diagnostic context. The boundary between operational activities and domain history is defined in [Activity Logging](activity-logging.md).

## Important Data Flows

### Translation Audit

1. Audit source code and language files.
2. Compare findings and assign statuses/classifications.
3. Synchronize findings into translation tables.
4. Review and edit work items in the administration UI.
5. Generate/apply source diffs when literals are replaced by keys.
6. Export reviewed database values back to language files.
7. Run final audits to verify consistency.

### Reference Data

Locale, country and language reference data is imported through `reference:import-locale-data`. `project:bootstrap-data` orchestrates migrations, seeders, imports and a final health check.

## Cross-Cutting Conventions

- Admin lists share pagination, sorting, filtering and highlighting components.
- PostgreSQL text search in admin/audit lists is normally case-insensitive.
- Source rewrites remain separate from ordinary UI persistence.
- Potentially destructive translation cleanup requires explicit review or an apply flag.
- Database history remains the durable record even when exported language-file entries change.
