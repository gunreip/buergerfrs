<?php

// packages/gunreip/laravel-translation-workbench/src/Console/RunTranslationWorkbench.php

// php artisan translation:workbench

// php artisan translation:workbench --dry-run
// php artisan translation:workbench --truncate
// php artisan translation:workbench --mark-obsolete
// php artisan translation:workbench --source-locale=en
// php artisan translation:workbench --paths=resources/views/components

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('translation:workbench
    {--paths= : Comma-separated relative paths passed to scan and dynamic option discovery.}
    {--source-locale=en : Source locale used for importing existing translations and dynamic option values.}
    {--dry-run : Report only; do not write database rows.}
    {--truncate : Truncate all Translation Workbench tables before rebuilding.}
    {--truncate-foundation : Truncate only the new foundation tables before syncing foundation rows.}
    {--mark-obsolete : Mark previously seen but now missing entries and occurrences as obsolete/stale.}
    {--skip-foundation : Skip syncing scanner findings into the new foundation tables.}
    {--skip-lang-values : Skip importing existing lang file values into the separate lang values table.}
    {--skip-import : Skip importing existing source language values.}
    {--skip-dynamic-options : Skip discovering dynamic option values.}
    {--skip-duplicates : Skip duplicate candidate detection.}
    {--skip-dynamic-classification : Skip classifying dynamic value sources.}
    {--skip-dynamic-resolution : Skip resolving unknown dynamic sources from option discoveries.}
    {--skip-dynamic-source-candidates : Skip discovering reviewable dynamic source candidates.}')]
#[Description('Run the full Translation Workbench scan/import/discovery/diagnostics pipeline.')]
class RunTranslationWorkbench extends Command
{
    use WritesTranslationWorkbenchReports;

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $truncate = (bool) $this->option('truncate');
        $truncateFoundation = $truncate || (bool) $this->option('truncate-foundation');
        $paths = $this->nullableOption('paths');
        $sourceLocale = $this->sourceLocale();
        $summary = [
            'steps_planned' => 0,
            'steps_succeeded' => 0,
            'steps_failed' => 0,
            'exit_code' => self::SUCCESS,
        ];

        $this->components->info('Translation Workbench pipeline started.');

        $steps = [
            [
                'label' => 'Scan translation-capable source code',
                'command' => 'translation-workbench:scan',
                'arguments' => array_filter([
                    '--paths' => $paths,
                    '--dry-run' => $dryRun,
                    '--truncate' => $truncate,
                    '--mark-obsolete' => (bool) $this->option('mark-obsolete'),
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ],
        ];

        if (! (bool) $this->option('skip-foundation')) {
            $steps[] = [
                'label' => 'Sync new foundation tables',
                'command' => 'translation-workbench:sync-foundation',
                'arguments' => array_filter([
                    '--paths' => $paths,
                    '--dry-run' => $dryRun,
                    '--truncate-foundation' => $truncateFoundation,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if (! (bool) $this->option('skip-lang-values')) {
            $steps[] = [
                'label' => 'Import existing lang values',
                'command' => 'translation-workbench:import-lang-values',
                'arguments' => array_filter([
                    '--all-locales' => true,
                    '--dry-run' => $dryRun,
                    '--truncate-lang-values' => $truncate,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if (! (bool) $this->option('skip-import')) {
            $steps[] = [
                'label' => 'Import existing source language values',
                'command' => 'translation-workbench:import-existing',
                'arguments' => array_filter([
                    '--source-locale' => $sourceLocale,
                    '--dry-run' => $dryRun,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if (! (bool) $this->option('skip-dynamic-options')) {
            $steps[] = [
                'label' => 'Discover dynamic option values',
                'command' => 'translation-workbench:discover-dynamic-options',
                'arguments' => array_filter([
                    '--paths' => $paths,
                    '--source-locale' => $sourceLocale,
                    '--sync' => ! $dryRun,
                    '--dry-run' => $dryRun,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if (! (bool) $this->option('skip-duplicates')) {
            $steps[] = [
                'label' => 'Detect duplicate candidates',
                'command' => 'translation-workbench:detect-duplicates',
                'arguments' => array_filter([
                    '--dry-run' => $dryRun,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if (! (bool) $this->option('skip-dynamic-classification')) {
            $steps[] = [
                'label' => 'Classify dynamic value sources',
                'command' => 'translation-workbench:classify-dynamic-values',
                'arguments' => array_filter([
                    '--dry-run' => $dryRun,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if (! (bool) $this->option('skip-dynamic-resolution')) {
            $steps[] = [
                'label' => 'Resolve unknown dynamic sources',
                'command' => 'translation-workbench:resolve-unknown-dynamic-sources',
                'arguments' => array_filter([
                    '--dry-run' => $dryRun,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if (! (bool) $this->option('skip-dynamic-source-candidates')) {
            $steps[] = [
                'label' => 'Discover dynamic source candidates',
                'command' => 'translation-workbench:discover-dynamic-source-candidates',
                'arguments' => array_filter([
                    '--dry-run' => $dryRun,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        $summary['steps_planned'] = count($steps);

        foreach ($steps as $index => $step) {
            $this->newLine();
            $this->components->info(sprintf('[%d/%d] %s', $index + 1, count($steps), $step['label']));

            try {
                $exitCode = $this->call($step['command'], $step['arguments']);
            } catch (Throwable $exception) {
                $this->components->error(sprintf(
                    'Translation Workbench pipeline failed at "%s": %s',
                    $step['command'],
                    $exception->getMessage(),
                ));
                $summary['steps_failed']++;
                $summary['exit_code'] = self::FAILURE;
                $this->writePipelineRawDataReport();

                return self::FAILURE;
            }

            if ($this->getOutput()->isVerbose()) {
                $this->line(sprintf('Command: php artisan %s %s', $step['command'], $this->formattedArguments($step['arguments'])));
            }

            if ($exitCode !== self::SUCCESS) {
                $this->components->error(sprintf('Translation Workbench pipeline stopped at "%s".', $step['command']));
                $summary['steps_failed']++;
                $summary['exit_code'] = $exitCode;
                $this->writePipelineRawDataReport();

                return $exitCode;
            }

            $summary['steps_succeeded']++;
        }

        $this->newLine();
        $this->components->info('Translation Workbench pipeline finished.');
        $this->writePipelineRawDataReport();

        return self::SUCCESS;
    }

    /**
     * Shared raw-data report.
     *
     * The report structure is centralized in WritesTranslationWorkbenchReports.
     * Do not add command-specific raw_data fields here or change the report
     * contract silently; discuss report contract changes first.
     */
    private function writePipelineRawDataReport(): void
    {
        $this->writeTranslationWorkbenchReport();
    }

    private function nullableOption(string $name): ?string
    {
        $value = trim((string) $this->option($name));

        return $value !== '' ? $value : null;
    }

    private function sourceLocale(): string
    {
        $sourceLocale = trim((string) $this->option('source-locale'));

        return $sourceLocale !== '' ? $sourceLocale : 'en';
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    private function formattedArguments(array $arguments): string
    {
        return collect($arguments)
            ->map(function (mixed $value, string $key): string {
                if ($value === true) {
                    return $key;
                }

                return $key . '=' . escapeshellarg((string) $value);
            })
            ->implode(' ');
    }
}
