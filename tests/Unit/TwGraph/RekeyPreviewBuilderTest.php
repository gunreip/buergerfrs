<?php

use App\Settings\AppGeneralSettings;
use Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\TimelineChainGraphData\RekeyPreviewBuilder;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $settings = (new ReflectionClass(AppGeneralSettings::class))->newInstanceWithoutConstructor();
    $settings->locale = 'en';
    $settings->availableLocales = ['en', 'de'];
    $settings->addedPrimaryLocales = ['en', 'de'];

    app()->instance(AppGeneralSettings::class, $settings);

    config()->set('translation-workbench.source_locale', 'en');
    config()->set('tw-graph-defaults.colors.rekey', 'sky');
    config()->set('tw-graph-data-driven-defaults.connector_gap', '0.5rem');

    Schema::shouldReceive('hasTable')->byDefault()->andReturnFalse();
});

it('does not build rekey previews for non moved chains', function (): void {
    $mainRow = [
        'chain_type' => 'single',
        'translation_key' => 'ui.save',
    ];

    expect(RekeyPreviewBuilder::previews($mainRow))->toBe([])
        ->and(RekeyPreviewBuilder::facts($mainRow))->toMatchArray([
            'count' => 0,
            'strangs' => [],
        ]);
});

it('ignores moved relations that do not touch the current trunk key', function (): void {
    $mainRow = [
        'chain_type' => 'moved',
        'translation_key' => 'ui.current.key',
        'root_key_id' => 124,
        'meta' => [
            'moved_relations' => [
                [
                    'translation_key' => 'ui.other.source',
                    'rekeyed_to_translation_key' => 'ui.other.target',
                    'locale' => 'en',
                    'updated_at' => '2026-08-04 09:22:55',
                ],
            ],
        ],
    ];

    expect(RekeyPreviewBuilder::previews($mainRow))->toBe([])
        ->and(RekeyPreviewBuilder::facts($mainRow))->toMatchArray([
            'count' => 0,
            'strangs' => [],
        ]);
});

it('builds a rekey source preview when the current key is the relation target', function (): void {
    $preview = RekeyPreviewBuilder::previews([
        'chain_type' => 'moved',
        'translation_key' => 'ui.button.save.save',
        'root_key_id' => 4507,
        'updated_at' => '2026-08-04 09:22:55',
        'meta' => [
            'moved_relations' => [
                [
                    'translation_key' => 'ui.save',
                    'rekeyed_to_translation_key' => 'ui.button.save.save',
                    'lang_value_id' => 801,
                    'rekeyed_to_lang_value_id' => 802,
                    'locale' => 'en',
                    'updated_at' => '2026-08-04 09:22:55',
                ],
            ],
        ],
    ])[0];

    expect($preview)->toMatchArray([
        'component' => 'tw-graph.strang.rekey-source-left',
        'kind' => 'source',
        'side' => 'left',
        'color' => 'sky',
        'attach_to' => 'strang.trunk.path.1.end',
        'stem_continuation' => [
            1 => ['compressed' => true],
        ],
    ]);

    expect($preview['start_label']['text'])->toBe(['rekey source from ID #?', '2026-08-04 09:22'])
        ->and($preview['start_label']['badgeColor'])->toBe('sky')
        ->and($preview['node_labels'][6]['left'])->toBe([
        'rekeyed into this key ID #4507',
        'ui.save -> ui.button.save.save',
    ])
        ->and($preview['node_labels'][6]['long'])->toBeTrue()
        ->and($preview['node_labels'][6]['connectorLength'])->toBe('5rem');
});

it('builds a rekey target preview when the current key is the relation source', function (): void {
    $preview = RekeyPreviewBuilder::previews([
        'chain_type' => 'moved',
        'translation_key' => 'ui.save',
        'root_key_id' => 3576,
        'updated_at' => '2026-08-04 09:22:55',
        'meta' => [
            'moved_relations' => [
                [
                    'translation_key' => 'ui.save',
                    'rekeyed_to_translation_key' => 'ui.button.save.save',
                    'lang_value_id' => 801,
                    'rekeyed_to_lang_value_id' => 802,
                    'locale' => 'en',
                    'updated_at' => '2026-08-04 09:22:55',
                ],
            ],
        ],
    ])[0];

    expect($preview)->toMatchArray([
        'component' => 'tw-graph.strang.rekey-target-right',
        'kind' => 'target',
        'side' => 'right',
        'color' => 'sky',
        'attach_to' => 'strang.trunk.path.7.end',
    ]);

    expect($preview['start_label']['text'])->toBe(['rekey target to ID #?', '2026-08-04 09:22'])
        ->and($preview['end_label']['text'])->toBe(['rekey target to ID #?', '2026-08-04 09:22'])
        ->and($preview['end_label']['badgeColor'])->toBe('sky')
        ->and($preview['end_label']['long'])->toBeTrue()
        ->and($preview['stem_continuation'][1]['compressed'])->toBeTrue()
        ->and($preview['stem_continuation'][2]['right'])->toBe([
            'Source',
            'source lang value ID #801',
            'target lang value ID #802',
        ])
        ->and($preview['stem_continuation'][2]['left'])->toBe([
            'Target key',
            'ui.button.save.save',
        ]);
});

it('prefers the active target locale relation when several rekey relations describe the same move', function (): void {
    $settings = (new ReflectionClass(AppGeneralSettings::class))->newInstanceWithoutConstructor();
    $settings->locale = 'de-DE';
    $settings->availableLocales = ['en', 'de'];
    $settings->addedPrimaryLocales = ['en', 'de'];

    app()->instance(AppGeneralSettings::class, $settings);

    $preview = RekeyPreviewBuilder::previews([
        'chain_type' => 'moved',
        'translation_key' => 'ui.button.save.save',
        'root_key_id' => 4507,
        'updated_at' => '2026-08-04 09:22:55',
        'meta' => [
            'moved_relations' => [
                [
                    'translation_key' => 'ui.save',
                    'rekeyed_to_translation_key' => 'ui.button.save.save',
                    'lang_value_id' => 801,
                    'rekeyed_to_lang_value_id' => 802,
                    'locale' => 'en',
                    'updated_at' => '2026-08-04 09:22:55',
                ],
                [
                    'translation_key' => 'ui.save',
                    'rekeyed_to_translation_key' => 'ui.button.save.save',
                    'lang_value_id' => 901,
                    'rekeyed_to_lang_value_id' => 902,
                    'locale' => 'de',
                    'updated_at' => '2026-08-05 10:30:00',
                ],
            ],
        ],
    ])[0];

    expect($preview['start_label']['text'])->toBe(['rekey source from ID #?', '2026-08-05 10:30'])
        ->and($preview['node_labels'][2]['right'])->toBe([
            'Source',
            'source lang value ID #901',
        ])
        ->and($preview['source']['relation_count'])->toBe(2);
});

it('returns both source and target previews when the current moved key has incoming and outgoing relations', function (): void {
    $previews = RekeyPreviewBuilder::previews([
        'chain_type' => 'moved',
        'translation_key' => 'ui.shared.save',
        'root_key_id' => 124,
        'updated_at' => '2026-08-04 09:22:55',
        'meta' => [
            'moved_relations' => [
                [
                    'translation_key' => 'ui.save',
                    'rekeyed_to_translation_key' => 'ui.shared.save',
                    'locale' => 'en',
                ],
                [
                    'translation_key' => 'ui.shared.save',
                    'rekeyed_to_translation_key' => 'ui.button.save.save',
                    'locale' => 'en',
                ],
            ],
        ],
    ]);

    expect($previews)->toHaveCount(2)
        ->and($previews[0])->toMatchArray([
            'component' => 'tw-graph.strang.rekey-source-left',
            'kind' => 'source',
            'side' => 'left',
        ])
        ->and($previews[1])->toMatchArray([
            'component' => 'tw-graph.strang.rekey-target-right',
            'kind' => 'target',
            'side' => 'right',
        ]);
});

it('reports rekey facts from the current trunk perspective', function (): void {
    $facts = RekeyPreviewBuilder::facts([
        'chain_type' => 'moved',
        'translation_key' => 'ui.shared.save',
        'root_key_id' => 124,
        'updated_at' => '2026-08-04 09:22:55',
        'meta' => [
            'moved_relations' => [
                [
                    'translation_key' => 'ui.save',
                    'rekeyed_to_translation_key' => 'ui.shared.save',
                    'locale' => 'en',
                ],
                [
                    'translation_key' => 'ui.shared.save',
                    'rekeyed_to_translation_key' => 'ui.button.save.save',
                    'locale' => 'en',
                ],
                [
                    'translation_key' => 'ui.unrelated',
                    'rekeyed_to_translation_key' => 'ui.other',
                    'locale' => 'en',
                ],
            ],
        ],
    ]);

    expect($facts)->toMatchArray([
        'component_family' => 'tw-graph.strang.rekey-source-* / tw-graph.strang.rekey-target-*',
        'role' => 'direct key identity transition into or out of the current trunk',
        'count' => 2,
    ])
        ->and($facts['strangs'][0])->toMatchArray([
            'direction' => 'source',
            'source_key' => 'ui.save',
            'target_key' => 'ui.shared.save',
        ])
        ->and($facts['strangs'][1])->toMatchArray([
            'direction' => 'target',
            'source_key' => 'ui.shared.save',
            'target_key' => 'ui.button.save.save',
        ]);
});
