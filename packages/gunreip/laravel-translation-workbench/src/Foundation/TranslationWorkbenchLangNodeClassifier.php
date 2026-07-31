<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TranslationWorkbenchLangNodeClassifier
{
    public const UNKNOWN = 'unknown';
    public const LEAF = 'leaf';
    public const CONTAINER = 'container';
    public const CONFLICT = 'conflict';

    public function __construct(
        private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder,
    ) {}

    /**
     * @return array<string, int>
     */
    public function classify(bool $dryRun = false): array
    {
        $summary = [
            'keys_checked' => 0,
            'keys_changed' => 0,
            'unknown' => 0,
            'leaf' => 0,
            'container' => 0,
            'conflict' => 0,
            'timeline_events_created' => 0,
        ];

        if (! $this->hasRequiredSchema()) {
            return $summary;
        }

        $activeKeys = TranslationWorkbenchKey::query()
            ->where('status', '<>', 'obsolete')
            ->whereNotNull('translation_key')
            ->orderBy('id')
            ->get();
        $activeTranslationKeys = $activeKeys
            ->pluck('translation_key')
            ->map(static fn(mixed $key): string => trim((string) $key, '.'))
            ->filter()
            ->unique()
            ->values();
        $activeLangValueKeys = DB::table('translation_workbench_lang_values')
            ->where('status', 'active')
            ->whereNotNull('translation_key')
            ->pluck('translation_key')
            ->map(static fn(mixed $key): string => trim((string) $key, '.'))
            ->filter()
            ->unique()
            ->values();
        $nodeKeys = $activeTranslationKeys
            ->merge($activeLangValueKeys)
            ->unique()
            ->values();

        /** @var Collection<string, true> $leafLookup */
        $leafLookup = $activeLangValueKeys
            ->flip()
            ->map(static fn(): bool => true);

        foreach ($activeKeys as $key) {
            $summary['keys_checked']++;

            $translationKey = trim((string) $key->translation_key, '.');
            $nodeType = $this->nodeType($translationKey, $leafLookup, $nodeKeys);
            $summary[$nodeType]++;

            if ((string) ($key->lang_node_type ?? self::UNKNOWN) === $nodeType) {
                continue;
            }

            $summary['keys_changed']++;

            if ($dryRun) {
                continue;
            }

            $oldType = (string) ($key->lang_node_type ?? self::UNKNOWN);
            $key->forceFill(['lang_node_type' => $nodeType])->save();

            $this->timelineRecorder->recordKeyEvent(
                key: $key,
                eventType: 'lang_node_type_classified',
                oldValues: [
                    'lang_node_type' => $oldType,
                ],
                newValues: [
                    'lang_node_type' => $nodeType,
                    'translation_key' => $translationKey,
                ],
                context: [
                    'source' => 'translation-workbench:classify-lang-node-types',
                ],
            );

            $summary['timeline_events_created']++;
        }

        return $summary;
    }

    /**
     * Review a single editable translation-key candidate against the current
     * lang-value tree. This must stay deterministic; do not silently rewrite
     * keys here without an explicit review/workflow decision.
     *
     * @return array{
     *     translation_key: ?string,
     *     node_type: string,
     *     has_leaf_value: bool,
     *     has_children: bool,
     *     is_blocked: bool,
     *     proposed_leaf_key: ?string,
     *     child_keys: array<int, string>,
     *     child_key_rows: array<int, array{translation_key: string, values: array<int, array{locale: string, value: string}>}>
     * }
     */
    public function reviewCandidate(?string $translationKey, ?int $currentKeyId = null): array
    {
        $translationKey = trim((string) $translationKey, '.');
        $translationKey = $translationKey !== '' ? $translationKey : null;

        $review = [
            'translation_key' => $translationKey,
            'node_type' => self::UNKNOWN,
            'has_leaf_value' => false,
            'has_children' => false,
            'is_blocked' => false,
            'proposed_leaf_key' => null,
            'child_keys' => [],
            'child_key_rows' => [],
        ];

        if ($translationKey === null || ! $this->hasRequiredSchema()) {
            return $review;
        }

        $hasLeafValue = DB::table('translation_workbench_lang_values')
            ->where('status', 'active')
            ->where('translation_key', $translationKey)
            ->exists();
        $childKeys = DB::table('translation_workbench_keys')
            ->where('status', '<>', 'obsolete')
            ->whereNotNull('translation_key')
            ->when($currentKeyId !== null, static function ($query) use ($currentKeyId): void {
                $query->where('id', '<>', $currentKeyId);
            })
            ->where('translation_key', 'like', $translationKey.'.%')
            ->pluck('translation_key')
            ->merge(
                DB::table('translation_workbench_lang_values')
                    ->where('status', 'active')
                    ->whereNotNull('translation_key')
                    ->where('translation_key', 'like', $translationKey.'.%')
                    ->pluck('translation_key'),
            )
            ->map(static fn(mixed $key): string => trim((string) $key, '.'))
            ->filter()
            ->unique()
            ->sort()
            ->values();
        $hasChildren = $childKeys->isNotEmpty();
        $nodeType = match (true) {
            $hasLeafValue && $hasChildren => self::CONFLICT,
            $hasChildren => self::CONTAINER,
            $hasLeafValue => self::LEAF,
            default => self::UNKNOWN,
        };
        $segments = collect(explode('.', $translationKey))
            ->map(static fn(string $segment): string => trim($segment))
            ->filter(static fn(string $segment): bool => $segment !== '')
            ->values();
        $lastSegment = $segments->last();
        $limitedChildKeys = $childKeys->take(8)->values();
        $childValues = $limitedChildKeys->isNotEmpty()
            ? DB::table('translation_workbench_lang_values')
                ->where('status', 'active')
                ->whereIn('translation_key', $limitedChildKeys->all())
                ->orderBy('translation_key')
                ->orderBy('locale')
                ->get(['translation_key', 'locale', 'value'])
                ->groupBy(static fn(object $row): string => trim((string) $row->translation_key, '.'))
            : collect();

        return [
            'translation_key' => $translationKey,
            'node_type' => $nodeType,
            'has_leaf_value' => $hasLeafValue,
            'has_children' => $hasChildren,
            'is_blocked' => $hasChildren,
            'proposed_leaf_key' => is_string($lastSegment) && $lastSegment !== ''
                ? $translationKey.'.'.$lastSegment
                : null,
            'child_keys' => $limitedChildKeys->all(),
            'child_key_rows' => $limitedChildKeys
                ->map(static fn(string $childKey): array => [
                    'translation_key' => $childKey,
                    'values' => collect($childValues->get($childKey, []))
                        ->map(static fn(object $row): array => [
                            'locale' => trim((string) $row->locale),
                            'value' => (string) $row->value,
                        ])
                        ->values()
                        ->all(),
                ])
                ->all(),
        ];
    }

    /**
     * @param  Collection<string, true>  $leafLookup
     * @param  Collection<int, string>  $nodeKeys
     */
    private function nodeType(string $translationKey, Collection $leafLookup, Collection $nodeKeys): string
    {
        if ($translationKey === '') {
            return self::UNKNOWN;
        }

        $isLeaf = $leafLookup->has($translationKey);
        $isContainer = $nodeKeys->contains(
            static fn(string $candidate): bool => str_starts_with($candidate, $translationKey . '.'),
        );

        return match (true) {
            $isLeaf && $isContainer => self::CONFLICT,
            $isContainer => self::CONTAINER,
            $isLeaf => self::LEAF,
            default => self::UNKNOWN,
        };
    }

    private function hasRequiredSchema(): bool
    {
        return Schema::hasTable('translation_workbench_keys')
            && Schema::hasTable('translation_workbench_lang_values')
            && Schema::hasColumn('translation_workbench_keys', 'lang_node_type');
    }
}
