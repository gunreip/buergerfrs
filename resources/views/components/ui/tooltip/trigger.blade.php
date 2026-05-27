{{-- resources/views/components/ui/tooltip/trigger.blade.php --}}

<span
    {{ $attributes->class(['tooltip-trigger inline-flex cursor-help'])->merge([
        'data-tooltip-trigger' => 'true',
        'data-tooltip-title' => $tooltipTitle(),
        'data-tooltip' => $tooltipText(),
    ]) }}
    @if ($tooltipField() !== null) data-form-field="{{ $tooltipField() }}" @endif
    @if ($tooltipRequired()) data-tooltip-required="true" @endif
    @if ($tooltipDelay() !== null) data-tooltip-delay="{{ $tooltipDelay() }}" @endif
>
    {{ $slot }}
</span>
