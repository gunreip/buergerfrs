<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraph\CanvasDiagnostics;
use Gunreip\TranslationWorkbench\Support\TwGraph\ComponentRegion;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('isolates nested canvases while internal components retain their public owner', function (): void {
    $canvas = new CanvasDiagnostics(true);
    $outer = ComponentRegion::begin('outer', $canvas);
    $token = ComponentRegion::current();
    $internal = ComponentRegion::begin('internal', $canvas);
    expect(ComponentRegion::current())->toBe($token);
    expect(ComponentRegion::finish($internal, 'internal', 'cyan'))->toBeNull();
    $nested = ComponentRegion::begin('nested', new CanvasDiagnostics(true));
    expect(ComponentRegion::current())->not->toBe($token);
    expect(ComponentRegion::finish($nested, 'nested', 'amber')['id'])->toBe('nested');
    expect(ComponentRegion::current())->toBe($token);
    expect(ComponentRegion::finish($outer, 'outer', 'cyan')['component'])->toBe('outer');
    expect(ComponentRegion::current())->toBeNull();
});

it('records resolved fallback IDs and restores ownership after rendering errors', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="region-defaults" :dev="true">
            <x-translation-workbench::ui.tw-graph.strang.flow-start />
            <x-translation-workbench::ui.tw-graph.parts.start id="independent" />
        </x-translation-workbench::ui.tw-graph>
        BLADE);
    preg_match_all('/data-tw-graph-bounds-records>(.*?)<\/script>/s', $html, $matches);
    $regions = collect(json_decode($matches[1][0], true))->pluck('region')->filter()->unique('token')->values();
    expect($regions)->toHaveCount(2);
    expect($regions[0]['id'])->not->toBe('');
    expect($regions[1]['id'])->toBe('independent');
    try {
        Blade::render('<x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi id="broken" :elseifs="[]" />');
        $this->fail('Expected invalid configuration');
    } catch (\Illuminate\View\ViewException $exception) {
        expect($exception->getMessage())->toContain('elseif');
    }
    expect(ComponentRegion::current())->toBeNull();
});
