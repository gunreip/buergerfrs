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
    'slot_min_height' => '52rem',
    'horizontal_padding' => '12rem',

    'label_width' => [
        'default' => '12rem',
        'half_long' => '16rem',
        'long' => '20rem',
    ],
];
