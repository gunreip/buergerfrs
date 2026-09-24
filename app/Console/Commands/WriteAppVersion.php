<?php

// app/Console/Commands/WriteAppVersion.php

namespace App\Console\Commands;

use App\Support\ActivityLog\ConsoleActivityContext;
use App\Support\AppVersion;
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
        $version = app(AppVersion::class)->gitVersion();
        if ($version === null) {
            $this->error('Git-Version konnte nicht ermittelt werden; version.txt bleibt unverändert.');

            return self::FAILURE;
        }
        $file = public_path('version.txt');
        file_put_contents($file, $version);
        $this->info("App-Version geschrieben: $version");

        $this->logRunActivity('app.write_app_version.completed', 'Application version file written.', [
            'version' => $version,
            'path' => str_replace(base_path().DIRECTORY_SEPARATOR, '', $file),
        ]);

        return self::SUCCESS;
    }

    private function logRunActivity(string $event, string $description, array $properties = []): void
    {
        try {
            activity('project')
                ->event($event)
                ->withProperties(ConsoleActivityContext::merge($this, $properties))
                ->log($description);
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: '.$exception->getMessage());
        }
    }
}
