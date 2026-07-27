{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-review.blade.php --}}

{{-- Card Finding --}}
<flux:modal
    class="w-full max-w-[calc(100vw-2rem)] lg:max-w-[calc(100vw-4rem)]"
    name="translation-workbench-finding-review"
    wire:model="reviewModalOpen"
>
    <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-4 overflow-hidden">
        <div class="flex shrink-0 items-start justify-between gap-4">
            {{-- Card Header Finding --}}
            <x-ui.headers.card
                :title="__('Review finding')"
                :description="__('Review key decisions and classification for the selected finding.')"
            />

            @if ($reviewFinding)
                @php
                    $reviewFindingIsDynamicNumeric =
                        ($reviewFinding->kind ?? null) === 'dynamic_numeric' ||
                        ($reviewFinding->entry_type ?? null) === 'dynamic_numeric';
                    $reviewFindingIsDynamicValues =
                        !$reviewFindingIsDynamicNumeric &&
                        (($reviewFinding->kind ?? null) === 'dynamic_multi' ||
                            ($reviewFinding->entry_type ?? null) === 'dynamic' ||
                            ($reviewFinding->candidate_type ?? null) === 'dynamic' ||
                            (bool) ($reviewFinding->is_dynamic_key ?? false) ||
                            (bool) ($reviewFinding->is_dynamic_multi ?? false) ||
                            (bool) ($reviewFinding->reviewed_is_dynamic_candidate ?? false) ||
                            (bool) ($reviewFinding->reviewed_is_dynamic_multi ?? false) ||
                            filled($reviewFinding->dynamic_data_state ?? null) ||
                            filled($reviewFinding->key_dynamic_data_state ?? null) ||
                            (int) ($reviewFinding->dynamic_source_count ?? 0) > 0);
                    $reviewFindingCanOpenDynamicReview =
                        $reviewFindingIsDynamicValues &&
                        filled($reviewFinding->key_id ?? null) &&
                        filled($reviewFinding->translation_key ?? null) &&
                        ($reviewFinding->review_status ?? null) === 'reviewed';
                    $reviewFindingCanOpenTranslationEdit =
                        !$reviewFindingIsDynamicValues &&
                        !$reviewFindingIsDynamicNumeric &&
                        filled($reviewFinding->key_id ?? null) &&
                        filled($reviewFinding->translation_key ?? null) &&
                        ($reviewFinding->review_status ?? null) === 'reviewed';
                @endphp

                <div class="mr-8 flex items-start gap-1.5">
                    {{-- Button Previous Review Finding --}}
                    <flux:button
                        class="h-7 w-7"
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="chevron-left"
                        :disabled="$previousReviewFindingId === null"
                        :aria-label="__('Previous review finding')"
                        wire:click="openPreviousReviewFinding"
                    />
                    {{-- Badge ID --}}
                    <flux:badge
                        class="tabular-nums"
                        variant="subtle"
                    >
                        #{{ $reviewFinding->id }}
                    </flux:badge>
                    {{-- Button Next Review Finding --}}
                    <flux:button
                        class="h-7 w-7"
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="chevron-right"
                        :disabled="$nextReviewFindingId === null"
                        :aria-label="__('Next review finding')"
                        wire:click="openNextReviewFinding"
                    />

                    <div class="flex flex-col gap-1">
                        {{-- Button Open Dynamic Data Review --}}
                        <x-ui.tooltip.simple
                            :title="$reviewFindingCanOpenDynamicReview
                                ? __('Review dynamic data')
                                : __('Review dynamic data unavailable')"
                            :text="$reviewFindingCanOpenDynamicReview
                                ? __(
                                    'Open the dynamic data review for this finding. From there you can continue to translating dynamic values when the data requirements are satisfied.',
                                )
                                : ($reviewFindingIsDynamicNumeric
                                    ? __(
                                        'Numeric dynamic findings are tracked for audit context but are not translated.',
                                    )
                                    : (!$reviewFindingIsDynamicValues
                                        ? __('This finding is not classified as dynamic values.')
                                        : __(
                                            'Complete the finding review first: a reviewed translation key is required before dynamic data can be reviewed.',
                                        )))"
                        >
                            <flux:button
                                class="h-7 w-full justify-start"
                                type="button"
                                size="xs"
                                variant="{{ $reviewFindingCanOpenDynamicReview ? 'primary' : 'subtle' }}"
                                color="{{ $reviewFindingCanOpenDynamicReview ? 'violet' : 'zinc' }}"
                                icon="database-zap"
                                :disabled="!$reviewFindingCanOpenDynamicReview"
                                :aria-label="__('Review dynamic data')"
                                wire:click="openDynamicReviewModal({{ $reviewFinding->id }})"
                            >
                                {{ __('Review dynamic data') }}
                            </flux:button>
                        </x-ui.tooltip.simple>

                        {{-- Button Open Translation Edit --}}
                        <x-ui.tooltip.simple
                            :title="$reviewFindingCanOpenTranslationEdit
                                ? __('Edit translations')
                                : __('Edit translations unavailable')"
                            :text="$reviewFindingCanOpenTranslationEdit
                                ? __('Open the translation editor for this reviewed finding.')
                                : ($reviewFindingIsDynamicNumeric
                                    ? __(
                                        'Numeric dynamic findings are tracked for audit context but are not translated.',
                                    )
                                    : ($reviewFindingIsDynamicValues
                                        ? __('Dynamic findings are edited through the dynamic data review workflow.')
                                        : __(
                                            'Complete the finding review first: a reviewed translation key is required before translation values can be edited.',
                                        )))"
                        >
                            <flux:button
                                class="h-7 w-full justify-start"
                                type="button"
                                size="xs"
                                variant="{{ $reviewFindingCanOpenTranslationEdit ? 'primary' : 'subtle' }}"
                                color="{{ $reviewFindingCanOpenTranslationEdit ? 'green' : 'zinc' }}"
                                icon="square-pen"
                                :disabled="!$reviewFindingCanOpenTranslationEdit"
                                :aria-label="__('Edit translations')"
                                wire:click="openEditModal({{ $reviewFinding->id }})"
                            >
                                {{ __('Edit translations') }}
                            </flux:button>
                        </x-ui.tooltip.simple>
                    </div>
                </div>
            @endif
        </div>

        @if ($reviewFinding)
            <div class="min-h-0 overflow-y-auto pr-2">
                @include('translation-workbench::livewire.entries.review.modal-states')
                @include('translation-workbench::livewire.entries.review.modal-details')
            </div>
        @else
            <flux:text class="text-sm text-zinc-500">
                {{ __('No finding selected.') }}
            </flux:text>
        @endif
    </div>
</flux:modal>
