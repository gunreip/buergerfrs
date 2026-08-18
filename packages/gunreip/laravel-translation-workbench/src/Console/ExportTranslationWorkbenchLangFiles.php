<?php

// packages/gunreip/laravel-translation-workbench/src/Console/ExportTranslationWorkbenchLangFiles.php

// php artisan translation-workbench:export-lang-files
// php artisan translation-workbench:export-lang-files --locale=de --namespace=ui
// php artisan translation-workbench:export-lang-files --write
// php artisan translation-workbench:export-lang-files --locale=de --namespace=ui --write
// php artisan translation-workbench:export-lang-files --suppress-dry-run-warning

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchLangFileExporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

#[Signature('translation-workbench:export-lang-files
    {--locale=* : Locale(s) to export. Defaults to all locales with active Workbench lang values.}
    {--namespace=* : Namespace file(s) to export, for example ui or admin. Defaults to all namespaces.}
    {--write : Write lang files. Without this option the command only reports the export plan.}
    {--suppress-dry-run-warning : Suppress the dry-run warning when the command is used as an orchestrated report refresh step.}')]
#[Description('Export reviewed Translation Workbench lang values into Laravel lang files.')]
class ExportTranslationWorkbenchLangFiles extends Command
{
    public function handle(TranslationWorkbenchLangFileExporter $exporter): int
    {
        $write = (bool) $this->option('write');
        $summary = $exporter->export(
            locales: (array) $this->option('locale'),
            namespaces: (array) $this->option('namespace'),
            write: $write,
        );
        $reportPath = $this->writeReport($summary);

        $this->components->info($write
            ? 'Translation Workbench lang file export finished.'
            : 'Translation Workbench lang file export dry-run finished.');
        $this->line('Locales: ' . implode(', ', $summary['locales'] ?: ['-']));
        $this->line('Namespaces: ' . implode(', ', $summary['namespaces'] ?: ['-']));
        $this->line('Files: ' . number_format((int) $summary['files']));
        $this->line('Exportable values: ' . number_format((int) $summary['values_exportable']));
        $this->line('New values: ' . number_format((int) $summary['values_new']));
        $this->line('Changed values: ' . number_format((int) $summary['values_changed']));
        $this->line('Unchanged values: ' . number_format((int) $summary['values_unchanged']));
        $this->line('Pruned values: ' . number_format((int) $summary['values_pruned']));
        $this->line('Conflicts: ' . number_format((int) $summary['values_conflicted']));
        $this->line('Active scope locales: ' . implode(', ', $summary['active_scope']['locales'] ?: ['-']));
        $this->line('Active scope exportable values: ' . number_format((int) $summary['active_scope']['values_exportable']));
        $this->line('Active scope source values: ' . number_format((int) $summary['active_scope']['source_values']));
        $this->line('Active scope target main values: ' . number_format((int) $summary['active_scope']['target_main_values']));
        $this->line('Active scope target main missing: ' . number_format((int) $summary['active_scope']['target_main_missing']));
        $this->line('Active scope target main extras: ' . number_format((int) $summary['active_scope']['target_main_extra']));
        $this->line('Active scope source/target balanced: ' . ($summary['active_scope']['target_main_balanced'] ? 'yes' : 'no'));
        $this->line('Active scope target sub values: ' . number_format((int) $summary['active_scope']['target_sub_values']));
        $this->line('Files written: ' . number_format((int) $summary['files_written']));
        $this->line('Timeline events created: ' . number_format((int) $summary['timeline_events_created']));
        $this->line('JSON report: ' . $reportPath);

        if (! $write && ! (bool) $this->option('suppress-dry-run-warning')) {
            $this->warn('Dry run only: no lang files were written. Re-run with --write to apply the export.');
        }

        if ((int) $summary['values_conflicted'] > 0) {
            $this->warn('Some values were not exportable because nested lang-key paths conflict with scalar values. See the JSON report.');
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    private function writeReport(array $summary): string
    {
        $path = storage_path('translation-workbench/' . Str::of((string) $this->getName())->replace(':', '-') . '.json');
        $directory = dirname($path);

        File::ensureDirectoryExists($directory);
        @chmod($directory, 0777);

        if (File::exists($path) && ! is_writable($path)) {
            @unlink($path);
        }

        File::put($path, json_encode([
            'command' => $this->getName(),
            'generated_at' => now()->toISOString(),
            'write' => (bool) $this->option('write'),
            'dry_run' => ! (bool) $this->option('write'),
            'summary' => collect($summary)->except('plans')->all(),
            'plans' => $summary['plans'],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        @chmod($path, 0666);

        return $path;
    }
}
