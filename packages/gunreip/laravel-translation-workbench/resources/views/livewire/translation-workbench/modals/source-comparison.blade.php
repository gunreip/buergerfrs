<flux:modal
    class="w-full max-w-7xl"
    name="source-comparison"
    wire:model="sourceComparisonOpen"
>
    <x-translation-workbench::ui.common.modal-loading
        target="openSourceComparison, applySourcePositionDecision, openWorkflowStatus, inspectSourcePositionOccupant, previewSelectedSource, previewComparisonPositions"
    />
    @if ($sourceComparison)
        @php
            $finding = $sourceComparison['finding'];
            $check = $sourceComparison['check'];
            $occupied = $sourceComparison['decision']['occupied'] ?? [];
            $allTargetsOccupied = $check['positions'] !== [] && count($occupied) === count($check['positions']);
            $retained = $sourceComparison['decision']['retained'] ?? null;
            $single = count($check['positions']) === 1 ? $check['positions'][0] : null;
            $target = $single ? $single['line'] . ':' . $single['column'] : $sourcePositionTarget;
            $applyDisabled =
                !$sourcePositionConfirmed ||
                $sourcePositionChoice === '' ||
                ($sourcePositionChoice === 'moved' && ($target === '' || isset($occupied[$target])));
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
            {{-- Captured Finding --}}
            <flux:callout
                color="sky"
                icon="file-search-corner"
                :heading="__('Captured finding')"
            >
                <x-translation-workbench::ui.common.details>
                    @if ($finding['key_id'])
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
                    @endif
                    <x-translation-workbench::ui.common.details.item :label="__('Captured position')">
                        <x-translation-workbench::ui.common.source-position-link
                            :path="$finding['path']"
                            :line="$finding['source_line']"
                            :column="$finding['source_column']"
                            :show-position="true"
                        />
                    </x-translation-workbench::ui.common.details.item>
                    <x-translation-workbench::ui.common.details.item
                        class="flex flex-wrap items-baseline gap-x-2"
                        :label="__('Position captured by scan at')"
                    >
                        <x-ui.date-time.date-time :value="$finding['captured_at'] ?? null" />
                        @if (!empty($finding['captured_at']))
                            <x-ui.date-time.ago :value="$finding['captured_at']" />
                        @endif
                    </x-translation-workbench::ui.common.details.item>
                    <x-translation-workbench::ui.common.details.item :label="__('Captured expression')">
                        <x-translation-workbench::ui.common.text-preview
                            :text="$finding['raw_expression']"
                            :mono="true"
                        />
                    </x-translation-workbench::ui.common.details.item>
                </x-translation-workbench::ui.common.details>
            </flux:callout>
            {{-- Current Observation --}}
            <flux:callout
                icon="telescope"
                :color="$check['state'] === 'position_confirmed' ? 'green' : 'amber'"
            >
                <div class="flex items-start justify-between gap-3">
                    <flux:callout.heading>{{ __('Current observation') }}</flux:callout.heading>
                    @if ($retained)
                        @include('translation-workbench::livewire.translation-workbench.shared.duplicate-finding-badge', ['findingId' => $finding['id']])
                    @elseif (count($check['positions']) > 1)
                        <flux:badge
                            size="sm"
                            color="amber"
                        >{{ __('Ambiguous matches') }}</flux:badge>
                    @endif
                </div>
                <flux:callout.text>{{ __($check['detail']) }}</flux:callout.text>
                <flux:callout.text>
                    {{ __('This is a read-only observation made when this window opened. It is not a saved decision and does not lock the source file.') }}
                </flux:callout.text>
                @if ($check['state'] !== 'position_confirmed' && $testToken === '')
                    <flux:button.group class="flex justify-end">
                        <flux:button
                            class="mt-3"
                            size="sm"
                            variant="outline"
                            wire:click="previewComparisonPositions"
                            wire:loading.attr="disabled"
                        >
                            <flux:icon.scan-text class="size-5" />
                        </flux:button>
                        <flux:button
                            class="mt-3"
                            size="sm"
                            variant="outline"
                            wire:click="previewComparisonPositions"
                            wire:loading.attr="disabled"
                        >
                            {{ __('Open scan position preview for finding #:id', ['id' => $finding['id']]) }}
                        </flux:button>
                    </flux:button.group>
                @endif
                @if ($check['state'] === 'position_confirmed' && empty($sourceComparison['from_issue']))
                    <div class="mt-3 flex justify-end">
                        <flux:tooltip>
                            <flux:button.group>
                                <flux:button
                                    variant="outline"
                                    wire:click="previewSelectedSource({{ $finding['id'] }})"
                                    wire:loading.attr="disabled"
                                >
                                    <flux:icon.replace-all class="size-5" />
                                </flux:button>
                                <flux:button
                                    variant="outline"
                                    wire:click="previewSelectedSource({{ $finding['id'] }})"
                                    wire:loading.attr="disabled"
                                >
                                    {{ __('Source replacement preview') }}
                                </flux:button>
                            </flux:button.group>
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
            {{-- Captured Code At The Captured Position / Possible Matching Occurence --}}
            @foreach ($check['contexts'] ?? [] as $context)
                @php
                    $coordinate = $context['line'] . ':' . $context['column'];
                    $selected = empty($context['captured']) && $sourcePositionChoice === 'moved' && $sourcePositionTarget === $coordinate;
                @endphp
                <flux:callout
                    class="min-w-0 [&>div]:min-w-0"
                    icon="code"
                    :color="!empty($context['captured']) && $check['state'] !== 'position_confirmed' ? 'red' : 'green'"
                >
                    <flux:accordion transition>
                        <flux:accordion.item>
                            <flux:accordion.heading>
                                <flux:callout.heading>
                                    {{ !empty($context['captured']) ? __('Current code at the captured position') : __('Possible matching occurrence') }}
                                    · {{ $coordinate }}
                                    @if ($selected)
                                        <flux:badge color="green" size="sm" icon="check">{{ __('Selected position') }}</flux:badge>
                                    @endif
                                </flux:callout.heading>
                            </flux:accordion.heading>
                            <flux:accordion.content>
                                @php
                                    $isCaptured = !empty($context['captured']);
                                    $occupants = $isCaptured
                                        ? [
                                            [
                                                'id' => $finding['id'],
                                                'expression' => $finding['raw_expression'],
                                                'captured_at' => $finding['captured_at'] ?? null,
                                            ],
                                        ]
                                        : $occupied[$coordinate] ?? [];
                                @endphp
                                <flux:callout
                                    class="mb-2"
                                    icon="summary"
                                    :color="!empty($context['captured']) && $check['state'] !== 'position_confirmed' ? 'red' :
                                        'green'"
                                >
                                    <flux:accordion>
                                        <flux:accordion.item>
                                            <flux:accordion.heading>
                                                <flux:callout.heading>
                                                    {{ __('Summary') }}
                                                </flux:callout.heading>
                                            </flux:accordion.heading>
                                            <flux:accordion.content>
                                                <x-translation-workbench::ui.common.details
                                                    class="mb-3 tabular-nums"
                                                    label-width="14rem"
                                                >
                                                    <x-translation-workbench::ui.common.details.item
                                                        :label="__('Captured finding')"
                                                        :item="'#' . $finding['id']"
                                                    />
                                                    <x-translation-workbench::ui.common.details.item :label="__('Captured in')">
                                                        <x-translation-workbench::ui.common.source-position-link
                                                            :path="$finding['path']"
                                                            :line="$context['line']"
                                                            :column="$context['column']"
                                                        />
                                                    </x-translation-workbench::ui.common.details.item>
                                                    @foreach ($occupants as $occupant)
                                                        <x-translation-workbench::ui.common.details.item
                                                            :label="__('Finding recorded at this position')"
                                                            :item="'#' .
                                                                $occupant['id'] .
                                                                ' · ' .
                                                                $occupant['expression']"
                                                        />
                                                        @if ($isCaptured || !empty($occupant['captured_at']))
                                                            <x-translation-workbench::ui.common.details.item
                                                                class="flex flex-wrap items-baseline gap-x-2"
                                                                :label="__('Finding #:id first captured at', [
                                                                    'id' => $occupant['id'],
                                                                ])"
                                                            >
                                                                <x-ui.date-time.date-time :value="$occupant['captured_at'] ?? null" />
                                                                @if (!empty($occupant['captured_at']))
                                                                    <x-ui.date-time.ago :value="$occupant['captured_at']" />
                                                                @endif
                                                            </x-translation-workbench::ui.common.details.item>
                                                        @endif
                                                    @endforeach
                                                    @if (!$occupants)
                                                        <x-translation-workbench::ui.common.details.item
                                                            :label="__('First captured at')"
                                                            :item="__(
                                                                'No active finding recorded at this position.',
                                                            )"
                                                        />
                                                    @endif
                                                    @if ($isCaptured)
                                                        <x-translation-workbench::ui.common.details.item
                                                            :label="__('Line / column')"
                                                            :item="$coordinate"
                                                        />
                                                    @else
                                                        @php
                                                            $lineDifference =
                                                                (int) $context['line'] - (int) $finding['source_line'];
                                                            $columnDifference =
                                                                (int) $context['column'] -
                                                                (int) $finding['source_column'];
                                                        @endphp
                                                        <x-translation-workbench::ui.common.details.item
                                                            :label="__('Position comparison')"
                                                        >
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
                                                        <x-translation-workbench::ui.common.details.item
                                                            :label="__('Difference')"
                                                        >
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
                                                    @endif
                                                </x-translation-workbench::ui.common.details>
                                            </flux:accordion.content>
                                        </flux:accordion.item>
                                    </flux:accordion>
                                </flux:callout>
                                @isset($context['expression'])
                                    <x-translation-workbench::ui.common.details class="mb-2">
                                        <x-translation-workbench::ui.common.details.item
                                            :label="__('Captured call')"
                                            :item="$finding['raw_expression']"
                                        />
                                        <x-translation-workbench::ui.common.details.item
                                            :label="($context['change'] ?? null) === 'reformatted' ? __('Reformatted call') : __('Changed call')"
                                            :item="$context['expression']"
                                        />
                                    </x-translation-workbench::ui.common.details>
                                @endisset
                                <x-translation-workbench::ui.common.source-code
                                    :lines="$context['lines'] ?? []"
                                    :text="($context['text'] ?? '') !== '' ? $context['text'] : __('This position is outside the current file.')"
                                    :focus-color="!empty($context['captured']) && $check['state'] !== 'position_confirmed' ? 'red' : 'green'"
                                />
                                @if (empty($context['captured']) && $occupants)
                                    @include('translation-workbench::livewire.translation-workbench.shared.source-position-inspect')
                                @elseif (!$isCaptured && count($check['positions']) > 1 && ($sourceComparison['decision']['can_move'] ?? false))
                                    <flux:button
                                        class="mt-2"
                                        size="sm"
                                        :disabled="$selected"
                                        :icon="$selected ? 'check' : 'map-pin'"
                                        wire:click="selectSourcePosition({{ $context['line'] }}, {{ $context['column'] }})"
                                    >
                                        {{ $selected ? __('Selected position') : __('Select this position') }}
                                    </flux:button>
                                @endif
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:callout>
            @endforeach

            @if (count($check['positions']) > 5)
                <flux:text>
                    {{ __('Only the first five text matches are shown. No automatic assignment is possible.') }}
                </flux:text>
                @foreach (array_slice($check['positions'], 5) as $position)
                    @php
                        $coordinate = $position['line'] . ':' . $position['column'];
                        $occupants = $occupied[$coordinate] ?? [];
                    @endphp
                    @if ($occupants)
                        @include('translation-workbench::livewire.translation-workbench.shared.source-position-inspect')
                    @endif
                @endforeach
            @endif
            {{-- Proposed Handling --}}
            <flux:callout
                icon="handshake"
                color="amber"
                :heading="__('Proposed handling — not applied')"
            >
                <flux:callout.text>
                    @if ($retained)
                        {{ __('Finding #:active already records the exact call at :position. If it is the same occurrence, keep that finding and archive #:old as a duplicate capture.', ['active' => $retained, 'position' => $target, 'old' => $finding['id']]) }}
                    @else
                    {{ match ($check['state']) {
                        'changed_matches' => __(
                            'Compare the captured and current calls. Confirming the occurrence links its new capture to the original finding and keeps the translation key assignment.',
                        ),
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
                    @endif
                </flux:callout.text>
                <flux:callout.text>
                    {{ __('Applying a decision updates only this finding and its assignment history. Source code, language files and other findings are not rewritten. The selection is released afterwards so it can be checked again.') }}
                </flux:callout.text>
                @if ($allTargetsOccupied && !$retained)
                    <flux:callout.text>
                        {{ __('Every matching position is occupied by an active finding. Inspect the finding at the intended position and check its captured location in the scan preview. After resolving the conflict, refresh this comparison.') }}
                    </flux:callout.text>
                @endif
                @if (
                    $retained || (!$allTargetsOccupied && ($sourceComparison['decision']['can_move'] ?? false)) ||
                        ($sourceComparison['decision']['can_remove'] ?? false))
                    <div class="mt-3 space-y-3">
                        <flux:field>
                            <flux:label>
                                <flux:callout.heading>
                                    {{ __('Decision') }}
                                </flux:callout.heading>
                            </flux:label>
                            <flux:select
                                variant="listbox"
                                wire:model.live="sourcePositionChoice"
                                :placeholder="__('Choose a decision')"
                            >
                                @if (!$allTargetsOccupied && $sourceComparison['decision']['can_move'])
                                    <flux:select.option value="moved">
                                        {{ $check['state'] === 'changed_matches' ? __('Confirm matching occurrence — create a successor') : __('Confirmed move — create a successor') }}
                                    </flux:select.option>
                                @endif
                                @if ($retained)
                                    <flux:select.option value="duplicate">
                                        {{ __('Keep #:active and archive #:old as duplicate', ['active' => $retained, 'old' => $finding['id']]) }}
                                    </flux:select.option>
                                @endif
                                @if ($sourceComparison['decision']['can_remove'])
                                    <flux:select.option value="removed">
                                        {{ __('Confirmed removal — archive this finding') }}</flux:select.option>
                                @endif
                            </flux:select>
                            <flux:error name="sourcePositionChoice" />
                        </flux:field>
                        @if (count($check['positions']) > 1)
                            <flux:field>
                                <flux:label>
                                    <flux:callout.heading>
                                        {{ __('Matching position (line:column)') }}
                                    </flux:callout.heading>
                                </flux:label>
                                <flux:select
                                    variant="listbox"
                                    wire:model.live="sourcePositionTarget"
                                    :placeholder="__('Choose the matching occurrence')"
                                >
                                    @foreach ($check['positions'] as $position)
                                        @php($coordinate = $position['line'] . ':' . $position['column'])
                                        <flux:select.option
                                            :value="$coordinate"
                                            :disabled="isset($occupied[$coordinate])"
                                        >
                                            {{ $coordinate }}
                                            @foreach ($occupied[$coordinate] ?? [] as $occupant)
                                                · {{ __('Occupied by finding #:id', ['id' => $occupant['id']]) }} ·
                                                {{ $occupant['expression'] }}
                                            @endforeach
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                        @endif
                        @include(
                            'translation-workbench::livewire.translation-workbench.shared.reason-select',
                            [
                                'reasonLabel' => __('Reason for source finding decision'),
                                'reasonField' => 'sourcePositionReason',
                            ]
                        )

                    </div>
                @else
                    <flux:callout.text>
                        {{ __('No supported decision is available for this observation. Resolve ambiguous matches or access problems and check again.') }}
                    </flux:callout.text>
                @endif

                @if ($sourcePositionFailure !== '')
                    <flux:callout
                        color="red"
                        icon="exclamation-triangle"
                        :heading="__('Decision not saved')"
                        :text="$sourcePositionFailure"
                    />
                @endif

                <div class="flex justify-end gap-3">
                    <flux:button.group>
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="{{ empty($sourceComparison['from_issue']) ? 'openSourceComparison' : 'openSourceIssue' }}({{ $finding['id'] }})"
                        >
                            <flux:icon.arrow-path class="size-5" />
                        </flux:button>
                        <flux:button
                            size="sm"
                            variant="outline"
                            wire:click="{{ empty($sourceComparison['from_issue']) ? 'openSourceComparison' : 'openSourceIssue' }}({{ $finding['id'] }})"
                        >
                            {{ __('Refresh comparison') }}
                        </flux:button>
                    </flux:button.group>
                    <flux:button.group>
                        <flux:button
                            variant="filled"
                            color="amber"
                            size="sm"
                            wire:click="applySourcePositionDecision"
                            wire:loading.attr="disabled"
                            :disabled="$applyDisabled"
                        >
                            <flux:icon.clapperboard class="size-5" />
                        </flux:button>
                        <flux:button
                            variant="filled"
                            color="amber"
                            size="sm"
                            wire:click="applySourcePositionDecision"
                            wire:loading.attr="disabled"
                            :disabled="$applyDisabled"
                        >
                            {{ __('Apply decision') }}
                        </flux:button>
                    </flux:button.group>
                </div>
            </flux:callout>
        </div>
    @endif
</flux:modal>
