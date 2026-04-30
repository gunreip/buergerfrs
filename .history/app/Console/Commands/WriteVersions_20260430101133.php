<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WriteVersions extends Command
{
    // Artisan-Aufruf: php artisan system:versions
    protected $signature = 'system:versions';
    protected $description = 'Schreibt OS-, PHP- und Package-Versionen in VERSIONS.md';

    public function handle()
    {
        $os = php_uname();
        $php = phpversion();
        $composer = trim(shell_exec('composer --version'));
        $node = trim(shell_exec('node --version'));
        $npm = trim(shell_exec('npm --version'));

        $content = "# VERSIONS.md\n\n";
        $content .= "| Komponente | Version |\n";
        $content .= "|------------|---------|\n";
        $content .= "| OS         | $os |\n";
        $content .= "| PHP        | $php |\n";
        $content .= "| Composer   | $composer |\n";
        $content .= "| Node.js    | $node |\n";
        $content .= "| npm        | $npm |\n";
        $content .= "\n**Stand:** " . date('Y-m-d H:i:s') . "\n";

        file_put_contents(base_path('VERSIONS.md'), $content);
        $this->info('VERSIONS.md wurde aktualisiert.');
    }
}
