{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/table-entries.blade.php --}}

{{-- Table Section --}}
<flux:card class="mt-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <x-ui.headers.card
            :title="__('Entries')"
            :description="__('Translation workbench entries.')"
        />

        <div class="flex items-center gap-2">
            <flux:button
                class="w-42 h-10"
                type="button"
                size="xs"
                variant="ghost"
                :icon="$showEntriesTable ? 'chevron-up' : 'chevron-down'"
                :aria-label="$showEntriesTable ? __('Hide entries') : __('Show entries')"
                wire:click="toggleEntriesTable"
            >{{ $showEntriesTable ? __('Hide') : __('Show') }}</flux:button>

            {{-- perPage Selector --}}
            <flux:select
                class="w-28"
                wire:model.live="perPage"
                variant="listbox"
            >
                {{-- perPage Option 10 --}}
                <flux:select.option value="10">
                    <div class="flex items-center gap-2">
                        <flux:icon.rows-3
                            class="text-zinc-400"
                            variant="mini"
                        /> 10
                    </div>
                </flux:select.option>
                {{-- perPage Option 25 --}}
                <flux:select.option value="25">
                    <div class="flex items-center gap-2">
                        <flux:icon.rows-3
                            class="text-zinc-400"
                            variant="mini"
                        /> 25
                    </div>
                </flux:select.option>
                {{-- perPage Option 50 --}}
                <flux:select.option value="50">
                    <div class="flex items-center gap-2">
                        <flux:icon.rows-3
                            class="text-zinc-400"
                            variant="mini"
                        /> 50
                    </div>
                </flux:select.option>
                {{-- perPage Option 100 --}}
                <flux:select.option value="100">
                    <div class="flex items-center gap-2">
                        <flux:icon.rows-3
                            class="text-zinc-400"
                            variant="mini"
                        /> 100
                    </div>
                </flux:select.option>
            </flux:select>

            {{-- Reset Button --}}
            <flux:button
                type="button"
                variant="ghost"
                icon="rotate-ccw"
                wire:click="clearFilters"
            >
                {{ __('Reset') }}
            </flux:button>
        </div>
    </div>

    @if ($showEntriesTable)
        {{-- Table --}}
        <flux:table>

            {{-- Table Header --}}
            <flux:table.columns class="rounded-t-lg">

                {{-- Table Column ID --}}
                <flux:table.column
                    class="ml-2 rounded-tl-lg bg-white dark:bg-zinc-700"
                    align="center"
                    sticky
                    variant="strong"
                    sortable
                    :sorted="$sortField === 'id'"
                    :direction="$sortDirection"
                    wire:click="sortBy('id')"
                >
                    <span class="ml-2">{{ __('ID') }}</span>
                </flux:table.column>

                {{-- Table Column Kind --}}
                <flux:table.column
                    sortable
                    :sorted="$sortField === 'kind'"
                    :direction="$sortDirection"
                    wire:click="sortBy('kind')"
                >
                    {{ __('Kind') }}
                </flux:table.column>

                {{-- Table Column Current Value --}}
                <flux:table.column
                    sortable
                    :sorted="$sortField === 'current_value'"
                    :direction="$sortDirection"
                    wire:click="sortBy('current_value')"
                >
                    {{ __('Current value') }}
                </flux:table.column>

                {{-- Table Column Suggested Key --}}
                <flux:table.column
                    sortable
                    :sorted="$sortField === 'suggested_key'"
                    :direction="$sortDirection"
                    wire:click="sortBy('suggested_key')"
                >
                    {{ __('Suggested key') }}
                </flux:table.column>

                {{-- Table Column Source --}}
                <flux:table.column
                    sortable
                    :sorted="$sortField === 'source'"
                    :direction="$sortDirection"
                    wire:click="sortBy('source')"
                >
                    {{ __('Source') }}
                </flux:table.column>

                {{-- Table Column Occurrences --}}
                <flux:table.column>
                    {{ __('Occurrences') }}
                </flux:table.column>

                {{-- Table Column Seen --}}
                <flux:table.column
                    sortable
                    :sorted="$sortField === 'last_seen_at'"
                    :direction="$sortDirection"
                    wire:click="sortBy('last_seen_at')"
                >
                    {{ __('Seen') }}
                </flux:table.column>

                {{-- Table Column Status --}}
                <flux:table.column
                    sortable
                    :sorted="$sortField === 'status'"
                    :direction="$sortDirection"
                    wire:click="sortBy('status')"
                >
                    {{ __('Status') }}
                </flux:table.column>

                {{-- Table Column Actions --}}
                <flux:table.column>
                    {{ __('Actions') }}
                </flux:table.column>
            </flux:table.columns>

            {{-- Table Rows --}}
            <flux:table.rows>
                @forelse ($entries as $entry)
                    @php
                        $workflowState = $entryWorkflowStates[$entry->id] ?? [
                            'has_key' => filled($entry->translation_key),
                            'source_exists' => false,
                            'target_exists' => false,
                            'has_deleted_segments' => filled($entry->deleted_segments),
                        ];
                        $dynamicOptionDiscovery = data_get($entry->meta, 'dynamic_option_discovery');
                        $dynamicOptionLabelUsage = data_get($dynamicOptionDiscovery, 'label_usage');
                        $dynamicOptionSourceType = data_get($dynamicOptionDiscovery, 'source_type');
                    @endphp

                    {{-- Table Row --}}
                    <flux:table.row :key="'translation-workbench-entry-' . $entry->id">

                        {{-- Table Cell ID --}}
                        <flux:table.cell
                            class="bg-white dark:bg-zinc-700"
                            align="end"
                            variant="strong"
                            sticky
                        >
                            <span class="font-mono text-xs">{{ $entry->id }}</span>
                        </flux:table.cell>

                        {{-- Table Cell Kind --}}
                        <flux:table.cell>
                            <flux:badge size="sm">
                                {{ str($entry->kind)->headline() }}
                            </flux:badge>
                        </flux:table.cell>

                        {{-- Table Cell Current Value --}}
                        <flux:table.cell>
                            <div class="max-w-xl">
                                <div
                                    class="truncate font-medium"
                                    title="{{ $entry->literal_text ?: ($entry->translation_key ?: __('Dynamic expression')) }}"
                                >
                                    {{ $entry->literal_text ?: ($entry->translation_key ?: __('Dynamic expression')) }}
                                </div>
                                <div class="truncate text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $entry->raw_expression }}
                                </div>
                            </div>
                        </flux:table.cell>

                        {{-- Table Cell Suggested Key --}}
                        <flux:table.cell>
                            <div class="max-w-xl">
                                <div
                                    class="truncate font-mono text-xs"
                                    title="{{ $entry->suggested_key ?: '—' }}"
                                >{{ $entry->suggested_key ?: '—' }}</div>
                            </div>
                        </flux:table.cell>

                        {{-- Table Cell Source --}}
                        <flux:table.cell>
                            <div
                                class="max-w-md truncate font-mono text-xs"
                                title="{{ $entry->source_path }}{{ $entry->source_line ? ':' . $entry->source_line : '' }}"
                            >
                                {{ $entry->source_path }}{{ $entry->source_line ? ':' . $entry->source_line : '' }}
                            </div>
                        </flux:table.cell>

                        {{-- Table Cell Occurrences --}}
                        <flux:table.cell>
                            <div class="flex items-center gap-1.5">
                                <flux:badge
                                    size="sm"
                                    color="sky"
                                >
                                    {{ __('Total') }} {{ number_format($entry->occurrences_count ?? 0) }}
                                </flux:badge>
                                <flux:badge
                                    size="sm"
                                    color="emerald"
                                >
                                    {{ __('Active') }} {{ number_format($entry->active_occurrences_count ?? 0) }}
                                </flux:badge>
                                <flux:badge
                                    size="sm"
                                    color="amber"
                                >
                                    {{ __('Stale') }} {{ number_format($entry->stale_occurrences_count ?? 0) }}
                                </flux:badge>
                            </div>
                        </flux:table.cell>

                        {{-- Table Cell Seen --}}
                        <flux:table.cell>
                            <div class="text-sm">{{ $entry->last_seen_at?->format('Y-m-d H:i') ?: '—' }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Scans') }}
                                {{ $entry->scan_count }}</div>
                        </flux:table.cell>

                        {{-- Table Cell Status --}}
                        <flux:table.cell>
                            <div class="flex min-w-0 flex-wrap gap-1.5">
                                <flux:badge
                                    size="sm"
                                    :color="$entry->status === 'obsolete' ? 'zinc' : 'emerald'"
                                >
                                    {{ str($entry->status)->headline() }}
                                </flux:badge>
                                <flux:badge
                                    size="sm"
                                    :color="$workflowState['has_key'] ? 'emerald' : 'amber'"
                                >
                                    {{ $workflowState['has_key'] ? __('Key') : __('No key') }}
                                </flux:badge>

                                @if ($workflowState['has_key'])
                                    <flux:badge
                                        size="sm"
                                        :color="$workflowState['source_exists'] ? 'emerald' : 'amber'"
                                    >
                                        {{ $workflowState['source_exists'] ? __('Source saved') : __('No source') }}
                                    </flux:badge>
                                    <flux:badge
                                        size="sm"
                                        :color="$workflowState['target_exists'] ? 'emerald' : 'amber'"
                                    >
                                        {{ $workflowState['target_exists'] ? __('Target') : __('No target') }}
                                    </flux:badge>
                                @endif
                                @if ($workflowState['has_deleted_segments'])
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('Deleted segments') }}
                                    </flux:badge>
                                @endif
                                @if ($dynamicOptionDiscovery)
                                    <flux:badge
                                        size="sm"
                                        :color="$dynamicOptionLabelUsage === 'plain_label' ? 'amber' : 'sky'"
                                    >
                                        {{ $dynamicOptionLabelUsage === 'plain_label' ? __('Plain label') : __('Option labels') }}
                                    </flux:badge>
                                    <flux:badge
                                        size="sm"
                                        :color="$dynamicOptionSourceType === 'unresolved' ? 'amber' : 'emerald'"
                                    >
                                        {{ str((string) $dynamicOptionSourceType)->headline() }}
                                    </flux:badge>
                                @endif
                            </div>
                        </flux:table.cell>

                        {{-- Table Cell Actions --}}
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button
                                    type="button"
                                    size="sm"
                                    variant="ghost"
                                    icon="eye"
                                    :aria-label="__('Review')"
                                    wire:click="openReviewModal({{ $entry->id }})"
                                />
                                <flux:button
                                    type="button"
                                    title="{{ blank($entry->translation_key) ? __('Set a translation key in review before editing translation values.') : __('Edit') }}"
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil"
                                    :disabled="blank($entry->translation_key)"
                                    :aria-label="__('Edit')"
                                    wire:click="openEditModal({{ $entry->id }})"
                                />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    {{-- Table Row Empty Table --}}
                    <flux:table.row>
                        <flux:table.cell colspan="9">
                            <div class="py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('No translation workbench entries found.') }}
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>

        </flux:table>

        <div class="mt-4">
            {{-- Pagination --}}
            <flux:pagination :paginator="$entries" />

        </div>
    @endif
</flux:card>
