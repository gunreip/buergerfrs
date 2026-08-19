{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/graph-v2/segments-catalog.blade.php --}}

@php
    $tab = '__tw_indent__';
    $tree = '|-- ';
    $treeNested = '|   |-- ';
    $part = '__tw_part__';
    $ellipsis = '...';
    $segmentRowsReordered = [
        [
            'name' => 'label',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.label',
            'structure' => ['segments.label', $tree . 'primitives.connector', $tree . 'primitives.text'],
            'composition' => ['primitives.connector', 'primitives.text'],
            'props' => [
                ':label="$label"',
                $tab . 'anchorX / anchorY',
                $tab . 'side=left|right|top|bottom',
                $tab . 'connectorLength / connectorGap',
                $tab . 'color / badgeColor',
            ],
            'view' => 'label',
        ],
        [
            'name' => 'path left-right',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.path',
            'structure' => [
                'segments.path',
                $tree . 'primitives.line',
                $part . 'nodeStart',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $treeNested . 'optional segments.label slot A/B',
                $part . 'line',
                $part . 'nodeEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $treeNested . 'optional segments.label slot A/B',
            ],
            'composition' => [
                'primitives.line owns line/nodeStart/nodeEnd',
                'node attachments: primitives.dev-node-counter + optional segments.label',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'direction=left-right|right-left|top-bottom|bottom-top',
                $tab . 'length',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=false|true|[slotA, slotB]',
                $tab . 'nodeEnd=false|true|[slotA, slotB]',
                $tab . 'gradient',
                $tab . 'cap / capLength',
                $tab . 'dev',
            ],
            'view' => 'path',
        ],
        [
            'name' => 'path top-bottom',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.path',
            'structure' => [
                'segments.path',
                $tree . 'primitives.line',
                $part . 'nodeStart',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $treeNested . 'optional segments.label slot A/B',
                $part . 'line',
                $part . 'nodeEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $treeNested . 'optional segments.label slot A/B',
            ],
            'composition' => [
                'primitives.line owns line/nodeStart/nodeEnd',
                'node attachments: primitives.dev-node-counter + optional segments.label',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'direction=top-bottom',
                $tab . 'length',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=false|true|[slotA, slotB]',
                $tab . 'nodeEnd=false|true|[slotA, slotB]',
                $tab . 'gradient',
                $tab . 'cap / capLength',
                $tab . 'dev',
            ],
            'view' => 'path-top-bottom',
        ],
        [
            'name' => 'start bottom-top',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.start',
            'structure' => [
                'segments.start',
                $tree . 'segments.path',
                $treeNested . 'primitives.line',
                $treeNested . 'optional primitives.text startLabel',
                $part . 'nodeStart',
                $part . 'line',
                $part . 'nodeEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $treeNested . 'optional segments.label slot A/B',
            ],
            'composition' => [
                'segments.start sets start defaults',
                'segments.path renders primitives.line + node attachments',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'direction',
                $tab . 'length',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=false default',
                $tab . 'nodeEnd=true default',
                $tab . 'gradient=true default',
                $tab . 'dev',
            ],
            'view' => 'start',
        ],
        [
            'name' => 'start right-left',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.start',
            'structure' => [
                'segments.start',
                $tree . 'segments.path',
                $treeNested . 'primitives.line',
                $treeNested . 'optional primitives.text startLabel',
                $part . 'nodeStart',
                $part . 'line',
                $part . 'nodeEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $treeNested . 'optional segments.label slot A/B',
            ],
            'composition' => [
                'segments.start sets start defaults',
                'segments.path renders primitives.line + node attachments',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'direction=right-left',
                $tab . 'length',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=false default',
                $tab . 'nodeEnd=true default',
                $tab . 'gradient=true default',
                $tab . 'startLabel optional',
                $tab . 'dev',
            ],
            'view' => 'start-right-left',
        ],
        [
            'name' => 'end bottom-top',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.end',
            'structure' => [
                'segments.end',
                $tree . 'segments.path',
                $treeNested . 'primitives.line',
                $part . 'nodeStart',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $treeNested . 'optional segments.label slot A/B',
                $part . 'line',
                $part . 'capEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $treeNested . 'optional primitives.text endLabel',
            ],
            'composition' => [
                'segments.end sets end defaults',
                'segments.path renders primitives.line + node attachments',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'direction=bottom-top',
                $tab . 'length',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=true default',
                $tab . 'nodeEnd=false enforced',
                $tab . 'cap=true enforced',
                $tab . 'capLength',
                $tab . 'devCounterEnd optional',
                $tab . 'endLabel optional',
                $tab . 'dev',
            ],
            'view' => 'end',
        ],
        [
            'name' => 'end left-right',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.end',
            'structure' => [
                'segments.end',
                $tree . 'segments.path',
                $treeNested . 'primitives.line',
                $part . 'nodeStart',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $treeNested . 'optional segments.label slot A/B',
                $part . 'line',
                $part . 'capEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $treeNested . 'optional primitives.text endLabel',
            ],
            'composition' => [
                'segments.end sets end defaults',
                'segments.path renders primitives.line + node attachments',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'direction=left-right',
                $tab . 'length',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=true default',
                $tab . 'nodeEnd=false enforced',
                $tab . 'cap=true enforced',
                $tab . 'capLength',
                $tab . 'devCounterEnd optional',
                $tab . 'endLabel optional',
                $tab . 'dev',
            ],
            'view' => 'end-left-right',
        ],
        [
            'name' => 'arc north-west',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.arc',
            'structure' => [
                'segments.arc',
                $tree . 'primitives.arc',
                $part . 'nodeStart',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'start anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
                $part . 'nodeEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'end anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
            ],
            'composition' => [
                'segments.arc renders primitives.arc',
                'anchor labels use segments.label: primitives.connector + primitives.text',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'startAnchor=n',
                $tab . 'endAnchor=w',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=true',
                $tab . 'nodeEnd=true',
                $tab . 'dev',
                $tab . 'devCounterColor',
                $tab . 'startLabel optional',
                $tab . 'endLabel optional',
            ],
            'view' => 'arc-north-west',
        ],
        [
            'name' => 'arc west-north',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.arc',
            'structure' => [
                'segments.arc',
                $tree . 'primitives.arc',
                $part . 'nodeStart',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'start anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
                $part . 'nodeEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'end anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
            ],
            'composition' => [
                'segments.arc renders primitives.arc',
                'anchor labels use segments.label: primitives.connector + primitives.text',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'startAnchor=w',
                $tab . 'endAnchor=n',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=true',
                $tab . 'nodeEnd=true',
                $tab . 'dev',
                $tab . 'devCounterColor',
                $tab . 'startLabel optional',
                $tab . 'endLabel optional',
            ],
            'view' => 'arc-west-north',
        ],
        [
            'name' => 'arc north-east',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.arc',
            'structure' => [
                'segments.arc',
                $tree . 'primitives.arc',
                $part . 'nodeStart',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'start anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
                $part . 'nodeEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'end anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
            ],
            'composition' => [
                'segments.arc renders primitives.arc',
                'anchor labels use segments.label: primitives.connector + primitives.text',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'startAnchor=n',
                $tab . 'endAnchor=e',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=true',
                $tab . 'nodeEnd=true',
                $tab . 'dev',
                $tab . 'devCounterColor',
                $tab . 'startLabel optional',
                $tab . 'endLabel optional',
            ],
            'view' => 'arc-north-east',
        ],
        [
            'name' => 'arc east-north',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.arc',
            'structure' => [
                'segments.arc',
                $tree . 'primitives.arc',
                $part . 'nodeStart',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'start anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
                $part . 'nodeEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'end anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
            ],
            'composition' => [
                'segments.arc renders primitives.arc',
                'anchor labels use segments.label: primitives.connector + primitives.text',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'startAnchor=e',
                $tab . 'endAnchor=n',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=true',
                $tab . 'nodeEnd=true',
                $tab . 'dev',
                $tab . 'devCounterColor',
                $tab . 'startLabel optional',
                $tab . 'endLabel optional',
            ],
            'view' => 'arc-east-north',
        ],
        [
            'name' => 'arc west-south',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.arc',
            'structure' => [
                'segments.arc',
                $tree . 'primitives.arc',
                $part . 'nodeStart',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'start anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
                $part . 'nodeEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'end anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
            ],
            'composition' => [
                'segments.arc renders primitives.arc',
                'anchor labels use segments.label: primitives.connector + primitives.text',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'startAnchor=w',
                $tab . 'endAnchor=s',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=true',
                $tab . 'nodeEnd=true',
                $tab . 'dev',
                $tab . 'devCounterColor',
                $tab . 'startLabel optional',
                $tab . 'endLabel optional',
            ],
            'view' => 'arc-west-south',
        ],
        [
            'name' => 'arc south-west',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.arc',
            'structure' => [
                'segments.arc',
                $tree . 'primitives.arc',
                $part . 'nodeStart',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'start anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
                $part . 'nodeEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'end anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
            ],
            'composition' => [
                'segments.arc renders primitives.arc',
                'anchor labels use segments.label: primitives.connector + primitives.text',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'startAnchor=s',
                $tab . 'endAnchor=w',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=true',
                $tab . 'nodeEnd=true',
                $tab . 'dev',
                $tab . 'devCounterColor',
                $tab . 'startLabel optional',
                $tab . 'endLabel optional',
            ],
            'view' => 'arc-south-west',
        ],
        [
            'name' => 'arc east-south',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.arc',
            'structure' => [
                'segments.arc',
                $tree . 'primitives.arc',
                $part . 'nodeStart',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'start anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
                $part . 'nodeEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'end anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
            ],
            'composition' => [
                'segments.arc renders primitives.arc',
                'anchor labels use segments.label: primitives.connector + primitives.text',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'startAnchor=e',
                $tab . 'endAnchor=s',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=true',
                $tab . 'nodeEnd=true',
                $tab . 'dev',
                $tab . 'devCounterColor',
                $tab . 'startLabel optional',
                $tab . 'endLabel optional',
            ],
            'view' => 'arc-east-south',
        ],
        [
            'name' => 'arc south-east',
            'component' => '<x-translation-workbench::ui.tw-graph-protocol.segments.arc',
            'structure' => [
                'segments.arc',
                $tree . 'primitives.arc',
                $part . 'nodeStart',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'start anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
                $part . 'nodeEnd',
                $treeNested . 'dev-mode primitives.dev-node-counter',
                $tree . 'end anchor attachments',
                $treeNested . 'optional segments.label',
                $treeNested . 'primitives.connector',
                $treeNested . 'primitives.text',
            ],
            'composition' => [
                'segments.arc renders primitives.arc',
                'anchor labels use segments.label: primitives.connector + primitives.text',
            ],
            'props' => [
                ':segment="$segment"',
                $tab . 'startAnchor=s',
                $tab . 'endAnchor=e',
                $tab . 'anchorStart / anchorEnd',
                $tab . 'nodeStart=true',
                $tab . 'nodeEnd=true',
                $tab . 'dev',
                $tab . 'devCounterColor',
                $tab . 'startLabel optional',
                $tab . 'endLabel optional',
            ],
            'view' => 'arc-south-east',
        ],
    ];
    $segmentRowsNeedsReview = [];

    /*
     * TODO TW-GRAPH cleanup:
     * Old segment fallback data kept inactive until the package-local
     * tw-graph-protocol segment catalogue has been validated.
     *
     * $previewSegment = [
     *     'id' => 'catalog.segment',
     *     'direction' => 'bottom-top',
     *     'length' => '4rem',
     *     'anchorStart' => ['x' => '0rem', 'y' => '0.75rem'],
     *     'anchorEnd' => ['x' => '0rem', 'y' => '4.75rem'],
     *     'nodeStart' => false,
     *     'nodeEnd' => true,
     *     'capLength' => '1.75rem',
     *     'color' => 'green',
     * ];
     */
    $pathSegment = [
        'id' => 'catalog.segment.path',
        'direction' => 'left-right',
        'length' => '5rem',
        'anchorStart' => ['x' => '-2.5rem', 'y' => '3rem'],
        'anchorEnd' => ['x' => '2.5rem', 'y' => '3rem'],
        'nodeStart' => [
            [
                'text' => ['Start', 'label'],
                'connectorLength' => '1.5rem',
                'connectorGap' => '0.25rem',
                'color' => 'amber',
                'badgeColor' => 'amber',
            ],
            null,
        ],
        'nodeEnd' => [
            null,
            [
                'text' => ['End', 'label'],
                'connectorLength' => '1.5rem',
                'connectorGap' => '0.25rem',
                'color' => 'sky',
                'badgeColor' => 'sky',
            ],
        ],
        'gradient' => false,
        'cap' => false,
        'color' => 'cyan',
        'dev' => true,
    ];
    $pathSegmentTopBottom = array_replace($pathSegment, [
        'id' => 'catalog.segment.path.top-bottom',
        'direction' => 'top-bottom',
        'length' => '4rem',
        'anchorStart' => ['x' => '0rem', 'y' => '5rem'],
        'anchorEnd' => ['x' => '0rem', 'y' => '1rem'],
        'color' => 'lime',
    ]);
    $startSegment = [
        'id' => 'catalog.segment.start',
        'direction' => 'bottom-top',
        'length' => '4rem',
        'anchorStart' => ['x' => '0rem', 'y' => '0.75rem'],
        'anchorEnd' => ['x' => '0rem', 'y' => '4.75rem'],
        'nodeEnd' => [
            [
                'text' => ['Start', 'end'],
                'connectorLength' => '1.5rem',
                'connectorGap' => '0.25rem',
                'color' => 'green',
                'badgeColor' => 'green',
            ],
            null,
        ],
        'color' => 'green',
        'dev' => true,
    ];
    $startSegmentRightLeft = array_replace($startSegment, [
        'id' => 'catalog.segment.start.right-left',
        'direction' => 'right-left',
        'length' => '5rem',
        'anchorStart' => ['x' => '2.5rem', 'y' => '3rem'],
        'anchorEnd' => ['x' => '-2.5rem', 'y' => '3rem'],
        'startLabel' => [
            'text' => ['Start', 'label'],
            'side' => 'right',
            'offset' => '0.75rem',
            'badgeColor' => 'rose',
        ],
        'color' => 'rose',
    ]);
    $endSegment = [
        'id' => 'catalog.segment.end',
        'direction' => 'bottom-top',
        'length' => '4rem',
        'anchorStart' => ['x' => '0rem', 'y' => '0.75rem'],
        'anchorEnd' => ['x' => '0rem', 'y' => '4.75rem'],
        'nodeStart' => [
            [
                'text' => ['End', 'start'],
                'connectorLength' => '1.5rem',
                'connectorGap' => '0.25rem',
                'color' => 'violet',
                'badgeColor' => 'violet',
            ],
            null,
        ],
        'capLength' => '1.75rem',
        'endLabel' => [
            'text' => ['End', 'label'],
            'side' => 'top',
            'offset' => '0.75rem',
            'badgeColor' => 'violet',
        ],
        'color' => 'violet',
        'dev' => true,
    ];
    $endSegmentLeftRight = array_replace($endSegment, [
        'id' => 'catalog.segment.end.left-right',
        'direction' => 'left-right',
        'length' => '5rem',
        'anchorStart' => ['x' => '-2.5rem', 'y' => '3rem'],
        'anchorEnd' => ['x' => '2.5rem', 'y' => '3rem'],
        'endLabel' => [
            'text' => ['End', 'label'],
            'side' => 'right',
            'offset' => '0.75rem',
            'badgeColor' => 'amber',
        ],
        'color' => 'amber',
    ]);
    $arcNorthWestSegment = [
        'id' => 'catalog.segment.arc.north-west',
        'startAnchor' => 'n',
        'endAnchor' => 'w',
        'anchorStart' => ['x' => '0rem', 'y' => '4.75rem'],
        'anchorEnd' => ['x' => '-2.75rem', 'y' => '2rem'],
        'nodeStart' => true,
        'nodeEnd' => true,
        'startLabel' => [
            'text' => ['North', 'start'],
            'side' => 'top',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'sky',
        ],
        'endLabel' => [
            'text' => ['West', 'end'],
            'side' => 'left',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'sky',
        ],
        'color' => 'sky',
        'dev' => true,
    ];
    $arcWestNorthSegment = array_replace($arcNorthWestSegment, [
        'id' => 'catalog.segment.arc.west-north',
        'startAnchor' => 'w',
        'endAnchor' => 'n',
        'anchorStart' => ['x' => '-2.75rem', 'y' => '2rem'],
        'anchorEnd' => ['x' => '0rem', 'y' => '4.75rem'],
        'startLabel' => [
            'text' => ['West', 'start'],
            'side' => 'left',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'amber',
        ],
        'endLabel' => [
            'text' => ['North', 'end'],
            'side' => 'top',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'amber',
        ],
        'color' => 'amber',
        'devCounterColor' => 'amber',
    ]);
    $arcNorthEastSegment = array_replace($arcNorthWestSegment, [
        'id' => 'catalog.segment.arc.north-east',
        'startAnchor' => 'n',
        'endAnchor' => 'e',
        'anchorStart' => ['x' => '0rem', 'y' => '4.75rem'],
        'anchorEnd' => ['x' => '2.75rem', 'y' => '2rem'],
        'startLabel' => [
            'text' => ['North', 'start'],
            'side' => 'top',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'green',
        ],
        'endLabel' => [
            'text' => ['East', 'end'],
            'side' => 'right',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'green',
        ],
        'color' => 'green',
        'devCounterColor' => 'green',
    ]);
    $arcEastNorthSegment = array_replace($arcNorthEastSegment, [
        'id' => 'catalog.segment.arc.east-north',
        'startAnchor' => 'e',
        'endAnchor' => 'n',
        'anchorStart' => ['x' => '2.75rem', 'y' => '2rem'],
        'anchorEnd' => ['x' => '0rem', 'y' => '4.75rem'],
        'startLabel' => [
            'text' => ['East', 'start'],
            'side' => 'right',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'lime',
        ],
        'endLabel' => [
            'text' => ['North', 'end'],
            'side' => 'top',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'lime',
        ],
        'color' => 'lime',
        'devCounterColor' => 'lime',
    ]);
    $arcWestSouthSegment = array_replace($arcNorthWestSegment, [
        'id' => 'catalog.segment.arc.west-south',
        'startAnchor' => 'w',
        'endAnchor' => 's',
        'anchorStart' => ['x' => '-2.75rem', 'y' => '4.75rem'],
        'anchorEnd' => ['x' => '0rem', 'y' => '2rem'],
        'startLabel' => [
            'text' => ['West', 'start'],
            'side' => 'left',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'emerald',
        ],
        'endLabel' => [
            'text' => ['South', 'end'],
            'side' => 'bottom',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'emerald',
        ],
        'color' => 'emerald',
        'devCounterColor' => 'emerald',
    ]);
    $arcSouthWestSegment = array_replace($arcWestSouthSegment, [
        'id' => 'catalog.segment.arc.south-west',
        'startAnchor' => 's',
        'endAnchor' => 'w',
        'anchorStart' => ['x' => '0rem', 'y' => '2rem'],
        'anchorEnd' => ['x' => '-2.75rem', 'y' => '4.75rem'],
        'startLabel' => [
            'text' => ['South', 'start'],
            'side' => 'bottom',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'teal',
        ],
        'endLabel' => [
            'text' => ['West', 'end'],
            'side' => 'left',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'teal',
        ],
        'color' => 'teal',
        'devCounterColor' => 'teal',
    ]);
    $arcEastSouthSegment = array_replace($arcNorthEastSegment, [
        'id' => 'catalog.segment.arc.east-south',
        'startAnchor' => 'e',
        'endAnchor' => 's',
        'anchorStart' => ['x' => '2.75rem', 'y' => '4.75rem'],
        'anchorEnd' => ['x' => '0rem', 'y' => '2rem'],
        'startLabel' => [
            'text' => ['East', 'start'],
            'side' => 'right',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'cyan',
        ],
        'endLabel' => [
            'text' => ['South', 'end'],
            'side' => 'bottom',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'cyan',
        ],
        'color' => 'cyan',
        'devCounterColor' => 'cyan',
    ]);
    $arcSouthEastSegment = array_replace($arcEastSouthSegment, [
        'id' => 'catalog.segment.arc.south-east',
        'startAnchor' => 's',
        'endAnchor' => 'e',
        'anchorStart' => ['x' => '0rem', 'y' => '2rem'],
        'anchorEnd' => ['x' => '2.75rem', 'y' => '4.75rem'],
        'startLabel' => [
            'text' => ['South', 'start'],
            'side' => 'bottom',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'sky',
        ],
        'endLabel' => [
            'text' => ['East', 'end'],
            'side' => 'right',
            'connectorLength' => '1.25rem',
            'connectorGap' => '0.25rem',
            'badgeColor' => 'sky',
        ],
        'color' => 'sky',
        'devCounterColor' => 'sky',
    ]);
    /*
     * TODO TW-GRAPH cleanup:
     * Old fallback variables for removed App-level segment cases.
     *
     * $mergeEndSegment = [
     *     'id' => 'catalog.segment.merge-end.1.top-bottom',
     *     'direction' => 'top-bottom',
     *     'length' => '3rem',
     *     'anchorStart' => ['x' => '0rem', 'y' => '4.75rem'],
     *     'anchorEnd' => ['x' => '0rem', 'y' => '1.75rem'],
     *     'nodeStart' => false,
     *     'nodeEnd' => true,
     *     'color' => 'amber',
     * ];
     * $labelSegment = [
     *     'id' => 'catalog.segment.label-source',
     *     'anchorEnd' => ['x' => '-1.75rem', 'y' => '3rem'],
     *     'color' => 'sky',
     * ];
     * $label = [
     *     'id' => 'catalog.segment.text-label',
     *     'side' => 'right',
     *     'text' => ['Text label', 'segment'],
     *     'connectorLength' => '2rem',
     *     'connectorGap' => '0.25rem',
     *     'color' => 'sky',
     *     'badgeColor' => 'sky',
     * ];
     */
    $packageLabelDirections = [
        [
            'id' => 'catalog.segment.label.top',
            'side' => 'top',
            'text' => ['Top', 'label'],
            'connectorLength' => '1.4rem',
            'connectorGap' => '0.25rem',
            'color' => 'amber',
            'badgeColor' => 'amber',
        ],
        [
            'id' => 'catalog.segment.label.bottom',
            'side' => 'bottom',
            'text' => ['Bottom', 'label'],
            'connectorLength' => '1.4rem',
            'connectorGap' => '0.25rem',
            'color' => 'sky',
            'badgeColor' => 'sky',
        ],
        [
            'id' => 'catalog.segment.label.left',
            'side' => 'left',
            'text' => ['Left', 'label'],
            'connectorLength' => '1.4rem',
            'connectorGap' => '0.25rem',
            'color' => 'lime',
            'badgeColor' => 'lime',
        ],
        [
            'id' => 'catalog.segment.label.right',
            'side' => 'right',
            'text' => ['Right', 'label'],
            'connectorLength' => '1.4rem',
            'connectorGap' => '0.25rem',
            'color' => 'cyan',
            'badgeColor' => 'cyan',
        ],
    ];
    /*
     * TODO: TW-GRAPH cleanup:
     * Old merge fallback data for removed App-level segment cases.
     *
     * $mergeEndPath = [
     *     'textEnd' => ['Merge end', 'segment'],
     *     'textEndAnchor' => 'anchorEnd',
     *     'textEndConnectorPlacement' => 'bottom',
     *     'textEndConnectorLength' => '1rem',
     *     'textEndConnectorGap' => '0.25rem',
     * ];
     * $mergeArcSegment = [
     *     'id' => 'catalog.segment.merge-arc',
     *     'type' => 'arc',
     *     'direction' => 'se',
     *     'anchorStart' => ['x' => '0rem', 'y' => '4.5rem'],
     *     'anchorEnd' => ['x' => '-2.5rem', 'y' => '2rem'],
     *     'color' => 'amber',
     * ];
     * $mergePathSegment = [
     *     'id' => 'catalog.segment.merge-path',
     *     'type' => 'path',
     *     'direction' => 'right-left',
     *     'length' => '5rem',
     *     'anchorStart' => ['x' => '2.5rem', 'y' => '3rem'],
     *     'anchorEnd' => ['x' => '-2.5rem', 'y' => '3rem'],
     *     'nodeStart' => false,
     *     'nodeEnd' => false,
     *     'color' => 'amber',
     * ];
     */
@endphp

<div
    class="w-full"
    x-data="{ twGraphDev: true }"
>
    <flux:accordion>
        <flux:accordion.item>
            <flux:accordion.heading class="rounded rounded-b-md bg-sky-800 p-2">
                <span class="inline-flex items-center gap-2">
                    <span class="w-32">{{ __('Segment catalog') }}</span>
                    <flux:field
                        class="items-center gap-2"
                        variant="inline"
                        x-on:click.stop
                    >
                        <flux:switch
                            class="switch-colored hover:cursor-pointer"
                            x-model="twGraphDev"
                        />
                        <flux:label class="text-xs opacity-70 hover:cursor-pointer">
                            {{ __('DEV') }}
                        </flux:label>
                    </flux:field>
                    <flux:badge
                        class="w-xs inline-block"
                        color="sky"
                    >
                        <flux:breadcrumbs>
                            <flux:breadcrumbs.item href="#">{{ __('primitives') }}</flux:breadcrumbs.item>
                            <flux:breadcrumbs.item href="#">{{ __('segments') }}</flux:breadcrumbs.item>
                        </flux:breadcrumbs>
                    </flux:badge>
                    <flux:badge
                        class="w-48"
                        color="amber"
                    >
                        {{ __('primitive compositions') }}
                    </flux:badge>
                    <flux:badge color="red">
                        {{ __('segments.label') }}
                    </flux:badge>
                    <flux:badge color="red">
                        {{ __('segments.path') }}
                    </flux:badge>
                    <flux:badge color="red">
                        {{ __('segments.start') }}
                    </flux:badge>
                    <flux:badge color="red">
                        {{ __('segments.end') }}
                    </flux:badge>
                    <flux:badge color="red">
                        {{ __('Segments: path') }}
                    </flux:badge>
                </span>
            </flux:accordion.heading>
            <flux:accordion.content>
                <div
                    class="mt-3 w-full"
                    x-bind:class="{ 'tw-graph-protocol-dev-disabled': !twGraphDev }"
                >
                    @php
                        $segmentTabRows = collect($segmentRowsReordered)
                            ->map(fn (array $row): array => array_replace($row, [
                                'tabName' => 'ready-' . (string) data_get($row, 'view', data_get($row, 'name', 'segment')),
                                'tabLabel' => (string) data_get($row, 'name', 'segment'),
                            ]))
                            ->concat(
                                collect($segmentRowsNeedsReview)->map(fn (array $row): array => array_replace($row, [
                                    'tabName' => 'review-' . (string) data_get($row, 'view', data_get($row, 'name', 'segment')),
                                    'tabLabel' => __('Review') . ': ' . (string) data_get($row, 'name', 'segment'),
                                ]))
                            )
                            ->values();
                    @endphp

                    @if ($segmentTabRows->isEmpty())
                        <div
                            class="rounded-lg border border-dashed border-amber-300 bg-amber-50/70 p-3 text-xs text-amber-800 dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-200">
                            {{ __('No reviewed/reordered segment components yet.') }}
                        </div>
                    @else
                        <flux:tab.group>
                            <flux:tabs>
                                @foreach ($segmentTabRows as $row)
                                    <flux:tab name="{{ $row['tabName'] }}">
                                        {{ $row['tabLabel'] }}
                                    </flux:tab>
                                @endforeach
                            </flux:tabs>

                            @foreach ($segmentTabRows as $row)
                                <flux:tab.panel name="{{ $row['tabName'] }}">
                                    <div class="mt-3">
                                        @include(
                                            'translation-workbench::livewire.raw-data.timeline-chains.graph-preview.graph-v2.segments-catalog-card',
                                            ['row' => $row]
                                        )
                                    </div>
                                </flux:tab.panel>
                            @endforeach
                        </flux:tab.group>
                    @endif
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</div>
