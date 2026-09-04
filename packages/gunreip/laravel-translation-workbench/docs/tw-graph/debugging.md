# TW Graph Debugging

Debugging is part of the TW Graph system. It is not decorative output and it is
not a temporary test layer.

When a graph uses calculated layout, the debug output must make that layout
traceable.

## DEV Mode

`dev=true` may show:

- dev node counters;
- debug bound boxes;
- debug bound numbers;
- collision summaries;
- applied compensation summaries;
- data-driven inspection tables.

`dev=false` hides these DEV-only visuals and tables. It must not disable
coordinate calculation, bounds calculation, collision detection or layout
compensation that is needed for correct graph rendering.

## Coordinates

`coordinates=true` enables coordinate badges in DEV mode.

`coordinates=false` hides coordinate badges only. It must not stop coordinate
calculation, registry writes or canvas sizing.

The coordinates switch is a DEV sub-tool. If DEV mode is unavailable, coordinate
badges are unavailable visually as well, but the underlying calculations still
run.

## Debug Bounds

Debug bounds are the measured rectangles that layout rules use to reason about
space.

If a collision resolver compares two areas, those exact areas should be visible
or listed in DEV output. A debug box that is only guessed visually is not useful
for collision work.

Important bound types:

- `strang`: the visual line/cap/arc footprint of the strang;
- `label`: the label-inclusive footprint;
- `start`: start-area bounds;
- `middle`: trunk middle-area bounds between side-strang anchors;
- `end`: end-area bounds;
- `bridge`: bridge-only bounds;
- `step`: step label and step-stem bounds;
- `tail`: tail-stem bounds before an arc or end.

Bounds must include the actual label width mode:

- `half`;
- default;
- `halfLong`;
- `long`.

They must also include label padding when that padding affects the real visual
footprint.

## Debug Bound Table

The debug bound table should make graph layout auditable without needing to
hover every element.

Recommended columns:

- `#`: visible debug number that maps table rows to graph boxes;
- `Scope`: trunk, merge, branch, rekey source, rekey target;
- `Side`: left, right or center;
- `Type`: strang, label, bridge, start, middle, end, step or tail;
- `Element ID`: canonical reference;
- `X`, `Y`, `Width`, `Height`: resolved dimensions;
- `Collision`: yes or no;
- `Collision Type`: main/main, main/sub or sub/sub;
- `Collision Delta`: calculated overlap that has not necessarily been applied;
- `Applied Compensation`: correction that was actually applied.

The table may be filterable, for example to show only rows with collisions, but
the full data should remain accessible in DEV mode.

## Collision Types

`main/main`
: Broad bound boxes overlap. This is useful as an early warning, but it does
  not always mean a visible label or bridge collision exists.

`main/sub`
: A broad bound overlaps a more specific bound. This is more relevant than a
  pure main/main warning, but still needs context.

`sub/sub`
: Specific visual areas overlap. This is the strongest signal and usually the
  best trigger for automatic compensation.

## Potential Vs. Actual Collisions

Potential collisions should remain reportable even when compensation has already
resolved them. This makes it clear why a stem or bridge was extended.

Actual visible collisions should be checked against the most specific available
bounds. For example, a trunk label may overlap a branch label even when the
larger trunk and branch boxes already look close.

## Applied Compensation

Applied compensation must describe what actually changed:

```text
trunk.center.1.stem-13 +2.75rem
branch.left.1.bridge1 +4rem
rekey.right.target.1.bridge +6rem
```

Do not list theoretical values as applied compensation. Theoretical values
belong in `Collision Delta`.

## Fallbacks

Fallbacks are allowed when a graph would otherwise fail, but they must be
traceable.

Fallbacks should:

- use resolved defaults instead of hidden literals;
- preserve rendering when possible;
- be visible in DEV diagnostics;
- avoid silently choosing an already occupied anchor when a clearer fallback is
  available.

## Do Not Hide Debug Contracts

Once a bound box has been checked and accepted, it becomes part of the layout
contract for that component. Do not remove or change it as a side effect of
unrelated styling work.

If a bound becomes wrong because a component changed, fix the bound calculation
from the same real values used by the component. Do not patch it with unrelated
offset guesses.
