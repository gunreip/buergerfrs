<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('keeps a step color independent of anchor metadata unless its incoming color is explicit', function (string $direction, ?string $beforeColor): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="step-color">
            <x-translation-workbench::ui.tw-graph.strang.flow-step
                id="step" color="cyan" :direction="$direction"
                :anchor-start="['x' => '0rem', 'y' => '0rem', 'color' => 'amber']"
                :before-color="$beforeColor"
                :step-label="['text' => ['Continue']]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('direction', 'beforeColor'));
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    foreach (['before' => $beforeColor ?? 'cyan', 'after' => 'cyan'] as $part => $color) {
        $stem = $xpath->query('//*[@data-tw-graph-path="step.stem.' . $part . '"]')->item(0);
        expect($stem)->not->toBeNull();
        expect($stem->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: ' . TranslationWorkbenchColorPalette::rgb($color) . ';');
    }
})->with(['bottom-top', 'top-bottom', 'left-right', 'right-left'])->with([null, 'green']);

it('renders the nested test continuation in its configured color while preserving internal ELSEIF transitions', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-test')->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $prefix = 'literature.flow.1.if-nested-test.';
    foreach (['before', 'after'] as $part) {
        $stem = $xpath->query('//*[@data-tw-graph-path="' . $prefix . 'continue.stem.' . $part . '"]')->item(0);
        expect($stem)->not->toBeNull();
        expect($stem->getAttribute('style'))->toContain('--tw-graph-protocol-local-color-rgb: ' . TranslationWorkbenchColorPalette::rgb('fuchsia') . ';');
    }
    $previous = $xpath->query('//*[@data-tw-graph-path="' . $prefix . 'outer.if.question.stem.after"]')->item(0);
    $next = $xpath->query('//*[@data-tw-graph-path="' . $prefix . 'outer.elseif.automatic.question.stem.before"]')->item(0);
    $localColor = function (DOMElement $element): string {
        preg_match('/--tw-graph-protocol-local-color-rgb: ([^;]+);/', $element->getAttribute('style'), $matches);
        return $matches[1];
    };
    expect($localColor($next))->toBe($localColor($previous));
});
