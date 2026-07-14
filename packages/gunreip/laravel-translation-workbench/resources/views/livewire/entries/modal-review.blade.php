{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-review.blade.php --}}

<flux:modal
    class="w-full max-w-[calc(100vw-2rem)] lg:max-w-[calc(100vw-4rem)]"
    name="translation-workbench-finding-review"
    {{-- class="w-[calc(100vw-2rem)] max-w-none lg:w-[calc(100vw-8rem)]" --}}
    wire:model="reviewModalOpen"
>
    <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-4 overflow-hidden">
        <div class="flex shrink-0 items-start justify-between gap-4">
            <x-ui.headers.card
                :title="__('Review finding')"
                :description="__('Review key decisions and classification for the selected finding.')"
            />

            @if ($reviewFinding)
                <div class="mr-8 flex items-center gap-1.5">
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

                    <flux:badge
                        class="tabular-nums"
                        variant="subtle"
                    >
                        #{{ $reviewFinding->id }}
                    </flux:badge>

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
