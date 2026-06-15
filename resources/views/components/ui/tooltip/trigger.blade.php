{{-- resources/views/components/ui/tooltip/trigger.blade.php --}}

@php
    $context = $tooltipContext();

    $tooltipSlotHtml = isset($tooltip) ? trim((string) $tooltip) : '';

    $hasTooltipSlot = $tooltipSlotHtml !== '';

    $tooltipFallbackText = $hasTooltipSlot
        ? trim((string) preg_replace('/\s+/', ' ', strip_tags($tooltipSlotHtml)))
        : $tooltipText();
@endphp

<span
    {{ $attributes->class(['tooltip-trigger inline-flex cursor-help', "tooltip-trigger--{$context}" => $context !== null])->merge([
            'data-tooltip-trigger' => 'true',
            'data-tooltip-title' => $tooltipTitle(),
            'data-tooltip' => $tooltipFallbackText,
            'data-tooltip-content' => $hasTooltipSlot ? 'slot' : 'text',
        ]) }}
    @if ($tooltipField() !== null) data-form-field="{{ $tooltipField() }}" @endif
    @if ($tooltipRequired()) data-tooltip-required="true" @endif
    @if ($tooltipDelay() !== null) data-tooltip-delay="{{ $tooltipDelay() }}" @endif
    @if ($context !== null) data-tooltip-context="{{ $context }}" @endif
    @if ($tooltipActionJson() !== null) data-tooltip-action='{{ $tooltipActionJson() }}' @endif
>
    {{ $slot }}

    @if ($hasTooltipSlot)
        <template data-tooltip-content-template>
            {!! $tooltip !!}
        </template>
    @endif
</span>
