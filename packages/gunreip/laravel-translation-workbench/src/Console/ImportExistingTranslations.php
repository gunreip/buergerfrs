<?php

// packages/gunreip/laravel-translation-workbench/src/Console/ImportExistingTranslations.php

// php artisan translation-workbench:import-existing
// php artisan translation-workbench:import-existing --dry-run
// php artisan translation-workbench:import-existing --source-locale=en --dry-run

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchEntry;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchEvent;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchValue;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

#[Signature('translation-workbench:import-existing
    {--source-locale=en : Source language locale directory to import as the translation basis.}
    {--dry-run : Report only; do not write database rows.}')]
#[Description('Import already existing source lang file values for keyed translation workbench entries.')]
class ImportExistingTranslations extends Command
{
    use WritesTranslationWorkbenchReports;

    public function handle(): int
    {
        $sourceLocale = $this->sourceLocale();
        $dryRun = (bool) $this->option('dry-run');
        $now = now();
        $summary = [
            'entries' => 0,
            'entries_with_translations' => 0,
            'entries_without_translations' => 0,
            'values_found' => 0,
            'values_created' => 0,
            'values_updated' => 0,
            'values_unchanged' => 0,
            'values_would_create' => 0,
            'values_would_update' => 0,
            'values_would_unchanged' => 0,
            'target_type_marked_lang' => 0,
            'target_type_cleared' => 0,
            'target_type_would_mark_lang' => 0,
            'target_type_would_clear' => 0,
        ];

        $entries = TranslationWorkbenchEntry::query()
            ->where(function ($query): void {
                $query
                    ->whereNotNull('translation_key')
                    ->orWhereNotNull('existing_key');
            })
            ->orderBy('id')
            ->get();

        $summary['entries'] = $entries->count();

        $runner = function () use ($entries, $sourceLocale, $dryRun, $now, &$summary): void {
            foreach ($entries as $entry) {
                $translation = $this->translationForEntry($entry, $sourceLocale);

                if ($translation === null) {
                    $summary['entries_without_translations']++;

                    $targetTypeResult = $dryRun
                        ? $this->evaluateTargetType($entry, null)
                        : $this->syncTargetType($entry, null);

                    if ($targetTypeResult !== null) {
                        $summary[$targetTypeResult]++;
                    }

                    continue;
                }

                $summary['entries_with_translations']++;
                $summary['values_found']++;

                $targetTypeResult = $dryRun
                    ? $this->evaluateTargetType($entry, 'lang')
                    : $this->syncTargetType($entry, 'lang');

                if ($targetTypeResult !== null) {
                    $summary[$targetTypeResult]++;
                }

                $result = $dryRun
                    ? $this->evaluateValue($entry, $translation)
                    : $this->syncValue($entry, $translation, $now);

                $summary[$result]++;
            }
        };

        if ($dryRun) {
            $runner();
        } else {
            DB::transaction($runner);
        }

        $this->components->info('Existing translation import finished.');
        $this->line('Keyed entries: ' . number_format($summary['entries']));

        if ($dryRun) {
            $this->warn('Dry run only: no database rows were written.');
        }

        /**
         * Shared raw-data report.
         *
         * The report structure is centralized in WritesTranslationWorkbenchReports.
         * Do not add command-specific raw_data fields here or change the report
         * contract silently; discuss report contract changes first.
         */
        $this->writeTranslationWorkbenchReport();

        return self::SUCCESS;
    }

    /**
     * @return non-empty-string
     */
    private function sourceLocale(): string
    {
        $sourceLocale = trim((string) $this->option('source-locale'));

        return $sourceLocale !== '' ? $sourceLocale : 'en';
    }

    /**
     * @return array{source_locale: string, namespace: string, lang_key: string, value: string, source_reference: string}|null
     */
    private function translationForEntry(TranslationWorkbenchEntry $entry, string $sourceLocale): ?array
    {
        $translationKey = trim((string) ($entry->translation_key ?: $entry->existing_key), '.');

        if ($translationKey === '') {
            return null;
        }

        [$namespace, $langKey] = $this->splitTranslationKey($translationKey);

        if ($namespace === '' || $langKey === '') {
            return null;
        }

        $path = lang_path("{$sourceLocale}/{$namespace}.php");

        if (! File::isFile($path)) {
            return null;
        }

        $lines = require $path;

        if (! is_array($lines)) {
            return null;
        }

        $value = data_get($lines, $langKey);

        if ($value === null) {
            return null;
        }

        return [
            'source_locale' => $sourceLocale,
            'namespace' => $namespace,
            'lang_key' => $langKey,
            'value' => is_scalar($value)
                ? (string) $value
                : json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'source_reference' => "lang/{$sourceLocale}/{$namespace}.php:{$langKey}",
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitTranslationKey(string $translationKey): array
    {
        $segments = explode('.', $translationKey);
        $namespace = (string) array_shift($segments);

        return [$namespace, implode('.', $segments)];
    }

    /**
     * @param  array{source_locale: string, namespace: string, lang_key: string, value: string, source_reference: string}  $translation
     */
    private function evaluateValue(TranslationWorkbenchEntry $entry, array $translation): string
    {
        $value = TranslationWorkbenchValue::query()
            ->where('entry_id', $entry->id)
            ->where('value_key', $this->sourceValueKey($translation['source_locale']))
            ->first();

        if (! $value) {
            return 'values_would_create';
        }

        return $value->native_label !== $translation['value']
            || $value->source_type !== 'lang_file'
            || $value->source_reference !== $translation['source_reference']
            ? 'values_would_update'
            : 'values_would_unchanged';
    }

    /**
     * @param  array{source_locale: string, namespace: string, lang_key: string, value: string, source_reference: string}  $translation
     */
    private function syncValue(TranslationWorkbenchEntry $entry, array $translation, mixed $now): string
    {
        $attributes = [
            'native_label' => $translation['value'],
            'source_type' => 'lang_file',
            'source_reference' => $translation['source_reference'],
            'status' => 'open',
            'last_seen_at' => $now,
            'meta' => [
                'source_locale' => $translation['source_locale'],
                'namespace' => $translation['namespace'],
                'lang_key' => $translation['lang_key'],
                'source' => 'translation-workbench:import-existing',
            ],
        ];

        $value = TranslationWorkbenchValue::query()
            ->where('entry_id', $entry->id)
            ->where('value_key', $this->sourceValueKey($translation['source_locale']))
            ->first();

        if (! $value) {
            $value = TranslationWorkbenchValue::query()->create([
                'entry_id' => $entry->id,
                'value_key' => $this->sourceValueKey($translation['source_locale']),
                ...$attributes,
                'first_seen_at' => $now,
            ]);

            $this->recordEvent($entry, 'existing_translation_value_imported', null, [
                'value_id' => $value->id,
                'value_key' => $value->value_key,
                ...$attributes,
            ]);

            return 'values_created';
        }

        $oldValues = $value->only(array_keys($attributes));
        $changed = collect($attributes)
            ->filter(static fn(mixed $newValue, string $key): bool => ($oldValues[$key] ?? null) !== $newValue)
            ->all();

        if ($changed === []) {
            return 'values_unchanged';
        }

        $value->forceFill($attributes)->save();

        $this->recordEvent($entry, 'existing_translation_value_changed', $oldValues, [
            'value_id' => $value->id,
            'value_key' => $value->value_key,
            ...$attributes,
        ]);

        return 'values_updated';
    }

    private function sourceValueKey(string $sourceLocale): string
    {
        return "source:{$sourceLocale}";
    }

    private function evaluateTargetType(TranslationWorkbenchEntry $entry, ?string $targetType): ?string
    {
        $currentTargetType = $entry->target_type;

        if ($currentTargetType === $targetType) {
            return null;
        }

        return $targetType === 'lang'
            ? 'target_type_would_mark_lang'
            : 'target_type_would_clear';
    }

    private function syncTargetType(TranslationWorkbenchEntry $entry, ?string $targetType): ?string
    {
        $currentTargetType = $entry->target_type;

        if ($currentTargetType === $targetType) {
            return null;
        }

        $entry->forceFill([
            'target_type' => $targetType,
        ])->save();

        $this->recordEvent($entry, 'target_type_changed', [
            'target_type' => $currentTargetType,
        ], [
            'target_type' => $targetType,
        ]);

        return $targetType === 'lang'
            ? 'target_type_marked_lang'
            : 'target_type_cleared';
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    private function recordEvent(
        TranslationWorkbenchEntry $entry,
        string $eventType,
        ?array $oldValues,
        ?array $newValues,
    ): void {
        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => $eventType,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'context' => [
                'source' => 'translation-workbench:import-existing',
            ],
            'created_by' => auth()->id(),
        ]);
    }
}
