# TW Graph Glossary

This glossary defines the words used by TW Graph components, debug output and
configuration.

## Anchor

A named point that another component can attach to.

Examples:

```text
trunk.center.1.stem-2.anchorNode-end
branch.left.1.bridge1.anchorEnd
```

An anchor may exist even when the visible node dot is hidden.

## Anchor Node

The visible or logical node at the start or end of a segment.

The dot can be hidden in some cases, but the logical segment boundary may still
exist. DEV node counters may still show that boundary in DEV mode.

## Bridge

The main sideways graph line.

Use `bridge` for graph structure, for example the line between two arcs in a
branch, merge or rekey component.

Do not use `connector` for this meaning.

## Connector

The helper line between a node and a text label.

`connectorLength` controls label distance from the node. It should not be mixed
up with `bridgeLength`.

## Stem

The main vertical graph line.

Use `stemLength` for the length of one vertical graph section.

Avoid public props such as `verticalLength` when `stemLength` is meant.

## Path

A route-level component that composes segments into a meaningful visual route.

Examples:

- trunk path;
- merge path;
- branch path;
- branch extension path;
- branch return path.

## Segment

A reusable visual unit built from primitives.

Examples:

- path segment;
- arc segment;
- label segment;
- step segment;
- end segment.

## Primitive

An atomic visual element such as a line, arc, node, text box, cap or DEV
counter.

Primitives should not contain graph-specific business meaning.

## Strang

A semantic graph group built from paths.

Examples:

- trunk;
- merge;
- branch;
- rekey source;
- rekey target.

`strang` is the chosen term for this package, even when ordinary conversation
sometimes says `strangs`.

## Part

A hand-authoring component for custom samples.

Parts are useful when a graph is intentionally built by visual sequence instead
of data-driven semantic groups.

## Step

A centered explanation inside a stem sequence.

A step describes a shared reason, state or transition, for example:

```text
Source inactive|shared obsolete|9 rows
```

## End

A semantic ending, usually a short line plus cap and centered label.

End labels should be centered around the cap area, not attached like ordinary
side labels.

## Label

A text box that describes a node, segment, step or end.

Node labels are usually data-specific. Step and end labels usually describe the
shared meaning of a section.

## Label Width

The fixed width mode of a text label.

Current modes:

- `half`;
- default;
- `halfLong`;
- `long`.

Labels should not grow unpredictably based on text content.

## Bounds

The measured rectangle used for DEV display and collision calculation.

Bounds should use the same real dimensions as the rendered component, including
label width mode and padding.

## Collision

A detected overlap between bounds.

Collision reports should distinguish broad main-bound warnings from actual
sub-bound collisions such as label-vs-label or bridge-vs-label.

## Compensation

A calculated layout change that resolves a collision.

Applied compensation should be visible in DEV output. A theoretical overlap
belongs in `Collision Delta`, not in `Applied Compensation`.

## Correction

A final explicit layout adjustment from config.

Corrections are for intentional tuning after defaults and calculated
compensation. They should target canonical element references.

## Protocol

Generated diagnostic output for coordinates, fingerprints and review.

The protocol is not the normal render source and should not be manually edited.

## DEV Mode

Debug view for authoring and verification.

DEV mode may show node counters, coordinates, debug bounds, collision reports,
applied compensation and inspection tables.
