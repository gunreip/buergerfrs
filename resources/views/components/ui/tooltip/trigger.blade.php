{{-- resources/views/components/ui/tooltip/trigger.blade.php --}}

@php
    $context = $tooltipContext();
@endphp

<span
    {{ $attributes->class([
        'tooltip-trigger inline-flex cursor-help',
        "tooltip-trigger--{$context}" => $context !== null,
    ])->merge([
        'data-tooltip-trigger' => 'true',
        'data-tooltip-title' => $tooltipTitle(),
        'data-tooltip' => $tooltipText(),
    ]) }}
    @if ($tooltipField() !== null) data-form-field="{{ $tooltipField() }}" @endif
    @if ($tooltipRequired()) data-tooltip-required="true" @endif
    @if ($tooltipDelay() !== null) data-tooltip-delay="{{ $tooltipDelay() }}" @endif
    @if ($context !== null) data-tooltip-context="{{ $context }}" @endif
    @if ($tooltipActionJson() !== null) data-tooltip-action='{{ $tooltipActionJson() }}' @endif
>
    {{ $slot }}
</span>
