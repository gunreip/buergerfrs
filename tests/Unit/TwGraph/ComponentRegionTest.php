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

it('derives merge groups and their public strang from shared primitive records', function (string $side): void {
    $html = Blade::render('<x-translation-workbench::ui.tw-graph graph-id="merge-groups" :dev="true">'
        .'<x-translation-workbench::ui.tw-graph.strang.merge-'.$side.' id="merge" :extension-count="2" />'
        .'</x-translation-workbench::ui.tw-graph>');
    preg_match('/data-tw-graph-bounds-records>(.*?)<\/script>/s', $html, $match);
    $records = collect(json_decode($match[1], true));
    $groups = $records->pluck('region')->filter()->unique('token')->values();
    expect($groups)->toHaveCount(3);
    expect($groups->pluck('id')->sort()->values()->all())->toBe([
        'merge.extension.1.paths.merge-extension',
        'merge.extension.2.paths.merge-extension',
        'merge.paths.merge',
    ]);
    foreach ($groups as $group) {
        expect($group['ancestors'])->toHaveCount(1)
            ->and($group['ancestors'][0]['id'])->toBe('merge')
            ->and($group['ancestors'][0]['component'])->toBe('...::ui.tw-graph.strang.merge-'.$side);
    }
    expect(substr_count($html, 'data-tw-graph-region-kind="component"'))->toBe(4);
    expect(substr_count($html, 'data-tw-graph-text-group-slot='))->toBe($records->where('kind', 'text')->unique('id')->count());
    expect($html)->toContain('TextLabelGroup', 'textPlacement');
    expect(ComponentRegion::current())->toBeNull();
})->with(['left', 'right']);
