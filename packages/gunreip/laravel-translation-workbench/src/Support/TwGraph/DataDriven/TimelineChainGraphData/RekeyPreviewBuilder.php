<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData;

use App\Support\Locale\LocaleCode;
use Gunreip\TranslationWorkbench\Support\TwGraph\Defaults;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class RekeyPreviewBuilder
{
    /**
     * @param  array<string, mixed>  $mainRow
     * @return array<string, mixed>
     */
    public static function facts(array $mainRow): array
    {
        $relations = self::relationsForCurrentKey($mainRow);

        return [
            'component_family' => 'tw-graph.strang.rekey-source-* / tw-graph.strang.rekey-target-*',
            'role' => 'direct key identity transition into or out of the current trunk',
            'count' => $relations->count(),
            'strangs' => $relations->values()->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $mainRow
     * @return array<int, array<string, mixed>>
     */
    public static function previews(array $mainRow): array
    {
        $relations = self::relationsForCurrentKey($mainRow);

        if ($relations->isEmpty()) {
            return [];
        }

        $sourceRelations = $relations
            ->filter(static fn(array $relation): bool => (string) ($relation['direction'] ?? '') === 'source')
            ->values();
        $targetRelations = $relations
            ->filter(static fn(array $relation): bool => (string) ($relation['direction'] ?? '') === 'target')
            ->values();
        $previews = [];

        if ($sourceRelations->isNotEmpty()) {
            $previews[] = self::preview($sourceRelations, 'source', 'left', 1, $mainRow);
        }

        if ($targetRelations->isNotEmpty()) {
            $previews[] = self::preview($targetRelations, 'target', 'right', 1, $mainRow);
        }

        return $previews;
    }

    /**
     * Direction naming is from the current trunk's perspective:
     * source = the current key was rekeyed from another key,
     * target = the current key was rekeyed to another key.
     *
     * @param  array<string, mixed>  $mainRow
     * @return Collection<int, array<string, mixed>>
     */
    private static function relationsForCurrentKey(array $mainRow): Collection
    {
        if ((string) ($mainRow['chain_type'] ?? '') !== 'moved') {
            return collect();
        }

        $currentKey = trim((string) ($mainRow['translation_key'] ?? ''));
        $chainTimestamp = $mainRow['updated_at'] ?? $mainRow['last_seen_at'] ?? $mainRow['created_at'] ?? null;
        $relations = collect(data_get($mainRow, 'meta.moved_relations', []))
            ->filter(static fn(mixed $relation): bool => is_array($relation))
            ->map(static function (array $relation) use ($chainTimestamp, $currentKey): array {
                $sourceKey = trim((string) ($relation['translation_key'] ?? ''));
                $targetKey = trim((string) ($relation['rekeyed_to_translation_key'] ?? ''));
                $direction = null;

                if ($currentKey !== '' && $targetKey === $currentKey && $sourceKey !== '') {
                    $direction = 'source';
                } elseif ($currentKey !== '' && $sourceKey === $currentKey && $targetKey !== '') {
                    $direction = 'target';
                }

                return [
                    ...$relation,
                    'direction' => $direction,
                    'source_key' => $sourceKey,
                    'target_key' => $targetKey,
                    'chain_timestamp' => $chainTimestamp,
                ];
            })
            ->filter(static fn(array $relation): bool => filled($relation['direction'] ?? null))
            ->values();

        if ($relations->isEmpty()) {
            return collect();
        }

        $keyIds = self::translationKeyIds(
            $relations
                ->flatMap(static fn(array $relation): array => [
                    $relation['source_key'] ?? null,
                    $relation['target_key'] ?? null,
                ])
                ->filter()
                ->unique()
                ->values()
                ->all(),
        );
        $langValues = self::langValueFacts(
            $relations
                ->flatMap(static fn(array $relation): array => [
                    $relation['lang_value_id'] ?? null,
                    $relation['rekeyed_to_lang_value_id'] ?? null,
                ])
                ->filter()
                ->unique()
                ->values()
                ->all(),
        );
        $events = self::eventFacts($mainRow);

        return $relations
            ->map(static fn(array $relation): array => [
                ...$relation,
                'source_key_id' => $keyIds[(string) ($relation['source_key'] ?? '')] ?? null,
                'target_key_id' => $keyIds[(string) ($relation['target_key'] ?? '')] ?? null,
                'source_lang_value' => $langValues[(int) ($relation['lang_value_id'] ?? 0)] ?? null,
                'target_lang_value' => $langValues[(int) ($relation['rekeyed_to_lang_value_id'] ?? 0)] ?? null,
                'event' => $events[(string) ($relation['source_key'] ?? '') . '->' . (string) ($relation['target_key'] ?? '')] ?? null,
            ])
            ->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $relations
     * @return array<string, mixed>
     */
    private static function preview(Collection $relations, string $kind, string $side, int $componentCounter, array $mainRow): array
    {
        $first = self::displayRelation($relations);
        $sourceKey = (string) ($first['source_key'] ?? '');
        $targetKey = (string) ($first['target_key'] ?? '');
        $sourceKeyId = $first['source_key_id'] ?? null;
        $targetKeyId = $first['target_key_id'] ?? null;
        $currentKeyId = $mainRow['root_key_id'] ?? null;
        $sourceLangValueId = $first['lang_value_id'] ?? $first['id'] ?? null;
        $targetLangValueId = $first['rekeyed_to_lang_value_id'] ?? null;
        $sourceLangValue = is_array($first['source_lang_value'] ?? null) ? $first['source_lang_value'] : [];
        $targetLangValue = is_array($first['target_lang_value'] ?? null) ? $first['target_lang_value'] : [];
        $event = is_array($first['event'] ?? null) ? $first['event'] : [];
        $timestamp = LabelFormatter::graphTimestampLabel($event['created_at'] ?? $sourceLangValue['first_seen_at'] ?? $first['updated_at'] ?? $first['last_seen_at'] ?? $first['chain_timestamp'] ?? null);
        $literal = LabelFormatter::graphLabelText((string) ($sourceLangValue['value'] ?? $targetLangValue['value'] ?? ''));
        $sourcePath = LabelFormatter::graphSourceLabelText((string) ($sourceLangValue['source_path'] ?? $targetLangValue['source_path'] ?? ''));
        $relationLine = trim(
            LabelFormatter::graphKeyLabelText($sourceKey, 44)
                . ' -> '
                . LabelFormatter::graphKeyLabelText($targetKey, 44),
            ' ->',
        );
        $isSource = $kind === 'source';
        $component = 'tw-graph.strang.rekey-' . $kind . '-' . $side;
        $outerLabelSide = $side === 'left' ? 'left' : 'right';
        $innerLabelSide = $side === 'left' ? 'right' : 'left';
        $firstSeenLabel = array_values(array_filter(['First seen', $timestamp]));
        $literalLabel = array_values(array_filter(['Literal', $literal]));
        $originKeyLabel = array_values(array_filter(['Origin key', LabelFormatter::graphKeyLabelText($sourceKey, 52)]));
        $targetKeyLabel = array_values(array_filter(['Target key', LabelFormatter::graphKeyLabelText($targetKey, 52)]));
        $sourceLabel = array_values(array_filter(['Source', $sourcePath]));
        $sourceStemContinuation = $isSource
            ? [
                1 => [
                    'compressed' => true,
                ],
            ]
            : [];
        $targetStemContinuation = $isSource
            ? []
            : [
                1 => [
                    'compressed' => true,
                    $outerLabelSide => $literalLabel,
                    $innerLabelSide => $firstSeenLabel,
                ],
                2 => [
                    $outerLabelSide => array_values(array_filter([
                        ...$sourceLabel,
                        $sourceLangValueId ? 'source lang value ID #' . (string) $sourceLangValueId : null,
                        $targetLangValueId ? 'target lang value ID #' . (string) $targetLangValueId : null,
                    ])),
                    $innerLabelSide => $targetKeyLabel,
                ],
            ];
        $sourceRekeyEndNode = 5 + count($sourceStemContinuation);

        return [
            'component' => $component,
            'kind' => $kind,
            'side' => $side,
            'component_counter' => $componentCounter,
            'color' => self::color('rekey', 'sky'),
            'attach_to' => $isSource ? 'strang.trunk.path.1.end' : 'strang.trunk.path.7.end',
            'stem_continuation' => $isSource ? $sourceStemContinuation : $targetStemContinuation,
            'end_label' => $isSource
                ? null
                : [
                    'text' => [
                        'rekey target to ID #' . (string) ($targetKeyId ?: '?'),
                        $timestamp,
                    ],
                    'badgeColor' => self::color('rekey', 'sky'),
                    'long' => true,
                ],
            'start_label' => [
                'text' => array_values(array_filter([
                    $isSource
                        ? 'rekey source from ID #' . (string) ($sourceKeyId ?: '?')
                        : 'rekey target to ID #' . (string) ($targetKeyId ?: '?'),
                    $timestamp,
                ])),
                'side' => 'bottom',
                'offset' => '0.75rem',
                'badgeColor' => self::color('rekey', 'sky'),
            ],
            'node_labels' => $isSource
                ? [
                    1 => [
                        $outerLabelSide => $firstSeenLabel,
                        $innerLabelSide => $literalLabel,
                    ],
                    2 => [
                        $outerLabelSide => array_values(array_filter([
                            'source key ID #' . (string) ($sourceKeyId ?: '?'),
                            ...$originKeyLabel,
                        ])),
                        $innerLabelSide => array_values(array_filter([
                            ...$sourceLabel,
                            $sourceLangValueId ? 'source lang value ID #' . (string) $sourceLangValueId : null,
                        ])),
                    ],
                    $sourceRekeyEndNode => [
                        $outerLabelSide => array_values(array_filter([
                            'rekeyed into this key ID #' . (string) ($currentKeyId ?: '?'),
                            $relationLine,
                        ])),
                        'connectorLength' => '5rem',
                        'long' => true,
                    ],
                ]
                : [],
            'source' => [
                'relation_count' => $relations->count(),
                'source_key' => $sourceKey,
                'target_key' => $targetKey,
                'target_key_id' => $targetKeyId,
            ],
        ];
    }

    /**
     * Prefer the active UI target locale so the graph follows the current UI
     * perspective. Fall back to source locale, then to the first relation.
     *
     * @param  Collection<int, array<string, mixed>>  $relations
     * @return array<string, mixed>
     */
    private static function displayRelation(Collection $relations): array
    {
        $activeLocale = LocaleResolver::activeTargetMainLocale();
        $sourceLocale = LocaleCode::normalize((string) config('translation-workbench.source_locale', 'en')) ?: 'en';

        return (array) (
            $relations->first(static fn(array $relation): bool => LocaleCode::normalize((string) ($relation['locale'] ?? '')) === $activeLocale)
            ?? $relations->first(static fn(array $relation): bool => LocaleCode::normalize((string) ($relation['locale'] ?? '')) === $sourceLocale)
            ?? $relations->first()
            ?? []
        );
    }

    /**
     * @param  array<int, mixed>  $langValueIds
     * @return array<int, array<string, mixed>>
     */
    private static function langValueFacts(array $langValueIds): array
    {
        $ids = collect($langValueIds)
            ->map(static fn(mixed $id): int => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty() || ! Schema::hasTable('translation_workbench_lang_values')) {
            return [];
        }

        return DB::table('translation_workbench_lang_values')
            ->whereIn('id', $ids->all())
            ->get(['id', 'locale', 'translation_key', 'value', 'source_path', 'status', 'first_seen_at', 'last_seen_at', 'updated_at'])
            ->mapWithKeys(static fn(object $row): array => [
                (int) $row->id => [
                    'id' => (int) $row->id,
                    'locale' => (string) $row->locale,
                    'translation_key' => (string) $row->translation_key,
                    'value' => (string) $row->value,
                    'source_path' => (string) ($row->source_path ?? ''),
                    'status' => (string) $row->status,
                    'first_seen_at' => $row->first_seen_at,
                    'last_seen_at' => $row->last_seen_at,
                    'updated_at' => $row->updated_at,
                ],
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $mainRow
     * @return array<string, array<string, mixed>>
     */
    private static function eventFacts(array $mainRow): array
    {
        $eventIds = ValueNormalizer::integerList($mainRow['timeline_event_ids'] ?? []);

        if ($eventIds === [] || ! Schema::hasTable('translation_workbench_timeline_events')) {
            return [];
        }

        return DB::table('translation_workbench_timeline_events')
            ->whereIn('id', $eventIds)
            ->where('event_type', 'translation_lang_values_rekeyed')
            ->get(['id', 'old_values', 'new_values', 'created_at', 'updated_at'])
            ->mapWithKeys(static function (object $event): array {
                $oldValues = is_string($event->old_values) ? (json_decode($event->old_values, true) ?: []) : (array) $event->old_values;
                $newValues = is_string($event->new_values) ? (json_decode($event->new_values, true) ?: []) : (array) $event->new_values;
                $sourceKey = trim((string) ($oldValues['translation_key'] ?? ''));
                $targetKey = trim((string) ($newValues['translation_key'] ?? ''));

                if ($sourceKey === '' || $targetKey === '') {
                    return [];
                }

                return [
                    $sourceKey . '->' . $targetKey => [
                        'id' => (int) $event->id,
                        'created_at' => $event->created_at,
                        'updated_at' => $event->updated_at,
                    ],
                ];
            })
            ->all();
    }

    /**
     * @param  array<int, string>  $translationKeys
     * @return array<string, int>
     */
    private static function translationKeyIds(array $translationKeys): array
    {
        $keys = collect($translationKeys)
            ->map(static fn(mixed $key): string => trim((string) $key))
            ->filter()
            ->unique()
            ->values();

        if ($keys->isEmpty() || ! Schema::hasTable('translation_workbench_keys')) {
            return [];
        }

        return DB::table('translation_workbench_keys')
            ->whereIn('translation_key', $keys->all())
            ->get(['id', 'translation_key'])
            ->mapWithKeys(static fn(object $row): array => [(string) $row->translation_key => (int) $row->id])
            ->all();
    }

    private static function color(string $key, string $fallback): string
    {
        return Defaults::dataDrivenString('colors.' . $key, Defaults::graphString('colors.' . $key, $fallback));
    }
}
