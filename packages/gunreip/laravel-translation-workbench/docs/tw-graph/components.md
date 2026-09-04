# TW Graph Components

This is an orientation map for the current component families. It does not try
to replace the Blade files as the final prop reference. Its purpose is to keep
the layer responsibilities clear.

## Top Level

`tw-graph`
: Canvas, graph id, defaults, registry, DEV mode, coordinates and layout
  bounds. This is the outer context every graph should use.

The top level should not contain graph-specific special cases. It may expose
common defaults and debug controls that all child components can use.

## Strang Components

`strang.trunk`
: The main timeline of a graph. It owns trunk start, trunk paths, optional
  trunk steps, trunk end and the trunk-side label placement.

`strang.merge-left` / `strang.merge-right`
: Origins entering the current key or shared state from the left or right side.
  Merge strangs may have merge extensions and aggregate sections.

`strang.branch-left` / `strang.branch-right`
: Records that diverge, end or no longer continue into the main target. Branch
  strangs may contain steps, branch ends, returns and extensions.

`strang.rekey-source-left` / `strang.rekey-source-right`
: A source-side rekey relation. It shows where the current key came from and
  which earlier key continued into this one.

`strang.rekey-target-left` / `strang.rekey-target-right`
: A target-side rekey relation. It shows that the current key continues into a
  different target key.

Strang components are the preferred entry point for semantic graphs.

## Parts Components

`parts.chain`
: Coordinates wrapper for hand-authored graphs. It should handle anchor flow so
  sample pages do not need to hard-code every coordinate.

`parts.start`
: Start piece for a hand-authored chain. It can render start labels and
  optional node labels.

`parts.sideways`
: A sideways movement from the current chain. It can render arc-in, bridge,
  arc-out, optional extension stems and labels.

`parts.end`
: End piece for a hand-authored chain, usually a short stem plus cap.

Parts are useful for samples such as resumes and custom diagrams. They are not
the preferred layer for data-driven semantic graph building.

## Paths Components

`paths.trunk`
: Builds trunk path sections from resolved path/stem lengths and labels.

`paths.merge`
: Builds the main merge route.

`paths.merge-extension`
: Extends merge routes while preserving the same merge semantics.

`paths.branch`
: Builds the main branch route.

`paths.branch-extension`
: Extends branch routes. It must respect whether the extension attaches to a
  bridge end or a stem end.

`paths.branch-return`
: Builds return routes back toward another chain/strang.

Paths should compose segments. They should not decide why a record is merged,
ended or rekeyed.

## Segments Components

`segments.path`
: A straight path/stem section with optional start/end nodes and labels.

`segments.arc`
: A route bend built from an arc primitive and its anchor behavior.

`segments.label`
: A node-attached text label and its connector.

`segments.step`
: A centered explanation inside a stem sequence. This is used for shared
  reasons or states such as `source inactive`.

`segments.end`
: A line/cap/centered label ending. Used when a strang needs a visible semantic
  end.

Segments should be reusable visual behaviors. If the same visual pattern starts
appearing in several paths, it probably belongs here.

## Primitives

`primitives.line`
: Atomic line/stem/bridge drawing.

`primitives.arc`
: Atomic arc drawing.

`primitives.node`
: Anchor node/dot drawing.

`primitives.text`
: Text box rendering with fixed width modes.

`primitives.connector`
: Helper line between a node and a label. This is different from a graph
  bridge.

`primitives.dev-node-counter`
: DEV-only node numbering.

Primitives should stay visually focused. They should not know about timeline
chains, findings, translations or collision policy.

## Naming Notes

Use `bridge` for the main sideways graph line.

Use `connector` only for the helper line between a node and its label.

Use `stem` for the main vertical graph line.

Avoid `horizontal` and `vertical` in public props when the semantic names
`bridgeLength` and `stemLength` are clearer.
