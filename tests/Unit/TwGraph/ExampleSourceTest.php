<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

uses(TestCase::class);

it('extracts raw Blade and preserves relative indentation without evaluating or escaping it', function () {
    $source = new ExampleSource(<<<'BLADE'
        Outside the example
            {{-- example.1:start --}}
            <x-example
                :label="['text' => ['$value'], 'width' => 'half']"
            />
            {{-- example.1:end --}}
        Outside the example
    BLADE);

    expect($source->example('example.1'))->toBe(<<<'BLADE'
    <x-example
        :label="['text' => ['$value'], 'width' => 'half']"
    />
    BLADE);
});

it('handles empty examples and normalizes line endings', function () {
    $source = new ExampleSource("  {{-- empty:start --}}\r\n  {{-- empty:end --}}\r\n  {{-- body:start --}}\r\n  first\r\n\r\n    second\r\n  {{-- body:end --}}");
    expect($source->example('empty'))->toBe('')
        ->and($source->example('body'))->toBe("first\n\n  second");
});

it('reports missing duplicate and reversed markers with their view context', function (string $source) {
    expect(fn () => (new ExampleSource($source, 'documentation.fixture'))->example('test'))
        ->toThrow(LogicException::class, 'Example [test] in [documentation.fixture]');
})->with([
    'missing end' => "{{-- test:start --}}\ntext",
    'missing start' => "text\n{{-- test:end --}}",
    'duplicate start' => "{{-- test:start --}}\n{{-- test:start --}}\ntext\n{{-- test:end --}}",
    'duplicate end' => "{{-- test:start --}}\ntext\n{{-- test:end --}}\n{{-- test:end --}}",
    'reversed' => "{{-- test:end --}}\ntext\n{{-- test:start --}}",
]);

it('reads current source through the view finder on every new load', function () {
    $directory = sys_get_temp_dir().'/example-source-'.bin2hex(random_bytes(8));
    mkdir($directory);
    $file = $directory.'/sample.blade.php';
    View::addNamespace('example-source-test', $directory);
    try {
        file_put_contents($file, "{{-- demo:start --}}\nfirst\n{{-- demo:end --}}");
        expect(ExampleSource::fromView('example-source-test::sample')->example('demo'))->toBe('first');
        file_put_contents($file, "{{-- demo:start --}}\nchanged\n{{-- demo:end --}}");
        expect(ExampleSource::fromView('example-source-test::sample')->example('demo'))->toBe('changed');
    } finally {
        unlink($file);
        rmdir($directory);
    }
});

it('derives changed props including arrays and removals from current authored source', function () {
    $fixture = <<<'BLADE'
        {{-- baseline:start --}}
        <x-graph graph-id="first" node-size="1rem">
            <x-line id="one" direction="top" :text="['a' => 'b']" old-prop="old" />
        </x-graph>
        {{-- baseline:end --}}
        {{-- variant:start --}}
        <x-graph graph-id="second" node-size="2rem">
            {{-- <x-line ignored="comment" /> --}}
            <x-line id="two" direction="top" :text="['a' => 'x > y']" length="8rem" enabled />
        </x-graph>
        {{-- variant:end --}}
        BLADE;
    $source = new ExampleSource($fixture);
    expect($source->changedProps('variant', 'baseline', 'x-line'))->toBe(<<<'PROPS'
        :text="['a' => 'x > y']"
        length="8rem"
        enabled
        {{-- old-prop is omitted in this example; the component fallback applies. --}}
        PROPS);
    expect($source->changedProps('variant', 'baseline', 'x-graph'))->toBe('node-size="2rem"');
    expect($source->changedProps('baseline', 'baseline', 'x-line'))->toBe('');
    expect((new ExampleSource(str_replace('length="8rem"', 'length="11rem"', $fixture)))
        ->changedProps('variant', 'baseline', 'x-line'))->toContain('length="11rem"')->not->toContain('length="8rem"');
});

it('rejects missing ambiguous or unsupported component selections rather than showing misleading props', function (string $body) {
    $source = new ExampleSource("{{-- demo:start --}}\n".$body."\n{{-- demo:end --}}", 'test.view');
    expect(fn () => $source->changedProps('demo', 'demo', 'x-line'))->toThrow(LogicException::class);
})->with([
    '<x-other />',
    '<x-line /><x-line />',
    '<x-line length="1rem" length="2rem" />',
    '<x-line {{ $attributes }} />',
]);

it('requires preview code boxes across Idea to Paper to use the shared source helper', function () {
    $root = dirname(View::getFinder()->find('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.index'));
    $count = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root)) as $file) {
        if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
            continue;
        }
        $relative = substr($file->getPathname(), strlen($root) + 1);
        // Deep Reference snippets are independent syntax illustrations, without previews.
        // Inventory displays source read from the actual selected component file.
        if (str_starts_with($relative, 'props-and-connections/') || $relative === 'inventory/index.blade.php') {
            continue;
        }
        $source = file_get_contents($file->getPathname());
        preg_match_all('~<x-translation-workbench::ui.tw-graph.code-box\b[^>]*>(.*?)</x-translation-workbench::ui.tw-graph.code-box>~s', $source, $boxes);
        if ($boxes[1] === []) {
            continue;
        }
        expect($source)->toContain('ExampleSource::fromView(');
        preg_match_all('/\$(\w+)\s*=\s*\$\w+->example\(/', $source, $derivedVariables);
        foreach ($boxes[1] as $body) {
            expect(trim($body))->toStartWith('{{')->toEndWith('}}');
            $directSource = preg_match('/\$\w+->(?:example|changedProps)\(/', $body) === 1;
            $derivedSource = preg_match('/^\s*\{\{\s*\$(\w+)\s*\}\}\s*$/', $body, $variable)
                && in_array($variable[1], $derivedVariables[1], true);
            expect($directSource || $derivedSource)->toBeTrue($relative.' must derive its code from the authored preview.');
            $count++;
        }
    }
    expect($count)->toBeGreaterThan(200);
});

it('renders extracted examples and every computed prop change in their code boxes', function (string $name, string $section = 'primitives') {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.'.$section.'.'.(str_starts_with($section, 'flow') ? 'flow' : basename(str_replace('.', '/', $section))).'-'.$name;
    $source = file_get_contents(View::getFinder()->find($view));
    $examples = ExampleSource::fromView($view);
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">'.view($view)->render());
    $xpath = new DOMXPath($dom);
    $code = implode("\n\n", array_map(fn ($node) => $node->textContent, iterator_to_array($xpath->query('//pre/code'))));
    preg_match_all("/->example\('([^']+)'\)/", $source, $complete);
    foreach ($complete[1] as $marker) {
        expect($code)->toContain($examples->example($marker));
    }
    preg_match_all("/->changedProps\('([^']+)', '([^']+)', '([^']+)'\)/", $source, $changes, PREG_SET_ORDER);
    foreach ($changes as $change) {
        $props = $examples->changedProps($change[1], $change[2], $change[3]);
        if ($props !== '') {
            expect($code)->toContain($props);
        }
    }
})->with(['arc', 'line', 'text-label', 'line-jump', 'node', 'joint-arrow', 'connector', 'Part Start' => ['start', 'parts'], 'Part End' => ['end', 'parts'], 'Part Sideways' => ['sideways', 'parts'], 'Part Chain' => ['chain', 'parts'], 'Part Fusion' => ['fusion', 'parts'], 'Flow branch-steps' => ['branch-steps', 'flow'], 'Flow start' => ['start', 'flow'], 'Flow step' => ['step', 'flow'], 'Flow decision' => ['decision', 'flow.if'], 'Flow if-else' => ['if-else', 'flow.if'], 'Flow if-elseif-multi' => ['if-elseif-multi', 'flow.if'], 'Flow if-elseif' => ['if-elseif', 'flow.if'], 'Flow if-nested-1' => ['if-nested-1', 'flow.if'], 'Flow if-nested-2' => ['if-nested-2', 'flow.if'], 'Flow if-nested-3' => ['if-nested-3', 'flow.if'], 'Flow if-nested-4' => ['if-nested-4', 'flow.if'], 'Flow if-nested-5' => ['if-nested-5', 'flow.if'], 'Flow if-nested-6' => ['if-nested-6', 'flow.if'], 'Flow if-nested-7' => ['if-nested-7', 'flow.if'], 'Flow if-nested-8' => ['if-nested-8', 'flow.if'], 'Flow if-nested-9' => ['if-nested-9', 'flow.if'], 'Flow if-nested-test' => ['if-nested-test', 'flow.if'], 'Flow if-simple' => ['if-simple', 'flow.if'], 'Flow if-ternary' => ['if-ternary', 'flow.if'], 'Flow switch-case-action-sequence' => ['switch-case-action-sequence', 'flow.switch-case'], 'Flow switch-case-default' => ['switch-case-default', 'flow.switch-case'], 'Flow switch-case-fallthrough' => ['switch-case-fallthrough', 'flow.switch-case'], 'Flow switch-case-grouped-3' => ['switch-case-grouped-3', 'flow.switch-case'], 'Flow switch-case-grouped-multi' => ['switch-case-grouped-multi', 'flow.switch-case'], 'Flow switch-case-grouped' => ['switch-case-grouped', 'flow.switch-case'], 'Flow switch-case-nested-1' => ['switch-case-nested-1', 'flow.switch-case'], 'Flow switch-case-nested-2' => ['switch-case-nested-2', 'flow.switch-case'], 'Flow switch-case-nested' => ['switch-case-nested', 'flow.switch-case'], 'Flow switch-case-test' => ['switch-case-test', 'flow.switch-case'], 'Flow switch-case-two-nested-1' => ['switch-case-two-nested-1', 'flow.switch-case'], 'Flow switch-case-two-nested-2' => ['switch-case-two-nested-2', 'flow.switch-case'], 'Flow switch-case-two-nested-3' => ['switch-case-two-nested-3', 'flow.switch-case'], 'Flow switch-case-without-default' => ['switch-case-without-default', 'flow.switch-case'], 'Flow while-action-sequence' => ['while-action-sequence', 'flow.while'], 'Flow while-basic' => ['while-basic', 'flow.while'], 'Flow while-if' => ['while-if', 'flow.while'], 'Flow while-independent' => ['while-independent', 'flow.while'], 'Flow while-mixed' => ['while-mixed', 'flow.while'], 'Flow while-multiple-actions' => ['while-multiple-actions', 'flow.while'], 'Flow while-nested' => ['while-nested', 'flow.while'], 'Flow while-test' => ['while-test', 'flow.while'], 'Strang Rekey default' => ['default', 'strang.rekey'], 'Strang Rekey source' => ['source', 'strang.rekey'], 'Strang Rekey target' => ['target', 'strang.rekey'], 'Strang Rekey compressed' => ['compressed', 'strang.rekey'], 'Strang Branch default' => ['default', 'strang.branch'], 'Strang Branch offset' => ['offset', 'strang.branch'], 'Strang Branch step' => ['step', 'strang.branch'], 'Strang Branch return' => ['return', 'strang.branch'], 'Strang Branch continuation' => ['continuation', 'strang.branch'], 'Strang Branch mismatch' => ['mismatch', 'strang.branch'], 'Strang Merge default' => ['default', 'strang.merge'], 'Strang Merge start' => ['start', 'strang.merge'], 'Strang Merge extension' => ['extension', 'strang.merge'], 'Strang Merge aggregated' => ['aggregated', 'strang.merge'], 'Strang Merge mismatch' => ['mismatch', 'strang.merge'], 'Strang Trunk default' => ['default', 'strang.trunk'], 'Strang Trunk direction' => ['direction', 'strang.trunk'], 'Strang Trunk end-cap' => ['end-cap', 'strang.trunk'], 'Strang Trunk end-color' => ['end-color', 'strang.trunk'], 'Strang Trunk end-default' => ['end-default', 'strang.trunk'], 'Strang Trunk end-long-end' => ['end-long-end', 'strang.trunk'], 'Strang Trunk end-overview' => ['end-overview', 'strang.trunk'], 'Strang Trunk end-wide-label' => ['end-wide-label', 'strang.trunk'], 'Strang Trunk start-colors' => ['start-colors', 'strang.trunk'], 'Strang Trunk start-compare' => ['start-compare', 'strang.trunk'], 'Strang Trunk start-default' => ['start-default', 'strang.trunk'], 'Strang Trunk start-long-start' => ['start-long-start', 'strang.trunk'], 'Strang Trunk start-overview' => ['start-overview', 'strang.trunk'], 'Strang Trunk start-shift' => ['start-shift', 'strang.trunk'], 'Strang Trunk start-spacing' => ['start-spacing', 'strang.trunk'], 'Strang Trunk start-wide-labels' => ['start-wide-labels', 'strang.trunk'], 'Strang Trunk stem-count' => ['stem-count', 'strang.trunk'], 'Strang Trunk stem-lengths' => ['stem-lengths', 'strang.trunk'], 'Path stem-detour' => ['stem-detour', 'paths'], 'Path branch-return-bridge' => ['branch-return-bridge', 'paths'], 'Path branch-return-extension' => ['branch-return-extension', 'paths'], 'Path branch-return' => ['branch-return', 'paths'], 'Path branch-extension' => ['branch-extension', 'paths'], 'Path branch' => ['branch', 'paths'], 'Path merge-extension' => ['merge-extension', 'paths'], 'Path merge' => ['merge', 'paths'], 'Path trunk' => ['trunk', 'paths']]);
