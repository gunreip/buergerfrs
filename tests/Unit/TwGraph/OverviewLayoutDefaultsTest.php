<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewEntryLayout;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutDefaults;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure;
use Tests\TestCase;

uses(TestCase::class);

it('gives every group the same role defaults with no layout exceptions hidden in entries', function () {
    $structure = OverviewStructure::data();
    $common = OverviewLayoutDefaults::levels();
    foreach (array_keys(array_filter($base ?? $structure, static fn ($entry) => is_array($entry) && isset($entry['levels'], $entry['children']))) as $key) {
        $tree = $structure[$key];
        expect($tree['levels'])->toBe($common);
        foreach ($tree['children'] as $entry) {
            expect(array_diff(array_keys($entry), ['id', 'text', 'children']))->toBe([]);
            $layout = OverviewEntryLayout::group($tree, $entry);
            expect($layout['side'])->toBe($common['subtabs']['side']);
            expect($layout['stemLength'])->toBe($common['subtabs']['stemLength']);
            expect($layout['bridgeLength'])->toBe($common['subtabs']['bridgeLength']);
            expect($layout['label'])->toBe($common['subtabs']['label']);
            foreach ($entry['children'] as $child) {
                expect(array_keys($child))->toBe(['text']);
            }
        }
    }

});

it('applies level and individual stem settings without changing other trees or the common baseline', function () {
    $base = OverviewStructure::data();
    $result = OverviewLayoutOverrides::apply($base, ['primitives.php' => [
        'primitivesTabs' => [
            'levels' => ['subtabs' => ['stemLength' => '12rem']],
            'children' => ['line' => ['side' => 'right', 'stemLength' => '5rem']],
        ],
    ]]);
    expect($result['issues'])->toBe([]);
    $tree = $result['data']['primitivesTabs'];
    expect(OverviewEntryLayout::group($tree, $tree['children']['line'])['stemLength'])->toBe('5rem');
    $arc = OverviewEntryLayout::group($tree, $tree['children']['arc']);
    expect($arc['stemLength'])->toBe('12rem');
    expect($arc['side'])->toBe('left');
    expect($result['data']['canvasTabs'])->toBe($base['canvasTabs']);
    expect($base['primitivesTabs']['levels'])->toBe(OverviewLayoutDefaults::levels());
});

it('keeps every subtree on one renderer and rejects overrides in the wrong file', function () {
    $directory = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/overview/');
    foreach (['canvas' => 'canvasTabs', 'primitives' => 'primitivesTabs', 'segments' => 'segmentsTabs', 'parts' => 'partsTabs', 'paths' => 'pathsTabs', 'strang-trunk' => 'strangTrunkTabs', 'strang-merge' => 'strangMergeTabs', 'strang-branch' => 'strangBranchTabs', 'strang-rekey' => 'strangRekeyTabs', 'flow' => 'flowTabs', 'deep-reference' => 'deepReference'] as $file => $key) {
        $source = file_get_contents($directory.'structure/'.$file.'.blade.php');
        expect($source)->toContain('ui.tw-graph.documentation-tree', ":tree=\"\$structure['$key']\"");
        expect($source)->not->toContain('@foreach', 'stemLength', 'beforeLength');
        $values = require $directory.'data/'.$file.'.php';
        expect(array_diff(array_keys($values), [$file]))->toBe([]);
    }
    $scoped = OverviewLayoutOverrides::scope('canvas.php', [
        'merges' => ['left' => ['extensionColors' => [2 => 'red']]],
        'canvas' => [
            'global' => [], 'children' => ['global' => [], 'default' => ['global' => ['side' => 'right']],
            ],
        ],
    ]);
    expect($scoped['issues'])->toHaveCount(1);
    expect($scoped['issues'][0]['path'])->toBe('merges');
    expect($scoped['values'])->toHaveKey('canvasTabs')->not->toHaveKey('merges');
    expect(OverviewLayoutOverrides::load()['issues'])->toBe([]);
});

it('uses default width on every overview level unless explicitly overridden or inherited', function () {
    $base = OverviewStructure::data();
    expect($base['tabLayout']['width'])->toBe('default');
    foreach ($base['merges']['left']['extensionEndLabels'] as $label) {
        expect($label['width'])->toBe('default');
    }
    foreach (array_keys(array_filter($base ?? $structure, static fn ($entry) => is_array($entry) && isset($entry['levels'], $entry['children']))) as $key) {
        expect($base[$key]['levels']['subtabs']['label']['width'])->toBe('default');
        expect($base[$key]['levels']['subsubtabs']['width'])->toBe('default');
    }
    $compiled = OverviewLayoutOverrides::scope('primitives.php', ['primitives' => [
        'global' => ['width' => 'half'],

        'children' => [
            'global' => [], 'markers-connectors' => ['global' => [], 'children' => ['global' => [], 'node' => ['global' => ['width' => 'default']]]],
        ],
    ]]);
    expect($compiled['issues'])->toBe([]);
    $resolved = OverviewLayoutOverrides::apply($base, ['primitives.php' => $compiled['values']]);
    expect($resolved['issues'])->toBe([]);
    $tree = $resolved['data']['primitivesTabs'];
    expect($resolved['data']['tabs']['primitives']['width'])->toBe('half');
    expect($tree['children']['markers-connectors']['label']['width'])->toBe('half');
    expect($tree['children']['markers-connectors']['children']['connector']['width'])->toBe('half');
    expect($tree['children']['markers-connectors']['children']['node']['width'])->toBe('default');
    expect($resolved['data']['canvasTabs'])->toBe($base['canvasTabs']);
});

it('applies the same nested override contract to every discovered subtree', function () {
    $base = OverviewStructure::data();
    foreach ($base as $treeKey => $tree) {
        if (! is_array($tree) || ! isset($tree['levels'], $tree['children'])) {
            continue;
        }
        preg_match('/^literature\.overview\.(.+)\.anchorNode-end$/', $tree['attachTo'], $match);
        expect($match)->toHaveCount(2);
        $name = $match[1];
        $first = array_key_first($tree['children']);
        $compiled = OverviewLayoutOverrides::scope($name.'.php', [$name => [
            'global' => ['label' => ['width' => 'default']],
            'children' => [
                'global' => ['label' => ['width' => 'half'], 'stemLength' => '7rem'],
                $first => ['global' => ['label' => ['width' => 'long'], 'stemLength' => '5rem']],
            ],
        ]]);
        expect($compiled['issues'])->toBe([]);
        $result = OverviewLayoutOverrides::apply($base, [$name.'.php' => $compiled['values']]);
        expect($result['issues'])->toBe([]);
        expect($result['data']['tabs'][$name]['width'])->toBe('default');
        foreach ($result['data'][$treeKey]['children'] as $key => $entry) {
            $layout = OverviewEntryLayout::group($result['data'][$treeKey], $entry);
            expect($layout['label']['width'])->toBe($key === $first ? 'long' : 'half');
            expect($layout['stemLength'])->toBe($key === $first ? '5rem' : '7rem');
        }
        $invalid = OverviewLayoutOverrides::scope($name.'.php', [$name => ['global' => [$first => ['stemLength' => '9rem']]]]);
        expect(array_column($invalid['issues'], 'path'))->toBe([$name.'.global.'.$first]);
    }
});

it('lists the actual tabs in order with short override keys', function (string $section) {
    $source = file_get_contents(base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/'.$section.'/index.blade.php'));
    preg_match_all('/<flux:tab name="idea-to-paper-'.$section.'-([^"]+)"/', $source, $matches);
    expect(array_keys(OverviewStructure::data()[$section.'Tabs']['children']))->toBe($matches[1]);
})->with(['parts', 'paths']);

it('matches the Trunk tab hierarchy including Start and End children', function () {
    $directory = base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/strang/trunk/');
    $groups = OverviewStructure::data()['strangTrunkTabs']['children'];
    foreach (['index' => 'trunk-', 'trunk-start' => 'trunk-start-', 'trunk-end' => 'trunk-end-'] as $file => $prefix) {
        preg_match_all('/<flux:tab name="'.$prefix.'([^\"]+)"/', file_get_contents($directory.$file.'.blade.php'), $matches);
        $entries = $file === 'index' ? $groups : $groups[substr($file, 6)]['children'];
        expect(array_keys($entries))->toBe(array_map(static fn ($key) => ['color' => 'colors', 'direction' => 'directions'][$key] ?? $key, $matches[1]));
    }
});

it('matches the Strang Merge tabs in order using concise override keys', function () {
    $source = file_get_contents(base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/strang/merge/index.blade.php'));
    preg_match_all('/<flux:tab name="idea-to-paper-merge-([^\"]+)">\{\{ __\(\x27([^\x27]+)\x27\)/', $source, $matches);
    $groups = OverviewStructure::data()['strangMergeTabs']['children'];
    expect(array_keys($groups))->toBe(array_map(static fn ($key) => $key === 'base' ? 'default' : $key, $matches[1]));
    expect(array_column($groups, 'text'))->toBe(array_map(fn ($label) => __($label), $matches[2]));
    foreach ($groups as $group) {
        expect($group['children'])->toBe([]);
    }
});

it('matches the Strang Branch tabs in order using concise override keys', function () {
    $source = file_get_contents(base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/strang/branch/index.blade.php'));
    preg_match_all('/<flux:tab name="branch-([^\"]+)">\{\{ __\(\x27([^\x27]+)\x27\)/', $source, $matches);
    $groups = OverviewStructure::data()['strangBranchTabs']['children'];
    expect(array_keys($groups))->toBe($matches[1]);
    expect(array_column($groups, 'text'))->toBe(array_map(fn ($label) => __($label), $matches[2]));
    foreach ($groups as $group) {
        expect($group['children'])->toBe([]);
    }
});

it('matches the Strang Rekey tabs in order using concise override keys', function () {
    $source = file_get_contents(base_path('packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/strang/rekey/index.blade.php'));
    preg_match_all('/<flux:tab name="rekey-([^\"]+)">\{\{ __\(\x27([^\x27]+)\x27\)/', $source, $matches);
    $groups = OverviewStructure::data()['strangRekeyTabs']['children'];
    expect(array_keys($groups))->toBe($matches[1]);
    expect(array_column($groups, 'text'))->toBe(array_map(fn ($label) => __($label), $matches[2]));
    foreach ($groups as $group) {
        expect($group['children'])->toBe([]);
    }
});
