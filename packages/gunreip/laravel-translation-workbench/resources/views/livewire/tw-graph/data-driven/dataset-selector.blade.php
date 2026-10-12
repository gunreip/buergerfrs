@php
    // Supplied by the including dataset view; null when no dataset is selected.
    /** @var array<string, mixed>|null $mainRow */
    $mainRow = $mainRow ?? null;
@endphp

<form
    class="mt-4 items-start gap-3"
    wire:submit="loadDataset"
>
    <flux:field>
        <div class="grid grid-cols-4 gap-3">

            <flux:input.group class="w-40">

                <flux:input
                    {{-- type="number" --}}
                    wire:model="datasetIdInput"
                    {{-- min="1" --}}
                    {{-- step="1" --}}
                    clearable
                    :aria-label="__('Dataset ID')"
                    :placeholder="__('Dataset ID')"
                />

                <flux:button
                    class="px-3"
                    type="submit"
                    icon="search"
                    variant="outline"
                    wire:loading.attr="disabled"
                />
                <flux:button
                    type="submit"
                    variant="outline"
                    wire:loading.attr="disabled"
                >
                    {{ __('Load dataset') }}
                </flux:button>
            </flux:input.group>
            <flux:error name="datasetIdInput" />

            @if (!empty($datasetHistory))
                <div class="col-span-2 w-full">
                    <flux:input.group>
                        <flux:button
                            variant="outline"
                            wire:click="randomDataset"
                            wire:loading.attr="disabled"
                        >
                            {{ __('Random dataset') }}
                        </flux:button>
                        <flux:button
                            class="px-3"
                            variant="outline"
                            icon="refresh-cw"
                            wire:click="randomDataset"
                            wire:loading.attr="disabled"
                        />

                        <flux:select
                            class="max-w-full"
                            variant="listbox"
                            searchable
                            clearable
                            wire:model.live="selectedHistoryId"
                            :aria-label="__('Recent datasets')"
                        >
                            @foreach ($datasetHistory as $historyEntry)
                                <flux:select.option
                                    class="text-wrap-anywhere"
                                    value="{{ $historyEntry['id'] }}"
                                    icon="database"
                                >
                                    {{ $historyEntry['label'] }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>

                        @if ($mainRow)
                            <flux:button
                                class="ml-auto px-3"
                                icon="refresh-ccw"
                                wire:click="reloadDataset"
                                wire:loading.attr="disabled"
                            />
                            <flux:button
                                class="ml-auto"
                                wire:click="reloadDataset"
                                wire:loading.attr="disabled"
                            >
                                {{ __('Reload ID #:id', ['id' => $mainRow['id']]) }}
                            </flux:button>
                        @endif
                        {{-- </flux:input.group.suffix> --}}
                    </flux:input.group>
                </div>
            @endif

            <div class="col-span-1 w-full max-w-xl">
                <flux:select
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="selectedCriticalDatasetId"
                    :aria-label="__('Critical datasets')"
                    :placeholder="__('Critical datasets')"
                >
                    @foreach (\Gunreip\TranslationWorkbench\Livewire\TwGraphDataDrivenDatasets::CRITICAL_DATASETS as $criticalId => $criticalDescription)
                        <flux:select.option
                            value="{{ $criticalId }}"
                            icon="database"
                        >
                            #{{ $criticalId }} · {{ __($criticalDescription) }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            {{-- @if ($mainRow)
        <flux:button
            class="ml-auto"
            type="button"
            icon="refresh-ccw"
            wire:click="reloadDataset"
            wire:loading.attr="disabled"
        >
            {{ __('Reload ID #:id', ['id' => $mainRow['id']]) }}
        </flux:button>
    @endif --}}
        </div>
    </flux:field>
</form>
