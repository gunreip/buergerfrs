<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:write-app-version')]
#[Description('Command description')]
class WriteAppVersion extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ermittle die Version aus Git (Tag, Commits seit Tag, Hash)
        $version = trim(shell_exec('git describe --tags --always --dirty')); // Fallback: Hash, falls keine Tags
        if (! $version) {
            $version = 'dev-unknown';
        }
        $file = public_path('version.txt');
        file_put_contents($file, $version);
        $this->info("App-Version geschrieben: $version");
    }
}
