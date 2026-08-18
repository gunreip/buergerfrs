<?php

// app/Console/Commands/ClearProject.php

// php artisan clear:project
// php artisan clear:project --rebuild-views
// php artisan clear:project --build-assets
// php artisan clear:project --rebuild-views --build-assets
// php artisan clear:project --watch
// php artisan clear:project --watch --watch-interval=2

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Throwable;

class ClearProject extends Command
{
    protected $signature = 'clear:project
        {--rebuild-views : Rebuild compiled Blade views after clearing caches. Useful for deploys, risky in local mixed-user runtimes.}
        {--build-assets : Rebuild Vite assets with npm run build after clearing caches. Needed when CSS/JS files changed and no Vite dev server is running.}
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
        );
    }

    private function clearProject(bool $rebuildViews, bool $buildAssets): int
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

        $messages = ['✅ Project caches cleared.'];

        if ($rebuildViews) {
            $messages[] = 'Blade views rebuilt.';
        } else {
            $messages[] = 'Blade views will be compiled by the web runtime on demand.';
        }

        $messages[] = $buildAssets
            ? 'Vite assets rebuilt.'
            : 'Vite assets were not rebuilt; use --build-assets for CSS/JS changes without a dev server.';

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
