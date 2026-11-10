{{-- length counts retained characters; side: left = beginning, right = end, center = both.
     Preview whitespace is collapsed; the tooltip preserves the original text.
     min-saving is the minimum character reduction including the three dots.
     A tooltip is rendered only when the preview is actually shortened.
     prefix-key stays beside the beginning of the preview; the preview text can still wrap.
     Set :mono="true" for keys/code; it applies to both preview and full tooltip text.
     Normal text uses automatic hyphenation; pass lang for the text's language.
     Set :tooltip="false" when the caller provides its own button and tooltip. --}}
@props([
    'text',
    'length' => 80,
    'minSaving' => 15,
    'side' => 'left',
    'tooltip' => true,
    'heading' => null,
    'prefixKey' => null,
    'mono' => false,
])

@php
    $characterCount = filter_var($length, FILTER_VALIDATE_INT, ['options' => ['min_range' => 2]]);
    $minimumSaving = filter_var($minSaving, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($characterCount === false || !in_array($side, ['left', 'right', 'center'], true)) {
        throw new \InvalidArgumentException('text-preview requires length >= 2 and side left, right or center.');
    }
    if ($minimumSaving === false) {
        throw new \InvalidArgumentException('text-preview requires min-saving >= 0.');
    }
    $hasPrefix = $prefixKey !== null && (string) $prefixKey !== '';
    $textClasses = $mono ? 'font-mono break-all hyphens-none' : 'break-normal wrap-break-word hyphens-auto';
    $fullText = (string) $text;
    $preview = trim(preg_replace('/\s+/u', ' ', $fullText) ?? $fullText);
    $saving = mb_strlen($preview) - ($characterCount + mb_strlen('...'));
    $isShortened = $saving > 0 && $saving >= $minimumSaving;
    if ($isShortened) {
        $preview = match ($side) {
            'left' => mb_substr($preview, 0, $characterCount) . '...',
            'right' => '...' . mb_substr($preview, -$characterCount),
            'center' => mb_substr($preview, 0, (int) ceil($characterCount / 2)) .
                '...' .
                mb_substr($preview, -(int) floor($characterCount / 2)),
        };
    }
@endphp

<div
    {{ $attributes->class(['min-w-0 max-w-full align-top', $hasPrefix ? 'inline-flex flex-nowrap items-start gap-2' : 'inline-block']) }}>
    @if ($hasPrefix)
        <flux:callout.heading class="shrink-0 whitespace-nowrap tabular-nums">
            {{ $prefixKey }}
        </flux:callout.heading>
    @endif
    @if ($tooltip && $isShortened)
        <flux:tooltip
            class="min-w-0"
            toggleable
        >
            <flux:button
                class="h-auto! px-0! py-0! max-w-full justify-start text-left"
                variant="ghost"
                size="sm"
                :aria-label="$heading ?? __('Show full text')"
            >
                <div @class([$textClasses, 'min-w-0 whitespace-normal'])>
                    <flux:callout.text>
                        {{ $preview }}
                    </flux:callout.text>
                </div>
            </flux:button>
            <x-translation-workbench::ui.common.tooltip-content
                class="max-h-80 overflow-auto"
                :heading="$heading ?? __('Full text')"
            >
                <span @class([$textClasses, 'whitespace-normal'])>
                    {!! nl2br(e($fullText)) !!}
                </span>
            </x-translation-workbench::ui.common.tooltip-content>
        </flux:tooltip>
    @else
        <flux:callout.text>
            <span @class([$textClasses, 'min-w-0 whitespace-normal'])>{{ $preview }}</span>
        </flux:callout.text>
    @endif
</div>
