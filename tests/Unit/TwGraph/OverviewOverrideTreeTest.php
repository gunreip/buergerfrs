<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewEntryLayout;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewOverrideTree;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure;
use Tests\TestCase;

uses(TestCase::class);

it('keeps section connection heading and descendants together with sparse inheritance', function () {
    $base = OverviewStructure::data();
    $compiled = (new OverviewOverrideTree($base))->compile('deep-reference.php', [
        'deep-reference' => [
            'global' => ['connection' => ['bridgeLength' => '19rem'], 'label' => ['width' => 'long'], 'color' => 'rose'], 'children' => ['global' => ['bridgeLength' => '7rem', 'width' => 'half'], 'strang' => [
                'global' => ['stemLength' => '9rem'],
                'children' => ['global' => ['stemLength' => '5rem'], 'flow-while' => ['global' => ['stemLength' => '8rem', 'color' => 'cyan']]],
            ],
                'parts' => ['global' => ['side' => 'right']],
            ],
        ],
    ]);
    expect($compiled['issues'])->toBe([]);
    $result = OverviewLayoutOverrides::apply($base, ['deep-reference.php' => $compiled['values']]);
    expect($result['issues'])->toBe([]);
    $data = $result['data'];
    expect($data['merges']['left']['extensionBridgeContinuations'])->toBe([4 => '19rem']);
    expect($data['merges']['left']['extensionColors'])->toBe([4 => 'rose']);
    expect($data['tabs']['deep-reference']['width'])->toBe('long');
    expect($data['canvasTabs'])->toBe($base['canvasTabs']);
    $tree = $data['deepReference'];
    $strang = OverviewEntryLayout::group($tree, $tree['children']['strang']);
    $parts = OverviewEntryLayout::group($tree, $tree['children']['parts']);
    expect($strang['bridgeLength'])->toBe('7rem');
    expect($strang['stemLength'])->toBe('9rem');
    expect($strang['nodes']['stemLength'])->toBe('5rem');
    expect($strang['nodes']['width'])->toBe('half');
    expect($parts['side'])->toBe('right');
    $leaf = OverviewEntryLayout::node($strang['nodes'], $strang, $tree['children']['strang']['children']['flow-while']);
    expect($leaf['stemLength'])->toBe('8rem');
    expect($leaf['width'])->toBe('half');
    expect($leaf['color'])->toBe('cyan');
});

it('reports authored paths for invalid legacy foreign and misspelled fields without discarding valid edits', function () {
    $result = OverviewLayoutOverrides::scope('canvas.php', [
        'merges' => [], 'canvasTabs' => [], 'deep-reference' => ['global' => []],
        'canvas' => ['global' => ['connection' => ['stemLenght' => '8rem', 'bridgeLength' => '9rem'], 'label' => ['width' => 10]]],
    ]);
    expect(array_column($result['issues'], 'path'))->toBe([
        'merges', 'canvasTabs', 'deep-reference', 'canvas.global.connection.stemLenght', 'canvas.global.label.width',
    ]);
    expect($result['values'])->toBe(['merges' => ['left' => ['extensionBridgeContinuations' => [3 => '9rem']]]]);
});

it('maps named right tabs and terminal labels without populating unrelated dimensions', function () {
    $right = OverviewLayoutOverrides::scope('flow.php', ['flow' => ['global' => ['connection' => ['stemLength' => '9rem', 'arcRadius' => '3rem']]]]);
    expect($right['issues'])->toBe([]);
    expect($right['values'])->toBe(['merges' => ['right' => ['extensionStemLengths' => [5 => '9rem'], 'extensionArcRadiuss' => [5 => '3rem']]]]);
    $end = OverviewLayoutOverrides::scope('overview.php', ['overview' => ['global' => ['label' => ['width' => 'long']]]]);
    expect($end['issues'])->toBe([]);
    expect($end['values'])->toBe(['merges' => ['left' => ['extensionEndLabels' => [5 => ['width' => 'long']]]]]);
    expect(OverviewLayoutOverrides::scope('main-tabs.php', ['global' => ['labelDefaults' => ['width' => 'long']]])['values'])->toBe(['tabLayout' => ['width' => 'long']]);
});

it('preserves authored connection adjustments during the hierarchy migration', function () {
    $loaded = OverviewLayoutOverrides::load();
    expect($loaded['issues'])->toBe([]);
    $directory = OverviewLayoutOverrides::directory();
    foreach (['canvas' => 3, 'deep-reference' => 4, 'overview' => 5, 'inventory' => 6] as $name => $index) {
        $authored = require $directory.'/'.$name.'.php';
        expect($loaded['data']['merges']['left']['extensionBridgeContinuations'][$index])->toBe($authored[$name]['global']['connection']['bridgeLength']);
    }
    $empty = OverviewLayoutOverrides::scope('deep-reference.php', ['deep-reference' => ['global' => []]]);
    expect($empty['values'])->toBe([]);
});

it('rejects parallel subtab entries and removed wrappers with their authored paths', function () {
    $result = OverviewLayoutOverrides::scope('deep-reference.php', [
        'deep-reference' => [
            'global' => ['vars' => ['color' => 'red'], 'strang' => ['stemLength' => '9rem']],
            'children' => [
                'global' => [], 'strang' => [
                    'global' => ['vars' => ['side' => 'left']],
                    'children' => ['global' => [], 'flow-while' => ['global' => ['vars' => ['stemLength' => '4rem']]]],
                ],
            ],
        ],
    ]);
    expect(array_column($result['issues'], 'path'))->toBe([
        'deep-reference.global.vars', 'deep-reference.global.strang', 'deep-reference.children.strang.global.vars',
        'deep-reference.children.strang.children.flow-while.global.vars',
    ]);
    expect($result['values'])->toBe([]);
});

it('forwards the authored radius for both base merge connections without adding a fallback override', function (string $name, string $side) {
    $base = OverviewStructure::data();
    expect($base['merges'][$side])->not->toHaveKey('arcRadius');
    $compiled = OverviewLayoutOverrides::scope($name.'.php', [$name => [
        'global' => ['connection' => ['arcRadius' => '3rem']],
    ]]);
    expect($compiled['issues'])->toBe([]);
    $resolved = OverviewLayoutOverrides::apply($base, [$name.'.php' => $compiled['values']]);
    expect($resolved['issues'])->toBe([]);
    expect($resolved['data']['merges'][$side]['arcRadius'])->toBe('3rem');
    $view = file_get_contents(OverviewLayoutOverrides::directory().'/../structure/main-tabs.blade.php');
    expect(substr_count($view, ':arc-radius="$merge[\'arcRadius\'] ?? null"'))->toBe(2);
})->with([['parts', 'left'], ['paths', 'right']]);
