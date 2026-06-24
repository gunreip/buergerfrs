<?php

// app/Console/Commands/CheckViewHtmlStructure.php

// php artisan html:sync-native-tags

namespace App\Console\Commands;

use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SplFileInfo;
use Throwable;

#[Signature('html:check')]
#[Description('Check Blade views for unclosed or mismatched native HTML tags and selected custom components.')]
/**
 * Audits Blade view structure and reports invalid HTML/component nesting issues.
 */
class CheckViewHtmlStructure extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $files = collect(File::allFiles(resource_path('views')))
            ->filter(fn (SplFileInfo $file): bool => Str::endsWith($file->getFilename(), '.blade.php'))
            ->reject(fn (SplFileInfo $file): bool => str_contains($this->relativePath($file->getPathname()), '/xxx/')
                || str_contains($this->relativePath($file->getPathname()), '/yyy/')
                || str_contains($this->relativePath($file->getPathname()), '/zzz/')
                || str_contains($file->getFilename(), 'xxx')
                || str_contains($file->getFilename(), 'yyy')
                || str_contains($file->getFilename(), 'zzz'))
            ->values();

        $nativeReference = $this->nativeTagReference();
        $componentReference = $this->componentTagReference();

        $nativeTags = $nativeReference['tags']['normal'];
        $componentTags = $componentReference['tags'];

        $nativeProblems = [];
        $customProblems = [];

        foreach ($files as $file) {
            $relativePath = $this->relativePath($file->getPathname());
            $content = File::get($file->getPathname());

            $nativeProblems = [
                ...$nativeProblems,
                ...$this->checkSection(
                    content: $content,
                    file: $relativePath,
                    tagsToCheck: $nativeTags,
                    section: 'native_html',
                ),
            ];

            $customProblems = [
                ...$customProblems,
                ...$this->checkSection(
                    content: $content,
                    file: $relativePath,
                    tagsToCheck: $componentTags,
                    section: 'custom_components',
                ),
            ];
        }

        $problemCount = count($nativeProblems) + count($customProblems);

        $payload = [
            'generated_at' => now()->toIso8601String(),
            'files_scanned' => $files->count(),
            'problem_count' => $problemCount,
            'note' => 'This is a static Blade structure audit. Complex conditional Blade markup may produce false positives and should be reviewed manually.',
            'references' => [
                'native_html' => $nativeReference['meta'],
                'custom_components' => $componentReference['meta'],
            ],
            'sections' => [
                'native_html' => [
                    'problem_count' => count($nativeProblems),
                    'problems' => $nativeProblems,
                ],
                'custom_components' => [
                    'problem_count' => count($customProblems),
                    'problems' => $customProblems,
                ],
            ],
        ];

        $previewPayload = [
            ...$payload,
            'sections' => [
                'native_html' => [
                    'problem_count' => count($nativeProblems),
                    'problems' => array_slice($nativeProblems, 0, 20),
                ],
                'custom_components' => [
                    'problem_count' => count($customProblems),
                    'problems' => array_slice($customProblems, 0, 20),
                ],
            ],
        ];

        File::ensureDirectoryExists(storage_path('audits/html'));

        File::put(
            storage_path('audits/html/view-html-check.json'),
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL,
        );

        File::put(
            storage_path('audits/html/view-html-check-preview.json'),
            json_encode($previewPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL,
        );

        $this->line('HTML / Blade structure check');
        $this->line('Files scanned: '.$files->count());
        $this->line('Problems: '.$problemCount);
        $this->line('Native HTML reference: '.$nativeReference['meta']['source']);
        $this->line('Native normal tags: '.$nativeReference['meta']['normal_count']);
        $this->line('Native void tags ignored: '.$nativeReference['meta']['void_count']);

        $this->line('Component reference: '.$componentReference['meta']['source']);
        $this->line('Component tags: '.$componentReference['meta']['tag_count']);

        if ($nativeReference['meta']['fallback'] === true) {
            $this->warn('Native HTML reference fallback is active.');
            $this->warn('Reason: '.$nativeReference['meta']['fallback_reason']);
            $this->warn('Hint: '.$nativeReference['meta']['fallback_hint']);
        }

        if ($componentReference['meta']['fallback'] === true) {
            $this->warn('Component reference fallback is active.');
            $this->warn('Reason: '.$componentReference['meta']['fallback_reason']);
            $this->warn('Hint: '.$componentReference['meta']['fallback_hint']);
        }

        $this->warn('Note: This is a static Blade structure audit. Complex conditional Blade markup may produce false positives and should be reviewed manually.');
        $this->newLine();

        $this->printSection('Native HTML Tags', $nativeProblems);
        $this->printSection('Custom Components', $customProblems);

        $this->newLine();
        $this->line('Audit written: storage/audits/html/view-html-check.json');
        $this->line('Preview written: storage/audits/html/view-html-check-preview.json');

        $exitCode = $problemCount === 0 ? self::SUCCESS : self::FAILURE;

        $this->logRunActivity(
            $exitCode === self::SUCCESS ? 'html.view_check.completed' : 'html.view_check.completed_with_findings',
            $exitCode === self::SUCCESS
                ? 'HTML view structure check completed without findings.'
                : 'HTML view structure check completed with findings.',
            [
                'files_scanned' => $files->count(),
                'problem_count' => $problemCount,
                'native_problem_count' => count($nativeProblems),
                'custom_problem_count' => count($customProblems),
            ],
        );

        return $exitCode;
    }

    /**
     * @return array<int, array<string, int|string|null>>
     */
    private function checkSection(string $content, string $file, array $tagsToCheck, string $section): array
    {
        $cleanContent = $this->stripComments($content);
        $tokens = $this->extractTagTokens($cleanContent, $tagsToCheck);

        $stack = [];
        $problems = [];

        foreach ($tokens as $token) {
            if ($token['type'] === 'open') {
                $stack[] = $token;

                continue;
            }

            $last = array_pop($stack);

            if ($last === null) {
                $problems[] = [
                    'section' => $section,
                    'type' => 'unexpected_closing',
                    'file' => $file,
                    'tag' => $token['tag'],
                    'opened_line' => null,
                    'closing_line' => $token['line'],
                    'expected_closing' => null,
                    'actual_closing' => '</'.$token['tag'].'>',
                ];

                continue;
            }

            if ($last['tag'] !== $token['tag']) {
                $problems[] = [
                    'section' => $section,
                    'type' => 'mismatched',
                    'file' => $file,
                    'tag' => $last['tag'],
                    'opened_line' => $last['line'],
                    'closing_tag' => $token['tag'],
                    'closing_line' => $token['line'],
                    'expected_closing' => '</'.$last['tag'].'>',
                    'actual_closing' => '</'.$token['tag'].'>',
                ];
            }
        }

        foreach (array_reverse($stack) as $unclosed) {
            $problems[] = [
                'section' => $section,
                'type' => 'unclosed',
                'file' => $file,
                'tag' => $unclosed['tag'],
                'opened_line' => $unclosed['line'],
                'closing_line' => null,
                'expected_closing' => '</'.$unclosed['tag'].'>',
                'actual_closing' => null,
            ];
        }

        return $problems;
    }

    /**
     * @param  array<int, string>  $tagsToCheck
     * @return array<int, array{type: string, tag: string, line: int, offset: int}>
     */
    private function extractTagTokens(string $content, array $tagsToCheck): array
    {
        if ($tagsToCheck === []) {
            return [];
        }

        $tagsPattern = implode('|', array_map(
            fn (string $tag): string => preg_quote($tag, '/'),
            $tagsToCheck,
        ));

        preg_match_all(
            '/<\s*(\/?)\s*('.$tagsPattern.')(?=[\s>\/])(?:[^"\'<>]|"[^"]*"|\'[^\']*\')*(\/?)\s*>/iu',
            $content,
            $matches,
            PREG_OFFSET_CAPTURE,
        );

        $tokens = [];

        foreach ($matches[0] as $index => $match) {
            $fullTag = $match[0];
            $offset = $match[1];
            $isClosing = $matches[1][$index][0] === '/';
            $tag = strtolower($matches[2][$index][0]);
            $isSelfClosing = str_ends_with(trim($fullTag), '/>');

            if ($isSelfClosing) {
                continue;
            }

            $tokens[] = [
                'type' => $isClosing ? 'close' : 'open',
                'tag' => $tag,
                'line' => substr_count(substr($content, 0, $offset), "\n") + 1,
                'offset' => $offset,
            ];
        }

        usort($tokens, fn (array $a, array $b): int => $a['offset'] <=> $b['offset']);

        return $tokens;
    }

    private function stripComments(string $content): string
    {
        $content = preg_replace('/{{--.*?--}}/s', '', $content) ?? $content;

        return preg_replace('/<!--.*?-->/s', '', $content) ?? $content;
    }

    /**
     * @return array{
     *     tags: array{normal: array<int, string>, void: array<int, string>},
     *     meta: array<string, int|string|bool|null>
     * }
     */
    private function nativeTagReference(): array
    {
        $path = storage_path('audits/html/native-html-tags.json');

        if (! File::exists($path)) {
            return $this->fallbackNativeTagReference(
                reason: 'Native HTML reference file is missing: storage/audits/html/native-html-tags.json.',
            );
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            return $this->fallbackNativeTagReference(
                reason: 'Native HTML reference file is not valid JSON: storage/audits/html/native-html-tags.json.',
            );
        }

        $normalTags = $payload['tags']['normal'] ?? null;
        $voidTags = $payload['tags']['void'] ?? [];

        if (! is_array($normalTags)) {
            return $this->fallbackNativeTagReference(
                reason: 'Native HTML reference does not contain tags.normal as an array.',
            );
        }

        $normalTags = $this->normalizeTagList($normalTags);

        if ($normalTags === []) {
            return $this->fallbackNativeTagReference(
                reason: 'Native HTML reference contains no normal tags.',
            );
        }

        $voidTags = is_array($voidTags) ? $this->normalizeTagList($voidTags) : [];

        return [
            'tags' => [
                'normal' => $normalTags,
                'void' => $voidTags,
            ],
            'meta' => [
                'source' => 'storage/audits/html/native-html-tags.json',
                'source_name' => $payload['source']['name'] ?? null,
                'source_url' => $payload['source']['url'] ?? null,
                'generated_at' => $payload['generated_at'] ?? null,
                'normal_count' => count($normalTags),
                'void_count' => count($voidTags),
                'fallback' => false,
                'fallback_reason' => null,
                'fallback_hint' => null,
            ],
        ];
    }

    /**
     * @return array{
     *     tags: array{normal: array<int, string>, void: array<int, string>},
     *     meta: array<string, int|string|bool|null>
     * }
     */
    private function fallbackNativeTagReference(string $reason): array
    {
        $fallbackTags = $this->fallbackNativeTags();
        $hint = 'Run php artisan html:sync-native-tags to refresh the WHATWG native HTML reference.';

        $this->warn('Native HTML reference fallback is active.');
        $this->warn('Reason: '.$reason);
        $this->warn('Hint: '.$hint);

        return [
            'tags' => [
                'normal' => $fallbackTags,
                'void' => [],
            ],
            'meta' => [
                'source' => 'built-in fallback',
                'source_name' => null,
                'source_url' => null,
                'generated_at' => null,
                'normal_count' => count($fallbackTags),
                'void_count' => 0,
                'fallback' => true,
                'fallback_reason' => $reason,
                'fallback_hint' => $hint,
            ],
        ];
    }

    /**
     * @return array{
     *     tags: array<int, string>,
     *     meta: array<string, int|string|bool|null>
     * }
     */
    private function componentTagReference(): array
    {
        $path = storage_path('audits/html/view-component-tags.json');

        if (! File::exists($path)) {
            return $this->fallbackComponentTagReference(
                reason: 'Component reference file is missing: storage/audits/html/view-component-tags.json.',
            );
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload)) {
            return $this->fallbackComponentTagReference(
                reason: 'Component reference file is not valid JSON: storage/audits/html/view-component-tags.json.',
            );
        }

        $componentTags = $payload['all'] ?? null;

        if (! is_array($componentTags)) {
            return $this->fallbackComponentTagReference(
                reason: 'Component reference does not contain all as an array.',
            );
        }

        $componentTags = $this->normalizeComponentTagList($componentTags);

        if ($componentTags === []) {
            return $this->fallbackComponentTagReference(
                reason: 'Component reference contains no component tags.',
            );
        }

        return [
            'tags' => $componentTags,
            'meta' => [
                'source' => 'storage/audits/html/view-component-tags.json',
                'generated_at' => $payload['generated_at'] ?? null,
                'files_scanned' => $payload['files_scanned'] ?? null,
                'tag_count' => count($componentTags),
                'flux_count' => $payload['counts']['flux'] ?? null,
                'custom_count' => $payload['counts']['custom'] ?? null,
                'livewire_count' => $payload['counts']['livewire'] ?? null,
                'fallback' => false,
                'fallback_reason' => null,
                'fallback_hint' => null,
            ],
        ];
    }

    /**
     * @return array{
     *     tags: array<int, string>,
     *     meta: array<string, int|string|bool|null>
     * }
     */
    private function fallbackComponentTagReference(string $reason): array
    {
        $fallbackTags = $this->fallbackComponentTags();
        $hint = 'Run php artisan views:sync-component-tags to refresh the Blade component tag reference.';

        $this->warn('Component reference fallback is active.');
        $this->warn('Reason: '.$reason);
        $this->warn('Hint: '.$hint);

        return [
            'tags' => $fallbackTags,
            'meta' => [
                'source' => 'built-in fallback',
                'generated_at' => null,
                'files_scanned' => null,
                'tag_count' => count($fallbackTags),
                'flux_count' => null,
                'custom_count' => null,
                'livewire_count' => null,
                'fallback' => true,
                'fallback_reason' => $reason,
                'fallback_hint' => $hint,
            ],
        ];
    }

    /**
     * @param  array<int, mixed>  $tags
     * @return array<int, string>
     */
    private function normalizeTagList(array $tags): array
    {
        return collect($tags)
            ->filter(fn (mixed $tag): bool => is_string($tag) && $tag !== '')
            ->map(fn (string $tag): string => strtolower(trim($tag)))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, mixed>  $tags
     * @return array<int, string>
     */
    private function normalizeComponentTagList(array $tags): array
    {
        return collect($tags)
            ->filter(fn (mixed $tag): bool => is_string($tag) && $tag !== '')
            ->map(fn (string $tag): string => strtolower(trim($tag)))
            ->reject(fn (string $tag): bool => $tag === 'x-slot' || Str::startsWith($tag, 'x-slot:'))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function fallbackNativeTags(): array
    {
        return [
            'article',
            'aside',
            'button',
            'div',
            'footer',
            'form',
            'header',
            'label',
            'li',
            'main',
            'nav',
            'ol',
            'p',
            'section',
            'span',
            'table',
            'tbody',
            'td',
            'tfoot',
            'th',
            'thead',
            'tr',
            'ul',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function fallbackComponentTags(): array
    {
        return [
            'flux:badge',
            'flux:card',
            'flux:callout',
            'flux:callout.heading',
            'flux:callout.text',
            'flux:modal',
            'flux:table',
            'flux:table.columns',
            'flux:table.column',
            'flux:table.rows',
            'flux:table.row',
            'flux:table.cell',
            'flux:button.group',
            'flux:input.group',
            'flux:input.group.prefix',
            'flux:input.group.suffix',
            'x-ui.headers.card',
            'x-ui.headers.page',
            'x-ui.tooltip.trigger',
        ];
    }

    /**
     * @param  array<int, array<string, int|string|null>>  $problems
     */
    private function printSection(string $title, array $problems): void
    {
        $this->line($title);
        $this->line(str_repeat('-', strlen($title)));

        if ($problems === []) {
            $this->info('OK');
            $this->newLine();

            return;
        }

        foreach (array_slice($problems, 0, 20) as $problem) {
            if ($problem['type'] === 'unclosed') {
                $this->warn(sprintf(
                    '[unclosed] %s:%s',
                    $problem['file'],
                    $problem['opened_line'],
                ));
                $this->line(sprintf(
                    'Opened <%s>, expected %s',
                    $problem['tag'],
                    $problem['expected_closing'],
                ));

                continue;
            }

            if ($problem['type'] === 'mismatched') {
                $this->warn(sprintf(
                    '[mismatched] %s:%s',
                    $problem['file'],
                    $problem['closing_line'],
                ));
                $this->line(sprintf(
                    'Closing %s does not match opened <%s> at line %s. Expected %s',
                    $problem['actual_closing'],
                    $problem['tag'],
                    $problem['opened_line'],
                    $problem['expected_closing'],
                ));

                continue;
            }

            $this->warn(sprintf(
                '[unexpected closing] %s:%s',
                $problem['file'],
                $problem['closing_line'],
            ));
            $this->line(sprintf(
                'Found %s without matching opening tag.',
                $problem['actual_closing'],
            ));
        }

        if (count($problems) > 20) {
            $this->line('... '.(count($problems) - 20).' more problem(s), see audit JSON.');
        }

        $this->newLine();
    }

    private function relativePath(string $path): string
    {
        return str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
    }

    private function logRunActivity(string $event, string $description, array $properties = []): void
    {
        try {
            $activity = activity('html')
                ->event($event);

            $activity
                ->withProperties(ConsoleActivityContext::merge($this, $properties))
                ->log($description);
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: '.$exception->getMessage());
        }
    }
}
