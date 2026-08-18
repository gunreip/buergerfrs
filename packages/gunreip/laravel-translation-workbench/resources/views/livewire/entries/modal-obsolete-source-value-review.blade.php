{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-obsolete-source-value-review.blade.php --}}

<flux:modal
    class="w-full max-w-5xl"
    name="translation-workbench-obsolete-source-value-review"
    wire:model="obsoleteSourceValueReviewModalOpen"
>
    @php
        $obsoleteReview = $obsoleteSourceValueReview ?? null;
        $obsoleteLangValue = $obsoleteReview['lang_value'] ?? null;
        $possibleMatchingEntry = $obsoleteReview['possible_matching_entry'] ?? null;
    @endphp

    <div class="space-y-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0 space-y-1">
                <flux:heading
                    size="xl"
                    level="3"
                >
                    {{ __('Review obsolete source value') }}
                </flux:heading>

                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('ui.remove.removed.review-whether-this-source-language-value-should-be-removed-from-the-active-export-workflow') }}
                </flux:text>
            </div>

            @if ($obsoleteReview)
                <flux:badge
                    size="sm"
                    color="{{ $obsoleteReview['is_obsolete'] ?? false ? 'zinc' : 'amber' }}"
                >
                    {{ $obsoleteReview['is_obsolete'] ?? false ? __('Already obsolete') : __('Needs review') }}
                </flux:badge>
            @endif
        </div>

        @if ($obsoleteReview)
            <div class="grid gap-3 lg:grid-cols-3">
                <flux:callout
                    color="sky"
                    icon="languages"
                >
                    <flux:callout.heading>{{ __('Locale context') }}</flux:callout.heading>
                    <flux:callout.text>
                        <div class="grid gap-2 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-zinc-500 dark:text-zinc-400">{{ __('ui.source.source') }}</span>
                                <flux:badge size="sm">{{ $obsoleteReview['source_locale'] }}
                                    <x-ui.locale.flag
                                        class="ml-1"
                                        :locale="$obsoleteReview['source_locale']"
                                        size="sm"
                                    />
                                </flux:badge>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-zinc-500 dark:text-zinc-400">{{ __('Target main') }}</span>
                                <flux:badge size="sm">{{ $obsoleteReview['target_locale'] }}
                                    <x-ui.locale.flag
                                        class="ml-1"
                                        :locale="$obsoleteReview['target_locale']"
                                        size="sm"
                                    />
                                </flux:badge>
                            </div>
                        </div>
                    </flux:callout.text>
                </flux:callout>

                <flux:callout
                    class="lg:col-span-2"
                    color="{{ $obsoleteLangValue ? 'amber' : 'red' }}"
                    icon="{{ $obsoleteLangValue ? 'key-round' : 'circle-alert' }}"
                >
                    <flux:callout.heading>{{ __('ui.translation.translation-key') }}</flux:callout.heading>
                    <flux:callout.text>
                        <div class="wrap-anywhere font-mono text-xs">
                            {{ $obsoleteReview['translation_key'] }}
                        </div>
                    </flux:callout.text>
                </flux:callout>
            </div>

            <flux:callout
                color="{{ $obsoleteLangValue ? ($obsoleteReview['is_obsolete'] ?? false ? 'zinc' : 'amber') : 'red' }}"
                icon="{{ $obsoleteLangValue ? 'file-text' : 'circle-alert' }}"
            >
                <div class="flex flex-wrap items-center gap-2">
                    <flux:callout.heading>{{ __('Source-language value') }}</flux:callout.heading>

                    @if ($obsoleteLangValue)
                        <flux:badge
                            size="sm"
                            color="{{ $obsoleteLangValue->status === 'obsolete' ? 'zinc' : 'green' }}"
                        >
                            {{ __('ui.state.status') }}: {{ $obsoleteLangValue->status }}
                        </flux:badge>
                        <flux:badge size="sm">
                            ID #{{ $obsoleteLangValue->id }}
                        </flux:badge>
                    @endif
                </div>

                <flux:callout.text>
                    @if ($obsoleteLangValue)
                        <div
                            class="mt-2 rounded-md border border-zinc-200 bg-white p-3 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <div class="wrap-anywhere whitespace-pre-wrap">{{ $obsoleteLangValue->value }}</div>
                        </div>
                    @else
                        {{ __('No source-language value was found for this translation key. The export report may be stale.') }}
                    @endif
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="{{ $possibleMatchingEntry ? 'cyan' : 'zinc' }}"
                icon="{{ $possibleMatchingEntry ? 'git-branch' : 'search' }}"
            >
                <div class="flex flex-wrap items-center gap-2">
                    <flux:callout.heading>{{ __('Possible matching entry') }}</flux:callout.heading>

                    @if ($possibleMatchingEntry)
                        <flux:badge
                            size="sm"
                            color="cyan"
                        >
                            {{ __('Reduced suffix') }}: {{ $possibleMatchingEntry['searched_suffix'] }}
                        </flux:badge>

                        <flux:badge size="sm">
                            {{ __('ui.remove.removed.removed') }}: {{ $possibleMatchingEntry['deleted_segments_count'] }}
                        </flux:badge>

                        @if ($possibleMatchingEntry['key_id'])
                            <flux:badge size="sm">
                                {{ __('Key') }} #{{ $possibleMatchingEntry['key_id'] }}
                            </flux:badge>
                        @endif

                        @if ($possibleMatchingEntry['finding_id'])
                            <flux:badge size="sm">
                                {{ __('Finding') }} #{{ $possibleMatchingEntry['finding_id'] }}
                            </flux:badge>
                        @endif
                    @endif
                </div>

                <flux:callout.text>
                    @if ($possibleMatchingEntry)
                        <div
                            class="mt-2 space-y-2 rounded-md border border-zinc-200 bg-white p-3 text-xs dark:border-zinc-700 dark:bg-zinc-900">
                            <div class="grid gap-1 lg:grid-cols-[12rem_minmax(0,1fr)]">
                                <span class="text-zinc-500 dark:text-zinc-400">{{ __('ui.translation.translation-key') }}</span>
                                <span class="wrap-anywhere font-mono">{{ $obsoleteReview['translation_key'] }}</span>
                            </div>

                            <div class="grid gap-1 lg:grid-cols-[12rem_minmax(0,1fr)]">
                                <span class="text-zinc-500 dark:text-zinc-400">{{ __('Possible matching key') }}</span>
                                <span class="wrap-anywhere font-mono">
                                    {{ $possibleMatchingEntry['translation_key'] ?: $possibleMatchingEntry['key_suggested_key'] ?: $possibleMatchingEntry['finding_suggested_key'] ?: $possibleMatchingEntry['searched_suffix'] }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-3 flex justify-end gap-2">
                            <flux:button
                                type="button"
                                size="xs"
                                variant="primary"
                                color="sky"
                                icon="list-filter"
                                wire:click="showExportReportKeyInWorkFindings('{{ $possibleMatchingEntry['searched_suffix'] }}')"
                            >
                                {{ __('Show suffix in work findings') }}
                            </flux:button>

                            @if ($possibleMatchingEntry['finding_id'])
                                <flux:button
                                    type="button"
                                    size="xs"
                                    variant="primary"
                                    color="cyan"
                                    icon="scan-search"
                                    wire:click="openReviewModal({{ $possibleMatchingEntry['finding_id'] }})"
                                >
                                    {{ __('Open matching review') }}
                                </flux:button>
                            @endif
                        </div>
                    @else
                        {{ __('No possible matching Workbench entry was found by removing leading key segments.') }}
                    @endif
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="amber"
                icon="triangle-alert"
            >
                <flux:callout.heading>{{ __('What will happen?') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('Only this source-language value row will be marked obsolete. Findings, keys and target-language values stay unchanged. The dry-run export report will be refreshed afterwards.') }}
                </flux:callout.text>
            </flux:callout>

            <div class="flex justify-end gap-2">
                <flux:button
                    type="button"
                    variant="ghost"
                    wire:click="closeObsoleteSourceValueReview"
                >
                    {{ __('ui.button.cancel') }}
                </flux:button>

                <flux:button
                    type="button"
                    variant="danger"
                    icon="archive-x"
                    wire:click="confirmObsoleteSourceValue"
                    :disabled="!$obsoleteLangValue || ($obsoleteReview['is_obsolete'] ?? false)"
                >
                    {{ __('Mark obsolete') }}
                </flux:button>
            </div>
        @else
            <flux:callout
                color="amber"
                icon="info"
            >
                <flux:callout.heading>{{ __('No obsolete review context available') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('Select a source-language translation key from the export report first.') }}
                </flux:callout.text>
            </flux:callout>
        @endif
    </div>
</flux:modal>
