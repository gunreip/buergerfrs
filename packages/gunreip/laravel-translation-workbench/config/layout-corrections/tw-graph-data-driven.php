<?php

return [
    /*
     * Deliberate layout corrections for the data-driven timeline-chain graph
     * family. These are graph-type corrections, not per timeline-chain records.
     *
     * Resolution order:
     * central defaults -> data-driven defaults -> collision calculation ->
     * automatic compensation -> correction delta from this file.
     *
     * Corrections are deltas. They adjust the already resolved value instead of
     * replacing defaults with another hard-coded final value.
     */
    'corrections' => [
        /*
        [
            'target' => 'strang.branch.left.1.main.bridge1',
            'prop' => 'bridge_length',
            'delta' => '+4rem',
            'reason' => 'Example: extend this bridge after calculated compensation.',
        ],
        */
    ],

    /*
     * Convenience form for trunk segment deltas. These map to
     * strang.trunk.1.main.path.trunk.path{n}.
     *
     * Example:
     * 'strang.trunk' => [
     *     'stem' => [
     *         3 => '+4rem',
     *     ],
     * ],
     */
    'strang.trunk' => [
        'stem' => [],
    ],
];
