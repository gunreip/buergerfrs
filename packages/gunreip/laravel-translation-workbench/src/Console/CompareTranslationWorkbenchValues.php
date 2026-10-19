<?php

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueComparison;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchHistoricalValueComparison;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CompareTranslationWorkbenchValues extends Command
{
    protected $signature = 'translation-workbench:compare-values
        {--package-history : Compare historical Workbench entry values against Foundation stores}
        {--json : Output the complete comparison as JSON, including original values and row IDs}';

    protected $description = 'Read-only comparison of Legacy and Foundation database translation values; does not import or publish.';

    public function handle(TranslationWorkbenchValueComparison $comparison): int
    {
        $packageHistory = (bool) $this->option('package-history');
        $tables = $packageHistory ? TranslationWorkbenchHistoricalValueComparison::TABLES : ['translation_keys', 'translation_values', 'translation_workbench_lang_values'];
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                $this->error('Required table missing: '.$table);

                return self::FAILURE;
            }
        }
        $report = $packageHistory ? app(TranslationWorkbenchHistoricalValueComparison::class)->snapshot() : $comparison->snapshot();
        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

            return self::SUCCESS;
        }
        if ($packageHistory) {
            $this->components->info('Historical Workbench values / Foundation (read-only)');
            $this->table(['Category', 'Historical rows'], collect($report['counts'])->map(fn ($count, $category) => [$category, $count])->values()->all());
            $this->comment('Explicit keys only; suggested keys do not establish identity. Options without a locale remain observations.');
            $this->comment('Identical means exact text only, not matching status, review or migration eligibility. Use --json for all source rows and candidates.');

            return self::SUCCESS;
        }
        $this->components->info('Legacy / Foundation database comparison (read-only)');
        $this->table(['Category', 'Key/locale pairs'], collect($report['counts'])->map(fn ($count, $category) => [$category, $count])->values()->all());
        $this->table(['Review category', 'Key/locale pairs'], collect($report['review_counts'])->map(fn ($count, $category) => [$category, $count])->values()->all());
        $this->comment('Review categories describe evidence, not import decisions. Multiple rows take precedence, including one-sided duplicates. Null and empty string are not translations.');
        $this->line('Value-status differences: '.$report['status_differences']);
        $this->line('Pairs with multiple rows: '.$report['multiple_row_pairs']);
        $this->line('Rows without a usable key/locale: '.count($report['unassigned']));
        $this->comment('Exact text comparison; null and empty string remain distinct. Legacy status ok maps to active for comparison only.');
        $this->comment('No review/export eligibility inferred. Language files, fallback resolution and dynamic option values are outside this comparison.');
        $this->line('Use --json for original values, statuses, IDs and base-duplicate flags. Differences do not imply which side should be retained.');

        return self::SUCCESS;
    }
}
