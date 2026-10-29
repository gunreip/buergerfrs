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


## Flux component reference

Verified on 2026-10-21 against the official v2 documentation and local vendor templates.
`composer.lock` contains `livewire/flux` v2.20.1, `livewire/flux-pro` 2.20.1 and Livewire v4.4.7.
These are the project's installed versions, not a claim about the latest available release.

The following components are marked new in the current documentation and are already installed here:

| Component | Purpose | Documentation |
|---|---|---|
| `flux:flag` | Country/region flag, for example `country="DE"`; supports sizes and circular presentation | [Flag](https://fluxui.dev/components/flag) |
| `flux:toggle` | Compact boolean control with distinct on/off labels or icons | [Toggle](https://fluxui.dev/components/toggle) |
| `flux:phone` (Pro) | International telephone input with country selection and formatting | [Phone](https://fluxui.dev/components/phone) |
| `flux:color-picker` (Pro) | Color selection | [Color picker](https://fluxui.dev/components/color-picker) |
| `flux:carousel` (Pro) | Horizontally scrolling slides | [Carousel](https://fluxui.dev/components/carousel) |

Other installed Pro components worth checking before implementing custom UI include `autocomplete`,
`command`, `composer`, `context`, `file-upload`, `kanban`, `pillbox`, `popover`, `timeline`,
`date-picker` and `time-picker`. Installation alone does not establish which props are supported:
inspect the specific component documentation and installed templates before using it.

For this project:

- Prefer the existing Flux component for tables, pagination, dialogs, accordions, context menus and toggles.
- Keep the project's locale-to-country mapping. A language identifier is not necessarily a country code;
  introducing `flux:flag` does not justify changing that mapping.
- Preserve the chosen callout layout: an icon on `flux:callout` indents its content, while an icon on
  `flux:callout.heading` belongs to the heading. Do not interchange these during cleanup.
- Check local icon implementations under `resources/views/flux/icon/` before selecting icon variants;
  the project's Lucide icons do not support every Heroicons variant.
- The current online carousel navigation mentions a Datetime picker, but its page could not be verified
  and no `datetime-picker` template exists in the installed packages. Do not assume it is available.
- Check `vendor/livewire/flux/stubs/resources/views/flux/` and the corresponding `flux-pro` directory,
  plus project overrides under `resources/views/flux/`. Do not edit vendor files to implement application UI.

This is a dated component reference. Recheck documentation and installed code when implementing a feature;
no dependency upgrade or automatic replacement of existing project components was performed for this inventory.
