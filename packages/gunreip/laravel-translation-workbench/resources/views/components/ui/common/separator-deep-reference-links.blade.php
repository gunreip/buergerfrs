@props(['text' => 'Deep reference links'])

<div class="relative mt-4">
    <x-translation-workbench::ui.common.component-marker
        name="translation-workbench::ui.common.separator-deep-reference-links"
    />
    <flux:separator
        {{ $attributes }}
        :text="__($text)"
    />
</div>
