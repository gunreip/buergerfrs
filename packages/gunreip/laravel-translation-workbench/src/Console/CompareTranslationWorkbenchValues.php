<?php

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchValueComparison;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CompareTranslationWorkbenchValues extends Command
{
    protected $signature = 'translation-workbench:compare-values
        {--json : Output the complete comparison as JSON, including original values and row IDs}';

    protected $description = 'Read-only comparison of Legacy and Foundation database translation values; does not import or publish.';

    public function handle(TranslationWorkbenchValueComparison $comparison): int
    {
        foreach (['translation_keys', 'translation_values', 'translation_workbench_lang_values'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->error('Required table missing: '.$table);

                return self::FAILURE;
            }
        }
        $report = $comparison->snapshot();
        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

            return self::SUCCESS;
        }
        $this->components->info('Legacy / Foundation database comparison (read-only)');
        $this->table(['Category', 'Key/locale pairs'], collect($report['counts'])->map(fn ($count, $category) => [$category, $count])->values()->all());
        $this->line('Value-status differences: '.$report['status_differences']);
        $this->line('Pairs with multiple rows: '.$report['multiple_row_pairs']);
        $this->line('Rows without a usable key/locale: '.count($report['unassigned']));
        $this->comment('Exact text comparison; null and empty string remain distinct. Legacy status ok maps to active for comparison only.');
        $this->comment('No review/export eligibility inferred. Language files, fallback resolution and dynamic option values are outside this comparison.');
        $this->line('Use --json for original values, statuses, IDs and base-duplicate flags. Differences do not imply which side should be retained.');

        return self::SUCCESS;
    }
}
