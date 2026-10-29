@php
    $workflowStates = $findingWorkflowStates[$workflowFinding['id']] ?? [];
    $workflowGroups = collect($workflowStates)->groupBy(
        fn($entry) => $entry['state'] . '|' . explode('-', str_replace('_', '-', $entry['locale']))[0],
    );
@endphp
@if (!$workflowFinding['key_id'])
    <flux:badge
        size="sm"
        color="amber"
    >{{ __('Not assigned') }}</flux:badge>
@elseif ($workflowStates === [])
    <flux:badge
        size="sm"
        color="sky"
    >{{ __('Assigned') }}</flux:badge>
@else
    @foreach ($workflowGroups as $workflowGroup)
        @php
            $baseLocale = explode('-', str_replace('_', '-', $workflowGroup->first()['locale']))[0];
            $workflowState = $workflowGroup->firstWhere('locale', $baseLocale) ?? $workflowGroup->first();
            $additionalCount = $workflowGroup->count() - 1;
        @endphp
        @php
            [$workflowLabel, $workflowColor, $workflowDescription] = match ($workflowState['state']) {
                'published' => [
                    __('Published'),
                    'green',
                    __(
                        'The current revision has current approval and a completed publication record. This is not a live file check.',
                    ),
                ],
                'approved' => [
                    __('Approved'),
                    'lime',
                    __(
                        'The current revision is approved for the current key and linked findings. Publication is still pending.',
                    ),
                ],
                'review' => [
                    __('In review'),
                    'sky',
                    __('The current revision was submitted for review and is awaiting a decision.'),
                ],
                default => [
                    __('Draft'),
                    'amber',
                    __(
                        'The saved revision has no current submission or approval for the current key and linked findings.',
                    ),
                ],
            };
            $workflowDetails = [['label' => __('Status'), 'value' => $workflowDescription]];
            foreach ($workflowGroup as $languageState) {
                $details = __('Revision') . ' #' . $languageState['revision'];
                if ($languageState['published_revision'] !== null) {
                    $details .= ' · ' . __('Published revision') . ' #' . $languageState['published_revision'];
                }
                $workflowDetails[] = ['label' => $languageState['locale'], 'value' => $details];
            }
        @endphp
        <flux:tooltip toggleable>
            <flux:badge
                class="hover:cursor-pointer"
                as="button"
                size="sm"
                :color="$workflowColor"
                :aria-label="$workflowLabel.
                ' · '.$workflowState['locale'].($additionalCount ? ' (+'.$additionalCount.
                    ')' : '')"
            >
                {{ $workflowLabel }}
                @if ($translationFlagCountries[$workflowState['locale']] ?? null)
                    <flux:flag
                        class="ml-1"
                        :country="$translationFlagCountries[$workflowState['locale']]"
                        size="xs"
                        circle
                    />
                @else
                    {{ $workflowState['locale'] }}
                @endif
                @if ($additionalCount > 0)
                    <span class="ml-1">(+{{ $additionalCount }})</span>
                @endif
            </flux:badge>
            <x-translation-workbench::ui.common.tooltip-content-tabular
                :heading="$workflowLabel"
                :rows="$workflowDetails"
            />
        </flux:tooltip>
    @endforeach
@endif
