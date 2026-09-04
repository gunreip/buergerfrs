# TW Graph Parts Authoring

`parts.*` components are for hand-authored graphs. They are useful when a graph
is designed as an individual visual composition instead of being generated from
translation-workbench timeline data.

Examples:

- resume timelines;
- bug lifecycle reports;
- process walkthroughs;
- custom diagrams for documentation or demos.

## Purpose

Parts should make manual graph authoring comfortable without requiring the
author to calculate every coordinate by hand.

The author should describe a chain of visual parts. The chain wrapper should
resolve the next anchor positions.

## Parts Chain

`parts.chain` owns coordinate flow for a hand-authored sequence.

It should:

- start from an initial anchor;
- pass the current anchor into each child part;
- receive the next anchor from each child part;
- keep the graph id and local references stable;
- avoid leaking raw coordinate arrays into sample pages.

The sample page should read like a graph sequence, not like geometry math.

```blade
<x-translation-workbench::ui.tw-graph.parts.chain
    id="resume.center.1"
    direction="bottom-top"
>
    {{-- parts.start, parts.sideways, parts.end, ... --}}
</x-translation-workbench::ui.tw-graph.parts.chain>
```

## Direction

Parts should support direction-aware rendering where it matters.

Common directions:

- `bottom-top`;
- `top-bottom`;
- `left`;
- `right`.

Direction must affect label placement when the visual meaning changes. For
example, a start label in a top-to-bottom chain should sit on the correct side
of the start, not reuse the bottom-to-top placement.

## parts.start

`parts.start` begins a hand-authored chain.

It may render:

- a start line or bridge;
- a start node;
- a start label;
- optional left/right node labels.

Use it for the first visible fact of a chain:

```blade
<x-translation-workbench::ui.tw-graph.parts.start
    id="resume.center.1.start"
    start-label="1879"
    node-label-left="Born in Ulm|Kingdom of Württemberg"
    label-width="halfLong"
/>
```

## parts.sideways

`parts.sideways` creates a sideways route from the current anchor and returns to
the main flow.

The basic shape is:

```text
arc-in -> bridge -> arc-out
```

With an extension, it can become:

```text
arc-in -> bridge -> arc-out -> stem -> anchor node -> stem
```

Use extension anchors when a long label needs room without forcing collision
logic into a hand-authored sample.

Expected authoring knobs:

- side/direction;
- arc radius;
- bridge length;
- optional extension length;
- node labels left/right;
- label width mode.

## parts.end

`parts.end` closes a hand-authored chain.

The basic shape is:

```text
stem -> cap
```

It should support direction-aware placement and optional end text when needed.

## Labels

Parts use the same text label expectations as the rest of TW Graph:

- predictable fixed width modes;
- explicit line breaks with `|`;
- optional text alignment;
- optional justification;
- hyphenation support for long words.

Width modes:

- `half`;
- default;
- `halfLong`;
- `long`.

Use the smallest width that keeps the graph readable.

## Image Labels

Parts can use image labels when the graph is illustrative.

The image should still be attached to a clear anchor and should not be handled
as a one-off sample hack. The image rendering behavior belongs in reusable
graph components so other graph types can use it later.

Useful image options:

- source path;
- size;
- border width;
- z-index above nearby arcs/bridges.

## Joints

When a full node dot is hidden between connected segments, the visual joint
still needs to look continuous.

Small joint markers or direction markers may be used to cover gaps between:

```text
arc-in -> bridge -> arc-out
```

Those markers should be automatic when normal dots are hidden. Avoid exposing a
wide set of extra props unless authors truly need them.

## Manual Values

Hand-authored samples may use explicit lengths and widths.

That is acceptable because the sample is a designed graph. The values should
still be grouped, readable and local to the sample.

Do not move sample-specific tuning into package defaults unless the behavior is
intended for all graphs.

## What Parts Should Not Do

Parts should not become a second data-driven renderer.

Avoid:

- timeline-chain classification in `parts.*`;
- finding/shared/rekey business logic in `parts.*`;
- copied data-driven compensation logic;
- graph-family defaults hidden inside sample-only parts.

If the graph meaning is semantic and repeatable, prefer `strang.*`.

## Authoring Checklist

When building a hand-authored graph:

1. Start with one `tw-graph`.
2. Use one `parts.chain` per continuous authored sequence.
3. Add `parts.start`.
4. Add each visual movement as a `parts.sideways` or future part.
5. Use extension anchors for labels that need extra room.
6. Close with `parts.end`.
7. Turn on DEV mode and check node counters, bounds and direction handling.
8. Keep sample-only values in the sample.
