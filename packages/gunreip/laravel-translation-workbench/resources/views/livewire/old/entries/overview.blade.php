{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/overview.blade.php --}}

    {{-- Overview Card --}}
    <flux:card class="mt-6">
        <x-ui.headers.card
            :title="__('Overview')"
            :description="__('Translation workbench entries overview.')"
        />
        {{-- Callout Section --}}
        <div class="grid gap-3 md:grid-cols-5">
            <flux:callout
                color="red"
                icon="info"
            >
                {{-- Callout Total --}}
                <flux:callout.heading class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Total') }}
                </flux:callout.heading>
                <flux:callout.text class="font-semibold">
                    {{ number_format($total) }}
                </flux:callout.text>
            </flux:callout>

            {{-- Callout Filtered --}}
            <flux:callout
                color="green"
                icon="sliders-horizontal"
            >
                <flux:callout.heading class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Filtered') }}
                </flux:callout.heading>
                <flux:callout.text class="font-semibold">
                    {{ number_format($filteredTotal) }}
                </flux:callout.text>
            </flux:callout>

            {{-- Callout Open --}}
            <flux:callout
                color="sky"
                icon="folder-open"
            >
                <flux:callout.heading class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Open') }}
                </flux:callout.heading>
                <flux:callout.text class="font-semibold">
                    {{ number_format($statusCounts['open'] ?? 0) }}
                </flux:callout.text>
            </flux:callout>

            {{-- Callout Obsolete --}}
            <flux:callout
                color="pink"
                icon="eraser"
            >
                <flux:callout.heading class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Obsolete') }}
                </flux:callout.heading>
                <flux:callout.text class="font-semibold">
                    {{ number_format($statusCounts['obsolete'] ?? 0) }}
                </flux:callout.text>
            </flux:callout>

            {{-- Callout Occurrences --}}
            <flux:callout
                color="amber"
                icon="map-pin"
            >
                <flux:callout.heading class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Occurrences') }}
                </flux:callout.heading>
                <flux:callout.text class="font-semibold">
                    {{ number_format($occurrenceCounts['total'] ?? 0) }}
                </flux:callout.text>
                <flux:callout.text class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Active') }} {{ number_format($occurrenceCounts['active'] ?? 0) }}
                    ·
                    {{ __('Stale') }} {{ number_format($occurrenceCounts['stale'] ?? 0) }}
                </flux:callout.text>
            </flux:callout>
        </div>
    </flux:card>

