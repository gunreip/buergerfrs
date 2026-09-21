<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry;
use Gunreip\TranslationWorkbench\Support\TwGraph\RootIdentifier;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('aligns switch case outputs and skips subsequent actions on the return rail', function (string $side, string $direction): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="switch-test">
            <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                id="status" :side="$side" :direction="$direction"
                :cases="[
                    ['key' => 'draft', 'entries' => [['key' => 'draft', 'label' => ['text' => ['CASE draft'], 'align' => 'left']], ['key' => 'review', 'label' => 'CASE review']], 'actionLabel' => ['text' => ['Edit'], 'width' => 'half']],
                    ['key' => 'published', 'label' => ['text' => ['CASE published'], 'align' => 'right'], 'actionLabel' => ['text' => ['Display'], 'width' => 'long']],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'direction'));
    $get = fn ($id) => AnchorRegistry::get('switch-test', 'status.' . $id);
    $evaluate = new ReflectionMethod(BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn ($value) => $evaluate->invoke(null, $value);
    foreach (['draft' => 'published', 'published' => 'default'] as $key => $next) {
        foreach (['x', 'y'] as $axis) {
            expect($number($get('case.' . $key . '.return.anchorNode-end')[$axis]))->toBe($number($get('case.' . $next . '.anchorNode-end')[$axis]));
        }
    }
    foreach (['x', 'y'] as $axis) {
        expect($number($get('anchorNode-end')[$axis]))->toBe($number($get('case.default.anchorNode-end')[$axis]));
    }
    expect(($side === 'left' ? -1 : 1) * ($number($get('anchorNode-end')['x']) - $number($get('anchorNode-start')['x'])))->toBeGreaterThan(0);
    expect(($direction === 'bottom-top' ? 1 : -1) * ($number($get('anchorNode-end')['y']) - $number($get('anchorNode-start')['y'])))->toBeGreaterThan(0);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//*[@data-tw-graph-path="status.expression.label"]')->length)->toBeGreaterThan(0);
    expect($dom->textContent)->toContain('CASE draft', 'CASE review', 'CASE published', 'DEFAULT', 'BREAK', 'END SWITCH');
    expect($xpath->query('//*[contains(@class, "tw-graph-protocol-primitive-text-line") and not(ancestor::*[@aria-hidden="true"]) and normalize-space(text())="Edit"]')->length)->toBe(1);
    expect($xpath->query('//*[contains(@class, "tw-graph-protocol-primitive-text-line") and not(ancestor::*[@aria-hidden="true"]) and normalize-space(text())="CASE review"]')->length)->toBe(1);
    $fusion = $get('case.draft.fusion.anchorNode-end');
    expect($number($fusion['y']))->toBe(($number($get('case.draft.entries.draft.anchorNode-end')['y']) + $number($get('case.draft.entries.review.anchorNode-end')['y'])) / 2);
    expect($number($get('case.draft.entries.draft.anchorNode-end')['y']))
        ->not->toBe($number($get('case.draft.entries.review.anchorNode-end')['y']));
    foreach (['CASE draft' => 'left', 'CASE published' => 'right'] as $text => $align) {
        expect($xpath->query('//*[contains(@class, "tw-graph-protocol-primitive-text-line") and normalize-space(.)="' . $text . '"]/ancestor::*[contains(@style, "text-align: ' . $align . '")]')->length)->toBe(1);
    }
    expect(RootIdentifier::tooltipSuffix())->toBe('');
})->with(['left', 'right'])->with(['bottom-top', 'top-bottom']);

it('renders mixed switch sides with explicit bridge jumps and independent return anchors', function (string $leaf, string $rootId): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.'.$leaf)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    foreach (['' => 1, '-right' => -1] as $suffix => $innerSign) {
        $graph = 'idea-to-paper-'.$leaf.$suffix;
        $root = 'literature.switch.1.'.$rootId.$suffix;
        $canvas = $xpath->query('//*[@id="'.$graph.'"]')->item(0);
        expect($canvas)->not->toBeNull();
        expect($canvas->textContent)->toContain('SWITCH ($status)', 'SWITCH ($format)', 'Continue process');
        expect($canvas->textContent)->not->toContain(':cases=', '=>', '{--');
        $get = fn ($key) => AnchorRegistry::get($graph, $root.'.'.$key);
        $number = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
        foreach (['x', 'y'] as $axis) {
            expect($number($get('inner.anchorNode-start')[$axis]))->toBe($number($get('outer.case.editable.anchorNode-end')[$axis]));
            expect($number($get('inner-return.stem.anchorNode-end')[$axis]))->toBe($number($get('outer.case.editable.anchorNode-return')[$axis]));
        }
        expect($get('outer.case.editable.return.anchorNode-end'))->toBeNull();
        expect($innerSign * ($number($get('inner.anchorNode-end')['x']) - $number($get('inner.anchorNode-start')['x'])))->toBeGreaterThan(0);
        expect($innerSign * ($number($get('inner.anchorNode-end')['x']) - $number($get('outer.anchorNode-start')['x'])))->toBeGreaterThan(0);
        expect($number($get('outer.case.editable.anchorNode-return')['y']))->toBeGreaterThanOrEqual($number($get('inner-return.anchorNode-end')['y']));
        $owners = $xpath->query('.//*[@data-tw-graph-line-jumps]', $canvas);
        expect($owners->length)->toBe(4);
        foreach ($owners as $owner) {
            $jump = json_decode($owner->getAttribute('data-tw-graph-line-jumps'), true)[0];
            expect($jump['over'])->toBe($root.'.outer.case.published.entry');
            expect($xpath->query('.//*[@data-tw-graph-path="'.$jump['over'].'"]', $canvas)->length)->toBeGreaterThan(0);
        }
        expect($get('outer.anchorNode-end')['returnColor'])->toBe('amber');
    }
})->with([
    ['flow-switch-case-two-nested-3', 'two-nested-3'],
]);


it('resolves each switch entry stem independently with explicit first-case precedence', function (?string $expressionStem, ?string $firstStem, string $expectedFirst, string $direction): void {
    $expression = ['text' => ['SWITCH status'], 'stemLength' => $expressionStem];
    $cases = [
        ['key' => 'first', 'stemLength' => $firstStem, 'actionLabel' => ['text' => ['First']]],
        ['key' => 'second', 'stemLength' => '6rem', 'actionLabel' => ['text' => ['Second']]],
        ['key' => 'third', 'actionLabel' => ['text' => ['Third']]],
    ];
    Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="switch-lengths">
            <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                id="lengths" stem-length="9rem" :direction="$direction"
                :case-expression="$expression" :cases="$cases"
                :case-default="['text' => ['Fallback'], 'stemLength' => '7rem']"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('expression', 'cases', 'direction'));
    $get = fn ($id) => AnchorRegistry::get('switch-lengths', 'lengths.' . $id);
    $evaluate = new ReflectionMethod(BoundsRegistry::class, 'evaluateRemExpression');
    $previous = $get('expression.anchorNode-end');
    foreach (['first' => $expectedFirst, 'second' => '6rem', 'third' => '9rem', 'default' => '7rem'] as $key => $length) {
        $end = $get('case.' . $key . '.entry.anchorNode-end');
        $distance = $evaluate->invoke(null, $end['y']) - $evaluate->invoke(null, $previous['y']);
        expect($distance)->toBe(($direction === 'bottom-top' ? 1 : -1) * $evaluate->invoke(null, $length));
        $previous = $end;
    }
})->with([
    'global fallback' => [null, null, '9rem'],
    'expression override' => ['3rem', null, '3rem'],
    'case overrides expression' => ['3rem', '5rem', '5rem'],
])->with(['bottom-top', 'top-bottom']);

it('keeps both saved switch orientations handmade with language examples below their props', function (): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-default';
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach')->not->toContain('@include');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-switch-case'))->toBe(2);
    expect(strpos($source, '</flux:table>'))->toBeLessThan(strpos($source, '<x-translation-workbench::ui.tw-graph.language-examples'));
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    foreach (['', '-right'] as $suffix) {
        $canvas = $xpath->query('//*[@id="idea-to-paper-flow-switch-case-default' . $suffix . '"]')->item(0);
        expect($canvas)->not->toBeNull();
        expect($canvas->textContent)->toContain('CASE draft', 'CASE published', 'DEFAULT', 'Continue process');
        expect($canvas->textContent)->not->toContain(':cases=', '{--');
        $output = AnchorRegistry::get('idea-to-paper-flow-switch-case-default' . $suffix, 'literature.switch.1.basic' . $suffix . '.status.anchorNode-end');
        expect($output)->not->toBeNull();
    }
});

it('customizes switch exit and default annotations independently of actions', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="switch-labels">
            <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                id="labels"
                :cases="[
                    ['key' => 'first', 'actionLabel' => ['text' => ['First action']],
                     'exitLabel' => ['text' => ['Leave case'], 'align' => 'right', 'width' => 'long', 'color' => 'rose', 'connectorLength' => '3rem']],
                    ['key' => 'second', 'actionLabel' => ['text' => ['Second action']], 'exitLabel' => false],
                ]"
                :case-default="[
                    'text' => ['Fallback action'],
                    'label' => ['text' => ['No matching case'], 'align' => 'left'],
                    'exitLabel' => ['text' => ['Continue here'], 'align' => 'right'],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($dom->textContent)->toContain('First action', 'Second action', 'Fallback action', 'Leave case', 'No matching case', 'Continue here');
    expect($dom->textContent)->not->toContain('DEFAULT', 'END SWITCH', 'BREAK');
    foreach (['Leave case' => 'right', 'No matching case' => 'left', 'Continue here' => 'right'] as $text => $align) {
        expect($xpath->query('//*[contains(@class, "tw-graph-protocol-primitive-text-line") and normalize-space(.)="' . $text . '"]/ancestor::*[contains(@style, "text-align: ' . $align . '")]')->length)->toBe(1);
    }
    expect($xpath->query('//*[normalize-space(.)="Leave case" and contains(@class, "w-96")]')->length)->toBe(1);
    expect(AnchorRegistry::get('switch-labels', 'labels.anchorNode-end'))->not->toBeNull();
});

it('connects case dots directly to fusion with uniform group spacing', function (string $direction, ?string $spacing): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="fusion-spacing">
            <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                id="spacing" :direction="$direction" arc-radius="3rem" stem-length="9rem"
                :cases="[['key' => 'group', 'entryStemLength' => $spacing, 'entries' => [
                    ['key' => 'first'], ['key' => 'second'], ['key' => 'third'],
                ], 'actionLabel' => ['text' => ['Shared action']]]]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('direction', 'spacing'));
    $get = fn ($key) => AnchorRegistry::get('fusion-spacing', 'spacing.'.$key);
    $number = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
    $previous = $get('expression.anchorNode-end');
    foreach (['first' => 9.0, 'second' => $spacing === null ? 6.0 : 4.0, 'third' => $spacing === null ? 6.0 : 4.0] as $key => $distance) {
        $entry = $get('case.group.entries.'.$key.'.entry.anchorNode-end');
        $lane = $get('case.group.entries.'.$key.'.anchorNode-end');
        expect($number($lane['y']))->toBe($number($entry['y']));
        expect(abs($number($entry['y']) - $number($previous['y'])))->toBe($distance);
        expect($html)->not->toContain('spacing.case.group.entries.'.$key.'.arc-in');
        $previous = $entry;
    }
})->with(['bottom-top', 'top-bottom'])->with([null, '4rem']);

it('renders independent left and right grouped case examples with matching output rails', function (string $variant): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-grouped'.$variant;
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', '@include');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-switch-case'))->toBe(2);
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe($variant === '-multi' ? 14 : 8);
    foreach (['', '-right'] as $suffix) {
        $graph = 'idea-to-paper-flow-switch-case-grouped'.$variant.$suffix;
        $prefix = 'literature.switch.1.grouped'.$variant.$suffix.'.status';
        $canvas = $xpath->query('//*[@id="'.$graph.'"]')->item(0);
        expect($canvas->textContent)->toContain('CASE draft', 'CASE review', 'Open editor', 'DEFAULT');
        if ($variant === '-multi') {
            expect($canvas->textContent)->toContain('CASE revision', 'CASE returned');
            if ($suffix === '-right') {
                expect($canvas->textContent)->toContain('CASE reopened');
                $center = AnchorRegistry::get($graph, $prefix.'.case.editable.fusion.anchorNode-end');
                $middle = AnchorRegistry::get($graph, $prefix.'.case.editable.entries.revision.anchorNode-end');
                expect(BoundsRegistry::evaluateRemExpression($center['y']))->toBe(BoundsRegistry::evaluateRemExpression($middle['y']));
            } else {
                expect($canvas->textContent)->not->toContain('CASE reopened');
            }
        }
        $get = fn ($key) => AnchorRegistry::get($graph, $prefix.'.'.$key);
        $number = fn ($v) => BoundsRegistry::evaluateRemExpression($v);
        foreach (['x', 'y'] as $axis) {
            expect($number($get('case.editable.return.anchorNode-end')[$axis]))->toBe($number($get('case.published.anchorNode-end')[$axis]));
        }
        $dx = $number($get('anchorNode-end')['x']) - $number($get('anchorNode-start')['x']);
        expect($suffix === '' ? -$dx : $dx)->toBeGreaterThan(0);
    }
})->with(['', '-3', '-multi']);

it('rejects explicit case entry spacing below three rem', function (): void {
    expect(fn () => Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph>
            <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                :cases="[['key' => 'group', 'entryStemLength' => '2.9rem', 'entries' => [['key' => 'a'], ['key' => 'b']]]]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE))->toThrow(\Illuminate\View\ViewException::class, 'entryStemLength must resolve to at least 3rem');
});

it('opens ordinary and grouped cases for an explicit nested return in either orientation', function (string $side, string $direction, bool $grouped): void {
    $entries = $grouped ? [['key' => 'a'], ['key' => 'b']] : [];
    Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="switch-open">
            <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                id="open" :side="$side" :direction="$direction"
                :cases="[
                    ['key' => 'nested', 'entries' => $entries, 'actionLabel' => ['text' => ['Prepare'], 'return' => false]],
                    ['key' => 'next', 'actionLabel' => ['text' => ['Other action']]],
                ]"
            />
            @php
                \Gunreip\TranslationWorkbench\Support\TwGraph\ReturnColorRegistry::connect('switch-open', 'open.case.nested.anchorNode-return', 'amber');
            @endphp
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'direction', 'entries'));
    $get = fn ($key) => AnchorRegistry::get('switch-open', 'open.'.$key);
    expect($get('case.nested.return.anchorNode-end'))->toBeNull();
    foreach (['x', 'y'] as $axis) {
        expect(BoundsRegistry::evaluateRemExpression($get('case.nested.anchorNode-return')[$axis]))
            ->toBe(BoundsRegistry::evaluateRemExpression($get('case.next.anchorNode-end')[$axis]));
    }
    expect($get('anchorNode-end')['returnColor'])->toBe('amber');
})->with(['left', 'right'])->with(['bottom-top', 'top-bottom'])->with([false, true]);

it('keeps the saved nested switch examples handmade and connected on both sides', function (): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.switch-case.flow-switch-case-nested';
    $source = file_get_contents(app('view')->getFinder()->find($view));
    expect($source)->not->toContain('@foreach', '@include');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-switch-case'))->toBe(4);
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//pre/code')->length)->toBe(8);
    foreach (['' => -1, '-right' => 1] as $suffix => $sign) {
        $graph = 'idea-to-paper-flow-switch-case-nested'.$suffix;
        $canvas = $xpath->query('//*[@id="'.$graph.'"]')->item(0);
        expect($canvas)->not->toBeNull();
        expect($canvas->textContent)->toContain('SWITCH ($status)', 'SWITCH ($format)', 'Continue process');
        expect($canvas->textContent)->not->toContain(':cases=', '=>', '{--');
        $get = fn ($key) => AnchorRegistry::get($graph, 'literature.switch.1.nested'.$suffix.'.'.$key);
        $number = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
        foreach (['x', 'y'] as $axis) {
            expect($number($get('inner.anchorNode-start')[$axis]))->toBe($number($get('outer.case.editable.anchorNode-end')[$axis]));
            expect($number($get('inner-return.stem.anchorNode-end')[$axis]))->toBe($number($get('outer.case.editable.anchorNode-return')[$axis]));
        }
        expect($get('outer.case.editable.return.anchorNode-end'))->toBeNull();
        expect($sign * ($number($get('inner.anchorNode-end')['x']) - $number($get('inner.anchorNode-start')['x'])))->toBeGreaterThan(0);
        expect($number($get('outer.case.editable.anchorNode-return')['y']) - $number($get('inner-return.anchorNode-end')['y']))->toBeGreaterThanOrEqual(0);
        expect($get('outer.anchorNode-end')['returnColor'])->toBe('amber');
    }
});


it('routes unmatched values through a plain bypass when DEFAULT is disabled', function (string $side, string $direction, bool $grouped): void {
    $entries = $grouped ? [['key' => 'a'], ['key' => 'b'], ['key' => 'c']] : [];
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="switch-no-default">
            <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                id="no-default" :side="$side" :direction="$direction" :case-default="false"
                :cases="[
                    ['key' => 'first', 'entries' => $entries, 'actionLabel' => ['text' => ['First action'], 'width' => 'half']],
                    ['key' => 'last', 'actionLabel' => ['text' => ['Last action'], 'width' => 'long']],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'direction', 'entries'));
    $get = fn ($key) => AnchorRegistry::get('switch-no-default', 'no-default.'.$key);
    $number = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
    expect($html)->not->toContain('DEFAULT', 'Default action', 'no-default.case.default', 'no-default.bypass.bridge1.label');
    expect($html)->toContain('First action', 'Last action', 'END SWITCH', 'no-default.bypass.bridge1');
    foreach (['x', 'y'] as $axis) {
        expect($number($get('bypass.anchorNode-end')[$axis]))->toBe($number($get('anchorNode-end')[$axis]));
        expect($number($get('case.last.return.anchorNode-end')[$axis]))->toBe($number($get('anchorNode-end')[$axis]));
    }
    expect($number($get('case.first.anchorNode-end')['x']))->toBe($number($get('anchorNode-end')['x']));
    expect(($direction === 'bottom-top' ? 1 : -1) * ($number($get('anchorNode-end')['y']) - $number($get('case.last.anchorNode-end')['y'])))->toBeGreaterThan(0);
})->with(['left', 'right'])->with(['bottom-top', 'top-bottom'])->with([false, true]);

it('connects fall-through to subsequent actions while keeping the break rail separate', function (string $side, string $direction, bool $grouped): void {
    $entries = $grouped ? [['key' => 'a'], ['key' => 'b']] : [];
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="switch-fall-through">
            <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                id="fall" :side="$side" :direction="$direction" stem-length="16rem" bridge-length="4.75rem"
                :cases="[
                    ['key' => 'closed', 'actionLabel' => ['text' => ['Closed action']]],
                    ['key' => 'first', 'fallThrough' => true, 'entries' => $entries, 'actionLabel' => ['text' => ['First action'], 'bridgeOutLength' => '2rem']],
                    ['key' => 'second', 'fallThrough' => true, 'actionLabel' => ['text' => ['Second action'], 'bridgeOutLength' => '2rem']],
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('side', 'direction', 'entries'));
    $get = fn ($key) => AnchorRegistry::get('switch-fall-through', 'fall.'.$key);
    $number = fn ($value) => BoundsRegistry::evaluateRemExpression($value);
    foreach (['first' => 'second', 'second' => 'default'] as $from => $to) {
        foreach (['x', 'y'] as $axis) {
            expect($number($get('case.'.$from.'.fall-through.join.anchorNode-end')[$axis]))
                ->toBe($number($get('case.'.$to.'.anchorNode-action')[$axis]));
        }
        expect($number($get('case.'.$from.'.anchorNode-end')['x']))->not->toBe($number($get('anchorNode-end')['x']));
        expect($number($get('case.'.$from.'.return.anchorNode-end')['x']))->toBe($number($get('anchorNode-end')['x']));
    }
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    foreach (['first', 'second'] as $key) {
        $prefix = 'fall.case.'.$key.'.fall-through';
        $arrow = $xpath->query('//*[@data-tw-graph-path="'.$prefix.'.stem.end.joint-arrow"]');
        expect($arrow->length)->toBe(1);
        expect($arrow->item(0)->getAttribute('class'))->toContain('tw-graph-protocol-primitive-joint-arrow-'.($direction === 'bottom-top' ? 'top' : 'bottom'));
        expect($xpath->query('//*[@data-tw-graph-path="'.$prefix.'.join.end.joint-arrow"]')->length)->toBe(0);
        $join = $xpath->query('//*[@data-tw-graph-path="'.$prefix.'.join"]')->item(0);
        expect($xpath->query('//*[@data-tw-graph-path="'.$prefix.'.join.node.end"]')->length)->toBe(0);
        $nextKey = $key === 'first' ? 'second' : 'default';
        $bridge = $xpath->query('//*[@data-tw-graph-path="fall.case.'.$nextKey.'.bridge1.bridge-in"]')->item(0);
        $dots = $xpath->query('//*[@data-tw-graph-path="fall.case.'.$nextKey.'.bridge1.bridge-in.node.join"]');
        expect($dots->length)->toBe(1);
        $dot = $dots->item(0);
        foreach (['x', 'y'] as $axis) {
            expect($number($get('case.'.$key.'.fall-through.join.anchorNode-end')[$axis]))->toBe($number($get('case.'.$nextKey.'.bridge1.bridge-in.anchorNode-join')[$axis]));
        }
        $layer = static function (DOMElement $element): int {
            preg_match('/--tw-graph-protocol-z-index:\s*(-?\d+)/', $element->getAttribute('style'), $matches);
            return (int) $matches[1];
        };
        expect($layer($join))->toBeLessThan($layer($bridge));
        expect($layer($dot))->toBeGreaterThan($layer($bridge));

    }
    foreach (['First action', 'Second action'] as $text) {
        expect($xpath->query('//*[contains(@class, "tw-graph-protocol-primitive-text-line") and not(ancestor::*[@aria-hidden="true"]) and normalize-space(text())="'.$text.'"]')->length)->toBe(1);
    }
})->with(['left', 'right'])->with(['bottom-top', 'top-bottom'])->with([false, true]);

it('honors visible fall-through bridge and join lengths without implicit shortening', function (?string $out): void {
    Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="explicit-fall">
            <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                id="explicit" bridge-length="5rem" arc-radius="2.75rem" stem-length="16rem"
                :cases="[
                    ['key' => 'first', 'fallThrough' => true, 'fallThroughJoinLength' => '3.25rem',
                     'actionLabel' => ['text' => ['First'], 'width' => 'default', 'bridgeOutLength' => $out]],
                    ['key' => 'next', 'actionLabel' => ['text' => ['Next'], 'width' => 'default']],
                ]"
                :case-default="['text' => ['Fallback'], 'width' => 'default']"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('out'));
    $get = fn ($key) => AnchorRegistry::get('explicit-fall', 'explicit.'.$key);
    $number = fn ($v) => BoundsRegistry::evaluateRemExpression($v);
    $span = $number($get('case.first.entry.anchorNode-end')['x']) - $number($get('case.first.anchorNode-end')['x']);
    expect($span)->toBe(2 * 2.75 + 12 + 5 + $number($out ?? '5rem'));
    $joinOffset = $number($get('case.next.entry.anchorNode-end')['x']) - $number($get('case.next.anchorNode-action')['x']);
    expect($joinOffset)->toBe(2.75 + 3.25);
})->with([null, '2rem', '3rem']);
