<?php

/**
 * REFERENCE TEMPLATE ONLY — never loaded by OverviewLayoutOverrides.
 * Static, editable PHP examples, not another source of runtime defaults.
 * Copy only needed overrides into the matching data/<tab>.php file.
 * Use global for inherited props, direct props for the current element only.
 * Named SubTabs belong in Tab.children, leaves in SubTab.children.
 * children.global applies to the children AND their descendants, never to their parent.
 * Ordinary prop arrays (label, sideways, endCap, connection) need no extra global wrapper.
 * global and children.global are OPTIONAL at every element/collection level.
 * Omit or comment out empty global => [] blocks: this does not change inheritance.
 * An empty global block does NOT reset inherited values to defaults.
 * Direct props override locally without being inherited.
 * children is a collection: shared props belong in children.global, individual props in named entries.
 * Only props applicable to the current element can be local; descendant-only settings stay in global.
 * Replace example keys with existing structure keys; this file does not create tabs.
 *
 * One vocabulary and inheritance rule for every existing Overview subtree:
 * entry local props > entry.global > parent.children.global > inherited global settings > role defaults.
 * Only the global chain is passed to descendants; local values never enter it.
 * label.width/align are more specific than direct width/align at the same level.
 * label.text changes only that entry. heading/vars/childrenDefaults are not aliases.
 * This is a complete field catalogue, not a neutral configuration to copy wholesale.
 * Local and global versions are shown together to document both valid locations.
 * Copy only the desired scope: local values win over global values on the same element.
 * Shared settings are optional: writing all defaults explicitly changes inheritance!
 * Shared scopes have NO independent default; their values below are examples using
 * leaf defaults. With these scopes omitted, each role keeps its own documented default.
 * Empty color means inherit; leave arcRadius out to inherit the canvas radius.
 *
 * SubTab side=center: straight stem, length=2*canvas arcRadius; bridgeLength has no effect.
 * Leaf connector side remains left/right. Below center, the nearest authored left/right
 * is retained, otherwise the shared right default; local child overrides still win.
 * Roles differ geometrically: a SubTab branch defaults left, a leaf connector right.
 * Global root label text and terminal-tab label text are arrays; other text is a string.
 * Root label supports only text/side/align; terminal labels text/width/align/side.
 * Other tabs use the same main-label settings as deep-reference below.
 * Subtrees currently exist for deep-reference, canvas, primitives, segments, parts, paths, strang-trunk, strang-merge, strang-branch, strang-rekey and flow.
 * Unsupported properties and unknown entry names produce a mismatch.
 * Source of defaults: OverviewLayoutDefaults.php / OverviewStructure.php.
 *
 * Minimal example for data/deep-reference.php (no empty global blocks required):
 * return [
 *     'deep-reference' => [
 *         'children' => [
 *             'global' => ['stemLength' => '6rem'], // Children and descendants.
 *             'strang' => [
 *                 'stemLength' => '10rem', // This SubTab only; descendants still inherit 6rem.
 *                 'children' => [
 *                     'flow-while' => ['stemLength' => '4rem'], // This leaf only.
 *                 ],
 *             ],
 *         ],
 *     ],
 * ];
 * Add an element's global block only when that setting should also reach descendants.
 * The full catalogue below remains PHP data for schema validation; it is never applied.
 */
return [
    // Global settings: copy into data/main-tabs.php only.
    'global' => [
        'canvas' => [
            // Default: 48rem.
            'minWidth' => '48rem',
            // Default: 64rem.
            'minHeight' => '64rem',
            // Default: 3rem.
            'horizontalPadding' => '3rem',
        ],
        'root' => [
            // Default: top-bottom.
            'direction' => 'top-bottom',
            // Default: root 4rem, end-cap stem 2rem.
            'length' => '4rem',
            // Root default: zinc (there is no preceding anchor to inherit from).
            'color' => 'zinc',
            'label' => [
                'text' => [
                    // Default shown below.
                    0 => 'Idea to Paper',
                ],
                // Root label default: bottom.
                'side' => 'bottom',
                // Root label default: center. Text alignment only.
                'align' => 'center',
            ],
        ],
        'labelDefaults' => [
            // Default: true; attached labels retain their anchor.
            'nodeEnd' => true,
            // Default: true.
            'nodeEndDot' => true,
            // Default: top-bottom.
            'direction' => 'top-bottom',
            // Default depends on role: main label 1rem, SubTab label 2rem, leaf 0rem.
            'beforeLength' => '1rem',
            // Default: main label 1rem, SubTab label 2rem.
            'afterLength' => '1rem',
            // Default depends on role: heading center, leaf left. Text alignment only.
            'align' => 'center',
            // Default: default. Supported: half, default, halfLong, long.
            'width' => 'default',
            // Default: inherit the connected color; empty means no explicit color.
            'color' => '',
        ],
    ],
    // Complete Tab -> SubTab -> SubSubTab example. Substitute EXISTING keys for your section.
    'deep-reference' => [
        // LOCAL: affects this element only. Values below show the role defaults.
        'color' => '',
        'connection' => [
            'stemLength' => '4rem',
            'bridgeLength' => '4rem',
            'arcRadius' => '',
        ],
        'label' => [
            'nodeEnd' => true,
            'nodeEndDot' => true,
            'direction' => 'top-bottom',
            'beforeLength' => '1rem',
            'afterLength' => '1rem',
            'align' => 'center',
            'width' => 'default',
            'color' => '',
            'text' => 'Deep Reference',
        ],
        'nodeEnd' => true,
        'nodeEndDot' => true,
        'direction' => 'top-bottom',
        'width' => 'default',
        'align' => 'left',
        'global' => [
            // Default: inherit the connected color; empty means no explicit color.
            'color' => '',
            'connection' => [
                // Default: connection 4rem, SubTab spine 16rem, leaf 3rem; a shared override applies to all descendants.
                'stemLength' => '4rem',
                // Default: 4rem.
                'bridgeLength' => '4rem',
                // Omitted: canvas arc radius (normally 2.75rem). Empty is a schema placeholder, not a radius override.
                'arcRadius' => '',
            ],
            'label' => [
                // Default: true; attached labels retain their anchor.
                'nodeEnd' => true,
                // Default: true.
                'nodeEndDot' => true,
                // Default: top-bottom.
                'direction' => 'top-bottom',
                // Default depends on role: main label 1rem, SubTab label 2rem, leaf 0rem.
                'beforeLength' => '1rem',
                // Default: main label 1rem, SubTab label 2rem.
                'afterLength' => '1rem',
                // Default depends on role: heading center, leaf left. Text alignment only.
                'align' => 'center',
                // Default: default. Supported: half, default, halfLong, long.
                'width' => 'default',
                // Default: inherit the connected color; empty means no explicit color.
                'color' => '',
                // Example content; no shared text default, never inherited.
                'text' => 'Deep Reference',
            ],
            // Default: true; attached labels retain their anchor.
            'nodeEnd' => true,
            // Default: true.
            'nodeEndDot' => true,
            // Default: top-bottom.
            'direction' => 'top-bottom',
            // Default depends on role: branch left, leaf connector right.
            'side' => 'right',
            // Default: connection 4rem, SubTab spine 16rem, leaf 3rem; a shared override applies to all descendants.
            'stemLength' => '3rem',
            // Default: 4rem.
            'bridgeLength' => '4rem',
            // Default depends on role: main label 1rem, SubTab label 2rem, leaf 0rem.
            'beforeLength' => '0rem',
            // Default: 0rem.
            'labelGap' => '0rem',
            // Default: default. Supported: half, default, halfLong, long.
            'width' => 'default',
            // Default depends on role: heading center, leaf left. Text alignment only.
            'align' => 'left',
            // Shared defaults for the next level, followed by individually named children where present.
            // Shared end-cap defaults for this subtree.
            'endCap' => [
                'length' => '2rem',
                'capLength' => '4rem',
            ],
            'sideways' => [
                'extensionLength' => '0rem',
                'nodeEnd' => true,
                'nodeEndDot' => true,
                'extensionEnd' => [
                    'nodeEnd' => true,
                    'nodeEndDot' => true,
                ],
            ],
        ],
        'children' => [
            'global' => [
                // Shared for the following SubTabs; their own endCap fields take precedence.
                'endCap' => [
                    'length' => '2rem',
                    'capLength' => '4rem',
                ],
                // Default: true; attached labels retain their anchor.
                'nodeEnd' => true,
                // Default: true.
                'nodeEndDot' => true,
                // Default: inherit the connected color; empty means no explicit color.
                'color' => '',
                // Default: top-bottom.
                'direction' => 'top-bottom',
                // Default depends on role: branch left, leaf connector right.
                'side' => 'right',
                // Default: connection 4rem, SubTab spine 16rem, leaf 3rem; a shared override applies to all descendants.
                'stemLength' => '3rem',
                // Default: 4rem.
                'bridgeLength' => '4rem',
                'sideways' => [
                    'extensionLength' => '0rem',
                    'nodeEnd' => true, // Arc exit.
                    'nodeEndDot' => true,
                    'extensionEnd' => [
                        'nodeEnd' => true,
                        'nodeEndDot' => true,
                    ],
                ],
                'label' => [
                    // Default depends on role: main label 1rem, SubTab label 2rem, leaf 0rem.
                    'beforeLength' => '2rem',
                    // Default: main label 1rem, SubTab label 2rem.
                    'afterLength' => '2rem',
                    // Default: default. Supported: half, default, halfLong, long.
                    'width' => 'default',
                    // Default depends on role: heading center, leaf left. Text alignment only.
                    'align' => 'center',
                ],
                // Default depends on role: main label 1rem, SubTab label 2rem, leaf 0rem.
                'beforeLength' => '0rem',
                // Default: default. Supported: half, default, halfLong, long.
                'width' => 'default',
                // Default depends on role: heading center, leaf left. Text alignment only.
                'align' => 'left',
                // Default: 0rem.
                'labelGap' => '0rem',
            ],
            // SubTab: its spine, sideways branch, label, end-cap and children.
            'strang' => [
                // LOCAL: affects this element only. Values below show the role defaults.
                'nodeEnd' => true,
                'nodeEndDot' => true,
                'color' => '',
                'direction' => 'top-bottom',
                'side' => 'left',
                'stemLength' => '16rem',
                'bridgeLength' => '4rem',
                'sideways' => [
                    'extensionLength' => '0rem',
                    'nodeEnd' => true,
                    'nodeEndDot' => true,
                    'extensionEnd' => [
                        'nodeEnd' => true,
                        'nodeEndDot' => true,
                    ],
                ],
                'label' => [
                    'beforeLength' => '2rem',
                    'afterLength' => '2rem',
                    'width' => 'default',
                    'align' => 'center',
                    'text' => 'Strang',
                ],
                'endCap' => [
                    'length' => '2rem',
                    'capLength' => '4rem',
                ],
                'text' => 'Strang',
                'width' => 'default',
                'align' => 'center',
                'global' => [
                    // Default: true; attached labels retain their anchor.
                    'nodeEnd' => true,
                    // Default: true.
                    'nodeEndDot' => true,
                    // Default: inherit the connected color; empty means no explicit color.
                    'color' => '',
                    // Default: top-bottom.
                    'direction' => 'top-bottom',
                    // Default depends on role: branch left, leaf connector right.
                    'side' => 'left',
                    // Default: connection 4rem, SubTab spine 16rem, leaf 3rem; a shared override applies to all descendants.
                    'stemLength' => '16rem',
                    // Default: 4rem.
                    'bridgeLength' => '4rem',
                    'sideways' => [
                        'extensionLength' => '0rem',
                        'nodeEnd' => true, // Arc exit.
                        'nodeEndDot' => true,
                        'extensionEnd' => [
                            'nodeEnd' => true,
                            'nodeEndDot' => true,
                        ],
                    ],
                    'label' => [
                        // Default depends on role: main label 1rem, SubTab label 2rem, leaf 0rem.
                        'beforeLength' => '2rem',
                        // Default: main label 1rem, SubTab label 2rem.
                        'afterLength' => '2rem',
                        // Default: default. Supported: half, default, halfLong, long.
                        'width' => 'default',
                        // Default depends on role: heading center, leaf left. Text alignment only.
                        'align' => 'center',
                        // Example content; no shared text default, never inherited.
                        'text' => 'Strang',
                    ],
                    'endCap' => [
                        // Default: root 4rem, end-cap stem 2rem.
                        'length' => '2rem',
                        // Default: 4rem.
                        'capLength' => '4rem',
                    ],
                    // Example content; no shared text default, never inherited.
                    'text' => 'Strang',
                    // Default: default. Supported: half, default, halfLong, long.
                    'width' => 'default',
                    // Default depends on role: heading center, leaf left. Text alignment only.
                    'align' => 'center',
                    // Default depends on role: main label 1rem, SubTab label 2rem, leaf 0rem.
                    'beforeLength' => '0rem',
                    // Default: 0rem.
                    'labelGap' => '0rem',
                ],
                // Shared defaults for the next level, followed by individually named children where present.
                'children' => [
                    'global' => [
                        // Default: true; attached labels retain their anchor.
                        'nodeEnd' => true,
                        // Default: true.
                        'nodeEndDot' => true,
                        // Default: connection 4rem, SubTab spine 16rem, leaf 3rem; a shared override applies to all descendants.
                        'stemLength' => '3rem',
                        // Default depends on role: main label 1rem, SubTab label 2rem, leaf 0rem.
                        'beforeLength' => '0rem',
                        // Default depends on role: branch left, leaf connector right.
                        'side' => 'right',
                        // Default: default. Supported: half, default, halfLong, long.
                        'width' => 'default',
                        // Default depends on role: heading center, leaf left. Text alignment only.
                        'align' => 'left',
                        // Default: inherit the connected color; empty means no explicit color.
                        'color' => '',
                        // Default: top-bottom.
                        'direction' => 'top-bottom',
                        'label' => [
                            // Default: default. Supported: half, default, halfLong, long.
                            'width' => 'default',
                            // Default depends on role: heading center, leaf left. Text alignment only.
                            'align' => 'left',
                            // Default depends on role: branch left, leaf connector right.
                            'side' => 'right',
                        ],
                        // Default: 0rem.
                        'labelGap' => '0rem',
                    ],
                    // SubSubTab: one connector label on the vertical stem.
                    'flow-while' => [
                        // LOCAL: affects this element only. Values below show the role defaults.
                        'label' => [
                            'nodeEnd' => true,
                            'width' => 'default',
                            'align' => 'left',
                            'side' => 'right',
                            'text' => 'flow-while',
                        ],
                        'color' => '',
                        'direction' => 'top-bottom',
                        'nodeEnd' => true,
                        'nodeEndDot' => true,
                        'stemLength' => '3rem',
                        'beforeLength' => '0rem',
                        'labelGap' => '0rem',
                        'side' => 'right',
                        'width' => 'default',
                        'align' => 'left',
                        'text' => 'flow-while',
                        'global' => [
                            // Default: inherit the connected color; empty means no explicit color.
                            'color' => '',
                            // Default: top-bottom.
                            'direction' => 'top-bottom',
                            // Default: true; attached labels retain their anchor.
                            'nodeEnd' => true,
                            // Default: true.
                            'nodeEndDot' => true,
                            // Default: connection 4rem, SubTab spine 16rem, leaf 3rem; a shared override applies to all descendants.
                            'stemLength' => '3rem',
                            // Default depends on role: main label 1rem, SubTab label 2rem, leaf 0rem.
                            'beforeLength' => '0rem',
                            // Default: 0rem.
                            'labelGap' => '0rem',
                            // Default depends on role: branch left, leaf connector right.
                            'side' => 'right',
                            // Default: default. Supported: half, default, halfLong, long.
                            'width' => 'default',
                            // Default depends on role: heading center, leaf left. Text alignment only.
                            'align' => 'left',
                            // Example content; no shared text default, never inherited.
                            'text' => 'flow-while',
                            'label' => [
                                // Default: a label shows a dot even when its owner disables markers.
                                // Explicit false suppresses only the dot, not the label or connector.
                                'nodeEnd' => true,
                                // Default: default. Supported: half, default, halfLong, long.
                                'width' => 'default',
                                // Default depends on role: heading center, leaf left. Text alignment only.
                                'align' => 'left',
                                // Default depends on role: branch left, leaf connector right.
                                'side' => 'right',
                                // Example content; no shared text default, never inherited.
                                'text' => 'flow-while',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    // Connection variant for base tabs Parts/Paths (additional startLength).
    // Parts/Paths also support the same children hierarchy shown under deep-reference above.
    'parts' => [
        'global' => [
            // Default: inherit the connected color; empty means no explicit color.
            'color' => '',
            'connection' => [
                // Omitted: inherit the canvas arc radius (normally 2.75rem).
                'arcRadius' => '',
                // Default: 0rem.
                'startLength' => '0rem',
                // Default: connection 4rem, SubTab spine 16rem, leaf 3rem; a shared override applies to all descendants.
                'stemLength' => '4rem',
                // Default: 4rem.
                'bridgeLength' => '4rem',
            ],
            'label' => [
                // Default: true; attached labels retain their anchor.
                'nodeEnd' => true,
                // Default: true.
                'nodeEndDot' => true,
                // Default: top-bottom.
                'direction' => 'top-bottom',
                // Default depends on role: main label 1rem, SubTab label 2rem, leaf 0rem.
                'beforeLength' => '1rem',
                // Default: main label 1rem, SubTab label 2rem.
                'afterLength' => '1rem',
                // Default depends on role: heading center, leaf left. Text alignment only.
                'align' => 'center',
                // Default: default. Supported: half, default, halfLong, long.
                'width' => 'default',
                // Default: inherit the connected color; empty means no explicit color.
                'color' => '',
                // Example content; no shared text default, never inherited.
                'text' => 'Parts',
            ],
        ],
    ],
    // Terminal-tab variant: Overview/Inventory have no children and use an end-label.
    'overview' => [
        // LOCAL: affects this element only. Values below show the role defaults.
        'color' => '',
        'connection' => [
            'stemLength' => '4rem',
            'bridgeLength' => '4rem',
            'arcRadius' => '',
        ],
        'label' => [
            'text' => [
                0 => 'Overview',
            ],
            'width' => 'default',
            'align' => 'center',
            'side' => 'bottom',
        ],
        'global' => [
            // Default: inherit the connected color; empty means no explicit color.
            'color' => '',
            'connection' => [
                // Default: connection 4rem, SubTab spine 16rem, leaf 3rem; a shared override applies to all descendants.
                'stemLength' => '4rem',
                // Default: 4rem.
                'bridgeLength' => '4rem',
                // Omitted: canvas arc radius (normally 2.75rem). Empty is a schema placeholder, not a radius override.
                'arcRadius' => '',
            ],
            'label' => [
                'text' => [
                    // Default shown below.
                    0 => 'Overview',
                ],
                // Default: default. Supported: half, default, halfLong, long.
                'width' => 'default',
                // Default depends on role: heading center, leaf left. Text alignment only.
                'align' => 'center',
                // Terminal label default: bottom.
                'side' => 'bottom',
            ],
        ],
    ],
];
