@props(['example', 'prefixText' => null, 'variant' => 'heading', 'size' => 'sm'])
@aware(['headingCounter' => null])
@php
    if (!$headingCounter instanceof \Gunreip\TranslationWorkbench\Support\HeadingCounter) {
        throw new \LogicException('heading-counter requires a surrounding heading-counter-group.');
    }
    if (!in_array($variant, ['heading', 'accordion'], true)) {
        throw new \InvalidArgumentException('heading-counter variant must be heading or accordion.');
    }
    $number = $headingCounter->number($example);
@endphp

@if ($variant === 'accordion')
    <flux:accordion.heading {{ $attributes->class('relative') }}>
        <x-translation-workbench::ui.common.component-marker name="translation-workbench::ui.common.heading-counter" />
        @if (filled($prefixText))
            {{ $prefixText }}:
        @endif
        {{ $slot }}
        <span
            class="text-zinc-400"
            data-heading-counter-group="{{ $headingCounter->group }}"
            data-heading-counter-example="{{ $example }}"
        >(#{{ $number }})</span>
    </flux:accordion.heading>
@else
    <flux:heading
        :size="$size"
        {{ $attributes->class('relative') }}
    >
        <x-translation-workbench::ui.common.component-marker name="translation-workbench::ui.common.heading-counter" />
        @if (filled($prefixText))
            {{ $prefixText }}:
        @endif
        {{ $slot }}
        <span
            class="text-zinc-400"
            data-heading-counter-group="{{ $headingCounter->group }}"
            data-heading-counter-example="{{ $example }}"
        >(#{{ $number }})</span>
    </flux:heading>
@endif
