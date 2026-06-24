<?php

// app/Console/Commands/TranslationsAuditCode.php

namespace App\Console\Commands;

use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use SplFileObject;
use Symfony\Component\Finder\Finder;
use Throwable;

/**
 * Audits translation usage in source code and writes structured audit reports.
 */
class TranslationsAuditCode extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'translations:audit-code';

    /**
     * The console command description.
     */
    protected $description = 'Audit translation calls in application code and write machine-readable reports.';

    private const PREVIEW_LIMIT = 20;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $calls = $this->scanTranslationCalls();

        $native = [];
        $keys = [];
        $dynamic = [];
        $invalid = [];

        foreach ($calls as $call) {
            match ($call['classification']) {
                'native' => $native[] = $call,
                'key' => $keys[] = $call,
                'dynamic' => $dynamic[] = $call,
                default => $invalid[] = $call,
            };
        }

        $summary = [
            'generated_at' => now()->toIso8601String(),
            'root_path' => base_path(),
            'preview_limit' => self::PREVIEW_LIMIT,
            'files_scanned' => $this->scannableFiles()->count(),
            'translation_calls' => count($calls),
            'proper_keys' => count($keys),
            'native_texts' => count($native),
            'dynamic_keys' => count($dynamic),
            'invalid_calls' => count($invalid),
        ];

        $this->writeAuditFile('summary', $summary);
        $this->writeAuditFile('calls', $calls);
        $this->writeAuditFile('native', $native);
        $this->writeAuditFile('keys', $keys);
        $this->writeAuditFile('dynamic', $dynamic);
        $this->writeAuditFile('invalid', $invalid);

        $this->components->info('Translation code audit finished.');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Files scanned', $summary['files_scanned']],
                ['Translation calls', $summary['translation_calls']],
                ['Proper keys', $summary['proper_keys']],
                ['Native texts', $summary['native_texts']],
                ['Dynamic keys', $summary['dynamic_keys']],
                ['Invalid calls', $summary['invalid_calls']],
            ],
        );

        $this->logRunCompletedActivity($summary);

        return self::SUCCESS;
    }

    /**
     * Scan all configured files for translation calls.
     *
     * @return array<int, array<string, mixed>>
     */
    private function scanTranslationCalls(): array
    {
        $calls = [];

        foreach ($this->scannableFiles() as $path) {
            $relativePath = $this->relativePath($path);
            $contents = File::get($path);

            foreach ($this->extractTranslationCalls($contents) as $match) {
                $line = $this->lineNumberForOffset($path, $match['offset']);
                $classification = $this->classifyValue($match['value'], $match['raw'], $match['dynamic']);

                $calls[] = [
                    'classification' => $classification,
                    'function' => $match['function'],
                    'value' => $match['value'],
                    'raw' => $match['raw'],
                    'reason' => $match['reason'],
                    'suggested_key' => $classification === 'native'
                        ? $this->suggestKey($match['value'], $relativePath)
                        : null,
                    'file' => $relativePath,
                    'line' => $line,
                ];
            }
        }

        usort($calls, fn (array $a, array $b): int => [$a['file'], $a['line']] <=> [$b['file'], $b['line']]);

        return $calls;
    }

    /**
     * Files that are scanned by this code audit.
     */
    private function scannableFiles(): Collection
    {
        $finder = Finder::create()
            ->files()
            ->in([
                app_path(),
                base_path('routes'),
                resource_path('views'),
            ])
            ->name('*.php')
            ->name('*.blade.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);

        return collect(iterator_to_array($finder, false))
            ->map(fn ($file): ?string => $file->getRealPath() ?: null)
            ->filter(fn (?string $path): bool => $path !== null && File::isFile($path))
            ->reject(fn (string $path): bool => $this->isParkedFile($path))
            ->values();
    }

    /**
     * Determine whether the file is intentionally parked and should not be audited.
     */
    private function isParkedFile(string $path): bool
    {
        $filename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        return str_contains($filename, 'xxx')
            || str_contains($filename, 'yyy')
            || str_contains($filename, 'zzz');
    }

    /**
     * Extract translation calls from file contents.
     *
     * @return array<int, array{function: string, value: string, raw: string, offset: int, dynamic: bool, reason: string|null}>
     */
    private function extractTranslationCalls(string $contents): array
    {
        $calls = [];

        $literalPatterns = [
            '/(?P<function>__|trans)\(\s*(?P<quote>[\'"])(?P<value>(?:\\\\.|(?!\k<quote>).)*)\k<quote>/su',
            '/(?P<function>@lang)\(\s*(?P<quote>[\'"])(?P<value>(?:\\\\.|(?!\k<quote>).)*)\k<quote>/su',
            '/(?P<function>Lang::get)\(\s*(?P<quote>[\'"])(?P<value>(?:\\\\.|(?!\k<quote>).)*)\k<quote>/su',
        ];

        foreach ($literalPatterns as $pattern) {
            preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

            foreach ($matches as $match) {
                $calls[] = [
                    'function' => $match['function'][0],
                    'value' => stripcslashes($match['value'][0]),
                    'raw' => $this->extractCallSnippet($contents, $match[0][1]),
                    'offset' => $match[0][1],
                    'dynamic' => false,
                    'reason' => null,
                ];
            }
        }

        foreach ($this->extractDynamicTranslationCalls($contents) as $dynamicCall) {
            $alreadyCaptured = collect($calls)
                ->contains(fn (array $call): bool => $call['offset'] === $dynamicCall['offset']);

            if (! $alreadyCaptured) {
                $calls[] = $dynamicCall;
            }
        }

        usort($calls, fn (array $a, array $b): int => $a['offset'] <=> $b['offset']);

        return $calls;
    }

    /**
     * Extract translation calls that do not start with a literal string.
     *
     * @return array<int, array{function: string, value: string, raw: string, offset: int, dynamic: bool, reason: string|null}>
     */
    private function extractDynamicTranslationCalls(string $contents): array
    {
        $patterns = [
            '/(?P<function>__|trans)\(\s*(?P<argument>[^\'"\s][^)]*)\)/su',
            '/(?P<function>@lang)\(\s*(?P<argument>[^\'"\s][^)]*)\)/su',
            '/(?P<function>Lang::get)\(\s*(?P<argument>[^\'"\s][^)]*)\)/su',
        ];

        $calls = [];

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

            foreach ($matches as $match) {
                $argument = trim($match['argument'][0]);

                $calls[] = [
                    'function' => $match['function'][0],
                    'value' => $argument,
                    'raw' => $this->extractCallSnippet($contents, $match[0][1]),
                    'offset' => $match[0][1],
                    'dynamic' => true,
                    'reason' => 'non_literal_first_argument',
                ];
            }
        }

        return $calls;
    }

    /**
     * Extract a readable single-call snippet from source contents.
     */
    private function extractCallSnippet(string $contents, int $offset): string
    {
        $length = strlen($contents);
        $position = $offset;
        $depth = 0;
        $quote = null;
        $escaped = false;

        while ($position < $length) {
            $character = $contents[$position];

            if ($quote !== null) {
                if ($escaped) {
                    $escaped = false;
                    $position++;

                    continue;
                }

                if ($character === '\\') {
                    $escaped = true;
                    $position++;

                    continue;
                }

                if ($character === $quote) {
                    $quote = null;
                }

                $position++;

                continue;
            }

            if ($character === '\'' || $character === '"') {
                $quote = $character;
                $position++;

                continue;
            }

            if ($character === '(') {
                $depth++;
                $position++;

                continue;
            }

            if ($character === ')') {
                $depth--;
                $position++;

                if ($depth <= 0) {
                    return trim(substr($contents, $offset, $position - $offset));
                }

                continue;
            }

            if ($character === "\n" && $depth <= 0) {
                return trim(substr($contents, $offset, $position - $offset));
            }

            $position++;
        }

        $snippet = substr($contents, $offset, 500);
        $lineEndPosition = strpos($snippet, "\n");

        if ($lineEndPosition !== false) {
            return trim(substr($snippet, 0, $lineEndPosition));
        }

        return trim($snippet);
    }

    /**
     * Classify a translation call value.
     */
    private function classifyValue(string $value, string $raw, bool $dynamic = false): string
    {
        $value = trim($value);

        if ($value === '') {
            return 'invalid';
        }

        if ($dynamic) {
            return 'dynamic';
        }

        if ($this->looksLikeTranslationKey($value)) {
            return 'key';
        }

        return 'native';
    }

    /**
     * Decide whether a string already looks like a real translation key.
     */
    private function looksLikeTranslationKey(string $value): bool
    {
        return str_contains($value, '.')
            && ! str_contains($value, ' ')
            && preg_match('/^[a-z0-9_.-]+$/', $value) === 1;
    }

    /**
     * Suggest a first-pass key for native texts.
     */
    private function suggestKey(string $value, string $relativePath): string
    {
        $namespace = $this->namespaceFromPath($relativePath);
        $slug = str($value)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/i', '_')
            ->trim('_')
            ->limit(80, '')
            ->toString();

        if ($slug === '') {
            $slug = 'text';
        }

        return $namespace.'.'.$slug;
    }

    /**
     * Build a rough namespace from a file path.
     */
    private function namespaceFromPath(string $relativePath): string
    {
        $path = str($relativePath)
            ->replace('\\', '/')
            ->replace('resources/views/components/', '')
            ->replace('resources/views/livewire/', '')
            ->replace('resources/views/', '')
            ->replace('app/Livewire/', '')
            ->replace('app/', '')
            ->replace('routes/', '')
            ->replaceMatches('#(^|/)partials/#', '$1')
            ->replace('.blade.php', '')
            ->replace('.php', '')
            ->replace('⚡', '')
            ->replace('/', '.')
            ->replace('-', '_')
            ->replaceMatches('/(?<!^)[A-Z]/', '_$0')
            ->lower()
            ->toString();

        $path = trim($path, '.');

        $path = preg_replace('/(^|\\.)_+/', '$1', $path) ?: $path;
        $path = preg_replace('/_+(\\.|$)/', '$1', $path) ?: $path;

        return $path !== '' ? $path : 'translations';
    }

    /**
     * Calculate the source line for an offset.
     */
    private function lineNumberForOffset(string $path, int $offset): int
    {
        $file = new SplFileObject($path);
        $position = 0;
        $lineNumber = 1;

        while (! $file->eof()) {
            $line = $file->fgets();
            $position += strlen($line);

            if ($position > $offset) {
                return $lineNumber;
            }

            $lineNumber++;
        }

        return $lineNumber;
    }

    /**
     * Write full and preview audit files.
     */
    private function writeAuditFile(string $name, array $data): void
    {
        $directory = storage_path('audits/translations/code');

        File::ensureDirectoryExists($directory);

        $fullPath = $directory.DIRECTORY_SEPARATOR.$name.'.json';
        $previewPath = $directory.DIRECTORY_SEPARATOR.$name.'.preview.json';
        $fullContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL;
        $previewContent = json_encode($this->previewData($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL;

        File::put($fullPath, $fullContent);

        File::put($previewPath, $previewContent);
    }

    /**
     * Build preview data with a limited number of entries.
     */
    private function previewData(array $data): array
    {
        if ($this->isList($data)) {
            return [
                'preview' => true,
                'preview_limit' => self::PREVIEW_LIMIT,
                'total' => count($data),
                'items' => array_slice($data, 0, self::PREVIEW_LIMIT),
            ];
        }

        return [
            'preview' => true,
            'preview_limit' => self::PREVIEW_LIMIT,
            'data' => $data,
        ];
    }

    /**
     * Determine whether the given array is a list.
     */
    private function isList(array $data): bool
    {
        return array_keys($data) === range(0, count($data) - 1);
    }

    /**
     * Convert an absolute path to a project-relative path.
     */
    private function relativePath(string $path): string
    {
        return str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    private function logRunCompletedActivity(array $summary): void
    {
        try {
            activity('translations')
                ->event('translations.audit.code.completed')
                ->withProperties(ConsoleActivityContext::merge($this, [
                    'summary' => $summary,
                ]))
                ->log('Translation code audit completed');
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed for command run summary: '.$exception->getMessage());
        }
    }
}
