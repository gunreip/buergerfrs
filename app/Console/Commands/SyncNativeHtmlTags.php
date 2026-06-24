<?php

// app/Console/Commands/SyncNativeHtmlTags.php

// php artisan html:sync-native-tags
// php artisan html:sync-native-tags --raw

namespace App\Console\Commands;

use App\Support\ActivityLog\ConsoleActivityContext;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Throwable;

#[Signature('html:sync-native-tags
    {--raw : Write the fetched WHATWG response body to storage/audits/html/native-html-tags-raw.html}
    {--raw-limit=20000 : Number of response body characters written to the raw preview file}
')]
#[Description('Sync the WHATWG HTML Living Standard native element reference into storage.')]
/**
 * Fetches and synchronizes WHATWG native HTML tag references to local storage.
 */
class SyncNativeHtmlTags extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sourceUrl = 'https://html.spec.whatwg.org/multipage/indices.html';

        $this->line('Sync native HTML tags');
        $this->line('Source: WHATWG HTML Living Standard');
        $this->line('URL: '.$sourceUrl);
        $this->newLine();

        $response = Http::timeout(20)
            ->retry(2, 500)
            ->get($sourceUrl);

        if (! $response->successful()) {
            $this->error('Unable to fetch WHATWG HTML index.');
            $this->line('HTTP status: '.$response->status());

            $this->logRunActivity('html.native_tags_sync.failed', 'Native HTML tags sync failed while fetching source.', [
                'source_url' => $sourceUrl,
                'http_status' => $response->status(),
            ]);

            return self::FAILURE;
        }

        if ((bool) $this->option('raw')) {
            $this->writeRawResponseDebug(
                body: $response->body(),
                status: $response->status(),
                contentType: (string) $response->header('Content-Type'),
                sourceUrl: $sourceUrl,
            );
        }

        $elements = $this->extractElements($response->body());

        if ($elements === []) {
            $this->error('No native HTML tags could be extracted from the WHATWG HTML index.');
            $this->line('Run with --raw to inspect the fetched source: php artisan html:sync-native-tags --raw');

            $this->logRunActivity('html.native_tags_sync.failed', 'Native HTML tags sync failed because no elements were extracted.', [
                'source_url' => $sourceUrl,
            ]);

            return self::FAILURE;
        }

        $voidTags = array_values(array_filter(
            array_keys($elements),
            fn (string $tag): bool => ($elements[$tag]['kind'] ?? null) === 'void',
        ));

        $normalTags = array_values(array_filter(
            array_keys($elements),
            fn (string $tag): bool => ($elements[$tag]['kind'] ?? null) === 'normal',
        ));

        sort($normalTags);
        sort($voidTags);

        $categories = $this->categoriesFromElements($elements);

        $payload = [
            'generated_at' => now()->toIso8601String(),
            'source' => [
                'name' => 'WHATWG HTML Living Standard',
                'url' => $sourceUrl,
                'section' => 'Index / Elements',
            ],
            'tags' => [
                'normal' => $normalTags,
                'void' => $voidTags,
            ],
            'elements' => $elements,
            'categories' => $categories,
            'counts' => [
                'normal' => count($normalTags),
                'void' => count($voidTags),
                'total' => count($normalTags) + count($voidTags),
                'categories' => count($categories),
            ],
        ];

        $previewPayload = [
            ...$payload,
            'tags' => [
                'normal' => array_slice($normalTags, 0, 30),
                'void' => $voidTags,
            ],
            'elements' => collect($elements)
                ->take(30)
                ->all(),
            'categories' => collect($categories)
                ->map(fn (array $tags): array => array_slice($tags, 0, 30))
                ->all(),
        ];

        File::ensureDirectoryExists(storage_path('audits/html'));

        File::put(
            storage_path('audits/html/native-html-tags.json'),
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL,
        );

        File::put(
            storage_path('audits/html/native-html-tags-preview.json'),
            json_encode($previewPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL,
        );

        $this->info('Native HTML tag reference synced.');
        $this->line('Normal tags: '.count($normalTags));
        $this->line('Void tags: '.count($voidTags));
        $this->line('Total tags: '.(count($normalTags) + count($voidTags)));
        $this->line('Categories: '.count($categories));
        $this->newLine();
        $this->line('Reference written: storage/audits/html/native-html-tags.json');
        $this->line('Preview written: storage/audits/html/native-html-tags-preview.json');

        $this->logRunActivity('html.native_tags_sync.completed', 'Native HTML tags sync completed.', [
            'source_url' => $sourceUrl,
            'counts' => $payload['counts'],
            'raw_option' => (bool) $this->option('raw'),
            'raw_limit' => (int) $this->option('raw-limit'),
        ]);

        return self::SUCCESS;
    }

    private function writeRawResponseDebug(string $body, int $status, string $contentType, string $sourceUrl): void
    {
        $limit = max(1000, (int) $this->option('raw-limit'));
        $preview = mb_substr($body, 0, $limit);

        File::ensureDirectoryExists(storage_path('audits/html'));

        File::put(
            storage_path('audits/html/native-html-tags-raw.html'),
            $body,
        );

        File::put(
            storage_path('audits/html/native-html-tags-raw-preview.txt'),
            implode(PHP_EOL, [
                'Source URL: '.$sourceUrl,
                'HTTP status: '.$status,
                'Content-Type: '.($contentType !== '' ? $contentType : 'n/a'),
                'Body bytes: '.strlen($body),
                'Preview chars: '.mb_strlen($preview),
                '',
                $preview,
            ]).PHP_EOL,
        );

        $this->warn('RAW WHATWG response written for inspection.');
        $this->line('RAW: storage/audits/html/native-html-tags-raw.html');
        $this->line('Preview: storage/audits/html/native-html-tags-raw-preview.txt');
        $this->newLine();
    }

    /**
     * @return array<string, array{kind: string, categories: array<int, string>}>
     */
    private function extractElements(string $html): array
    {
        $document = new DOMDocument;

        libxml_use_internal_errors(true);
        $loaded = $document->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        if (! $loaded) {
            return [];
        }

        $xpath = new DOMXPath($document);
        $tables = $xpath->query('//table[caption[normalize-space() = "List of elements"]]');

        if ($tables === false || $tables->length < 1) {
            return [];
        }

        $table = $tables->item(0);

        if (! $table instanceof DOMElement) {
            return [];
        }

        $rows = $xpath->query('.//tr', $table);

        if ($rows === false) {
            return [];
        }

        $elements = [];

        foreach ($rows as $row) {
            if (! $row instanceof DOMElement) {
                continue;
            }

            $cells = $xpath->query('./th|./td', $row);

            if ($cells === false || $cells->length < 3) {
                continue;
            }

            $elementCell = $cells->item(0);
            $categoriesCell = $cells->item(2);

            if (! $elementCell instanceof DOMElement || ! $categoriesCell instanceof DOMElement) {
                continue;
            }

            $tags = $this->extractTagsFromElementDomCell($xpath, $elementCell);

            if ($tags === []) {
                continue;
            }

            $categories = $this->extractCategoriesFromText($categoriesCell->textContent);

            foreach ($tags as $tag) {
                $elements[$tag] = [
                    'kind' => in_array($tag, $this->voidTags(), true) ? 'void' : 'normal',
                    'categories' => $categories,
                ];
            }
        }

        ksort($elements, SORT_NATURAL);

        return $elements;
    }

    /**
     * @return array<int, string>
     */
    private function extractTagsFromElementDomCell(DOMXPath $xpath, DOMElement $cell): array
    {
        $codeNodes = $xpath->query('.//code', $cell);

        if ($codeNodes === false) {
            return [];
        }

        $tags = [];

        foreach ($codeNodes as $codeNode) {
            $label = trim($codeNode->textContent);

            foreach ($this->extractTagNamesFromLabel($label) as $tag) {
                $tags[] = $tag;
            }
        }

        return collect($tags)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function extractCategoriesFromText(string $text): array
    {
        $text = preg_replace('/\s+/u', ' ', trim($text)) ?? '';

        if ($text === '') {
            return [];
        }

        $text = strtolower($text);

        $categories = preg_split('/\s*;\s*|\s*,\s*/u', $text) ?: [];

        return collect($categories)
            ->map(fn (string $category): string => trim($category))
            ->filter(fn (string $category): bool => $category !== '')
            ->map(fn (string $category): string => preg_replace('/\s+/u', ' ', $category) ?? $category)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @param  array<string, array{kind: string, categories: array<int, string>}>  $elements
     * @return array<string, array<int, string>>
     */
    private function categoriesFromElements(array $elements): array
    {
        $categories = [];

        foreach ($elements as $tag => $element) {
            foreach ($element['categories'] as $category) {
                $categories[$category][] = $tag;
            }
        }

        foreach ($categories as $category => $tags) {
            $tags = array_values(array_unique($tags));
            sort($tags, SORT_NATURAL);
            $categories[$category] = $tags;
        }

        ksort($categories, SORT_NATURAL);

        return $categories;
    }

    /**
     * @return array<int, string>
     */
    private function extractTagNamesFromLabel(string $label): array
    {
        $tags = [];

        $label = html_entity_decode($label, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $label = strip_tags($label);

        foreach (preg_split('/\s*,\s*/u', strtolower(trim($label))) ?: [] as $tag) {
            $tag = trim($tag);
            $tag = trim($tag, '<>');

            if ($this->isNativeHtmlTagName($tag)) {
                $tags[] = $tag;
            }
        }

        return $tags;
    }

    private function isNativeHtmlTagName(string $tag): bool
    {
        return $tag !== ''
            && preg_match('/^[a-z][a-z0-9]*$/', $tag) === 1
            && ! in_array($tag, [
                'math',
                'svg',
            ], true);
    }

    private function logRunActivity(string $event, string $description, array $properties = []): void
    {
        try {
            activity('html')
                ->event($event)
                ->withProperties(ConsoleActivityContext::merge($this, $properties))
                ->log($description);
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: '.$exception->getMessage());
        }
    }

    /**
     * @return array<int, string>
     */
    private function voidTags(): array
    {
        return [
            'area',
            'base',
            'br',
            'col',
            'embed',
            'hr',
            'img',
            'input',
            'link',
            'meta',
            'source',
            'track',
            'wbr',
        ];
    }
}
