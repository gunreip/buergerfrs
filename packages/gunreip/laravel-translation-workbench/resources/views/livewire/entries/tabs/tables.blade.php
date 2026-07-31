{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/tabs/tables.blade.php --}}

<flux:field class="grid gap-4 md:grid-cols-4 xl:grid-cols-8">
    {{-- Callout props are prepared in TranslationWorkbenchEntries::databaseTableCallouts(). --}}
    {{-- packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchEntries.php --}}
    @foreach ($databaseTableCallouts as $callout)
        @php
            $tableStorageSize = $callout['storage_size'] ?? ['bytes' => null, 'pretty' => '—'];
            $tableStorageSizeText = (string) ($tableStorageSize['pretty'] ?? '—');
        @endphp

        <flux:callout
            class="{{ trim(($callout['class'] ?? '') . ' h-full') }}"
            color="{{ $callout['color'] }}"
            icon="{{ $callout['icon'] }}"
            heading="{{ $callout['table'] }}"
            {{-- text="{{ $callout['text'] }}" --}}
        >
            <flux:callout.text class="hyphens-auto text-xs">
                {{ $callout['text'] }}
            </flux:callout.text>
            <flux:callout.text class="flex h-full flex-col">
                <div class="mt-auto flex items-start justify-between gap-3">
                    <div class="mt-2 flex flex-wrap items-baseline gap-x-3 gap-y-1">
                        <span class="text-2xl font-semibold tabular-nums">
                            {{ number_format($callout['count']) }}
                        </span>

                        <span class="text-sm font-normal text-zinc-500 dark:text-zinc-400">
                            {{ __('ui.storage') }}:
                            <span class="font-mono tabular-nums">{{ $tableStorageSizeText }}</span>
                        </span>
                    </div>
                </div>
            </flux:callout.text>
        </flux:callout>
    @endforeach
</flux:field>
