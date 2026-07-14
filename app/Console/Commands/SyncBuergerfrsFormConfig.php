<?php

// app/Console/Commands/SyncBuergerfrsFormConfig.php

// php artisan buergerfrs:forms:sync

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('buergerfrs:forms:sync {--write : Write missing fields to config/buergerfrs-forms.php} {--form=* : Limit sync to one or more form keys}')]
#[Description('Scan managed form views and sync missing fields into config/buergerfrs-forms.php.')]
class SyncBuergerfrsFormConfig extends Command
{
    /**
     * @var list<string>
     */
    private const IGNORED_FIELDS = [
        'activeFormTab',
        'addDocumentModalOpen',
        'documentArchiveFilter',
        'documentArchiveModalOpen',
    ];

    public function handle(): int
    {
        $config = $this->readConfig();
        $forms = $this->selectedScopes($config);
        $changes = [];

        foreach ($forms as $form => $scope) {
            $scannedFields = $this->scanScope($scope['path'], $scope['default_tab']);
            $configuredFields = array_keys(data_get($config, "{$form}.fields", []));
            $missingFields = array_diff(array_keys($scannedFields), $configuredFields);
            $staleFields = array_diff($configuredFields, array_keys($scannedFields));

            $changes[$form] = [
                'missing' => array_values($missingFields),
                'stale' => array_values($staleFields),
                'fields' => $scannedFields,
            ];
        }

        $this->renderReport($changes);

        if (! $this->option('write')) {
            $this->line('Dry run only. Re-run with --write to update config/buergerfrs-forms.php.');

            return self::SUCCESS;
        }

        $updatedConfig = $this->updatedConfig($config, $changes);
        $this->writeConfig($updatedConfig);
        $this->info('Updated config/buergerfrs-forms.php.');

        return self::SUCCESS;
    }

    /**
     * @return array<string, mixed>
     */
    private function readConfig(): array
    {
        $config = require config_path('buergerfrs-forms.php');

        return is_array($config) ? $config : [];
    }

    /**
     * @param array<string, mixed> $config
     * @return array<string, array{path:string, default_tab:?string}>
     */
    private function selectedScopes(array $config): array
    {
        $selectedForms = array_filter((array) $this->option('form'));
        $scopes = [];

        $this->collectScopes($config, '', $scopes);

        if ($selectedForms === []) {
            return $scopes;
        }

        return array_intersect_key($scopes, array_flip($selectedForms));
    }

    /**
     * @param array<string, mixed> $node
     * @param array<string, array{path:string, default_tab:?string}> $scopes
     */
    private function collectScopes(array $node, string $namespace, array &$scopes): void
    {
        $path = $node['path'] ?? data_get($node, 'scope.path');

        if (is_string($path) && $path !== '' && array_key_exists('fields', $node)) {
            $defaultTab = $node['default_tab'] ?? data_get($node, 'scope.default_tab');
            $scopes[$namespace] = [
                'path' => $path,
                'default_tab' => is_string($defaultTab) && $defaultTab !== '' ? $defaultTab : $this->tabFromNamespace($namespace),
            ];
        }

        foreach ($node as $key => $child) {
            if (in_array($key, ['scope', 'fields', 'path', 'default_tab'], true) || ! is_array($child)) {
                continue;
            }

            $childNamespace = $namespace === '' ? (string) $key : "{$namespace}.{$key}";
            $this->collectScopes($child, $childNamespace, $scopes);
        }
    }

    /**
     * @return array<string, array{tab:?string, required:bool, status_relevant:bool}>
     */
    private function scanScope(string $path, ?string $defaultTab): array
    {
        $absolutePath = base_path($path);
        $files = is_file($absolutePath)
            ? [new \SplFileInfo($absolutePath)]
            : File::allFiles($absolutePath);
        $fields = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace('\\', '/', $file->getPathname());
            $contents = (string) File::get($file->getPathname());
            $tab = $this->tabForFile($relativePath, $defaultTab);

            foreach ($this->fieldsFromContents($contents) as $field) {
                if ($this->shouldIgnoreField($field)) {
                    continue;
                }

                $fields[$field] ??= [
                    'tab' => $tab,
                    'required' => false,
                    'status_relevant' => true,
                ];
            }
        }

        ksort($fields);

        return $fields;
    }

    /**
     * @return list<string>
     */
    private function fieldsFromContents(string $contents): array
    {
        preg_match_all('/\bwire:model(?:\.[\w.-]+)?="([^"]+)"/', $contents, $wireModels);
        preg_match_all('/<flux:error\s+name="([^"]+)"/', $contents, $errors);
        preg_match_all('/\bfield="([^"]+)"/', $contents, $tooltipFields);

        return collect([...$wireModels[1], ...$errors[1], ...$tooltipFields[1]])
            ->map(fn(string $field): string => trim($field))
            ->filter(fn(string $field): bool => $field !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function shouldIgnoreField(string $field): bool
    {
        return in_array($field, self::IGNORED_FIELDS, true)
            || str_contains($field, '{{')
            || str_contains($field, '*')
            || str_contains($field, '.');
    }

    private function tabForFile(string $path, ?string $defaultTab): ?string
    {
        if ($defaultTab !== null) {
            return $defaultTab;
        }

        if (! preg_match('#/sections/⚡([^/]+)\.blade\.php$#', $path, $matches)) {
            return null;
        }

        return $matches[1];
    }

    private function tabFromNamespace(string $namespace): ?string
    {
        $parts = explode('.', $namespace);
        $sectionsIndex = array_search('sections', $parts, true);

        if ($sectionsIndex === false) {
            return null;
        }

        $tab = $parts[$sectionsIndex + 1] ?? null;

        return is_string($tab) && $tab !== '' ? $tab : null;
    }

    /**
     * @param array<string, array{missing:list<string>, stale:list<string>, fields:array<string, array{tab:?string, required:bool, status_relevant:bool}>}> $changes
     */
    private function renderReport(array $changes): void
    {
        foreach ($changes as $form => $change) {
            $this->line('');
            $this->info($form);

            if ($change['missing'] === []) {
                $this->line('  Missing fields: none');
            } else {
                $this->warn('  Missing fields: ' . implode(', ', $change['missing']));
            }

            if ($change['stale'] === []) {
                $this->line('  Config-only fields: none');
            } else {
                $this->line('  Config-only fields: ' . implode(', ', $change['stale']));
            }
        }
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, array{missing:list<string>, stale:list<string>, fields:array<string, array{tab:?string, required:bool, status_relevant:bool}>}> $changes
     * @return array<string, mixed>
     */
    private function updatedConfig(array $config, array $changes): array
    {
        foreach ($changes as $form => $change) {
            $fieldsPath = "{$form}.fields";
            $configuredFields = data_get($config, $fieldsPath, []);

            foreach ($change['missing'] as $field) {
                $configuredFields[$field] = false;
            }

            data_set($config, $fieldsPath, $configuredFields);
        }

        return $config;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function writeConfig(array $config): void
    {
        $export = var_export($config, true);
        $export = preg_replace('/^([ ]*)array \(/m', '$1[', $export) ?? $export;
        $export = preg_replace('/\)(,?)$/m', ']$1', $export) ?? $export;

        File::put(config_path('buergerfrs-forms.php'), "<?php\n\n// config/buergerfrs-forms.php\n\nreturn {$export};\n");
    }
}
