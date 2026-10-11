@php
    // Diagnostic settings belong exclusively to the enclosing tw-graph canvas.
    $dev = \Gunreip\TranslationWorkbench\Support\TwGraph\CanvasDiagnostics::current($__env)->dev;
    $attributes = ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->except(['dev', 'dev-mode', 'coordinates']);
@endphp
{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/dev-box.blade.php --}}
{{--
    DEV overlay: bounding box

    Usage:
    <x-translation-workbench::ui.tw-graph.dev-box
        id="catalog.path.merge"
        x="0rem"
        y="0rem"
        width="8rem"
        height="6rem"
        color="amber"
        label="paths.merge"
    />

    Rule:
    This is a pure diagnostic overlay. It must not affect graph geometry.
    Region boxes show an information button with a toggleable tooltip; individual segment boxes disable showLabel
    because the rendered element already owns its tooltip and copy action.
--}}

@props([
    'id' => 'tw-graph.dev-box',
    'x' => '0rem',
    'y' => '0rem',
    'width' => '0rem',
    'height' => '0rem',
    'color' => 'sky',
    'label' => null,
    'component' => null,
    'showLabel' => true,
    'region' => null,
    'regionKind' => 'component',
    'textGroupSlot' => null,
])

@php
    $colorRgb = $region['colorRgb'] ?? \Gunreip\TranslationWorkbench\Support\TranslationWorkbenchColorPalette::rgb($color, '14 165 233');
    $devComponentName = $component;
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($label ?? $id);

@endphp

@if ($dev)
    <span
        data-tw-graph-dev-box="{{ $id }}"
        @if ($textGroupSlot !== null) data-tw-graph-text-group-slot="{{ $textGroupSlot }}" @endif
        @if ($region !== null) data-tw-graph-region="{{ $region['token'] }}" @endif
        @if ($region !== null) data-tw-graph-region-kind="{{ $regionKind }}" @endif
        class="tw-graph-protocol-dev-only group pointer-events-none absolute rounded border border-dashed"
        style="
            left: calc(var(--tw-graph-protocol-trunk-x) + {{ $x }});
            bottom: calc(var(--tw-graph-protocol-origin-bottom) + {{ $y }});
            width: {{ $width }};
            height: {{ $height }};
            border-color: rgb({{ $colorRgb }} / 1);
            {{ $regionKind === 'strang-text-labels' ? 'border-style:solid;' : '' }}
            @if ($regionKind === 'strang-text-labels')
                background-color: rgb({{ $colorRgb }} / 0.06);
            @endif
            {{ $region !== null ? 'visibility:hidden;' : '' }}
        "
    >
        @if (\Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::bool($showLabel, true))
            <span
                data-tw-graph-dev-caption
                class="pointer-events-auto absolute left-1 top-0 z-[90]"
                style="width:max-content;"
            >
                <flux:tooltip
                    toggleable
                    x-data="{}"
                    x-on:click.outside.capture="if ($el.value) $el.value = false"
                >
                    <flux:button
                        size="xs"
                        icon="information-circle"
                        icon:variant="outline"
                        aria-label="{{ __('Bounding box information') }}"
                        style="color:rgb({{ $colorRgb }});"
                    />
                    <flux:tooltip.content style="max-width:min(34rem, calc(100vw - 2rem)); white-space:normal; overflow-wrap:anywhere;">
                        @if (filled($devComponentName))
                            <span class="block font-mono">{{ $devComponentName }}</span>
                            <span class="block font-mono">id="{{ $label ?? $id }}"</span>
                        @else
                            <span class="block font-mono">{{ $devIdentifier }}</span>
                        @endif
                    </flux:tooltip.content>
                </flux:tooltip>
            </span>
        @endif
    </span>
@endif
