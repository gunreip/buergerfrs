{{-- <div class="mb-3 flex items-center justify-end gap-3">
    <flux:button
        size="sm"
        icon="check-check"
        wire:click="toggleVisibleReviewRevisions"
        :disabled="$reviewTasks->isEmpty()"
        :tooltip="__('Select or deselect all revisions on this page')"
    />
    <flux:button
        size="sm"
        wire:click="openReviewBatch"
        :disabled="count($selectedReviewRevisions) === 0"
    >
        {{ __('Review selected revisions') }} ({{ count($selectedReviewRevisions) }})
    </flux:button>
</div> --}}

{{-- <flux:error name="selectedReviewRevisions" /> --}}

{{-- <flux:error name="reviewBatch" /> --}}

{{-- Table --}}
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
            <flux:tooltip :content="__('Sort by ID')">
                <flux:callout.heading class="ml-2">
                    {{ __('Key') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Language --}}
        <flux:table.column
            sortable
            :sorted="$reviewSort === 'language'"
            :direction="$reviewDirection"
            wire:click="sortReview('language')"
        >
            <flux:tooltip :content="__('Sort by Language')">
                <flux:callout.heading>
                    {{ __('Language') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Submitted --}}
        <flux:table.column
            sortable
            :sorted="$reviewSort === 'submitted'"
            :direction="$reviewDirection"
            wire:click="sortReview('submitted')"
        >
            <flux:tooltip :content="__('Sort by Submitted')">
                <flux:callout.heading>
                    {{ __('Submitted') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Status --}}
        <flux:table.column>
            <flux:tooltip content="Status">
                <flux:callout.heading>
                    {{ __('Status') }}
                </flux:callout.heading>
            </flux:tooltip>
        </flux:table.column>
        {{-- Column Actions --}}
        <flux:table.column class="w-90">
            <flux:tooltip content="{{ __('Actions') }}">
                <flux:callout.heading>
                    <div class="flex items-center justify-end gap-3">
                        {{ __('Actions') }}
                        <flux:button
                            size="sm"
                            icon="check-check"
                            wire:click="toggleVisibleReviewRevisions"
                            :disabled="$reviewTasks->isEmpty()"
                            :tooltip="__('Select or deselect all revisions on this page')"
                        />
                        <flux:button
                            size="sm"
                            wire:click="openReviewBatch"
                            :disabled="count($selectedReviewRevisions) === 0"
                        >
                            {{ __('Review selected revisions') }} ({{ count($selectedReviewRevisions) }})
                        </flux:button>
                    </div>
                </flux:callout.heading>
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
                {{-- Column Actions --}}
                <flux:table.cell>
                    <flux:button size="sm" variant="outline" icon="queue-list" wire:click="openKeyWorkflowStatus({{ $task['key_id'] }})" :tooltip="__('Workflow status')" :aria-label="__('Workflow status')" />
                    <div class="flex items-center gap-3">
                        <flux:checkbox
                            wire:model.live="selectedReviewRevisions"
                            :value="(string) $task['revision']"
                            :aria-label="__('Select revision #:revision', ['revision' => $task['revision']])"
                        />
                        <flux:button.group>
                            <flux:button
                                size="sm"
                                variant="outline"
                                icon="eye"
                                wire:click="openReviewTask({{ $task['revision'] }})"
                            />
                            <flux:button
                                size="sm"
                                variant="outline"
                                wire:click="openReviewTask({{ $task['revision'] }})"
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
                <flux:table.cell colspan="5">
                    <flux:callout.text class="ml-2">
                        {{ __('No revisions waiting for review.') }}
                    </flux:callout.text>
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.rows>
</flux:table>

<flux:pagination :paginator="$reviewTasks" />
