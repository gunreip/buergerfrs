{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/table-panel.blade.php --}}

@foreach ($tables as $panelTable)
    <flux:tab.panel name="{{ $panelTable }}">
        @if ($panelTable === $table)
            @include('translation-workbench::livewire.raw-data.table-card')
            @include('translation-workbench::livewire.raw-data.table-summary')
        @endif
    </flux:tab.panel>
@endforeach
