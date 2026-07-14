{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/tabs/timeline.blade.php --}}

<flux:field class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
    {{-- Timeline health callout props are prepared in TranslationWorkbenchEntries::timelineHealthCallouts(). --}}
    {{-- packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchEntries.php --}}
    @foreach ($timelineHealthCallouts as $callout)
        <flux:callout
            class="col-span-2 h-full"
            color="{{ $callout['color'] }}"
            icon="{{ $callout['icon'] }}"
            heading="{{ $callout['title'] }}"
            text="{{ $callout['text'] }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format($callout['count']) }}
            </flux:callout.text>
        </flux:callout>
    @endforeach
</flux:field>
