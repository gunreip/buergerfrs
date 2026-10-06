<?php

declare(strict_types=1);

namespace Gunreip\TranslationWorkbench\Support\TwGraph\Documentation;

/**
 * Authored data for the Overview only. Other documentation examples remain handmade.
 * Keys form stable component IDs; order controls rendering and attachment order.
 * Layout comes from OverviewLayoutDefaults; authored exceptions belong in overview/data/*.php.
 */
final class OverviewStructure
{
    public static function data(): array
    {
        return [
            'canvas' => [
                'graphId' => 'idea-to-paper-overview-structure',
                ...OverviewLayoutDefaults::canvas(),
            ],
            'root' => [
                'id' => 'literature.overview.start',
                ...OverviewLayoutDefaults::root(),
                'label' => [
                    'text' => [__('Idea to Paper')],
                    'side' => 'bottom',
                    'align' => 'center',
                ],
            ],
            'merges' => [
                'left' => [
                    ...OverviewLayoutDefaults::merge(),
                    'id' => 'literature.overview.tabs.left',
                    'attachTo' => 'literature.overview.start.anchorNode-end',
                    'nodeLabels' => [
                        'start' => false,
                    ],
                    'extensionCount' => 6,
                    'extensionNodeLabels' => [
                        1 => [
                            'start' => false,
                        ],
                        2 => [
                            'start' => false,
                        ],
                        3 => [
                            'start' => false,
                        ],
                        4 => [
                            'start' => false,
                        ],
                    ],
                    'extensionEndLabels' => [
                        5 => [
                            'text' => [__('Overview')],
                            'width' => 'default',
                            'align' => 'center',
                            'side' => 'bottom',
                        ],
                        6 => [
                            'text' => [__('Inventory')],
                            'width' => 'default',
                            'align' => 'center',
                            'side' => 'bottom',
                        ],
                    ],
                ],
                'right' => [
                    ...OverviewLayoutDefaults::merge(),
                    'id' => 'literature.overview.tabs.right',
                    'attachTo' => 'literature.overview.start.anchorNode-end',
                    'nodeLabels' => [
                        'start' => false,
                    ],
                    'extensionCount' => 5,
                    'extensionNodeLabels' => [
                        1 => [
                            'start' => false,
                        ],
                        2 => [
                            'start' => false,
                        ],
                        3 => [
                            'start' => false,
                        ],
                        4 => [
                            'start' => false,
                        ],
                        5 => [
                            'start' => false,
                        ],
                    ],
                ],
            ],
            'tabLayout' => OverviewLayoutDefaults::tab(),
            // Empty color inherits from the connected anchor; explicit colors start a new run.
            'tabs' => [
                'parts' => ['text' => __('Parts'), 'attachTo' => 'strang.merge-left.node.1'],
                'segments' => ['text' => __('Segments'), 'attachTo' => 'strang.merge-left.extension.1.node.1'],
                'primitives' => ['text' => __('Primitives'), 'attachTo' => 'strang.merge-left.extension.2.node.1'],
                'canvas' => ['text' => __('Canvas'), 'attachTo' => 'strang.merge-left.extension.3.node.1'],
                'deep-reference' => ['text' => __('Deep Reference'), 'attachTo' => 'strang.merge-left.extension.4.node.1'],
                'paths' => ['text' => __('Paths'), 'attachTo' => 'strang.merge-right.node.1'],
                'strang-trunk' => ['text' => __('Strang Trunk'), 'attachTo' => 'strang.merge-right.extension.1.node.1'],
                'strang-merge' => ['text' => __('Strang Merge'), 'attachTo' => 'strang.merge-right.extension.2.node.1'],
                'strang-branch' => ['text' => __('Strang Branch'), 'attachTo' => 'strang.merge-right.extension.3.node.1'],
                'strang-rekey' => ['text' => __('Strang Rekey'), 'attachTo' => 'strang.merge-right.extension.4.node.1'],
                'flow' => ['text' => __('Flow'), 'attachTo' => 'strang.merge-right.extension.5.node.1'],
            ],
            'canvasTabs' => [
                'attachTo' => 'literature.overview.canvas.anchorNode-end',
                // The entire SubTab level uses groups, including entries without children.
                'levels' => OverviewLayoutDefaults::levels(),
                'children' => [
                    'default' => [
                        'id' => 'literature.overview.canvas.tabs.default', 'text' => __('Default'),
                        'children' => [],
                    ],
                    'borders' => [
                        'id' => 'literature.overview.canvas.tabs.borders', 'text' => __('Borders'),
                        'children' => [],
                    ],
                    'default-trunk' => [
                        'id' => 'literature.overview.canvas.tabs.default-trunk', 'text' => __('Default + trunk'),
                        'children' => [],
                    ],
                    'coordinates' => [
                        'id' => 'literature.overview.canvas.tabs.coordinates', 'text' => __('Canvas + coord'),
                        'children' => [],
                    ],
                    'height' => [
                        'id' => 'literature.overview.canvas.tabs.height', 'text' => __('Canvas height'),
                        'children' => [],
                    ],
                    'props' => [
                        'id' => 'literature.overview.canvas.props', 'text' => __('Canvas + props'),
                        'children' => [
                            'line' => ['text' => __('Line width')],
                            'stem-length' => ['text' => __('Stem length')],
                            'node-size' => ['text' => __('Node size')],
                            'cap-length' => ['text' => __('Cap length')],
                            'min-width' => ['text' => __('Min width')],
                        ],
                    ],
                ],
            ],
            'primitivesTabs' => [
                'attachTo' => 'literature.overview.primitives.anchorNode-end',
                'levels' => OverviewLayoutDefaults::levels(),
                'children' => [
                    'line' => [
                        'id' => 'literature.overview.primitives.tabs.line', 'text' => __('Line'),
                        'children' => [],
                    ],
                    'arc' => [
                        'id' => 'literature.overview.primitives.tabs.arc', 'text' => __('Arc'),
                        'children' => [],
                    ],
                    'line-jump' => [
                        'id' => 'literature.overview.primitives.tabs.line-jump', 'text' => __('Line jump'),
                        'children' => [],
                    ],
                    'text-label' => [
                        'id' => 'literature.overview.primitives.tabs.text-label', 'text' => __('Text Label'),
                        'children' => [],
                    ],
                    'markers-connectors' => [
                        'id' => 'literature.overview.primitives.markers-connectors', 'text' => __('Markers & Connectors'),
                        'children' => [
                            'node' => ['text' => __('Node')],
                            'joint-arrow' => ['text' => __('Joint Arrow')],
                            'connector' => ['text' => __('Connector')],
                        ],
                    ],
                ],
            ],
            'segmentsTabs' => [
                'attachTo' => 'literature.overview.segments.anchorNode-end',
                'levels' => OverviewLayoutDefaults::levels(),
                'children' => [
                    'path' => [
                        'id' => 'literature.overview.segments.tabs.path', 'text' => __('Path'),
                        'children' => [],
                    ],
                    'start-end' => [
                        'id' => 'literature.overview.segments.tabs.start-end', 'text' => __('Start + End'),
                        'children' => [],
                    ],
                    'arc' => [
                        'id' => 'literature.overview.segments.tabs.arc', 'text' => __('Arc'),
                        'children' => [],
                    ],
                    'labels' => [
                        'id' => 'literature.overview.segments.tabs.labels', 'text' => __('Labels'),
                        'children' => [],
                    ],
                    'step' => [
                        'id' => 'literature.overview.segments.tabs.step', 'text' => __('Step'),
                        'children' => [],
                    ],
                    'stem-compressed' => [
                        'id' => 'literature.overview.segments.tabs.stem-compressed', 'text' => __('Stem compressed'),
                        'children' => [],
                    ],
                    'fusion' => [
                        'id' => 'literature.overview.segments.tabs.fusion', 'text' => __('Fusion'),
                        'children' => [],
                    ],
                ],
            ],
            'partsTabs' => [
                'attachTo' => 'literature.overview.parts.anchorNode-end',
                'levels' => OverviewLayoutDefaults::levels(),
                'children' => [
                    'start' => [
                        'id' => 'literature.overview.parts.tabs.start', 'text' => __('Start'),
                        'children' => [],
                    ],
                    'end' => [
                        'id' => 'literature.overview.parts.tabs.end', 'text' => __('End'),
                        'children' => [],
                    ],
                    'sideways' => [
                        'id' => 'literature.overview.parts.tabs.sideways', 'text' => __('Sideways'),
                        'children' => [],
                    ],
                    'chain' => [
                        'id' => 'literature.overview.parts.tabs.chain', 'text' => __('Chain'),
                        'children' => [],
                    ],
                    'fusion' => [
                        'id' => 'literature.overview.parts.tabs.fusion', 'text' => __('Fusion'),
                        'children' => [],
                    ],
                ],
            ],
            'pathsTabs' => [
                'attachTo' => 'literature.overview.paths.anchorNode-end',
                'levels' => OverviewLayoutDefaults::levels(),
                'children' => [
                    'trunk' => [
                        'id' => 'literature.overview.paths.tabs.trunk', 'text' => __('Trunk'),
                        'children' => [],
                    ],
                    'merge' => [
                        'id' => 'literature.overview.paths.tabs.merge', 'text' => __('Merge'),
                        'children' => [],
                    ],
                    'merge-extension' => [
                        'id' => 'literature.overview.paths.tabs.merge-extension', 'text' => __('Merge extension'),
                        'children' => [],
                    ],
                    'branch' => [
                        'id' => 'literature.overview.paths.tabs.branch', 'text' => __('Branch'),
                        'children' => [],
                    ],
                    'branch-extension' => [
                        'id' => 'literature.overview.paths.tabs.branch-extension', 'text' => __('Branch extension'),
                        'children' => [],
                    ],
                    'branch-return' => [
                        'id' => 'literature.overview.paths.tabs.branch-return', 'text' => __('Branch return'),
                        'children' => [],
                    ],
                    'branch-return-extension' => [
                        'id' => 'literature.overview.paths.tabs.branch-return-extension', 'text' => __('Branch return extension'),
                        'children' => [],
                    ],
                    'branch-return-bridge' => [
                        'id' => 'literature.overview.paths.tabs.branch-return-bridge', 'text' => __('Branch return bridge'),
                        'children' => [],
                    ],
                    'stem-detour' => [
                        'id' => 'literature.overview.paths.tabs.stem-detour', 'text' => __('Stem detour'),
                        'children' => [],
                    ],
                ],
            ],
            'strangTrunkTabs' => [
                'attachTo' => 'literature.overview.strang-trunk.anchorNode-end',
                'levels' => OverviewLayoutDefaults::levels(),
                'children' => [
                    'default' => [
                        'id' => 'literature.overview.strang-trunk.tabs.default', 'text' => __('Default'),
                        'children' => [],
                    ],
                    'stem-count' => [
                        'id' => 'literature.overview.strang-trunk.tabs.stem-count', 'text' => __('Stem count'),
                        'children' => [],
                    ],
                    'stem-lengths' => [
                        'id' => 'literature.overview.strang-trunk.tabs.stem-lengths', 'text' => __('Stem lengths'),
                        'children' => [],
                    ],
                    'directions' => [
                        'id' => 'literature.overview.strang-trunk.tabs.directions', 'text' => __('Direction'),
                        'children' => [],
                    ],
                    'start' => [
                        'id' => 'literature.overview.strang-trunk.tabs.start', 'text' => __('Start'),
                        'children' => [
                            'overview' => ['text' => __('Overview')],
                            'compare' => ['text' => __('Start compare')],
                            'default' => ['text' => __('Start default')],
                            'long-start' => ['text' => __('Start long start')],
                            'wide-labels' => ['text' => __('Start wide labels')],
                            'spacing' => ['text' => __('Start spacing')],
                            'colors' => ['text' => __('Start colors')],
                        ],
                    ],
                    'start-shift' => [
                        'id' => 'literature.overview.strang-trunk.tabs.start-shift', 'text' => __('Start shift'),
                        'children' => [],
                    ],
                    'end' => [
                        'id' => 'literature.overview.strang-trunk.tabs.end', 'text' => __('End'),
                        'children' => [
                            'overview' => ['text' => __('Overview')],
                            'default' => ['text' => __('End default')],
                            'long-end' => ['text' => __('End long end')],
                            'wide-label' => ['text' => __('End wide label')],
                            'cap' => ['text' => __('End cap')],
                            'colors' => ['text' => __('End color')],
                        ],
                    ],
                ],
            ],
            'strangMergeTabs' => [
                'attachTo' => 'literature.overview.strang-merge.anchorNode-end',
                'levels' => OverviewLayoutDefaults::levels(),
                'children' => [
                    'default' => [
                        'id' => 'literature.overview.strang-merge.tabs.default', 'text' => __('Default'),
                        'children' => [],
                    ],
                    'start' => [
                        'id' => 'literature.overview.strang-merge.tabs.start', 'text' => __('Merge start'),
                        'children' => [],
                    ],
                    'mismatch' => [
                        'id' => 'literature.overview.strang-merge.tabs.mismatch', 'text' => __('Merge mismatch'),
                        'children' => [],
                    ],
                    'extension' => [
                        'id' => 'literature.overview.strang-merge.tabs.extension', 'text' => __('Extension'),
                        'children' => [],
                    ],
                    'aggregated' => [
                        'id' => 'literature.overview.strang-merge.tabs.aggregated', 'text' => __('Aggregated'),
                        'children' => [],
                    ],
                ],
            ],
            'strangBranchTabs' => [
                'attachTo' => 'literature.overview.strang-branch.anchorNode-end',
                'levels' => OverviewLayoutDefaults::levels(),
                'children' => [
                    'default' => [
                        'id' => 'literature.overview.strang-branch.tabs.default', 'text' => __('Default'),
                        'children' => [],
                    ],
                    'offset' => [
                        'id' => 'literature.overview.strang-branch.tabs.offset', 'text' => __('Offset'),
                        'children' => [],
                    ],
                    'step' => [
                        'id' => 'literature.overview.strang-branch.tabs.step', 'text' => __('Step'),
                        'children' => [],
                    ],
                    'continuation' => [
                        'id' => 'literature.overview.strang-branch.tabs.continuation', 'text' => __('Continuation'),
                        'children' => [],
                    ],
                    'return' => [
                        'id' => 'literature.overview.strang-branch.tabs.return', 'text' => __('Return'),
                        'children' => [],
                    ],
                    'mismatch' => [
                        'id' => 'literature.overview.strang-branch.tabs.mismatch', 'text' => __('Mismatch'),
                        'children' => [],
                    ],
                ],
            ],
            'strangRekeyTabs' => [
                'attachTo' => 'literature.overview.strang-rekey.anchorNode-end',
                'levels' => OverviewLayoutDefaults::levels(),
                'children' => [
                    'default' => [
                        'id' => 'literature.overview.strang-rekey.tabs.default', 'text' => __('Default'),
                        'children' => [],
                    ],
                    'source' => [
                        'id' => 'literature.overview.strang-rekey.tabs.source', 'text' => __('Source'),
                        'children' => [],
                    ],
                    'target' => [
                        'id' => 'literature.overview.strang-rekey.tabs.target', 'text' => __('Target'),
                        'children' => [],
                    ],
                    'compressed' => [
                        'id' => 'literature.overview.strang-rekey.tabs.compressed', 'text' => __('Compressed'),
                        'children' => [],
                    ],
                ],
            ],
            'flowTabs' => [
                'attachTo' => 'literature.overview.flow.anchorNode-end',
                'levels' => OverviewLayoutDefaults::levels(),
                'children' => [
                    'start' => [
                        'id' => 'literature.overview.flow.tabs.start', 'text' => __('Flow start'),
                        'children' => [],
                    ],
                    'step' => [
                        'id' => 'literature.overview.flow.tabs.step', 'text' => __('Flow step'),
                        'children' => [],
                    ],
                    'branch-steps' => [
                        'id' => 'literature.overview.flow.tabs.branch-steps', 'text' => __('Flow branch steps'),
                        'children' => [],
                    ],
                    'if' => [
                        'id' => 'literature.overview.flow.tabs.if', 'text' => __('Flow IF'),
                        'children' => [
                            'simple' => ['text' => __('IF')],
                            'else' => ['text' => __('IF ELSE')],
                            'elseif' => ['text' => __('IF ELSEIF')],
                            'elseif-multi' => ['text' => __('IF ELSEIF multi')],
                            'ternary' => ['text' => __('IF ternär')],
                            'nested-1' => ['text' => __('IF nested 1')],
                            'nested-2' => ['text' => __('IF nested 2')],
                            'nested-3' => ['text' => __('IF nested 3')],
                            'nested-4' => ['text' => __('IF nested 4')],
                            'nested-5' => ['text' => __('IF nested 5')],
                            'nested-6' => ['text' => __('IF nested 6')],
                            'nested-7' => ['text' => __('IF nested 7')],
                            'nested-8' => ['text' => __('IF nested 8')],
                            'nested-9' => ['text' => __('IF nested 9')],
                        ],
                    ],
                    'switch-case' => [
                        'id' => 'literature.overview.flow.tabs.switch-case', 'text' => __('Flow SWITCH/CASE'),
                        'children' => [
                            'default' => ['text' => __('CASEs single')],
                            'grouped' => ['text' => __('CASEs grouped')],
                            'grouped-3' => ['text' => __('CASE grouped (3)')],
                            'grouped-multi' => ['text' => __('CASE grouped (>4)')],
                            'nested' => ['text' => __('CASE nested')],
                            'without-default' => ['text' => __('CASEs without DEFAULT')],
                            'fallthrough' => ['text' => __('CASE fallthrough')],
                            'nested-1' => ['text' => __('CASE SWITCH CASE (1)')],
                            'nested-2' => ['text' => __('CASE SWITCH CASE (2)')],
                            'two-nested-1' => ['text' => __('2 nested CASEs in SWITCH (1)')],
                            'two-nested-2' => ['text' => __('2 nested CASEs in SWITCH (2)')],
                            'two-nested-3' => ['text' => __('2 nested CASEs in SWITCH (3)')],
                            'action-sequence' => ['text' => __('Action → Nested → Action')],
                        ],
                    ],
                    'while' => [
                        'id' => 'literature.overview.flow.tabs.while', 'text' => __('Flow WHILE'),
                        'children' => [
                            'basic' => ['text' => __('WHILE basic')],
                            'multiple-actions' => ['text' => __('WHILE multiple actions')],
                            'if' => ['text' => __('WHILE with IF')],
                            'switch' => ['text' => __('WHILE with SWITCH')],
                            'nested' => ['text' => __('Nested WHILE')],
                            'independent' => ['text' => __('Two independent inner loops')],
                            'mixed' => ['text' => __('Mixed sides and crossings')],
                            'action-sequence' => ['text' => __('Action → Nested WHILE → Action')],
                        ],
                    ],
                    'for' => [
                        'id' => 'literature.overview.flow.tabs.for', 'text' => __('Flow FOR'),
                        'children' => [
                            'basic' => ['text' => __('FOR basic')],
                            'descending' => ['text' => __('FOR descending')],
                            'multiple-actions' => ['text' => __('FOR multiple actions')],
                            'if' => ['text' => __('FOR with IF')],
                            'switch' => ['text' => __('FOR with SWITCH')],
                            'nested' => ['text' => __('Nested FOR')],
                            'independent' => ['text' => __('Two independent inner loops')],
                            'mixed' => ['text' => __('Mixed sides and crossings')],
                            'action-sequence' => ['text' => __('Action → Nested FOR → Action')],
                        ],
                    ],
                    'foreach' => [
                        'id' => 'literature.overview.flow.tabs.foreach', 'text' => __('Flow FOREACH'),
                        'children' => [
                            'collection' => ['text' => __('Collection')],
                            'key-value' => ['text' => __('Key / Value')],
                            'nested' => ['text' => __('Nested FOREACH')],
                        ],
                    ],
                    'do-while' => [
                        'id' => 'literature.overview.flow.tabs.do-while', 'text' => __('Flow DO WHILE'),
                        'children' => [
                            'basic' => ['text' => __('DO WHILE basic')],
                            'multiple-actions' => ['text' => __('Multiple actions')],
                            'bounded-retry' => ['text' => __('Bounded retry')],
                        ],
                    ],
                    'try-catch' => [
                        'id' => 'literature.overview.flow.tabs.try-catch', 'text' => __('Flow TRY/CATCH/FINALLY'),
                        'children' => [
                            'basic' => ['text' => __('TRY / CATCH')],
                            'finally' => ['text' => __('TRY / CATCH / FINALLY')],
                            'multiple' => ['text' => __('Multiple CATCH clauses')],
                            'if-try' => ['text' => __('IF → TRY/CATCH')],
                            'try-if-finally' => ['text' => __('TRY → IF/ELSE → FINALLY')],
                            'foreach-try' => ['text' => __('FOREACH → TRY/CATCH')],
                            'try-foreach' => ['text' => __('TRY → FOREACH → CATCH')],
                            'while-try-finally' => ['text' => __('WHILE → TRY/CATCH/FINALLY')],
                        ],
                    ],
                    'break-continue' => [
                        'id' => 'literature.overview.flow.tabs.break-continue', 'text' => __('Flow BREAK / CONTINUE'),
                        'children' => [
                            'break-while' => ['text' => __('BREAK in WHILE')],
                            'continue-while' => ['text' => __('CONTINUE in WHILE')],
                            'continue-for' => ['text' => __('CONTINUE in FOR')],
                            'continue-nested' => ['text' => __('CONTINUE in nested WHILE')],
                            'break-nested' => ['text' => __('BREAK in nested WHILE')],
                            'break-finally' => ['text' => __('BREAK with FINALLY (1)')],
                            'break-finally-2' => ['text' => __('BREAK with FINALLY (2)')],
                            'continue-finally' => ['text' => __('CONTINUE with FINALLY (1)')],
                            'continue-finally-2' => ['text' => __('CONTINUE with FINALLY (2)')],
                        ],
                    ],
                    'return' => [
                        'id' => 'literature.overview.flow.tabs.return', 'text' => __('Flow RETURN'),
                        'children' => [
                            'guard' => ['text' => __('Guard clause')],
                            'multiple-guards' => ['text' => __('Multiple guards')],
                            'loop' => ['text' => __('RETURN from a loop')],
                            'nested' => ['text' => __('RETURN from nested loops')],
                            'finally' => ['text' => __('RETURN with FINALLY')],
                            'void' => ['text' => __('RETURN without a value')],
                            'test' => ['text' => __('RETURN Test')],
                        ],
                    ],
                    'throw' => [
                        'id' => 'literature.overview.flow.tabs.throw', 'text' => __('Flow THROW / RETHROW'),
                        'children' => [
                            'catch' => ['text' => __('THROW to matching CATCH')],
                            'propagation' => ['text' => __('Propagation to outer CATCH')],
                            'rethrow' => ['text' => __('RETHROW after logging')],
                            'finally' => ['text' => __('FINALLY during propagation')],
                            'finally-return' => ['text' => __('THROW in FINALLY replaces RETURN')],
                            'finally-exception' => ['text' => __('THROW in FINALLY replaces an exception')],
                            'return-finally' => ['text' => __('RETURN inside FINALLY')],
                            'unhandled' => ['text' => __('Unhandled exception')],
                            'foreach' => ['text' => __('THROW inside FOREACH')],
                            'foreach-catch' => ['text' => __('CATCH inside FOREACH')],
                        ],
                    ],
                    'function' => [
                        'id' => 'literature.overview.flow.tabs.function', 'text' => __('Flow FUNCTION'),
                        'children' => [
                            'basic' => ['text' => __('Function call and return value')],
                            'arguments' => ['text' => __('Arguments and local variables')],
                            'void' => ['text' => __('Function without return value')],
                            'nested' => ['text' => __('Nested function calls')],
                            'multiple' => ['text' => __('Multiple call sites')],
                            'recursion' => ['text' => __('Recursion with a base case')],
                        ],
                    ],
                    'callback' => [
                        'id' => 'literature.overview.flow.tabs.callback', 'text' => __('Flow CALLBACK'),
                        'children' => [
                            'basic' => ['text' => __('Pass and invoke a callback')],
                            'interchangeable' => ['text' => __('Interchangeable callbacks')],
                            'loop' => ['text' => __('Callback inside a loop')],
                        ],
                    ],
                    'async-await' => [
                        'id' => 'literature.overview.flow.tabs.async-await', 'text' => __('Flow ASYNC/AWAIT'),
                        'children' => [
                            'basic' => ['text' => __('Await one operation')],
                            'sequential' => ['text' => __('Sequential awaits')],
                            'concurrent' => ['text' => __('Concurrent operations')],
                            'finally' => ['text' => __('Await with TRY/CATCH/FINALLY')],
                            'loop' => ['text' => __('Await inside a loop')],
                            'cancellation' => ['text' => __('Cancellation')],
                            'timeout' => ['text' => __('Timeout')],
                            'partial' => ['text' => __('Partial success')],
                            'first' => ['text' => __('First completion / First success')],
                            'limited' => ['text' => __('Limited concurrency')],
                            'retry' => ['text' => __('Retry with delay')],
                            'group-cleanup' => ['text' => __('Failure → Cancel remaining → Cleanup')],
                            'stream' => ['text' => __('Async iteration / Stream')],
                            'retry-deadline' => ['text' => __('Retry with total deadline')],
                        ],
                    ],
                ],
            ],
            'deepReference' => [
                'attachTo' => 'literature.overview.deep-reference.anchorNode-end',
                'levels' => OverviewLayoutDefaults::levels(),
                'children' => [
                    'strang' => [
                        'id' => 'literature.overview.deep-reference.strang',
                        'text' => __('Strang'),
                        'children' => [
                            'flow-switch-case' => ['text' => 'flow-switch-case'],
                            'flow-start' => ['text' => 'flow-start'],
                            'flow-step' => ['text' => 'flow-step'],
                            'flow-if' => ['text' => 'flow-if'],
                            'flow-if-else' => ['text' => 'flow-if-else'],
                            'flow-if-elseif' => ['text' => 'flow-if-elseif'],
                            'flow-if-elseif-multi' => ['text' => 'flow-if-elseif-multi'],
                            'flow-if-ternary' => ['text' => 'flow-if-ternary'],
                            'trunk' => ['text' => 'trunk'],
                            'merge-left' => ['text' => 'merge-left'],
                            'merge-right' => ['text' => 'merge-right'],
                            'branch-left' => ['text' => 'branch-left'],
                            'branch-right' => ['text' => 'branch-right'],
                            'branch-end' => ['text' => 'branch-end'],
                            'rekey-source-left' => ['text' => 'rekey-source-left'],
                            'rekey-source-right' => ['text' => 'rekey-source-right'],
                            'rekey-target-left' => ['text' => 'rekey-target-left'],
                            'rekey-target-right' => ['text' => 'rekey-target-right'],
                            'flow-while' => ['text' => 'flow-while'],
                        ],
                    ],
                    'parts' => [
                        'id' => 'literature.overview.deep-reference.parts',
                        'text' => __('Parts'),
                        'children' => [
                            'start' => ['text' => 'start'],
                            'end' => ['text' => 'end'],
                            'sideways' => ['text' => 'sideways'],
                            'chain' => ['text' => 'chain'],
                            'fusion' => ['text' => 'fusion'],
                            'split' => ['text' => 'split'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
