{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-tabs.blade.php --}}

        <flux:tabs
            class="min-w-max"
            scrollable
            scrollable:fade
            scrollable:scrollbar="hide"
            wire:model.live="activeTable"
        >
            @foreach ($tables as $tableName)
                {{-- Tabs --}}
                <flux:tab name="{{ $tableName }}">
                    <span class="inline-flex items-center gap-2">
                        <span>{{ $tableName }}</span>
                        {{-- Tabs Counter Badge --}}
                        <flux:badge
                            class="tabular-nums"
                            size="sm"
                            variant="subtle"
                        >
                            {{ number_format($tableCounts[$tableName] ?? 0) }}
                        </flux:badge>
                    </span>
                </flux:tab>
            @endforeach
        </flux:tabs>
