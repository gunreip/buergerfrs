{{-- resources/views/components/ui/tooltip/simple.blade.php --}}

@props([
    'header' => null,
    'title' => null,
    'texts' => [],
    'text' => null,
    'maxWidth' => 'max-w-[24rem]',
])

@php
    $tooltipHeader = $header ?? $title;
    $tooltipTextValues = $texts !== [] ? $texts : $text;
    $tooltipTexts = collect(is_array($tooltipTextValues) ? $tooltipTextValues : [$tooltipTextValues])
        ->map(static fn(mixed $text): string => trim((string) $text))
        ->filter(static fn(string $text): bool => $text !== '')
        ->values();
    $hasTriggerSlot = trim((string) $slot) !== '';
@endphp

<flux:tooltip {{ $attributes }}>
    @if ($hasTriggerSlot)
        {{ $slot }}
    @else
        <flux:icon.info class="size-3.5 text-zinc-400" />
    @endif

    <flux:tooltip.content class="{{ $maxWidth }}">
        <div class="flex items-start gap-3">
            <flux:icon.information-circle class="mt-0.5 size-4 shrink-0 text-zinc-200" />

            <div class="min-w-0 space-y-2">
                @if (filled($tooltipHeader))
                    <flux:heading class="font-semibold">{{ $tooltipHeader }}</flux:heading>
                @endif

                @foreach ($tooltipTexts as $text)
                    <flux:text class="hyphens-auto text-wrap text-xs">{{ $text }}</flux:text>
                @endforeach
            </div>
        </div>
    </flux:tooltip.content>
</flux:tooltip>
