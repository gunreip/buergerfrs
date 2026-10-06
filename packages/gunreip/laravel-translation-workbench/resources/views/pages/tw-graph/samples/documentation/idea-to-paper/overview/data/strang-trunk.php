<?php

/**
 * strang-trunk: global for inherited settings; direct props for this element; named sub-sections in children.
 * global applies to this element and descendants; direct props override only this element.
 * children.global applies from the children onward, never to their parent.
 * No parallel named SubTabs; no vars wrapper or legacy aliases.
 *
 * Main section global fields:
 * color = merge/extension color, inherited by following connections until overridden.
 * connection = ['stemLength' => '8rem', 'bridgeLength' => '11rem', 'arcRadius' => '3rem'].
 * Base tabs Parts/Paths support the same arcRadius and additionally startLength.
 * label = ['width' => 'halfLong', 'align' => 'center'].
 * Regular label also supports text (translated string), direction, beforeLength,
 * afterLength, color. Overview/Inventory label supports text (array), width, align, side.
 * Their end-labels do not support beforeLength/afterLength/direction/color.
 *
 * Width: half/default/halfLong/long. align: left/center/right (text within the box).
 * side: left/right/center for SubTab branches; left/right for leaf connectors; vertical direction: top-bottom/bottom-top.
 * A center branch preserves the nearest inherited left/right side for connector children,
 * or their common right default. Explicit children.global.side=left/right still wins.
 * Omitted values inherit; no special first-entry length or geometry compensation.
 * IDs, new entries and unsupported fields cannot be added as overrides.
 * Unknown keys/types produce a mismatch; old vars/merges/levels/nodes paths are rejected.
 * Marker options for flow-step endpoints (booleans):
 * nodeEnd=true and nodeEndDot=true are the defaults. false nodeEndDot selects an arrow.
 * For a joint-arrow: 'nodeEnd' => true, 'nodeEndDot' => false.
 * Set these directly for one element, in global for its subtree, or children.global from its children onward.
 * Local values override inherited ones. Group settings affect its spine and label steps.
 * Regular main-tab labels also accept these in label; global.labelDefaults sets their defaults.
 * Overview/Inventory end-labels and merge/sideways paths are not flow-step endpoints.
 * nodeEnd=false suppresses an unlabelled endpoint; attached labels retain their anchor
 * according to flow-step's public contract. An attached label forces a dot unless label.nodeEnd=false; the connector remains.
 */
return [
    'strang-trunk' => [
        'global' => [
            'color' => 'orange',
            'connection' => [
                'stemLength' => '3rem',
                'bridgeLength' => '19rem',
                // 'arcRadius' => '3rem',
            ],
            'label' => [
                'width' => 'half',
                'align' => 'center',
            ],
        ],
        'children' => [
            'global' => [
                'side' => 'right',
                'bridgeLength' => '3rem',
            ],
            'default' => [
                'global' => [
                    'stemLength' => '4rem',
                    'bridgeLength' => '0rem',
                ],
            ],
            'stem-count' => [
                'global' => [
                    'stemLength' => '7rem',
                    'bridgeLength' => '0rem',
                    'side' => 'left',
                ],
            ],
            'stem-lengths' => [
                'global' => [
                    'stemLength' => '7rem',
                    'bridgeLength' => '0rem',
                ],
            ],
            // direction is the traversal setting; directions identifies this SubTab.
            'directions' => [
                'global' => [
                    'stemLength' => '14rem',
                    'bridgeLength' => '0rem',
                ],
            ],
            'start' => [
                'global' => [
                    'stemLength' => '14rem',
                    'bridgeLength' => '0rem',
                ],
                'children' => [
                    'global' => [
                        'stemLength' => '3rem',
                        'align' => 'left',
                    ],
                    'overview' => [
                        'global' => [
                            'stemLength' => '0rem',
                        ],
                    ],
                    'compare' => [
                        'global' => [],
                    ],
                    'default' => [
                        'global' => [],
                    ],
                    'long-start' => [
                        'global' => [
                            'width' => 'default',
                        ],
                    ],
                    'wide-labels' => [
                        'global' => [
                            'width' => 'default',
                        ],
                    ],
                    'spacing' => [
                        'global' => [],
                    ],
                    'colors' => [
                        'global' => [],
                    ],
                ],
            ],
            'start-shift' => [
                'global' => [
                    'stemLength' => '34rem',
                    'bridgeLength' => '15rem',
                    'label' => [
                        'beforeLength' => '6rem',
                    ],
                ],
            ],
            'end' => [
                'global' => [
                    'stemLength' => '4rem',
                    'bridgeLength' => '0rem',
                ],
                'children' => [
                    'global' => [
                        'stemLength' => '3rem',
                        'align' => 'left',
                    ],
                    'overview' => [
                        'global' => [
                            'stemLength' => '0rem',
                        ],
                    ],
                    'default' => [
                        'global' => [],
                    ],
                    'long-end' => [
                        'global' => [],
                    ],
                    'wide-label' => [
                        'global' => [],
                    ],
                    'cap' => [
                        'global' => [],
                    ],
                    // Plural, as under Start: color is the inherited setting.
                    'colors' => [
                        'global' => [],
                    ],
                ],
            ],
        ],
    ],
];
