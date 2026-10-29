{{-- Translate labels at the call site. Each row contains label and value. --}}
@props(['heading' => null, 'rows' => []])

<flux:tooltip.content {{ $attributes->class(['max-w-[24rem] space-y-2 text-left']) }}>
    @if ($heading !== null && $heading !== '')
        <p class="border-b border-zinc-500 pb-1 font-bold">{{ $heading }}</p>
    @endif
    <dl class="grid grid-cols-[auto_minmax(0,1fr)] gap-x-3 gap-y-1">
        @foreach ($rows as $row)
            <dt>{{ $row['label'] }}</dt>
            <dd class="min-w-0 whitespace-pre-wrap wrap-anywhere tabular-nums dark:text-zinc-300">{{ $row['value'] }}</dd>
        @endforeach
    </dl>
</flux:tooltip.content>
