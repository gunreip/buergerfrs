<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TranslationWorkbenchLangValueImporter
{
    public function __construct(
        private readonly TranslationWorkbenchTimelineRecorder $timelineRecorder,
    ) {}

    /**
     * @return array<string, int>
     */
    public function import(string $locale, bool $truncate = false): array
    {
        return $this->importLocales([$locale], $truncate);
    }

    /**
     * @param  array<int, string>  $locales
     * @return array<string, int>
     */
    public function importLocales(array $locales, bool $truncate = false): array
    {
        $summary = [
            'locales' => 0,
            'files' => 0,
            'values_found' => 0,
            'values_created' => 0,
            'values_updated' => 0,
            'values_unchanged' => 0,
            'values_obsoleted' => 0,
            'truncated' => 0,
            'timeline_events_created' => 0,
        ];
        $now = now();
        $locales = $this->normalizeLocales($locales);
        $values = collect($locales)
            ->flatMap(fn(string $locale): array => $this->readLangValues($locale))
            ->values()
            ->all();

        $summary['locales'] = count($locales);
        $summary['files'] = collect($values)->pluck('source_path')->unique()->count();
        $summary['values_found'] = count($values);

        DB::transaction(function () use ($values, $truncate, $now, &$summary): void {
            if ($truncate) {
                $summary['truncated'] = (int) TranslationWorkbenchLangValue::query()->count();
                $this->truncateLangValues();
            }

            foreach ($values as $value) {
                $result = $this->syncValue($value, $now);
                $summary[$result['result']]++;
                $summary['timeline_events_created'] += $result['timeline_events_created'];
            }

            if ($values !== []) {
                $obsoleteResult = $this->markMissingValuesObsolete($values, $now);
                $summary['values_obsoleted'] += $obsoleteResult['values_obsoleted'];
                $summary['timeline_events_created'] += $obsoleteResult['timeline_events_created'];
            }
        });

        return $summary;
    }

    /**
     * @return array<int, string>
     */
    public function availableLocales(): array
    {
        $langPath = lang_path();

        if (! File::isDirectory($langPath)) {
            return [];
        }

        return collect(File::directories($langPath))
            ->map(static fn(string $path): string => basename($path))
            ->filter(static fn(string $locale): bool => $locale !== 'vendor')
            ->filter(fn(string $locale): bool => $this->hasPhpLangFiles($locale))
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function readLangValues(string $locale): array
    {
        $locale = trim($locale) !== '' ? trim($locale) : 'en';
        $directory = lang_path($locale);

        if (! File::isDirectory($directory)) {
            return [];
        }

        return collect(File::files($directory))
            ->filter(static fn(\SplFileInfo $file): bool => $file->getExtension() === 'php')
            ->sortBy(static fn(\SplFileInfo $file): string => $file->getFilename())
            ->flatMap(fn(\SplFileInfo $file): array => $this->valuesFromFile($locale, $file->getPathname()))
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $locales
     * @return array<int, string>
     */
    private function normalizeLocales(array $locales): array
    {
        return collect($locales)
            ->map(static fn(string $locale): string => trim($locale))
            ->filter(static fn(string $locale): bool => $locale !== '' && $locale !== 'vendor')
            ->unique()
            ->values()
            ->all();
    }

    private function hasPhpLangFiles(string $locale): bool
    {
        $directory = lang_path($locale);

        if (! File::isDirectory($directory)) {
            return false;
        }

        return collect(File::files($directory))
            ->contains(static fn(\SplFileInfo $file): bool => $file->getExtension() === 'php');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function valuesFromFile(string $locale, string $path): array
    {
        $lines = require $path;

        if (! is_array($lines)) {
            return [];
        }

        $namespace = pathinfo($path, PATHINFO_FILENAME);
        $sourcePath = $this->relativePath($path);
        $localeContext = $this->localeContext($locale);

        return collect($this->flatten($lines))
            ->map(function (mixed $value, string $langKey) use ($locale, $namespace, $sourcePath, $localeContext): array {
                $serializedValue = $this->serializeValue($value);

                return [
                    'locale' => $locale,
                    ...$localeContext,
                    'namespace' => $namespace,
                    'lang_key' => $langKey,
                    'translation_key' => "{$namespace}.{$langKey}",
                    'value' => $serializedValue,
                    'value_type' => $this->valueType($value),
                    'source_path' => $sourcePath,
                    'source_hash' => hash('sha256', $serializedValue ?? ''),
                    'status' => 'active',
                    'meta' => [
                        'source' => 'translation-workbench:import-lang-values',
                    ],
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function flatten(array $values, string $prefix = ''): array
    {
        $flattened = [];

        foreach ($values as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $flattened += $this->flatten($value, $fullKey);

                continue;
            }

            $flattened[$fullKey] = $value;
        }

        return $flattened;
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array{result: string, timeline_events_created: int}
     */
    private function syncValue(array $attributes, mixed $now): array
    {
        $langValue = TranslationWorkbenchLangValue::query()
            ->where('locale', $attributes['locale'])
            ->where('namespace', $attributes['namespace'])
            ->where('lang_key', $attributes['lang_key'])
            ->first();

        if (! $langValue) {
            $langValue = TranslationWorkbenchLangValue::query()->create([
                ...$attributes,
                'first_seen_at' => $now,
                'last_seen_at' => $now,
                'scan_count' => 1,
            ]);

            $this->timelineRecorder->record(
                eventType: 'lang_value_discovered',
                newValues: $langValue->only([
                    'id',
                    'locale',
                    'namespace',
                    'lang_key',
                    'translation_key',
                    'value',
                    'value_type',
                    'is_source_locale',
                    'locale_role',
                    'main_locale',
                    'parent_locale',
                    'source_path',
                    'source_hash',
                    'status',
                ]),
                context: [
                    'source' => 'translation-workbench:import-lang-values',
                    'lang_value_id' => $langValue->id,
                ],
            );

            return ['result' => 'values_created', 'timeline_events_created' => 1];
        }

        $oldValues = $langValue->only(array_keys($attributes));
        $changed = collect($attributes)
            ->filter(static fn(mixed $value, string $key): bool => ($oldValues[$key] ?? null) !== $value)
            ->all();

        $langValue->forceFill([
            ...$attributes,
            'last_seen_at' => $now,
            'scan_count' => ((int) $langValue->scan_count) + 1,
        ])->save();

        if ($changed === []) {
            return ['result' => 'values_unchanged', 'timeline_events_created' => 0];
        }

        $this->timelineRecorder->record(
            eventType: 'lang_value_changed',
            oldValues: collect(array_keys($changed))
                ->mapWithKeys(static fn(string $key): array => [$key => $oldValues[$key] ?? null])
                ->all(),
            newValues: $changed,
            context: [
                'source' => 'translation-workbench:import-lang-values',
                'lang_value_id' => $langValue->id,
                'changed_fields' => array_keys($changed),
            ],
        );

        return ['result' => 'values_updated', 'timeline_events_created' => 1];
    }

    private function serializeValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return is_scalar($value)
            ? (string) $value
            : json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function valueType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            $value === null => 'null',
            default => 'string',
        };
    }

    /**
     * @return array{is_source_locale: bool, locale_role: string, main_locale: string, parent_locale: string|null}
     */
    private function localeContext(string $locale): array
    {
        $sourceLocale = (string) config('translation-workbench.source_locale', 'en');
        $isSourceLocale = $locale === $sourceLocale;
        $isSubLocale = str_contains($locale, '-');
        $mainLocale = $isSourceLocale
            ? $sourceLocale
            : (explode('-', $locale, 2)[0] ?: $locale);

        return [
            'is_source_locale' => $isSourceLocale,
            'locale_role' => $isSourceLocale
                ? 'source_main'
                : ($isSubLocale ? 'target_sub' : 'target_main'),
            'main_locale' => $mainLocale,
            'parent_locale' => $isSubLocale ? $mainLocale : null,
        ];
    }

    private function relativePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $basePath = str_replace('\\', '/', base_path());

        return str_starts_with($path, $basePath . '/')
            ? substr($path, strlen($basePath) + 1)
            : $path;
    }

    private function truncateLangValues(): void
    {
        match (DB::getDriverName()) {
            'pgsql' => DB::statement('TRUNCATE TABLE translation_workbench_lang_values RESTART IDENTITY CASCADE'),
            'mysql', 'mariadb' => $this->truncateMysqlLangValues(),
            default => TranslationWorkbenchLangValue::query()->delete(),
        };
    }

    private function truncateMysqlLangValues(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            DB::table('translation_workbench_lang_values')->truncate();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $values
     * @return array{values_obsoleted: int, timeline_events_created: int}
     */
    private function markMissingValuesObsolete(array $values, mixed $now): array
    {
        $seenKeys = collect($values)
            ->map(static fn(array $value): string => $value['locale'] . "\n" . $value['namespace'] . "\n" . $value['lang_key'])
            ->flip();
        $locales = collect($values)->pluck('locale')->unique()->values()->all();
        $result = [
            'values_obsoleted' => 0,
            'timeline_events_created' => 0,
        ];

        TranslationWorkbenchLangValue::query()
            ->whereIn('locale', $locales)
            ->where('status', 'active')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($seenKeys, $now, &$result): void {
                foreach ($rows as $row) {
                    $rowKey = $row->locale . "\n" . $row->namespace . "\n" . $row->lang_key;

                    if ($seenKeys->has($rowKey)) {
                        continue;
                    }

                    $oldValues = $row->only(['status', 'last_seen_at']);
                    $row->forceFill([
                        'status' => 'obsolete',
                        'last_seen_at' => $now,
                    ])->save();

                    $this->timelineRecorder->record(
                        eventType: 'lang_value_obsoleted',
                        oldValues: $oldValues,
                        newValues: [
                            'status' => 'obsolete',
                            'last_seen_at' => $row->last_seen_at,
                        ],
                        context: [
                            'source' => 'translation-workbench:import-lang-values',
                            'lang_value_id' => $row->id,
                            'translation_key' => $row->translation_key,
                        ],
                    );

                    $result['values_obsoleted']++;
                    $result['timeline_events_created']++;
                }
            });

        return $result;
    }
}
