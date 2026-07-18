<?php

// packages/gunreip/laravel-translation-workbench/src/Console/DiscoverDynamicOptions.php

// php artisan translation-workbench:discover-dynamic-options --dry-run
// php artisan translation-workbench:discover-dynamic-options --sync

namespace Gunreip\TranslationWorkbench\Console;

use Gunreip\TranslationWorkbench\Console\Concerns\WritesTranslationWorkbenchReports;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchEntry;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchEvent;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchDynamicValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchValue;
use Gunreip\TranslationWorkbench\Models\TranslationWorkbenchOptionDiscovery;
use Gunreip\TranslationWorkbench\Scanner\TranslationFindingNormalizer;
use Gunreip\TranslationWorkbench\Scanner\TranslationFingerprintFactory;
use Gunreip\TranslationWorkbench\Scanner\SuggestedKeyFactory;
use App\Support\Locale\LocaleCode;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

#[Signature('translation-workbench:discover-dynamic-options
    {--paths= : Comma-separated relative view paths to scan. Defaults to translation-workbench paths.}
    {--source-locale=en : Source locale used when writing discovered option labels as dynamic source values.}
    {--sync : Write discovered options into translation_workbench_values and translation_workbench_dynamic_values.}
    {--dry-run : Report only; do not write database rows.}')]
#[Description('Discover possible dynamic multi option values from Blade foreach option lists and simple hard-coded component arrays.')]
class DiscoverDynamicOptions extends Command
{
    use WritesTranslationWorkbenchReports;

    private bool $databaseWarningShown = false;

    public function __construct(
        private readonly SuggestedKeyFactory $suggestedKeyFactory,
        private readonly TranslationFingerprintFactory $fingerprintFactory,
        private readonly TranslationFindingNormalizer $findingNormalizer,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $sync = (bool) $this->option('sync') && ! (bool) $this->option('dry-run');
        $phpArrayOptions = $this->hardcodedPublicArrayOptions()
            ->merge($this->providerMethodOptions());
        $discoveries = $this->discoveries($phpArrayOptions);
        $summary = [
            'discoveries' => $discoveries->count(),
            'matched_entries' => 0,
            'unmatched_entries' => 0,
            'options_found' => 0,
            'options_created' => 0,
            'options_updated' => 0,
            'options_unchanged' => 0,
            'dynamic_values_created' => 0,
            'dynamic_values_updated' => 0,
            'dynamic_values_unchanged' => 0,
            'dynamic_values_skipped_missing_table' => 0,
            'options_unresolved' => 0,
            'entry_discovery_created' => 0,
            'entry_discovery_updated' => 0,
            'entry_discovery_unchanged' => 0,
            'workbench_entries_created' => 0,
            'option_discoveries_created' => 0,
            'option_discoveries_updated' => 0,
            'option_discoveries_unchanged' => 0,
            'untranslated_option_labels' => 0,
            'suggested_equal' => 0,
            'suggested_different' => 0,
            'suggested_missing_entry' => 0,
        ];

        $runner = function () use ($discoveries, $sync, &$summary): void {
            foreach ($discoveries as $discovery) {
                $entry = $this->matchingEntry($discovery);
                $options = $discovery['options'];

                if ($sync && ! $entry) {
                    $entry = $this->ensureWorkbenchEntry($discovery);
                    $summary['workbench_entries_created']++;
                }

                $suggestedState = $this->suggestedState($discovery, $entry);
                $summary[$entry ? 'matched_entries' : 'unmatched_entries']++;

                if ($options === []) {
                    $summary['options_unresolved']++;
                }

                $summary['options_found'] += count($options);
                $summary[$suggestedState['summary_key']]++;
                $summary['untranslated_option_labels'] += $discovery['label_usage'] === 'plain_label' ? 1 : 0;

                if ($sync) {
                    $summary[$this->syncOptionDiscovery($discovery, $entry, $suggestedState['label'])]++;

                    if (! $entry) {
                        continue;
                    }

                    $summary[$this->syncEntryDiscovery($entry, $discovery, $suggestedState['label'])]++;

                    if ($options !== []) {
                        foreach ($options as $valueKey => $nativeLabel) {
                            $summary[$this->syncOption($entry, $discovery, $valueKey, $nativeLabel)]++;
                            $summary[$this->syncDynamicSourceValue($entry, $discovery, $valueKey, $nativeLabel)]++;
                        }
                    }
                }

            }
        };

        $sync ? DB::transaction($runner) : $runner();

        $this->components->info('Dynamic option discovery finished.');
        $this->line('Discoveries: ' . number_format($summary['discoveries']));

        if ((bool) $this->option('dry-run')) {
            $this->warn('Dry run only: no database rows were written.');
        } elseif (! $sync) {
            $this->warn('No database rows were written. Use --sync to persist discovered options.');
        }

        /**
         * Shared raw-data report.
         *
         * The report structure is centralized in WritesTranslationWorkbenchReports.
         * Do not add command-specific raw_data fields here or change the report
         * contract silently; discuss report contract changes first.
         */
        $this->writeTranslationWorkbenchReport();

        return self::SUCCESS;
    }

    /**
     * @param  Collection<string, array{source_file: string, options: array<string, string>}>  $phpArrayOptions
     * @return Collection<int, array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>}>
     */
    private function discoveries(Collection $phpArrayOptions): Collection
    {
        return collect($this->paths())
            ->flatMap(function (string $path) use ($phpArrayOptions): array {
                $absolutePath = base_path($path);

                if (! File::isDirectory($absolutePath) && ! File::isFile($absolutePath)) {
                    return [];
                }

                $files = File::isDirectory($absolutePath)
                    ? collect(File::allFiles($absolutePath))
                    : collect([new \SplFileInfo($absolutePath)]);

                return $files
                    ->filter(static fn(\SplFileInfo $file): bool => str_ends_with($file->getFilename(), '.blade.php'))
                    ->flatMap(fn(\SplFileInfo $file): array => $this->discoveriesInBladeFile($file, $phpArrayOptions))
                    ->all();
            })
            ->values();
    }

    /**
     * @param  Collection<string, array{source_file: string, options: array<string, string>}>  $phpArrayOptions
     * @return array<int, array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>}>
     */
    private function discoveriesInBladeFile(\SplFileInfo $file, Collection $phpArrayOptions): array
    {
        $contents = File::get($file->getPathname());
        $relativePath = str_replace('\\', '/', Str::after($file->getPathname(), base_path() . DIRECTORY_SEPARATOR));
        $discoveries = [];
        $pattern = '/@foreach\s*\(\s*\$(?<options>[A-Za-z_][A-Za-z0-9_]*)\s+as\s+\$(?<key>[A-Za-z_][A-Za-z0-9_]*)\s*=>\s*\$(?<label>[A-Za-z_][A-Za-z0-9_]*)\s*\)(?<body>.*?)@endforeach/su';

        if (! preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return [];
        }

        foreach ($matches as $match) {
            $body = (string) $match['body'][0];
            $labelVariable = (string) $match['label'][0];
            $labelUsage = $this->labelUsageInBody($body, $labelVariable);

            if ($labelUsage === null) {
                continue;
            }

            $optionsVariable = (string) $match['options'][0];
            $scope = $this->findingNormalizer->scopeFromOptionsVariable($optionsVariable);
            $suggestedKey = $this->suggestedKeyFactory
                ->forDynamicExpressionAtSource($scope, $relativePath)
                ->key;
            $line = $this->lineNumberForOffset($contents, (int) $match[0][1]);
            $hardcodedOptions = $phpArrayOptions->get($optionsVariable);
            $options = $hardcodedOptions['options'] ?? [];

            $discoveries[] = [
                'scope' => $scope,
                'suggested_key' => $suggestedKey,
                'suggested_dynamic_key' => $suggestedKey,
                'source_path' => $relativePath,
                'source_line' => $line,
                'options_variable' => $optionsVariable,
                'key_variable' => (string) $match['key'][0],
                'label_variable' => $labelVariable,
                'label_usage' => $labelUsage,
                'source_type' => $options !== [] ? 'hardcoded_public_array' : 'unresolved',
                'options' => $options,
                'source_reference' => $hardcodedOptions['source_file'] ?? $relativePath . ':' . $line,
            ];
        }

        return $discoveries;
    }

    private function labelUsageInBody(string $body, string $labelVariable): ?string
    {
        $variable = preg_quote($labelVariable, '/');

        if (preg_match('/dynamic_label\s*\(/u', $body) === 1) {
            return 'dynamic_label';
        }

        if (preg_match('/__\(\s*\$' . $variable . '\s*\)/u', $body) === 1) {
            return 'translated_label';
        }

        if (
            preg_match('/\{\{\s*\$' . $variable . '\s*\}\}/u', $body) === 1
            || preg_match('/:\s*label\s*=\s*"\s*\$' . $variable . '\s*"/u', $body) === 1
        ) {
            return 'plain_label';
        }

        return null;
    }

    /**
     * @return Collection<string, array{source_file: string, options: array<string, string>}>
     */
    private function hardcodedPublicArrayOptions(): Collection
    {
        $files = collect(File::allFiles(app_path()))
            ->filter(static fn(\SplFileInfo $file): bool => $file->getExtension() === 'php');

        return $files
            ->flatMap(function (\SplFileInfo $file): array {
                $contents = File::get($file->getPathname());
                $relativePath = str_replace('\\', '/', Str::after($file->getPathname(), base_path() . DIRECTORY_SEPARATOR));
                $arrays = [];

                foreach ($this->publicArrayBodies($contents) as $name => $body) {
                    $options = $this->stringOptionsFromArrayBody($body);

                    if ($options === []) {
                        continue;
                    }

                    $arrays[] = [
                        'name' => $name,
                        'source_file' => $relativePath,
                        'options' => $options,
                    ];
                }

                return $arrays;
            })
            ->groupBy('name')
            ->map(function (Collection $matches, string $name): array {
                return [
                    'source_file' => $matches
                        ->pluck('source_file')
                        ->unique()
                        ->implode(', '),
                    'options' => $matches
                        ->pluck('options')
                        ->reduce(static fn(array $carry, array $options): array => [...$carry, ...$options], []),
                ];
            });
    }

    /**
     * Discover simple view option payloads passed from Livewire render methods.
     *
     * Example:
     * return view(..., [
     *     'roleBadgeVariantOptions' => $iconRegistry->roleUserManagementBadgeVariants(),
     * ]);
     *
     * If the render parameter is type-hinted and the provider method has no required
     * parameters, we can evaluate it and use the returned key => label array for
     * dynamic option discovery.
     *
     * @return Collection<string, array{source_file: string, options: array<string, string>}>
     */
    private function providerMethodOptions(): Collection
    {
        $files = collect(File::allFiles(app_path('Livewire')))
            ->filter(static fn(\SplFileInfo $file): bool => $file->getExtension() === 'php');

        return $files
            ->flatMap(function (\SplFileInfo $file): array {
                $contents = File::get($file->getPathname());
                $relativePath = str_replace('\\', '/', Str::after($file->getPathname(), base_path() . DIRECTORY_SEPARATOR));
                $renderParameterTypes = $this->renderParameterTypes($contents);

                if ($renderParameterTypes === []) {
                    return [];
                }

                $options = [];
                $pattern = '/[\'"](?<name>[A-Za-z_][A-Za-z0-9_]*)[\'"]\s*=>\s*\$(?<provider>[A-Za-z_][A-Za-z0-9_]*)->(?<method>[A-Za-z_][A-Za-z0-9_]*)\(\s*\)/u';

                if (preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER) === 0) {
                    return [];
                }

                foreach ($matches as $match) {
                    $name = (string) $match['name'];
                    $provider = (string) $match['provider'];
                    $method = (string) $match['method'];
                    $class = $renderParameterTypes[$provider] ?? null;

                    if (! $class || ! class_exists($class) || ! method_exists($class, $method)) {
                        continue;
                    }

                    try {
                        $reflection = new \ReflectionMethod($class, $method);

                        if ($reflection->getNumberOfRequiredParameters() > 0) {
                            continue;
                        }

                        $rawOptions = app($class)->{$method}();
                    } catch (Throwable) {
                        continue;
                    }

                    $normalizedOptions = $this->normalizeOptionPayload($rawOptions);

                    if ($normalizedOptions === []) {
                        continue;
                    }

                    $options[] = [
                        'name' => $name,
                        'source_file' => $relativePath . ':' . $method,
                        'options' => $normalizedOptions,
                    ];
                }

                return $options;
            })
            ->groupBy('name')
            ->map(function (Collection $matches): array {
                return [
                    'source_file' => $matches
                        ->pluck('source_file')
                        ->unique()
                        ->implode(', '),
                    'options' => $matches
                        ->pluck('options')
                        ->reduce(static fn(array $carry, array $options): array => [...$carry, ...$options], []),
                ];
            });
    }

    /**
     * @return array<string, class-string>
     */
    private function renderParameterTypes(string $contents): array
    {
        if (preg_match('/function\s+render\s*\((?<params>[^)]*)\)/su', $contents, $match) !== 1) {
            return [];
        }

        $uses = $this->importedClasses($contents);
        $params = (string) $match['params'];
        $types = [];

        if (preg_match_all('/(?<type>\\\\?[A-Za-z_][A-Za-z0-9_\\\\]*)\s+\$(?<name>[A-Za-z_][A-Za-z0-9_]*)/u', $params, $matches, PREG_SET_ORDER) === 0) {
            return [];
        }

        foreach ($matches as $param) {
            $type = ltrim((string) $param['type'], '\\');
            $shortType = Str::afterLast($type, '\\');
            $class = str_contains($type, '\\') ? $type : ($uses[$shortType] ?? null);

            if ($class && class_exists($class)) {
                $types[(string) $param['name']] = $class;
            }
        }

        return $types;
    }

    /**
     * @return array<string, class-string>
     */
    private function importedClasses(string $contents): array
    {
        $uses = [];

        if (preg_match_all('/^use\s+(?<class>[^;]+);/mu', $contents, $matches, PREG_SET_ORDER) === 0) {
            return [];
        }

        foreach ($matches as $match) {
            $class = trim((string) $match['class']);
            $alias = Str::afterLast($class, '\\');
            $uses[$alias] = $class;
        }

        return $uses;
    }

    /**
     * @return array<string, string>
     */
    private function normalizeOptionPayload(mixed $payload): array
    {
        if ($payload instanceof Collection) {
            $payload = $payload->all();
        }

        if (! is_array($payload)) {
            return [];
        }

        return collect($payload)
            ->mapWithKeys(function (mixed $value, mixed $key): array {
                $label = match (true) {
                    is_string($value) => $value,
                    is_array($value) && is_string($value['label'] ?? null) => (string) $value['label'],
                    default => null,
                };

                return $label !== null && trim((string) $key) !== '' && trim($label) !== ''
                    ? [(string) $key => $label]
                    : [];
            })
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function publicArrayBodies(string $contents): array
    {
        $arrays = [];
        $lines = preg_split('/\R/u', $contents) ?: [];

        for ($index = 0; $index < count($lines); $index++) {
            $line = $lines[$index];

            if (preg_match('/public\s+array\s+\$(?<name>[A-Za-z_][A-Za-z0-9_]*)\s*=\s*\[(?<rest>.*)$/u', $line, $match) !== 1) {
                continue;
            }

            $name = (string) $match['name'];
            $rest = (string) $match['rest'];
            $bodyLines = [];

            if (str_contains($rest, '];')) {
                $arrays[$name] = (string) Str::before($rest, '];');

                continue;
            }

            for ($bodyIndex = $index + 1; $bodyIndex < count($lines); $bodyIndex++) {
                $bodyLine = $lines[$bodyIndex];

                if (preg_match('/^\s*\];/u', $bodyLine) === 1) {
                    $index = $bodyIndex;
                    break;
                }

                $bodyLines[] = $bodyLine;
            }

            $arrays[$name] = implode("\n", $bodyLines);
        }

        return $arrays;
    }

    /**
     * @return array<string, string>
     */
    private function stringOptionsFromArrayBody(string $body): array
    {
        $options = [];
        $pattern = '/[\'"](?<key>(?:\\\\.|[^\'"])*)[\'"]\s*=>\s*[\'"](?<label>(?:\\\\.|[^\'"])*)[\'"]/u';

        if (! preg_match_all($pattern, $body, $matches, PREG_SET_ORDER)) {
            return [];
        }

        foreach ($matches as $match) {
            $key = stripcslashes((string) $match['key']);
            $label = stripcslashes((string) $match['label']);

            if (trim($key) === '' || trim($label) === '') {
                continue;
            }

            $options[$key] = $label;
        }

        return $options;
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>}  $discovery
     */
    private function matchingEntry(array $discovery): ?TranslationWorkbenchEntry
    {
        $scope = $discovery['scope'];
        $suggestedKey = $discovery['suggested_key'];

        try {
            return TranslationWorkbenchEntry::query()
                ->where(function ($query) use ($suggestedKey, $scope): void {
                    $query
                        ->where('suggested_key', $suggestedKey)
                        ->orWhere('translation_key', $suggestedKey)
                        ->orWhere('meta->dynamic_scope', $scope);
                })
                ->orderByRaw(
                    'CASE WHEN suggested_key = ? THEN 0 WHEN translation_key = ? THEN 1 ELSE 2 END',
                    [$suggestedKey, $suggestedKey],
                )
                ->orderBy('id')
                ->first();
        } catch (Throwable $exception) {
            if (! $this->databaseWarningShown) {
                $this->warn('Database lookup failed; continuing without matched workbench entries.');

                if ($this->getOutput()->isVerbose()) {
                    $this->line($exception->getMessage());
                }

                $this->databaseWarningShown = true;
            }

            return null;
        }
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>, source_reference?: string}  $discovery
     */
    private function ensureWorkbenchEntry(array $discovery): TranslationWorkbenchEntry
    {
        $suggestedKey = trim($discovery['suggested_key']);
        $keyResult = $this->suggestedKeyFactory->forExistingKey($suggestedKey);
        $sourceExpression = '@foreach ($' . $discovery['options_variable'] . ' as $' . $discovery['key_variable'] . ' => $' . $discovery['label_variable'] . ')';
        $sourceSignature = $this->fingerprintFactory->signature([
            $discovery['source_path'],
            (string) $discovery['source_line'],
            'dynamic_option_discovery',
            $suggestedKey,
            $sourceExpression,
        ]);
        $fingerprint = $this->fingerprintFactory->signature([
            'dynamic_option_discovery',
            $suggestedKey,
        ]);
        $attributes = [
            'fingerprint' => $fingerprint,
            'source_signature' => $sourceSignature,
            'source_fingerprint' => $this->fingerprintFactory->sourceFingerprint(
                $discovery['source_path'],
                $discovery['source_line'],
                $sourceExpression,
            ),
            'expression_fingerprint' => $this->fingerprintFactory->expressionFingerprint($sourceExpression),
            'semantic_fingerprint' => $this->fingerprintFactory->signature([
                'dynamic_multi',
                $suggestedKey,
            ]),
            'kind' => 'dynamic_multi',
            'entry_type' => 'dynamic',
            'candidate_type' => 'dynamic',
            'candidate_reason' => 'dynamic_option_discovery',
            'source_type' => str_ends_with($discovery['source_path'], '.blade.php') ? 'blade' : 'php',
            'source_path' => $discovery['source_path'],
            'source_line' => $discovery['source_line'],
            'function_name' => 'dynamic_option_discovery',
            'raw_expression' => $sourceExpression,
            'literal_text' => null,
            'literal_text_suggested' => $this->findingNormalizer->literalTextSuggestedFromScope($discovery['scope']),
            'translation_key' => null,
            'translation_key_source' => null,
            'existing_key' => null,
            'suggested_key' => $suggestedKey,
            'namespace' => $keyResult->namespace,
            'group' => $keyResult->group,
            'meta' => [
                'scanner' => 'dynamic_option_discovery',
                'dynamic_scope' => $discovery['scope'],
                'suggested_dynamic_key' => $discovery['suggested_dynamic_key'],
                'options_variable' => $discovery['options_variable'],
                'key_variable' => $discovery['key_variable'],
                'label_variable' => $discovery['label_variable'],
                'label_usage' => $discovery['label_usage'],
                'source_type' => $discovery['source_type'],
            ],
            'status' => 'open',
            'review_status' => 'pending',
            'first_seen_at' => now(),
            'last_seen_at' => now(),
            'scan_count' => 1,
        ];

        $entry = TranslationWorkbenchEntry::query()
            ->where('fingerprint', $fingerprint)
            ->first();

        if ($entry) {
            return $entry;
        }

        $entry = TranslationWorkbenchEntry::query()->create($attributes);

        $this->recordEvent($entry, 'dynamic_option_entry_created', null, $attributes);

        return $entry;
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>, source_reference?: string}  $discovery
     */
    private function syncOption(TranslationWorkbenchEntry $entry, array $discovery, string $valueKey, string $nativeLabel): string
    {
        $attributes = [
            'native_label' => $nativeLabel,
            'source_type' => $discovery['source_type'],
            'source_reference' => (string) ($discovery['source_reference'] ?? $discovery['source_path'] . ':' . $discovery['source_line']),
            'status' => 'open',
            'last_seen_at' => now(),
            'meta' => [
                'source' => 'translation-workbench:discover-dynamic-options',
                'scope' => $discovery['scope'],
                'suggested_key' => $discovery['suggested_key'],
                'suggested_dynamic_key' => $discovery['suggested_dynamic_key'],
                'options_variable' => $discovery['options_variable'],
                'label_usage' => $discovery['label_usage'],
            ],
        ];
        $value = TranslationWorkbenchValue::query()
            ->where('entry_id', $entry->id)
            ->where('value_key', $valueKey)
            ->first();

        if (! $value) {
            $value = TranslationWorkbenchValue::query()->create([
                'entry_id' => $entry->id,
                'value_key' => $valueKey,
                ...$attributes,
                'first_seen_at' => now(),
            ]);

            $this->recordEvent($entry, 'dynamic_option_discovered', null, [
                'value_id' => $value->id,
                'value_key' => $valueKey,
                ...$attributes,
            ]);

            return 'options_created';
        }

        $oldValues = $value->only(array_keys($attributes));
        $changed = collect($attributes)
            ->filter(static fn(mixed $attribute, string $key): bool => ($oldValues[$key] ?? null) !== $attribute)
            ->all();

        if ($changed === []) {
            return 'options_unchanged';
        }

        $value->forceFill($attributes)->save();

        $this->recordEvent($entry, 'dynamic_option_updated', $oldValues, $attributes, [
            'value_id' => $value->id,
            'value_key' => $valueKey,
        ]);

        return 'options_updated';
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>, source_reference?: string}  $discovery
     */
    private function syncDynamicSourceValue(TranslationWorkbenchEntry $entry, array $discovery, string $valueKey, string $nativeLabel): string
    {
        if (! Schema::hasTable('translation_workbench_dynamic_values')) {
            return 'dynamic_values_skipped_missing_table';
        }

        $locale = LocaleCode::normalize((string) $this->option('source-locale'));
        $locale = $locale !== '' ? $locale : 'en';
        $attributes = [
            'value' => $nativeLabel,
            'status' => trim($nativeLabel) !== '' ? 'ok' : 'missing',
            'source' => 'dynamic_option_discovery',
            'reviewed_at' => trim($nativeLabel) !== '' ? now() : null,
            'reviewed_by_user_id' => trim($nativeLabel) !== '' ? auth()->id() : null,
            'meta' => [
                'source' => 'translation-workbench:discover-dynamic-options',
                'scope' => $discovery['scope'],
                'suggested_key' => $discovery['suggested_key'],
                'suggested_dynamic_key' => $discovery['suggested_dynamic_key'],
                'options_variable' => $discovery['options_variable'],
                'label_usage' => $discovery['label_usage'],
                'source_type' => $discovery['source_type'],
                'source_reference' => (string) ($discovery['source_reference'] ?? $discovery['source_path'] . ':' . $discovery['source_line']),
            ],
        ];
        $dynamicValue = TranslationWorkbenchDynamicValue::query()
            ->where('entry_id', $entry->id)
            ->where('value_key', $valueKey)
            ->where('locale', $locale)
            ->first();

        if (! $dynamicValue) {
            $dynamicValue = TranslationWorkbenchDynamicValue::query()->create([
                'entry_id' => $entry->id,
                'value_key' => $valueKey,
                'locale' => $locale,
                ...$attributes,
            ]);

            $this->recordEvent($entry, 'dynamic_source_value_discovered', null, [
                'dynamic_value_id' => $dynamicValue->id,
                'value_key' => $valueKey,
                'locale' => $locale,
                ...$attributes,
            ]);

            return 'dynamic_values_created';
        }

        $oldValues = $dynamicValue->only(array_keys($attributes));
        $changed = collect($attributes)
            ->filter(static fn(mixed $attribute, string $key): bool => ($oldValues[$key] ?? null) !== $attribute)
            ->all();

        if ($changed === []) {
            return 'dynamic_values_unchanged';
        }

        $dynamicValue->forceFill($attributes)->save();

        $this->recordEvent($entry, 'dynamic_source_value_updated', $oldValues, [
            'dynamic_value_id' => $dynamicValue->id,
            'value_key' => $valueKey,
            'locale' => $locale,
            ...$attributes,
        ]);

        return 'dynamic_values_updated';
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>, source_reference?: string}  $discovery
     */
    private function syncOptionDiscovery(array $discovery, ?TranslationWorkbenchEntry $entry, string $suggestedState): string
    {
        $fingerprint = $this->optionDiscoveryFingerprint($discovery);
        $attributes = [
            'matched_entry_id' => $entry?->id,
            'scope' => $discovery['scope'],
            'suggested_key' => $discovery['suggested_key'],
            'suggested_dynamic_key' => $discovery['suggested_dynamic_key'],
            'workbench_suggested_key' => $entry?->suggested_key,
            'suggested_state' => $suggestedState,
            'source_path' => $discovery['source_path'],
            'source_line' => $discovery['source_line'],
            'options_variable' => $discovery['options_variable'],
            'key_variable' => $discovery['key_variable'],
            'label_variable' => $discovery['label_variable'],
            'label_usage' => $discovery['label_usage'],
            'source_type' => $discovery['source_type'],
            'source_reference' => (string) ($discovery['source_reference'] ?? $discovery['source_path'] . ':' . $discovery['source_line']),
            'options_count' => count($discovery['options']),
            'status' => 'open',
            'last_seen_at' => now(),
            'options' => $discovery['options'],
            'meta' => [
                'source' => 'translation-workbench:discover-dynamic-options',
                'matched_entry_translation_key' => $entry?->translation_key,
                'matched_entry_status' => $entry?->status,
            ],
        ];
        $optionDiscovery = TranslationWorkbenchOptionDiscovery::query()
            ->where('fingerprint', $fingerprint)
            ->first();

        if (! $optionDiscovery) {
            TranslationWorkbenchOptionDiscovery::query()->create([
                'fingerprint' => $fingerprint,
                ...$attributes,
                'first_seen_at' => now(),
            ]);

            return 'option_discoveries_created';
        }

        $oldValues = $optionDiscovery->only(array_keys($attributes));
        $compareOldValues = $oldValues;
        $compareNewValues = $attributes;
        unset($compareOldValues['last_seen_at'], $compareNewValues['last_seen_at']);
        $changed = collect($compareNewValues)
            ->filter(static fn(mixed $attribute, string $key): bool => ($compareOldValues[$key] ?? null) !== $attribute)
            ->all();

        if ($changed === []) {
            $optionDiscovery->forceFill(['last_seen_at' => now()])->save();

            return 'option_discoveries_unchanged';
        }

        $optionDiscovery->forceFill($attributes)->save();

        return 'option_discoveries_updated';
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>, source_reference?: string}  $discovery
     */
    private function syncEntryDiscovery(TranslationWorkbenchEntry $entry, array $discovery, string $suggestedState): string
    {
        $oldMeta = is_array($entry->meta) ? $entry->meta : [];
        $oldDiscovery = $oldMeta['dynamic_option_discovery'] ?? null;
        $newDiscovery = [
            'scope' => $discovery['scope'],
            'suggested_key' => $discovery['suggested_key'],
            'suggested_dynamic_key' => $discovery['suggested_dynamic_key'],
            'source_path' => $discovery['source_path'],
            'source_line' => $discovery['source_line'],
            'options_variable' => $discovery['options_variable'],
            'key_variable' => $discovery['key_variable'],
            'label_variable' => $discovery['label_variable'],
            'label_usage' => $discovery['label_usage'],
            'source_type' => $discovery['source_type'],
            'source_reference' => (string) ($discovery['source_reference'] ?? $discovery['source_path'] . ':' . $discovery['source_line']),
            'options_count' => count($discovery['options']),
            'suggested_state' => $suggestedState,
            'synced_at' => now()->toISOString(),
        ];

        $compareOldDiscovery = $oldDiscovery;
        unset($compareOldDiscovery['synced_at']);
        $compareNewDiscovery = $newDiscovery;
        unset($compareNewDiscovery['synced_at']);

        if ($compareOldDiscovery === $compareNewDiscovery) {
            return 'entry_discovery_unchanged';
        }

        $newMeta = [
            ...$oldMeta,
            'dynamic_option_discovery' => $newDiscovery,
        ];

        $entry->forceFill(['meta' => $newMeta])->save();

        $this->recordEvent(
            $entry,
            $oldDiscovery === null ? 'dynamic_option_discovery_created' : 'dynamic_option_discovery_updated',
            ['dynamic_option_discovery' => $oldDiscovery],
            ['dynamic_option_discovery' => $newDiscovery],
        );

        return $oldDiscovery === null
            ? 'entry_discovery_created'
            : 'entry_discovery_updated';
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>}  $discovery
     * @return array{label: string, summary_key: string}
     */
    private function suggestedState(array $discovery, ?TranslationWorkbenchEntry $entry): array
    {
        if (! $entry) {
            return [
                'label' => 'no_workbench_entry',
                'summary_key' => 'suggested_missing_entry',
            ];
        }

        $workbenchSuggestedKey = trim((string) $entry->suggested_key);

        if ($workbenchSuggestedKey === $discovery['suggested_key']) {
            return [
                'label' => 'suggested_equal',
                'summary_key' => 'suggested_equal',
            ];
        }

        return [
            'label' => $workbenchSuggestedKey === '' ? 'workbench_suggested_missing' : 'suggested_different',
            'summary_key' => 'suggested_different',
        ];
    }

    private function recordEvent(
        TranslationWorkbenchEntry $entry,
        string $eventType,
        ?array $oldValues,
        array $newValues,
        array $context = [],
    ): void {
        TranslationWorkbenchEvent::query()->create([
            'entry_id' => $entry->id,
            'event_type' => $eventType,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'context' => [
                'source' => 'translation-workbench:discover-dynamic-options',
                ...$context,
            ],
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function paths(): array
    {
        $paths = trim((string) $this->option('paths'));

        if ($paths !== '') {
            return collect(explode(',', $paths))
                ->map(static fn(string $path): string => trim($path))
                ->filter()
                ->values()
                ->all();
        }

        return collect((array) config('translation-workbench.paths', ['resources/views']))
            ->filter(static fn(mixed $path): bool => is_string($path) && trim($path) !== '')
            ->values()
            ->all();
    }

    /**
     * @param  array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>, source_reference?: string}  $discovery
     */
    private function optionDiscoveryFingerprint(array $discovery): string
    {
        return $this->fingerprintFactory->signature([
            $discovery['scope'],
            $discovery['suggested_key'],
            $discovery['suggested_dynamic_key'],
            $discovery['source_path'],
            (string) $discovery['source_line'],
            $discovery['options_variable'],
            $discovery['key_variable'],
            $discovery['label_variable'],
        ]);
    }

    private function lineNumberForOffset(string $contents, int $offset): int
    {
        return substr_count(substr($contents, 0, $offset), "\n") + 1;
    }
}
