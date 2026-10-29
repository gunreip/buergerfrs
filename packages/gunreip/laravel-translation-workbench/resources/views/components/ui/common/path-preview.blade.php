{{-- length counts retained path characters, excluding the ellipsis and optional position.
     side selects the visible portion: left = beginning, right = end, center = both ends.
     Omit line/column (or pass null) to hide either position independently.
     Set :tooltip="false" inside an existing tooltip. --}}
@props([
    'path',
    'length' => 40,
    'side' => 'right',
    'line' => null,
    'column' => null,
    'tooltip' => true,
    'heading' => null,
])

@php
    $characterCount = filter_var($length, FILTER_VALIDATE_INT, ['options' => ['min_range' => 2]]);
    if ($characterCount === false || !in_array($side, ['left', 'right', 'center'], true)) {
        throw new \InvalidArgumentException('path-preview requires length >= 2 and side left, right or center.');
    }

    $displayPath = str_replace('\\', '/', (string) $path);
    if (mb_strlen($displayPath) > $characterCount) {
        $displayPath = match ($side) {
            'left' => mb_substr($displayPath, 0, $characterCount) . '...',
            'right' => '...' . mb_substr($displayPath, -$characterCount),
            'center' => mb_substr($displayPath, 0, (int) ceil($characterCount / 2)) .
                '...' .
                mb_substr($displayPath, -(int) floor($characterCount / 2)),
        };
    }
    $position =
        ($line !== null && $line !== '' ? ':' . $line : '') . ($column !== null && $column !== '' ? ':' . $column : '');
@endphp

<span {{ $attributes->class(['inline-block min-w-0 max-w-full whitespace-normal text-left']) }}>
    @if ($tooltip)
        <flux:tooltip toggleable>
            <flux:button
                class="h-auto! px-0! py-0! max-w-full justify-start text-left"
                variant="ghost"
                size="sm"
                :aria-label="$heading ?? __('Show full source path')"
            >
                <x-translation-workbench::ui.common.path-preview
                    class="mx-1"
                    :path="$path"
                    :length="$length"
                    :side="$side"
                    :line="$line"
                    :column="$column"
                    :tooltip="false"
                />
            </flux:button>
            <x-translation-workbench::ui.common.tooltip-content
                class="max-h-80 overflow-auto"
                :heading="$heading ?? __('Source file')"
            >
                <span class="break-all font-mono">
                    {{ $path . $position }}
                </span>
            </x-translation-workbench::ui.common.tooltip-content>
        </flux:tooltip>
    @else
        <flux:callout.text>
            <code class="min-w-0 hyphens-none whitespace-normal break-all text-left">
                {{ $displayPath . $position }}
            </code>
        </flux:callout.text>
    @endif
</span>
