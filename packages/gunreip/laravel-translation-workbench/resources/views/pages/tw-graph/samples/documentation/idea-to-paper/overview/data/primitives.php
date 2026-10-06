<?php

/**
 * primitives: global for inherited settings; direct props for this element; named sub-sections in children.
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
 * Example inside 'primitives.children' (inactive; merge into your existing entries):
 * 'markers-connectors' => [
 *     'global' => [
 *         'side' => 'left', 'stemLength' => '12rem', 'bridgeLength' => '2rem',
 *         'label' => ['width' => 'half', 'align' => 'center'],
 *     ],
 *     'children' => [
 *         'global' => [],
 *         'node' => ['global' => ['stemLength' => '4rem', 'width' => 'halfLong', 'color' => 'cyan']],
 *     ],
 * ],
 *
 * All sub-section global fields:
 * text (translated string), color, direction, side, width, align, stemLength, bridgeLength,
 * label => text, beforeLength, afterLength, width, align;
 * sideways => extensionLength (0rem), nodeEnd (true), nodeEndDot (true),
 * extensionEnd => nodeEnd (true), nodeEndDot (true). Arc and extension markers are independent.
 * endCap => length, capLength; allowed in Tab.global, Tab.children.global and each SubTab.global.
 * Inherits down to each rendered end-cap; local fields override shared ones.
 * children => global for shared settings and named entries of the next level;
 * All leaf fields: text, color, direction, beforeLength, labelGap, stemLength, side, width, align.
 * Leaf label => text, width, align, side, nodeEnd; children.global.label => width, align, side.
 * label is the same name at every level; heading is rejected (no alias).
 * Direct width/align apply as shared presentation settings; label.width/align are
 * more specific at the same level. Local child values always beat inherited values.
 * label.text changes only this entry, never the text of its descendants.
 *
 * Inheritance is the same on every level: supplied settings flow down until
 * explicitly replaced. Inside children.global, props apply to all its entries:
 * 'children' => [
 *     'global' => ['side' => 'right', 'width' => 'halfLong'],
 *     '<existing-leaf-key>' => ['global' => ['side' => 'left']],
 * ],
 * Local settings win over children defaults, which win over inherited settings.
 * Text identifies an entry and is not inherited. Settings affect only elements
 * that support them. Defaults are used only when no authored value was inherited.
 * Element mapping for sub-section ID G and leaf key K:
 * stemLength -> G-stem.stem.before; bridgeLength -> G-branch;
 * label.beforeLength/afterLength -> G.stem.before/after;
 * leaf beforeLength/stemLength -> G.K.stem.before/after; endCap -> G.end-cap.
 * Existing children (use these keys, not IDs or DEV-counter numbers):
 * line: (no children)
 * arc: (no children)
 * line-jump: (no children)
 * text-label: (no children)
 * markers-connectors: node, joint-arrow, connector
 * Width: half/default/halfLong/long. align: left/center/right (text within the box).
 * side: left/right/center for SubTab branches; left/right for leaf connectors; vertical direction: top-bottom/bottom-top.
 * A center branch preserves the nearest inherited left/right side for connector children,
 * or their common right default. Explicit children.global.side=left/right still wins.
 * Omitted values inherit; no special first-entry length or geometry compensation.
 * IDs, new entries and unsupported fields cannot be added as overrides.
 * Unknown keys/types produce a mismatch; old vars/childrenDefaults/merges/levels/nodes paths are rejected.
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
    'primitives' => [
        'global' => [
            'color' => 'yellow',
            'connection' => [
                'bridgeLength' => '21rem',
                'stemLength' => '3rem',
            ],
            'label' => [
                'afterLength' => '0rem',
                'nodeEnd' => false,
                // 'nodeEndDot' => true,
            ],
        ],
        'children' => [
            'global' => [],
            'line' => [
                'global' => [
                    'stemLength' => '4rem',
                    'bridgeLength' => '0rem',
                    'side' => 'left',
                    // 'color' => 'violet',
                    'label' => [
                        'width' => 'half',
                    ],
                    'endCap' => [
                        // 'length' => '3rem',
                    ],
                ],
            ],
            'arc' => [
                'global' => [
                    'stemLength' => '8rem',
                    'bridgeLength' => '0rem',
                    'side' => 'right',
                    'label' => [
                        'width' => 'half',
                    ],
                ],
            ],
            'line-jump' => [
                'global' => [
                    'stemLength' => '8rem',
                    'bridgeLength' => '0rem',
                    'side' => 'left',
                    'label' => [
                        'width' => 'half',
                    ],
                ],
            ],
            'text-label' => [
                'global' => [
                    'stemLength' => '8rem',
                    'bridgeLength' => '0rem',
                    'side' => 'right',
                    'label' => [
                        'width' => 'half',
                    ],
                ],
            ],
            'markers-connectors' => [
                'global' => [
                    'stemLength' => '8rem',
                    'bridgeLength' => '0rem',
                    'side' => 'center',
                    'label' => [
                        'beforeLength' => '2rem',
                        'afterLength' => '0rem',
                    ],
                    'nodeEnd' => false,
                ],
                // 'nodeEndDot' => false,
                // SubSubTab: markers-connectors children
                'children' => [
                    'global' => [
                        'nodeEnd' => true,
                        'nodeEndDot' => true,
                        'stemLength' => '3rem',
                    ],
                    'node' => [
                        'global' => [
                            'width' => 'half',
                        ],
                        // 'nodeEnd' => true,
                        // 'nodeEndDot' => false,
                        // 'color' => 'cyan',
                    ],
                    'joint-arrow' => [
                        'global' => [
                            'width' => 'half',
                        ],
                    ],
                    'connector' => [
                        'global' => [
                            'width' => 'half',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
