<?php

namespace Gunreip\TranslationWorkbench\Scanner;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class TranslationWorkbenchScanner
{
    public function __construct(
        private readonly SuggestedKeyFactory $suggestedKeyFactory,
        private readonly TranslationFingerprintFactory $fingerprintFactory,
        private readonly TranslationFindingClassifier $findingClassifier,
        private readonly TranslationFindingNormalizer $findingNormalizer,
    ) {}

    /**
     * @param  array<int, string>|null  $paths
     * @return Collection<int, DiscoveredTranslation>
     */
    public function scan(?array $paths = null): Collection
    {
        $items = collect();

        foreach ($this->scannableFiles($paths) as $file) {
            $items = $items->merge($this->scanFile($file->getPathname()));
        }

        return $items
            ->sortBy(fn (DiscoveredTranslation $item): array => [
                $item->sourcePath,
                $item->sourceLine ?? 0,
                $item->kind,
                $item->rawExpression ?? '',
            ])
            ->values();
    }

    /**
     * @param  array<int, string>|null  $paths
     * @return iterable<int, SplFileInfo>
     */
    public function scannableFiles(?array $paths = null): iterable
    {
        $paths ??= (array) config('translation-workbench.paths', []);

        $directories = collect($paths)
            ->map(static fn (string $path): string => base_path($path))
            ->filter(static fn (string $path): bool => File::isDirectory($path))
            ->values()
            ->all();

        $files = collect($paths)
            ->map(static fn (string $path): string => base_path($path))
            ->filter(static fn (string $path): bool => File::isFile($path))
            ->reject(fn (string $path): bool => $this->isExcludedPath($path))
            ->map(static fn (string $path): SplFileInfo => new SplFileInfo($path))
            ->values()
            ->all();

        if ($directories === []) {
            return $files;
        }

        $finder = Finder::create()
            ->files()
            ->in($directories)
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);

        foreach ((array) config('translation-workbench.file_patterns', ['*.php', '*.blade.php']) as $pattern) {
            $finder->name($pattern);
        }

        return collect($files)
            ->merge(iterator_to_array($finder, false))
            ->reject(fn (SplFileInfo $file): bool => $this->isExcludedPath($file->getPathname()))
            ->all();
    }

    /**
     * @return Collection<int, DiscoveredTranslation>
     */
    public function scanFile(string $path): Collection
    {
        if ($this->isParkedFile($path)) {
            return collect();
        }

        $contents = File::get($path);
        $relativePath = $this->relativePath($path);
        $commentRanges = $this->commentRanges($contents);

        return collect()
            ->merge($this->extractLiteralTranslationCalls($contents, $relativePath, $commentRanges))
            ->merge($this->extractDynamicTranslationCalls($contents, $relativePath, $commentRanges))
            ->merge($this->extractDynamicLabelCalls($contents, $relativePath, $commentRanges))
            ->unique(static fn (DiscoveredTranslation $item): string => $item->sourceSignature)
            ->values();
    }

    /**
     * @param  array<int, array{start: int, end: int, type: string}>  $commentRanges
     * @return array<int, DiscoveredTranslation>
     */
    private function extractLiteralTranslationCalls(string $contents, string $relativePath, array $commentRanges): array
    {
        $items = [];
        $patterns = [
            '/(?P<function>__|trans)\(\s*(?P<quote>[\'"])(?P<value>(?:\\\\.|(?!\k<quote>).)*)\k<quote>/su',
            '/(?P<function>@lang)\(\s*(?P<quote>[\'"])(?P<value>(?:\\\\.|(?!\k<quote>).)*)\k<quote>/su',
            '/(?P<function>Lang::get)\(\s*(?P<quote>[\'"])(?P<value>(?:\\\\.|(?!\k<quote>).)*)\k<quote>/su',
        ];

        foreach ($patterns as $pattern) {
            if (! preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
                continue;
            }

            foreach ($matches as $match) {
                $function = (string) $match['function'][0];
                $value = stripcslashes((string) $match['value'][0]);
                $offset = (int) $match[0][1];
                $raw = $this->rawCallAt($contents, $offset);
                $line = $this->lineNumberForOffset($contents, $offset);
                $kind = $this->findingNormalizer->looksLikeTranslationKey($value) ? 'key' : 'literal';
                $existingKey = $kind === 'key' ? $value : null;
                $translationKey = $kind === 'key' ? $value : null;
                $suggestedKey = $kind === 'literal'
                    ? $this->suggestedKeyFactory->forLiteral($value, $relativePath)
                    : $this->suggestedKeyFactory->forExistingKeyAtSource($value, $relativePath);
                $commentContext = $this->commentContextForOffset($commentRanges, $offset);

                $items[] = $this->makeDiscoveredTranslation(
                    kind: $kind,
                    sourcePath: $relativePath,
                    sourceLine: $line,
                    functionName: $function,
                    rawExpression: $raw,
                    literalText: $kind === 'literal' ? $value : null,
            literalTextSuggested: $kind === 'literal'
                ? null
                : $this->findingNormalizer->literalTextSuggestedFromTranslationKey($value),
            existingKey: $existingKey,
            translationKey: $translationKey,
            translationKeySource: $translationKey !== null ? 'code' : null,
            suggestedKey: $suggestedKey?->key,
                    keyResult: $suggestedKey,
                    meta: [
                        'scanner' => 'literal_translation_call',
                        'existing_key' => $existingKey,
                        'suggested_key_path' => $suggestedKey?->pathSegments,
                        'suggested_key_name' => $suggestedKey?->keyName,
                        'suggested_key_source' => $kind === 'key' ? 'existing_key_at_source' : 'literal',
                        ...$commentContext,
                    ],
                );
            }
        }

        return $items;
    }

    /**
     * @param  array<int, array{start: int, end: int, type: string}>  $commentRanges
     * @return array<int, DiscoveredTranslation>
     */
    private function extractDynamicTranslationCalls(string $contents, string $relativePath, array $commentRanges): array
    {
        $items = [];
        $translatedPropDefaults = $this->translatedPropDefaults($contents);
        $patterns = [
            '/(?P<function>__|trans)\(\s*(?P<argument>(?![\'"])[^,\)\n]+)/su',
            '/(?P<function>@lang)\(\s*(?P<argument>(?![\'"])[^,\)\n]+)/su',
            '/(?P<function>Lang::get)\(\s*(?P<argument>(?![\'"])[^,\)\n]+)/su',
            '/(?P<function>@js)\(\s*(?P<argument>\$[A-Za-z_][A-Za-z0-9_]*)\s*\)/su',
        ];

        foreach ($patterns as $pattern) {
            if (! preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
                continue;
            }

            foreach ($matches as $match) {
                $argument = $this->findingNormalizer->normalizedDynamicArgument((string) $match['argument'][0]);

                if (! $this->findingNormalizer->isDynamicArgumentCandidate($argument)) {
                    continue;
                }

                $function = (string) $match['function'][0];
                $offset = (int) $match[0][1];
                $raw = $this->rawCallAt($contents, (int) $match[0][1]);
                $line = $this->lineNumberForOffset($contents, (int) $match[0][1]);
                $commentContext = $this->commentContextForOffset($commentRanges, $offset);
                $optionLoopContext = $this->optionLoopContextForDynamicArgument($contents, $offset, $argument);
                $isNumericDynamic = $this->findingNormalizer->isDynamicNumericArgument($argument, $raw);
                $resolvedStaticProp = $this->resolvedStaticTranslatedProp($argument, $translatedPropDefaults);
                $dynamicKeyName = $optionLoopContext['scope'] ?? $this->findingNormalizer->dynamicKeyNameFromArgument($argument);
                $scope = $dynamicKeyName;
                $suggestedKey = $this->suggestedKeyFactory->forDynamicExpressionAtSource($dynamicKeyName, $relativePath);

                $items[] = $this->makeDiscoveredTranslation(
                    kind: $resolvedStaticProp !== null
                        ? 'dynamic_resolved_static_prop'
                        : ($isNumericDynamic ? 'dynamic_numeric' : 'dynamic_multi'),
                    sourcePath: $relativePath,
                    sourceLine: $line,
                    functionName: $function,
                    rawExpression: $raw,
                    literalText: null,
                    literalTextSuggested: $this->findingNormalizer->literalTextSuggestedFromDynamicKeyName($dynamicKeyName),
                    existingKey: null,
                    translationKey: null,
                    translationKeySource: null,
                    suggestedKey: $suggestedKey->key,
                    keyResult: $suggestedKey,
                    meta: [
                        'scanner' => 'dynamic_translation_call',
                        'dynamic_expression' => $argument,
                        'dynamic_key_name' => $dynamicKeyName,
                        'dynamic_scope' => $scope,
                        'dynamic_value_type' => $resolvedStaticProp !== null
                            ? 'static_translated_prop'
                            : ($isNumericDynamic ? 'numeric' : 'text'),
                        'translation_relevant' => $resolvedStaticProp === null && ! $isNumericDynamic,
                        'resolved_by_static_prop_translation' => $resolvedStaticProp,
                        'dynamic_option_context' => $optionLoopContext,
                        'suggested_key_path' => $suggestedKey->pathSegments,
                        'suggested_key_name' => $suggestedKey->keyName,
                        ...$commentContext,
                    ],
                );
            }
        }

        return $items;
    }

    /**
     * @return array<string, array{literal: string, function: string, raw_expression: string}>
     */
    private function translatedPropDefaults(string $contents): array
    {
        if (! preg_match_all('/@props\s*\(\s*\[(?<body>.*?)\]\s*\)/su', $contents, $matches, PREG_SET_ORDER)) {
            return [];
        }

        $defaults = [];

        foreach ($matches as $match) {
            $body = (string) $match['body'];

            if (! preg_match_all(
                '/(?P<quote>[\'"])(?P<prop>[A-Za-z_][A-Za-z0-9_]*)\k<quote>\s*=>\s*(?P<function>__|trans)\(\s*(?P<literal_quote>[\'"])(?P<literal>(?:\\\\.|(?!\k<literal_quote>).)*)\k<literal_quote>\s*\)/su',
                $body,
                $propMatches,
                PREG_SET_ORDER,
            )) {
                continue;
            }

            foreach ($propMatches as $propMatch) {
                $prop = (string) $propMatch['prop'];
                $literal = stripcslashes((string) $propMatch['literal']);
                $defaults[$prop] = [
                    'literal' => $literal,
                    'function' => (string) $propMatch['function'],
                    'raw_expression' => (string) $propMatch[0],
                ];
            }
        }

        return $defaults;
    }

    /**
     * @param  array<string, array{literal: string, function: string, raw_expression: string}>  $translatedPropDefaults
     * @return array{prop: string, literal: string, function: string, raw_expression: string}|null
     */
    private function resolvedStaticTranslatedProp(string $argument, array $translatedPropDefaults): ?array
    {
        if (! preg_match('/^\$(?<prop>[A-Za-z_][A-Za-z0-9_]*)$/', trim($argument), $match)) {
            return null;
        }

        $prop = (string) $match['prop'];

        if (! isset($translatedPropDefaults[$prop])) {
            return null;
        }

        return [
            'prop' => $prop,
            ...$translatedPropDefaults[$prop],
        ];
    }

    /**
     * @param  array<int, array{start: int, end: int, type: string}>  $commentRanges
     * @return array<int, DiscoveredTranslation>
     */
    private function extractDynamicLabelCalls(string $contents, string $relativePath, array $commentRanges): array
    {
        $items = [];
        $pattern = '/dynamic_label\(\s*(?P<quote>[\'"])(?P<scope>(?:\\\\.|(?!\k<quote>).)*)\k<quote>\s*,(?P<args>.*?)\)/su';

        if (! preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return [];
        }

        foreach ($matches as $match) {
            $scope = stripcslashes((string) $match['scope'][0]);
            $offset = (int) $match[0][1];
            $raw = $this->rawCallAt($contents, $offset);
            $line = $this->lineNumberForOffset($contents, $offset);
            $commentContext = $this->commentContextForOffset($commentRanges, $offset);
            $suggestedKey = $this->suggestedKeyFactory->forDynamicExpressionAtSource($scope, $relativePath);

            $items[] = $this->makeDiscoveredTranslation(
                kind: 'dynamic_multi',
                sourcePath: $relativePath,
                sourceLine: $line,
                functionName: 'dynamic_label',
                rawExpression: $raw,
                literalText: null,
                literalTextSuggested: $this->findingNormalizer->literalTextSuggestedFromRawExpression($raw),
                existingKey: null,
                translationKey: null,
                translationKeySource: null,
                suggestedKey: $suggestedKey->key,
                keyResult: $suggestedKey,
                meta: [
                    'scanner' => 'dynamic_label',
                    'dynamic_scope' => $scope,
                    'suggested_key_path' => $suggestedKey->pathSegments,
                    'suggested_key_name' => $suggestedKey->keyName,
                    'arguments' => trim((string) $match['args'][0]),
                    ...$commentContext,
                ],
            );
        }

        return $items;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function makeDiscoveredTranslation(
        string $kind,
        string $sourcePath,
        ?int $sourceLine,
        ?string $functionName,
        ?string $rawExpression,
        ?string $literalText,
        ?string $literalTextSuggested,
        ?string $existingKey,
        ?string $translationKey,
        ?string $translationKeySource,
        ?string $suggestedKey,
        ?SuggestedKeyResult $keyResult = null,
        array $meta = [],
    ): DiscoveredTranslation {
        $classification = $this->findingClassifier->classify($kind, $literalText, $functionName);

        if ($existingKey !== null) {
            $existingKey = $this->suggestedKeyFactory->forExistingKey($existingKey)->key;
        }

        if ($translationKey !== null) {
            $existingKeyResult = $this->suggestedKeyFactory->forExistingKey($translationKey);
            $translationKey = $existingKeyResult->key;
            $keyResult ??= $existingKeyResult;
        }

        if ($suggestedKey !== null) {
            $suggestedKeyResult = $this->suggestedKeyFactory->forExistingKey($suggestedKey);
            $suggestedKey = $suggestedKeyResult->key;
            $keyResult ??= $suggestedKeyResult;
        }

        $namespace = $keyResult?->namespace;
        $group = $keyResult?->group;
        $sourceSignature = $this->fingerprintFactory->sourceSignature(
            sourcePath: $sourcePath,
            sourceLine: $sourceLine,
            functionName: $functionName,
            kind: $kind,
            suggestedKey: $suggestedKey,
            rawExpression: $rawExpression,
            literalText: $literalText,
            translationKey: $translationKey,
        );
        $semanticValue = $this->fingerprintFactory->normalizedExpression(
            $literalText
                ?? $literalTextSuggested
                ?? $existingKey
                ?? $translationKey
                ?? $suggestedKey
                ?? '',
        );
        $sourceFingerprint = $this->fingerprintFactory->sourceFingerprint($sourcePath, $sourceLine, $rawExpression);
        $expressionFingerprint = $this->fingerprintFactory->expressionFingerprint($rawExpression);
        $semanticFingerprint = $this->fingerprintFactory->semanticFingerprint($kind, $functionName, $semanticValue);
        $fingerprint = $this->fingerprintFactory->entryFingerprint(
            kind: $kind,
            functionName: $functionName,
            literalText: $literalText,
            literalTextSuggested: $literalTextSuggested,
            existingKey: $existingKey,
            translationKey: $translationKey,
            dynamicScope: $this->dynamicScopeFromMeta($meta),
        );

        return new DiscoveredTranslation(
            kind: $kind,
            sourceType: str_ends_with($sourcePath, '.blade.php') ? 'blade' : 'php',
            sourcePath: $sourcePath,
            sourceLine: $sourceLine,
            functionName: $functionName,
            rawExpression: $rawExpression,
            literalText: $literalText,
            literalTextSuggested: $literalTextSuggested,
            existingKey: $existingKey,
            translationKey: $translationKey,
            translationKeySource: $translationKeySource,
            suggestedKey: $suggestedKey,
            namespace: $namespace,
            group: $group,
            sourceSignature: $sourceSignature,
            fingerprint: $fingerprint,
            sourceFingerprint: $sourceFingerprint,
            expressionFingerprint: $expressionFingerprint,
            semanticFingerprint: $semanticFingerprint,
            meta: $meta,
            entryType: $classification['entry_type'],
            candidateType: $classification['candidate_type'],
            candidateReason: $classification['candidate_reason'],
        );
    }

    /**
     * @return array<int, array{start: int, end: int, type: string}>
     */
    private function commentRanges(string $contents): array
    {
        $ranges = [];
        $patterns = [
            'blade_comment' => '/\{\{--.*?--\}\}/su',
            'html_comment' => '/<!--.*?-->/su',
            'php_block_comment' => '/\/\*.*?\*\//su',
            'php_line_comment' => '/^[ \t]*(?:\/\/|#).*$/mu',
        ];

        foreach ($patterns as $type => $pattern) {
            if (! preg_match_all($pattern, $contents, $matches, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            foreach ($matches[0] as $match) {
                $start = (int) $match[1];
                $ranges[] = [
                    'start' => $start,
                    'end' => $start + strlen((string) $match[0]),
                    'type' => $type,
                ];
            }
        }

        usort($ranges, static fn(array $left, array $right): int => $left['start'] <=> $right['start']);

        return $ranges;
    }

    /**
     * @param  array<int, array{start: int, end: int, type: string}>  $commentRanges
     * @return array<string, mixed>
     */
    private function commentContextForOffset(array $commentRanges, int $offset): array
    {
        foreach ($commentRanges as $range) {
            if ($offset < $range['start']) {
                return [];
            }

            if ($offset >= $range['start'] && $offset < $range['end']) {
                return [
                    'source_context' => 'commented_out',
                    'comment_type' => $range['type'],
                    'translation_relevant' => false,
                ];
            }
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function dynamicScopeFromMeta(array $meta): ?string
    {
        $scope = $meta['dynamic_scope'] ?? null;

        return is_string($scope) && $scope !== '' ? $scope : null;
    }

    /**
     * Detect whether a dynamic translation argument is fed by the surrounding
     * Blade loop. Keep suggested-key naming independent from this context; the
     * context only classifies the finding as runtime option-like data.
     *
     * @return array{options_variable: string, key_variable: string, label_variable: string, argument_variable: string, argument_variable_role: string, value_transform: string|null, source_expression_type: string, scope?: string}|null
     */
    private function optionLoopContextForDynamicArgument(string $contents, int $offset, string $argument): ?array
    {
        $argumentVariableContext = $this->dynamicArgumentVariableContext($argument);

        if ($argumentVariableContext === null) {
            return null;
        }

        $argumentVariable = $argumentVariableContext['variable'];
        $pattern = '/@foreach\s*\(\s*\$(?<options>[A-Za-z_][A-Za-z0-9_]*)\s+as\s+\$(?<key>[A-Za-z_][A-Za-z0-9_]*)\s*=>\s*\$(?<label>[A-Za-z_][A-Za-z0-9_]*)\s*\)(?<body>.*?)@endforeach/su';

        if (! preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return null;
        }

        foreach ($matches as $match) {
            $loopStart = (int) $match[0][1];
            $loopEnd = $loopStart + strlen((string) $match[0][0]);
            $keyVariable = (string) $match['key'][0];
            $labelVariable = (string) $match['label'][0];

            if (! in_array($argumentVariable, [$keyVariable, $labelVariable], true) || $offset < $loopStart || $offset > $loopEnd) {
                continue;
            }

            $optionsVariable = (string) $match['options'][0];
            $argumentVariableRole = $argumentVariable === $keyVariable ? 'key' : 'label';
            $context = [
                'options_variable' => $optionsVariable,
                'key_variable' => $keyVariable,
                'label_variable' => $labelVariable,
                'argument_variable' => $argumentVariable,
                'argument_variable_role' => $argumentVariableRole,
                'value_transform' => $argumentVariableContext['transform'],
                'source_expression_type' => $argumentVariableContext['type'],
            ];

            if ($argumentVariableRole === 'label' && $argumentVariableContext['type'] === 'direct_variable') {
                $context['scope'] = $this->findingNormalizer->scopeFromOptionsVariable($optionsVariable);
            }

            return $context;
        }

        return null;
    }

    /**
     * @return array{variable: string, transform: string|null, type: string}|null
     */
    private function dynamicArgumentVariableContext(string $argument): ?array
    {
        $argument = trim($argument);

        if (preg_match('/^\$(?P<name>[A-Za-z_][A-Za-z0-9_]*)$/u', $argument, $match)) {
            return [
                'variable' => (string) $match['name'],
                'transform' => null,
                'type' => 'direct_variable',
            ];
        }

        if (preg_match('/\bStr::headline\(\s*\$(?P<name>[A-Za-z_][A-Za-z0-9_]*)\b/u', $argument, $match)) {
            return [
                'variable' => (string) $match['name'],
                'transform' => 'headline',
                'type' => 'str_headline',
            ];
        }

        if (preg_match('/\bstr\(\s*\$(?P<name>[A-Za-z_][A-Za-z0-9_]*)\s*\)->headline\(/u', $argument, $match)) {
            return [
                'variable' => (string) $match['name'],
                'transform' => 'headline',
                'type' => 'str_helper_headline',
            ];
        }

        return null;
    }

    private function normalizedExpression(string $value): string
    {
        return $this->findingNormalizer->normalizedExpression($value);
    }

    private function rawCallAt(string $contents, int $offset): string
    {
        $length = strlen($contents);
        $depth = 0;
        $inQuote = null;
        $escaped = false;

        for ($i = $offset; $i < $length; $i++) {
            $char = $contents[$i];

            if ($inQuote !== null) {
                if ($escaped) {
                    $escaped = false;
                } elseif ($char === '\\') {
                    $escaped = true;
                } elseif ($char === $inQuote) {
                    $inQuote = null;
                }

                continue;
            }

            if ($char === '"' || $char === "'") {
                $inQuote = $char;
                continue;
            }

            if ($char === '(') {
                $depth++;
                continue;
            }

            if ($char === ')') {
                $depth--;

                if ($depth <= 0) {
                    return $this->normalizedExpression(substr($contents, $offset, $i - $offset + 1));
                }
            }
        }

        return $this->normalizedExpression(substr($contents, $offset, 180));
    }

    private function lineNumberForOffset(string $contents, int $offset): int
    {
        return substr_count(substr($contents, 0, $offset), "\n") + 1;
    }

    private function isParkedFile(string $path): bool
    {
        $filename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        foreach ((array) config('translation-workbench.ignored_filename_contains', []) as $needle) {
            if ($needle !== '' && str_contains($filename, strtolower((string) $needle))) {
                return true;
            }
        }

        return false;
    }

    private function isExcludedPath(string $path): bool
    {
        $relativePath = $this->relativePath($path);

        foreach ((array) config('translation-workbench.exclude_paths', []) as $excludedPath) {
            $excludedPath = trim(str_replace('\\', '/', (string) $excludedPath), '/');

            if ($excludedPath === '') {
                continue;
            }

            if ($relativePath === $excludedPath || str_starts_with($relativePath, $excludedPath . '/')) {
                return true;
            }
        }

        return false;
    }

    private function relativePath(string $path): string
    {
        $basePath = rtrim(str_replace('\\', '/', base_path()), '/');
        $normalizedPath = str_replace('\\', '/', $path);

        return ltrim(str_starts_with($normalizedPath, $basePath)
            ? substr($normalizedPath, strlen($basePath))
            : $normalizedPath, '/');
    }
}
