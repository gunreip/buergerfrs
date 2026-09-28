@props(['text' => 'Props used in these examples'])

<div class="relative mt-4">
    <x-translation-workbench::ui.common.component-marker name="translation-workbench::ui.common.separator-props-used-tw-graph" />
    <flux:separator
        {{ $attributes }}
        :text="__($text)"
    />
</div>
