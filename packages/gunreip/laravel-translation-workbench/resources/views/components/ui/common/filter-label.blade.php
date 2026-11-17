@props(['heading', 'text'])

<flux:tooltip>
    <flux:label tabindex="0">
        <flux:callout.heading>{{ $heading }}</flux:callout.heading>
    </flux:label>
    <x-translation-workbench::ui.common.tooltip-content :heading="$heading" :text="$text" />
</flux:tooltip>
