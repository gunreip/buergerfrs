<?php

return [
    /*
     * Defaults for the timeline-chain data-driven tw-graph. Values listed here
     * override config/tw-graph-defaults.php only for this graph family.
     */
    // '' => '',
    // Paused while component-owned DEV bounds are verified. Authored layout remains unchanged.
    'collision_compensation_enabled' => false,
    // Browser-measured label clearance: trunk stems first, then horizontal lanes.
    'horizontal_label_gap' => '3rem',
    'vertical_label_gap' => '1rem',
    // Clearance between separate strangs; does not stretch a strang's own label sequence.
    'inter_strang_vertical_label_gap' => '3rem',
    // Vertical bridge/label clearance; independent of horizontal stem/label clearance.
    'line_label_gap' => '1rem',
    'horizontal_line_label_gap' => '3rem',
    // Maximum changes per coupled optimization, not forced lengths. Clearance is measured first.
    'merge_compaction_stem_budget' => '3rem',
    'merge_compaction_bridge_budget' => '8rem',
    // Explicitly enabled post-layout extension lanes; all datasets and both sides use the same rule.
    'merge_extension_compaction_indices' => [3, 4],
    'arc_radius' => '2.75rem',
    // Stem lengths inherit the shared TW-Graph defaults.
    // 'label_offset' => '0.75rem',
    // 'merge_end_label_connector_length' => '5rem',
    // 'rekey_source_end_label_connector_length' => '5rem',
    // 'rekey_target_trunk_label_connector_length' => '5rem',
    // 'debug_bound_box_gap' => '2rem',
    // 'debug_bound_bridge_height' => '1.5rem',
    // 'debug_bound_end_segment_width' => '1.5rem',
    // 'debug_bound_label_reach' => '16rem',
    // 'trunk_spacing_compensation_factor' => 1.0,
    // 'trunk_spacing_compensation_stem_step' => '2.75rem',
    // 'trunk_start_shift_enabled' => false,
    // 'trunk_start_shift_length' => '4rem',
    // 'trunk_start_unlabeled_next_stem_factor' => 0.333,

    /*
     * Merge preview layout defaults for this graph family. Override these when
     * the visual merge rhythm should change for every data-driven timeline
     * graph, not for one specific timeline-chain record.
     */
    'merge_layout' => [
        // Horizontal placement starts at the normal bridge defaults for every extension.
        'extension_bridge_extra_length' => '0rem',
        'extension_compensation_enabled' => false,
        'extension_overrides' => [
            1 => ['bridge_extra_length' => '0rem', 'compensation_enabled' => true],
            2 => ['bridge_extra_length' => '0rem', 'compensation_enabled' => true],
            3 => ['bridge_extra_length' => '0rem', 'compensation_enabled' => true],
            4 => ['bridge_extra_length' => '0rem', 'compensation_enabled' => true],
        ],
        'preview_head_candidates' => 6,
        'direct_per_side_before_aggregate' => 5,
        'preferred_compensation_direction' => 'vertical',
        'vertical_stagger_enabled' => false,
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
     * Timeline-chain graph colors. These override config/tw-graph-defaults.php
     * for this data-driven graph family only.
     */
    'colors' => [
        'graph' => 'cyan',
        'trunk' => 'green',
        'merge' => 'amber',
        'merge_aggregate' => 'indigo',
        'branch' => 'rose',
        'branch_badge' => 'red',
        'rekey' => 'sky',
        'chunk_event' => 'fuchsia',
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
];
