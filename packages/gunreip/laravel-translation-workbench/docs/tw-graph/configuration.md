# TW Graph Configuration

TW Graph configuration exists to keep visual defaults, calculated compensation
and final layout tuning out of scattered component code.

If a value is expected to influence more than one graph or more than one
dataset, it should usually live in config.

## Config Layers

Configuration resolves from broad to specific:

1. Package defaults.
2. Graph-family defaults.
3. Calculated layout and collision compensation.
4. Layout corrections.
5. Explicit local props.

The exact implementation may evolve, but the order must stay understandable.
When a rendered value looks wrong, it should be possible to tell which layer
provided it.

## Package Defaults

Package defaults live in:

```text
config/tw-graph-defaults.php
```

They define shared baseline values such as:

- line and stem lengths;
- bridge lengths;
- arc sizes;
- label widths;
- label connector lengths;
- debug bound gaps;
- common colors;
- compensation factors and step sizes.

These defaults should be boring, stable and broadly useful.

## Graph-Family Defaults

Graph-family defaults live in files such as:

```text
config/defaults/tw-graph-data-driven.php
```

They override package defaults for a specific graph family. For example, a
data-driven timeline graph may need different merge aggregation behavior than a
hand-authored sample.

Graph-family defaults are still general rules. They should not encode one
timeline chain id unless the file is explicitly for a sample.

## Layout Corrections

Layout corrections live in files such as:

```text
config/layout-corrections/tw-graph-data-driven.php
```

They are the final manual tuning layer after defaults and calculated
compensation.

Corrections should target canonical element references:

```php
return [
    'trunk.center.1.stem-13' => [
        'length_delta' => '2rem',
    ],

    'branch.left.1.bridge1' => [
        'length_delta' => '4rem',
    ],
];
```

The correction should describe what changes, not restate the entire graph.

## Absolute Values Vs. Deltas

Use an absolute value when the author wants to replace the resolved default:

```php
'stem_length' => '8rem',
```

Use a delta when the author wants to modify the resolved value:

```php
'length_delta' => '2rem',
```

Use a factor when the author wants proportional behavior:

```php
'trunk_start_unlabeled_next_stem_factor' => 0.33,
```

Do not silently add hidden values to an absolute prop. If a component receives
`stemLength="8rem"`, the rendered stem should be `8rem` unless another visible
correction or compensation layer changes it.

## Compensation Config

Collision compensation may need tunable values:

- gap between bounds;
- maximum pass count;
- stem distribution step;
- compensation factor;
- preferred compensation direction.

These values belong in defaults, not as literals inside a resolver.

Example meanings:

`debug_bound_box_gap`
: The intended spacing between debug/collision bounds.

`trunk_spacing_compensation_factor`
: Multiplies a calculated delta before distributing it over affected trunk
  stems.

`trunk_spacing_compensation_stem_step`
: Controls distribution granularity, not the final collision delta itself.

`merge_layout.preferred_compensation_direction`
: Selects whether merge compensation should prefer vertical or horizontal
  candidates when both are valid.

## Colors

Colors should also come from config when they carry semantic meaning.

Examples:

- normal trunk timeline labels;
- compacted/chunk timeline labels;
- merge labels;
- aggregate merge labels;
- branch labels;
- rekey source/target labels;
- DEV/debug visuals.

If a color communicates state or meaning, it should not be buried in one Blade
branch.

## Fallback Values

Fallback values should resolve through the same defaults.

A fallback may prevent a crash, but it should not introduce an invisible
hard-coded geometry value. In DEV mode, fallback usage should be visible enough
to explain why the rendered graph differs from the expected route.

## What Does Not Belong In Config

Config should not contain:

- generated coordinates;
- generated protocol output;
- per-request runtime state;
- copied render plans;
- one-off dataset patches disguised as defaults.

Generated data belongs in runtime structures or protocol/debug output.
Intentional final tuning belongs in layout corrections.
