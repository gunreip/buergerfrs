# TW Graph Authoring

This document describes how a graph should be authored without bypassing the
component chain.

## Choose The Right Entry Point

Use `strang.*` when the rendered piece has a semantic meaning:

- a main timeline: `strang.trunk`;
- origins entering a key: `strang.merge`;
- records ending or diverging: `strang.branch`;
- moved keys: `strang.rekey-source` or `strang.rekey-target`.

Use `parts.*` for deliberately hand-authored examples. Parts are useful when a
graph is more illustrative than data-driven, for example a resume graph or a
technical sample that demonstrates possible visual paths.

Avoid assembling low-level `segments.*` or `primitives.*` directly from a
data-driven builder. If a repeated shape is needed, add or extend the matching
`strang` or `paths` component.

## Basic Graph Shape

A page should normally render one `tw-graph` canvas and pass graph-level
defaults into the graph:

```blade
<x-translation-workbench::ui.tw-graph
    graph-id="example-graph"
    :dev="true"
    :coordinates="true"
>
    {{-- strangs or parts go here --}}
</x-translation-workbench::ui.tw-graph>
```

`graph-id` separates registries, coordinates and debug/protocol output. Reusing
the same id for multiple graphs on one page will make debugging unreliable.

## Semantic Authoring

Semantic graphs should read like graph meaning, not drawing instructions:

```blade
<x-translation-workbench::ui.tw-graph.strang.trunk
    id="trunk.center.1"
    start-label="Key created|2026-07-17 16:39"
    end-label="key ID #5|2 active - 1 ended|chain end"
/>

<x-translation-workbench::ui.tw-graph.strang.merge-left
    id="merge.left.1"
    attach-to="trunk.center.1.stem-2.anchorEnd"
    :node-labels="$mergeLabels"
/>
```

The exact component names and props may evolve, but the rule should stay the
same: semantic graph pieces are built through `strang.*`.

## Hand-Authored Parts

Hand-authored examples can chain `parts.*` when the graph itself is the design:

```blade
<x-translation-workbench::ui.tw-graph.parts.chain
    id="resume.center.1"
    direction="bottom-top"
>
    <x-translation-workbench::ui.tw-graph.parts.start
        id="resume.center.1.start"
        node-label-left="1879|Born in Ulm"
        label-width="halfLong"
    />

    <x-translation-workbench::ui.tw-graph.parts.sideways
        id="resume.left.1.sideways"
        direction="left"
        node-label-left="1905|Annus mirabilis"
    />
</x-translation-workbench::ui.tw-graph.parts.chain>
```

`parts.chain` should handle coordinate flow. Individual parts should describe
what they render and which labels or options they expose.

## Labels

Text labels should keep predictable dimensions. Width modes are preferable to
ad hoc classes:

- `half`: half the default width;
- default: the normal label width;
- `halfLong`: one and a half times the default width;
- `long`: twice the default width.

Use explicit line breaks when the content has a meaningful structure:

```blade
node-label-left="finding ID #5486|ui.states.all|2026-08-04 09:22"
```

Use long labels intentionally. If a label needs more room, mark it as such
instead of relying on uncontrolled natural text width.

## Defaults

Defaults should come from config, not from scattered literal values:

- package-wide defaults: `config/tw-graph-defaults.php`;
- graph-family defaults: `config/defaults/tw-graph-data-driven.php`;
- final layout corrections:
  `config/layout-corrections/tw-graph-data-driven.php`.

When a prop sets an absolute value, it should override the resolved default.
When a prop is meant to modify a default, name it as a delta, factor or
compensation so the behavior is visible.

## DEV Mode

DEV mode may render:

- node counters;
- coordinate badges;
- debug bounds;
- collision reports;
- applied compensation values.

`coordinates=false` should hide coordinate badges only. It must not disable
coordinate calculation, registry updates or canvas correction.

`dev=false` should hide DEV visuals. It must not disable layout calculations
that are required for correct rendering.

## Element References

Use concise references in visible debug output, config and reports:

```text
trunk.center.1.stem-2
branch.left.1.bridge1
merge.right.1.extension-2.stem-2
rekey.right.target.1.end.end-label
```

Do not introduce a second naming scheme for corrections or debug tables. The
same element should be addressable by the same reference everywhere.

## Before Adding A New Shape

Before creating a new component or prop:

1. Check whether the shape already exists in `strang`, `paths` or `segments`.
2. Decide which layer should own the behavior.
3. Add the prop at the highest useful layer and pass it down.
4. Keep fallback behavior visible in DEV mode.
5. Update documentation when the behavior becomes part of the contract.
