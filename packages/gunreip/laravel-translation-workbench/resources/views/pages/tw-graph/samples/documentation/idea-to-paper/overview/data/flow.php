<?php

/**
 * flow: global for inherited settings; direct props for this element; named sub-sections in children.
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
    // Flow configuration
    'flow' => [
        'global' => [
            'color' => 'purple',
            'connection' => [
                'stemLength' => '3rem',
                'bridgeLength' => '50rem',
                // 'arcRadius' => '3rem',
            ],
            'label' => [
                'width' => 'default',
                'align' => 'center',
            ],
        ],
        'label' => [
            'width' => 'half',
            // 'align' => 'center',
        ],
        // Flow children configuration
        'children' => [
            'global' => [
                'stemLength' => '7rem',
                'bridgeLength' => '1rem',
                'width' => 'half',
            ],
            // Flow start node configuration
            'start' => [
                // 'global' => [],
                'stemLength' => '3rem',
                'bridgeLength' => '7rem',
            ],
            // Flow step node configuration
            'step' => [
                // 'global' => [],
                'stemLength' => '6rem',
                'side' => 'right',
            ],
            // Flow branch steps node configuration
            'branch-steps' => [
                // 'global' => [],
                'stemLength' => '6rem',
                'bridgeLength' => '4rem',
                'label' => [
                    'width' => 'default',
                    // 'align' => 'center',
                ],
            ],
            // Flow if node configuration
            'if' => [
                'global' => [
                    'stemLength' => '3rem',
                    'label' => [
                        'align' => 'left',
                        'beforeLength' => '2rem',
                        'afterLength' => '0rem',
                    ],
                    'nodeEnd' => false,
                ],
                'side' => 'right',
                'stemLength' => '8rem',
                'bridgeLength' => '54rem',
                'label' => [
                    // 'width' => 'default',
                    'align' => 'center',
                ],
                // Flow if children configuration
                'children' => [
                    // 'global' => [],
                    // Flow if-simple node configuration
                    'simple' => [
                        // 'global' => [],
                    ],
                    // Flow if-else node configuration
                    'else' => [
                        // 'global' => [],
                    ],
                    // Flow if-elseif node configuration
                    'elseif' => [
                        // 'global' => [],
                    ],
                    // Flow if-elseif-multi node configuration
                    'elseif-multi' => [
                        // 'global' => [],
                        'label' => [
                            'width' => 'default'
                        ],
                    ],
                    // Flow if-ternary node configuration
                    'ternary' => [
                        // 'global' => [],
                    ],
                    // Flow if-nested-1 node configuration
                    'nested-1' => [
                        // 'global' => [],
                    ],
                    // Flow if-nested-2 node configuration
                    'nested-2' => [
                        // 'global' => [],
                    ],
                    // Flow if-nested-3 node configuration
                    'nested-3' => [
                        // 'global' => [],
                    ],
                    // Flow if-nested-4 node configuration
                    'nested-4' => [
                        // 'global' => [],
                    ],
                    // Flow if-nested-5 node configuration
                    'nested-5' => [
                        // 'global' => [],
                    ],
                    // Flow if-nested-6 node configuration
                    'nested-6' => [
                        // 'global' => [],
                    ],
                    // Flow if-nested-7 node configuration
                    'nested-7' => [
                        // 'global' => [],
                    ],
                    // Flow if-nested-8 node configuration
                    'nested-8' => [
                        // 'global' => [],
                    ],
                    // Flow if-nested-9 node configuration
                    'nested-9' => [
                        // 'global' => [],
                    ],
                ],
            ],
            // Flow switch-case node configuration
            'switch-case' => [
                'global' => [
                    'width' => 'default',
                ],
                'stemLength' => '6rem',
                'bridgeLength' => '53rem',
                'label' => [
                    'width' => 'default',
                    'beforeLength' => '2rem',
                    'afterLength' => '0rem',
                ],
                'nodeEnd' => false,
                // Flow switch-case children configuration
                'children' => [
                    'global' => [
                        'stemLength' => '3rem',
                        'label' => [
                            'align' => 'left',
                        ],
                    ],
                    // Flow switch-case-default node configuration
                    'default' => [
                        // 'global' => [],
                    ],
                    // Flow switch-case-grouped node configuration
                    'grouped' => [
                        // 'global' => [],
                    ],
                    // Flow switch-case-grouped-3 node configuration
                    'grouped-3' => [
                        // 'global' => [],
                    ],
                    // Flow switch-case-grouped-multi node configuration
                    'grouped-multi' => [
                        // 'global' => [],
                    ],
                    // Flow switch-case-nested node configuration
                    'nested' => [
                        // 'global' => [],
                    ],
                    // Flow switch-case-without-default node configuration
                    'without-default' => [
                        // 'global' => [],
                    ],
                    // Flow switch-case-fallthrough node configuration
                    'fallthrough' => [
                        // 'global' => [],
                    ],
                    // Flow switch-case-nested-1 node configuration
                    'nested-1' => [
                        // 'global' => [],
                    ],
                    // Flow switch-case-nested-2 node configuration
                    'nested-2' => [
                        // 'global' => [],
                    ],
                    // Flow switch-case-two-nested-1 node configuration
                    'two-nested-1' => [
                        // 'global' => [],
                        'label' => [
                            'width' => 'halfLong'
                        ],
                    ],
                    // Flow switch-case-two-nested-2 node configuration
                    'two-nested-2' => [
                        // 'global' => [],
                        'label' => [
                            'width' => 'halfLong'
                        ],
                    ],
                    // Flow switch-case-two-nested-3 node configuration
                    'two-nested-3' => [
                        // 'global' => [],
                        'label' => [
                            'width' => 'halfLong'
                        ],
                    ],
                    // Flow switch-case-action-sequence node configuration
                    'action-sequence' => [
                        // 'global' => [],
                    ],
                ],
            ],
            // Flow while node configuration
            'while' => [
                'global' => [
                    'stemLength' => '3rem',
                    'label' => [
                        'width' => 'default',
                        'align' => 'left',
                        'beforeLength' => '2rem',
                        'afterLength' => '0rem',
                    ],
                    'nodeEnd' => false,
                ],
                'bridgeLength' => '28rem',
                'side' => 'right',
                'label' => [
                    'width' => 'half',
                    'align' => 'center',
                ],
                // Flow while children configuration
                'children' => [
                    // 'global' => [],
                    // Flow while-basic node configuration
                    'basic' => [
                        // 'global' => [],
                    ],
                    // Flow while-multiple-actions node configuration
                    'multiple-actions' => [
                        // 'global' => [],
                    ],
                    // Flow while-if node configuration
                    'if' => [
                        // 'global' => [],
                    ],
                    // Flow while-switch node configuration
                    'switch' => [
                        // 'global' => [],
                    ],
                    // Flow while-nested node configuration
                    'nested' => [
                        // 'global' => [],
                    ],
                    // Flow while-independent node configuration
                    'independent' => [
                        // 'global' => [],
                    ],
                    // Flow while-mixed node configuration
                    'mixed' => [
                        // 'global' => [],
                    ],
                    // Flow while-action- sequence node configuration
                    'action-sequence' => [
                        // 'global' => [],
                        'label' => [
                            'width' => 'halfLong',
                            // 'align' => 'left',
                        ],
                    ],
                ],
            ],
            // Flow for (loop) configuration
            'for' => [
                'bridgeLength' => '26rem',
                'global' => [
                    'stemLength' => '3rem',
                    'sideways' => [
                        'extensionLength' => '19rem',
                        // 'nodeEnd' => true, // Arc exit.
                        // 'nodeEndDot' => true,
                        'extensionEnd' => [
                            'nodeEnd' => true,
                            'nodeEndDot' => false,
                        ],
                    ],
                    'label' => [
                        'width' => 'default',
                        'align' => 'left',
                        'beforeLength' => '2rem',
                        'afterLength' => '0rem',
                    ],
                    'nodeEnd' => false,
                ],
                'label' => [
                    'width' => 'half',
                    'align' => 'center',
                ],
                // Flow for (loop) children configuration
                'children' => [
                    // 'global' => [],
                    // Flow for-(loop)-basic node configuration
                    'basic' => [
                        // 'global' => [],
                    ],
                    // Flow for-(loop)-descending node configuration
                    'descending' => [
                        // 'global' => [],
                    ],
                    // Flow for-(loop)-multiple-actions node configuration
                    'multiple-actions' => [
                        // 'global' => [],
                    ],
                    // Flow for-(loop)-if node configuration
                    'if' => [
                        // 'global' => [],
                    ],
                    // Flow for-(loop)-switch node configuration
                    'switch' => [
                        // 'global' => [],
                    ],
                    // Flow for-(loop)-nested node configuration
                    'nested' => [
                        // 'global' => [],
                    ],
                    // Flow for-(loop)-independent node configuration
                    'independent' => [
                        // 'global' => [],
                    ],
                    // Flow for-(loop)-mixed node configuration
                    'mixed' => [
                        // 'global' => [],
                    ],
                    // Flow for-(loop)-action-sequence node configuration
                    'action-sequence' => [
                        // 'global' => [],
                        'label' => [
                            'width' => 'halfLong',
                            // 'align' => 'left',
                        ],
                    ],
                ],
            ],
            // Flow for-(loop)-foreach configuration
            'foreach' => [
                'side' => 'right',
                'global' => [
                    'stemLength' => '3rem',
                ],
                'bridgeLength' => '16rem',
                'label' => [
                    'width' => 'default',
                    // 'align' => 'left',
                    'beforeLength' => '2rem',
                    'afterLength' => '0rem',
                ],
                'nodeEnd' => false,
                // Flow for-(loop)-foreach children configuration
                'children' => [
                    'global' => [
                        'label' => [
                            'width' => 'default',
                            'align' => 'right',
                            'side' => 'left',
                        ],
                    ],
                    // Flow for-(loop)-collection node configuration
                    'collection' => [
                        // 'global' => [],
                    ],
                    // Flow for-(loop)-key-value node configuration
                    'key-value' => [
                        // 'global' => [],
                    ],
                    // Flow for-(loop)-nested node configuration
                    'nested' => [
                        // 'global' => [],
                    ],
                ],
            ],
            // Flow do-while (loop) configuration
            'do-while' => [
                'bridgeLength' => '14rem',
                'label' => [
                    // 'width' => 'default',
                    'align' => 'center',
                ],
                'global' => [
                    'stemLength' => '3rem',
                    'label' => [
                        'width' => 'default',
                        'align' => 'left',
                        'beforeLength' => '2rem',
                        'afterLength' => '0rem',
                    ],
                    'nodeEnd' => false,
                ],
                // Flow do-while (loop) children configuration
                'children' => [
                    // 'global' => [],
                    // Flow do-while-(loop)-basic node configuration
                    'basic' => [
                        // 'global' => [],
                    ],
                    // Flow do-while-(loop)-multiple-actions node configuration
                    'multiple-actions' => [
                        // 'global' => [],
                    ],
                    // Flow do-while-(loop)-bounded-retry node configuration
                    'bounded-retry' => [
                        // 'global' => [],
                    ],
                ],
            ],
            // Flow try-catch configuration
            'try-catch' => [
                'stemLength' => '34rem',
                'bridgeLength' => '48rem',
                'side' => 'right',
                'global' => [
                    'stemLength' => '3rem',
                    'label' => [
                        'width' => 'default',
                        'align' => 'left',
                        'beforeLength' => '2rem',
                        'afterLength' => '0rem',
                    ],
                    'nodeEnd' => false,
                ],
                'label' => [
                    // 'width' => 'default',
                    'align' => 'center',
                ],
                // Flow try-catch children configuration
                'children' => [
                    // 'global' => [],
                    // Flow try-catch-basic node configuration
                    'basic' => [
                        'global' => []
                    ],
                    // Flow try-catch-finally node configuration
                    'finally' => [
                        // 'global' => [],
                    ],
                    // Flow try-catch-multiple node configuration
                    'multiple' => [
                        // 'global' => [],
                    ],
                    // Flow try-catch-if-try node configuration
                    'if-try' => [
                        // 'global' => [],
                    ],
                    // Flow try-catch-try-if-finally node configuration
                    'try-if-finally' => [
                        // 'global' => [],
                    ],
                    // Flow try-catch-foreach-try node configuration
                    'foreach-try' => [
                        // 'global' => [],
                    ],
                    // Flow try-catch-try-foreach node configuration
                    'try-foreach' => [
                        // 'global' => [],
                    ],
                    'while-try-finally' => [
                        // 'global' => [],
                        'label' => [
                            'width' => 'halfLong',
                            // 'align' => 'left',
                        ],
                    ],
                ],
            ],
            // Flow break-continuation configuration
            'break-continue' => [
                'stemLength' => '3rem',
                'bridgeLength' => '26rem',
                'side' => 'right',
                'global' => [
                    'stemLength' => '3rem',
                    'label' => [
                        'width' => 'default',
                        'align' => 'left',
                        'beforeLength' => '2rem',
                        'afterLength' => '0rem',
                    ],
                    'nodeEnd' => false,
                ],
                'label' => [
                    // 'width' => 'halfLong',
                    'align' => 'center',
                ],
                // Flow break-continuation children configuration
                'children' => [
                    // 'global' => [],
                    // Flow break-continuation-break-while node configuration
                    'break-while' => [
                        // 'global' => [],
                    ],
                    // Flow break-continuation-continue-while node configuration
                    'continue-while' => [
                        // 'global' => [],
                    ],
                    // Flow break-continuation-continue-for node configuration
                    'continue-for' => [
                        // 'global' => [],
                    ],
                    // Flow break-continuation-continue-nested node configuration
                    'continue-nested' => [
                        // 'global' => [],
                    ],
                    // Flow break-continuation-break-nested node configuration
                    'break-nested' => [
                        // 'global' => [],
                    ],
                    // Flow break-continuation-break-finally node configuration
                    'break-finally' => [
                        // 'global' => [],
                    ],
                    // Flow break-continuation-break-finally-2 node configuration
                    'break-finally-2' => [
                        // 'global' => [],
                    ],
                    // Flow break-continuation-continue-finally node configuration
                    'continue-finally' => [
                        // 'global' => [],
                    ],
                    // Flow break-continuation-continue-finally-2 node configuration
                    'continue-finally-2' => [
                        // 'global' => [],
                    ],
                ],
            ],
            // Flow return configuration
            'return' => [
                'stemLength' => '3rem',
                'bridgeLength' => '4rem',
                'global' => [
                    'stemLength' => '3rem',
                    'label' => [
                        'width' => 'default',
                        'align' => 'left',
                        'beforeLength' => '2rem',
                        'afterLength' => '0rem',
                    ],
                    'nodeEnd' => false,
                ],
                'side' => 'right',
                'label' => [
                    'width' => 'half',
                    'align' => 'center',
                ],
                // Flow return children configuration
                'children' => [
                    // 'global' => [],
                    // Flow return-guard node configuration
                    'guard' => [
                        // 'global' => [],
                    ],
                    // Flow return-multiple-guards node configuration
                    'multiple-guards' => [
                        // 'global' => [],
                    ],
                    // Flow return-loop node configuration
                    'loop' => [
                        // 'global' => [],
                    ],
                    // Flow return-nested node configuration
                    'nested' => [
                        // 'global' => [],
                    ],
                    // Flow return-finally node configuration
                    'finally' => [
                        // 'global' => [],
                    ],
                    // Flow return-void node configuration
                    'void' => [
                        // 'global' => [],
                    ],
                    // Flow return-test node configuration
                    'test' => [
                        // 'global' => [],
                    ],
                ],
            ],
            // Flow throw configuration
            'throw' => [
                'stemLength' => '16rem',
                'bridgeLength' => '32rem',
                'global' => [
                    'stemLength' => '3rem',
                    'side' => 'left',
                    'label' => [
                        'width' => 'default',
                        'align' => 'right',
                        'beforeLength' => '2rem',
                        'afterLength' => '0rem',
                    ],
                    'nodeEnd' => false,
                ],
                'label' => [
                    // 'width' => 'halfLong',
                    'align' => 'center',
                ],
                // Flow return children configuration
                'children' => [
                    // 'global' => [],
                    // Flow throw-catch node configuration
                    'catch' => [
                        // 'global' => [],
                    ],
                    // Flow throw-propagation node configuration
                    'propagation' => [
                        // 'global' => [],
                    ],
                    // Flow throw-rethrow node configuration
                    'rethrow' => [
                        // 'global' => [],
                    ],
                    // Flow throw-finally node configuration
                    'finally' => [
                        // 'global' => [],
                    ],
                    // Flow throw-finally-return node configuration
                    'finally-return' => [
                        // 'global' => [],
                        'label' => [
                            // 'side' => 'left',
                            'width' => 'halfLong',
                        ],
                    ],
                    // Flow throw-finally-exception node configuration
                    'finally-exception' => [
                        // 'global' => [],
                        'label' => [
                            // 'side' => 'left',
                            'width' => 'halfLong',
                        ],
                    ],
                    // Flow throw-return-finally node configuration
                    'return-finally' => [
                        // 'global' => [],
                    ],
                    // Flow throw-unhandled node configuration
                    'unhandled' => [
                        // 'global' => [],
                    ],
                    // Flow throw-foreach node configuration
                    'foreach' => [
                        // 'global' => [],
                    ],
                    // Flow throw-foreach-catch node configuration
                    'foreach-catch' => [
                        // 'global' => [],
                    ],
                ],
            ],
            // Flow function configuration
            'function' => [
                'stemLength' => '8rem',
                'bridgeLength' => '20rem',
                'global' => [
                    'stemLength' => '3rem',
                    'label' => [
                        // 'side' => 'left',
                        'width' => 'halfLong',
                        'align' => 'left',
                        'beforeLength' => '2rem',
                        'afterLength' => '0rem',
                    ],
                    'nodeEnd' => false,
                ],
                'label' => [
                    // 'side' => 'left',
                    'width' => 'default',
                    'align' => 'center',
                ],
                // Flow throw children configuration
                'children' => [
                    // 'global' => [],
                    // Flow throw-basic node configuration
                    'basic' => [
                        // 'global' => [],
                    ],
                    // Flow throw-arguments node configuration
                    'arguments' => [
                        // 'global' => [],
                    ],
                    // Flow throw-void node configuration
                    'void' => [
                        // 'global' => [],
                    ],
                    // Flow throw-nested node configuration
                    'nested' => [
                        // 'global' => [],
                        'label' => [
                            'width' => 'default',
                        ],
                    ],
                    // Flow throw-multiple node configuration
                    'multiple' => [
                        // 'global' => [],
                        'label' => [
                            // 'side' => 'left',
                            'width' => 'default',
                            // 'align' => 'left',
                        ],
                    ],
                    // Flow throw-recursion node configuration
                    'recursion' => [
                        // 'global' => [],
                        'label' => [
                            'width' => 'default',
                        ],
                    ],
                ],
            ],
            // Flow callback configuration
            'callback' => [
                'stemLength' => '16rem',
                'bridgeLength' => '36rem',
                'global' => [
                    'stemLength' => '3rem',
                    'label' => [
                        'width' => 'default',
                        'align' => 'left',
                    ],
                ],
                'label' => [
                    // 'width' => 'default',
                    'align' => 'center',
                    'beforeLength' => '2rem',
                    'afterLength' => '0rem',
                ],
                'nodeEnd' => false,
                'side' => 'right',
                // Flow callback children configuration
                'children' => [
                    // 'global' => [],
                    // Flow callback-basic node configuration
                    'basic' => [
                        // 'global' => [],
                    ],
                    // Flow callback-interchangeable node configuration
                    'interchangeable' => [
                        // 'global' => [],
                    ],
                    // Flow callback-loop node configuration
                    'loop' => [
                        // 'global' => [],
                    ],
                ],
            ],
            // Flow async-await configuration
            'async-await' => [
                'stemLength' => '3rem',
                'bridgeLength' => '6rem',
                'side' => 'right',
                'global' => [
                    'stemLength' => '3rem',
                    'label' => [
                        'width' => 'default',
                        'align' => 'left',
                    ],
                ],
                'label' => [
                    // 'width' => 'default',
                    'align' => 'center',
                    'beforeLength' => '2rem',
                    'afterLength' => '0rem',
                ],
                'nodeEnd' => false,
                // Flow async-await children configuration
                'children' => [
                    'global' => [
                        'stemLength' => '3rem',
                    ],
                    // Flow async-await-basic node configuration
                    'basic' => [
                        // 'global' => [],
                    ],
                    // Flow async-await-sequential node configuration
                    'sequential' => [
                        // 'global' => [],
                    ],
                    // Flow async-await-concurrent node configuration
                    'concurrent' => [
                        // 'global' => [],
                    ],
                    // Flow async-await-finally node configuration
                    'finally' => [
                        // 'global' => [],
                        'label' => [
                            'width' => 'halfLong',
                            // 'align' => 'center',
                        ],
                    ],
                    // Flow async-await-loop node configuration
                    'loop' => [
                        // 'global' => [],
                    ],
                    // Flow async-await-cancellation node configuration
                    'cancellation' => [
                        // 'global' => [],
                    ],
                    // Flow async-await-timeout node configuration
                    'timeout' => [
                        // 'global' => [],
                    ],
                    // Flow async-await-partial node configuration
                    'partial' => [
                        // 'global' => [],
                    ],
                    // Flow async-await-first node configuration
                    'first' => [
                        // 'global' => [],
                        'label' => [
                            'width' => 'halfLong',
                            // 'align' => 'center',
                        ],
                    ],
                    // Flow async-await-limited node configuration
                    'limited' => [
                        // 'global' => [],
                    ],
                    // Flow async-await-retry node configuration
                    'retry' => [
                        // 'global' => [],
                    ],
                    // Flow async-await-group-cleanup node configuration
                    'group-cleanup' => [
                        // 'global' => [],
                        'label' => [
                            'width' => 'halfLong',
                            // 'align' => 'center',
                        ],
                    ],
                    // Flow async-await-stream node configuration
                    'stream' => [
                        // 'global' => [],
                    ],
                    // Flow async-await-retry-deadline node configuration
                    'retry-deadline' => [
                        // 'global' => [],
                    ],
                ],
            ],
        ],
    ],
];
