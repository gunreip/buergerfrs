<?php

// app/Console/Commands/SyncNativeHtmlTags.php

// php artisan html:sync-native-tags
// php artisan html:sync-native-tags --raw

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

#[Signature('html:sync-native-tags
    {--raw : Write the fetched WHATWG response body to storage/audits/html/native-html-tags-raw.html}
    {--raw-limit=20000 : Number of response body characters written to the raw preview file}
')]
#[Description('Sync the WHATWG HTML Living Standard native element reference into storage.')]

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
        $this->line('URL: ' . $sourceUrl);
        $this->newLine();

        $response = Http::timeout(20)
            ->retry(2, 500)
            ->get($sourceUrl);

        if (! $response->successful()) {
            $this->error('Unable to fetch WHATWG HTML index.');
            $this->line('HTTP status: ' . $response->status());

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

        $allTags = $this->extractElementTags($response->body());

        if ($allTags === []) {
            $this->error('No native HTML tags could be extracted from the WHATWG HTML index.');
            $this->line('Run with --raw to inspect the fetched source: php artisan html:sync-native-tags --raw');

            return self::FAILURE;
        }

        $voidTags = array_values(array_intersect($allTags, $this->voidTags()));
        $normalTags = array_values(array_diff($allTags, $voidTags));

        sort($normalTags);
        sort($voidTags);

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
            'counts' => [
                'normal' => count($normalTags),
                'void' => count($voidTags),
                'total' => count($normalTags) + count($voidTags),
            ],
        ];

        $previewPayload = [
            ...$payload,
            'tags' => [
                'normal' => array_slice($normalTags, 0, 30),
                'void' => $voidTags,
            ],
        ];

        File::ensureDirectoryExists(storage_path('audits/html'));

        File::put(
            storage_path('audits/html/native-html-tags.json'),
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
        );

        File::put(
            storage_path('audits/html/native-html-tags-preview.json'),
            json_encode($previewPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL,
        );

        $this->info('Native HTML tag reference synced.');
        $this->line('Normal tags: ' . count($normalTags));
        $this->line('Void tags: ' . count($voidTags));
        $this->line('Total tags: ' . (count($normalTags) + count($voidTags)));
        $this->newLine();
        $this->line('Reference written: storage/audits/html/native-html-tags.json');
        $this->line('Preview written: storage/audits/html/native-html-tags-preview.json');

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
                'Source URL: ' . $sourceUrl,
                'HTTP status: ' . $status,
                'Content-Type: ' . ($contentType !== '' ? $contentType : 'n/a'),
                'Body bytes: ' . strlen($body),
                'Preview chars: ' . mb_strlen($preview),
                '',
                $preview,
            ]) . PHP_EOL,
        );

        $this->warn('RAW WHATWG response written for inspection.');
        $this->line('RAW: storage/audits/html/native-html-tags-raw.html');
        $this->line('Preview: storage/audits/html/native-html-tags-raw-preview.txt');
        $this->newLine();
    }

    /**
     * @return array<int, string>
     */
    private function extractElementTags(string $html): array
    {
        $section = $this->extractElementsTable($html);

        preg_match_all(
            '/<code\b[^>]*\bid=(?:"|\')?[^"\'\s>]*:the-[^"\'\s>]*-element[^"\'\s>]*(?:"|\')?[^>]*>\s*<a\b[^>]*>(?P<label>.*?)<\/a>\s*<\/code>/isu',
            $section,
            $matches,
            PREG_SET_ORDER,
        );

        $tags = [];

        foreach ($matches as $match) {
            $label = html_entity_decode(strip_tags($match['label']), ENT_QUOTES | ENT_HTML5, 'UTF-8');

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

    private function extractElementsTable(string $html): string
    {
        if (preg_match('/<caption>\s*List of elements\s*<\/caption>/isu', $html, $captionMatch, PREG_OFFSET_CAPTURE) !== 1) {
            return $html;
        }

        $captionOffset = $captionMatch[0][1];

        $tableStart = strripos(substr($html, 0, $captionOffset), '<table');

        if ($tableStart === false) {
            return $html;
        }

        $tableEnd = stripos($html, '</table>', $captionOffset);

        if ($tableEnd === false) {
            return substr($html, $tableStart);
        }

        return substr($html, $tableStart, ($tableEnd + strlen('</table>')) - $tableStart);
    }

    /**
     * @return array<int, string>
     */
    private function extractTagNamesFromLabel(string $label): array
    {
        $tags = [];

        foreach (preg_split('/\s*,\s*/', strtolower(trim($label))) ?: [] as $tag) {
            $tag = trim($tag);

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
