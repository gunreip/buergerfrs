<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\FileRegion;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('assigns nested file calls to their own file and omits empty files', function () {
    $script = fn ($token) => '<script data-tw-graph-component-region>'.json_encode(['token' => $token]).'</script>';
    expect(FileRegion::tokens($script(1).$script(2).'<div data-tw-graph-file-tokens="[2]"></div>'))->toBe([1]);
    expect(FileRegion::tokens(''))->toBe([]);
});

it('renders file provenance without changing anchors or adding geometry', function () {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.canvas';
    $render = fn ($wrapped) => Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="file-regions" :dev="true">
            @if ($wrapped)
                <x-translation-workbench::ui.tw-graph.file-region :view="$view">
                    <x-translation-workbench::ui.tw-graph.parts.start id="sample" length="4rem" />
                </x-translation-workbench::ui.tw-graph.file-region>
            @else
                <x-translation-workbench::ui.tw-graph.parts.start id="sample" length="4rem" />
            @endif
        </x-translation-workbench::ui.tw-graph>
    BLADE, ['wrapped' => $wrapped, 'view' => $view]);
    $plain = $render(false);
    $anchor = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('file-regions', 'sample.anchorNode-end');
    $marked = $render(true);
    expect(\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get('file-regions', 'sample.anchorNode-end'))->toBe($anchor);
    expect($marked)->toContain('data-tw-graph-file-region=', realpath(view($view)->getPath()), 'data-copy-text=');
    preg_match('/data-tw-graph-file-tokens="([^"]+)"/', $marked, $match);
    expect(json_decode(html_entity_decode($match[1]), true))->toHaveCount(1);
    expect(substr_count($marked, 'data-tw-graph-bounds="'))->toBe(substr_count($plain, 'data-tw-graph-bounds="'));
});
