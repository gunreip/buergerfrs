<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewEntryLayout;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('inherits each authored level and lets local children win independently of key order', function () {
    foreach ([false, true] as $reverse) {
        $children = ['global' => ['side' => 'right', 'width' => 'halfLong'], 'flow-start' => ['global' => []], 'flow-while' => ['global' => ['side' => 'left']]];
        if ($reverse) {
            $children = array_reverse($children, true);
        }
        $compiled = OverviewLayoutOverrides::scope('deep-reference.php', ['deep-reference' => [
            'global' => ['side' => 'left', 'width' => 'half', 'align' => 'right'], 'children' => [
                'global' => [],
                'strang' => ['global' => [], 'children' => $children],
                'parts' => ['global' => ['side' => 'right'], 'children' => ['global' => [], 'split' => ['global' => ['width' => 'long']]]],
            ],
        ]]);
        expect($compiled['issues'])->toBe([]);
        $result = OverviewLayoutOverrides::apply(OverviewStructure::data(), ['deep-reference.php' => $compiled['values']]);
        expect($result['issues'])->toBe([]);
        $tree = $result['data']['deepReference'];
        $strang = $tree['children']['strang'];
        $parts = $tree['children']['parts'];
        expect($strang['side'])->toBe('left');
        expect($strang['label']['width'])->toBe('half');
        expect($strang['children']['flow-start']['side'])->toBe('right');
        expect($strang['children']['flow-start']['width'])->toBe('halfLong');
        expect($strang['children']['flow-start']['align'])->toBe('right');
        expect($strang['children']['flow-while']['side'])->toBe('left');
        expect($parts['children']['start']['side'])->toBe('right');
        expect($parts['children']['start']['width'])->toBe('half');
        expect($parts['children']['split']['width'])->toBe('long');
        expect($parts['children']['split']['text'])->toBe('split');
    }
});

it('rejects childrenDefaults and keeps invalid values local in diagnostics', function () {
    $r = OverviewLayoutOverrides::scope('deep-reference.php', ['deep-reference' => [
        'global' => ['childrenDefaults' => ['side' => 'right']],

        'children' => [
            'global' => [], 'strang' => ['global' => [], 'children' => ['global' => ['side' => 42], 'flow-while' => ['global' => ['width' => 'long']]]],
        ],
    ]]);
    expect(array_column($r['issues'], 'path'))->toBe(['deep-reference.global.childrenDefaults', 'deep-reference.children.strang.children.global.side']);
});

it('uses stemLength for inherited and individual child stems and rejects length without an alias', function () {
    $r = OverviewLayoutOverrides::scope('primitives.php', ['primitives' => [

        'global' => [], 'children' => [
            'global' => [], 'markers-connectors' => [
                'global' => ['stemLength' => '8rem', 'endCap' => ['length' => '3rem']],
                'children' => [
                    'global' => ['stemLength' => '4rem'],
                    'node' => ['global' => ['stemLength' => '6rem']],
                    'joint-arrow' => ['global' => ['length' => '2rem']],
                ],
            ],
        ],
    ]]);
    expect(array_column($r['issues'], 'path'))->toBe(['primitives.children.markers-connectors.children.joint-arrow.global.length']);
    $group = $r['values']['primitivesTabs']['children']['markers-connectors'];
    expect($group['stemLength'])->toBe('8rem');
    expect($group['children']['node']['stemLength'])->toBe('6rem');
    expect($group['children']['joint-arrow']['stemLength'])->toBe('4rem');
    expect($group['children']['connector']['stemLength'])->toBe('4rem');
    expect($group['children']['node'])->not->toHaveKey('length');
    expect($group['endCap']['length'])->toBe('3rem');
});

it('uses label at every level with deterministic local precedence and local text', function () {
    foreach ([false, true] as $reverse) {
        $leaf = ['width' => 'half', 'label' => ['width' => 'long', 'text' => 'Custom start']];
        if ($reverse) {
            $leaf = array_reverse($leaf, true);
        }
        $r = OverviewLayoutOverrides::scope('deep-reference.php', ['deep-reference' => [
            'global' => ['label' => ['width' => 'halfLong', 'align' => 'center', 'text' => 'Reference']],

            'children' => [
                'global' => [], 'strang' => [
                    'global' => ['label' => ['text' => 'Custom group']],
                    'children' => [
                        'global' => [],
                        'flow-start' => ['global' => $leaf],
                        'flow-while' => ['global' => ['width' => 'default']],
                    ],
                ],
            ],
        ]]);
        expect($r['issues'])->toBe([]);
        $group = $r['values']['deepReference']['children']['strang'];
        expect($group['label']['width'])->toBe('halfLong');
        expect($group['text'])->toBe('Custom group');
        expect($group['children']['flow-start']['width'])->toBe('long');
        expect($group['children']['flow-start']['text'])->toBe('Custom start');
        expect($group['children']['flow-while']['width'])->toBe('default');
        expect($group['children']['flow-while'])->not->toHaveKey('text');
        expect($group['children']['flow-if']['width'])->toBe('halfLong');
        expect($group['children']['flow-if']['align'])->toBe('center');
    }
});

it('rejects heading on every authored level without an alias', function () {
    $r = OverviewLayoutOverrides::scope('deep-reference.php', ['deep-reference' => [
        'global' => ['heading' => ['width' => 'half']],

        'children' => [
            'global' => [], 'strang' => ['global' => ['heading' => ['width' => 'half']], 'children' => [
                'global' => [], 'flow-start' => ['global' => ['heading' => ['width' => 'half']]],
            ]],
        ],
    ]]);
    expect(array_column($r['issues'], 'path'))->toBe([
        'deep-reference.global.heading', 'deep-reference.children.strang.global.heading',
        'deep-reference.children.strang.children.flow-start.global.heading',
    ]);
});

it('keeps nested children settings inside their scope regardless of entry order', function () {
    foreach ([false, true] as $reverse) {
        $children = [
            'global' => ['label' => ['width' => 'half']],
            'default' => ['global' => ['label' => ['width' => 'long']]],
            'default-trunk' => ['global' => []],
            'props' => ['global' => [], 'children' => [
                'global' => ['label' => ['width' => 'halfLong']],
                'line' => ['global' => ['label' => ['width' => 'default']]],
            ]],
        ];
        if ($reverse) {
            $children = array_reverse($children, true);
        }
        $compiled = OverviewLayoutOverrides::scope('canvas.php', ['canvas' => [
            'global' => ['label' => ['width' => 'default']],
            'children' => $children,
        ]]);
        expect($compiled['issues'])->toBe([]);
        $result = OverviewLayoutOverrides::apply(OverviewStructure::data(), ['canvas.php' => $compiled['values']]);
        expect($result['issues'])->toBe([]);
        $data = $result['data'];
        expect($data['tabs']['canvas']['width'])->toBe('default');
        $groups = $data['canvasTabs']['children'];
        expect($groups['default']['label']['width'])->toBe('long');
        expect($groups['default-trunk']['label']['width'])->toBe('half');
        expect($groups['borders']['label']['width'])->toBe('half');
        expect($groups['props']['label']['width'])->toBe('half');
        expect($groups['props']['children']['line']['width'])->toBe('default');
        expect($groups['props']['children']['node-size']['width'])->toBe('halfLong');
    }
});

it('preserves connector labels below center branches with inherited and local sides', function (?string $ancestorSide) {
    $values = ['global' => [], 'children' => [
        'global' => [],
        'start' => ['global' => ['side' => 'center'], 'children' => ['global' => [], 'compare' => ['global' => ['side' => 'left']]]],
        'end' => ['global' => ['side' => 'center'], 'children' => ['global' => ['side' => 'right']]],
    ]];
    if ($ancestorSide !== null) {
        $values['global']['side'] = $ancestorSide;
    }
    $compiled = OverviewLayoutOverrides::scope('strang-trunk.php', ['strang-trunk' => $values]);
    expect($compiled['issues'])->toBe([]);
    $result = OverviewLayoutOverrides::apply(OverviewStructure::data(), ['strang-trunk.php' => $compiled['values']]);
    expect($result['issues'])->toBe([]);
    $structure = $result['data'];
    $groups = $structure['strangTrunkTabs']['children'];
    expect($groups['start']['side'])->toBe('center');
    foreach ($groups['start']['children'] as $key => $child) {
        expect($child['side'])->toBe($key === 'compare' ? 'left' : ($ancestorSide ?? 'right'));
    }
    foreach ($groups['end']['children'] as $child) {
        expect($child['side'])->toBe('right');
    }
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph :graph-id="$structure['canvas']['graphId']">
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs')
            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.strang-trunk')
        </x-translation-workbench::ui.tw-graph>
    BLADE, compact('structure'));
    foreach ($groups as $group) {
        foreach ($group['children'] as $key => $child) {
            expect($html)->toContain($group['id'].'.'.$key.'.stem.after.label.'.$child['side'].'.'.($child['side'] === 'left' ? '2' : '1').'.connector');
        }
    }
})->with([null, 'left', 'right']);

it('reports center assigned explicitly to a connector label instead of silently dropping it', function () {
    $r = OverviewLayoutOverrides::scope('strang-trunk.php', ['strang-trunk' => ['global' => [], 'children' => [
        'global' => [], 'start' => ['global' => [], 'children' => ['global' => [], 'overview' => ['global' => ['side' => 'center']]]],
    ]]]);
    expect(array_column($r['issues'], 'path'))->toContain('strang-trunk.children.start.children.overview.global.side');
});

it('inherits endCap through children with local field overrides and a real end tab', function () {
    $compiled = OverviewLayoutOverrides::scope('strang-trunk.php', ['strang-trunk' => [
        'global' => ['endCap' => ['length' => '3rem', 'capLength' => '6rem']],
        'children' => [
            'global' => ['endCap' => ['length' => '1rem']],
            'default' => ['global' => []],
            'start' => ['global' => ['endCap' => ['length' => '2rem']]],
            'end' => ['global' => ['endCap' => ['capLength' => '5rem']]],
        ],
    ]]);
    expect($compiled['issues'])->toBe([]);
    $result = OverviewLayoutOverrides::apply(OverviewStructure::data(), ['strang-trunk.php' => $compiled['values']]);
    expect($result['issues'])->toBe([]);
    $tree = $result['data']['strangTrunkTabs'];
    foreach ($tree['children'] as $key => $entry) {
        $layout = OverviewEntryLayout::group($tree, $entry);
        expect($layout['endCap'])->toBe([
            'length' => $key === 'start' ? '2rem' : '1rem',
            'capLength' => $key === 'end' ? '5rem' : '6rem',
        ]);
    }
    $bad = OverviewLayoutOverrides::scope('strang-branch.php', ['strang-branch' => ['global' => [], 'children' => [
        'global' => [], 'default' => ['global' => ['end' => ['length' => '1rem']]],
    ]]]);
    expect(array_column($bad['issues'], 'path'))->toBe(['strang-branch.children.default.global.end']);
});
