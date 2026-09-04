# TW Graph Data Driven

Data-driven TW Graphs turn translation-workbench records into a render plan.
The builder should decide what the data means, then hand that meaning to
`strang.*` components.

It must not become a place where individual arcs, stems and bridges are wired
together for one dataset.

## Render Flow

The expected flow is:

1. Load the selected timeline chain dataset.
2. Classify records by lifecycle meaning.
3. Build a graph plan from semantic groups.
4. Resolve defaults.
5. Build bounds from the same values that will render the graph.
6. Detect collisions.
7. Apply calculated compensation.
8. Apply explicit layout corrections.
9. Render the graph and DEV/debug information.

Each dataset gets recalculated when it is selected. Compensation from one
dataset must not leak into another dataset.

## Semantic Groups

The builder should map data to semantic graph groups:

- the selected key lifecycle becomes `strang.trunk`;
- shared origins become `strang.merge` and `strang.merge-extension`;
- large origin sets may become aggregate merge sections;
- ended records become `strang.branch`;
- moved/rekeyed records become `strang.rekey-source` or
  `strang.rekey-target`;
- high-volume timeline events may become compacted trunk entries.

This keeps the data layer focused on meaning and lets the component chain keep
ownership of visual structure.

## Trunk Planning

The trunk should be built from the required timeline entries, side-strang
anchors, rekey labels and layout corrections.

`pathCount` should be the result of the plan. It should not start from a
fixed DEV-era minimum and then delete empty paths afterwards.

Empty trunk stems may still be neutralized as a safety net, but only when they
have no labels, no side-strang attach point, no compressed/step meaning and no
applied correction.

## Merge Planning

Merge candidates are distributed by graph-family defaults. Small origin sets
should be rendered directly; larger sets can use aggregate merge sections.

The important rule is that aggregation is data-driven by count and layout
policy, not forced into a fixed slot when a real merge-extension would be more
accurate.

The current model is:

- up to the direct threshold: render real merge/merge-extension entries;
- above the threshold: render head entries, aggregate middle entries and tail
  entries;
- never render fake `finding ID ?` placeholders just to fill a shape.

## Branch Planning

Branches should represent records that diverged, ended or no longer arrive at
the trunk target.

Branch placement and spacing are calculated from the current dataset. If
multiple branches on the same side would collide, compensation should be
calculated generally from their bounds instead of hard-coding a bridge length
for one chain id.

## Rekey Planning

`rekey-source` means the current key came from another key.

`rekey-target` means the current key continued into another key.

Both directions should show enough information to understand the transition:

- source/target key id;
- source and target translation keys;
- first/last relevant timestamps;
- origin/source/literal information where available.

The full target/source lifecycle can be inspected by opening the related
timeline chain. The current graph should show the transition without trying to
inline the whole other graph.

## Event Compaction

Large event groups should not be hidden, but they should not flood the graph
either.

A compacted event entry should show:

- event type;
- event count;
- timestamp range or sample timestamp;
- a clear sample event id;
- key/finding ids where available.

Compacted entries should remain visible in chronological order together with
normal entries. They should not be moved into a separate block that breaks the
timeline reading order.

## Debug Output

In DEV mode, data-driven graphs may show:

- Debug bounds table;
- collision deltas;
- applied compensations;
- merge origin outcomes;
- finding/sample details;
- coordinate badges when `coordinates=true`.

When `dev=false`, DEV-only output is hidden. Layout calculations still run.

## Configuration

Use config for graph-family behavior:

- central defaults in `config/tw-graph-defaults.php`;
- data-driven defaults in `config/defaults/tw-graph-data-driven.php`;
- final correction deltas in
  `config/layout-corrections/tw-graph-data-driven.php`.

Config values should describe general behavior for the data-driven graph type.
They should not silently become one-off fixes for a single timeline chain.

## Correction Rules

Calculated compensation should handle the normal cases first.

Layout corrections are allowed for final tuning, but their targets must use the
canonical element reference system:

```php
'trunk.center.1.stem-13' => ['length_delta' => '2rem'],
'branch.left.1.bridge1' => ['length_delta' => '4rem'],
```

A correction should be visible in DEV output as an applied correction. If a
value is only theoretical and not applied, it belongs in collision diagnostics,
not in applied compensation.
