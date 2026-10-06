<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Tests\TestCase;

uses(TestCase::class);

it('supports local props at every element level and requires global for children-wide settings', function () {
    $result = OverviewLayoutOverrides::scope('deep-reference.php', ['deep-reference' => [
        'color' => 'red',
        'global' => ['color' => 'cyan'],
        'children' => [
            'side' => 'right',
            'global' => ['side' => 'left'],
            'strang' => [
                'stemLength' => '99rem',
                'global' => ['stemLength' => '7rem'],
                'children' => [
                    'width' => 'half',
                    'global' => ['width' => 'long'],
                    'flow-while' => [
                        'color' => 'red',
                        'global' => ['color' => 'green'],
                    ],
                ],
            ],
        ],
    ]]);
    expect(array_column($result['issues'], 'path'))->toBe([
        'deep-reference.children.strang.children.width',
        'deep-reference.children.side',
    ]);
    $group = $result['values']['deepReference']['children']['strang'];
    expect($group['color'])->toBe('cyan');
    expect($group['side'])->toBe('left');
    expect($group['stemLength'])->toBe('99rem');
    expect($group['children']['flow-while']['stemLength'])->toBe('7rem');
    expect($group['children']['flow-while']['width'])->toBe('long');
    expect($group['children']['flow-while']['color'])->toBe('red');
});

it('keeps local nested label values out of descendants regardless of array order', function () {
    $entry = [
        'children' => ['global' => ['color' => 'green']],
        'label' => ['width' => 'half', 'text' => 'Local heading'],
        'side' => 'right',
        'global' => ['label' => ['width' => 'long'], 'side' => 'left', 'stemLength' => '4rem'],
        'stemLength' => '8rem',
    ];
    $build = fn ($e) => OverviewLayoutOverrides::scope('deep-reference.php', ['deep-reference' => ['children' => ['strang' => $e]]]);
    $result = $build($entry);
    expect($result['issues'])->toBe([]);
    expect($build(array_reverse($entry, true)))->toBe($result);
    $group = $result['values']['deepReference']['children']['strang'];
    expect($group['stemLength'])->toBe('8rem')
        ->and($group['side'])->toBe('right')
        ->and($group['label']['width'])->toBe('half')
        ->and($group['text'])->toBe('Local heading');
    foreach ($group['children'] as $child) {
        expect($child['stemLength'])->toBe('4rem')
            ->and($child['width'])->toBe('long')
            ->and($child['side'])->toBe('left')
            ->and($child['color'])->toBe('green')
            ->and($child)->not->toHaveKey('text');
    }
});

it('reports invalid local props at the authored path without propagating them', function () {
    $result = OverviewLayoutOverrides::scope('deep-reference.php', ['deep-reference' => ['children' => ['strang' => [
        'global' => ['stemLength' => '4rem'],
        'stemLength' => 8,
        'label' => ['unknown' => 'test'],
    ]]]]);
    expect(array_column($result['issues'], 'path'))->toBe([
        'deep-reference.children.strang.stemLength',
        'deep-reference.children.strang.label.unknown',
    ]);
    expect($result['values']['deepReference']['children']['strang']['children']['flow-while']['stemLength'])->toBe('4rem');
});

it('merges nested global props without affecting the parent or unrelated siblings', function () {
    $result = OverviewLayoutOverrides::scope('parts.php', ['parts' => [
        'global' => ['sideways' => ['extensionLength' => '1rem']],
        'children' => [
            'global' => ['sideways' => ['extensionLength' => '2rem', 'extensionEnd' => ['nodeEndDot' => false]]],
            'sideways' => ['global' => ['sideways' => ['extensionEnd' => ['nodeEnd' => false]]]],
            'start' => ['global' => []],
        ],
    ]]);
    expect($result['issues'])->toBe([]);
    $tree = $result['values']['partsTabs'];
    expect($tree['children']['sideways']['sideways'])->toBe([
        'extensionLength' => '2rem',
        'extensionEnd' => ['nodeEndDot' => false, 'nodeEnd' => false],
    ]);
    expect($tree['children']['start']['sideways'])->toBe([
        'extensionLength' => '2rem',
        'extensionEnd' => ['nodeEndDot' => false],
    ]);
    expect($tree['children']['start']['sideways']['extensionEnd'])->not->toHaveKey('nodeEnd');
    expect($result['values'])->not->toHaveKey('canvasTabs');
});

it('reports malformed global blocks at their authored path without losing child overrides', function () {
    $result = OverviewLayoutOverrides::scope('canvas.php', ['canvas' => [
        'global' => 'invalid',
        'children' => ['global' => [], 'default' => ['global' => ['stemLength' => '6rem']]],
    ]]);
    expect(array_column($result['issues'], 'path'))->toBe(['canvas.global']);
    expect($result['values']['canvasTabs']['children']['default']['stemLength'])->toBe('6rem');
});

it('treats omitted empty global blocks like explicit empty blocks at every scope', function () {
    $without = ['flow' => ['children' => ['start' => ['stemLength' => '3rem']]]];
    $with = ['flow' => ['global' => [], 'children' => ['global' => [], 'start' => [
        'global' => [], 'stemLength' => '3rem',
    ]]]];
    $result = OverviewLayoutOverrides::scope('flow.php', $without);
    expect($result['issues'])->toBe([]);
    expect($result)->toBe(OverviewLayoutOverrides::scope('flow.php', $with));
    expect($result['values']['flowTabs']['children']['start']['stemLength'])->toBe('3rem');
});
