@props(['id', 'details', 'length', 'direction', 'startX', 'startY'])
@php
    $value = \Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::evaluateRemExpression((string) $length);
    $horizontal = in_array($direction, ['left-right', 'right-left'], true);
    $sign = in_array($direction, ['right-left', 'top-bottom'], true) ? '-1' : '1';
    $x = $horizontal ? "calc({$startX} + ({$length}) * {$sign} / 2)" : $startX;
    $y = $horizontal ? $startY : "calc({$startY} + ({$length}) * {$sign} / 2)";
    $source = data_get($details, 'inputs.start.source');
    [$status, $markerColor] = match ($details['kind'] ?? 'calculated') {
        'prop' => [__('Set by prop'), '#2563eb'],
        'default' => [__('Default'), '#71717a'],
        default => [__('Calculated'), '#dc2626'],
    };
    $tooltip =
        __($details['reason']) .
        "\n" .
        $details['component'] .
        "\n" .
        __('Length') .
        ': ' .
        $status .
        "\n" .
        __('ID') .
        ': ' .
        $details['owner'] .
        "\n" .
        __('Element') .
        ': ' .
        $id .
        "\n" .
        __('Property') .
        ': ' .
        $details['property'] .
        ($source ? "\n" . __('Source') . ': ' . $source : '') .
        "\n" .
        __('Result') .
        ': ' .
        ($value === null ? __('unresolved') : round($value, 4) . 'rem');
@endphp
@if ($value === null || $value > 0)
    <flux:tooltip
        class="tw-graph-protocol-dev-only"
        data-tw-graph-calculated-marker="{{ $id }}"
        data-tw-graph-length-kind="{{ $details['kind'] ?? 'calculated' }}"
        style="display:none; position:absolute; left:calc(var(--tw-graph-protocol-trunk-x) + {{ $x }}); bottom:calc(var(--tw-graph-protocol-origin-bottom) + {{ $y }}); transform:translate(4px, -4px); width:16px; height:16px; z-index:90;"
        toggleable
    >
        <flux:button
            class="cursor-pointer"
            aria-label="{{ $status }}: {{ $id }}"
            style="color:{{ $markerColor }}"
            size="xs"
            icon:variant="outline"
            icon="drafting-compass"
        />

        <flux:tooltip.content
            class="space-y-3"
            style="max-width:min(34rem, calc(100vw - 2rem));white-space:normal"
        >
            <p>{{ __($details['reason']) }}</p>
            <p class="font-mono text-zinc-300">{{ $details['component'] }}</p>
            <dl
                class="grid gap-x-3 gap-y-1"
                style="grid-template-columns:auto minmax(0,1fr)"
            >
                <dt class="text-zinc-300">{{ __('Length') }}</dt>
                <dd class="font-semibold">{{ $status }}</dd>
                <dt class="text-zinc-300">{{ __('ID') }}</dt>
                <dd
                    class="font-mono"
                    style="overflow-wrap:anywhere"
                >{{ $details['owner'] }}</dd>
                <dt class="text-zinc-300">{{ __('Element') }}</dt>
                <dd
                    class="font-mono"
                    style="overflow-wrap:anywhere"
                >{{ $id }}</dd>
                <dt class="text-zinc-300">{{ __('Property') }}</dt>
                <dd
                    class="font-mono"
                    style="overflow-wrap:anywhere"
                >{{ $details['property'] }}</dd>
                @if ($source)
                    <dt class="text-zinc-300">{{ __('Source') }}</dt>
                    <dd
                        class="font-mono"
                        style="overflow-wrap:anywhere"
                    >{{ $source }}</dd>
                @endif
                <dt class="text-zinc-300">{{ __('Result') }}</dt>
                <dd class="font-mono font-semibold">{{ $value === null ? __('unresolved') : round($value, 4) . 'rem' }}
                </dd>
            </dl>
            <div class="flex justify-end">
                <flux:button
                    data-copy-text="{{ $tooltip }}"
                    size="xs"
                    icon="clipboard"
                    x-on:click.stop="navigator.clipboard?.writeText($el.dataset.copyText)"
                >
                    {{ __('Copy') }}
                </flux:button>
            </div>
        </flux:tooltip.content>
    </flux:tooltip>
@endif
