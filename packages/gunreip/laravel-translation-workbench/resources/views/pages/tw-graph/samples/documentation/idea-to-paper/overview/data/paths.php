<?php

/**
 * paths: global for inherited settings; direct props for this element; named sub-sections in children.
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
// Existing children: trunk, merge, merge-extension, branch, branch-extension, branch-return, branch-return-extension, branch-return-bridge, stem-detour.
// Same subtree settings as data/template.php; no local layout overrides are required.
return [
    'paths' => [
        'global' => [
            'color' => 'rose',
            'connection' => [
                'stemLength' => '3rem',
                'bridgeLength' => '5rem',
                // 'arcRadius' => '5rem',
            ],
            'label' => [
                'width' => 'half',
                'afterLength' => '0rem',
                'nodeEnd' => false,
                // 'nodeEndDot' => true,
            ],
        ],
        'children' => [
            'global' => [],
            'trunk' => [
                'global' => [
                    'stemLength' => '4rem',
                    'bridgeLength' => '0rem',
                ],
            ],
            'merge' => [
                'global' => [
                    // 'stemLength' => '4rem',
                    'bridgeLength' => '0rem',
                ],
            ],
            'merge-extension' => [
                'global' => [
                    'stemLength' => '8rem',
                    'bridgeLength' => '3rem',
                    'side' => 'right',
                    'width' => 'default',
                ],
            ],
            'branch' => [
                'global' => [
                    'stemLength' => '8rem',
                    'bridgeLength' => '0rem',
                ],
            ],
            'branch-extension' => [
                'global' => [
                    'stemLength' => '8rem',
                    'bridgeLength' => '3rem',
                    'side' => 'right',
                    'width' => 'default',
                ],
            ],
            'branch-return' => [
                'global' => [
                    'stemLength' => '8rem',
                    'bridgeLength' => '0rem',
                ],
            ],
            'branch-return-extension' => [
                'global' => [
                    'stemLength' => '8rem',
                    'bridgeLength' => '3rem',
                    'side' => 'right',
                    'width' => 'default',
                ],
            ],
            'branch-return-bridge' => [
                'global' => [
                    'stemLength' => '16rem',
                    'bridgeLength' => '3rem',
                    'side' => 'right',
                    'width' => 'default',
                ],
            ],
            'stem-detour' => [
                'global' => [
                    'stemLength' => '4rem',
                    'bridgeLength' => '0rem',
                ],
            ],
        ],
    ],
];
