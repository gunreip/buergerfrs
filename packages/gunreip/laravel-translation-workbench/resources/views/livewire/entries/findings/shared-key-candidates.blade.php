{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/shared-key-candidates.blade.php --}}

<div class="mt-4 space-y-4">

    {{-- Callout Shared Key Candidates --}}
    <flux:callout
        color="amber"
        icon="git-compare-arrows"
    >
        <flux:callout.heading>{{ __('Shared key candidates') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('New findings whose literal matches an already bulk-reviewed shared translation key. These rows are suggestions only; no translation key is applied automatically.') }}
        </flux:callout.text>
    </flux:callout>

    {{-- Table Shared Key Candidates --}}
    <flux:table container:class="overflow-x-auto">
        <flux:table.columns class="bg-white dark:bg-zinc-700">
            <flux:table.column class="w-24">
                {{ __('Follow-up') }}
            </flux:table.column>
            <flux:table.column class="w-32">
                {{ __('Finding') }}
            </flux:table.column>
            <flux:table.column>
                {{ __('ui.literal.literal') }}
            </flux:table.column>
            <flux:table.column>
                {{ __('Finding key') }}
            </flux:table.column>
            <flux:table.column>
                {{ __('Reviewed shared key') }}
            </flux:table.column>
            <flux:table.column class="w-40">
                {{ __('Evidence') }}
            </flux:table.column>
            <flux:table.column class="w-36">
                {{ __('ui.state') }}
            </flux:table.column>
            <flux:table.column class="w-32">
                {{ __('Actions') }}
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($sharedKeyCandidateRows as $row)
                <flux:table.row wire:key="translation-workbench-shared-key-candidate-{{ $row['id'] }}">
                    <flux:table.cell class="bg-white dark:bg-zinc-700">
                        <flux:badge
                            class="tabular-nums"
                            color="amber"
                            size="sm"
                            variant="subtle"
                        >
                            C#{{ $row['id'] }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex flex-wrap items-center gap-1">
                            <flux:badge
                                class="tabular-nums"
                                color="sky"
                                size="sm"
                                variant="subtle"
                            >
                                F#{{ $row['finding_id'] }}
                            </flux:badge>

                            @if ($row['key_id'])
                                <flux:badge
                                    class="tabular-nums"
                                    color="violet"
                                    size="sm"
                                    variant="subtle"
                                >
                                    K#{{ $row['key_id'] }}
                                </flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="max-w-sm space-y-1">
                            <div class="text-wrap text-sm text-zinc-800 dark:text-zinc-100">
                                {{ $row['literal_text'] ?? __('No literal') }}
                            </div>
                            <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                                {{ $row['normalized_literal'] }}
                            </div>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="wrap-anywhere max-w-md space-y-1 text-wrap font-mono text-xs">
                            @if ($row['current_translation_key'])
                                <flux:badge
                                    color="green"
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('ui.translation.translation-key') }}
                                </flux:badge>
                                <div class="text-zinc-700 dark:text-zinc-200">{{ $row['current_translation_key'] }}</div>
                            @else
                                <flux:badge
                                    color="red"
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_states.translation_key_missing') }}
                                </flux:badge>
                            @endif

                            @if ($row['current_suggested_key'])
                                <flux:badge
                                    color="zinc"
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('Finding suggested key') }}
                                </flux:badge>
                                <div class="text-zinc-400">{{ $row['current_suggested_key'] }}</div>
                            @elseif ($row['finding_suggested_key'])
                                <flux:badge
                                    color="zinc"
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ __('Finding suggested key') }}
                                </flux:badge>
                                <div class="text-zinc-400">{{ $row['finding_suggested_key'] }}</div>
                            @endif
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div
                            class="wrap-anywhere max-w-md text-wrap font-mono text-xs font-semibold text-amber-700 dark:text-amber-300">
                            {{ $row['suggested_shared_translation_key'] }}
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex flex-col items-start gap-1">
                            <flux:badge
                                color="{{ $row['confidence'] === 'high' ? 'green' : 'amber' }}"
                                size="sm"
                                variant="subtle"
                            >
                                {{ str($row['confidence'])->headline() }}
                            </flux:badge>
                            <flux:badge
                                color="zinc"
                                size="sm"
                                variant="subtle"
                            >
                                {{ __('Reviews') }}: {{ $row['matched_review_count'] }}
                            </flux:badge>
                            <flux:badge
                                color="zinc"
                                size="sm"
                                variant="subtle"
                            >
                                {{ __('Findings') }}: {{ $row['matched_finding_count'] }}
                            </flux:badge>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="space-y-1">
                            <flux:badge
                                color="{{ $row['status'] === 'pending' ? 'amber' : 'zinc' }}"
                                size="sm"
                                variant="subtle"
                            >
                                {{ str($row['status'])->headline() }}
                            </flux:badge>

                            @if ($row['last_seen_at'])
                                <div class="text-xs text-zinc-500">
                                    <x-ui.date-time.ago
                                        :value="$row['last_seen_at']"
                                        size="xs"
                                    />
                                </div>
                            @endif
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex items-center gap-1.5">
                            <flux:button
                                type="button"
                                size="xs"
                                variant="primary"
                                color="sky"
                                icon="badge-check"
                                :aria-label="__('Review finding')"
                                wire:click="openReviewModal({{ $row['finding_id'] }})"
                            />

                            <flux:button
                                type="button"
                                size="xs"
                                variant="primary"
                                color="amber"
                                icon="activity"
                                :aria-label="__('Show timeline')"
                                wire:click="openTimelineModal({{ $row['finding_id'] }})"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8">
                        <flux:text class="text-sm text-zinc-500">
                            {{ __('No shared-key follow-up candidates have been detected yet.') }}
                        </flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
