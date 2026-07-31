<?php

// packages/gunreip/laravel-translation-workbench/src/Console/ClassifyLangNodeTypes.php

// php artisan translation-workbench:classify-lang-node-types
// php artisan translation-workbench:classify-lang-node-types --dry-run

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangNodeClassifier;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

#[Signature('translation-workbench:classify-lang-node-types
    {--dry-run : Report lang-node type changes without writing database rows.}')]
#[Description('Classify Translation Workbench keys as lang-file leaf, container, conflict or unknown nodes.')]
class ClassifyLangNodeTypes extends Command
{
    use WritesTranslationWorkbenchReports;

    public function handle(TranslationWorkbenchLangNodeClassifier $classifier): int
    {
        if (! $this->hasRequiredSchema()) {
            $this->error('Lang-node classification schema is missing. Run the workbench migrations first.');
            $this->writeTranslationWorkbenchReport(summary: [
                'error' => 'missing_schema',
            ]);

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $summary = $classifier->classify($dryRun);

        $this->components->info('Translation Workbench lang-node classification finished.');
        $this->line('Keys checked: ' . number_format($summary['keys_checked']));
        $this->line('Keys changed: ' . number_format($summary['keys_changed']));
        $this->line('Leaf keys: ' . number_format($summary['leaf']));
        $this->line('Container keys: ' . number_format($summary['container']));
        $this->line('Conflict keys: ' . number_format($summary['conflict']));
        $this->line('Unknown keys: ' . number_format($summary['unknown']));
        $this->line('Timeline events created: ' . number_format($summary['timeline_events_created']));

        if ($dryRun) {
            $this->warn('Dry run only: no lang-node classifications were written.');
        }

        $this->writeTranslationWorkbenchReport(summary: $summary);

        return self::SUCCESS;
    }

    private function hasRequiredSchema(): bool
    {
        return Schema::hasTable('translation_workbench_keys')
            && Schema::hasTable('translation_workbench_lang_values')
            && Schema::hasColumn('translation_workbench_keys', 'lang_node_type');
    }
}
