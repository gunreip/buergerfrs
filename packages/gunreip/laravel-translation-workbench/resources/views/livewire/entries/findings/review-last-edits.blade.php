{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/review-last-edits.blade.php --}}

@php
    $sourceLocaleHeader = strtoupper((string) ($sourceMainLocale ?? 'en'));
    $targetLocaleHeader = strtoupper((string) ($targetMainLocale ?? app()->getLocale()));
@endphp

<div class="mt-4 space-y-4">
    <flux:callout
        color="sky"
        icon="list-checks"
        variant="secondary"
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
                    <span>{{ __('Translation key') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Translation key')"
                        :text="__('Reviewed translation key whose source and target language values are shown in this row.')"
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
                {{ __('Source :locale', ['locale' => $sourceLocaleHeader]) }}
            </flux:table.column>
            <flux:table.column>
                {{ __('Target :locale', ['locale' => $targetLocaleHeader]) }}
            </flux:table.column>
            <flux:table.column>
                {{ __('Edited') }}
            </flux:table.column>
            <flux:table.column>
                {{ __('ui.table.headers.actions') }}
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($lastEditedTranslationRows as $row)
                <flux:table.row wire:key="translation-workbench-last-edit-{{ $row['translation_key'] }}-{{ $row['target_locale'] }}">
                    <flux:table.cell>
                        <div class="max-w-md wrap-anywhere text-wrap font-mono text-xs">
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
                        <div class="max-w-lg text-wrap text-sm leading-relaxed text-zinc-700 dark:text-zinc-200">
                            {{ $row['source_value'] }}
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="max-w-lg text-wrap text-sm leading-relaxed text-zinc-900 dark:text-zinc-100">
                            {{ $row['target_value'] }}
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="space-y-0.5 text-xs text-zinc-500">
                            <x-ui.date-time.date :value="$row['updated_at']" />
                            <x-ui.date-time.time :value="$row['updated_at']" />
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($row['finding_id'])
                            <flux:button
                                type="button"
                                size="xs"
                                variant="primary"
                                color="green"
                                icon="square-pen"
                                :aria-label="__('Edit translation values')"
                                wire:click="openEditModal({{ $row['finding_id'] }})"
                            />
                        @else
                            <flux:button
                                type="button"
                                size="xs"
                                variant="subtle"
                                color="zinc"
                                icon="square-pen"
                                :disabled="true"
                                :aria-label="__('No finding context available')"
                            />
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7">
                        <flux:text class="text-sm text-zinc-500">
                            {{ __('No recently edited translation values for the active target language yet.') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
