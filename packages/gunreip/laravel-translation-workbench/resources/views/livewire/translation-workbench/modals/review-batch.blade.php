<flux:modal
    class="w-full max-w-5xl"
    name="workbench-review-batch"
    wire:model="reviewBatchOpen"
>
    @if ($reviewBatch)
        <div class="space-y-4">
            <div class="-mt-1 flex items-start justify-between gap-3 pe-6">
                <flux:heading
                    class="flex items-center gap-2"
                    size="xl"
                >
                    <flux:icon.pencil-line
                        class="shrink-0"
                        size="sm"
                    />

                    {{ __('Review selected revisions') }}
                </flux:heading>
                <x-translation-workbench::ui.common.input-language-selector
                    class="shrink-0"
                    wire:model.live="reviewBatchReasonLocale"
                    :target="$this->reasonTargetLocale()"
                    :countries="$translationFlagCountries ?? []"
                    :focus="'review-batch-reason-search-' . $this->getId()"
                />
            </div>

            <div class="max-h-96 space-y-3 overflow-auto">
                @foreach ($reviewBatch as $task)
                    <flux:callout wire:key="review-batch-{{ $task['context']['revision_id'] }}">
                        <flux:callout.heading>
                            {{ $task['context']['full_name'] }} · {{ $task['context']['locale'] }} ·
                            #{{ $task['context']['revision_id'] }}
                        </flux:callout.heading>
                        <flux:callout.text
                            class="whitespace-pre-wrap"
                            :lang="$task['context']['locale']"
                        >{{ $task['context']['value'] }}</flux:callout.text>
                    </flux:callout>
                @endforeach
            </div>
            @include('translation-workbench::livewire.translation-workbench.shared.reason-select', [
                'reasonField' => 'reviewBatchReason',
                'reasonLabel' => __('Reason for review decision'),
                'reasonInputLocale' => $reviewBatchReasonLocale,
                'reasonSearchId' => 'review-batch-reason-search-' . $this->getId(),
            ])
            <flux:text>
                {{ __('The decision applies to all listed revisions and their linked findings. It does not write language files or source code.') }}
            </flux:text>

            <flux:error name="reviewBatch" />

            <div class="flex flex-wrap justify-end gap-3">
                <flux:button
                    variant="ghost"
                    wire:click="$set('reviewBatchOpen', false)"
                >
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button
                    wire:click="decideReviewBatch(false)"
                    wire:loading.attr="disabled"
                >
                    {{ __('Return for correction') }}
                </flux:button>
                <flux:button
                    variant="primary"
                    wire:click="decideReviewBatch(true)"
                    wire:loading.attr="disabled"
                >
                    {{ __('Approve revisions') }}
                </flux:button>
            </div>
        </div>
    @endif
</flux:modal>
