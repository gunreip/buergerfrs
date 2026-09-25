# Canvas-owned diagnostics

Set `dev` and `coordinates` only on `x-translation-workbench::ui.tw-graph`.

```blade
<x-translation-workbench::ui.tw-graph :dev="true" :coordinates="true">
    <x-translation-workbench::ui.tw-graph.segments.path
        id="example.path"
        :node-end="true"
    />
</x-translation-workbench::ui.tw-graph>
```

The canvas component creates an immutable `CanvasDiagnostics` instance before
Blade renders its slot. Descendants read this context through the view component
stack; nested and successive canvases each have their own settings. There is no
process-global diagnostic state.

`dev=false` disables graph diagnostics, including coordinates. With `dev=true`,
`coordinates` determines coordinate visibility. Documentation preview tools may
hide or show the available overlays without changing the authored components.

`dev`, `devMode`, `dev-mode`, `coordinates`, and `segment.dev` are not child
component configuration options. Removed local attributes or array keys cannot
override the canvas. Set the options on the enclosing canvas instead. Components
rendered outside a canvas have diagnostics disabled.

Counter eligibility is separate: a node may intentionally have no counter, for
example at a shared join. The internal counter primitive's `visible` prop controls
that individual marker; it does not enable DEV mode.

Documentation code boxes extract their code from the authored preview source.
No additional diagnostic props are needed on segments, paths, parts, or strangs.
