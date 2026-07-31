<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use App\Models\Locale;
use App\Settings\AppGeneralSettings;
use App\Support\Locale\LocaleCode;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TranslationWorkbenchLangFileExporter
{
    public function __construct(
        private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder,
    ) {}

    /**
     * @param  array<int, string>  $locales
     * @param  array<int, string>  $namespaces
     * @return array<string, mixed>
     */
    public function export(array $locales = [], array $namespaces = [], bool $write = false): array
    {
        $rows = $this->exportableRows($locales, $namespaces);
        $activeScope = $this->activeScopeSummary($rows);
        $plans = $rows
            ->groupBy(fn(TranslationWorkbenchLangValue $row): string => $row->locale . "\n" . $row->namespace)
            ->map(fn(Collection $group): array => $this->planFile($group, $write))
            ->values()
            ->all();
        $timelineEventsCreated = $write ? $this->recordLangFileTimelineEvents($plans) : 0;

        return [
            'dry_run' => ! $write,
            'locales' => collect($plans)->pluck('locale')->unique()->sort()->values()->all(),
            'namespaces' => collect($plans)->pluck('namespace')->unique()->sort()->values()->all(),
            'files' => count($plans),
            'values_exportable' => $rows->count(),
            'values_new' => collect($plans)->sum('values_new'),
            'values_changed' => collect($plans)->sum('values_changed'),
            'values_unchanged' => collect($plans)->sum('values_unchanged'),
            'values_pruned' => collect($plans)->sum('values_pruned'),
            'values_conflicted' => collect($plans)->sum('values_conflicted'),
            'active_scope' => $activeScope,
            'files_written' => collect($plans)->where('written', true)->count(),
            'files_skipped' => collect($plans)->where('written', false)->count(),
            'timeline_events_created' => $timelineEventsCreated,
            'plans' => $plans,
        ];
    }

    /**
     * @param  array<int, string>  $locales
     * @param  array<int, string>  $namespaces
     * @return Collection<int, TranslationWorkbenchLangValue>
     */
    private function exportableRows(array $locales, array $namespaces): Collection
    {
        $locales = $this->normalizedFilters($locales);
        $namespaces = $this->normalizedFilters($namespaces);

        return TranslationWorkbenchLangValue::query()
            ->where('status', 'active')
            ->whereNotNull('translation_key')
            ->whereNotNull('value')
            ->whereExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('translation_workbench_keys')
                    ->whereColumn('translation_workbench_keys.translation_key', 'translation_workbench_lang_values.translation_key')
                    ->where('translation_workbench_keys.status', '<>', 'obsolete');
            })
            ->when($locales !== [], fn($query) => $query->whereIn('locale', $locales))
            ->when($namespaces !== [], fn($query) => $query->whereIn('namespace', $namespaces))
            ->orderBy('locale')
            ->orderBy('namespace')
            ->orderBy('lang_key')
            ->get();
    }

    /**
     * @param  Collection<int, TranslationWorkbenchLangValue>  $rows
     * @return array<string, mixed>
     */
    private function planFile(Collection $rows, bool $write): array
    {
        /** @var TranslationWorkbenchLangValue $first */
        $first = $rows->first();
        $locale = (string) $first->locale;
        $namespace = (string) $first->namespace;
        $path = lang_path($locale . '/' . $namespace . '.php');
        $existing = $this->readExistingFile($path);
        $merged = $existing;
        $changes = [];
        $pruned = [];
        $conflicts = [];

        foreach ($this->prunableRows($locale, $namespace) as $row) {
            $langKey = (string) $row->lang_key;

            if (! Arr::has($merged, $langKey)) {
                continue;
            }

            $oldValue = Arr::get($merged, $langKey);
            $prunableValue = $this->typedPrunableRowValue($row);

            if (is_array($oldValue) && ! is_array($prunableValue)) {
                continue;
            }

            $pruned[] = [
                'lang_key' => $langKey,
                'translation_key' => (string) $row->translation_key,
                'reason' => (string) $row->prune_reason,
                'old_value' => $oldValue,
            ];

            Arr::forget($merged, $langKey);
        }

        foreach ($rows as $row) {
            $langKey = (string) $row->lang_key;
            $value = $this->typedValue($row);
            $existingValue = Arr::get($existing, $langKey);
            $exists = Arr::has($existing, $langKey);

            if (! $this->canSetNested($merged, $langKey)) {
                $conflicts[] = [
                    'lang_key' => $langKey,
                    'translation_key' => (string) $row->translation_key,
                    'reason' => 'nested_path_conflict',
                    ...$this->nestedPathConflictContext($merged, $row, $langKey, $locale, $namespace, $path),
                ];

                continue;
            }

            $changes[] = [
                'lang_key' => $langKey,
                'translation_key' => (string) $row->translation_key,
                'state' => ! $exists ? 'new' : ($existingValue === $value ? 'unchanged' : 'changed'),
                'old_value' => $exists ? $existingValue : null,
                'new_value' => $value,
            ];

            Arr::set($merged, $langKey, $value);
        }

        $changes = collect($changes);
        $written = false;

        if ($write && $conflicts === []) {
            File::ensureDirectoryExists(dirname($path));
            File::put($path, $this->formatPhpArray($merged));
            $written = true;
        }

        return [
            'locale' => $locale,
            'namespace' => $namespace,
            'path' => $this->relativePath($path),
            'rows' => $rows->count(),
            'values_new' => $changes->where('state', 'new')->count(),
            'values_changed' => $changes->where('state', 'changed')->count(),
            'values_unchanged' => $changes->where('state', 'unchanged')->count(),
            'values_pruned' => count($pruned),
            'values_conflicted' => count($conflicts),
            'written' => $written,
            'conflicts' => $conflicts,
            'pruned' => $pruned,
            'changes' => $changes
                ->reject(static fn(array $change): bool => $change['state'] === 'unchanged')
                ->values()
                ->all(),
        ];
    }

    /**
     * @return Collection<int, object>
     */
    private function prunableRows(string $locale, string $namespace): Collection
    {
        return DB::table('translation_workbench_lang_values')
            ->select([
                'translation_workbench_lang_values.lang_key',
                'translation_workbench_lang_values.translation_key',
                'translation_workbench_lang_values.value',
                'translation_workbench_lang_values.value_type',
                DB::raw("
                    CASE
                        WHEN translation_workbench_lang_values.status <> 'active'
                            THEN 'obsolete_lang_value'
                        ELSE 'no_active_workbench_key'
                    END as prune_reason
                "),
            ])
            ->where('translation_workbench_lang_values.locale', $locale)
            ->where('translation_workbench_lang_values.namespace', $namespace)
            ->where(function ($query): void {
                $query
                    ->where('translation_workbench_lang_values.status', '<>', 'active')
                    ->orWhereNotExists(function ($query): void {
                        $query
                            ->selectRaw('1')
                            ->from('translation_workbench_keys')
                            ->whereColumn('translation_workbench_keys.translation_key', 'translation_workbench_lang_values.translation_key')
                            ->where('translation_workbench_keys.status', '<>', 'obsolete');
                    });
            })
            ->orderBy('translation_workbench_lang_values.lang_key')
            ->get();
    }

    /**
     * @param  array<int, array<string, mixed>>  $plans
     */
    private function recordLangFileTimelineEvents(array $plans): int
    {
        $eventRows = collect($plans)
            ->filter(static fn(array $plan): bool => (bool) ($plan['written'] ?? false))
            ->flatMap(function (array $plan): Collection {
                $baseContext = [
                    'source' => 'translation-workbench:export-lang-files',
                    'locale' => $plan['locale'] ?? null,
                    'namespace' => $plan['namespace'] ?? null,
                    'path' => $plan['path'] ?? null,
                ];

                $changes = collect($plan['changes'] ?? [])
                    ->map(static fn(array $change): array => [
                        ...$change,
                        'event_type' => 'lang_file_value_exported',
                        'context' => [
                            ...$baseContext,
                            'state' => $change['state'] ?? null,
                            'lang_key' => $change['lang_key'] ?? null,
                        ],
                    ]);

                $pruned = collect($plan['pruned'] ?? [])
                    ->map(static fn(array $pruned): array => [
                        ...$pruned,
                        'event_type' => 'lang_file_value_pruned',
                        'state' => 'pruned',
                        'new_value' => null,
                        'context' => [
                            ...$baseContext,
                            'state' => 'pruned',
                            'reason' => $pruned['reason'] ?? null,
                            'lang_key' => $pruned['lang_key'] ?? null,
                        ],
                    ]);

                return $changes->merge($pruned);
            })
            ->values();

        if ($eventRows->isEmpty()) {
            return 0;
        }

        $keys = TranslationWorkbenchKey::query()
            ->whereIn('translation_key', $eventRows->pluck('translation_key')->filter()->unique()->values())
            ->orderByRaw("CASE WHEN status = 'obsolete' THEN 1 ELSE 0 END")
            ->orderBy('id')
            ->get()
            ->unique('translation_key')
            ->keyBy('translation_key');
        $created = 0;

        foreach ($eventRows as $row) {
            $translationKey = (string) ($row['translation_key'] ?? '');
            $key = $translationKey !== '' ? $keys->get($translationKey) : null;

            if (! $key instanceof TranslationWorkbenchKey) {
                continue;
            }

            $this->timelineRecorder->recordKeyEvent(
                key: $key,
                eventType: (string) $row['event_type'],
                oldValues: [
                    'value' => $row['old_value'] ?? null,
                    'locale' => $row['context']['locale'] ?? null,
                    'namespace' => $row['context']['namespace'] ?? null,
                    'lang_key' => $row['context']['lang_key'] ?? null,
                    'translation_key' => $translationKey,
                    'state' => $row['context']['state'] ?? null,
                    'reason' => $row['context']['reason'] ?? null,
                    'path' => $row['context']['path'] ?? null,
                ],
                newValues: [
                    'value' => $row['new_value'] ?? null,
                    'locale' => $row['context']['locale'] ?? null,
                    'namespace' => $row['context']['namespace'] ?? null,
                    'lang_key' => $row['context']['lang_key'] ?? null,
                    'translation_key' => $translationKey,
                    'state' => $row['context']['state'] ?? null,
                    'reason' => $row['context']['reason'] ?? null,
                    'path' => $row['context']['path'] ?? null,
                ],
                context: [
                    ...((array) ($row['context'] ?? [])),
                    'translation_key' => $translationKey,
                ],
            );

            $created++;
        }

        return $created;
    }

    /**
     * @return array<string, mixed>
     */
    private function readExistingFile(string $path): array
    {
        if (! File::exists($path)) {
            return [];
        }

        $values = require $path;

        return is_array($values) ? $values : [];
    }

    private function canSetNested(array $values, string $key): bool
    {
        $segments = explode('.', $key);
        array_pop($segments);
        $current = $values;
        $path = '';

        foreach ($segments as $segment) {
            $path = $path === '' ? $segment : $path . '.' . $segment;

            if (! Arr::has($current, $segment)) {
                return true;
            }

            $next = $current[$segment];

            if (! is_array($next)) {
                return false;
            }

            $current = $next;
        }

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function nestedPathConflictContext(
        array $merged,
        TranslationWorkbenchLangValue $blockedRow,
        string $langKey,
        string $locale,
        string $namespace,
        string $path,
    ): array {
        $blockingLangKey = $this->blockingLangKey($merged, $langKey);
        $blockingValue = $blockingLangKey !== null ? Arr::get($merged, $blockingLangKey) : null;
        $blockingLangValue = $blockingLangKey !== null
            ? $this->blockingLangValue($locale, $namespace, $blockingLangKey)
            : null;
        $blockingTranslationKey = (string) (
            $blockingLangValue?->translation_key
            ?? $this->translationKeyFromLangKey($namespace, $blockingLangKey)
            ?? ''
        );
        $blockingKey = $blockingTranslationKey !== ''
            ? $this->translationWorkbenchKey($blockingTranslationKey)
            : null;
        $blockingFindingRows = $blockingKey
            ? $this->translationWorkbenchFindingRows((int) $blockingKey->id)
            : collect();

        return [
            'blocked_value' => $this->reportableValue($this->typedValue($blockedRow)),
            'blocking_lang_key' => $blockingLangKey,
            'blocking_translation_key' => $blockingTranslationKey !== '' ? $blockingTranslationKey : null,
            'blocking_value' => $this->reportableValue($blockingValue),
            'blocking_value_type' => get_debug_type($blockingValue),
            'blocking_path' => $this->relativePath($path),
            'blocking_lang_value_id' => $blockingLangValue?->id,
            'blocking_lang_value_status' => $blockingLangValue?->status,
            'blocking_key_id' => $blockingKey?->id,
            'blocking_key_status' => $blockingKey?->status,
            'blocking_finding_ids' => $blockingFindingRows->pluck('id')->all(),
            'blocking_findings' => $blockingFindingRows->all(),
            'blocking_usage_status' => $this->blockingUsageStatus($blockingLangValue, $blockingKey, $blockingFindingRows),
        ];
    }

    private function blockingLangKey(array $values, string $langKey): ?string
    {
        $segments = explode('.', $langKey);
        array_pop($segments);
        $current = $values;
        $path = '';

        foreach ($segments as $segment) {
            $path = $path === '' ? $segment : $path . '.' . $segment;

            if (! Arr::has($current, $segment)) {
                return null;
            }

            $next = $current[$segment];

            if (! is_array($next)) {
                return $path;
            }

            $current = $next;
        }

        return null;
    }

    private function blockingLangValue(string $locale, string $namespace, string $langKey): ?object
    {
        return DB::table('translation_workbench_lang_values')
            ->where('locale', $locale)
            ->where('namespace', $namespace)
            ->where('lang_key', $langKey)
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderByDesc('updated_at')
            ->first([
                'id',
                'translation_key',
                'status',
            ]);
    }

    private function translationKeyFromLangKey(string $namespace, ?string $langKey): ?string
    {
        if ($langKey === null || $langKey === '') {
            return null;
        }

        return $namespace . '.' . $langKey;
    }

    private function translationWorkbenchKey(string $translationKey): ?object
    {
        return DB::table('translation_workbench_keys')
            ->where('translation_key', $translationKey)
            ->orderByRaw("CASE WHEN status = 'obsolete' THEN 1 ELSE 0 END")
            ->orderBy('id')
            ->first([
                'id',
                'translation_key',
                'status',
                'is_ui_key',
                'is_dynamic_key',
                'is_dynamic_multi',
            ]);
    }

    private function translationWorkbenchFindingRows(int $keyId): Collection
    {
        return DB::table('translation_workbench_key_findings')
            ->join('translation_workbench_findings', 'translation_workbench_findings.id', '=', 'translation_workbench_key_findings.finding_id')
            ->leftJoin('translation_workbench_source_files', 'translation_workbench_source_files.id', '=', 'translation_workbench_findings.source_file_id')
            ->where('translation_workbench_key_findings.key_id', $keyId)
            ->where('translation_workbench_key_findings.status', 'active')
            ->orderBy('translation_workbench_findings.id')
            ->limit(8)
            ->get([
                'translation_workbench_findings.id',
                'translation_workbench_findings.status',
                'translation_workbench_findings.kind',
                'translation_workbench_findings.source_line',
                'translation_workbench_source_files.path as source_path',
            ])
            ->map(static fn(object $row): array => [
                'id' => (int) $row->id,
                'status' => (string) $row->status,
                'kind' => (string) $row->kind,
                'source_path' => (string) ($row->source_path ?? ''),
                'source_line' => $row->source_line !== null ? (int) $row->source_line : null,
            ]);
    }

    private function blockingUsageStatus(?object $blockingLangValue, ?object $blockingKey, Collection $blockingFindingRows): string
    {
        if ($blockingKey === null && $blockingLangValue === null) {
            return 'lang_file_only';
        }

        if ($blockingKey !== null && (string) $blockingKey->status === 'obsolete') {
            return 'obsolete';
        }

        if ($blockingFindingRows->isNotEmpty()) {
            return 'active';
        }

        return 'missing_active_usage';
    }

    private function reportableValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return [
                'type' => 'array',
                'count' => count($value),
            ];
        }

        if (is_object($value)) {
            return [
                'type' => get_debug_type($value),
            ];
        }

        return $value;
    }

    private function typedPrunableRowValue(object $row): mixed
    {
        return match ((string) ($row->value_type ?? 'string')) {
            'boolean' => filter_var($row->value ?? null, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) ($row->value ?? 0),
            'float' => (float) ($row->value ?? 0),
            'null' => null,
            default => (string) ($row->value ?? ''),
        };
    }

    private function typedValue(TranslationWorkbenchLangValue $row): mixed
    {
        return match ((string) $row->value_type) {
            'boolean' => filter_var($row->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $row->value,
            'float' => (float) $row->value,
            'null' => null,
            default => (string) $row->value,
        };
    }

    /**
     * @param  Collection<int, TranslationWorkbenchLangValue>  $rows
     * @return array<string, mixed>
     */
    private function activeScopeSummary(Collection $rows): array
    {
        $locales = $this->activeScopeLocales();
        $scopeRows = $rows->whereIn('locale', $locales['all']);
        $sourceKeys = $scopeRows
            ->where('locale', $locales['source'])
            ->pluck('translation_key')
            ->map(static fn(mixed $key): string => (string) $key)
            ->filter(static fn(string $key): bool => $key !== '')
            ->unique()
            ->values();
        $targetMainKeys = $scopeRows
            ->where('locale', $locales['target_main'])
            ->pluck('translation_key')
            ->map(static fn(mixed $key): string => (string) $key)
            ->filter(static fn(string $key): bool => $key !== '')
            ->unique()
            ->values();
        $targetMainMissingKeys = $sourceKeys
            ->diff($targetMainKeys)
            ->sort()
            ->values();
        $targetMainExtraKeys = $targetMainKeys
            ->diff($sourceKeys)
            ->sort()
            ->values();

        return [
            'source_locale' => $locales['source'],
            'target_main_locale' => $locales['target_main'],
            'target_sub_locales' => $locales['target_sub'],
            'locales' => $locales['all'],
            'values_exportable' => $scopeRows->count(),
            'source_values' => $scopeRows->where('locale', $locales['source'])->count(),
            'target_main_values' => $scopeRows->where('locale', $locales['target_main'])->count(),
            'target_main_missing' => $targetMainMissingKeys->count(),
            'target_main_extra' => $targetMainExtraKeys->count(),
            'target_main_balanced' => $targetMainMissingKeys->isEmpty() && $targetMainExtraKeys->isEmpty(),
            'target_main_missing_keys' => $targetMainMissingKeys->all(),
            'target_main_extra_keys' => $targetMainExtraKeys->all(),
            'target_sub_values' => $scopeRows
                ->filter(fn(TranslationWorkbenchLangValue $row): bool => in_array((string) $row->locale, $locales['target_sub'], true))
                ->count(),
            'values_by_locale' => $scopeRows
                ->groupBy('locale')
                ->map(static fn(Collection $localeRows): int => $localeRows->count())
                ->sortKeys()
                ->all(),
        ];
    }

    /**
     * @return array{source: string, target_main: string, target_sub: array<int, string>, all: array<int, string>}
     */
    private function activeScopeLocales(): array
    {
        $sourceLocale = $this->normalizedLangLocale((string) config('translation-workbench.source_locale', 'en')) ?: 'en';
        $configuredLocale = $this->normalizedLangLocale((string) (app(AppGeneralSettings::class)->locale ?? app()->getLocale()));
        $configuredLocale = $configuredLocale !== '' ? $configuredLocale : app()->getLocale();
        $activeLanguage = (string) (LocaleCode::parts($configuredLocale)['language'] ?? $configuredLocale);
        $targetMainLocale = $activeLanguage !== '' ? $activeLanguage : $configuredLocale;
        $targetSubLocales = Locale::query()
            ->where('is_active', true)
            ->ordered()
            ->get(['code', 'normalized_code'])
            ->map(fn(Locale $locale): string => $this->normalizedLangLocale((string) ($locale->normalized_code ?: $locale->code)))
            ->filter(static fn(string $locale): bool => $locale !== '')
            ->filter(static function (string $locale) use ($activeLanguage): bool {
                return (string) (LocaleCode::parts($locale)['language'] ?? '') === $activeLanguage
                    && $locale !== $activeLanguage;
            })
            ->unique()
            ->values()
            ->all();

        return [
            'source' => $sourceLocale,
            'target_main' => $targetMainLocale,
            'target_sub' => $targetSubLocales,
            'all' => collect([$sourceLocale, $targetMainLocale, ...$targetSubLocales])
                ->filter()
                ->unique()
                ->values()
                ->all(),
        ];
    }

    private function normalizedLangLocale(string $locale): string
    {
        return strtolower(LocaleCode::normalize($locale));
    }

    /**
     * @param  array<int|string, mixed>  $values
     */
    private function formatPhpArray(array $values): string
    {
        ksort($values);

        return "<?php\n\nreturn " . $this->formatArray($values) . ";\n";
    }

    /**
     * @param  array<int|string, mixed>  $values
     */
    private function formatArray(array $values, int $level = 0): string
    {
        if ($values === []) {
            return '[]';
        }

        $indent = str_repeat('    ', $level);
        $nextIndent = str_repeat('    ', $level + 1);
        $lines = ['['];

        foreach ($values as $key => $value) {
            $formattedKey = var_export($key, true);
            $formattedValue = is_array($value)
                ? $this->formatArray($this->sortedArray($value), $level + 1)
                : var_export($value, true);

            $lines[] = "{$nextIndent}{$formattedKey} => {$formattedValue},";
        }

        $lines[] = "{$indent}]";

        return implode("\n", $lines);
    }

    /**
     * @param  array<int|string, mixed>  $values
     * @return array<int|string, mixed>
     */
    private function sortedArray(array $values): array
    {
        ksort($values);

        return $values;
    }

    /**
     * @param  array<int, string>  $values
     * @return array<int, string>
     */
    private function normalizedFilters(array $values): array
    {
        return collect($values)
            ->map(static fn(string $value): string => trim($value))
            ->filter(static fn(string $value): bool => $value !== '' && $value !== 'all')
            ->unique()
            ->values()
            ->all();
    }

    private function relativePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $basePath = str_replace('\\', '/', base_path());

        return str_starts_with($path, $basePath . '/')
            ? substr($path, strlen($basePath) + 1)
            : $path;
    }
}
