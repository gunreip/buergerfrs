# Documentation

This directory contains the maintained project documentation. Generated PHPDoc output is not a source of truth and is excluded from Git.

## Start Here

| Document | Purpose |
|---|---|
| [Project README](../README.md) | Requirements, setup and common workflows |
| [Architecture](architecture.md) | Application structure, domains and major data flows |
| [Operations](operations.md) | Development, deployment, scheduler, queues, backups and recovery |
| [Artisan Commands](artisan-commands.md) | Project-specific command reference and activity-log conventions |

## Translation Domain

| Document | Purpose |
|---|---|
| [Translation Key Rules](key-rules.md) | Agreed status, workflow and suggested-key rules |
| [Translation Key Patterns](key-rules-pattern.md) | Path-to-key patterns and examples |
| [Translation State Matrix](state-matrix.md) | Compact definitions of statuses and classifications |
| [Translation History](translation-history.md) | Audit-event lifecycle, snapshots, baselines and timeline behavior |

## Reports

Reports under `docs/reports/` document time-specific audits and decisions. They are evidence, not timeless project rules.

- [Flag Audit 2026-06-01](reports/flag-audit-2026-06-01.md)

## Generated API Documentation

```bash
composer docs:phpdoc
composer docs:phpdoc:publish
```

Use `composer docs:phpdoc:public` to run both steps. Generated output is local and should be regenerated after meaningful PHP API changes.

## Maintenance Rules

- Update the relevant document in the same change as a command, state or workflow change.
- Add every project-specific Artisan command to `artisan-commands.md`.
- Treat `key-rules.md` and `key-rules-pattern.md` as agreed domain rules; semantic changes require explicit discussion.
- Prefer short explanations of intent and invariants over comments that merely repeat code.
- Run `composer docs:check` before handing off documentation changes.
