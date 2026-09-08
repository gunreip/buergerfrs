<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\TextLabel;

it('normalizes pipe separated strings into trimmed label lines', function (): void {
    expect(TextLabel::normalize('finding ID #5486 | ui.states.all | 2026-08-04 09:22', 'left', 'amber'))
        ->toMatchArray([
            'text' => ['finding ID #5486', 'ui.states.all', '2026-08-04 09:22'],
            'side' => 'left',
            'badgeColor' => 'amber',
        ]);
});

it('keeps named label options while normalizing nested side text', function (): void {
    $label = TextLabel::normalize([
        'right' => [
            'text' => ['Source', 'lang/de/ui.php'],
            'width' => 'halfLong',
            'align' => 'left',
            'justify' => true,
            'color' => 'sky',
        ],
    ], 'left', 'amber');

    expect($label)->toMatchArray([
        'text' => ['Source', 'lang/de/ui.php'],
        'side' => 'right',
        'width' => 'halfLong',
        'align' => 'left',
        'justify' => true,
        'color' => 'sky',
        'badgeColor' => 'sky',
    ]);
});

it('supports text arrays without leaking formatting options into rendered lines', function (): void {
    expect(TextLabel::normalize([
        'text' => ['merged into', 'shared key ID #124'],
        'width' => 'halfLong',
        'align' => 'right',
    ]))->toMatchArray([
        'text' => ['merged into', 'shared key ID #124'],
        'width' => 'halfLong',
        'align' => 'right',
    ]);
});

it('keeps sibling options when side text is provided as a scalar', function (): void {
    expect(TextLabel::normalize([
        'right' => 'Origin key|admin.buttons.save',
        'width' => 'default',
        'align' => 'left',
        'justify' => true,
        'color' => 'amber',
    ], 'left', 'rose'))->toMatchArray([
        'text' => ['Origin key', 'admin.buttons.save'],
        'side' => 'right',
        'width' => 'default',
        'align' => 'left',
        'justify' => true,
        'color' => 'amber',
        'badgeColor' => 'amber',
    ]);
});

it('ignores null label options so fallbacks can still resolve', function (): void {
    expect(TextLabel::normalize([
        'left' => 'Literal|Save',
        'color' => 'amber',
        'badgeColor' => null,
        'connectorLength' => null,
        'connectorGap' => null,
    ], 'right', 'green'))->toMatchArray([
        'text' => ['Literal', 'Save'],
        'side' => 'left',
        'color' => 'amber',
        'badgeColor' => 'amber',
    ])->not->toHaveKeys(['connectorLength', 'connectorGap']);
});

it('keeps explicit badge colors ahead of generic label colors', function (): void {
    expect(TextLabel::normalize([
        'left' => [
            'text' => 'Literal|Save',
            'color' => 'amber',
            'badgeColor' => 'red',
        ],
    ], 'right', 'sky'))->toMatchArray([
        'text' => ['Literal', 'Save'],
        'side' => 'left',
        'color' => 'amber',
        'badgeColor' => 'red',
    ]);
});

it('returns null for intentionally disabled labels', function (): void {
    expect(TextLabel::normalize(false))->toBeNull()
        ->and(TextLabel::normalize(''))->toBeNull()
        ->and(TextLabel::normalize('null'))->toBeNull();
});

it('preserves ordinal line arrays used by styled label fragments', function (): void {
    $line = ['ordinal' => 1, 'suffix' => 'st'];

    expect(TextLabel::lines([$line, 'sample event']))->toBe([$line, 'sample event']);
});

it('normalizes numeric and boolean label lines without treating true as disabled', function (): void {
    expect(TextLabel::normalize([
        'text' => [5486, true, false, null, ''],
        'color' => 'amber',
    ]))->toMatchArray([
        'text' => ['5486', '1'],
        'color' => 'amber',
        'badgeColor' => 'amber',
    ]);
});

it('keeps parent fallback color when no label color is configured', function (): void {
    expect(TextLabel::normalize([
        'text' => 'Fallback color',
    ], 'right', 'green'))->toMatchArray([
        'text' => ['Fallback color'],
        'side' => 'right',
        'badgeColor' => 'green',
    ]);
});

it('uses the first configured side label and ignores later side alternatives', function (): void {
    expect(TextLabel::normalize([
        'left' => [
            'text' => 'Left label|wins',
            'align' => 'right',
        ],
        'right' => [
            'text' => 'Right label|ignored',
            'align' => 'left',
        ],
        'width' => 'default',
    ], 'right', 'amber'))->toMatchArray([
        'text' => ['Left label', 'wins'],
        'side' => 'left',
        'align' => 'right',
        'width' => 'default',
        'badgeColor' => 'amber',
    ]);
});

it('skips blank side labels while keeping shared options for the first real side', function (): void {
    $label = TextLabel::normalize([
        'left' => '',
        'right' => 'Literal|Save',
        'width' => 'halfLong',
        'align' => 'left',
        'justify' => true,
        'color' => 'sky',
    ], 'left', 'amber');

    expect($label)->toMatchArray([
        'text' => ['Literal', 'Save'],
        'side' => 'right',
        'width' => 'halfLong',
        'align' => 'left',
        'justify' => true,
        'color' => 'sky',
        'badgeColor' => 'sky',
    ])
        ->and($label['text'])->not->toContain('halfLong')
        ->and($label['text'])->not->toContain('left')
        ->and($label['text'])->not->toContain('sky');
});

it('flattens nested label line arrays without leaking option keys', function (): void {
    expect(TextLabel::lines([
        ['text' => 'First|Second'],
        ['Third', ['text' => ['Fourth', '']]],
        ['width' => 'long'],
    ]))->toBe(['First', 'Second', 'Third', 'Fourth', 'long']);
});

it('keeps shared formatting options out of side specific scalar label text', function (): void {
    $label = TextLabel::normalize([
        'left' => 'merged into|shared key ID #124',
        'width' => 'halfLong',
        'align' => 'right',
        'justify' => true,
        'maxLines' => 3,
    ], 'right', 'amber');

    expect($label)->toMatchArray([
        'text' => ['merged into', 'shared key ID #124'],
        'side' => 'left',
        'width' => 'halfLong',
        'align' => 'right',
        'justify' => true,
        'maxLines' => 3,
        'badgeColor' => 'amber',
    ])
        ->and($label['text'])->not->toContain('halfLong')
        ->and($label['text'])->not->toContain('right');
});

it('lets nested side label options override shared label options without leaking either into text', function (): void {
    $label = TextLabel::normalize([
        'width' => 'default',
        'align' => 'left',
        'right' => [
            'text' => ['target key', 'ui.button.save'],
            'width' => 'long',
            'align' => 'right',
            'color' => 'sky',
        ],
    ], 'left', 'amber');

    expect($label)->toMatchArray([
        'text' => ['target key', 'ui.button.save'],
        'side' => 'right',
        'width' => 'long',
        'align' => 'right',
        'color' => 'sky',
        'badgeColor' => 'sky',
    ])
        ->and($label['text'])->not->toContain('long')
        ->and($label['text'])->not->toContain('right');
});

it('keeps shared text label options unless the selected side overrides them', function (): void {
    $label = TextLabel::normalize([
        'width' => 'halfLong',
        'align' => 'right',
        'justify' => true,
        'maxLines' => 3,
        'color' => 'amber',
        'left' => [
            'text' => 'finding ID #5486|ui.states.all|2026-08-04 09:22',
            'align' => 'left',
        ],
    ], 'right', 'rose');

    expect($label)->toMatchArray([
        'text' => ['finding ID #5486', 'ui.states.all', '2026-08-04 09:22'],
        'side' => 'left',
        'width' => 'halfLong',
        'align' => 'left',
        'justify' => true,
        'maxLines' => 3,
        'color' => 'amber',
        'badgeColor' => 'amber',
    ])
        ->and($label['text'])->not->toContain('halfLong')
        ->and($label['text'])->not->toContain('right')
        ->and($label['text'])->not->toContain('true')
        ->and($label['text'])->not->toContain('3');
});

it('keeps center side text from leaking back into label options', function (): void {
    $label = TextLabel::normalize([
        'center' => [
            'text' => ['Step reason', 'source inactive'],
            'width' => 'halfLong',
            'align' => 'center',
            'color' => 'amber',
        ],
    ], null, 'green');

    expect($label)->toMatchArray([
        'text' => ['Step reason', 'source inactive'],
        'side' => 'center',
        'width' => 'halfLong',
        'align' => 'center',
        'color' => 'amber',
        'badgeColor' => 'amber',
    ])
        ->and($label)->not->toHaveKey('center');
});

it('normalizes canonical node labels without leaking geometry options into text', function (): void {
    $labels = TextLabel::nodeLabels([
        'length' => '4rem',
        'compressed' => true,
        'labels' => [
            'left' => [
                'text' => ['Finding ID #42', 'archive note'],
                'align' => 'right',
            ],
            'right' => 'Finding ID #43|review note',
        ],
        'width' => 'halfLong',
        'color' => 'amber',
    ], 'right', 'green', ['length', 'compressed']);

    expect($labels)->toHaveCount(2)
        ->and($labels[0])->toMatchArray([
            'text' => ['Finding ID #42', 'archive note'],
            'side' => 'left',
            'width' => 'halfLong',
            'align' => 'right',
            'color' => 'amber',
            'badgeColor' => 'amber',
        ])
        ->and($labels[1])->toMatchArray([
            'text' => ['Finding ID #43', 'review note'],
            'side' => 'right',
            'width' => 'halfLong',
            'color' => 'amber',
            'badgeColor' => 'amber',
        ])
        ->and($labels[0]['text'])->not->toContain('4rem')
        ->and($labels[0]['text'])->not->toContain('1');
});

it('preserves an explicit side when an already normalized label is normalized again', function (): void {
    $label = TextLabel::normalize([
        'text' => 'First seen|2026-04-14 11:36',
        'side' => 'left',
        'align' => 'right',
    ], null, 'green');

    expect($label)->toMatchArray([
        'text' => ['First seen', '2026-04-14 11:36'],
        'side' => 'left',
        'align' => 'right',
        'badgeColor' => 'green',
    ]);
});
