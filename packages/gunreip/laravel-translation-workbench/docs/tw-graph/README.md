# TW Graph

TW Graph is the visual graph layer of the Laravel Translation Workbench package.
It is used to render translation timelines, data-driven lifecycle diagrams and
hand-authored sample graphs.

The goal is not to create one-off SVG drawings. A graph should be assembled from
clear, reusable component layers so the same rules work for many datasets and
many future graph types.

## Current Locations

- Components: `resources/views/components/ui/tw-graph`
- Central defaults: `config/tw-graph-defaults.php`
- Data-driven defaults: `config/defaults/tw-graph-data-driven.php`
- Data-driven layout corrections:
  `config/layout-corrections/tw-graph-data-driven.php`
- Data-driven builder:
  `src/Support/TwGraph/DataDriven/TimelineChainGraphData.php`
- Data-driven helper classes:
  `src/Support/TwGraph/DataDriven/TimelineChainGraphData`
- Sample pages: `resources/views/pages/tw-graph/samples`
- Historical working notes: `../tw-praph-concept.md`

## Layer Model

`tw-graph`
: Owns the canvas, global defaults, graph id, registry, DEV mode, coordinates
  output and debug bounds.

`strang.*`
: Semantic graph groups such as `strang.trunk`, `strang.merge`,
  `strang.branch`, `strang.rekey-source` and `strang.rekey-target`.
  A strang knows how paths belong together as a meaningful unit.

`parts.*`
: Hand-authoring building blocks for custom examples. Parts are useful for
  free-form graphs such as samples and resumes, where the author intentionally
  chains pieces by visual meaning instead of data-driven semantics.

`paths.*`
: Path-level building blocks. A path knows how route-specific segments belong
  together, for example branch, merge, trunk, extension or return paths.

`segments.*`
: Composed visual units such as path segments, arcs, labels, steps and ends.
  Segments combine primitives into one reusable behavior.

`primitives.*`
: Atomic drawing elements: lines, arcs, nodes, text, caps, counters and small
  helper visuals.

## When To Use Which Layer

Use `strang.*` when a graph element has a domain meaning, for example a branch
that represents ended findings or a merge that represents origins entering a
shared key.

Use `parts.*` when a graph is intentionally hand-authored and illustrative. A
sample can use explicit lengths and anchors, but it should still demonstrate
clear props and predictable component behavior.

Use `paths.*`, `segments.*` and `primitives.*` by extending the component chain.
Do not bypass higher layers from a data-driven builder just to solve one visual
case.

## Core Rules

- Enter the component tree at the highest meaningful layer.
- Keep graph-specific logic out of low-level components.
- Do not duplicate the component set for every graph type.
- Do not manually edit generated protocol/cache files.
- Use the component tree as the render source.
- Use protocol output for inspection, reproduction and debugging.
- Keep defaults, compensation and corrections transparent and configurable.
- Keep element ids stable and usable across debug output, tooltips and config.

## Documentation

- [Concept](concept.md) defines the current architecture and implementation
  discipline.
- [Authoring](authoring.md) explains how to assemble hand-authored and
  semantic graphs from the component layers.
- [Data Driven](data-driven.md) describes the render-plan flow for timeline
  chain datasets.
- [Debugging](debugging.md) documents DEV mode, debug bounds and collision
  diagnostics.
- [Components](components.md) gives a layer-by-layer overview of the current
  component families.
- [Configuration](configuration.md) explains defaults, graph-family overrides
  and layout corrections.
- [Examples](examples.md) describes the intended sample and demonstration
  pages.
- [Element References](element-references.md) defines the canonical naming
  scheme for debug output, tooltips and correction targets.
- [Glossary](glossary.md) defines the core TW Graph terms.
- [Development Rules](development-rules.md) defines the implementation
  discipline for future TW Graph work.
- [Layout And Collisions](layout-and-collisions.md) explains bounds,
  collision detection and compensation strategy.
- [Data Driven Cases](data-driven-cases.md) lists the dataset constellations
  the data-driven renderer should cover.
- [Parts Authoring](parts-authoring.md) explains hand-authored graph chains
  built from `parts.*`.
- [Roadmap](roadmap.md) lists the next open documentation and implementation
  areas.
