<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\Defaults;
use Tests\TestCase;

uses(TestCase::class);

it('resolves central graph defaults before fallback values', function (): void {
    config()->set('tw-graph-defaults.stem_length', '8rem');

    expect(Defaults::graphString('stem_length', '5rem'))->toBe('8rem')
        ->and(Defaults::graphRem('stem_length', '5rem'))->toBe(8.0);
});

it('falls back when a central graph default is blank or missing', function (): void {
    config()->set('tw-graph-defaults.bridge_length', '');

    expect(Defaults::graphString('bridge_length', '18rem'))->toBe('18rem')
        ->and(Defaults::graphString('unknown_length', '4rem'))->toBe('4rem');
});

it('lets data driven defaults override central graph defaults', function (): void {
    config()->set('tw-graph-defaults.stem_length', '5rem');
    config()->set('tw-graph-data-driven-defaults.stem_length', '9rem');

    expect(Defaults::dataDrivenString('stem_length', '3rem'))->toBe('9rem')
        ->and(Defaults::dataDrivenRem('stem_length', '3rem'))->toBe(9.0);
});

it('resolves local values before inherited values before fallback values', function (): void {
    expect(Defaults::string('local', 'inherited', 'fallback'))->toBe('local')
        ->and(Defaults::string(null, 'inherited', 'fallback'))->toBe('inherited')
        ->and(Defaults::string(null, null, 'fallback'))->toBe('fallback');
});

it('uses config-aware boolean defaults without treating false as missing', function (): void {
    config()->set('tw-graph-defaults.coordinates', false);
    config()->set('tw-graph-data-driven-defaults.dev', true);

    expect(Defaults::graphBool('coordinates', true))->toBeFalse()
        ->and(Defaults::dataDrivenBool('dev', false))->toBeTrue();
});

it('lets local values override graph defaults for graph string helpers', function (): void {
    config()->set('tw-graph-defaults.label_width.half', '6rem');
    config()->set('tw-graph-defaults.connector_gap', '0.25rem');

    expect(Defaults::graphStringFor('9rem', '8rem', 'label_width.half', '4rem'))->toBe('9rem')
        ->and(Defaults::graphStringFor(null, '8rem', 'label_width.half', '4rem'))->toBe('8rem')
        ->and(Defaults::graphStringFor(null, null, 'label_width.half', '4rem'))->toBe('6rem')
        ->and(Defaults::localOrGraphString('1rem', 'connector_gap', '0rem'))->toBe('1rem')
        ->and(Defaults::localOrGraphString(null, 'connector_gap', '0rem'))->toBe('0.25rem');
});

it('lets explicit data driven false override central true booleans', function (): void {
    config()->set('tw-graph-defaults.trunk_start_shift_enabled', true);
    config()->set('tw-graph-data-driven-defaults.trunk_start_shift_enabled', false);

    expect(Defaults::dataDrivenBool('trunk_start_shift_enabled', true))->toBeFalse();
});

it('falls back from blank data driven booleans to central graph booleans', function (): void {
    config()->set('tw-graph-defaults.trunk_start_shift_enabled', true);
    config()->set('tw-graph-data-driven-defaults.trunk_start_shift_enabled', '');

    expect(Defaults::dataDrivenBool('trunk_start_shift_enabled', false))->toBeTrue();
});

it('returns zero rem values for non numeric defaults', function (): void {
    config()->set('tw-graph-defaults.arc_size', 'wide');
    config()->set('tw-graph-data-driven-defaults.stem_length', 'compact');

    expect(Defaults::graphRem('arc_size', '4rem'))->toBe(0.0)
        ->and(Defaults::dataDrivenRem('stem_length', '4rem'))->toBe(0.0);
});

it('parses string based boolean defaults consistently', function (): void {
    config()->set('tw-graph-defaults.dev', 'off');
    config()->set('tw-graph-data-driven-defaults.coordinates', 'yes');

    expect(Defaults::graphBool('dev', true))->toBeFalse()
        ->and(Defaults::dataDrivenBool('coordinates', false))->toBeTrue();
});

it('falls back from blank data driven values to central graph defaults', function (): void {
    config()->set('tw-graph-defaults.bridge_length', '20rem');
    config()->set('tw-graph-data-driven-defaults.bridge_length', '');

    expect(Defaults::dataDrivenString('bridge_length', '4rem'))->toBe('20rem')
        ->and(Defaults::dataDrivenRem('bridge_length', '4rem'))->toBe(20.0);
});

it('resolves semantic color defaults from graph specific config before central config', function (): void {
    config()->set('tw-graph-defaults.colors.merge_aggregate', 'orange');
    config()->set('tw-graph-defaults.colors.chunk_event', 'amber');
    config()->set('tw-graph-data-driven-defaults.colors.merge_aggregate', 'indigo');
    config()->set('tw-graph-data-driven-defaults.colors.chunk_event', '');

    expect(Defaults::dataDrivenString('colors.merge_aggregate', 'zinc'))->toBe('indigo')
        ->and(Defaults::dataDrivenString('colors.chunk_event', 'zinc'))->toBe('amber')
        ->and(Defaults::graphString('colors.merge_aggregate', 'zinc'))->toBe('orange');
});

it('keeps label width variants configurable through central defaults', function (): void {
    config()->set('tw-graph-defaults.label_width.half', '7rem');
    config()->set('tw-graph-defaults.label_width.default', '14rem');
    config()->set('tw-graph-defaults.label_width.half_long', '18rem');
    config()->set('tw-graph-defaults.label_width.long', '24rem');

    expect(Defaults::graphString('label_width.half', '6rem'))->toBe('7rem')
        ->and(Defaults::graphString('label_width.default', '12rem'))->toBe('14rem')
        ->and(Defaults::graphString('label_width.half_long', '16rem'))->toBe('18rem')
        ->and(Defaults::graphString('label_width.long', '20rem'))->toBe('24rem');
});

it('lets data driven label width variants override central label width defaults', function (): void {
    config()->set('tw-graph-defaults.label_width.default', '14rem');
    config()->set('tw-graph-defaults.label_width.half_long', '18rem');
    config()->set('tw-graph-data-driven-defaults.label_width.default', '15rem');
    config()->set('tw-graph-data-driven-defaults.label_width.half_long', '21rem');

    expect(Defaults::dataDrivenString('label_width.default', '12rem'))->toBe('15rem')
        ->and(Defaults::dataDrivenString('label_width.half_long', '16rem'))->toBe('21rem');
});

it('resolves nested merge layout defaults through the same graph specific chain', function (): void {
    config()->set('tw-graph-defaults.merge_layout.vertical_stagger_length', '6rem');
    config()->set('tw-graph-defaults.merge_layout.preferred_compensation_direction', 'horizontal');
    config()->set('tw-graph-data-driven-defaults.merge_layout.vertical_stagger_length', '9rem');
    config()->set('tw-graph-data-driven-defaults.merge_layout.preferred_compensation_direction', '');

    expect(Defaults::dataDrivenString('merge_layout.vertical_stagger_length', '4rem'))->toBe('9rem')
        ->and(Defaults::dataDrivenString('merge_layout.preferred_compensation_direction', 'vertical'))->toBe('horizontal');
});

it('keeps partial nested data driven defaults from shadowing central sibling defaults', function (): void {
    config()->set('tw-graph-defaults.merge_layout.vertical_stagger_length', '6rem');
    config()->set('tw-graph-defaults.merge_layout.vertical_stagger_sequence', 'odd');
    config()->set('tw-graph-defaults.merge_layout.vertical_stagger_stem', 3);
    config()->set('tw-graph-data-driven-defaults.merge_layout', [
        'vertical_stagger_length' => '10rem',
    ]);

    expect(Defaults::dataDrivenString('merge_layout.vertical_stagger_length', '4rem'))->toBe('10rem')
        ->and(Defaults::dataDrivenString('merge_layout.vertical_stagger_sequence', 'even'))->toBe('odd')
        ->and(Defaults::dataDriven('merge_layout.vertical_stagger_stem', 2))->toBe(3);
});

it('keeps partial nested data driven label and color defaults from shadowing central siblings', function (): void {
    config()->set('tw-graph-defaults.label_width', [
        'half' => '6rem',
        'default' => '12rem',
        'half_long' => '18rem',
        'long' => '24rem',
    ]);
    config()->set('tw-graph-defaults.colors', [
        'trunk' => 'green',
        'merge' => 'amber',
        'branch' => 'rose',
        'rekey' => 'sky',
    ]);
    config()->set('tw-graph-data-driven-defaults.label_width', [
        'half_long' => '20rem',
    ]);
    config()->set('tw-graph-data-driven-defaults.colors', [
        'merge' => 'orange',
    ]);

    expect(Defaults::dataDrivenString('label_width.half', '4rem'))->toBe('6rem')
        ->and(Defaults::dataDrivenString('label_width.half_long', '16rem'))->toBe('20rem')
        ->and(Defaults::dataDrivenString('label_width.long', '20rem'))->toBe('24rem')
        ->and(Defaults::dataDrivenString('colors.trunk', 'zinc'))->toBe('green')
        ->and(Defaults::dataDrivenString('colors.merge', 'zinc'))->toBe('orange')
        ->and(Defaults::dataDrivenString('colors.rekey', 'zinc'))->toBe('sky');
});

it('keeps blank nested color overrides on the fallback chain instead of inventing component colors', function (): void {
    config()->set('tw-graph-defaults.colors.merge', 'amber');
    config()->set('tw-graph-defaults.colors.rekey', '');
    config()->set('tw-graph-data-driven-defaults.colors.merge', '');
    config()->set('tw-graph-data-driven-defaults.colors.rekey', '');

    expect(Defaults::dataDrivenString('colors.merge', 'zinc'))->toBe('amber')
        ->and(Defaults::dataDrivenString('colors.rekey', 'zinc'))->toBe('zinc')
        ->and(Defaults::graphString('colors.rekey', 'zinc'))->toBe('zinc');
});

it('keeps central tw graph layout defaults available for every graph family', function (): void {
    expect(config('tw-graph-defaults'))->toHaveKeys([
        'line_length',
        'line_width',
        'node_size',
        'node_image_size',
        'arc_size',
        'cap_length',
        'bridge_length',
        'stem_length',
        'part_end_length',
        'connector_length',
        'connector_gap',
        'label_offset',
        'merge_end_label_connector_length',
        'rekey_source_end_label_connector_length',
        'rekey_target_trunk_label_connector_length',
        'debug_bound_box_gap',
        'debug_bound_bridge_height',
        'debug_bound_end_segment_width',
        'debug_bound_label_reach',
        'trunk_spacing_compensation_factor',
        'trunk_spacing_compensation_stem_step',
        'trunk_start_shift_enabled',
        'trunk_start_shift_length',
        'trunk_start_unlabeled_next_stem_factor',
        'merge_layout',
        'colors',
        'label_width',
    ]);
});

it('keeps graph specific defaults partial instead of duplicating all central defaults', function (): void {
    expect(config('tw-graph-data-driven-defaults'))->toHaveKeys([
        'arc_size',
        'stem_length',
        'merge_layout',
        'colors',
    ])
        ->and(config('tw-graph-data-driven-defaults'))->not->toHaveKey('label_width')
        ->and(Defaults::dataDrivenString('label_width.long', '20rem'))->toBe(
            Defaults::graphString('label_width.long', '20rem'),
        )
        ->and(Defaults::dataDrivenString('bridge_length', '4rem'))->toBe(
            Defaults::graphString('bridge_length', '4rem'),
        );
});

it('exposes collision spacing defaults through the same data driven override chain', function (): void {
    config()->set('tw-graph-defaults.debug_bound_box_gap', '3rem');
    config()->set('tw-graph-defaults.trunk_spacing_compensation_factor', 0.65);
    config()->set('tw-graph-defaults.trunk_spacing_compensation_stem_step', '2.75rem');
    config()->set('tw-graph-data-driven-defaults.debug_bound_box_gap', '1.5rem');
    config()->set('tw-graph-data-driven-defaults.trunk_spacing_compensation_factor', 0.4);
    config()->set('tw-graph-data-driven-defaults.trunk_spacing_compensation_stem_step', '');

    expect(Defaults::dataDrivenString('debug_bound_box_gap', '0rem'))->toBe('1.5rem')
        ->and(Defaults::dataDriven('trunk_spacing_compensation_factor', 1.0))->toBe(0.4)
        ->and(Defaults::dataDrivenString('trunk_spacing_compensation_stem_step', '1rem'))->toBe('2.75rem');
});

it('exposes debug bound footprint defaults through the same data driven override chain', function (): void {
    config()->set('tw-graph-defaults.debug_bound_bridge_height', '1.5rem');
    config()->set('tw-graph-defaults.debug_bound_end_segment_width', '1.25rem');
    config()->set('tw-graph-defaults.debug_bound_label_reach', '16rem');
    config()->set('tw-graph-data-driven-defaults.debug_bound_bridge_height', '2rem');
    config()->set('tw-graph-data-driven-defaults.debug_bound_end_segment_width', '');
    config()->set('tw-graph-data-driven-defaults.debug_bound_label_reach', '18rem');

    expect(Defaults::dataDrivenString('debug_bound_bridge_height', '0rem'))->toBe('2rem')
        ->and(Defaults::dataDrivenString('debug_bound_end_segment_width', '0rem'))->toBe('1.25rem')
        ->and(Defaults::dataDrivenString('debug_bound_label_reach', '0rem'))->toBe('18rem');
});

it('keeps specialized label connector lengths configurable through central defaults', function (): void {
    config()->set('tw-graph-defaults.merge_end_label_connector_length', '5rem');
    config()->set('tw-graph-defaults.rekey_source_end_label_connector_length', '6rem');
    config()->set('tw-graph-defaults.rekey_target_trunk_label_connector_length', '7rem');

    expect(Defaults::graphString('merge_end_label_connector_length', '2rem'))->toBe('5rem')
        ->and(Defaults::graphString('rekey_source_end_label_connector_length', '2rem'))->toBe('6rem')
        ->and(Defaults::graphString('rekey_target_trunk_label_connector_length', '2rem'))->toBe('7rem');
});

it('keeps canvas line defaults separate from context specific geometry defaults', function (): void {
    config()->set('tw-graph-defaults.line_length', '4rem');
    config()->set('tw-graph-defaults.line_width', '0.25rem');
    config()->set('tw-graph-defaults.stem_length', '6rem');
    config()->set('tw-graph-defaults.bridge_length', '18rem');
    config()->set('tw-graph-defaults.cap_length', '2rem');

    expect(Defaults::graphString('line_length', '1rem'))->toBe('4rem')
        ->and(Defaults::graphString('line_width', '0.1rem'))->toBe('0.25rem')
        ->and(Defaults::graphString('stem_length', '1rem'))->toBe('6rem')
        ->and(Defaults::graphString('bridge_length', '1rem'))->toBe('18rem')
        ->and(Defaults::graphString('cap_length', '1rem'))->toBe('2rem');
});

it('keeps blank graph specific geometry overrides on the central default chain', function (): void {
    config()->set('tw-graph-defaults.arc_size', '3.25rem');
    config()->set('tw-graph-defaults.stem_length', '6rem');
    config()->set('tw-graph-defaults.bridge_length', '18rem');
    config()->set('tw-graph-data-driven-defaults.arc_size', '');
    config()->set('tw-graph-data-driven-defaults.stem_length', null);
    config()->set('tw-graph-data-driven-defaults.bridge_length', '22rem');

    expect(Defaults::dataDrivenString('arc_size', '2rem'))->toBe('3.25rem')
        ->and(Defaults::dataDrivenString('stem_length', '2rem'))->toBe('6rem')
        ->and(Defaults::dataDrivenString('bridge_length', '2rem'))->toBe('22rem');
});
