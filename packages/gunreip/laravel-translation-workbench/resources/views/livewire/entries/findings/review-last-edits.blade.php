{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/review-last-edits.blade.php --}}

@php
    $sourceLocaleHeader = strtoupper((string) ($sourceMainLocale ?? 'en'));
    $targetLocaleHeader = strtoupper((string) ($targetMainLocale ?? app()->getLocale()));
@endphp

<div class="mt-4 space-y-4">
    <flux:callout
        color="sky"
        icon="list-checks"
    >
        <flux:callout.heading>{{ __('Review last edits') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Recently saved normal translation values for the active target language, shown next to the source-language literal for consistency checks.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:table container:class="overflow-x-auto">
        <flux:table.columns>
            <flux:table.column>
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('ui.translation.translation-key') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('ui.translation.translation-key')"
                        :text="__(
                            'Reviewed translation key whose source and target language values are shown in this row.',
                        )"
                    />
                </span>
            </flux:table.column>
            <flux:table.column>
                {{ __('Namespace') }}
            </flux:table.column>
            <flux:table.column>
                {{ __('Group') }}
            </flux:table.column>
            <flux:table.column>
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('ui.type') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Type context')"
                        :text="__(
                            'Shows whether the row belongs to a bulk-equalized key, a dynamic key, and which finding can be opened from this row.',
                        )"
                    />
                </span>
            </flux:table.column>
            <flux:table.column>
                <span class="inline-flex items-center gap-1">
                    <x-ui.locale.flag
                        class="size-4"
                        :locale="$sourceMainLocale"
                    />
                    <span>{{ __('Source :locale', ['locale' => $sourceLocaleHeader]) }}</span>
                </span>
            </flux:table.column>
            <flux:table.column>
                <span class="inline-flex items-center gap-1">
                    <x-ui.locale.flag
                        class="size-4"
                        :locale="$targetMainLocale"
                    />
                    <span>{{ __('Target :locale', ['locale' => $targetLocaleHeader]) }}</span>
                </span>
            </flux:table.column>
            <flux:table.column>
                {{ __('Edited') }}
            </flux:table.column>
            <flux:table.column align="center">
                {{ __('ui.table.headers.actions') }}
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($lastEditedTranslationRows as $row)
                <flux:table.row
                    wire:key="translation-workbench-last-edit-{{ $row['translation_key'] }}-{{ $row['target_locale'] }}"
                >
                    <flux:table.cell>
                        <div class="wrap-anywhere max-w-md text-wrap font-mono text-xs">
                            {{ $row['translation_key'] }}
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            size="sm"
                            variant="subtle"
                        >
                            {{ $row['namespace'] ?: __('None') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            size="sm"
                            variant="subtle"
                        >
                            {{ $row['group'] ?: __('None') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex max-w-xs flex-wrap gap-1">
                            @if ($row['is_bulk'])
                                <flux:badge
                                    size="sm"
                                    color="cyan"
                                >
                                    {{ __('Bulk') }}
                                </flux:badge>

                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('Bulk #:id', ['id' => $row['bulk_id']]) }}
                                </flux:badge>

                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('Entries: :count', ['count' => number_format((int) $row['bulk_entry_count'])]) }}
                                </flux:badge>
                            @endif

                            @if ($row['is_dynamic'])
                                <flux:badge
                                    size="sm"
                                    color="{{ $row['is_dynamic_multi'] ? 'violet' : 'sky' }}"
                                >
                                    {{ $row['is_dynamic_multi'] ? __('Dynamic multi') : __('Dynamic') }}
                                </flux:badge>
                            @endif

                            @if ($row['finding_id'])
                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('Entry #:id', ['id' => $row['finding_id']]) }}
                                </flux:badge>
                            @else
                                <flux:badge
                                    size="sm"
                                    color="amber"
                                >
                                    {{ __('Entry missing') }}
                                </flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="max-w-lg text-wrap text-sm leading-relaxed text-zinc-700 dark:text-zinc-200">
                            {{ str((string) $row['source_value'])->limit(80)->toString() }}
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="max-w-lg text-wrap text-sm leading-relaxed text-zinc-900 dark:text-zinc-100">
                            {{ str((string) $row['target_value'])->limit(80)->toString() }}
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="space-y-0.5 text-xs text-zinc-500">
                            <x-ui.date-time.date :value="$row['updated_at']" />
                            <x-ui.date-time.time :value="$row['updated_at']" />
                        </div>
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        @if ($row['finding_id'])
                            <x-ui.tooltip.simple
                                :title="__('Show in Work findings')"
                                :text="__(
                                    'Filters the Work findings tab to this translation key. Use the Work findings actions for review and edit workflows.',
                                )"
                            >
                                <flux:button
                                    type="button"
                                    size="xs"
                                    variant="primary"
                                    color="pink"
                                    icon="list-filter"
                                    :aria-label="__('Show in Work findings')"
                                    wire:click="showExportReportKeyInWorkFindings(@js($row['translation_key']))"
                                />
                            </x-ui.tooltip.simple>
                        @else
                            <x-ui.tooltip.simple
                                :title="__('No finding context available')"
                                :text="__(
                                    'This translation value exists, but no active finding relation could be resolved for direct editing.',
                                )"
                            >
                                <flux:button
                                    type="button"
                                    size="xs"
                                    variant="subtle"
                                    color="zinc"
                                    icon="square-pen"
                                    :disabled="true"
                                    :aria-label="__('No finding context available')"
                                />
                            </x-ui.tooltip.simple>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8">
                        <flux:text class="text-sm text-zinc-500">
                            {{ __('No recently edited translation values for the active target language yet.') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
