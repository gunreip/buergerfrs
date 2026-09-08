<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\LabelFormatter;
use Illuminate\Support\Collection;

it('normalizes whitespace and truncates regular graph label text at the end', function (): void {
    expect(LabelFormatter::graphLabelText('  alpha    beta gamma delta  ', 15))
        ->toBe('alpha beta gamm...');
});

it('truncates graph key labels at the beginning', function (): void {
    expect(LabelFormatter::graphKeyLabelText('admin.translation.workbench.raw-data.table.columns.translation_key', 24))
        ->toBe('...lumns.translation_key');
});

it('formats timestamps as minute precision labels', function (): void {
    expect(LabelFormatter::graphTimestampLabel('2026-08-04T09:22:55+00:00'))
        ->toBe('2026-08-04 09:22');
});

it('formats ordinal sample labels and styled ordinal lines', function (): void {
    expect(LabelFormatter::ordinalSampleLabel(1))->toBe('1st sample:')
        ->and(LabelFormatter::ordinalSampleLabel(2))->toBe('2nd sample:')
        ->and(LabelFormatter::ordinalSampleLabel(3))->toBe('3rd sample:')
        ->and(LabelFormatter::ordinalSampleLabel(11))->toBe('11th sample:')
        ->and(LabelFormatter::ordinalSampleLine(21))->toBe([
            'ordinal' => [
                'number' => 21,
                'suffix' => 'st',
            ],
            'text' => 'sample:',
        ]);
});

it('formats finding id labels with optional timestamps', function (): void {
    expect(LabelFormatter::findingLabel('root finding #5486'))->toBe('finding ID #5486')
        ->and(LabelFormatter::findingIdLabel('root finding #5486'))->toBe(['findingID', '#5486'])
        ->and(LabelFormatter::findingIdLabelWithTimestamp([
            'first_root' => 'root finding #5486',
            'first_timestamp' => '2026-08-04 09:22:11',
        ]))->toBe(['findingID #5486', '2026-08-04 09:22']);
});

it('builds trunk end lines for non moved chains from the state summary', function (): void {
    expect(LabelFormatter::trunkEndLabelLines([
        'root_key_id' => 5,
        'chain_type' => 'bulk',
        'translation_key' => 'ui.states.all',
    ], '37 active - 18 ended'))->toBe([
        'key ID #5',
        '37 active - 18 ended',
        '- TIMELINE CHAIN END -',
    ]);
});

it('builds trunk end lines for moved source and target chains', function (): void {
    $source = LabelFormatter::trunkEndLabelLines([
        'root_key_id' => 3576,
        'chain_type' => 'moved',
        'translation_key' => 'ui.save',
        'meta' => [
            'moved_relations' => [
                [
                    'translation_key' => 'ui.save',
                    'rekeyed_to_translation_key' => 'ui.button.save.save',
                ],
            ],
        ],
    ], '0 active · 0 ended');

    $target = LabelFormatter::trunkEndLabelLines([
        'root_key_id' => 4507,
        'chain_type' => 'moved',
        'translation_key' => 'ui.button.save.save',
        'meta' => [
            'moved_relations' => [
                [
                    'translation_key' => 'ui.save',
                    'rekeyed_to_translation_key' => 'ui.button.save.save',
                ],
            ],
        ],
    ], '1 active · 0 ended');

    expect($source)->toBe([
        'key ID #3576',
        'moved / merged to target key',
        'ui.save -> ui.button.save.save',
        '- TIMELINE CHAIN END -',
    ])->and($target)->toBe([
        'key ID #4507',
        'moved / merged into this key',
        'ui.save -> ui.button.save.save',
        '- TIMELINE CHAIN END -',
    ]);
});

it('formats lang value timestamp lines with locale and shortened literal', function (): void {
    $row = (object) [
        'locale' => 'de',
        'value' => 'Speichern mit einem sehr langen erklärenden Literal',
        'last_seen_at' => '2026-08-04 09:22:55',
        'updated_at' => '2026-08-05 10:00:00',
        'created_at' => '2026-08-01 08:00:00',
    ];

    expect(LabelFormatter::langValueTimestampLine($row))
        ->toBe('2026-08-04 09:22 · de · Speichern mit einem sehr lange...');
});

it('formats merge outcome summary lines', function (): void {
    $summary = new Collection([
        'total' => 56,
        'source_active' => 37,
        'source_inactive' => 18,
        'unknown' => 1,
    ]);

    expect(LabelFormatter::mergeOriginCountLabel($summary))->toBe('56 origins')
        ->and(LabelFormatter::mergeOutcomeResultLine($summary))->toBe('37 active · 18 ended · 1 unknown');
});

it('returns empty labels for blank graph text and timestamps', function (): void {
    expect(LabelFormatter::graphLabelText('   '))->toBe('')
        ->and(LabelFormatter::graphKeyLabelText('   '))->toBe('')
        ->and(LabelFormatter::graphTimestampLabel(null))->toBe('')
        ->and(LabelFormatter::mergeOriginCountLabel(new Collection(['total' => 0])))->toBe('')
        ->and(LabelFormatter::mergeOutcomeResultLine(new Collection(['total' => 0])))->toBe('');
});

it('keeps ordinal suffix exceptions for teen sample numbers', function (): void {
    expect(LabelFormatter::ordinalSampleLabel(0))->toBe('1st sample:')
        ->and(LabelFormatter::ordinalSampleLabel(12))->toBe('12th sample:')
        ->and(LabelFormatter::ordinalSampleLabel(13))->toBe('13th sample:')
        ->and(LabelFormatter::ordinalSampleLabel(22))->toBe('22nd sample:')
        ->and(LabelFormatter::ordinalSampleLine(113))->toBe([
            'ordinal' => [
                'number' => 113,
                'suffix' => 'th',
            ],
            'text' => 'sample:',
        ]);
});

it('builds generic moved trunk end lines when current key is not clearly source or target', function (): void {
    expect(LabelFormatter::trunkEndLabelLines([
        'root_key_id' => 999,
        'chain_type' => 'moved',
        'translation_key' => 'ui.current',
        'meta' => [
            'moved_relations' => [
                [
                    'translation_key' => 'ui.old',
                    'rekeyed_to_translation_key' => 'ui.new',
                ],
            ],
        ],
    ], '0 active - 0 ended'))->toBe([
        'key ID #999',
        'moved / rekeyed',
        'ui.old -> ui.new',
        '- TIMELINE CHAIN END -',
    ]);
});

it('compacts multiple moved relation keys into one trunk end relation line', function (): void {
    expect(LabelFormatter::trunkEndLabelLines([
        'root_key_id' => 4507,
        'chain_type' => 'moved',
        'translation_key' => 'ui.shared.save',
        'meta' => [
            'moved_relations' => [
                [
                    'translation_key' => 'ui.save',
                    'rekeyed_to_translation_key' => 'ui.shared.save',
                ],
                [
                    'translation_key' => 'ui.button.save',
                    'rekeyed_to_translation_key' => 'ui.shared.save',
                ],
                [
                    'translation_key' => 'ui.save',
                    'rekeyed_to_translation_key' => 'ui.shared.save',
                ],
            ],
        ],
    ], '2 active - 0 ended'))->toBe([
        'key ID #4507',
        'moved / merged into this key',
        'ui.save, ui.button.save -> ui.shared.save',
        '- TIMELINE CHAIN END -',
    ]);
});

it('keeps graph source label truncation aligned with key truncation', function (): void {
    expect(LabelFormatter::graphSourceLabelText('resources/views/admin/translation-workbench/pages/raw-data/index.blade.php', 32))
        ->toBe('...ages/raw-data/index.blade.php');
});
