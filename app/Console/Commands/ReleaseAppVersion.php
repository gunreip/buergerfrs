<?php

namespace App\Console\Commands;

use App\Support\VersionManifest;
use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class ReleaseAppVersion extends Command
{
    protected $signature = 'app:release {component : buergerfrs, translation-workbench or tw-graph}';

    protected $description = 'Create a local annotated release tag from committed versions.json; does not push.';

    public function handle(VersionManifest $manifest): int
    {
        try {
            $component = $this->argument('component');
            $version = $manifest->all()[$component] ?? throw new RuntimeException('Unknown component: '.$component);
            $head = $this->git(['rev-parse', '--verify', 'HEAD']);
            if ($this->git(['status', '--porcelain', '--untracked-files=normal']) !== '') {
                throw new RuntimeException('Commit or resolve all working tree changes before creating a release tag.');
            }
            $committed = json_decode($this->git(['show', $head.':versions.json']), true, flags: JSON_THROW_ON_ERROR);
            if (($committed[$component] ?? null) !== $version) {
                throw new RuntimeException('The version must match versions.json in HEAD.');
            }

            $tag = $component.'/v'.$version;
            $this->git(['tag', '-a', $tag, $head, '-m', $component.' '.$version]);
            $this->info('Created local release tag: '.$tag);
            $this->line('Publish explicitly with: git push origin '.$tag);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    private function git(array $arguments): string
    {
        $process = new Process(['git', '-c', 'safe.directory='.base_path(), ...$arguments], base_path());
        $process->setTimeout(10);
        $process->mustRun();

        return trim($process->getOutput());
    }
}
