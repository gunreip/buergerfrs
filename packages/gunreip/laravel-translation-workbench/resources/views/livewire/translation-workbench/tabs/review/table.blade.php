{{-- <flux:error name="selectedReviewRevisions" /> --}}

{{-- <flux:error name="reviewBatch" /> --}}

{{-- Table --}}
<flux:checkbox.group>
    <flux:table container:class="max-h-[100vh] overflow-x-auto rounded-t-lg">
        {{-- Table Header --}}
        <flux:table.columns
            class="bg-white dark:bg-zinc-800"
            sticky
        >
            {{-- Column Key --}}
            <flux:table.column
                class="tabular-nums"
                sortable
                :sorted="$reviewSort === 'key'"
                :direction="$reviewDirection"
                wire:click="sortReview('key')"
            >
                <flux:tooltip>
                    <flux:callout.heading class="ml-2">
                        {{ __('Key') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Key')"
                        :text="__(
                            'The ID of the translation key record and its full translation key, including a namespace when present. This is the key ID, not a finding ID.',
                        )"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Column Language --}}
            <flux:table.column
                sortable
                :sorted="$reviewSort === 'language'"
                :direction="$reviewDirection"
                wire:click="sortReview('language')"
            >
                <flux:tooltip>
                    <flux:callout.heading>
                        {{ __('Language') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Language')"
                        :text="__(
                            'The language of the saved translation awaiting review. The number after the language is the revision ID identifying the exact saved text being reviewed.',
                        )"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Column Submitted --}}
            <flux:table.column
                sortable
                :sorted="$reviewSort === 'submitted'"
                :direction="$reviewDirection"
                wire:click="sortReview('submitted')"
            >
                <flux:tooltip>
                    <flux:callout.heading>
                        {{ __('Submitted') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Submitted')"
                        :text="__(
                            'The date and time this revision was submitted for review, followed by the ID of the user who submitted it.',
                        )"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Column Status --}}
            <flux:table.column>
                <flux:tooltip>
                    <flux:callout.heading>
                        {{ __('Status') }}
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Status')"
                        :text="__(
                            'A Shared key badge means multiple active findings use this translation key. Reviewing this language revision applies to all of those findings.',
                        )"
                    />
                </flux:tooltip>
            </flux:table.column>
            {{-- Column Select --}}
            <flux:table.column>
                <flux:checkbox.all
                    class="hover:cursor-pointer"
                    :disabled="$reviewTasks->isEmpty()"
                    :aria-label="__('Select or clear all revisions on this page')"
                    :tooltip="__('Select or clear all revisions on this page. Selections on other pages are kept.')"
                />
            </flux:table.column>
            {{-- Column Actions --}}
            <flux:table.column class="w-90">
                <flux:tooltip>
                    <flux:callout.heading>
                        <div class="flex items-center justify-end gap-3">
                            {{ __('Actions') }}

                            <flux:button
                                class="hover:cursor-pointer"
                                size="sm"
                                wire:click="openReviewBatch"
                                :disabled="count($selectedReviewRevisions) === 0"
                            >
                                {{ __('Review selected revisions') }} ({{ count($selectedReviewRevisions) }})
                            </flux:button>
                        </div>
                    </flux:callout.heading>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Actions')"
                        :text="__(
                            'Open the workflow overview or review an individual revision. Select revisions to review them together. The header checkbox selects or clears this page; selections on other pages are retained. Approval does not publish language files.',
                        )"
                    />
                </flux:tooltip>

                <flux:error name="selectedReviewRevisions" />

                <flux:error name="reviewBatch" />

            </flux:table.column>
        </flux:table.columns>
        {{-- Table Rows --}}
        <flux:table.rows>
            @forelse ($reviewTasks as $task)
                {{-- Table Row --}}
                <flux:table.row :key="'review-task-'.$task['submission']">
                    {{-- Column Key --}}
                    <flux:table.cell class="whitespace-normal">
                        <flux:callout.text class="ml-2">
                            <code>#{{ $task['key_id'] }} ·
                                {{ $task['namespace'] !== '' ? $task['namespace'] . '::' : '' }}{{ $task['full_name'] }}</code>
                        </flux:callout.text>
                    </flux:table.cell>
                    {{-- Column Language --}}
                    <flux:table.cell>
                        <flux:callout.text>
                            {{ $task['locale'] }} · #{{ $task['revision'] }}
                        </flux:callout.text>
                    </flux:table.cell>
                    {{-- Column Submitted --}}
                    <flux:table.cell>
                        <flux:callout.text>
                            <x-ui.date-time.date-time :value="$task['occurred_at']" />
                            · #{{ $task['actor_id'] }}
                        </flux:callout.text>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if (($task['active_findings_count'] ?? 0) > 1)
                            <flux:tooltip
                                toggleable
                                :content="__(':count active findings use this translation key. Reviewing this language revision applies to all of them.',
                                    ['count' => $task['active_findings_count']])"
                            >
                                <flux:badge
                                    color="sky"
                                    size="sm"
                                >{{ __('Shared key') }}</flux:badge>
                            </flux:tooltip>
                        @endif
                    </flux:table.cell>
                    {{-- Column Select --}}
                    <flux:table.cell>
                        <flux:checkbox
                            class="hover:cursor-pointer"
                            wire:model.live="selectedReviewRevisions"
                            :value="(string) $task['revision']"
                            :aria-label="__('Select revision #:revision', ['revision' => $task['revision']])"
                        />
                    </flux:table.cell>
                    {{-- Column Actions --}}
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <flux:button
                                size="sm"
                                variant="outline"
                                :tooltip="__('Workflow status')"
                                :aria-label="__('Workflow status')"
                                wire:click="openKeyWorkflowStatus({{ $task['key_id'] }})"
                            >
                                <flux:icon.queue-list size="5" />
                            </flux:button>

                            <flux:button.group>
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    :tooltip="__('Review revision')"
                                    :aria-label="__('Review revision')"
                                    wire:click="openReviewBatch({{ $task['revision'] }})"
                                    :disabled="count($selectedReviewRevisions) > 1 && in_array((string) $task['revision'], array_map('strval', $selectedReviewRevisions), true)"
                                >
                                    <flux:icon.eye size="5" />
                                </flux:button>
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    wire:click="openReviewBatch({{ $task['revision'] }})"
                                    :disabled="count($selectedReviewRevisions) > 1 && in_array((string) $task['revision'], array_map('strval', $selectedReviewRevisions), true)"
                                >
                                    {{ __('Review revision') }}
                                </flux:button>
                            </flux:button.group>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                {{-- Table Rows Empty --}}
                <flux:table.row>
                    {{-- Column Empty --}}
                    <flux:table.cell colspan="6">
                        <flux:callout.text class="ml-2">
                            {{ __('No revisions waiting for review.') }}
                        </flux:callout.text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</flux:checkbox.group>

<flux:pagination :paginator="$reviewTasks" />
