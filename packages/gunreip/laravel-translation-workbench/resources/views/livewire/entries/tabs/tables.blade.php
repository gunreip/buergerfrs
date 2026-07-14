{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/tabs/tables.blade.php --}}

<flux:field class="grid gap-4 md:grid-cols-2 xl:grid-cols-10">
    {{-- Callout props are prepared in TranslationWorkbenchEntries::databaseTableCallouts(). --}}
    {{-- packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchEntries.php --}}
    @foreach ($databaseTableCallouts as $callout)
        <flux:callout
            class="{{ trim(($callout['class'] ?? '') . ' h-full') }}"
            color="{{ $callout['color'] }}"
            icon="{{ $callout['icon'] }}"
            heading="{{ $callout['table'] }}"
            text="{{ $callout['text'] }}"
        >
            <flux:callout.text class="flex h-full flex-col">
                <div class="mt-auto flex items-start justify-between gap-3">
                    <div class="mt-2 text-2xl font-semibold tabular-nums">
                        {{ number_format($callout['count']) }}
                    </div>
                </div>
            </flux:callout.text>
        </flux:callout>
    @endforeach
</flux:field>
