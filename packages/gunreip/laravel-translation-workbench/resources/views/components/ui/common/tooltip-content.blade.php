{{-- Pass translated text; an array renders one paragraph per entry. --}}
@props(['heading', 'text' => []])

<flux:tooltip.content {{ $attributes->class(['max-w-[24rem] space-y-2 hyphens-auto text-justify']) }}>
    <p class="border-b border-zinc-500 pb-1 font-bold">{{ $heading }}</p>
    @foreach (is_array($text) ? $text : [$text] as $paragraph)
        <p class="tabular-nums dark:text-zinc-300">{{ $paragraph }}</p>
    @endforeach
    @if (isset($slot) && $slot->isNotEmpty())
        <div class="tabular-nums dark:text-zinc-300">{{ $slot }}</div>
    @endif
</flux:tooltip.content>
