<?php

/**
 * Global canvas/root/main-label defaults only. Individual sections live in their
 * named files. Optional shape:
 * 'global' => [
 *     'canvas' => ['minWidth' => '48rem', 'minHeight' => '64rem', 'horizontalPadding' => '3rem'],
 *     'root' => ['direction' => 'top-bottom', 'stemLength' => '4rem', 'color' => 'zinc',
 *         'label' => ['text' => [__('Idea to Paper')], 'side' => 'bottom', 'align' => 'center']],
 *     'labelDefaults' => ['direction' => 'top-bottom', 'beforeLength' => '1rem',
 *         'afterLength' => '1rem', 'width' => 'default', 'align' => 'center', 'color' => ''],
 * ],
 * Only supplied fields override defaults. Root label width/extra canvas props are
 * not exposed. Unknown fields/types produce a mismatch; no legacy aliases exist.
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
    'global' => [
        // Overall traversal: root and both main merge chains use this direction.
        'root' => [
            'direction' => 'top-bottom',
        ],
    ],
];
