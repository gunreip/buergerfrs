<flux:modal
    class="w-full max-w-7xl"
    name="source-comparison"
    wire:model="sourceComparisonOpen"
>
    <x-translation-workbench::ui.common.modal-loading
        target="applySourcePositionDecision, openWorkflowStatus, previewSelectedSource"
    />
    @if ($sourceComparison)
        @php
            $finding = $sourceComparison['finding'];
            $check = $sourceComparison['check'];
        @endphp
        <div class="space-y-4">
            <div class="flex items-start justify-between gap-3">
                <flux:heading
                    class="flex items-center gap-2"
                    size="xl"
                >
                    <flux:icon.scale class="size-8 shrink-0" />
                    {{ __('Source comparison') }}
                    ·
                    #{{ $finding['id'] }}
                </flux:heading>
                <flux:tooltip>
                    <flux:button.group class="mr-8 shrink-0">
                        <flux:button
                            variant="outline"
                            wire:click="openWorkflowStatus({{ $finding['id'] }})"
                            wire:loading.attr="disabled"
                            :aria-label="__('Workflow status')"
                        >
                            <flux:icon.workflow class="size-5" />
                        </flux:button>
                        <flux:button
                            variant="outline"
                            wire:click="openWorkflowStatus({{ $finding['id'] }})"
                            wire:loading.attr="disabled"
                        >
                            {{ __('Workflow status') }}
                        </flux:button>
                    </flux:button.group>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Workflow status')"
                        :text="__(
                            'View completed and outstanding steps for finding #:finding and its assigned translation key.',
                            ['finding' => $finding['id']],
                        )"
                    />
                </flux:tooltip>
            </div>

            <flux:callout
                color="sky"
                icon="file-search-corner"
                :heading="__('Captured finding')"
            >
                <x-translation-workbench::ui.common.details>
                    <x-translation-workbench::ui.common.details.item
                        :label="__('Key ID')"
                        :item="'#' . $finding['key_id']"
                    />
                    <x-translation-workbench::ui.common.details.item :label="__('Translation key')">
                        <x-translation-workbench::ui.common.text-preview
                            :text="$finding['full_name']"
                            :mono="true"
                        />
                    </x-translation-workbench::ui.common.details.item>
                    <x-translation-workbench::ui.common.details.item :label="__('Captured position')">
                        <x-translation-workbench::ui.common.path-preview
                            :path="$finding['path']"
                            :line="$finding['source_line']"
                            :column="$finding['source_column']"
                            :length="80"
                            side="center"
                        />
                    </x-translation-workbench::ui.common.details.item>
                    <x-translation-workbench::ui.common.details.item :label="__('Captured expression')">
                        <x-translation-workbench::ui.common.text-preview
                            :text="$finding['raw_expression']"
                            :mono="true"
                        />
                    </x-translation-workbench::ui.common.details.item>
                </x-translation-workbench::ui.common.details>
            </flux:callout>

            <flux:callout
                icon="telescope"
                :color="$check['state'] === 'position_confirmed' ? 'green' : 'amber'"
                :heading="__('Current observation')"
            >
                <flux:callout.text>{{ __($check['detail']) }}</flux:callout.text>
                <flux:callout.text>
                    {{ __('This is a read-only observation made when this window opened. It is not a saved decision and does not lock the source file.') }}
                </flux:callout.text>
                @if ($check['state'] === 'position_confirmed')
                    <div class="mt-3 flex justify-end">
                        <flux:tooltip>
                            <flux:button
                                variant="outline"
                                icon="replace-all"
                                wire:click="previewSelectedSource({{ $finding['id'] }})"
                                wire:loading.attr="disabled"
                            >{{ __('Source replacement preview') }}</flux:button>
                            <x-translation-workbench::ui.common.tooltip-content
                                :heading="__('Source replacement preview')"
                                :text="__(
                                    'Check the proposed replacement and publication requirements for this finding. Opening the preview does not change the source file.',
                                )"
                            />
                        </flux:tooltip>
                    </div>
                    <flux:error name="sourcePreview" />
                @endif
            </flux:callout>

            @foreach ($check['contexts'] ?? [] as $context)
                <flux:callout
                    class="min-w-0 [&>div]:min-w-0"
                    icon="code"
                    :color="!empty($context['captured']) && $check['state'] !== 'position_confirmed' ? 'red' : 'green'"
                    :heading="!empty($context['captured']) ? __('Current code at the captured position') : __(
                        'Possible matching occurrence')"
                >
                    @if (empty($context['captured']))
                        @php
                            $lineDifference = (int) $context['line'] - (int) $finding['source_line'];
                            $columnDifference = (int) $context['column'] - (int) $finding['source_column'];
                        @endphp
                        <x-translation-workbench::ui.common.details class="mb-3 tabular-nums">
                            <x-translation-workbench::ui.common.details.item :label="__('Position comparison')">
                                <flux:callout.text class="flex justify-start">
                                    <flux:callout.heading class="mr-2">
                                        {{ __('Captured') }}:
                                    </flux:callout.heading>
                                    {{ $finding['source_line'] }}:{{ $finding['source_column'] }}
                                    <span class="mx-2">→</span>
                                    <flux:callout.heading class="mr-2">
                                        {{ __('Possible match') }}:
                                    </flux:callout.heading>
                                    {{ $context['line'] }}:{{ $context['column'] }}
                                </flux:callout.text>
                            </x-translation-workbench::ui.common.details.item>
                            <x-translation-workbench::ui.common.details.item :label="__('Difference')">
                                <flux:callout.text class="flex justify-start">
                                    <flux:callout.heading class="mr-2">
                                        {{ __('Lines') }}:
                                    </flux:callout.heading>
                                    {{ sprintf('%+d', $lineDifference) }}
                                    <span class="mx-2">·</span>
                                    <flux:callout.heading class="mr-2">
                                        {{ __('Columns') }}:
                                    </flux:callout.heading>
                                    {{ sprintf('%+d', $columnDifference) }}
                                </flux:callout.text>
                            </x-translation-workbench::ui.common.details.item>
                        </x-translation-workbench::ui.common.details>
                    @else
                        <flux:callout.text class="tabular-nums">
                            {{ __('Line / column') }}:
                            {{ $context['line'] }}:{{ $context['column'] }}
                        </flux:callout.text>
                    @endif
                    <x-translation-workbench::ui.common.source-code
                        :lines="$context['lines'] ?? []"
                        :text="($context['text'] ?? '') !== '' ? $context['text'] : __('This position is outside the current file.')"
                        :focus-color="!empty($context['captured']) && $check['state'] !== 'position_confirmed' ? 'red' : 'green'"
                    />
                </flux:callout>
            @endforeach
            @if (count($check['positions']) > 5)
                <flux:text>
                    {{ __('Only the first five text matches are shown. No automatic assignment is possible.') }}
                </flux:text>
            @endif

            <flux:callout
                icon="code"
                color="amber"
                :heading="__('Proposed handling — not applied')"
            >
                <flux:callout.text>
                    {{ match ($check['state']) {
                        'possible_move' => __(
                            'If the context confirms the same occurrence, create a successor at the new position, archive and link the predecessor, and retain its suggested key and translation key assignment.',
                        ),
                        'source_unavailable' => __(
                            'First check whether the file was moved, renamed or is temporarily unreadable. Only if the occurrence is confirmed removed should this finding be archived as removed. Keep the shared translation key and the other findings.',
                        ),
                        'expression_missing' => __(
                            'Check whether the expression changed, was removed or moved to another file. Do not archive it or create a successor until its identity and disposition are established.',
                        ),
                        'ambiguous_matches' => __(
                            'Compare the surrounding code to identify the original occurrence. Equal text alone is not enough to choose a successor.',
                        ),
                        'source_changed' => __(
                            'Reopen the comparison to inspect a fresh file snapshot. Do not use this outdated observation.',
                        ),
                        default => __(
                            'The captured text still matches at its recorded position. No position correction is proposed. Replacement eligibility is checked separately.',
                        ),
                    } }}
                </flux:callout.text>
                <flux:callout.text>
                    {{ __('Applying a decision updates only this finding and its assignment history. Source code, language files and other findings are not rewritten. The selection is released afterwards so it can be checked again.') }}
                </flux:callout.text>
                @if (($sourceComparison['decision']['can_move'] ?? false) || ($sourceComparison['decision']['can_remove'] ?? false))
                    <div class="mt-3 space-y-3">
                        <flux:field>
                            <flux:label>{{ __('Decision') }}</flux:label>
                            <flux:select
                                wire:model.live="sourcePositionChoice"
                                :placeholder="__('Choose a decision')"
                            >
                                @if ($sourceComparison['decision']['can_move'])
                                    <flux:select.option value="moved">{{ __('Confirmed move — create a successor') }}
                                    </flux:select.option>
                                @endif
                                @if ($sourceComparison['decision']['can_remove'])
                                    <flux:select.option value="removed">
                                        {{ __('Confirmed removal — archive this finding') }}</flux:select.option>
                                @endif
                            </flux:select>
                            <flux:error name="sourcePositionChoice" />
                        </flux:field>
                        @include(
                            'translation-workbench::livewire.translation-workbench.shared.reason-select',
                            [
                                'reasonLabel' => __('Reason for source finding decision'),
                                'reasonField' => 'sourcePositionReason',
                            ]
                        )
                        <flux:checkbox
                            wire:model.live="sourcePositionConfirmed"
                            :label="__('I checked the context. This is the same occurrence at a new position, or a confirmed removal rather than a move, rename or temporary access problem.')"
                        />
                        <flux:error name="sourcePositionConfirmed" />
                    </div>
                @else
                    <flux:callout.text>
                        {{ __('No supported decision is available for this observation. Resolve ambiguous matches or access problems and check again.') }}
                    </flux:callout.text>
                @endif

                <flux:error name="sourcePositionDecision" />

                <div class="mt-3 flex justify-end">
                    <flux:button.group>
                        <flux:button
                            variant="outline"
                            wire:click="applySourcePositionDecision"
                            wire:loading.attr="disabled"
                            :disabled="!$sourcePositionConfirmed || $sourcePositionChoice === ''"
                        >
                            <flux:icon.clapperboard class="size-5" />
                        </flux:button>
                        <flux:button
                            variant="outline"
                            wire:click="applySourcePositionDecision"
                            wire:loading.attr="disabled"
                            :disabled="!$sourcePositionConfirmed || $sourcePositionChoice === ''"
                        >
                            {{ __('Apply decision') }}
                        </flux:button>
                    </flux:button.group>
                </div>
            </flux:callout>
        </div>
    @endif
</flux:modal>
