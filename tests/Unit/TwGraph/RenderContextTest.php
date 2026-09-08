<?php

declare(strict_types=1);

use Gunreip\TranslationWorkbench\Support\TwGraph\RenderContext;

it('uses geometry values ahead of inherited render defaults for protocol rendering', function (): void {
    $context = RenderContext::make(
        [
            'geometry' => [
                'direction' => 'top-bottom',
                'color' => 'amber',
                'minWidth' => '72rem',
                'minHeight' => '80rem',
                'pathWidth' => '0.5rem',
                'nodeSize' => '1.25rem',
                'arcSize' => '4rem',
            ],
        ],
        'context-test',
        false,
        null,
        '0.25rem',
        '0.95rem',
        '2.75rem',
        '52rem',
        null,
        null,
    );

    expect($context)
        ->toMatchArray([
            'direction' => 'top-bottom',
            'color' => 'amber',
            'graphId' => 'context-test',
            'minWidth' => '72rem',
            'minHeight' => '80rem',
            'pathWidth' => '0.5rem',
            'nodeSize' => '1.25rem',
            'arcSize' => '4rem',
        ])
        ->and($context['colorRgb'])->toBe('245 158 11');
});

it('uses slot min height for manually authored slot graphs', function (): void {
    $context = RenderContext::make(
        [],
        'slot-context-test',
        true,
        'green',
        '0.25rem',
        '0.95rem',
        '2.75rem',
        '66rem',
        '44rem',
        null,
    );

    expect($context)
        ->toMatchArray([
            'direction' => 'bottom-top',
            'color' => 'green',
            'graphId' => 'slot-context-test',
            'minWidth' => '44rem',
            'minHeight' => '66rem',
            'pathWidth' => '0.25rem',
            'nodeSize' => '0.95rem',
            'arcSize' => '2.75rem',
        ])
        ->and($context['colorRgb'])->toBe('44 144 103');
});

it('calculates protocol canvas size from resolved segment anchors', function (): void {
    $context = RenderContext::make(
        [
            'twGraph' => [
                'strang' => [
                    'trunk' => [
                        'trunk' => [
                            'paths' => [
                                [
                                    'segments' => [
                                        'paths' => [
                                            ['anchorStart' => ['x' => '-8rem', 'y' => '0rem'], 'anchorEnd' => ['x' => '6rem', 'y' => '18rem']],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'size-context-test',
        false,
        null,
        '0.25rem',
        '0.95rem',
        '2.75rem',
        '52rem',
        null,
        null,
    );

    expect($context['canvasWidth'])->toBe('40rem')
        ->and($context['canvasHeight'])->toBe('24rem')
        ->and($context['minWidth'])->toBe('40rem')
        ->and($context['minHeight'])->toBe('24rem');
});

it('keeps explicit min width and min height ahead of calculated protocol canvas size', function (): void {
    $context = RenderContext::make(
        [
            'twGraph' => [
                'strang' => [
                    'trunk' => [
                        'trunk' => [
                            'paths' => [
                                [
                                    'segments' => [
                                        'paths' => [
                                            ['anchorStart' => ['x' => '-8rem', 'y' => '0rem'], 'anchorEnd' => ['x' => '6rem', 'y' => '18rem']],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'explicit-size-context-test',
        false,
        null,
        '0.25rem',
        '0.95rem',
        '2.75rem',
        '52rem',
        '80rem',
        '90rem',
    );

    expect($context['canvasWidth'])->toBe('40rem')
        ->and($context['canvasHeight'])->toBe('24rem')
        ->and($context['minWidth'])->toBe('80rem')
        ->and($context['minHeight'])->toBe('90rem');
});

it('uses minimum canvas dimensions when protocol has no segments', function (): void {
    $context = RenderContext::make(
        [],
        'empty-context-test',
        false,
        'unknown-color',
        '0.25rem',
        '0.95rem',
        '2.75rem',
        '52rem',
        null,
        null,
    );

    expect($context['canvasWidth'])->toBe('40rem')
        ->and($context['canvasHeight'])->toBe('18rem')
        ->and($context['minWidth'])->toBe('40rem')
        ->and($context['minHeight'])->toBe('18rem')
        ->and($context['colorRgb'])->toBe('113 113 122');
});
