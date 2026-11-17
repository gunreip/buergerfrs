{{-- length counts retained path characters, excluding the ellipsis and optional position.
     side selects the visible portion: left = beginning, right = end, center = both ends.
     Omit line/column (or pass null) to hide either position independently.
     Set :vsc="false" to omit the VS Code link. Inside an existing tooltip, disable both tooltip and vsc. --}}
@props([
    'path',
    'length' => 40,
    'side' => 'right',
    'line' => null,
    'column' => null,
    'tooltip' => true,
    'vsc' => true,
    'heading' => null,
    'search' => '',
    'caseSensitive' => false,
    'wholeWord' => false,
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

@php
    $showVsc = filter_var($vsc, FILTER_VALIDATE_BOOLEAN) && trim((string) $path) !== '';
@endphp

@if ($showVsc)
    <span class="inline-flex max-w-full min-w-0 items-center gap-1">
@endif
@if (trim((string) $search) !== '')
    <x-translation-workbench::ui.common.text-preview
        :attributes="$attributes"
        :text="str_replace('\\', '/', (string) $path) . $position"
        :length="$length" :side="$side" :tooltip="$tooltip"
        :heading="$heading ?? __('translation-workbench.literal.source_file')" :mono="true"
        :search="$search" :case-sensitive="$caseSensitive" :whole-word="$wholeWord"
    />
@else
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
                    :path="$path"
                    :length="$length"
                    :side="$side"
                    :line="$line"
                    :column="$column"
                    :tooltip="false"
                    :vsc="false"
                />
            </flux:button>
            <x-translation-workbench::ui.common.tooltip-content
                class="max-h-80 overflow-auto"
                :heading="$heading ?? __('translation-workbench.literal.source_file')"
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

@endif
@if ($showVsc)
        <x-translation-workbench::ui.common.vsc-link :path="$path" :line="$line" :column="$column" />
    </span>
@endif
