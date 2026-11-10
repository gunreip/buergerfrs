{{-- Lines contain number, before, match, after and focus; interpretation stays with the caller. --}}
@props([
    'lines' => [],
    'text' => '',
    'maxHeight' => '16rem',
    'lineNumbers' => true,
    'wrap' => false,
    'focusColor' => 'green',
])

<div
    {{ $attributes->class(['min-w-0 max-w-full overflow-auto rounded-lg bg-zinc-800 p-2 font-mono text-sm'])->style(['max-height: '.$maxHeight]) }}
    tabindex="0"
>
    @forelse ($lines as $line)
        <div @class([
            'min-w-max whitespace-pre' => !$wrap,
            'whitespace-pre-wrap wrap-anywhere' => $wrap,
            'bg-red-500/15' => ($line['focus'] ?? false) && $focusColor === 'red',
            'bg-green-500/15' => ($line['focus'] ?? false) && $focusColor === 'green',
        ])>@if ($lineNumbers)<span class="mr-3 inline-block min-w-10 text-right opacity-60">{{ $line['number'] }}</span>@endif{{ $line['before'] }}@if ($line['match'] !== '')<mark class="rounded bg-green-300 px-0.5 font-semibold text-green-950">{{ $line['match'] }}</mark>@endif{{ $line['after'] }}</div>
    @empty
        <pre @class(['m-0 font-mono text-sm', 'min-w-max whitespace-pre' => !$wrap, 'whitespace-pre-wrap wrap-anywhere' => $wrap])><code>{{ $text }}</code></pre>
    @endforelse
</div>
