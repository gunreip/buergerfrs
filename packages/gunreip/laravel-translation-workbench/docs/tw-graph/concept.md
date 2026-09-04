# TW Graph Concept

TW Graph is intended to become a reusable package feature. Its structure must
therefore stay general enough for different datasets, graph types and
hand-authored examples.

The most important rule is simple: solve layout and rendering through the
component chain, not through isolated special cases.

## Source Of Truth

The Blade component tree is the render source. A `tw-graph` is authored or
assembled, then rendered from its components.

Protocol output belongs to diagnostics. It may be used to inspect coordinates,
verify generated bounds, review fingerprints or reproduce a state, but it should
not become the primary render input for normal graph rendering.

Every graph needs its own stable graph id so registries, coordinates and
protocol/debug output remain separated when multiple graphs are rendered on one
page.

## Responsibilities

The data-driven layer decides what a graph means:

- which datasets become a trunk, merge, branch or rekey;
- which outcomes are grouped or aggregated;
- which labels describe records, counts, timestamps and states;
- which graph-family defaults and layout corrections apply.

`strang.*` decides how semantic groups are assembled:

- `strang.trunk` for the main timeline;
- `strang.merge` and merge extensions for origins entering a shared key;
- `strang.branch` and branch extensions for ended or diverging records;
- `strang.rekey-source` and `strang.rekey-target` for moved keys.

`paths.*` decides how route-specific pieces are chained.

`segments.*` decides how reusable visual behaviors are composed from
primitives. Examples are `segments.step`, `segments.end`, labels and path
segments.

`primitives.*` draws atoms only. A primitive should not know why a translation
record exists or why a collision must be compensated.

## Defaults And Overrides

Defaults should resolve in a predictable order:

1. Central package defaults.
2. Graph-family defaults, for example data-driven defaults.
3. Calculated layout and collision compensation.
4. Layout-correction config for final explicit deltas.
5. Local props that were intentionally set by the graph author.

Fallbacks are allowed to prevent crashes, but they must use the same defaults
system instead of hidden hard-coded values. In DEV mode a fallback path should
be visible or traceable so it is clear whether the desired route or a fallback
route was used.

Local props should be treated as intentional values. Hidden additions on top of
explicit props should only happen when the prop is clearly named as a delta,
factor or compensation.

## Element References

Tooltips, debug bounds, collision reports and layout-correction config should
use the same canonical element references.

The preferred form is concise and meaningful:

```text
trunk.center.1.stem-2
branch.left.1.bridge1
branch.left.1.end.end-label
merge.right.1.extension-2.stem-2
rekey.left.source.1.bridge
rekey.right.target.1.end.end-label
```

Avoid filler layers in visible/debug ids when they do not add meaning, for
example repeated `strang`, `paths`, `main` or duplicated path names.

Long component/render ids may still exist internally when needed, but the
public diagnostic and correction target should be stable, short and unique.

## Debug Bounds

Debug bounds are part of the layout contract, not disposable test drawings.
When a layout rule uses a footprint, the same footprint must be available in
DEV output so the result can be checked.

Verified debug bounds must not be changed as a side effect of unrelated visual
tuning.

Important bound groups:

- `strang.trunk`: raw trunk bounds, label-inclusive bounds, start sub-bounds,
  middle sub-bounds and end sub-bounds.
- `strang.merge` and `strang.merge-extension`: bridge bounds, start/stem
  label-inclusive bounds and tail-stem bounds.
- `strang.rekey-source`: same start/stem/bridge logic as merge, plus the
  trunk-side rekey label zone.
- `strang.branch`, `strang.branch-extension` and `strang.rekey-target`:
  bridge bounds, body/stem label bounds, step bounds and end bounds.

The bounds must be calculated from actual component dimensions, including label
width mode, padding and step/end labels.

## Collision Compensation

Collision detection should first report potential collisions, then apply
general compensation rules where possible.

The debug table should distinguish:

- `Collision Delta`: the currently calculated overlap;
- `Applied Compensation`: the correction that was actually applied;
- `Collision Type`: whether the collision is main/main, main/sub or sub/sub.

Compensation must be calculated from the current dataset each time the graph is
built. A fix for one dataset must not become a hidden special case for all
other datasets.

Config-based layout corrections are for final explicit deltas after general
calculation, for example:

```php
'branch.left.1.bridge1' => ['length_delta' => '4rem'],
'trunk.center.1.stem-13' => ['length_delta' => '2rem'],
```

Those corrections should be graph-family rules unless a sample is intentionally
hand-authored.

## Data-Driven Graphs

Data-driven graphs should build from data meaning first:

- a key timeline becomes `strang.trunk`;
- shared origins become `strang.merge` or merge extensions;
- large origin groups may become aggregate merge sections;
- ended records may become `strang.branch`;
- moved keys become `strang.rekey-source` or `strang.rekey-target`.

The builder should create a render plan from the dataset. It should not
manually assemble low-level arcs, bridges and stems when a higher-level
component already represents that concept.

## Hand-Authored Samples

Samples may use `parts.*` and explicit values because they are examples of
manual graph authoring. They should still follow the same naming, defaults and
debug rules where applicable.

Good samples show how a user can build a graph intentionally without copying a
data-driven renderer or editing generated protocol output.

## Working Discipline

Before adding or changing a component:

1. Inspect the existing layer that should own the behavior.
2. Prefer extending the proper component in the chain.
3. Keep the data-driven builder focused on meaning and orchestration.
4. Keep visual composition in `strang`, `paths`, `segments` and `primitives`.
5. Document new defaults, debug bounds and compensation behavior when they
   become part of the contract.
