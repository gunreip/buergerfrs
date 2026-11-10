<flux:modal
    class="w-full max-w-7xl"
    name="workbench-review-batch"
    wire:model="reviewBatchOpen"
>
    <x-translation-workbench::ui.common.modal-loading target="decideReviewBatch, openWorkflowReason" />
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
                    {{ count($reviewBatch) === 1 ? __('Review 1 selected revision') : __('Review :count selected revisions', ['count' => count($reviewBatch)]) }}
                </flux:heading>
            </div>

            <flux:callout
                icon="table"
                color="sky"
                heading="{{ __('Review decision') }}"
            >
                {{-- Table --}}
                <flux:table container:class="max-h-96 overflow-auto rounded-t-lg">
                    {{-- Table Header Row --}}
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-800"
                        sticky
                    >
                        {{-- Table Header Column Revision --}}
                        <flux:table.column>
                            <flux:callout.heading class="ml-2">
                                {{ __('Revision') }}
                            </flux:callout.heading>
                        </flux:table.column>
                        {{-- Table Header Column Translation key --}}
                        <flux:table.column>
                            <flux:callout.heading>
                                {{ __('Translation key') }}
                            </flux:callout.heading>
                        </flux:table.column>
                        {{-- Table Header Column Language --}}
                        <flux:table.column>
                            <flux:callout.heading>
                                {{ __('Language') }}
                            </flux:callout.heading>
                        </flux:table.column>
                        {{-- Table Header Column Saved text --}}
                        <flux:table.column>
                            <flux:callout.heading>
                                {{ __('Saved text') }}
                            </flux:callout.heading>
                        </flux:table.column>
                    </flux:table.columns>
                    {{-- Table Body Rows --}}
                    <flux:table.rows>
                        @foreach ($reviewBatch as $task)
                            {{-- Table Body Row Column Revision ID --}}
                            <flux:table.row wire:key="review-batch-{{ $task['context']['revision_id'] }}">
                                <flux:table.cell class="tabular-nums">
                                    <flux:callout.text class="ml-2">
                                        #{{ $task['context']['revision_id'] }}
                                    </flux:callout.text>
                                </flux:table.cell>
                                {{-- Table Body Row Column Translation key --}}
                                <flux:table.cell class="max-w-xs whitespace-normal">
                                    <x-translation-workbench::ui.common.text-preview
                                        :mono="true"
                                        :text="$task['context']['full_name']"
                                        :length="60"
                                        side="center"
                                        :heading="__('Translation key')"
                                    />
                                </flux:table.cell>
                                {{-- Table Body Row Column Language --}}
                                <flux:table.cell>
                                    <flux:tooltip toggleable>
                                        <flux:button
                                            type="button"
                                            square
                                            variant="ghost"
                                            size="xs"
                                            :aria-label="$task['context']['locale']"
                                        >
                                            <flux:flag
                                                :country="$translationFlagCountries[$task['context']['locale']] ?? null"
                                                size="xs"
                                                circle
                                            />
                                        </flux:button>
                                        <x-translation-workbench::ui.common.tooltip-content
                                            :heading="__('Language')"
                                            :text="$task['context']['locale']"
                                        />
                                    </flux:tooltip>
                                </flux:table.cell>
                                {{-- Table Body Row Column Saved text --}}
                                <flux:table.cell class="max-w-lg whitespace-normal">
                                    <x-translation-workbench::ui.common.text-preview
                                        class="mr-3"
                                        :text="$task['context']['value']"
                                        :lang="str_replace('_', '-', $task['context']['locale'])"
                                        :length="160"
                                        :heading="__('Saved text')"
                                    />
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:callout>

            @include('translation-workbench::livewire.translation-workbench.shared.reason-select', [
                'reasonField' => 'reviewBatchReason',
                'reasonLabel' => __('Reason for review decision'),
                'reasonInputLocale' => $reviewBatchReasonLocale,
                'reasonLocaleField' => 'reviewBatchReasonLocale',
                'reasonSearchId' => 'review-batch-reason-search-' . $this->getId(),
                'reasonHint' => __(
                    'The decision applies to all listed revisions and their linked findings and is recorded with your user and reason. It does not write language files or source code.'),
            ])

            <flux:error name="reviewBatch" />

            <div class="flex flex-wrap justify-end gap-3">
                <flux:button.group>
                    <flux:button
                        size="sm"
                        color="red"
                        variant="filled"
                        wire:click="decideReviewBatch(false)"
                        wire:loading.attr="disabled"
                    >
                        <flux:icon.undo-2 size="5" />
                    </flux:button>
                    <flux:button
                        size="sm"
                        color="red"
                        variant="filled"
                        wire:click="decideReviewBatch(false)"
                        wire:loading.attr="disabled"
                    >
                        {{ __('Return for correction') }}
                    </flux:button>
                </flux:button.group>
                <flux:button.group>
                    <flux:button
                        size="sm"
                        color="green"
                        variant="filled"
                        wire:click="decideReviewBatch(true)"
                        wire:loading.attr="disabled"
                    >
                        <flux:icon.circle-dashed-check size="5" />
                    </flux:button>
                    <flux:button
                        size="sm"
                        color="green"
                        variant="filled"
                        wire:click="decideReviewBatch(true)"
                        wire:loading.attr="disabled"
                    >
                        {{ count($reviewBatch) === 1 ? __('Approve revision') : __('Approve revisions') }}
                    </flux:button>
                </flux:button.group>
            </div>
        </div>
    @endif
</flux:modal>
