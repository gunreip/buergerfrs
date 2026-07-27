<?php

// packages/gunreip/laravel-translation-workbench/src/Console/ApplyTranslationWorkbenchCodeUpdates.php

// php artisan translation-workbench:apply-code-updates
// php artisan translation-workbench:apply-code-updates --paths=resources/views/components
// php artisan translation-workbench:apply-code-updates --limit=10
// php artisan translation-workbench:apply-code-updates --limit=10 --write

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchCodeUpdateApplier;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

#[Signature('translation-workbench:apply-code-updates
    {--paths= : Comma-separated relative paths to limit the code update apply plan.}
    {--limit= : Maximum number of reviewed findings to inspect before applying safe updates.}
    {--write : Actually update source files. Without this option the command only reports what would be changed.}')]
#[Description('Apply safe reviewed Translation Workbench code replacements, dry-run by default.')]
class ApplyTranslationWorkbenchCodeUpdates extends Command
{
    public function handle(TranslationWorkbenchCodeUpdateApplier $applier): int
    {
        $write = (bool) $this->option('write');
        $report = $applier->apply(
            paths: $this->paths(),
            limit: $this->limit(),
            write: $write,
        );
        $diffPath = $this->writeDiff($report);
        $report['diff_path'] = $diffPath;
        $reportPath = $this->writeReport($report);
        $summary = $report['summary'];

        $this->components->info($write
            ? 'Translation Workbench code updates applied.'
            : 'Translation Workbench code update dry-run finished.');
        $this->line('Planned safe updates: ' . number_format((int) $summary['planned_safe_updates']));
        $this->line('Would apply: ' . number_format((int) $summary['would_apply']));
        $this->line('Applied: ' . number_format((int) $summary['applied']));
        $this->line('Skipped: ' . number_format((int) $summary['skipped']));
        $this->line('Stale source: ' . number_format((int) $summary['stale_source']));
        $this->line('Duplicate expressions: ' . number_format((int) $summary['duplicate_expression']));
        $this->line('Reviewed duplicates: ' . number_format((int) ($summary['duplicate_reviewed'] ?? 0)));
        $this->line('JSON report: ' . $reportPath);
        $this->line('Patch report: ' . $diffPath);

        if (! $write) {
            $this->warn('Dry run only: no source files were changed. Re-run with --write to apply safe updates.');
        }

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

    /**
     * @param  array<string, mixed>  $report
     */
    private function writeDiff(array $report): string
    {
        $path = storage_path('translation-workbench/' . Str::of((string) $this->getName())->replace(':', '-') . '.patch');
        $content = (string) ($report['diff']['content'] ?? '');

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content === '' ? '' : rtrim($content) . PHP_EOL);

        return $path;
    }
}
