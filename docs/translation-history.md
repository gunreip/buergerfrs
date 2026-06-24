# Translation History and Audit Events

## Purpose

Translation history records meaningful changes to persistent translation work items. The timeline is an audit view, not merely a rendering of current model values.

Audit events are stored in `translation_audit_events` and associated with a `TranslationKey`; usage-specific events may additionally reference a `TranslationUsage`.

## Event Types

| Event type | Meaning |
|---|---|
| `discovered` | Initial translation work item discovered during an audit scan |
| `created` | Locale-specific `TranslationValue` created manually |
| `value_changed` | Locale-specific translation text changed |
| `key_changed` | Translation key changed, including accepted suggested keys |
| `fingerprint_changed` | Same usage detected with a different fingerprint |
| `moved` | Usage moved to another file or line |
| `stale_marked` | Usage was not seen, or a key was marked obsolete |
| `reactivated` | Previously stale/obsolete usage or key was found again |
| `manual_needs_new_key_changed` | Manual Needs-New-Key marker changed |
| `workflow_status_changed` | Human review workflow status changed |
| `native_text_filled` | Previously empty native text populated by an audit |
| `native_text_changed` | Audited native text changed |
| `legacy_status_normalized` | Legacy non-key obsolete state normalized |

Every known event type has a dedicated timeline component. Unknown future types use the fallback renderer and are visibly marked as such.

## Baseline Events

New translation work items receive a real `discovered` event during their first audit synchronization.

Older rows may predate this behavior. Missing baselines are created with:

```bash
php artisan translations:backfill-audit-discovered-events --dry-run
php artisan translations:backfill-audit-discovered-events
```

The command is idempotent. It uses `translation_keys.first_seen_at` for chronological placement and marks the event context with `backfilled: true`.

Backfilled values describe the record available during backfill, not a perfectly reconstructed original row. The UI therefore displays an incomplete-history warning.

## Usage Snapshots

New audit events store the relevant usage rows under `context.affected_usages` and mark `context.affected_usages_snapshot_complete` as true. Timeline components use this immutable snapshot instead of current relationships.

Events created before snapshots were introduced cannot be reconstructed reliably. For those events:

- the timeline displays a `Usage snapshot unavailable` warning;
- usage sections are labeled `Current affected usages`;
- the current rows may differ from the state at event time.

Historical data must never be silently presented as an exact snapshot when it is derived from current state.

## `raw` and `original_raw`

- `TranslationUsage.raw` contains the most recently synchronized source call.
- `TranslationUsage.original_raw` preserves the first non-empty source call observed for that usage.

Timeline fields labeled `Original source call` prefer `original_raw` and use `raw` only as a compatibility fallback.

## Ordering and Loading

Events are ordered by `created_at` descending and then by `id` descending. This keeps backfilled baseline events at their historical position even though their database IDs are newer.

The modal initially loads 50 events. When additional events exist, `Load older events` retrieves another batch. The counter displays loaded events and total stored events.

## Technical Metadata

The event badge tooltip explains the domain meaning of the state. Technical metadata belongs to the information tooltip and includes event ID, entity type, event type, renderer, locale and technical reason.

Backfilled or otherwise incomplete events must retain their warning badges even when their normal body renders successfully.

## Adding an Event Type

1. Define a stable snake_case event type and reason values.
2. Store old/new fields and context required to understand the change later.
3. Include an immutable affected-usage snapshot when applicable.
4. Add color, title and explanation mappings in the timeline.
5. Add a dedicated component named from the event type (`some_event` → `some-event.blade.php`).
6. Add or update tests and this event table.
