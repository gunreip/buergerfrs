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

        // Composer-Pakete (nur direkte Abhängigkeiten)
        $composerPackagesPath = base_path('composer_packages.json');
        if (file_exists($composerPackagesPath)) {
            $json = json_decode(file_get_contents($composerPackagesPath), true);
            $locked = $json['locked'] ?? [];
            $direct = array_filter($locked, fn($pkg) => ($pkg['direct-dependency'] ?? false) === true);
            if (count($direct) > 0) {
                $content .= "\n## Wichtige Composer-Packages (direkte Abhängigkeiten)\n\n";
                $content .= "| Package | Version | Beschreibung |\n";
                $content .= "|---------|---------|--------------|\n";
                foreach ($direct as $pkg) {
                    $name = $pkg['name'] ?? '';
                    $version = $pkg['version'] ?? '';
                    $desc = $pkg['description'] ?? '';
                    // Pipe-Zeichen escapen
                    $desc = str_replace('|', '\\|', $desc);
                    $content .= "| $name | $version | $desc |\n";
                }
            }
        } else {
            $content .= "\n*composer_packages.json nicht gefunden – bitte vorher \"composer show --all --format=json > composer_packages.json\" ausführen.*\n";
        }

        // npm-Pakete (direkte Abhängigkeiten)
        $npmPackagesPath = base_path('npm_packages.json');
        $packageJsonPath = base_path('package.json');
        if (file_exists($npmPackagesPath) && file_exists($packageJsonPath)) {
            $npmJson = json_decode(file_get_contents($npmPackagesPath), true);
            $pkgJson = json_decode(file_get_contents($packageJsonPath), true);
            $deps = array_merge(
                $pkgJson['dependencies'] ?? [],
                $pkgJson['devDependencies'] ?? [],
                $pkgJson['optionalDependencies'] ?? []
            );
            $npmDeps = $npmJson['dependencies'] ?? [];
            if (count($deps) > 0) {
                $content .= "\n## Wichtige npm-Packages (direkte Abhängigkeiten)\n\n";
                $content .= "| Package | Version | Beschreibung |\n";
                $content .= "|---------|---------|--------------|\n";
                foreach ($deps as $name => $reqVersion) {
                    $info = $npmDeps[$name] ?? null;
                    $version = $info['version'] ?? $reqVersion;
                    // Beschreibung ist im npm_packages.json nicht enthalten, daher leer lassen
                    $desc = '';
                    $content .= "| $name | $version | $desc |\n";
                }
            }
        } else {
            $content .= "\n*npm_packages.json oder package.json nicht gefunden – bitte vorher \"npm ls --json --depth=0 > npm_packages.json\" ausführen.*\n";
        }

        file_put_contents(base_path('VERSIONS.md'), $content);
        $this->info('VERSIONS.md wurde aktualisiert.');
    }
}
