{{-- packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/primitives/text.blade.php --}}
{{--
    Primitive: text

    Usage:
    <x-translation-workbench::ui.tw-graph.primitives.text
        text="Label"
        anchor-x="0rem"
        anchor-y="0rem"
    />

    Rule:
    Text renders only text. Segments decide whether it belongs to a node label,
    path label, name, start/end marker, or DEV marker.
--}}

@props([
    'id' => 'text',
    'text' => null,
    'anchorX' => '0rem',
    'anchorY' => '0rem',
    'side' => 'right',
    'offset' => '0rem',
    'badge' => true,
    'badgeColor' => 'cyan',
    'long' => false,
    'halfLong' => false,
    'half' => false,
    'align' => 'center',
    'justify' => false,
    'maxLines' => 3,
])

@php
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id);
    $resolvedAlign = in_array($align, ['left', 'right'], true) ? $align : 'center';
    $resolvedTextAlign = (bool) $justify ? 'justify' : $resolvedAlign;
    $resolvedMaxLines = max(1, (int) $maxLines);
    $lines = collect(is_iterable($text) && !is_string($text) ? $text : [$text])
        ->filter(fn($line) => filled($line))
        ->take($resolvedMaxLines)
        ->values();
@endphp

@if ($lines->isNotEmpty())
    <span
        data-tw-graph-path="{{ $devIdentifier }}"
        title="{{ $devIdentifier }}"
        {{ $attributes->class([
                'tw-graph-protocol-primitive',
                'tw-graph-protocol-primitive-text',
                'tw-graph-protocol-primitive-text-label',
                'tw-graph-protocol-primitive-text-label-center' => $side === 'center',
                'tw-graph-protocol-primitive-text-label-left' => $side === 'left',
                'tw-graph-protocol-primitive-text-label-top' => $side === 'top',
                'tw-graph-protocol-primitive-text-label-bottom' => $side === 'bottom',
                'tw-graph-protocol-primitive-text-label-right' => !in_array($side, ['center', 'left', 'top', 'bottom'], true),
            ])->style([
                '--tw-graph-protocol-anchor-x: ' . $anchorX,
                '--tw-graph-protocol-anchor-y: ' . $anchorY,
                '--tw-graph-protocol-text-label-offset: ' . $offset,
            ]) }}
        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
    >
        @if ($badge)
            <flux:badge color="{{ $badgeColor }}">
                <span
                    @class([
                        'tw-graph-protocol-primitive-text-badge wrap-anywhere inline-flex flex-col gap-0.5 whitespace-normal leading-tight hyphens-auto',
                        'items-start text-left' => $resolvedAlign === 'left',
                        'items-end text-right' => $resolvedAlign === 'right',
                        'items-center text-center' => $resolvedAlign === 'center',
                        'text-justify' => (bool) $justify,
                        'w-96' => (bool) $long,
                        'w-72' => ! (bool) $long && (bool) $halfLong,
                        'w-24' => ! (bool) $long && ! (bool) $halfLong && (bool) $half,
                        'w-48' => ! (bool) $long && ! (bool) $halfLong && ! (bool) $half,
                    ])
                    style="text-align: {{ $resolvedTextAlign }};"
                >
                    @foreach ($lines as $line)
                        <span @class(['tw-graph-protocol-primitive-text-line wrap-anywhere text-wrap', 'block w-full' => (bool) $justify, 'text-xs' => !$loop->first])>
                            @if (is_array($line) && data_get($line, 'ordinal'))
                                <span>{{ data_get($line, 'ordinal.number') }}</span><sup class="text-[0.58em] leading-none">{{ data_get($line, 'ordinal.suffix') }}</sup>
                                <span>{{ data_get($line, 'text') }}</span>
                            @else
                                {{ $line }}
                            @endif
                        </span>
                    @endforeach
                </span>
            </flux:badge>
        @else
            <span class="font-mono text-[0.625rem] text-zinc-100">
                {{ $lines->join(' / ') }}
            </span>
        @endif
    </span>
@endif
