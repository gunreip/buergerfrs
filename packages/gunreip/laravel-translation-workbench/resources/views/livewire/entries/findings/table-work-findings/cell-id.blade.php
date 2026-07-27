{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-id.blade.php --}}

{{-- Table Findings Cell ID --}}
                <flux:table.cell
                    class="bg-white font-mono text-xs tabular-nums dark:bg-zinc-700"
                    sticky
                >
                    #{{ $finding->id }}
                </flux:table.cell>
