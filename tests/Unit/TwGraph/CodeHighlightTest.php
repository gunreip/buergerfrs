<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\CodeHighlight;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('highlights source comments and component lines without altering copied text', function (): void {
    $source = <<<'CODE'
        {{-- Fusion 2 Inputs / Left --}}
        <x-example
            :label="['text' => ['A & B'], 'align' => 'left']"
        />
        <!-- Multiple
             comment lines -->
        <flux:heading>Preview</flux:heading>
        <script>alert('example only');</script>
    CODE;
    $html = Blade::render('<x-translation-workbench::ui.tw-graph.code-box>{{ $source }}</x-translation-workbench::ui.tw-graph.code-box>', compact('source'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//code')->item(0)->textContent)->toBe(trim($source));
    expect($xpath->query('//code/span[@class="tw-graph-code-comment"]')->length)->toBe(1);
    expect($xpath->query('//code/span[@class="tw-graph-code-source-comment"]')->length)->toBe(1);
    expect($xpath->query('//code/span[@class="tw-graph-code-component"]')->length)->toBe(2);
    expect($xpath->query('//code/script')->length)->toBe(0);
});

it('preserves non-Blade source and literal entity text', function (): void {
    $code = "const example = '&gt;';\nif (x > 1) { return 'A & B'; }";
    expect(CodeHighlight::render(e($code)))->toBe(e($code));
});

it('highlights PHP comments while preserving strings and copyable source', function (bool $blade): void {
    $body = <<<'CODE'

        /** Layout settings. */
        // A normal comment.
        # Another comment.
        $url = 'https://example.test/path';
        $literal = "/* text, not a comment */ &lt;";
        $label = ['text' => ['A & B', '<script>example</script>']];

    CODE;
    $source = $blade ? '@php'.$body.'@endphp' : '<?php'.$body;
    $html = '<pre><code>'.CodeHighlight::render(e($source)).'</code></pre>';
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    expect($xpath->query('//code')->item(0)->textContent)->toBe($source);
    expect($xpath->query('//span[@class="tw-graph-code-source-comment"]')->length)->toBe(3);
    expect($xpath->query('//span[@class="tw-graph-code-php"]')->length)->toBe($blade ? 1 : 0);
    expect($xpath->query('//code/script')->length)->toBe(0);
})->with([true, false]);

it('keeps Blade comments distinct from PHP blocks and following component lines', function (): void {
    $source = "{{-- @php is explained here --}}\n@php\n    // Settings\n    \$value = 2;\n@endphp\n<flux:heading>Preview</flux:heading>";
    $html = '<code>'.CodeHighlight::render(e($source)).'</code>';
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    expect($xpath->query('//code')->item(0)->textContent)->toBe($source);
    foreach (['comment', 'php', 'source-comment', 'component'] as $kind) {
        expect($xpath->query('//span[@class="tw-graph-code-'.$kind.'"]')->length)->toBe(1);
    }
});
