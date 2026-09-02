<?php

return [
    /*
     * Central defaults for every tw-graph renderer. Components and diagnostic
     * calculations must read from here instead of duplicating fallback values.
     */
    'line_length' => '4rem',
    'line_width' => '0.25rem',
    'node_size' => '0.95rem',
    'arc_size' => '2.75rem',
    'cap_length' => '1.75rem',
    'bridge_length' => '20rem',
    'stem_length' => '4rem',
    'connector_length' => '2rem',
    'connector_gap' => '0.25rem',
    'label_offset' => '0.75rem',
    'merge_end_label_connector_length' => '5rem',
    'rekey_source_end_label_connector_length' => '5rem',
    'rekey_target_trunk_label_connector_length' => '5rem',
    /*
     * DEV bounds/collision diagnostics. debug_bound_box_gap is only the extra
     * clearance added after the measured overlap; the other values describe the
     * inspected footprint itself and must stay configurable, not hidden in code.
     */
    'debug_bound_box_gap' => '3rem',
    'debug_bound_bridge_height' => '1.5rem',
    'debug_bound_end_segment_width' => '1.5rem',
    'debug_bound_label_reach' => '16rem',
    /*
     * Final side-strang end/bridge collision compensation. The factor scales
     * the measured vertical delta for coarse optical tuning; the stem_step only
     * controls how evenly the resulting delta is distributed across following
     * trunk stems. This applies to branch-like strangs, including rekey-target.
     */
    'trunk_spacing_compensation_factor' => 0.65,
    'trunk_spacing_compensation_stem_step' => '2.75rem',
    'slot_min_height' => '52rem',
    'horizontal_padding' => '12rem',

    /*
     * Optional trunk start collision spacing. When enabled, the first trunk
     * stem may be extended after a real start-label collision was measured.
     * trunk_start_shift_length is the minimum applied delta for that visible
     * compensation, not a pre-rendered default spacer.
     */
    'trunk_start_shift_enabled' => true,
    'trunk_start_shift_length' => '10rem',
    /*
     * If the trunk-start anchor dot is hidden because no left/right labels are
     * attached there, the following visible trunk stem may be shortened by this
     * factor. This is layout rhythm only; DEV counters stay tied to nodeEnd.
     */
    'trunk_start_unlabeled_next_stem_factor' => 0.25,
    /*
     * Data-driven merge layout baseline. These values describe the current
     * verified visual rhythm before collision compensation and optional layout
     * corrections are applied.
     */
    'merge_layout' => [
        'preview_head_candidates' => 6,
        'direct_per_side_before_aggregate' => 5,
        'preferred_compensation_direction' => 'vertical',
        /*
         * Baseline staggering is applied before collision compensation. It sets
         * a real stem-continuation length on every configured merge sequence
         * item (main=1, extension1=2, extension2=3, ...). If collisions remain,
         * the measured compensation is added on top of this configured length.
         */
        'vertical_stagger_enabled' => true,
        'vertical_stagger_sequence' => 'even',
        'vertical_stagger_length' => '8rem',
        'vertical_stagger_stem' => 2,
        'main_stem_continuation' => [
            1 => [],
        ],
        'real_extension_stem_continuation' => [
            1 => [],
        ],
        'tail_extension_stem_continuation' => [
            1 => [],
        ],
    ],

    /*
     * Semantic colors used by tw-graph renderers and data-driven previews.
     * Graph-specific defaults may override these keys without changing builder
     * code or component markup.
     */
    'colors' => [
        'graph' => 'cyan',
        'trunk' => 'green',
        'merge' => 'amber',
        'merge_aggregate' => 'orange',
        'branch' => 'rose',
        'branch_badge' => 'red',
        'rekey' => 'sky',
        'chunk_event' => 'amber',
        'root_event' => 'green',
        'key_event' => 'violet',
        'key_reviewed_event' => 'green',
        'key_updated_event' => 'cyan',
        'finding_event' => 'sky',
        'lang_value_active_event' => 'emerald',
        'lang_value_inactive_event' => 'zinc',
        'review_event' => 'amber',
        'fallback' => 'zinc',
    ],

    'label_width' => [
        'default' => '12rem',
        'half_long' => '16rem',
        'long' => '20rem',
    ],
];
