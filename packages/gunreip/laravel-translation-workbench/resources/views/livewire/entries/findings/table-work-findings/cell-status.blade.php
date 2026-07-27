{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-status.blade.php --}}

{{-- Table Findings Cell Status --}}
                <flux:table.cell class="text-center">
                    {{-- Badge Finding State --}}
                    <flux:badge
                        size="sm"
                        color="{{ $finding->status === 'active' ? 'green' : 'amber' }}"
                    >
                        {{ $finding->status }}
                    </flux:badge>
                </flux:table.cell>
