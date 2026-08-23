# Translation Workbench Graph Components

This folder contains package-owned UI components for graph-like timeline visualizations.

## Component Families

`tw-graph-protocol` is the frozen reference renderer from the earlier graph-v2 prototype. It renders resolved protocol data and should not be used as the place for new authoring API changes.

`tw-graph` is the new authoring component family. All future graph component changes belong here first. The goal is to normalize defaults, prop names, and data flow without disturbing the working protocol reference.

Every rendered `tw-graph` needs its own protocol identity. The component `graph-id` is that identity. Multiple graphs on one page must never silently share one protocol.

Child components inherit the `graph-id` and extend it with their component-specific path and counter. Authoring enters through `strang.*`, so a default trunk starts with a stable ID such as:

```text
{graph-id}.strang.trunk.1
```

No graph component should live in the app-level component tree. Package graph components belong under:

```text
packages/gunreip/laravel-translation-workbench/resources/views/components/ui
```

## Layer Order

The intended component chain is:

```text
tw-graph
|-- strang.*
|   |-- paths.*
|   |   |-- segments.*
|   |   |   |-- primitives.*
```

`tw-graph` owns the canvas and global defaults.

`strang.*` describes a logical graph strand such as trunk, merge, or branch.
The new authoring family uses singular component calls such as
`tw-graph.strang.trunk`, `tw-graph.strang.merge-left`, and
`tw-graph.strang.merge-right`.
Merge and branch strangs should attach to registered `strang` anchors such as
`attach-to="strang.trunk.node.3"` instead of hand-wiring coordinates.
`strang.trunk` registers reusable anchors while it renders:

```text
strang.trunk.start
strang.trunk.node.N
strang.trunk.path.N.start
strang.trunk.path.N.end
strang.trunk.end
```

Merge strangs register their own reusable anchors while they render:

```text
strang.merge-left.start
strang.merge-left.node.N
strang.merge-left.stem.start
strang.merge-left.stem.end
strang.merge-left.bridge.start
strang.merge-left.bridge.end
strang.merge-left.end
```

The same pattern applies to `strang.merge-right`.
Merge strangs can continue outward through `paths.merge-extension` by setting
`extension-count`. Each extension registers its own anchors under the active
merge side:

```text
strang.merge-left.extension.N.start
strang.merge-left.extension.N.node.1
strang.merge-left.extension.N.node.2
strang.merge-left.extension.N.node.3
strang.merge-left.extension.N.node.4
strang.merge-left.extension.N.end
```

The same pattern applies to `strang.merge-right.extension.N`.
Extension-specific lengths are configured through `extensionBridgeLength`,
`extensionBridgeLengths`, `extensionStemLength`, and
`extensionStemLengths`.
Extension labels follow the same node-numbered shape through
`extensionNodeLabels`:

```blade
:extension-node-labels="[
    1 => [
        1 => ['right' => 'Extension start'],
        4 => ['top' => 'Extension end'],
    ],
]"
```
Merge labels use the same node numbering:

```blade
:node-labels="[
    1 => ['right' => 'Source'],
    5 => ['left' => 'Attach'],
]"
```

Branch strangs attach to trunk anchors the same way and register their own
basic path anchors:

```text
strang.branch-left.start
strang.branch-left.node.N
strang.branch-left.bridge.start
strang.branch-left.bridge.end
strang.branch-left.stem.start
strang.branch-left.stem.end
strang.branch-left.end
```

The same pattern applies to `strang.branch-right`.

Branch extension entries may define `returnBridge` when only an open
`arc -> bridge` section is needed instead of a complete branch-return path.
The value can be a simple bridge length or an indexed configuration array:

```blade
:branch-extension="[
    'stem.1' => [
        1 => [
            'bridgeLength' => '8rem',
            'stemLength' => '4rem',
            'returnBridge' => [
                1 => [
                    'bridgeLength' => '7rem',
                    'color' => 'amber',
                    'nodeLabels' => [
                        1 => ['top' => 'Return arc'],
                        2 => ['bottom' => 'Bridge end'],
                    ],
                ],
            ],
        ],
    ],
]"
```

`returnBridge.nodeLabels` follows the path node numbering:

```text
1 = arc end
2 = bridge end
```

For `strang.branch-left`, this renders
`paths.branch-return-bridge left`: `arc west-north -> bridge left-right`.
For `strang.branch-right`, it renders
`paths.branch-return-bridge right`: `arc east-north -> bridge right-left`.

The registry is scoped by `graph-id`. The current authoring layer resets the
registry when `strang.trunk` renders, because Blade child slots are evaluated
before the root component body.

`paths.*` order one or more path sections into a continuous path.

`segments.*` describe concrete reusable graph sections such as a straight path, start, end, or arc.

`primitives.*` are neutral drawing primitives only: line, arc, connector, text, node, and dev markers.

Naming rule:

Props and local variables follow the `whoWhat` pattern. The component subject
comes first, the property comes second. This keeps context-specific props
grouped when reading, sorting, and searching.

```text
stemLength
stemContinuation
bridgeLength
bridgeContinuation
nodeLabels
nodeLabelConnectorLength
labelConnectorGap
devCounterColor
extensionStemLengths
extensionBridgeLengths
branchReturnBridgeLengths
```

Avoid reversed names such as `lengthStem`, `heightStem`, `labelNode`, or
`colorDevCounter`.

Short primitive props may stay neutral when the primitive itself already owns
the subject. For example, `primitives.line` may use `length`, because it is
already inside the `line` primitive. As soon as a higher layer configures a
specific graph concept, use `whoWhat`, such as `stemLength` or `bridgeLength`.

Graph vocabulary:

```text
bridge
  A graph/path line between path parts, commonly the horizontal line between arcs.

bridgeLength
  The length of that graph/path bridge.

connector
  A helper/association line from a node to a label. It is not a graph path.

connectorLength
  The default helper-line length for node labels. Individual labels may override
  it in `nodeLabels`.

connectorGap
  The default gap between a label connector and the anchor node.
```

Graph construction must use `bridge` / `bridgeLength` for path-to-path lines.
`connector` / `connectorLength` are reserved for label helper lines such as
`segments.label -> primitives.connector`.

## Data Flow

Component calls are the source of authoring intent and the render source. Props define what should be rendered.

Data-driven graph building has its own neutral preparation layer under:

```text
Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven
```

That layer may read aggregated workbench data and produce graph intent such as
`strang.trunk`, `strang.merge-left/right`, and `strang.branch-left/right`.
It must not render geometry directly and must not become a second component
tree. The intended flow is:

```text
timeline/workbench data
-> data-driven graph intent
-> tw-graph strang.* component calls
-> paths.*
-> segments.*
-> primitives.*
```

The JSON protocol is a debug/review artifact. It stores calculated coordinates such as anchor start/end points so the generated graph can be inspected and reproduced during development.

The JSON protocol is never edited manually and must not replace the component tree as the render source.

When a dimension, order, direction, or other geometry-relevant prop changes, the fingerprint must change and the affected coordinates must be recalculated before writing the debug protocol.

## Props

Common props should be named consistently across layers:

```text
id
side
direction
length
color
dev
anchorStart
anchorEnd
bridgeLength
stemLength
connectorLength
connectorGap
```

Component-specific props stay as close as possible to the layer where they are needed. Avoid passing large prop sets through unrelated layers.

## Defaults

Global visual defaults live on `tw-graph` and may be inherited by child components:

```text
defaultColor=zinc
lineLength=4rem
lineWidth=0.25rem
nodeSize=0.95rem
arcSize=2.75rem
capLength=1.75rem
bridgeLength=lineLength
stemLength=lineLength
connectorLength=2rem
connectorGap=0.25rem
```

Child components may override these values locally. A local component prop wins over the inherited graph default.

Default resolution is centralized through `Gunreip\TranslationWorkbench\Support\TwGraph\Defaults`.
Active authoring components should use this resolver instead of hand-writing
their own fallback chain.

For merge authoring, the effective length precedence is:

```text
local strang prop -> inherited tw-graph default -> lineLength
```

This applies to `bridgeLength` and `stemLength`.

`strang.trunk` renders ten path sections by default through its owned `paths.trunk`.
`path-count` controls how many path sections are rendered. `path-lengths` may override individual 1-based sections; missing or `null` values use `lineLength`.

Authoring enters through `tw-graph.strang.*`. `paths.*`, `segments.*`, and
`primitives.*` are lower layers and should only be called by their owning layer.

## Working Rule

Change one layer at a time.

After changing a layer, verify:

```text
component call -> protocol/cache -> rendered graph
```

If a behavior is still exploratory, keep it in `tw-graph-protocol` as reference or move it into `tw-graph` explicitly. Do not mix both component families in one change.
