<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use Illuminate\Support\Collection;

final class LabelFormatter
{
    public static function graphLabelText(string $value, int $limit = 44): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');

        if ($value === '') {
            return '';
        }

        return str($value)->limit($limit, '...')->toString();
    }

    public static function graphTimestampLabel(mixed $value): string
    {
        $value = trim(str_replace('T', ' ', (string) $value));

        if ($value === '') {
            return '';
        }

        return mb_substr($value, 0, 16);
    }

    public static function langValueTimestampLine(object $row): string
    {
        $timestamp = self::graphTimestampLabel($row->last_seen_at ?? $row->updated_at ?? $row->created_at ?? null);
        $localeValue = trim((string) $row->locale . ' · ' . self::graphLabelText((string) $row->value, 30));

        return trim(implode(' · ', array_filter([$timestamp, $localeValue])));
    }

    /**
     * @param  array<string, mixed>  $mainRow
     * @return array<int, string>
     */
    public static function trunkEndLabelLines(array $mainRow, string $defaultStateLine): array
    {
        $keyIdLine = 'key ID #' . (string) ($mainRow['root_key_id'] ?? '?');
        $chainEndLine = '- TIMELINE CHAIN END -';

        if ((string) ($mainRow['chain_type'] ?? '') !== 'moved') {
            return array_values(array_filter([
                $keyIdLine,
                $defaultStateLine,
                $chainEndLine,
            ]));
        }

        $currentKey = trim((string) ($mainRow['translation_key'] ?? ''));
        $movedRelations = collect(data_get($mainRow, 'meta.moved_relations', []))
            ->filter(static fn(mixed $relation): bool => is_array($relation))
            ->values();
        $sourceKeys = $movedRelations
            ->pluck('translation_key')
            ->map(static fn(mixed $key): string => trim((string) $key))
            ->filter()
            ->unique()
            ->values();
        $targetKeys = $movedRelations
            ->pluck('rekeyed_to_translation_key')
            ->map(static fn(mixed $key): string => trim((string) $key))
            ->filter()
            ->unique()
            ->values();
        $relationText = '';
        $stateText = 'moved / rekeyed';

        if ($currentKey !== '' && $targetKeys->contains($currentKey) && $sourceKeys->isNotEmpty()) {
            $stateText = 'moved / merged into this key';
            $relationText = self::graphKeyLabelText($sourceKeys->implode(', '), 54)
                . ' -> '
                . self::graphKeyLabelText($currentKey, 54);
        } elseif ($currentKey !== '' && $sourceKeys->contains($currentKey) && $targetKeys->isNotEmpty()) {
            $stateText = 'moved / merged to target key';
            $relationText = self::graphKeyLabelText($currentKey, 54)
                . ' -> '
                . self::graphKeyLabelText($targetKeys->implode(', '), 54);
        } elseif ($sourceKeys->isNotEmpty() || $targetKeys->isNotEmpty()) {
            $relationText = trim(
                self::graphKeyLabelText($sourceKeys->implode(', '), 54)
                    . ' -> '
                    . self::graphKeyLabelText($targetKeys->implode(', '), 54),
                ' ->',
            );
        }

        return array_values(array_filter([
            $keyIdLine,
            $stateText,
            $relationText,
            $chainEndLine,
        ]));
    }

    public static function findingLabel(string $value): string
    {
        $value = trim($value);

        if (preg_match('/#\d+/', $value, $matches) === 1) {
            return 'finding ID ' . $matches[0];
        }

        return $value !== '' ? $value : 'finding ID ?';
    }

    /**
     * @return array<int, string>
     */
    public static function findingIdLabel(string $value): array
    {
        $value = trim($value);

        if (preg_match('/#\d+/', $value, $matches) === 1) {
            return ['findingID', $matches[0]];
        }

        return ['findingID', $value !== '' ? $value : '?'];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<int, string>
     */
    public static function findingIdLabelWithTimestamp(array $row): array
    {
        $findingId = self::findingIdLabel(trim((string) ($row['first_root'] ?? '')));

        return array_values(array_filter([
            trim(implode(' ', $findingId)),
            self::graphTimestampLabel($row['first_timestamp'] ?? null),
        ]));
    }

    public static function graphKeyLabelText(string $value, int $limit = 52): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');

        if ($value === '') {
            return '';
        }

        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return '...' . mb_substr($value, - ($limit - 3));
    }

    public static function graphSourceLabelText(string $value, int $limit = 52): string
    {
        return self::graphKeyLabelText($value, $limit);
    }

    /**
     * @param  Collection<string, mixed>  $summary
     */
    public static function mergeOriginCountLabel(Collection $summary): string
    {
        $total = (int) $summary->get('total', 0);

        return $total > 0 ? $total . ' origins' : '';
    }

    /**
     * @param  Collection<string, mixed>  $summary
     */
    public static function mergeOutcomeResultLine(Collection $summary): string
    {
        $total = (int) $summary->get('total', 0);

        if ($total <= 0) {
            return '';
        }

        $active = (int) $summary->get('source_active', 0);
        $ended = (int) $summary->get('source_inactive', 0);
        $unknown = (int) $summary->get('unknown', 0);
        $parts = [
            $active . ' active',
            $ended . ' ended',
        ];

        if ($unknown > 0) {
            $parts[] = $unknown . ' unknown';
        }

        return implode(' · ', $parts);
    }
}
