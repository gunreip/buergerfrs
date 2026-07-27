<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use App\Models\Locale;
use App\Settings\AppGeneralSettings;
use App\Support\Locale\LocaleCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TranslationWorkbenchCodeUpdatePlanner
{
    /**
     * @param  array<int, string>  $paths
     * @return array<string, mixed>
     */
    public function plan(array $paths = [], ?int $limit = null): array
    {
        $rows = $this->reviewedFindingRows($paths, $limit);
        $updates = collect($rows)
            ->map(fn(object $row): array => $this->planRow($row))
            ->values();

        return [
            'generated_at' => now()->toISOString(),
            'paths' => array_values($paths),
            'summary' => [
                'reviewed_findings' => $updates->count(),
                'safe_updates' => $updates->where('state', 'safe_update')->count(),
                'already_current' => $updates->where('state', 'already_current')->count(),
                'manual_review' => $updates->where('state', 'manual_review')->count(),
                'missing_lang_values' => $updates->where('state', 'missing_lang_values')->count(),
                'stale_source' => $updates->where('state', 'stale_source')->count(),
                'missing_source_file' => $updates->where('state', 'missing_source_file')->count(),
                'unsupported_expression' => $updates->where('state', 'unsupported_expression')->count(),
            ],
            'updates' => $updates->all(),
        ];
    }

    /**
     * @param  array<int, string>  $paths
     * @return array<int, object>
     */
    private function reviewedFindingRows(array $paths, ?int $limit): array
    {
        $keyLinks = DB::table('translation_workbench_key_findings')
            ->selectRaw('finding_id, MIN(key_id) as key_id')
            ->where('status', 'active')
            ->groupBy('finding_id');

        $query = DB::table('translation_workbench_findings as findings')
            ->join('translation_workbench_source_files as source_files', 'source_files.id', '=', 'findings.source_file_id')
            ->joinSub($keyLinks, 'key_links', function ($join): void {
                $join->on('key_links.finding_id', '=', 'findings.id');
            })
            ->join('translation_workbench_keys as keys', 'keys.id', '=', 'key_links.key_id')
            ->where('findings.status', 'active')
            ->where('keys.status', 'open')
            ->where('keys.review_status', 'reviewed')
            ->whereRaw("NULLIF(BTRIM(keys.translation_key), '') IS NOT NULL")
            ->when($paths !== [], function ($query) use ($paths): void {
                $query->where(function ($query) use ($paths): void {
                    foreach ($paths as $path) {
                        $query->orWhere('source_files.path', 'like', rtrim($path, '/') . '/%')
                            ->orWhere('source_files.path', $path);
                    }
                });
            })
            ->orderBy('source_files.path')
            ->orderBy('findings.source_line')
            ->select([
                'findings.id as finding_id',
                'findings.source_line',
                'findings.kind',
                'findings.function_name',
                'findings.raw_expression',
                'findings.literal_text',
                'findings.found_translation_key',
                'findings.existing_key',
                'findings.suggested_key as finding_suggested_key',
                'source_files.path as source_path',
                'keys.id as key_id',
                'keys.translation_key',
                'keys.suggested_key as key_suggested_key',
                'keys.key_type',
                'keys.is_dynamic_key',
                'keys.is_dynamic_multi',
            ]);

        if ($limit !== null && $limit > 0) {
            $query->limit($limit);
        }

        return $query->get()->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function planRow(object $row): array
    {
        $langState = $this->translationLangState((string) $row->translation_key);
        $base = [
            'finding_id' => (int) $row->finding_id,
            'key_id' => (int) $row->key_id,
            'source_path' => (string) $row->source_path,
            'source_line' => $row->source_line ? (int) $row->source_line : null,
            'kind' => (string) $row->kind,
            'function_name' => $row->function_name,
            'translation_key' => (string) $row->translation_key,
            'found_translation_key' => $row->found_translation_key,
            'existing_key' => $row->existing_key,
            'literal_text' => $row->literal_text,
            'raw_expression' => $row->raw_expression,
            'new_expression' => null,
            'state' => null,
            'reason' => null,
            'lang_state' => $langState,
        ];

        if ((bool) $row->is_dynamic_key || (bool) $row->is_dynamic_multi || str_starts_with((string) $row->kind, 'dynamic')) {
            return [
                ...$base,
                'state' => 'manual_review',
                'reason' => 'dynamic_expression_requires_dedicated_workflow',
            ];
        }

        if (! $langState['source_exists'] || ! $langState['target_main_exists']) {
            return [
                ...$base,
                'state' => 'missing_lang_values',
                'reason' => ! $langState['source_exists'] && ! $langState['target_main_exists']
                    ? 'source_and_target_lang_values_missing'
                    : (! $langState['source_exists'] ? 'source_lang_value_missing' : 'target_main_lang_value_missing'),
            ];
        }

        $sourcePath = base_path((string) $row->source_path);

        if (! File::exists($sourcePath)) {
            return [
                ...$base,
                'state' => 'missing_source_file',
                'reason' => 'source_file_no_longer_exists',
            ];
        }

        $rawExpression = trim((string) $row->raw_expression);

        if ($rawExpression === '') {
            return [
                ...$base,
                'state' => 'unsupported_expression',
                'reason' => 'raw_expression_missing',
            ];
        }

        $source = (string) File::get($sourcePath);

        if (! str_contains($source, $rawExpression)) {
            return [
                ...$base,
                'state' => 'stale_source',
                'reason' => 'raw_expression_not_found_in_current_source_file',
            ];
        }

        $replacement = $this->replaceFirstTranslationArgument($rawExpression, (string) $row->translation_key);

        if ($replacement === null) {
            return [
                ...$base,
                'state' => 'unsupported_expression',
                'reason' => 'first_translation_argument_is_not_a_literal_string',
            ];
        }

        if ($replacement === $rawExpression) {
            return [
                ...$base,
                'new_expression' => $replacement,
                'state' => 'already_current',
                'reason' => 'raw_expression_already_uses_translation_key',
            ];
        }

        return [
            ...$base,
            'new_expression' => $replacement,
            'state' => 'safe_update',
            'reason' => 'first_literal_argument_can_be_replaced_with_reviewed_translation_key',
        ];
    }

    /**
     * @return array{source_locale: string, target_main_locale: string, source_exists: bool, target_main_exists: bool}
     */
    private function translationLangState(string $translationKey): array
    {
        $sourceLocale = $this->sourceMainLocale();
        $targetLocale = $this->targetMainLocale();
        $rows = DB::table('translation_workbench_lang_values')
            ->where('translation_key', $translationKey)
            ->where('status', 'active')
            ->whereNotNull('value')
            ->whereIn('locale', [$sourceLocale, $targetLocale])
            ->pluck('locale')
            ->map(static fn(mixed $locale): string => LocaleCode::normalize((string) $locale))
            ->unique()
            ->values();

        return [
            'source_locale' => $sourceLocale,
            'target_main_locale' => $targetLocale,
            'source_exists' => $rows->contains($sourceLocale),
            'target_main_exists' => $rows->contains($targetLocale),
        ];
    }

    private function sourceMainLocale(): string
    {
        return LocaleCode::normalize((string) config('translation-workbench.source_locale', 'en')) ?: 'en';
    }

    private function targetMainLocale(): string
    {
        $configuredLocale = LocaleCode::normalize((string) (app(AppGeneralSettings::class)->locale ?? app()->getLocale()));
        $configuredLocale = $configuredLocale !== '' ? $configuredLocale : app()->getLocale();
        $activeLanguage = (string) (LocaleCode::parts($configuredLocale)['language'] ?? $configuredLocale);

        if ($activeLanguage !== '') {
            return $activeLanguage;
        }

        return Locale::query()
            ->where('is_active', true)
            ->ordered()
            ->value('normalized_code') ?: $configuredLocale;
    }

    private function replaceFirstTranslationArgument(string $rawExpression, string $translationKey): ?string
    {
        if (! preg_match('/^\\s*(@js\\s*\\(\\s*)?(__|trans)\\s*\\(\\s*([\\\'"])(.*?)\\3/s', $rawExpression, $match, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        $quote = $match[3][0];
        $value = $match[4][0];
        $valueOffset = $match[4][1];

        if ($value === $translationKey) {
            return $rawExpression;
        }

        $escapedKey = str_replace(['\\', $quote], ['\\\\', '\\' . $quote], $translationKey);

        return substr($rawExpression, 0, $valueOffset)
            . $escapedKey
            . substr($rawExpression, $valueOffset + strlen($value));
    }
}
