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
])

@php
    $devIdentifier = \Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier::label($id);
    $lines = collect(is_iterable($text) && ! is_string($text) ? $text : [$text])
        ->filter(fn ($line) => filled($line))
        ->take(2)
        ->values();
@endphp

@if ($lines->isNotEmpty())
    <span
        {{ $attributes->class([
            'tw-graph-protocol-primitive',
            'tw-graph-protocol-primitive-text',
            'tw-graph-protocol-primitive-text-label',
            'tw-graph-protocol-primitive-text-label-left' => $side === 'left',
            'tw-graph-protocol-primitive-text-label-top' => $side === 'top',
            'tw-graph-protocol-primitive-text-label-bottom' => $side === 'bottom',
            'tw-graph-protocol-primitive-text-label-right' => ! in_array($side, ['left', 'top', 'bottom'], true),
        ])->style([
            '--tw-graph-protocol-anchor-x: ' . $anchorX,
            '--tw-graph-protocol-anchor-y: ' . $anchorY,
            '--tw-graph-protocol-text-label-offset: ' . $offset,
        ]) }}
        title="{{ $devIdentifier }}"
        data-tw-graph-path="{{ $devIdentifier }}"
        x-on:click.stop="navigator.clipboard?.writeText($el.dataset.twGraphPath)"
    >
        @if ($badge)
            <flux:badge color="{{ $badgeColor }}">
                <span class="inline-flex max-w-48 flex-col items-center gap-0.5 text-center leading-tight">
                    @foreach ($lines as $line)
                        <span @class(['text-xs' => ! $loop->first])>
                            {{ $line }}
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
