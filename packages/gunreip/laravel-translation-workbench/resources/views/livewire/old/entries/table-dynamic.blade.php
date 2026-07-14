{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/table-dynamic.blade.php --}}

{{-- Dynamic Option Discoveries Section --}}
<flux:card class="mt-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <x-ui.headers.card
            :title="__('Dynamic option discoveries')"
            :description="__('Discovered option lists from code, including matched and unmatched dynamic candidates.')"
        />

        <div class="flex flex-wrap items-center gap-2">
            <flux:badge
                size="sm"
                color="sky"
            >
                {{ __('Total') }} {{ number_format($optionDiscoveryCounts['total'] ?? 0) }}
            </flux:badge>
            <flux:badge
                size="sm"
                color="emerald"
            >
                {{ __('Matched') }} {{ number_format($optionDiscoveryCounts['matched'] ?? 0) }}
            </flux:badge>
            <flux:badge
                size="sm"
                color="amber"
            >
                {{ __('Unmatched') }} {{ number_format($optionDiscoveryCounts['unmatched'] ?? 0) }}
            </flux:badge>

            <flux:button
                class="h-6 w-24"
                type="button"
                isnset
                size="xs"
                variant="ghost"
                :icon="$showDynamicTable ? 'chevron-up' : 'chevron-down'"
                :aria-label="$showDynamicTable ? __('Hide dynamic option discoveries') : __('Show dynamic option discoveries')"
                wire:click="toggleDynamicTable"
            >{{ $showDynamicTable ? __('Hide') : __('Show') }}</flux:button>

            <flux:select
                class="min-w-56"
                wire:model.live="optionDiscoveryFilter"
                variant="listbox"
                clearable
                searchable
            >
                <flux:select.option value="">{{ __('All discoveries') }}</flux:select.option>
                <flux:select.option value="matched">
                    {{ __('Matched') }} ({{ $optionDiscoveryCounts['matched'] ?? 0 }})
                </flux:select.option>
                <flux:select.option value="unmatched">
                    {{ __('Unmatched') }} ({{ $optionDiscoveryCounts['unmatched'] ?? 0 }})
                </flux:select.option>
                <flux:select.option value="plain_label">
                    {{ __('Plain label') }} ({{ $optionDiscoveryCounts['plain_label'] ?? 0 }})
                </flux:select.option>
                <flux:select.option value="translated_label">
                    {{ __('Translated label') }} ({{ $optionDiscoveryCounts['translated_label'] ?? 0 }})
                </flux:select.option>
                <flux:select.option value="unresolved_source">
                    {{ __('Unresolved source') }} ({{ $optionDiscoveryCounts['unresolved_source'] ?? 0 }})
                </flux:select.option>
                <flux:select.option value="hardcoded_source">
                    {{ __('Hardcoded source') }} ({{ $optionDiscoveryCounts['hardcoded_source'] ?? 0 }})
                </flux:select.option>
            </flux:select>
        </div>
    </div>

    @if ($showDynamicTable)
        @if (!$optionDiscoveryTableExists)
            <div
                class="mt-4 rounded border border-amber-300 p-4 text-sm text-amber-700 dark:border-amber-700 dark:text-amber-300">
                {{ __('The option discovery table is not available yet. Run the workbench migrations first.') }}
            </div>
        @else
            <flux:table class="mt-4">
                <flux:table.columns>
                    <flux:table.column>{{ __('Entry ID') }}</flux:table.column>
                    <flux:table.column>{{ __('Suggested key') }}</flux:table.column>
                    <flux:table.column>{{ __('State') }}</flux:table.column>
                    <flux:table.column>{{ __('Scope') }}</flux:table.column>
                    <flux:table.column>{{ __('Dynamic ID') }}</flux:table.column>
                    <flux:table.column>{{ __('Suggested dynamic key') }}</flux:table.column>
                    <flux:table.column>{{ __('Source') }}</flux:table.column>
                    <flux:table.column>{{ __('Options') }}</flux:table.column>
                    <flux:table.column>{{ __('Labels') }}</flux:table.column>
                    <flux:table.column>{{ __('Source type') }}</flux:table.column>
                    <flux:table.column>{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($optionDiscoveries as $discovery)
                        @php
                            $matchedEntry = $discovery->matchedEntry;
                            $hasMatchedEntry = $matchedEntry !== null;
                            $discoverySuggestedKey = $discovery->suggested_key
                                ?: $discovery->workbench_suggested_key
                                ?: $matchedEntry?->suggested_key;
                            $canEditDynamic = $hasMatchedEntry
                                && filled($matchedEntry->translation_key)
                                && (bool) $matchedEntry->is_dynamic_multi;
                        @endphp

                        <flux:table.row :key="'translation-workbench-option-discovery-' . $discovery->id">
                            <flux:table.cell>
                                @if ($discovery->matched_entry_id)
                                    <flux:badge
                                        class="tabular-nums"
                                        size="sm"
                                        color="zinc"
                                    >
                                        #{{ $discovery->matched_entry_id }}
                                    </flux:badge>
                                @else
                                    <span class="text-xs text-sky-500 dark:text-sky-400">—</span>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                <div
                                    class="max-w-xs truncate font-mono text-xs"
                                    title="{{ $discoverySuggestedKey ?: '—' }}"
                                >
                                    {{ $discoverySuggestedKey ?: '—' }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex flex-wrap gap-1.5">
                                    <flux:badge
                                        size="sm"
                                        :color="$discovery->matched_entry_id ? 'emerald' : 'amber'"
                                    >
                                        {{ $discovery->matched_entry_id ? __('Matched') : __('Unmatched') }}
                                    </flux:badge>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="font-mono text-xs">{{ $discovery->scope }}</div>
                            </flux:table.cell>

                            <flux:table.cell>
                                @if ($discovery->matched_entry_id)
                                    <flux:badge
                                        class="tabular-nums"
                                        size="sm"
                                        color="violet"
                                    >
                                        #{{ $discovery->matched_entry_id }}
                                    </flux:badge>
                                @else
                                    <span class="text-xs text-sky-500 dark:text-sky-400">—</span>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell>
                                <div
                                    class="max-w-sm truncate font-mono text-xs"
                                    title="{{ $discovery->suggested_dynamic_key ?: '—' }}"
                                >
                                    {{ $discovery->suggested_dynamic_key ?: '—' }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div
                                    class="max-w-md truncate font-mono text-xs"
                                    title="{{ $discovery->source_path }}{{ $discovery->source_line ? ':' . $discovery->source_line : '' }}"
                                >
                                    {{ $discovery->source_path }}{{ $discovery->source_line ? ':' . $discovery->source_line : '' }}
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    ${{ $discovery->options_variable }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge
                                    size="sm"
                                    color="sky"
                                >
                                    {{ number_format($discovery->options_count ?? 0) }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge
                                    size="sm"
                                    :color="$discovery->label_usage === 'plain_label' ? 'amber' : 'emerald'"
                                >
                                    {{ str((string) $discovery->label_usage)->headline() }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge
                                    size="sm"
                                    :color="$discovery->source_type === 'unresolved' ? 'amber' : 'zinc'"
                                >
                                    {{ str((string) $discovery->source_type)->headline() }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex items-center gap-1">
                                    <flux:button
                                        type="button"
                                        size="sm"
                                        variant="ghost"
                                        icon="eye"
                                        title="{{ $hasMatchedEntry ? __('Review matched workbench entry') : __('Check whether a matching workbench entry exists.') }}"
                                        :aria-label="__('Review')"
                                        wire:click="openReviewModalFromOptionDiscovery({{ $discovery->id }})"
                                    />
                                    <flux:button
                                        type="button"
                                        size="sm"
                                        variant="ghost"
                                        icon="pencil"
                                        title="{{ $canEditDynamic ? __('Edit dynamic values') : __('A matched dynamic multi entry with translation key is required before editing dynamic values.') }}"
                                        :disabled="!$canEditDynamic"
                                        :aria-label="__('Edit dynamic values')"
                                        wire:click="openDynamicEditModalFromOptionDiscovery({{ $discovery->id }})"
                                    />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="11">
                                <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __('No dynamic option discoveries found.') }}
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        @endif
    @endif
</flux:card>
