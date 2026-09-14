{{-- Configuration overlay only: never register these dimensions as content bounds. --}}
@props([
    'minWidth' => '40rem',
    'minHeight' => '18rem',
    'horizontalPadding' => null,
])

<div
    class="tw-graph-protocol-dev-only tw-graph-protocol-coordinate-only tw-graph-protocol-canvas-dimensions"
    data-tw-graph-canvas-minimum
    aria-hidden="true"
    @style([
        '--tw-graph-protocol-diagnostic-padding-x: ' . $horizontalPadding => $horizontalPadding !== null,
    ])
>
    @if ($horizontalPadding !== null)
        <div class="tw-graph-protocol-canvas-padding" data-tw-graph-canvas-padding></div>
    @endif
    <span class="tw-graph-protocol-canvas-dimensions-label">
        {{ __('Minimum canvas') }}: {{ $minWidth }} × {{ $minHeight }}
        @if ($horizontalPadding !== null)
            · {{ __('Padding (dashed)') }}: x={{ $horizontalPadding }}, y=2rem
        @endif
    </span>
</div>
