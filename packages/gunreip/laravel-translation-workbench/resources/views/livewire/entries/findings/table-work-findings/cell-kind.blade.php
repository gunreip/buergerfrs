{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-kind.blade.php --}}

{{-- Table Findings Cell Kind --}}
                <flux:table.cell>
                    <div class="space-y-1">
                        <x-ui.tooltip.simple
                            :title="$kindLabel"
                            :text="$kindTooltip"
                        >
                            {{-- Badge Kind --}}
                            <flux:badge
                                size="sm"
                                color="{{ $kindColor }}"
                            >
                                {{ $kindLabel }}
                            </flux:badge>
                        </x-ui.tooltip.simple>
                        @if (filled($functionName))
                            {{-- Badge Function Name --}}
                            <flux:badge
                                size="sm"
                                variant="subtle"
                            >
                                {{ $functionName }}
                            </flux:badge>
                        @endif
                    </div>
                </flux:table.cell>
