# Activity Logging

The global `activity_log` is the operational audit trail for security events, administrative actions and command runs. It records who initiated an action, its subject and a compact summary of the change.

## Scope

Log actions that change shared domain or administrative state, including:

- authentication and security events;
- role, permission and review workflow changes;
- manual translation decisions and value updates;
- imports, backups, synchronization and maintenance command runs.

Do not log ordinary filtering, sorting, pagination or personal list preferences. Never write passwords, tokens or complete authentication credential payloads.

## Translation History

`translation_audit_events` and `activity_log` have different responsibilities:

- `translation_audit_events` is the fine-grained, immutable history of a translation entity;
- `activity_log` contains one summarized entry for the user action or command that initiated changes.

Avoid copying the complete translation timeline into `activity_log`.

## Conventions

- Use `AdminActivity`, `ManagementActivity` or `TranslationActivity` for web actions.
- Use `ConsoleActivityContext` for commands so terminal user, hostname, SAPI and working directory are available.
- Use stable domain-prefixed event names such as `admin.role.updated` or `translations.admin.key.values_updated`.
- Attach a model subject whenever the action targets a concrete entity.
- Attach the authenticated user as causer; commands use `properties.actor` instead.
- Store compact `before` and `after` payloads for meaningful state transitions.
- Prefer one command-run summary over one activity entry per generated file.

## Verification

```bash
php artisan activity-log:audit
php artisan test tests/Feature/ActivityLogTest.php
```

The audit reports existing call quality, runtime identity gaps and mutating source files without an apparent logging integration. Reports are generated under `storage/audits/` and are not versioned.
