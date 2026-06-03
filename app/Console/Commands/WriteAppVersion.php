<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('app:write-app-version')]
#[Description('Write git-based application version to public/version.txt')]
/**
 * Writes the current git-derived application version to public/version.txt.
 */
class WriteAppVersion extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Ermittle die Version aus Git (Tag, Commits seit Tag, Hash)
        $version = trim(shell_exec('git describe --tags --always --dirty')); // Fallback: Hash, falls keine Tags
        if (! $version) {
            $version = 'dev-unknown';
        }
        $file = public_path('version.txt');
        file_put_contents($file, $version);
        $this->info("App-Version geschrieben: $version");

        $this->logRunActivity('app.write_app_version.completed', 'Application version file written.', [
            'version' => $version,
            'path' => str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file),
        ]);

        return self::SUCCESS;
    }

    private function logRunActivity(string $event, string $description, array $properties = []): void
    {
        try {
            activity('project')
                ->event($event)
                ->withProperties(array_merge([
                    'command' => $this->getName(),
                ], $properties))
                ->log($description);
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: ' . $exception->getMessage());
        }
    }
}
