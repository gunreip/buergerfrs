<?php

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven;

final class FindingEventLabel
{
    public static function lines(object $event): array
    {
        $lines = [
            (string) str($event->event_type)->replace('_', ' ')->headline(),
            'Event #'.$event->id.' · Finding #'.$event->finding_id.($event->key_id ? ' · Key #'.$event->key_id : ''),
            (string) $event->created_at,
        ];
        $old = self::values($event->old_values ?? null);
        $new = self::values($event->new_values ?? null);
        $changes = [];
        foreach (['key_ids', 'key_id', 'translation_key', 'source_file_id', 'line', 'column', 'status'] as $field) {
            if (! array_key_exists($field, $old) && ! array_key_exists($field, $new)) {
                continue;
            }
            if (($old[$field] ?? null) !== ($new[$field] ?? null)) {
                $changes[] = $field.': '.self::value($old[$field] ?? null).' → '.self::value($new[$field] ?? null);
            }
        }
        // Respect the text-label five-line contract; full values remain in the context menu.
        if (count($changes) > 2) {
            $lines[] = $changes[0];
            $lines[] = __(':count further changes — right-click for details', ['count' => count($changes) - 1]);
        } else {
            $lines = [...$lines, ...$changes];
        }
        return $lines;
    }

    private static function values(mixed $value): array
    {
        $decoded = is_array($value) ? $value : json_decode($value ?? '{}', true);
        return is_array($decoded) ? $decoded : [];
    }

    private static function value(mixed $value): string
    {
        return $value === null ? '—' : (is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
