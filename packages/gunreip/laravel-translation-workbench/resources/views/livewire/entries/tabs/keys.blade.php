{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/tabs/keys.blade.php --}}

<flux:field class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    {{-- Key coverage callout props are prepared in TranslationWorkbenchEntries::keyCoverageCallouts(). --}}
    {{-- packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchEntries.php --}}
    @foreach ($keyCoverageCallouts as $callout)
        <flux:callout
            class="col-span-1 h-full"
            color="{{ $callout['color'] }}"
            icon="{{ $callout['icon'] }}"
            heading="{{ $callout['title'] }}"
            {{-- text="{{ $callout['text'] }}" --}}
        >
            <flux:callout.text class="hyphens-auto text-xs">
                {{ $callout['text'] }}
            </flux:callout.text>
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ number_format($callout['count']) }}
            </flux:callout.text>
        </flux:callout>
    @endforeach
</flux:field>
