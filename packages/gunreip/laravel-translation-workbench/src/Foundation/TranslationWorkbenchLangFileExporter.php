<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use App\Models\Locale;
use App\Settings\AppGeneralSettings;
use App\Support\Locale\LocaleCode;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class TranslationWorkbenchLangFileExporter
{
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

        return [
            'dry_run' => ! $write,
            'locales' => collect($plans)->pluck('locale')->unique()->sort()->values()->all(),
            'namespaces' => collect($plans)->pluck('namespace')->unique()->sort()->values()->all(),
            'files' => count($plans),
            'values_exportable' => $rows->count(),
            'values_new' => collect($plans)->sum('values_new'),
            'values_changed' => collect($plans)->sum('values_changed'),
            'values_unchanged' => collect($plans)->sum('values_unchanged'),
            'values_conflicted' => collect($plans)->sum('values_conflicted'),
            'active_scope' => $activeScope,
            'files_written' => collect($plans)->where('written', true)->count(),
            'files_skipped' => collect($plans)->where('written', false)->count(),
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
        $conflicts = [];

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
            'values_conflicted' => count($conflicts),
            'written' => $written,
            'conflicts' => $conflicts,
            'changes' => $changes
                ->reject(static fn(array $change): bool => $change['state'] === 'unchanged')
                ->values()
                ->all(),
        ];
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
