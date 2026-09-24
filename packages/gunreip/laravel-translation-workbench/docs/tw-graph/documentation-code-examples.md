# Documentation code sources

Preview examples in Idea to Paper have one authored source: their actual Blade
components. Code boxes use `Documentation\ExampleSource::fromView()` to read that
view and `example('marker')` to extract an explicitly marked example. Do not
maintain an escaped copy or an array of duplicated prop values in the code box.

```blade
{{-- arc-west-north:start --}}
<x-translation-workbench::ui.tw-graph.primitives.arc
    start-anchor="w"
    end-anchor="n"
    color="cyan"
/>
{{-- arc-west-north:end --}}
```

For compact comparisons use the same helper:

```php
$source->changedProps(
    'arc-east-north',
    'arc-west-north',
    'x-translation-workbench::ui.tw-graph.primitives.arc',
);
```

The selected markers must each contain exactly one opening tag of that component.
The comparison reads attribute names and their original quoted values, including
bound PHP arrays. It does not execute PHP. Unchanged props are omitted. `id` and
`graph-id` are excluded by default; callers may supply a different ignore list.
Removed props produce an explicit comment describing the omitted prop rather
than inventing a value. Missing or ambiguous selections and unsupported syntax
raise an error instead of silently displaying incomplete documentation.

Compare the graph wrapper separately when demonstrating inherited canvas props,
for example `node-size` in the joint-arrow examples. Do not include helper-line
changes when the subject of a comparison is an arc or line jump.

The Text Label page shows the complete shared canvas with all seven labels;
its short comparisons use the two-line default label as their baseline.
The Line page keeps full examples for its endpoint combinations and computed
prop differences for the direction/length variations. All preview components
remain individually authored; there is no loop generating the demonstrations.

## Audit and regression coverage

The audit covered 136 Idea to Paper views containing code boxes:

- 112 preview views, with 234 code boxes, derive their code from `ExampleSource`.
- Inventory reads the actual selected component source.
- 23 Deep Reference views contain 28 independent syntax illustrations. These
  explain authoring structures rather than duplicating a rendered preview.
- Real PHP/JavaScript/C/C++/C#/Java examples are independent language source files,
  displayed by the shared language-example component.

`ExampleSourceTest` scans preview pages for source-derived code boxes and checks
all seven Primitive pages, including their computed differences. Helper tests
cover source changes, PHP arrays, removed props and invalid selections. Existing
preview tests continue to check the rendered graph structures.
