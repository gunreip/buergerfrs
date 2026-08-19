# Translation Workbench Graph Components

This folder contains package-owned UI components for graph-like timeline visualizations.

## Component Families

`tw-graph-protocol` is the frozen reference renderer for the current graph-v2 prototype. It renders resolved protocol data and should not be used as the place for new authoring API changes.

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
|-- strangs.*
|   |-- paths.*
|   |   |-- segments.*
|   |   |   |-- primitives.*
```

`tw-graph` owns the canvas and global defaults.

`strangs.*` describe a logical graph strand such as trunk, merge, or branch.

`paths.*` order one or more path sections into a continuous path.

`segments.*` describe concrete reusable graph sections such as a straight path, start, end, or arc.

`primitives.*` are neutral drawing primitives only: line, arc, connector, text, node, and dev markers.

## Data Flow

Component calls are the source of authoring intent and the render source. Props define what should be rendered.

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
```

Child components may override these values locally. A local component prop wins over the inherited graph default.

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
