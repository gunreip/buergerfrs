<?php

// app/Console/Commands/ClearProject.php

// php artisan clear:project
// php artisan clear:project --rebuild-views

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class ClearProject extends Command
{
    protected $signature = 'clear:project
        {--rebuild-views : Rebuild compiled Blade views after clearing caches. Useful for deploys, risky in local mixed-user runtimes.}';

    protected $description = 'Clears project caches and optionally rebuilds the Blade view cache.';

    public function handle(): int
    {
        $steps = [
            ['label' => 'Clear optimized framework caches', 'command' => 'optimize:clear'],
            ['label' => 'Clear compiled Blade views', 'command' => 'view:clear'],
        ];

        if ((bool) $this->option('rebuild-views')) {
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

        $this->info((bool) $this->option('rebuild-views')
            ? '✅ Project caches cleared and Blade views rebuilt.'
            : '✅ Project caches cleared. Blade views will be compiled by the web runtime on demand.');

        return self::SUCCESS;
    }
}
