<?php

// app/Console/Commands/ClearProject.php

// php artisan clear:project
// php artisan clear:project --rebuild-views
// php artisan clear:project --build-assets
// php artisan clear:project --rebuild-views --build-assets
// php artisan clear:project --tw-graph-tests
// php artisan clear:project --watch
// php artisan clear:project --watch --watch-interval=2

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Throwable;

class ClearProject extends Command
{
    protected $signature = 'clear:project
        {--rebuild-views : Rebuild compiled Blade views after clearing caches. Useful for deploys, risky in local mixed-user runtimes.}
        {--build-assets : Rebuild Vite assets with npm run build after clearing caches. Needed when CSS/JS files changed and no Vite dev server is running.}
        {--tw-graph-tests : Run the TW-Graph Pest suite and write a visible report after clearing caches.}
        {--watch : Keep running and rebuild views/assets whenever relevant project files change.}
        {--watch-interval=1 : Poll interval in seconds for --watch.}';

    protected $description = 'Clears project caches and optionally rebuilds Blade views and Vite assets.';

    public function handle(): int
    {
        if ((bool) $this->option('watch')) {
            return $this->watch();
        }

        return $this->clearProject(
            rebuildViews: (bool) $this->option('rebuild-views'),
            buildAssets: (bool) $this->option('build-assets'),
            runTwGraphTests: (bool) $this->option('tw-graph-tests'),
        );
    }

    private function clearProject(bool $rebuildViews, bool $buildAssets, bool $runTwGraphTests = false): int
    {
        $steps = [
            ['label' => 'Clear optimized framework caches', 'command' => 'optimize:clear'],
            ['label' => 'Clear compiled Blade views', 'command' => 'view:clear'],
        ];

        if ($rebuildViews) {
            $steps[] = ['label' => 'Rebuild compiled Blade views', 'command' => 'view:cache'];
        }

        foreach ($steps as $step) {
            $this->line('▶ ' . $step['label']);

            try {
                $exitCode = Artisan::call($step['command']);
            } catch (Throwable $exception) {
                $this->error('❌ Failed: ' . $step['command']);
                $this->error(trim($exception->getMessage()));

                return self::FAILURE;
            }

            $output = trim(Artisan::output());

            if ($output !== '') {
                $this->line($output);
            }

            if ($exitCode !== self::SUCCESS) {
                $this->error('❌ Failed: ' . $step['command']);

                return self::FAILURE;
            }
        }

        if ($buildAssets) {
            $this->line('▶ Rebuild Vite assets');

            $process = new Process(['npm', 'run', 'build'], base_path());
            $process->setTimeout(120);
            $process->run(function (string $type, string $buffer): void {
                $this->output->write($buffer);
            });

            if (! $process->isSuccessful()) {
                $this->error('❌ Failed: npm run build');

                return self::FAILURE;
            }
        }

        if ($runTwGraphTests) {
            $exitCode = $this->runTwGraphTests();

            if ($exitCode !== self::SUCCESS) {
                return $exitCode;
            }
        }

        $messages = ['✅ Project caches cleared.'];

        if ($rebuildViews) {
            $messages[] = 'Blade views rebuilt.';
        } elseif ($runTwGraphTests) {
            $messages[] = 'Blade views rebuilt during TW-Graph checks.';
        } else {
            $messages[] = 'Blade views will be compiled by the web runtime on demand.';
        }

        $messages[] = $buildAssets
            ? 'Vite assets rebuilt.'
            : 'Vite assets were not rebuilt; use --build-assets for CSS/JS changes without a dev server.';

        if ($runTwGraphTests) {
            $messages[] = 'TW-Graph tests passed.';
        }

        $this->info(implode(' ', $messages));

        return self::SUCCESS;
    }

    private function watch(): int
    {
        $interval = max(1, (int) $this->option('watch-interval'));

        $this->info('👀 Watching project files. Press Ctrl+C to stop.');
        $this->line('   On changes: clear caches, rebuild Blade views, rebuild Vite assets.');
        $this->line('   Poll interval: ' . $interval . 's');

        $lastSignature = $this->watchedFilesSignature();

        $this->newLine();
        $this->line('▶ Initial project clear/build');
        $this->clearProject(rebuildViews: true, buildAssets: true);
        $this->warn('Watcher: clear:project still running! Press Ctrl+C to stop.');

        while (true) {
            sleep($interval);
            clearstatcache();

            $currentSignature = $this->watchedFilesSignature();

            if ($currentSignature === $lastSignature) {
                continue;
            }

            usleep(350_000);
            clearstatcache();

            $currentSignature = $this->watchedFilesSignature();
            $lastSignature = $currentSignature;

            $this->newLine();
            $this->line('▶ Change detected at ' . now()->format('Y-m-d H:i:s'));

            $exitCode = $this->clearProject(rebuildViews: true, buildAssets: true);

            if ($exitCode !== self::SUCCESS) {
                $this->warn('⚠ Watcher continues after the failed rebuild.');
            } else {
                $this->warn('Watcher: clear:project still running! Press Ctrl+C to stop.');
            }
        }
    }

    private function runTwGraphTests(): int
    {
        $this->line('▶ Run TW-Graph checks');

        $checks = collect($this->twGraphTestGroups())
            ->map(static fn(array $group): array => [
                'name' => 'Pest: ' . $group['name'],
                'type' => 'pest',
                'group' => $group['name'],
                'description' => $group['description'],
                'command' => array_values(array_filter(array_merge(
                    [PHP_BINARY, 'artisan', 'test'],
                    $group['paths'],
                    ['--stop-on-failure'],
                ))),
            ])
            ->all();

        $checks[] = [
            'name' => 'Blade view cache',
            'type' => 'infrastructure',
            'group' => 'Infrastructure',
            'description' => 'Compiles Blade views so syntax and component call errors surface outside the browser.',
            'command' => [PHP_BINARY, 'artisan', 'view:cache'],
        ];

        $checks[] = [
            'name' => 'Git diff whitespace check',
            'type' => 'infrastructure',
            'group' => 'Infrastructure',
            'description' => 'Checks the current diff for whitespace errors before the work is committed.',
            'command' => ['git', 'diff', '--check'],
        ];

        $results = [];
        $failed = false;

        foreach ($checks as $check) {
            $this->line('  • ' . $check['name']);

            $startedAt = microtime(true);
            $process = new Process($check['command'], base_path());
            $process->setTimeout(300);
            $process->run();

            $output = trim($process->getOutput() . "\n" . $process->getErrorOutput());
            $parsedOutput = $this->parseJsonProcessOutput($output);
            $result = [
                'name' => $check['name'],
                'type' => $check['type'],
                'group' => $check['group'],
                'description' => $check['description'],
                'command' => implode(' ', $check['command']),
                'exit_code' => $process->getExitCode(),
                'status' => $process->isSuccessful() ? 'passed' : 'failed',
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'summary' => $this->processSummary($output, $parsedOutput),
                'output' => $output,
                'parsed_output' => $parsedOutput,
            ];
            $results[] = $result;

            if (! $process->isSuccessful()) {
                $failed = true;
            }

            $this->line('    ' . ($process->isSuccessful() ? 'passed' : 'failed') . ': ' . $result['summary']);
        }

        $report = [
            'generated_at' => now()->toIso8601String(),
            'status' => $failed ? 'failed' : 'passed',
            'groups' => $this->twGraphReportGroups($results),
            'checks' => $results,
        ];
        $paths = $this->writeTwGraphTestsReport($report);

        $this->line('  Report JSON: ' . $paths['json']);
        $this->line('  Report HTML: ' . $paths['html']);

        if ($failed) {
            $this->error('❌ TW-Graph checks failed. See report above.');

            return self::FAILURE;
        }

        $this->info('✅ TW-Graph checks passed.');

        return self::SUCCESS;
    }

    /**
     * The report runs themed Pest groups instead of one opaque suite so the
     * diagnostics page can show which TW-Graph layer failed without exposing
     * raw console output as a second table row.
     *
     * @return array<int, array{name: string, description: string, paths: array<int, string>}>
     */
    private function twGraphTestGroups(): array
    {
        return [
            [
                'name' => 'Defaults, identifiers & geometry',
                'description' => 'Central defaults, stable references, bounds, coordinates, and primitive geometry.',
                'paths' => [
                    'tests/Unit/TwGraph/AnchorRegistryTest.php',
                    'tests/Unit/TwGraph/BoundsRegistryTest.php',
                    'tests/Unit/TwGraph/DefaultsTest.php',
                    'tests/Unit/TwGraph/DevIdentifierTest.php',
                    'tests/Unit/TwGraph/ElementIdentifierTest.php',
                    'tests/Unit/TwGraph/GeometryBoundsTest.php',
                    'tests/Unit/TwGraph/GeometryCacheTest.php',
                    'tests/Unit/TwGraph/GeometryResolverTest.php',
                    'tests/Unit/TwGraph/LayoutCorrectionConfigTest.php',
                    'tests/Unit/TwGraph/LocaleResolverTest.php',
                    'tests/Unit/TwGraph/RenderContextTest.php',
                ],
            ],
            [
                'name' => 'Primitives, parts & strang views',
                'description' => 'Blade components for text labels, primitives, parts, paths, segments, and strangs.',
                'paths' => [
                    'tests/Unit/TwGraph/ManualPartsViewTest.php',
                    'tests/Unit/TwGraph/PathViewTest.php',
                    'tests/Unit/TwGraph/PrimitiveViewTest.php',
                    'tests/Unit/TwGraph/ProtocolCanvasViewTest.php',
                    'tests/Unit/TwGraph/SegmentViewTest.php',
                    'tests/Unit/TwGraph/StrangBranchViewTest.php',
                    'tests/Unit/TwGraph/StrangMergeViewTest.php',
                    'tests/Unit/TwGraph/StrangRekeyViewTest.php',
                    'tests/Unit/TwGraph/StrangTrunkViewTest.php',
                    'tests/Unit/TwGraph/TextLabelTest.php',
                ],
            ],
            [
                'name' => 'Data-driven graph assembly',
                'description' => 'Timeline-chain graph data, preview builders, labels, outcomes, and noise classification.',
                'paths' => [
                    'tests/Unit/TwGraph/BranchLabelCollisionResolverTest.php',
                    'tests/Unit/TwGraph/BranchPreviewBuilderTest.php',
                    'tests/Unit/TwGraph/ClassificationNoiseAnalyzerTest.php',
                    'tests/Unit/TwGraph/DataDrivenDatasetsComponentTest.php',
                    'tests/Unit/TwGraph/DataDrivenDebugBoundsViewTest.php',
                    'tests/Unit/TwGraph/DataDrivenGraphFactsTest.php',
                    'tests/Unit/TwGraph/DataDrivenLabelFormatterTest.php',
                    'tests/Unit/TwGraph/DataDrivenValueNormalizerTest.php',
                    'tests/Unit/TwGraph/FindingInspectorTest.php',
                    'tests/Unit/TwGraph/LangValueLabelsTest.php',
                    'tests/Unit/TwGraph/MergeOutcomesTest.php',
                    'tests/Unit/TwGraph/MergePreviewBuilderTest.php',
                    'tests/Unit/TwGraph/RekeyPreviewBuilderTest.php',
                    'tests/Unit/TwGraph/RenderPreviewBuilderTest.php',
                    'tests/Unit/TwGraph/TimelineChainGraphDataTest.php',
                ],
            ],
            [
                'name' => 'Diagnostics, routes & documentation',
                'description' => 'Visible diagnostics, routing, and handmade/documentation sample contracts.',
                'paths' => [
                    'tests/Unit/TwGraph/ClearProjectTwGraphReportTest.php',
                    'tests/Unit/TwGraph/DiagnosticsViewTest.php',
                    'tests/Unit/TwGraph/DocumentationSampleViewTest.php',
                    'tests/Unit/TwGraph/DocumentationViewResolverTest.php',
                    'tests/Unit/TwGraph/TwGraphDiagnosticsViewTest.php',
                    'tests/Unit/TwGraph/TwGraphRoutesTest.php',
                ],
            ],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $results
     * @return array<int, array<string, mixed>>
     */
    private function twGraphReportGroups(array $results): array
    {
        return collect($results)
            ->groupBy(static fn(array $result): string => (string) ($result['group'] ?? 'Other'))
            ->map(static function ($groupResults, string $group): array {
                $first = $groupResults->first();
                $tests = $groupResults->sum(static fn(array $result): int => (int) data_get($result, 'parsed_output.tests', 0));
                $assertions = $groupResults->sum(static fn(array $result): int => (int) data_get($result, 'parsed_output.assertions', 0));

                return [
                    'name' => $group,
                    'description' => (string) data_get($first, 'description', ''),
                    'status' => $groupResults->contains(static fn(array $result): bool => ($result['status'] ?? null) !== 'passed') ? 'failed' : 'passed',
                    'checks' => $groupResults->count(),
                    'tests' => $tests,
                    'assertions' => $assertions,
                    'duration_ms' => $groupResults->sum(static fn(array $result): int => (int) ($result['duration_ms'] ?? 0)),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseJsonProcessOutput(string $output): ?array
    {
        foreach (array_reverse(preg_split('/\R/', $output) ?: []) as $line) {
            $line = trim($line);

            if ($line === '' || ! str_starts_with($line, '{')) {
                continue;
            }

            $decoded = json_decode($line, true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|null  $parsedOutput
     */
    private function processSummary(string $output, ?array $parsedOutput): string
    {
        if ($parsedOutput !== null) {
            $parts = array_filter([
                (string) data_get($parsedOutput, 'result', ''),
                data_get($parsedOutput, 'tests') !== null ? data_get($parsedOutput, 'tests') . ' tests' : null,
                data_get($parsedOutput, 'assertions') !== null ? data_get($parsedOutput, 'assertions') . ' assertions' : null,
            ]);

            if ($parts !== []) {
                return implode(', ', $parts);
            }
        }

        $lastLine = collect(preg_split('/\R/', trim($output)) ?: [])
            ->map(static fn(string $line): string => trim($line))
            ->filter()
            ->last();

        return $lastLine ?: 'No output.';
    }

    /**
     * @param  array<string, mixed>  $report
     * @return array{json: string, html: string}
     */
    private function writeTwGraphTestsReport(array $report): array
    {
        $directory = storage_path('translation-workbench/reports/tw-graph-tests');

        File::ensureDirectoryExists($directory);

        $jsonPath = $directory . '/latest.json';
        $htmlPath = $directory . '/latest.html';

        File::put($jsonPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
        File::put($htmlPath, $this->twGraphTestsReportHtml($report));

        return [
            'json' => $jsonPath,
            'html' => $htmlPath,
        ];
    }

    /**
     * @param  array<string, mixed>  $report
     */
    private function twGraphTestsReportHtml(array $report): string
    {
        $status = e((string) ($report['status'] ?? 'unknown'));
        $generatedAt = e((string) ($report['generated_at'] ?? ''));
        $groupRows = collect((array) ($report['groups'] ?? []))
            ->map(static function (array $group): string {
                $name = e((string) ($group['name'] ?? ''));
                $description = e((string) ($group['description'] ?? ''));
                $groupStatus = e((string) ($group['status'] ?? 'unknown'));
                $checks = e((string) ($group['checks'] ?? ''));
                $tests = e((string) ($group['tests'] ?? ''));
                $assertions = e((string) ($group['assertions'] ?? ''));
                $duration = e((string) ($group['duration_ms'] ?? ''));

                return <<<HTML
                    <tr>
                        <td><strong>{$name}</strong><br><span class="muted">{$description}</span></td>
                        <td><span class="badge {$groupStatus}">{$groupStatus}</span></td>
                        <td>{$checks}</td>
                        <td>{$tests}</td>
                        <td>{$assertions}</td>
                        <td>{$duration} ms</td>
                    </tr>
                HTML;
            })
            ->join("\n");
        $rows = collect((array) ($report['checks'] ?? []))
            ->map(static function (array $check): string {
                $name = e((string) ($check['name'] ?? ''));
                $checkStatus = e((string) ($check['status'] ?? 'unknown'));
                $command = e((string) ($check['command'] ?? ''));
                $duration = e((string) ($check['duration_ms'] ?? ''));
                $summary = e((string) ($check['summary'] ?? ''));
                $output = e((string) ($check['output'] ?? ''));
                $description = e((string) ($check['description'] ?? ''));

                return <<<HTML
                    <tr>
                        <td title="{$output}"><strong>{$name}</strong><br><span class="muted">{$description}</span></td>
                        <td><span class="badge {$checkStatus}">{$checkStatus}</span></td>
                        <td><code>{$command}</code></td>
                        <td>{$duration} ms</td>
                        <td>{$summary}</td>
                    </tr>
                HTML;
            })
            ->join("\n");

        return <<<HTML
            <!doctype html>
            <html lang="en">
            <head>
                <meta charset="utf-8">
                <title>TW-Graph Test Report</title>
                <style>
                    body { font-family: ui-sans-serif, system-ui, sans-serif; margin: 2rem; color: #18181b; }
                    table { border-collapse: collapse; width: 100%; }
                    th, td { border: 1px solid #d4d4d8; padding: 0.65rem; vertical-align: top; text-align: left; }
                    th { background: #f4f4f5; }
                    pre { margin: 0; white-space: pre-wrap; overflow-wrap: anywhere; font-size: 0.8rem; }
                    code { font-size: 0.85rem; }
                    .muted { color: #71717a; font-size: 0.85rem; }
                    .badge { border-radius: 999px; padding: 0.15rem 0.55rem; font-size: 0.8rem; font-weight: 700; }
                    .passed { background: #dcfce7; color: #166534; }
                    .failed { background: #fee2e2; color: #991b1b; }
                </style>
            </head>
            <body>
                <h1>TW-Graph Test Report</h1>
                <p>Status: <strong>{$status}</strong></p>
                <p>Generated at: {$generatedAt}</p>
                <h2>Group Summary</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Status</th>
                            <th>Checks</th>
                            <th>Tests</th>
                            <th>Assertions</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$groupRows}
                    </tbody>
                </table>
                <h2>Check Results</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Check</th>
                            <th>Status</th>
                            <th>Command</th>
                            <th>Duration</th>
                            <th>Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$rows}
                    </tbody>
                </table>
            </body>
            </html>
        HTML;
    }

    private function watchedFilesSignature(): string
    {
        $roots = [
            app_path(),
            config_path(),
            base_path('routes'),
            resource_path('css'),
            resource_path('js'),
            resource_path('views'),
            base_path('packages/gunreip/laravel-translation-workbench/config'),
            base_path('packages/gunreip/laravel-translation-workbench/resources/css'),
            base_path('packages/gunreip/laravel-translation-workbench/resources/js'),
            base_path('packages/gunreip/laravel-translation-workbench/resources/views'),
            base_path('packages/gunreip/laravel-translation-workbench/src'),
        ];

        $extensions = ['blade.php', 'css', 'js', 'php'];
        $parts = [];

        foreach ($roots as $root) {
            if (! is_dir($root)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (! $file->isFile()) {
                    continue;
                }

                $path = $file->getPathname();

                if (! $this->isWatchedFile($path, $extensions)) {
                    continue;
                }

                $parts[] = $path . ':' . $file->getMTime() . ':' . $file->getSize();
            }
        }

        sort($parts);

        return hash('sha256', implode('|', $parts));
    }

    private function isWatchedFile(string $path, array $extensions): bool
    {
        foreach ($extensions as $extension) {
            if (str_ends_with($path, '.' . $extension)) {
                return true;
            }
        }

        return false;
    }
}
