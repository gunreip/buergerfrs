<flux:modal
    class="w-full max-w-4xl"
    name="workbench-scan-results"
    wire:model="scanOpen"
>
    @if ($scanRows || $preservedRows || $captureRows)
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Scan results') }}</flux:heading>
            <flux:text class="break-all">{{ $selectedScan }}</flux:text>
            <flux:error name="scanReview" />
            @if ($captureRows)
                @include('translation-workbench::livewire.translation-workbench.scan.captured-source')
            @elseif ($preservedRows)
                @include('translation-workbench::livewire.translation-workbench.scan.preserved-findings')
            @else
                @include('translation-workbench::livewire.translation-workbench.scan.results-table')
            @endif
        </div>
    @endif
</flux:modal>
