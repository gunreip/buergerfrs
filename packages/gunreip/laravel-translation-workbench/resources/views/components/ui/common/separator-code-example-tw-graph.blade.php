@props(['text' => 'TW-Graph code examples'])

<div class="relative mt-4">
    <x-translation-workbench::ui.common.component-marker name="translation-workbench::ui.common.separator-code-example-tw-graph" />
    <flux:separator
        {{ $attributes }}
        :text="__($text)"
    />
</div>
