# Artisan Commands Reference

This file summarizes the project-specific Artisan commands.

## Quick Help

Every Artisan command already supports `--help` by default.

Examples:

```bash
php artisan project:build --help
php artisan translations:export-lang-files --help
php artisan reference:import-locale-data --help
```
## Orchestrierungs-Commands (Empfohlen)

Diese zwei Commands sind die zentralen Einstiegspunkte fuer wiederholbare Team-Workflows:

- `project:build`
  - Voller Projekt-Build inkl. Wartungs-/Audit-Schritten.
  - Nutze diesen Command fuer den allgemeinen Build-/Release-Flow.
- `project:translations`
  - Spezieller Translation-Workflow (Audit -> Sync -> strict Diffs -> optional Apply -> Export -> finale Audits).
  - Nutze diesen Command fuer die taegliche Uebersetzungsarbeit im Team.

Empfohlene Aufrufe:

```bash
php artisan project:build
php artisan project:translations
```

Nuetzliche Varianten:

```bash
php artisan project:translations --apply-diffs
php artisan project:translations --locales=de,en
php artisan project:translations --skip-export --skip-audits
```
## Entscheidungshilfe: Welcher Orchestrierungs-Command?

| Situation | Command | Warum |
|---|---|---|
| Normaler Build-/Wartungslauf (Technik + Audits + Assets) | `php artisan project:build` | Vollstaendiger Projektlauf fuer Betriebs-/Build-Aufgaben. |
| Team arbeitet aktiv an Uebersetzungen in der App | `php artisan project:translations` | Fokus auf Translation-Workflow ohne manuelles Einzelklicken im Terminal. |
| Translation-DIFFs sollen automatisch angewendet werden | `php artisan project:translations --apply-diffs` | Wendet `latest.patch` nach erfolgreichem Check an. |
| Nur bestimmte Locales exportieren | `php artisan project:translations --locales=de,en` | Begrenzter Export fuer gezielte Spracharbeit. |
| Erst nur Audit/Sync/DIFF erzeugen (ohne Export/Audit-Finale) | `php artisan project:translations --skip-export --skip-audits` | Gut fuer Zwischenstaende waehrend App-Review. |

Empfehlung fuer euren Teamprozess:

1. Fachliche Bearbeitung/Review in der App.
2. Danach `project:translations` fuer konsistente Nachverarbeitung.
3. Vor groesseren Releases optional `project:build`.

## Project Commands

| Command | Purpose |
|---|---|
| `app:backfill-user-persons` | Create and link `Person` records for users missing `person_id`. |
| `app:write-app-version` | Write the current git-based app version to `public/version.txt`. |
| `html:check` | Check Blade views for unclosed/mismatched HTML and selected components. |
| `html:check-view-html-used` | Audit used native HTML tags and Blade component usage in views. |
| `html:sync-native-tags` | Sync WHATWG native HTML tag reference into storage. |
| `html:sync-view-audit` | Sync HTML view audit snapshot JSON into DB history. |
| `project:bootstrap-data` | Migrate/seed/import reference data and run DB healthcheck. |
| `project:build` | Run project maintenance/build pipeline. |
| `project:db-backup` | Create DB backup and apply project retention policy. |
| `project:db-health` | Check DB health and optionally fail on empty critical tables. |
| `project:test-safe` | Backup DB, run tests, and restore DB safely. |
| `reference:import-locale-data` | Import locale/country/language reference data. |
| `system:versions` | Write OS/PHP/package versions to `VERSIONS.md`. |
| `translations:audit-code` | Audit translation calls in code. |
| `translations:audit-compare` | Compare code audit and language-file audit. |
| `translations:audit-duplicate-usage-literals` | Find source-language literals used by multiple keys and identify centralization candidates. |
| `translations:audit-frequent-usage-literals` | Report frequently used source-language literals and their centralization status. |
| `translations:audit-lang` | Audit files under `lang/`. |
| `translations:audit-lang-ballast` | Audit language-file entries that no longer match the database state. |
| `translations:audit-sub-language-redundancy` | Mark sub-language values that duplicate their base locale. |
| `translations:backfill-audit-discovered-events` | Create missing translation-history baseline events; supports `--dry-run`. |
| `translations:backfill-native-text-from-values` | Fill missing native text from existing locale values. |
| `translations:ensure-lang-directories` | Ensure `lang/{locale}` directories exist for translation locales. |
| `translations:export-lang-files` | Export translation values from DB to `lang/{locale}/*.php` and `lang/{locale}.json`. |
| `translations:generate-literal-diffs` | Generate runnable patch diffs for replacing literal translation calls with mapped translation keys. |
| `translations:lang-ballast:apply` | Preview or apply approved language-ballast decisions. |
| `translations:normalize-keyed-native-statuses` | Normalize legacy native-status rows that already have a concrete key. |
| `translations:rewrite-literals` | Replace literal texts in translation calls with mapped translation keys from `translation_keys`. |
| `translations:sync-audits` | Sync translation audit JSON into DB translation tables. |
| `translations:usage-decisions:apply` | Apply ready translation-usage audit decisions to source files. |
| `translations:usage-decisions:preview` | Generate previews for ready translation-usage audit decisions. |
| `views:sync-component-tags` | Scan Blade views and write used component tags reference. |

## Typical Translation Workflow

```bash
php artisan translations:ensure-lang-directories
php artisan translations:audit-code
php artisan translations:audit-lang
php artisan translations:audit-compare
php artisan translations:sync-audits
php artisan translations:backfill-audit-discovered-events --dry-run
php artisan translations:generate-literal-diffs
# Optional apply step:
bash storage/audits/translations/diffs/latest.apply.sh
php artisan translations:export-lang-files
```

The discovered-event backfill is normally a one-time compatibility step for existing databases. Omit it from routine runs after all translation keys have a baseline event.
## Activity Log Convention For Artisan Commands

All project-specific commands should write activity log entries so command runs are traceable in production and local debugging.

- Preferred log names by domain:
  - project / app / system commands: project
  - translation commands: translations
  - html/view audit commands: html
  - reference import commands: reference
- Event naming pattern:
  - {domain}.{action}.completed
  - {domain}.{action}.failed
  - optional: {domain}.{action}.no_target_locales, {domain}.{action}.completed_with_findings, file_created, file_updated
- Minimum properties:
  - command (Artisan command name)
  - options (if relevant)
  - summary or counters (for completed runs)
  - error (for failed runs)

Example event names used in this project:

- project.build.completed
- project.build.failed
- project.db_health.completed
- translations.audit.code.completed
- translations.audit.sync.completed
- html.view_usage_check.completed
- reference.import_locale_data.completed

Recommended helper pattern in command classes:

```php
private function logRunActivity(string $event, string $description, array $properties = []): void
{
    try {
        activity('project')
            ->event($event)
            ->withProperties(array_merge([
                'command' => $this->getName(),
            ], $properties))
            ->log($description);
    } catch (Throwable $exception) {
        $this->warn('Activity log write failed: ' . $exception->getMessage());
    }
}
```
## Diagnostics Property Checklist

Use the following diagnostics fields consistently in activity properties for command runs.

Required base fields:

- command
- event
- status (success, failed, warning)
- exit_code
- started_at
- finished_at
- duration_ms

Recommended context fields:

- options
- environment
- actor

Recommended result fields:

- summary (created, updated, skipped, unchanged)
- affected_paths or affected_locales

Recommended failure fields:

- failed_step
- error_message
- error_type
- stack_trace (local and debug only)

Example completed payload:

    {
      "command": "translations:export-lang-files",
      "event": "translations.lang.export.completed",
      "status": "success",
      "exit_code": 0,
      "duration_ms": 842,
      "options": {
        "dry_run": false,
        "locales": "de,en"
      },
      "summary": {
        "created_directories": 0,
        "created_files": 1,
        "updated_files": 3,
        "unchanged_files": 12
      }
    }

Example failed payload:

    {
      "command": "project:build",
      "event": "project.build.failed",
      "status": "failed",
      "exit_code": 1,
      "failed_step": "translations:sync-audits",
      "error_message": "SQLSTATE ...",
      "duration_ms": 12455
    }

## Tip

When unsure about options, always run:

```bash
php artisan <command> --help
```
