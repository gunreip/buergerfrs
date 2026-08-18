<?php

// packages/gunreip/laravel-translation-workbench/src/Console/DetectSuspiciousKeyedAdditions.php

// php artisan translation-workbench:detect-suspicious-keyed-additions
// php artisan translation-workbench:detect-suspicious-keyed-additions --paths=resources/views/components
// php artisan translation-workbench:detect-suspicious-keyed-additions --since-hours=24
// php artisan translation-workbench:detect-suspicious-keyed-additions --all
// php artisan translation-workbench:detect-suspicious-keyed-additions --fail-on-suspicious

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchFinding;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchKey;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchLangValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchReview;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchTimelineEvent;
use Gunreip\TranslationWorkbench\Scanner\DiscoveredTranslation;
use Gunreip\TranslationWorkbench\Scanner\TranslationWorkbenchScanner;
use Carbon\CarbonInterface;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

#[Signature('translation-workbench:detect-suspicious-keyed-additions
    {--paths= : Comma-separated relative paths to scan. Defaults to translation-workbench.paths.}
    {--source-locale=en : Source locale used to check whether the keyed call already exists in lang/* files.}
    {--since-hours=72 : Only report newly seen keyed additions from the last N hours unless --all is used.}
    {--all : Report all suspicious keyed additions, including historical rows.}
    {--skip-raw-data : Write only suspicious-key results and summary, without the centralized raw scanner data.}
    {--fail-on-suspicious : Return a failing exit code when suspicious keyed additions are found.}')]
#[Description('Detect code-level translation keys that appear without a reviewed Workbench or code-update provenance.')]
class DetectSuspiciousKeyedAdditions extends Command
{
    use WritesTranslationWorkbenchReports;

    public function handle(TranslationWorkbenchScanner $scanner): int
    {
        if (! $this->hasRequiredTables()) {
            $this->error('Required Translation Workbench tables are missing. Run the workbench migrations first.');
            $this->writeReport(collect(), [
                'error' => 'missing_required_tables',
                'suspicious' => 0,
            ]);

            return self::FAILURE;
        }

        $paths = $this->paths();
        $sourceLocale = trim((string) $this->option('source-locale')) ?: 'en';
        $all = (bool) $this->option('all');
        $cutoff = $this->sinceCutoffContext($all);
        $since = $cutoff['since_at'];
        $keyedItems = $scanner
            ->scan($paths)
            ->filter(static fn(DiscoveredTranslation $item): bool => $item->kind === 'key' && filled($item->translationKey))
            ->values();

        $rows = $keyedItems
            ->map(fn(DiscoveredTranslation $item): ?array => $this->suspiciousRow($item, $since, $all))
            ->filter()
            ->filter(static fn(array $row): bool => empty($row['review_decision'])
                || (string) ($row['review_decision'] ?? '') === 'needs_literal_restore')
            ->values();

        $openRows = $rows->filter(static fn(array $row): bool => empty($row['review_decision']));
        $reviewedRows = $rows->filter(static fn(array $row): bool => ! empty($row['review_decision']));
        $summary = [
            'keyed_findings_scanned' => $keyedItems->count(),
            'report_rows' => $rows->count(),
            'suspicious' => $openRows->count(),
            'reviewed' => $reviewedRows->count(),
            'recent_only' => ! $all,
            'source_locale' => $sourceLocale,
            'since_hours' => $all ? null : (int) $this->option('since-hours'),
            'since_at' => $all ? null : $since?->toISOString(),
            'default_since_at' => $all ? null : $cutoff['default_since_at']?->toISOString(),
            'previous_detector_report_generated_at' => $all ? null : $cutoff['previous_detector_report_generated_at']?->toISOString(),
            'pipeline_report_generated_at' => $all ? null : $cutoff['pipeline_report_generated_at']?->toISOString(),
            'fail_on_suspicious' => (bool) $this->option('fail-on-suspicious'),
        ];

        $this->components->info('Translation Workbench suspicious keyed addition detection finished.');
        $this->line('Keyed findings scanned: ' . number_format($summary['keyed_findings_scanned']));
        $this->line('Open suspicious keyed additions: ' . number_format($summary['suspicious']));
        $this->line('Reviewed suspicious keyed additions: ' . number_format($summary['reviewed']));

        if ($openRows->isNotEmpty()) {
            $this->warn('Review suspicious keyed additions before committing code changes.');
        }

        $this->writeReport($rows, $summary);

        return $openRows->isNotEmpty() && (bool) $this->option('fail-on-suspicious')
            ? self::FAILURE
            : self::SUCCESS;
    }

    private function hasRequiredTables(): bool
    {
        return collect([
            'translation_workbench_findings',
            'translation_workbench_source_files',
            'translation_workbench_keys',
            'translation_workbench_key_findings',
            'translation_workbench_lang_values',
            'translation_workbench_reviews',
            'translation_workbench_timeline_events',
        ])->every(static fn(string $table): bool => Schema::hasTable($table));
    }

    /**
     * @return array<int, string>|null
     */
    private function paths(): ?array
    {
        $paths = trim((string) $this->option('paths'));

        if ($paths === '') {
            return null;
        }

        return collect(explode(',', $paths))
            ->map(static fn(string $path): string => trim($path))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Keep the default 72-hour window as the minimum lookback, but extend it
     * to the previous detector or pipeline report timestamp when either report
     * is older. Do not silently shrink this window; it is a guardrail against
     * direct translation-key edits that bypass the Workbench review workflow.
     *
     * @return array{since_at: CarbonInterface|null, default_since_at: CarbonInterface|null, previous_detector_report_generated_at: CarbonInterface|null, pipeline_report_generated_at: CarbonInterface|null}
     */
    private function sinceCutoffContext(bool $all): array
    {
        if ($all) {
            return [
                'since_at' => null,
                'default_since_at' => null,
                'previous_detector_report_generated_at' => null,
                'pipeline_report_generated_at' => null,
            ];
        }

        $hours = max(1, (int) $this->option('since-hours'));
        $defaultSinceAt = now()->subHours($hours);
        $previousDetectorReportGeneratedAt = $this->reportGeneratedAt('translation-workbench:detect-suspicious-keyed-additions');
        $pipelineReportGeneratedAt = $this->reportGeneratedAt('translation:workbench');

        $sinceAt = collect([
            $defaultSinceAt,
            $previousDetectorReportGeneratedAt,
            $pipelineReportGeneratedAt,
        ])
            ->filter(static fn(mixed $value): bool => $value instanceof CarbonInterface)
            ->sortBy(static fn(CarbonInterface $value): int => $value->getTimestamp())
            ->first();

        return [
            'since_at' => $sinceAt,
            'default_since_at' => $defaultSinceAt,
            'previous_detector_report_generated_at' => $previousDetectorReportGeneratedAt,
            'pipeline_report_generated_at' => $pipelineReportGeneratedAt,
        ];
    }

    private function reportGeneratedAt(string $commandName): ?CarbonInterface
    {
        $path = storage_path(
            'translation-workbench' . DIRECTORY_SEPARATOR . Str::of($commandName)
                ->replace(':', '-')
                ->replace('\\', '-')
                ->replace('/', '-')
                ->slug('-')
                ->append('.json')
                ->toString(),
        );

        if (! File::exists($path)) {
            return null;
        }

        $data = json_decode((string) File::get($path), true);
        $generatedAt = is_array($data) ? trim((string) ($data['generated_at'] ?? '')) : '';

        if ($generatedAt === '') {
            return null;
        }

        try {
            return Carbon::parse($generatedAt);
        } catch (\Throwable) {
            return null;
        }
    }

    private function suspiciousRow(DiscoveredTranslation $item, ?CarbonInterface $since, bool $all): ?array
    {
        $sourceLocale = trim((string) $this->option('source-locale')) ?: 'en';
        $sourceLangContext = $this->sourceLangContext((string) $item->translationKey, $sourceLocale);
        $usageContext = $this->usageContext((string) $item->translationKey);

        $finding = TranslationWorkbenchFinding::query()
            ->with(['sourceFile'])
            ->where('source_signature', $item->sourceSignature)
            ->first();

        if (! $finding) {
            $latestSuspiciousReview = $this->latestSuspiciousReviewDecisionForItem($item);

            return [
                'state' => 'missing_finding',
                'reason' => 'The scanner found a keyed translation call, but no synced Workbench finding exists yet.',
                ...$this->itemContext($item),
                ...$this->reviewDecisionContext($latestSuspiciousReview),
                'finding_id' => null,
                'key_id' => null,
                'finding_first_seen_at' => null,
                'key_review_status' => null,
                'has_active_key_link' => false,
                'has_reviewed_key' => false,
                'has_code_update_applied_event' => false,
                ...$sourceLangContext,
                ...$usageContext,
            ];
        }

        if (! $all && $since && $finding->first_seen_at instanceof CarbonInterface && $finding->first_seen_at->lt($since)) {
            return null;
        }

        $key = $this->linkedKeyForFinding($finding, (string) $item->translationKey);
        $hasActiveKeyLink = $key instanceof TranslationWorkbenchKey;
        $hasReviewedKey = $key instanceof TranslationWorkbenchKey
            && (string) $key->review_status === 'reviewed'
            && (string) $key->translation_key === (string) $item->translationKey;
        $hasCodeUpdateAppliedEvent = $this->hasCodeUpdateAppliedEvent($finding, $key, $item);
        $hasReviewEvent = $this->hasReviewEvent($finding, $key, $item);
        $latestSuspiciousReview = $this->latestSuspiciousReviewDecision($finding, $key, $item);
        $hasSuspiciousReviewDecision = $latestSuspiciousReview instanceof TranslationWorkbenchReview;

        if ($hasReviewedKey || $hasCodeUpdateAppliedEvent || $hasReviewEvent) {
            return null;
        }

        $state = match (true) {
            ($sourceLangContext['source_lang_value_exists'] ?? false) && ($usageContext['active_usage_count'] ?? 0) > 1 => 'existing_lang_value_without_provenance',
            ($sourceLangContext['source_lang_value_exists'] ?? false) => 'unreviewed_keyed_call_with_source_lang_value',
            default => 'orphaned_direct_key',
        };

        return [
            'state' => $state,
            'reason' => 'A translation key is already present in code, but no reviewed key, review event or code-update-applied timeline event was found for this occurrence.',
            ...$this->itemContext($item),
            ...$this->reviewDecisionContext($latestSuspiciousReview),
            'finding_id' => $finding->id,
            'key_id' => $key?->id,
            'finding_status' => (string) $finding->status,
            'finding_review_status' => (string) ($finding->review_status ?? ''),
            'finding_first_seen_at' => $finding->first_seen_at?->toISOString(),
            'finding_last_seen_at' => $finding->last_seen_at?->toISOString(),
            'key_status' => $key?->status,
            'key_review_status' => $key?->review_status,
            'has_active_key_link' => $hasActiveKeyLink,
            'has_reviewed_key' => false,
            'has_code_update_applied_event' => false,
            'has_review_event' => false,
            'has_suspicious_review_decision' => $hasSuspiciousReviewDecision,
            ...$sourceLangContext,
            ...$usageContext,
        ];
    }

    private function linkedKeyForFinding(TranslationWorkbenchFinding $finding, string $translationKey): ?TranslationWorkbenchKey
    {
        $keyId = DB::table('translation_workbench_key_findings as key_findings')
            ->join('translation_workbench_keys as keys', 'keys.id', '=', 'key_findings.key_id')
            ->where('key_findings.finding_id', $finding->id)
            ->where('key_findings.status', 'active')
            ->where('keys.translation_key', $translationKey)
            ->orderByDesc('keys.review_status')
            ->orderBy('keys.id')
            ->value('keys.id');

        return $keyId ? TranslationWorkbenchKey::query()->find((int) $keyId) : null;
    }

    private function hasCodeUpdateAppliedEvent(
        TranslationWorkbenchFinding $finding,
        ?TranslationWorkbenchKey $key,
        DiscoveredTranslation $item,
    ): bool {
        return TranslationWorkbenchTimelineEvent::query()
            ->where('event_type', 'code_update_applied')
            ->where(function ($query) use ($finding, $key, $item): void {
                $query->where('finding_id', $finding->id);

                if ($key) {
                    $query->orWhere('key_id', $key->id);
                }

                $query->orWhere(function ($query) use ($item): void {
                    $query
                        ->where('context->source_path', $item->sourcePath)
                        ->where('context->translation_key', $item->translationKey);

                    if ($item->sourceLine !== null) {
                        $query->where('context->source_line', $item->sourceLine);
                    }
                });
            })
            ->exists();
    }

    private function latestSuspiciousReviewDecision(
        TranslationWorkbenchFinding $finding,
        ?TranslationWorkbenchKey $key,
        DiscoveredTranslation $item,
    ): ?TranslationWorkbenchReview {
        return TranslationWorkbenchReview::query()
            ->where('review_type', 'suspicious_key_provenance')
            ->where(function ($query) use ($finding, $key, $item): void {
                $query
                    ->where('finding_id', $finding->id)
                    ->orWhere('meta->source_signature', $item->sourceSignature);

                if ($key) {
                    $query->orWhere('key_id', $key->id);
                }
            })
            ->where('meta->translation_key', $item->translationKey)
            ->latest('reviewed_at')
            ->latest('id')
            ->first();
    }

    private function latestSuspiciousReviewDecisionForItem(DiscoveredTranslation $item): ?TranslationWorkbenchReview
    {
        return TranslationWorkbenchReview::query()
            ->where('review_type', 'suspicious_key_provenance')
            ->where('meta->source_signature', $item->sourceSignature)
            ->where('meta->translation_key', $item->translationKey)
            ->latest('reviewed_at')
            ->latest('id')
            ->first();
    }

    /**
     * @return array<string, mixed>
     */
    private function reviewDecisionContext(?TranslationWorkbenchReview $review): array
    {
        return [
            'review_decision' => $review?->decision,
            'review_decision_label' => $review ? $this->reviewDecisionLabel((string) $review->decision) : null,
            'reviewed_at' => $review?->reviewed_at?->toISOString(),
            'reviewed_by_user_id' => $review?->reviewed_by_user_id,
        ];
    }

    private function reviewDecisionLabel(string $decision): string
    {
        return match ($decision) {
            'mark_as_valid_existing_key' => 'Accepted existing key',
            'needs_literal_restore' => 'Will restore literal',
            'needs_key_review' => 'Queued for key review',
            'ignore_for_now' => 'Deferred',
            default => str($decision)->replace('_', ' ')->headline()->toString(),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function sourceLangContext(string $translationKey, string $sourceLocale): array
    {
        $values = TranslationWorkbenchLangValue::query()
            ->where('locale', $sourceLocale)
            ->where('translation_key', $translationKey)
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 WHEN status = 'obsolete' THEN 1 ELSE 2 END")
            ->orderBy('id')
            ->get();
        $primary = $values->first();

        return [
            'source_locale' => $sourceLocale,
            'source_lang_value_exists' => $values->isNotEmpty(),
            'source_lang_value_count' => $values->count(),
            'source_lang_active_value_count' => $values->where('status', 'active')->count(),
            'source_lang_status' => $primary?->status,
            'source_lang_namespace' => $primary?->namespace,
            'source_lang_key' => $primary?->lang_key,
            'source_lang_value' => $primary?->value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function usageContext(string $translationKey): array
    {
        $keyIds = TranslationWorkbenchKey::query()
            ->where('translation_key', $translationKey)
            ->pluck('id');

        $activeUsageCount = $keyIds->isEmpty()
            ? 0
            : DB::table('translation_workbench_key_findings')
                ->whereIn('key_id', $keyIds)
                ->where('status', 'active')
                ->count();
        $reviewedUsageCount = $keyIds->isEmpty()
            ? 0
            : DB::table('translation_workbench_key_findings')
                ->join('translation_workbench_keys', 'translation_workbench_keys.id', '=', 'translation_workbench_key_findings.key_id')
                ->whereIn('translation_workbench_key_findings.key_id', $keyIds)
                ->where('translation_workbench_key_findings.status', 'active')
                ->where('translation_workbench_keys.review_status', 'reviewed')
                ->count();
        $directCodeUsageCount = TranslationWorkbenchFinding::query()
            ->where('status', 'active')
            ->where(function ($query) use ($translationKey): void {
                $query
                    ->where('found_translation_key', $translationKey)
                    ->orWhere('existing_key', $translationKey);
            })
            ->count();

        return [
            'key_record_count' => $keyIds->count(),
            'active_usage_count' => $activeUsageCount,
            'reviewed_usage_count' => $reviewedUsageCount,
            'direct_code_usage_count' => $directCodeUsageCount,
        ];
    }

    private function hasReviewEvent(
        TranslationWorkbenchFinding $finding,
        ?TranslationWorkbenchKey $key,
        DiscoveredTranslation $item,
    ): bool {
        return DB::table('translation_workbench_reviews')
            ->where(function ($query) use ($finding, $key): void {
                $query->where('finding_id', $finding->id);

                if ($key) {
                    $query->orWhere('key_id', $key->id);
                }
            })
            ->whereIn('decision', [
                'suggested_key_accepted',
                'translation_key_updated',
                'translation_key_bulk_equalized',
                'translation_values_saved',
            ])
            ->where(function ($query) use ($item): void {
                $query
                    ->where('new_values->translation_key', $item->translationKey)
                    ->orWhere('meta->translation_key', $item->translationKey);
            })
            ->exists();
    }

    /**
     * @return array<string, mixed>
     */
    private function itemContext(DiscoveredTranslation $item): array
    {
        return [
            'source_path' => $item->sourcePath,
            'source_signature' => $item->sourceSignature,
            'source_line' => $item->sourceLine,
            'kind' => $item->kind,
            'function_name' => $item->functionName,
            'raw_expression' => $item->rawExpression,
            'translation_key' => $item->translationKey,
            'existing_key' => $item->existingKey,
            'suggested_key' => $item->suggestedKey,
            'literal_text_suggested' => $item->literalTextSuggested,
        ];
    }

    /**
     * Command-specific results are kept separate from the centralized raw_data
     * scanner report. Do not change raw_data here; extend the results/summary
     * structure only after the workflow meaning is clear.
     *
     * @param  Collection<int, array<string, mixed>>  $rows
     * @param  array<string, mixed>  $summary
     */
    private function writeReport(Collection $rows, array $summary): void
    {
        $directory = storage_path('translation-workbench');
        $path = $directory . DIRECTORY_SEPARATOR . Str::of((string) $this->getName())->replace(':', '-')->append('.json');

        File::ensureDirectoryExists($directory);
        @chmod($directory, 0777);

        if (File::exists($path) && ! is_writable($path)) {
            @unlink($path);
        }

        $payload = [
            'command' => $this->getName(),
            'generated_at' => now()->toISOString(),
            'summary' => $summary,
            'results' => $rows->take(200)->values()->all(),
        ];

        if ((bool) $this->option('skip-raw-data')) {
            $payload['summary']['raw_data_skipped'] = true;
        } else {
            $payload['raw_data'] = $this->translationWorkbenchReportRawData();
        }

        File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);
        @chmod($path, 0666);

        $this->line('JSON report: ' . $path);
    }
}
