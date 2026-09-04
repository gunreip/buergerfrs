# TW Graph Development Rules

These rules exist because TW Graph is meant to become reusable package
infrastructure, not a collection of dataset-specific drawings.

The short version:

```text
Use the component chain. Keep decisions visible. Do not hide special cases.
```

## No Island Solutions

Do not solve a graph problem by wiring low-level pieces directly into a
data-driven builder when an existing component layer should own the behavior.

If a shape belongs to a semantic graph group, implement or extend it in
`strang.*`.

If a shape belongs to a route, implement or extend it in `paths.*`.

If a shape is a reusable visual unit, implement or extend it in `segments.*`.

If a shape is atomic drawing only, keep it in `primitives.*`.

## Respect The Layer Responsibilities

The data-driven builder may decide:

- which semantic groups exist;
- where they attach;
- which labels they receive;
- which defaults/config profiles apply;
- which generated layout plan should be rendered.

The data-driven builder must not decide:

- how an arc is visually built;
- how a branch route is internally chained;
- how a segment label draws its connector;
- how a primitive compensates a domain-specific collision.

Lower layers should not make business decisions. A primitive line should not
know whether a finding was ended, merged or rekeyed.

## No Hidden Dataset Fixes

Do not hard-code behavior for one timeline chain id inside the general
data-driven renderer.

If a dataset exposes a real layout problem, solve it as a general rule:

1. identify the actual collision or missing behavior;
2. add or fix the correct bounds;
3. add a general compensation rule if possible;
4. add a graph-family correction only for final tuning.

Sample pages may be hand-tuned, but those values must stay local to the sample.

## Defaults Belong In Config

Shared values belong in config, not in scattered Blade/PHP literals.

Use:

- `config/tw-graph-defaults.php` for package defaults;
- `config/defaults/*` for graph-family defaults;
- `config/layout-corrections/*` for final correction deltas.

If changing a default does not affect the expected render path, check for hidden
literals or fallback values.

## Fallbacks Must Be Visible

Fallbacks may prevent crashes, but they must not hide wrong routing.

A fallback should:

- use resolved defaults;
- avoid silently occupying an already used anchor;
- be visible in DEV output;
- make it clear which requested reference failed.

If a fallback is used, debugging should answer:

```text
What was requested?
Why did it fail?
Which fallback was used?
What visual effect did that have?
```

## Debug Bounds Are Contracts

Debug bounds that drive layout decisions must stay visible and correct in DEV
mode.

When a component changes, update its bounds from the actual values used by that
component. Do not patch a wrong bound with unrelated offset guesses.

Once a bound is verified for `trunk`, `merge`, `branch` or `rekey`, do not
change it as a side effect of styling or sample tuning.

## Keep DEV Mode Separate From Layout

`dev=false` hides DEV visuals.

It must not disable:

- coordinate calculation;
- registry writes;
- bounds calculation;
- collision detection;
- compensation required for correct rendering.

`coordinates=false` hides coordinate badges only.

It must not disable coordinate calculation.

## Canonical References Everywhere

Use the same element reference in:

- tooltip/debug labels;
- debug bounds table;
- collision reports;
- layout correction config;
- applied compensation output.

Do not introduce parallel naming systems.

Prefer:

```text
branch.left.1.bridge1
trunk.center.1.stem-2
rekey.right.target.1.end.end-label
```

Avoid:

```text
strang.branch-left.1.main.path.branch.bridge1
strang.branch-end.main.segment
```

## Explicit Props Mean Explicit Values

If a graph author sets an explicit length, width or radius, treat it as an
intentional value.

Do not silently add hidden defaults on top of it.

If a value is meant to modify another value, name it as:

- `*_delta`;
- `*_factor`;
- `*_compensation`;
- another clearly modifying term.

## Before Changing A Component

Before editing TW Graph code:

1. Locate the current owner layer.
2. Check whether an existing component already represents the shape.
3. Check which defaults feed the value.
4. Check whether bounds/debug output depend on the value.
5. Make the smallest change in the correct layer.
6. Verify DEV mode and non-DEV rendering still use the same layout data.

## Before Adding A Prop

Add a prop only if it exposes a real authoring need.

Do not add parallel ways to do the same thing. For example, avoid keeping both
`extensionStemLength` and `extensionStemLengths` if one consistent
`stemLength` / `stemContinuation` model is enough.

Prefer `whoWhat` naming:

```text
stemLength
bridgeLength
connectorLength
labelWidth
```

Avoid flipped naming such as `lengthStem` when the context is the stem.

## Manual Tuning

Manual tuning is allowed in hand-authored samples.

Manual tuning in data-driven graphs should go through correction config and
should be visible in DEV output as applied correction.

Do not bury manual tuning inside a builder branch where it looks like general
logic.

## Review Checklist

For every non-trivial TW Graph change, check:

- Does the change live in the right layer?
- Are defaults still coming from config?
- Are fallbacks visible?
- Are element references stable?
- Are debug bounds still correct?
- Does `dev=false` still render a correct graph?
- Does `coordinates=false` only hide coordinate badges?
- Does the change work for more than one dataset or sample?
