# Numbered example headings

Wrap related code and preview headings in one `heading-counter-group`. It emits
no HTML wrapper and owns a fresh counter for each render. Separate group
instances, including nested groups, count independently.

```blade
<x-translation-workbench::ui.common.heading-counter-group group="primitives-line">
    <x-translation-workbench::ui.common.heading-counter
        example="bottom-top-4"
        :prefix-text="__('Complete example')"
        size="sm"
    >
        {{ __('bottom-top · 4rem') }}
    </x-translation-workbench::ui.common.heading-counter>

    <x-translation-workbench::ui.common.heading-counter
        example="bottom-top-4"
        class="px-3 pt-3"
        size="sm"
    >
        {{ __('bottom-top · 4rem') }}
    </x-translation-workbench::ui.common.heading-counter>
</x-translation-workbench::ui.common.heading-counter-group>
```

These headings display `Complete example: bottom-top · 4rem (#1)` and
`bottom-top · 4rem (#1)`. Use stable `example` keys, not manual numbers. The first
occurrence assigns the next number; repeated keys reuse it, even in a different
preview order. Hidden accordion content still participates in server rendering.

`prefix-text` maps to the Blade prop `prefixText`. Pass an already translated
string; the colon is added only for a nonempty prefix. Headings and prefixes
retain Blade escaping.

The default variant uses `flux:heading` and forwards `size` (default `sm`) and
attributes such as `class`. Set `variant="accordion"` inside a
`flux:accordion.item` to use `flux:accordion.heading` with its native interaction.
Accordion styling comes from Flux; `size` applies only to regular headings.
Both variants use the same numbering. No request-global, persistent or static
counter is used, so Livewire refreshes and tab changes start fresh.

Example content stays handwritten. Existing callout icons and layout are
independent of the counter components.
