<?php

return [
    /*
     * Defaults for the timeline-chain data-driven tw-graph. Values listed here
     * override config/tw-graph-defaults.php only for this graph family.
     */
    // '' => '',
    'arc_size' => '2.75rem',
    'stem_length' => '5.75rem',
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
        'preview_head_candidates' => 6,
        'direct_per_side_before_aggregate' => 5,
        'preferred_compensation_direction' => 'vertical',
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
