{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/filters.blade.php --}}

{{-- Filter Section --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Filters')"
        :description="__('Filter the translation workbench entries by kind, status, or search term.')"
    />
    <div class="grid gap-3 lg:grid-cols-4">

        {{-- Filter Search Input --}}
        <flux:field>
            <flux:tooltip
                class="hover:cursor-help"
                :content="__('Search for a literal, key, suggested key or source path.')"
                placement="top"
            >
                <flux:label>{{ __('Search') }}</flux:label>
            </flux:tooltip>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.search
                        class="text-zinc-400"
                        variant="mini"
                    />
                </flux:input.group.prefix>
                <flux:input
                    wire:model.live.debounce.350ms="search"
                    clearable
                    :placeholder="__('Search literal, key, suggested key or source path')"
                />
            </flux:input.group>
        </flux:field>

        {{-- Filter By Kind --}}
        <flux:field>
            <flux:tooltip
                class="hover:cursor-help"
                :content="__('Filter by kind of translation workbench entry.')"
                placement="top"
            >
                <flux:label>{{ __('Kinds') }}</flux:label>
            </flux:tooltip>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.tag
                        class="text-zinc-400"
                        variant="mini"
                    />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="kind"
                    variant="listbox"
                    clearable
                    searchable
                >
                    <flux:select.option value="">
                        <div class="flex items-center gap-2">
                            <flux:icon.option
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All kinds') }}
                        </div>
                    </flux:select.option>
                    @foreach ($kindCounts as $option => $count)
                        <flux:select.option value="{{ $option }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.option
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ str($option)->headline() }} ({{ $count }})
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Filter By Status --}}
        <flux:field>
            <flux:tooltip
                class="hover:cursor-help"
                :content="__('Filter by status of translation workbench entry.')"
                placement="top"
            >
                <flux:label>{{ __('Statuses') }}</flux:label>
            </flux:tooltip>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.folder
                        class="text-zinc-400"
                        variant="mini"
                    />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="status"
                    variant="listbox"
                    clearable
                    searchable
                >
                    <flux:select.option value="">
                        <div class="flex items-center gap-2">
                            <flux:icon.tag
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All statuses') }}
                        </div>
                    </flux:select.option>
                    @foreach ($statusCounts as $option => $count)
                        <flux:select.option value="{{ $option }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.tag
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ str($option)->headline() }} ({{ $count }})
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Filter By Dynamic --}}
        <flux:field>
            <flux:tooltip
                class="hover:cursor-help"
                :content="__('Filter dynamic translation workbench entries.')"
                placement="top"
            >
                <flux:label>{{ __('Dynamic') }}</flux:label>
            </flux:tooltip>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.braces
                        class="text-zinc-400"
                        variant="mini"
                    />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="dynamicFilter"
                    variant="listbox"
                    clearable
                    searchable
                >
                    <flux:select.option value="">
                        <div class="flex items-center gap-2">
                            <flux:icon.option
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All entries') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="dynamic">
                        <div class="flex items-center gap-2">
                            <flux:icon.braces
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Dynamic') }} ({{ $dynamicCounts['dynamic'] ?? 0 }})
                        </div>
                    </flux:select.option>
                    <flux:select.option value="dynamic_multi">
                        <div class="flex items-center gap-2">
                            <flux:icon.braces
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Dynamic multi') }} ({{ $dynamicCounts['dynamic_multi'] ?? 0 }})
                        </div>
                    </flux:select.option>
                    <flux:select.option value="not_dynamic">
                        <div class="flex items-center gap-2">
                            <flux:icon.option
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Not dynamic') }} ({{ $dynamicCounts['not_dynamic'] ?? 0 }})
                        </div>
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Filter By Dynamic Options --}}
        <flux:field>
            <flux:tooltip
                class="hover:cursor-help"
                :content="__('Filter entries with discovered dynamic option metadata.')"
                placement="top"
            >
                <flux:label>{{ __('Options') }}</flux:label>
            </flux:tooltip>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.list-filter
                        class="text-zinc-400"
                        variant="mini"
                    />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="dynamicOptionFilter"
                    variant="listbox"
                    clearable
                    searchable
                >
                    <flux:select.option value="">
                        <div class="flex items-center gap-2">
                            <flux:icon.option
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All option states') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="discovered">
                        <div class="flex items-center gap-2">
                            <flux:icon.list-filter
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Options discovered') }} ({{ $dynamicOptionCounts['discovered'] ?? 0 }})
                        </div>
                    </flux:select.option>
                    <flux:select.option value="plain_label">
                        <div class="flex items-center gap-2">
                            <flux:icon.list-filter
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Plain label') }} ({{ $dynamicOptionCounts['plain_label'] ?? 0 }})
                        </div>
                    </flux:select.option>
                    <flux:select.option value="translated_label">
                        <div class="flex items-center gap-2">
                            <flux:icon.languages
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Translated label') }} ({{ $dynamicOptionCounts['translated_label'] ?? 0 }})
                        </div>
                    </flux:select.option>
                    <flux:select.option value="unresolved_source">
                        <div class="flex items-center gap-2">
                            <flux:icon.option
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Unresolved source') }} ({{ $dynamicOptionCounts['unresolved_source'] ?? 0 }})
                        </div>
                    </flux:select.option>
                    <flux:select.option value="hardcoded_source">
                        <div class="flex items-center gap-2">
                            <flux:icon.option
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Hardcoded source') }} ({{ $dynamicOptionCounts['hardcoded_source'] ?? 0 }})
                        </div>
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </flux:field>

        {{-- Filter By Workflow --}}
        <flux:field>
            <flux:tooltip
                class="hover:cursor-help"
                :content="__('Filter by translation workflow readiness.')"
                placement="top"
            >
                <flux:label>{{ __('Workflow') }}</flux:label>
            </flux:tooltip>
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.list-filter
                        class="text-zinc-400"
                        variant="mini"
                    />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="workflowFilter"
                    variant="listbox"
                    clearable
                    searchable
                >
                    <flux:select.option value="">
                        <div class="flex items-center gap-2">
                            <flux:icon.option
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('All workflow states') }}
                        </div>
                    </flux:select.option>
                    <flux:select.option value="ready_for_edit">
                        <div class="flex items-center gap-2">
                            <flux:icon.badge-check
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Ready for edit') }} ({{ $workflowCounts['ready_for_edit'] ?? 0 }})
                        </div>
                    </flux:select.option>
                    <flux:select.option value="missing_key">
                        <div class="flex items-center gap-2">
                            <flux:icon.key-round
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Missing translation key') }} ({{ $workflowCounts['missing_key'] ?? 0 }})
                        </div>
                    </flux:select.option>
                    <flux:select.option value="has_key">
                        <div class="flex items-center gap-2">
                            <flux:icon.key-round
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Has translation key') }} ({{ $workflowCounts['has_key'] ?? 0 }})
                        </div>
                    </flux:select.option>
                    <flux:select.option value="editable">
                        <div class="flex items-center gap-2">
                            <flux:icon.pencil
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Editable') }} ({{ $workflowCounts['editable'] ?? 0 }})
                        </div>
                    </flux:select.option>
                    <flux:select.option value="source_exists">
                        <div class="flex items-center gap-2">
                            <flux:icon.languages
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Source saved') }}
                            ({{ $workflowCounts['source_saved'] ?? ($workflowCounts['source_exists'] ?? 0) }})
                        </div>
                    </flux:select.option>
                    <flux:select.option value="target_exists">
                        <div class="flex items-center gap-2">
                            <flux:icon.languages
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Target translation exists') }} ({{ $workflowCounts['target_exists'] ?? 0 }})
                        </div>
                    </flux:select.option>
                    <flux:select.option value="target_missing">
                        <div class="flex items-center gap-2">
                            <flux:icon.languages
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Target translation missing') }} ({{ $workflowCounts['target_missing'] ?? 0 }})
                        </div>
                    </flux:select.option>
                    <flux:select.option value="has_deleted_segments">
                        <div class="flex items-center gap-2">
                            <flux:icon.undo-2
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('Has deleted segments') }} ({{ $workflowCounts['has_deleted_segments'] ?? 0 }})
                        </div>
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </flux:field>
    </div>
</flux:card>
