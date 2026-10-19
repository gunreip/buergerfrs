<?php

namespace Gunreip\TranslationWorkbench\Scanner;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

/** Source observation only; this collector does not persist Workbench records. */
class TranslationWorkbenchDynamicOptionCollector
{
    public function __construct(
        private readonly SuggestedKeyFactory $suggestedKeyFactory,
        private readonly TranslationFindingNormalizer $findingNormalizer,
    ) {}

    public function collect(array $paths): Collection
    {
        return $this->discoveries($paths, $this->groupOptions($this->hardcodedPublicArrayOptions()->concat($this->providerMethodOptions())));
    }

    /** Parse source declarations only; never invoke providers or execute application classes. */
    public function collectStatic(array $paths): Collection
    {
        return $this->discoveries($paths, $this->groupOptions($this->hardcodedPublicArrayOptions()));
    }

    private function groupOptions(Collection $options): Collection
    {
        return $options->groupBy('name')->map(static function (Collection $candidates): array {
            $rows = $candidates->values()->all();
            return [
                'source_file' => $candidates->pluck('source_file')->implode(', '),
                'options' => count($rows) === 1 ? $rows[0]['options'] : [],
                'source_candidates' => $rows,
                'source_resolution' => count($rows) === 1 ? 'single_candidate' : 'ambiguous_source',
            ];
        });
    }

    /**
     * @param  Collection<string, array{source_file: string, options: array<string, string>}>  $phpArrayOptions
     * @return Collection<int, array{scope: string, suggested_key: string, suggested_dynamic_key: string, source_path: string, source_line: int, options_variable: string, key_variable: string, label_variable: string, label_usage: string, source_type: string, options: array<string, string>}>
     */
    private function discoveries(array $paths, Collection $phpArrayOptions): Collection
    {
        return collect($paths)
            ->flatMap(function (string $path) use ($phpArrayOptions): array {
                $absolutePath = base_path($path);

                if (! File::isDirectory($absolutePath) && ! File::isFile($absolutePath)) {
                    return [];
                }

                $files = File::isDirectory($absolutePath)
                    ? collect(File::allFiles($absolutePath))
                    : collect([new \SplFileInfo($absolutePath)]);

                return $files
                    ->filter(static fn (\SplFileInfo $file): bool => str_ends_with($file->getFilename(), '.blade.php'))
                    ->flatMap(fn (\SplFileInfo $file): array => $this->discoveriesInBladeFile($file, $phpArrayOptions))
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
        $relativePath = str_replace('\\', '/', Str::after($file->getPathname(), base_path().DIRECTORY_SEPARATOR));
        $discoveries = [];
        $pattern = '/@foreach\s*\(\s*\$(?<options>[A-Za-z_][A-Za-z0-9_]*)\s+as\s+\$(?<key>[A-Za-z_][A-Za-z0-9_]*)\s*=>\s*\$(?<label>[A-Za-z_][A-Za-z0-9_]*)\s*\)(?<body>.*?)@endforeach/su';

        preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        foreach ($matches as $match) {
            $body = (string) $match['body'][0];
            $labelVariable = (string) $match['label'][0];
            $labelUsage = $this->labelUsageInBody($body, $labelVariable, (string) $match['key'][0]);

            if ($labelUsage === null) {
                continue;
            }

            $optionsVariable = (string) $match['options'][0];
            $scope = $this->findingNormalizer->scopeFromOptionsVariable($optionsVariable);
            $suggestedKey = $this->suggestedKeyFactory
                ->forDynamicExpressionAtSource($scope, $relativePath)
                ->key;
            $line = $this->lineNumberForOffset($contents, (int) $match[0][1]);
            $hardcodedOptions = $this->withViewBindings($phpArrayOptions->get($optionsVariable), $relativePath);
            $options = $hardcodedOptions['options'] ?? [];

            $discoveries[] = [
                'scope' => $scope,
                'suggested_key' => $suggestedKey,
                'suggested_dynamic_key' => $suggestedKey,
                'source_path' => $relativePath,
                'source_line' => $line,
                'source_end_line' => $this->lineNumberForOffset($contents, (int) $match[0][1] + strlen((string) $match[0][0])),
                'options_variable' => $optionsVariable,
                'key_variable' => (string) $match['key'][0],
                'label_variable' => $labelVariable,
                'label_usage' => $labelUsage,
                'source_type' => $options !== [] ? 'hardcoded_public_array' : 'unresolved',
                'options' => $options,
                'source_reference' => $hardcodedOptions['source_file'] ?? $relativePath.':'.$line,
                'source_candidates' => $hardcodedOptions['source_candidates'] ?? [],
                'source_resolution' => $hardcodedOptions['source_resolution'] ?? 'unresolved',
            ];
        }

        foreach (WorkbenchOptionCall::indexedMatches($contents) as $call) {
            $optionsVariable = $call['options'];
            $source = $this->withViewBindings($phpArrayOptions->get($optionsVariable), $relativePath);
            $options = $source['options'] ?? [];
            $line = $this->lineNumberForOffset($contents, $call['offset']);
            $discoveries[] = [
                'scope' => $this->findingNormalizer->scopeFromOptionsVariable($optionsVariable),
                'suggested_key' => $call['owner'], 'suggested_dynamic_key' => $call['owner'],
                'source_path' => $relativePath, 'source_line' => $line,
                'source_end_line' => $this->lineNumberForOffset($contents, $call['offset'] + strlen($call['raw'])),
                'options_variable' => $optionsVariable, 'key_variable' => $call['key'], 'label_variable' => '',
                'label_usage' => 'workbench_indexed_option', 'option_owner_key' => $call['owner'],
                'source_expression_type' => 'indexed_option',
                'source_type' => $options !== [] ? 'hardcoded_public_array' : 'unresolved',
                'options' => $options, 'source_reference' => $source['source_file'] ?? $relativePath.':'.$line,
                'source_candidates' => $source['source_candidates'] ?? [],
                'source_resolution' => $source['source_resolution'] ?? 'unresolved',
            ];
        }

        return $discoveries;
    }

    private function withViewBindings(?array $source, string $viewPath): ?array
    {
        if ($source === null) {
            return null;
        }
        $binding = new TranslationWorkbenchOptionViewBinding;
        $source['source_candidates'] = array_map(static fn (array $candidate): array => [
            ...$candidate,
            'view_binding' => $binding->inspect($candidate['source_file'], $viewPath, $candidate['name']),
        ], $source['source_candidates']);

        // Narrow only a fully traversed set of literal view/include relationships.
        // Unknown relationships or writes must never win by elimination.
        $candidates = collect($source['source_candidates']);
        $reachable = $candidates->filter(fn ($candidate) => ($candidate['view_binding']['status'] ?? null) === 'static_view_path');
        if ($reachable->count() === 1
            && $candidates->every(fn ($candidate) => ($candidate['view_binding']['include_traversal_complete'] ?? false) === true)
            && ($reachable->sole()['view_binding']['variable_flow']['status'] ?? null) === 'no_detected_override') {
            $selected = $reachable->sole();
            $source['options'] = $selected['options'];
            $source['source_file'] = $selected['source_file'];
            $source['source_resolution'] = 'view_bound_candidate';
        }

        return $source;
    }

    private function labelUsageInBody(string $body, string $labelVariable, string $keyVariable): ?string
    {
        $variable = preg_quote($labelVariable, '/');

        foreach (WorkbenchOptionCall::matches($body) as $call) {
            if ($call['key'] === $keyVariable && $call['label'] === $labelVariable) {
                return 'workbench_option_label';
            }
        }


        if (preg_match('/dynamic_label\s*\(/u', $body) === 1) {
            return 'dynamic_label';
        }

        if (preg_match('/__\(\s*\$'.$variable.'\s*\)/u', $body) === 1) {
            return 'translated_label';
        }

        if (
            preg_match('/\{\{\s*\$'.$variable.'\s*\}\}/u', $body) === 1
            || preg_match('/:\s*label\s*=\s*"\s*\$'.$variable.'\s*"/u', $body) === 1
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
            ->filter(static fn (\SplFileInfo $file): bool => $file->getExtension() === 'php'
                && array_intersect(['_old', '-old'], explode('/', str_replace('\\', '/', $file->getPathname()))) === []);

        return $files
            ->flatMap(function (\SplFileInfo $file): array {
                $contents = File::get($file->getPathname());
                $relativePath = str_replace('\\', '/', Str::after($file->getPathname(), base_path().DIRECTORY_SEPARATOR));
                $arrays = [];

                foreach ($this->publicArrayBodies($contents) as $name => $body) {
                    $options = (new StaticOptionArray)->read($body, $contents);

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
            ->values();
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
            ->filter(static fn (\SplFileInfo $file): bool => $file->getExtension() === 'php'
                && array_intersect(['_old', '-old'], explode('/', str_replace('\\', '/', $file->getPathname()))) === []);

        return $files
            ->flatMap(function (\SplFileInfo $file): array {
                $contents = File::get($file->getPathname());
                $relativePath = str_replace('\\', '/', Str::after($file->getPathname(), base_path().DIRECTORY_SEPARATOR));
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
                        'source_file' => $relativePath.':'.$method,
                        'options' => $normalizedOptions,
                    ];
                }

                return $options;
            })
            ->values();
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
            $bodyLines = [$rest];

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

    private function lineNumberForOffset(string $contents, int $offset): int
    {
        return substr_count(substr($contents, 0, $offset), "\n") + 1;
    }
}
