<?php

// packages/gunreip/laravel-translation-workbench/src/Console/RunTranslationWorkbench.php

// php artisan translation:workbench

// php artisan translation:workbench --dry-run
// php artisan translation:workbench --truncate
// php artisan translation:workbench --truncate --force-truncate
// php artisan translation:workbench --mark-obsolete
// php artisan translation:workbench --source-locale=en
// php artisan translation:workbench --paths=resources/views/components
// php artisan translation:workbench --write-lang-files
// php artisan translation:workbench --complete
// php artisan translation:workbench --skip-lang-node-classification
// php artisan translation:workbench --skip-shared-key-candidates
// php artisan translation:workbench --skip-code-update-apply

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\ConfirmsTranslationWorkbenchTruncate;
use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

#[Signature('translation:workbench
    {--paths= : Comma-separated relative paths passed to scan and dynamic option discovery.}
    {--source-locale=en : Source locale used for importing existing translations and dynamic option values.}
    {--dry-run : Report only; do not write database rows.}
    {--truncate : Truncate all Translation Workbench tables before rebuilding.}
    {--force-truncate : Skip the interactive safety confirmation for --truncate.}
    {--truncate-foundation : Truncate only the new foundation tables before syncing foundation rows.}
    {--mark-obsolete : Mark previously seen but now missing entries and occurrences as obsolete/stale.}
    {--skip-foundation : Skip syncing scanner findings into the new foundation tables.}
    {--skip-lang-values : Skip importing existing lang file values into the separate lang values table.}
    {--skip-import : Skip importing existing source language values.}
    {--skip-dynamic-options : Skip discovering dynamic option values.}
    {--skip-duplicates : Skip duplicate candidate detection.}
    {--skip-shared-key-candidates : Skip detecting new findings that may reuse reviewed shared translation keys.}
    {--skip-dynamic-classification : Skip classifying dynamic value sources.}
    {--skip-dynamic-resolution : Skip resolving unknown dynamic sources from option discoveries.}
    {--skip-dynamic-source-candidates : Skip discovering reviewable dynamic source candidates.}
    {--skip-lang-file-export : Skip the final lang file export dry-run/report step.}
    {--skip-lang-node-classification : Skip classifying keys as lang-file leaf/container/conflict nodes.}
    {--skip-code-update-plan : Skip the final code update planning report step.}
    {--skip-code-update-apply : Skip the final code update apply dry-run and patch report step.}
    {--write-lang-files : Write exported lang files as the final pipeline step. Without this option the export step is a dry-run report only.}
    {--complete : Run the full safe write workflow: mark obsolete, write lang files, apply safe code updates, then refresh reports.}')]
#[Description('Run the full Translation Workbench scan/import/discovery/diagnostics pipeline.')]
class RunTranslationWorkbench extends Command
{
    use ConfirmsTranslationWorkbenchTruncate;
    use WritesTranslationWorkbenchReports;

    public function handle(): int
    {
        $complete = (bool) $this->option('complete');
        $dryRun = (bool) $this->option('dry-run');
        $truncate = (bool) $this->option('truncate');
        $truncateFoundation = $truncate || (bool) $this->option('truncate-foundation');
        $hasTruncateRequest = $truncate || $truncateFoundation;
        $paths = $this->nullableOption('paths');
        $sourceLocale = $this->sourceLocale();
        $markObsolete = (bool) $this->option('mark-obsolete') || $complete;
        $writeLangFiles = ((bool) $this->option('write-lang-files') || $complete) && ! $dryRun;
        $pipelineStartedAt = microtime(true);
        $summary = [
            'steps_planned' => 0,
            'steps_succeeded' => 0,
            'steps_failed' => 0,
            'exit_code' => self::SUCCESS,
        ];
        $executedSteps = [];

        $this->components->info('Translation Workbench pipeline started.');

        if (! $this->confirmTranslationWorkbenchTruncate(
            $hasTruncateRequest,
            $dryRun,
            'The requested truncate option will delete Translation Workbench database rows before rebuilding.',
            'force-truncate',
            [
                'scope' => 'pipeline',
                'all_or_nothing' => true,
                'truncate_all' => $truncate,
                'truncate_foundation' => $truncateFoundation,
            ],
        )) {
            $summary['exit_code'] = self::FAILURE;
            $summary['steps_failed'] = 1;
            $this->writePipelineRawDataReport($summary, [], $this->elapsedMilliseconds($pipelineStartedAt));
            $this->logPipelineActivity(
                'translation_workbench.pipeline_cancelled',
                'Translation Workbench pipeline cancelled',
                [
                    'reason' => 'truncate_confirmation_declined',
                    'summary' => $summary,
                ],
            );

            return self::FAILURE;
        }

        $steps = [
            [
                'label' => 'Scan translation-capable source code',
                'command' => 'translation-workbench:scan',
                'arguments' => array_filter([
                    '--paths' => $paths,
                    '--dry-run' => $dryRun,
                    '--truncate' => $truncate,
                    '--force-truncate' => $truncate && ! $dryRun,
                    '--mark-obsolete' => $markObsolete,
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
                    '--force-truncate' => $truncateFoundation && ! $dryRun,
                    '--mark-obsolete' => $markObsolete,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        $deferLangValueImportUntilAfterWriteExport = ! (bool) $this->option('skip-lang-values') && ! $dryRun && $writeLangFiles;

        if (! (bool) $this->option('skip-lang-values') && ! $deferLangValueImportUntilAfterWriteExport) {
            $steps[] = [
                'label' => 'Import existing lang values',
                'command' => 'translation-workbench:import-lang-values',
                'arguments' => array_filter([
                    '--all-locales' => true,
                    '--dry-run' => $dryRun,
                    '--truncate-lang-values' => $truncate,
                    '--force-truncate' => $truncate && ! $dryRun,
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

        if (! (bool) $this->option('skip-shared-key-candidates')) {
            $steps[] = [
                'label' => 'Detect shared-key candidates',
                'command' => 'translation-workbench:detect-shared-key-candidates',
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

        if (! (bool) $this->option('skip-lang-node-classification')) {
            $steps[] = [
                'label' => 'Classify lang node types',
                'command' => 'translation-workbench:classify-lang-node-types',
                'arguments' => array_filter([
                    '--dry-run' => $dryRun,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if (! (bool) $this->option('skip-lang-file-export')) {
            $steps[] = [
                'label' => $writeLangFiles
                    ? 'Export reviewed lang files'
                    : 'Report reviewed lang file export',
                'command' => 'translation-workbench:export-lang-files',
                'arguments' => array_filter([
                    '--write' => $writeLangFiles,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if ($deferLangValueImportUntilAfterWriteExport && ! $complete) {
            $steps[] = [
                'label' => 'Refresh existing lang values after lang export',
                'command' => 'translation-workbench:import-lang-values',
                'arguments' => [
                    '--all-locales' => true,
                ],
            ];
        }

        if (! (bool) $this->option('skip-code-update-plan')) {
            $steps[] = [
                'label' => 'Plan reviewed code updates',
                'command' => 'translation-workbench:plan-code-updates',
                'arguments' => array_filter([
                    '--paths' => $paths,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if ((! $complete || $dryRun) && ! (bool) $this->option('skip-code-update-plan') && ! (bool) $this->option('skip-code-update-apply')) {
            $steps[] = [
                'label' => 'Report reviewed code update dry-run',
                'command' => 'translation-workbench:apply-code-updates',
                'arguments' => array_filter([
                    '--paths' => $paths,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if ($complete && ! $dryRun && ! (bool) $this->option('skip-code-update-plan') && ! (bool) $this->option('skip-code-update-apply')) {
            $steps[] = [
                'label' => 'Apply reviewed code updates',
                'command' => 'translation-workbench:apply-code-updates',
                'arguments' => array_filter([
                    '--paths' => $paths,
                    '--write' => true,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if ($complete && ! $dryRun) {
            $steps = [
                ...$steps,
                ...$this->completeRefreshSteps(
                    paths: $paths,
                    sourceLocale: $sourceLocale,
                    markObsolete: true,
                ),
            ];
        }

        $summary['steps_planned'] = count($steps);
        $this->logPipelineActivity(
            'translation_workbench.pipeline_started',
            'Translation Workbench pipeline started',
            [
                'options' => $this->activityOptionSnapshot(),
                'steps' => $this->activityStepsSnapshot($steps),
                'summary' => $summary,
            ],
        );

        foreach ($steps as $index => $step) {
            $this->newLine();
            $this->components->info(sprintf('[%d/%d] %s', $index + 1, count($steps), $step['label']));
            $stepStartedAt = microtime(true);
            $stepProperties = [
                'step' => [
                    'index' => $index + 1,
                    'total' => count($steps),
                    'label' => $step['label'],
                    'command' => $step['command'],
                    'arguments' => $this->activityArgumentsSnapshot($step['arguments']),
                ],
            ];

            $this->logPipelineActivity(
                'translation_workbench.step_started',
                sprintf('Translation Workbench step started: %s', $step['command']),
                $stepProperties,
            );

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
                $executedSteps[] = $this->pipelineStepExecutionSnapshot($step, $index + 1, count($steps), $stepStartedAt, self::FAILURE);
                $this->writePipelineRawDataReport($summary, $executedSteps, $this->elapsedMilliseconds($pipelineStartedAt));
                $this->logPipelineActivity(
                    'translation_workbench.step_failed',
                    sprintf('Translation Workbench step failed: %s', $step['command']),
                    array_replace_recursive($stepProperties, [
                        'duration_ms' => $this->elapsedMilliseconds($stepStartedAt),
                        'exception' => [
                            'class' => $exception::class,
                            'message' => $exception->getMessage(),
                        ],
                        'summary' => $summary,
                    ]),
                );
                $this->logPipelineActivity(
                    'translation_workbench.pipeline_failed',
                    'Translation Workbench pipeline failed',
                    [
                        'duration_ms' => $this->elapsedMilliseconds($pipelineStartedAt),
                        'summary' => $summary,
                    ],
                );

                return self::FAILURE;
            }

            if ($this->getOutput()->isVerbose()) {
                $this->line(sprintf('Command: php artisan %s %s', $step['command'], $this->formattedArguments($step['arguments'])));
            }

            if ($exitCode !== self::SUCCESS) {
                $this->components->error(sprintf('Translation Workbench pipeline stopped at "%s".', $step['command']));
                $summary['steps_failed']++;
                $summary['exit_code'] = $exitCode;
                $executedSteps[] = $this->pipelineStepExecutionSnapshot($step, $index + 1, count($steps), $stepStartedAt, $exitCode);
                $this->writePipelineRawDataReport($summary, $executedSteps, $this->elapsedMilliseconds($pipelineStartedAt));
                $this->logPipelineActivity(
                    'translation_workbench.step_failed',
                    sprintf('Translation Workbench step failed: %s', $step['command']),
                    array_replace_recursive($stepProperties, [
                        'duration_ms' => $this->elapsedMilliseconds($stepStartedAt),
                        'exit_code' => $exitCode,
                        'report' => $this->activityReportSummary($step['command']),
                        'summary' => $summary,
                    ]),
                );
                $this->logPipelineActivity(
                    'translation_workbench.pipeline_failed',
                    'Translation Workbench pipeline failed',
                    [
                        'duration_ms' => $this->elapsedMilliseconds($pipelineStartedAt),
                        'summary' => $summary,
                    ],
                );

                return $exitCode;
            }

            $summary['steps_succeeded']++;
            $executedSteps[] = $this->pipelineStepExecutionSnapshot($step, $index + 1, count($steps), $stepStartedAt, $exitCode);
            $this->logPipelineActivity(
                'translation_workbench.step_finished',
                sprintf('Translation Workbench step finished: %s', $step['command']),
                array_replace_recursive($stepProperties, [
                    'duration_ms' => $this->elapsedMilliseconds($stepStartedAt),
                    'exit_code' => $exitCode,
                    'report' => $this->activityReportSummary($step['command']),
                    'summary' => $summary,
                ]),
            );
        }

        $this->newLine();
        $this->components->info('Translation Workbench pipeline finished.');
        $this->writePipelineRawDataReport($summary, $executedSteps, $this->elapsedMilliseconds($pipelineStartedAt));
        $this->logPipelineActivity(
            'translation_workbench.pipeline_finished',
            'Translation Workbench pipeline finished',
            [
                'duration_ms' => $this->elapsedMilliseconds($pipelineStartedAt),
                'report' => $this->activityReportSummary((string) $this->getName()),
                'summary' => $summary,
            ],
        );

        return self::SUCCESS;
    }

    /**
     * Shared pipeline raw-data report.
     *
     * The raw_data base structure is centralized in WritesTranslationWorkbenchReports.
     * Do not add command-specific raw_data fields here or change the report
     * contract silently; discuss report contract changes first.
     *
     * @param  array<string, mixed>  $summary
     * @param  array<int, array<string, mixed>>  $steps
     */
    private function writePipelineRawDataReport(array $summary = [], array $steps = [], ?int $durationMs = null): void
    {
        $path = $this->translationWorkbenchReportPath((string) $this->getName());

        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode([
            'command' => $this->getName(),
            'generated_at' => now()->toISOString(),
            'options' => $this->activityOptionSnapshot(),
            'duration_ms' => $durationMs,
            'summary' => $summary,
            'steps' => $steps,
            'raw_data' => $this->translationWorkbenchReportRawData(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);

        $this->line('JSON report: ' . $path);
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
     * @return array<int, array{label: string, command: string, arguments: array<string, mixed>}>
     */
    private function completeRefreshSteps(?string $paths, string $sourceLocale, bool $markObsolete): array
    {
        $steps = [
            [
                'label' => 'Refresh source scan after code updates',
                'command' => 'translation-workbench:scan',
                'arguments' => array_filter([
                    '--paths' => $paths,
                    '--mark-obsolete' => $markObsolete,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ],
        ];

        if (! (bool) $this->option('skip-foundation')) {
            $steps[] = [
                'label' => 'Refresh foundation tables after code updates',
                'command' => 'translation-workbench:sync-foundation',
                'arguments' => array_filter([
                    '--paths' => $paths,
                    '--mark-obsolete' => $markObsolete,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if (! (bool) $this->option('skip-lang-values')) {
            $steps[] = [
                'label' => 'Refresh existing lang values after lang export',
                'command' => 'translation-workbench:import-lang-values',
                'arguments' => [
                    '--all-locales' => true,
                ],
            ];
        }

        if (! (bool) $this->option('skip-import')) {
            $steps[] = [
                'label' => 'Refresh existing source language values',
                'command' => 'translation-workbench:import-existing',
                'arguments' => [
                    '--source-locale' => $sourceLocale,
                ],
            ];
        }

        if (! (bool) $this->option('skip-dynamic-options')) {
            $steps[] = [
                'label' => 'Refresh dynamic option values',
                'command' => 'translation-workbench:discover-dynamic-options',
                'arguments' => array_filter([
                    '--paths' => $paths,
                    '--source-locale' => $sourceLocale,
                    '--sync' => true,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if (! (bool) $this->option('skip-duplicates')) {
            $steps[] = [
                'label' => 'Refresh duplicate candidates',
                'command' => 'translation-workbench:detect-duplicates',
                'arguments' => [],
            ];
        }

        if (! (bool) $this->option('skip-shared-key-candidates')) {
            $steps[] = [
                'label' => 'Refresh shared-key candidates',
                'command' => 'translation-workbench:detect-shared-key-candidates',
                'arguments' => [],
            ];
        }

        if (! (bool) $this->option('skip-dynamic-classification')) {
            $steps[] = [
                'label' => 'Refresh dynamic value source classification',
                'command' => 'translation-workbench:classify-dynamic-values',
                'arguments' => [],
            ];
        }

        if (! (bool) $this->option('skip-dynamic-resolution')) {
            $steps[] = [
                'label' => 'Refresh unknown dynamic source resolution',
                'command' => 'translation-workbench:resolve-unknown-dynamic-sources',
                'arguments' => [],
            ];
        }

        if (! (bool) $this->option('skip-dynamic-source-candidates')) {
            $steps[] = [
                'label' => 'Refresh dynamic source candidates',
                'command' => 'translation-workbench:discover-dynamic-source-candidates',
                'arguments' => [],
            ];
        }

        if (! (bool) $this->option('skip-lang-node-classification')) {
            $steps[] = [
                'label' => 'Refresh lang node types after workbench refresh',
                'command' => 'translation-workbench:classify-lang-node-types',
                'arguments' => [],
            ];
        }

        if (! (bool) $this->option('skip-lang-file-export')) {
            $steps[] = [
                'label' => 'Finalize reviewed lang files after refresh',
                'command' => 'translation-workbench:export-lang-files',
                'arguments' => [
                    '--write' => true,
                ],
            ];
        }

        if (! (bool) $this->option('skip-lang-values') && ! (bool) $this->option('skip-lang-file-export')) {
            $steps[] = [
                'label' => 'Refresh existing lang values after final lang export',
                'command' => 'translation-workbench:import-lang-values',
                'arguments' => [
                    '--all-locales' => true,
                ],
            ];
        }

        if (! (bool) $this->option('skip-lang-node-classification') && ! (bool) $this->option('skip-lang-file-export')) {
            $steps[] = [
                'label' => 'Refresh lang node types after final lang export',
                'command' => 'translation-workbench:classify-lang-node-types',
                'arguments' => [],
            ];
        }

        if (! (bool) $this->option('skip-lang-file-export')) {
            $steps[] = [
                'label' => 'Refresh reviewed lang file export report',
                'command' => 'translation-workbench:export-lang-files',
                'arguments' => [
                    '--suppress-dry-run-warning' => true,
                ],
            ];
        }

        if (! (bool) $this->option('skip-code-update-plan')) {
            $steps[] = [
                'label' => 'Refresh reviewed code update plan',
                'command' => 'translation-workbench:plan-code-updates',
                'arguments' => array_filter([
                    '--paths' => $paths,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        if (! (bool) $this->option('skip-code-update-plan') && ! (bool) $this->option('skip-code-update-apply')) {
            $steps[] = [
                'label' => 'Refresh reviewed code update dry-run',
                'command' => 'translation-workbench:apply-code-updates',
                'arguments' => array_filter([
                    '--paths' => $paths,
                    '--suppress-dry-run-warning' => true,
                ], static fn(mixed $value): bool => $value !== null && $value !== false),
            ];
        }

        return $steps;
    }

    /**
     * @param  array{label: string, command: string, arguments: array<string, mixed>}  $step
     * @return array<string, mixed>
     */
    private function pipelineStepExecutionSnapshot(array $step, int $index, int $total, float $startedAt, int $exitCode): array
    {
        return [
            'index' => $index,
            'total' => $total,
            'label' => $step['label'],
            'command' => $step['command'],
            'arguments' => $this->activityArgumentsSnapshot($step['arguments']),
            'duration_ms' => $this->elapsedMilliseconds($startedAt),
            'exit_code' => $exitCode,
            'report' => $this->pipelineCommandReportSnapshot($step['command']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function pipelineCommandReportSnapshot(string $commandName): array
    {
        $path = $this->translationWorkbenchReportPath($commandName);

        if (! File::exists($path)) {
            return [
                'path' => $path,
                'exists' => false,
            ];
        }

        try {
            $report = json_decode((string) File::get($path), true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            return [
                'path' => $path,
                'exists' => true,
                'readable' => false,
                'error' => $exception->getMessage(),
            ];
        }

        $rawData = is_array($report['raw_data'] ?? null) ? $report['raw_data'] : [];

        return [
            'path' => $path,
            'exists' => true,
            'readable' => true,
            'command' => $report['command'] ?? $commandName,
            'generated_at' => $report['generated_at'] ?? null,
            'write' => (bool) ($report['write'] ?? false),
            'dry_run' => (bool) (($report['summary']['dry_run'] ?? false)
                || ($report['dry_run'] ?? false)
                || (array_key_exists('write', $report) && ! (bool) $report['write'])),
            'summary' => is_array($report['summary'] ?? null) ? $report['summary'] : [],
            'plan_summary' => is_array($report['plan_summary'] ?? null) ? $report['plan_summary'] : [],
            'raw_summary' => [
                'files' => $rawData['files'] ?? null,
                'found' => $rawData['found'] ?? null,
                'scanned_paths' => is_array($rawData['scanned_paths'] ?? null) ? count($rawData['scanned_paths']) : null,
                'file_patterns' => is_array($rawData['file_patterns'] ?? null) ? count($rawData['file_patterns']) : null,
            ],
        ];
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

    /**
     * @param  array<string, mixed>  $properties
     */
    private function logPipelineActivity(string $event, string $description, array $properties = []): void
    {
        $this->recordTranslationWorkbenchActivity($event, $description, array_replace_recursive([
            'scope' => 'pipeline',
        ], $properties));
    }

    /**
     * @return array<string, mixed>
     */
    private function activityOptionSnapshot(): array
    {
        return collect($this->options())
            ->reject(static fn(mixed $value): bool => $value === null || $value === false || $value === '')
            ->map(static fn(mixed $value): mixed => is_array($value) ? array_values($value) : $value)
            ->all();
    }

    /**
     * @param  array<int, array{label: string, command: string, arguments: array<string, mixed>}>  $steps
     * @return array<int, array<string, mixed>>
     */
    private function activityStepsSnapshot(array $steps): array
    {
        return collect($steps)
            ->map(fn(array $step, int $index): array => [
                'index' => $index + 1,
                'label' => $step['label'],
                'command' => $step['command'],
                'arguments' => $this->activityArgumentsSnapshot($step['arguments']),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    private function activityArgumentsSnapshot(array $arguments): array
    {
        return collect($arguments)
            ->map(static fn(mixed $value): mixed => is_array($value) ? array_values($value) : $value)
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function activityReportSummary(string $commandName): array
    {
        $path = $this->translationWorkbenchReportPath($commandName);

        if (! File::exists($path)) {
            return [
                'path' => $path,
                'exists' => false,
            ];
        }

        try {
            $report = json_decode((string) File::get($path), true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            return [
                'path' => $path,
                'exists' => true,
                'readable' => false,
                'error' => $exception->getMessage(),
            ];
        }

        $rawData = is_array($report['raw_data'] ?? null) ? $report['raw_data'] : [];

        return [
            'path' => $path,
            'exists' => true,
            'readable' => true,
            'command' => $report['command'] ?? $commandName,
            'generated_at' => $report['generated_at'] ?? null,
            'files' => $rawData['files'] ?? null,
            'found' => $rawData['found'] ?? null,
            'scanned_paths' => $rawData['scanned_paths'] ?? [],
            'file_patterns_count' => is_countable($rawData['file_patterns'] ?? null)
                ? count($rawData['file_patterns'])
                : null,
        ];
    }

    private function translationWorkbenchReportPath(string $commandName): string
    {
        $filename = Str::of($commandName)
            ->replace(':', '-')
            ->replace('\\', '-')
            ->replace('/', '-')
            ->slug('-')
            ->append('.json')
            ->toString();

        return storage_path('translation-workbench' . DIRECTORY_SEPARATOR . $filename);
    }

    private function elapsedMilliseconds(float $startedAt): int
    {
        return (int) round((microtime(true) - $startedAt) * 1000);
    }
}
