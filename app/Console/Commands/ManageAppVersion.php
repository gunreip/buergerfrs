<?php

namespace App\Console\Commands;

use App\Support\AppVersion;
use App\Support\VersionManifest;
use App\Support\WatchVersion;
use Illuminate\Console\Command;
use InvalidArgumentException;
use Throwable;

class ManageAppVersion extends Command
{
    protected $signature = 'app:version {component?} {--bump= : major, minor or patch} {--set= : Explicit Major.Minor.Patch version}';

    protected $description = 'Show or update independent application, Workbench and TW-Graph versions.';

    public function handle(VersionManifest $manifest, AppVersion $appVersion): int
    {
        try {
            $component = $this->argument('component');
            $bump = $this->option('bump');
            $set = $this->option('set');
            if ($component === null) {
                if ($bump !== null || $set !== null) {
                    throw new InvalidArgumentException('Specify a component to change its version.');
                }
                $this->table(['Component', 'Version'], collect($manifest->all())->map(fn ($version, $name) => [$name, $version])->values()->all());
                $this->line('Git: '.$appVersion->label());
                $watch = app(WatchVersion::class)->current($appVersion->commit());
                if ($watch !== null) {
                    $this->line('Watch: '.$watch['count'].' ('.$watch['updated_at'].')');
                }
            } elseif ($bump === null && $set === null) {
                $version = $manifest->all()[$component] ?? throw new InvalidArgumentException('Unknown component: '.$component);
                $this->line($component.': '.$version);
            } else {
                $version = $manifest->update($component, $bump, $set);
                $this->info($component.': '.$version.' — commit versions.json with the corresponding code changes.');
            }

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
