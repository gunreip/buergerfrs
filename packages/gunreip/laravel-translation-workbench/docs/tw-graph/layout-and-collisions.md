# TW Graph Layout And Collisions

Layout and collision handling turn a graph plan into a readable drawing. The
system should first render from clear defaults, then measure what really exists,
then compensate only where the measured layout requires it.

## Pipeline

The layout pipeline should be:

1. Resolve central and graph-family defaults.
2. Build the semantic graph plan.
3. Render or model the component dimensions from real resolved values.
4. Build debug bounds and sub-bounds.
5. Detect potential collisions.
6. Apply calculated compensation.
7. Re-check relevant collisions.
8. Apply final layout corrections from config.
9. Render debug output showing what happened.

The same values used to render components must be used to calculate bounds.

## Bounds

Bounds are rectangles in graph coordinate space.

They should include:

- actual stem, bridge and arc dimensions;
- actual label width mode;
- label padding;
- step labels;
- end labels;
- cap areas when they affect the visible footprint.

They should not be built from guessed offsets when the component already knows
its real lengths and label modes.

## Main Bounds And Sub-Bounds

Main bounds are useful for overview diagnostics, but they are often too broad
for final collision decisions.

Sub-bounds are more precise and should drive most compensation decisions.

Examples:

- trunk start label bounds;
- trunk middle label bounds;
- trunk end label bounds;
- merge start/stem label bounds;
- merge bridge bounds;
- branch bridge bounds;
- branch step bounds;
- branch end bounds;
- rekey target bridge bounds;
- rekey source trunk-side label bounds.

If a main/main collision is reported but no sub/sub collision exists, the graph
may already be visually correct.

## Collision Classes

Use `main/main` for broad diagnostic overlap.

Use `main/sub` when a broad area overlaps a specific component area.

Use `sub/sub` when two specific visible areas overlap. This is usually the best
input for automatic compensation.

## Trunk Compensation

Trunk compensation should be used when a side-strang collision can be resolved
by shifting later trunk content.

The resolver should identify the trunk stem that separates the colliding
elements and distribute the required delta across suitable stems where possible.

This is preferable to blindly extending one bridge when the real problem is
vertical crowding between side strangs.

Applied trunk compensation must be visible in the debug table.

## Branch Compensation

Branch compensation should compare branch bounds against:

- other branches on the same side;
- relevant trunk label sub-bounds;
- branch end sub-bounds;
- branch bridge sub-bounds.

When two branches collide vertically, prefer the general spacing calculation
over hand-coded branch bridge values.

When a branch collides with trunk labels, use the relevant trunk sub-bound, not
only the full trunk label-inclusive box.

## Merge Compensation

Merge layouts first use baseline merge planning:

- direct merge entries for small origin sets;
- aggregate entries only when the origin count requires them;
- vertical staggering from merge layout defaults.

Only after that should collision compensation run.

Merge compensation should react to concrete sub-bound collisions. Broad merge
main bounds may be reported, but should not automatically trigger large
vertical changes by themselves.

## Rekey Compensation

`rekey-target` behaves similarly to branch for bridge/body/end bounds.

Trunk-label collisions can be compensated by extending the rekey-target bridge
when that moves the label footprint away from the trunk label footprint.

`rekey-source` has a special trunk-side label case. Extending its bridge does
not necessarily move that label out of collision. Valid strategies include:

- mirror the label to the other side when that side is free;
- keep the collision visible when no safe automatic strategy exists;
- introduce a clearer anchor rule later if the case appears often.

## Re-Checks

Some compensations create new spacing relationships. After applying calculated
compensation, run a focused re-check on affected bounds.

The process should stop when no relevant collision remains or when the maximum
configured pass count is reached.

If the maximum pass count is reached, the unresolved collision should stay
visible in DEV output.

## Applied Values

Applied compensation should be concrete:

```text
trunk.center.1.stem-13 +2.75rem
branch.left.1.bridge1 +4rem
rekey.right.target.1.bridge +6rem
```

Do not mix calculated-but-not-applied suggestions into applied compensation.

## Layout Corrections

Layout corrections are the final manual layer.

Use them when the general collision system produced a correct but visually
imperfect result.

Corrections should:

- use canonical element references;
- describe deltas, not duplicate full component props;
- remain graph-family scoped unless the page is a hand-authored sample;
- be visible in DEV output.

## Anti-Patterns

Avoid:

- adding a hidden offset to a component without debug output;
- fixing one timeline chain id inside the general builder;
- using one full trunk bound when a specific trunk sub-bound exists;
- compensating from stale pre-compensation bounds;
- rendering debug boxes from different values than the actual graph;
- removing debug boxes because they are visually noisy.
