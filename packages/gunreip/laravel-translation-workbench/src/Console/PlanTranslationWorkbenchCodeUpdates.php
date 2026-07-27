<?php

// packages/gunreip/laravel-translation-workbench/src/Console/PlanTranslationWorkbenchCodeUpdates.php

// php artisan translation-workbench:plan-code-updates
// php artisan translation-workbench:plan-code-updates --paths=resources/views/components
// php artisan translation-workbench:plan-code-updates --limit=100

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchCodeUpdatePlanner;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

#[Signature('translation-workbench:plan-code-updates
    {--paths= : Comma-separated relative paths to limit the code update plan.}
    {--limit= : Maximum number of reviewed findings to inspect.}')]
#[Description('Plan safe code replacements for reviewed Translation Workbench keys without changing source files.')]
class PlanTranslationWorkbenchCodeUpdates extends Command
{
    public function handle(TranslationWorkbenchCodeUpdatePlanner $planner): int
    {
        $report = $planner->plan(
            paths: $this->paths(),
            limit: $this->limit(),
        );
        $reportPath = $this->writeReport($report);
        $summary = $report['summary'];

        $this->components->info('Translation Workbench code update plan finished.');
        $this->line('Reviewed findings: ' . number_format((int) $summary['reviewed_findings']));
        $this->line('Safe updates: ' . number_format((int) $summary['safe_updates']));
        $this->line('Already current: ' . number_format((int) $summary['already_current']));
        $this->line('Manual review: ' . number_format((int) $summary['manual_review']));
        $this->line('Missing lang values: ' . number_format((int) $summary['missing_lang_values']));
        $this->line('Stale source: ' . number_format((int) $summary['stale_source']));
        $this->line('Missing source files: ' . number_format((int) $summary['missing_source_file']));
        $this->line('Unsupported expressions: ' . number_format((int) $summary['unsupported_expression']));
        $this->line('JSON report: ' . $reportPath);

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function paths(): array
    {
        $paths = trim((string) $this->option('paths'));

        if ($paths === '') {
            return [];
        }

        return collect(explode(',', $paths))
            ->map(static fn(string $path): string => trim(str_replace('\\', '/', $path)))
            ->filter()
            ->values()
            ->all();
    }

    private function limit(): ?int
    {
        $limit = trim((string) $this->option('limit'));

        if ($limit === '') {
            return null;
        }

        return max(1, (int) $limit);
    }

    /**
     * @param  array<string, mixed>  $report
     */
    private function writeReport(array $report): string
    {
        $path = storage_path('translation-workbench/' . Str::of((string) $this->getName())->replace(':', '-') . '.json');

        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode([
            'command' => $this->getName(),
            ...$report,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);

        return $path;
    }
}
