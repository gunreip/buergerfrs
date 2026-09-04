# TW Graph Element References

Element references identify graph pieces in tooltips, debug bounds, collision
reports and layout-correction config.

The same rendered element should use the same reference everywhere.

## Goals

References should be:

- stable across render/debug/config layers;
- unique inside one graph;
- short enough to read in a table;
- meaningful enough to locate the visual element;
- free of duplicated layer names.

## Preferred Shape

Use this general shape:

```text
{kind}.{side}.{index}.{chapter}.{element}
```

Not every segment needs every part.

Examples:

```text
trunk.center.1.start
trunk.center.1.stem-2
trunk.center.1.end.end-label
merge.left.1.bridge
merge.left.1.extension-2.stem-2
branch.right.3.bridge1
branch.right.3.step.label
branch.right.3.end.end-label
rekey.left.source.1.bridge
rekey.right.target.1.stem-2
```

## Kinds

Use these kind names:

- `trunk`
- `merge`
- `branch`
- `rekey`

For rekey, add the semantic direction as a chapter:

```text
rekey.left.source.1.bridge
rekey.right.target.1.bridge
```

## Sides

Use:

- `left`
- `right`
- `center`

`center` is used for the trunk and other centerline elements.

## Indexes

Indexes are one-based counters within the same kind and side.

Examples:

```text
branch.left.1.bridge1
branch.left.2.bridge1
branch.right.1.bridge1
```

The counter must identify the visible semantic instance, not the internal loop
position of a nested segment. If a branch moves from left to right, it must
receive a reference that matches the rendered side.

## Chapters

Use chapters for meaningful subdivisions:

- `start`
- `end`
- `extension-1`
- `extension-2`
- `source`
- `target`
- `step`

Avoid `main` in public references. The normal/main section is implied when no
extension, source, target, start or end chapter is present.

## Elements

Use element names that describe the controllable visual part:

- `stem-1`
- `stem-2`
- `bridge`
- `bridge1`
- `arc-east-north-1`
- `arc-south-west-2`
- `start-label`
- `end-label`
- `label-1`
- `label-2`
- `anchorNode-start`
- `anchorNode-end`
- `cap`

Prefer `stem` for vertical path sections and `bridge` for sideways graph lines.
Reserve `connector` for helper lines between a node and a label.

## Labels

Labels should reference the node or segment they belong to.

Examples:

```text
trunk.center.1.stem-2.anchorNode-end.label-1
branch.left.1.end.end-label
merge.right.1.arc-south-west-2.end-label
```

If a centered label belongs to an end cap or step, name that semantic element
instead of pretending it is a side label.

## Bounds

Debug bounds may append a bound type:

```text
trunk.center.1.label-bounds
trunk.center.1.start.bounds
branch.left.1.bridge1.bounds
branch.left.1.end.bounds
merge.right.1.extension-2.start-stem.bounds
```

The bound reference should still point back to the canonical graph element.

## Avoid

Avoid references like:

```text
strang.branch-left.1.main.path.branch.bridge1
strang.branch.left.1.paths.branch.step.stem.after.label.end.1.text
strang.branch-end.main.segment
```

These are too noisy for debugging. They repeat implementation layers and often
hide the actual semantic element.

Long internal component ids may still exist where needed, but visible/debug
references should use the canonical form.

## Correction Targets

Layout corrections should use canonical references:

```php
return [
    'branch.left.1.bridge1' => [
        'length_delta' => '4rem',
    ],

    'trunk.center.1.stem-13' => [
        'length_delta' => '2rem',
    ],
];
```

Do not use raw Blade component ids as correction targets unless there is no
canonical reference yet. If that happens, add the canonical reference first.
