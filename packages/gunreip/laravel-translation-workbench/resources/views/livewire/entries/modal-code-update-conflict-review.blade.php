{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-code-update-conflict-review.blade.php --}}

@php
    $context = $codeUpdateConflictReview ?? null;
    $finding = $context['finding'] ?? null;
    $applyRow = $context['apply_row'] ?? null;
    $latestReview = $context['latest_review'] ?? null;
@endphp

<flux:modal
    class="w-[min(64rem,calc(100vw-4rem))] max-w-full"
    wire:model.self="codeUpdateConflictReviewModalOpen"
>
    <div class="space-y-4">
        <x-ui.headers.card
            :title="__('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.review_duplicate_expression')"
            :description="__(
                'Store the explicit decision for a duplicate expression before source code updates are applied.',
            )"
        >
            <div class="flex flex-wrap items-center gap-2">
                @if ($finding)
                    <flux:badge size="sm">F#{{ $finding->id }}</flux:badge>
                @endif

                @if ($codeUpdateConflictKeyId)
                    <flux:badge size="sm">K#{{ $codeUpdateConflictKeyId }}</flux:badge>
                @endif

                @if ($latestReview)
                    <flux:badge
                        size="sm"
                        color="cyan"
                    >
                        {{ __('Reviewed') }}:
                        {{ str($latestReview->decision)->replace('_', ' ')->title()->toString() }}
                    </flux:badge>
                @else
                    <flux:badge
                        size="sm"
                        color="red"
                    >
                        {{ __('Review needed') }}
                    </flux:badge>
                @endif
            </div>
        </x-ui.headers.card>

        @if ($finding)
            <div class="grid grid-cols-1 gap-3 xl:grid-cols-2">
                <flux:callout
                    color="sky"
                    icon="map-pin"
                >
                    <flux:callout.heading>{{ __('ui.source.source') }}</flux:callout.heading>
                    <flux:callout.text>
                        <div class="wrap-anywhere font-mono text-xs">
                            {{ $finding->source_path }}@if ($finding->source_line)
                                :{{ $finding->source_line }}
                            @endif
                        </div>
                    </flux:callout.text>
                </flux:callout>

                <flux:callout
                    color="amber"
                    icon="copy-x"
                >
                    <flux:callout.heading>{{ __('ui.apply.apply-conflict') }}</flux:callout.heading>
                    <flux:callout.text>
                        <div class="flex flex-wrap gap-2">
                            <flux:badge size="sm">
                                {{ __('Occurrences') }}: {{ $applyRow['occurrences'] ?? __('Unknown') }}
                            </flux:badge>
                            <flux:badge size="sm">
                                {{ __('ui.state.state') }}: {{ $applyRow['state'] ?? 'duplicate_expression' }}
                            </flux:badge>
                        </div>
                    </flux:callout.text>
                </flux:callout>
            </div>

            <flux:callout
                color="zinc"
                icon="code"
            >
                <flux:callout.heading>{{ __('Expression') }}</flux:callout.heading>
                <flux:callout.text>
                    <div class="grid grid-cols-1 gap-3 xl:grid-cols-2">
                        <div>
                            <flux:text class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                                {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.current_expression') }}
                            </flux:text>
                            <div class="wrap-anywhere mt-1 font-mono text-xs">
                                {{ $applyRow['raw_expression'] ?? $finding->raw_expression }}
                            </div>
                        </div>

                        <div>
                            <flux:text class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                                {{ __('Planned expression') }}
                            </flux:text>
                            <div class="wrap-anywhere mt-1 font-mono text-xs text-green-700 dark:text-green-200">
                                {{ $applyRow['new_expression'] ?? __('Not available') }}
                            </div>
                        </div>
                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:field>
                <flux:label>{{ __('Conflict decision') }}</flux:label>
                <flux:radio.group
                    variant="segmented"
                    wire:model.live="codeUpdateConflictDecision"
                >
                    <flux:radio
                        value="duplicate_confirmed_same_key"
                        :label="__('Same expression is valid')"
                    />
                    <flux:radio
                        value="existing_key_should_be_replaced"
                        :label="__('Replace existing key')"
                    />
                    <flux:radio
                        value="duplicate_dynamic_manual_workflow"
                        :label="__('Dynamic/manual workflow')"
                    />
                    <flux:radio
                        value="ignore_for_now"
                        :label="__('Ignore for now')"
                    />
                </flux:radio.group>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Review note') }}</flux:label>
                <flux:textarea
                    rows="3"
                    wire:model.live.debounce.300ms="codeUpdateConflictNote"
                    :placeholder="__('Optional note for later review or timeline context')"
                />
            </flux:field>
        @else
            <flux:callout
                color="amber"
                icon="triangle-alert"
            >
                <flux:callout.heading>{{ __('Conflict context unavailable') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('Refresh the apply report and open the conflict review again.') }}
                </flux:callout.text>
            </flux:callout>
        @endif

        <div class="flex justify-end gap-2">
            <flux:button
                type="button"
                variant="subtle"
                wire:click="closeCodeUpdateConflictReview"
            >
                {{ __('ui.button.cancel') }}
            </flux:button>

            <flux:button
                type="button"
                variant="primary"
                icon="save"
                wire:click="saveCodeUpdateConflictReview"
                :disabled="!$finding"
            >
                {{ __('Save review') }}
            </flux:button>
        </div>
    </div>
</flux:modal>
