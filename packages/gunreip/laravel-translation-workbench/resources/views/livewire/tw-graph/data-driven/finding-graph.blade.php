<flux:callout color="violet" class="mt-4">
    <flux:callout.heading>{{ __('Finding history') }} #{{ $finding?->id ?? '—' }}</flux:callout.heading>
    <flux:callout.text>
        {{ __('Chronological events for this finding only. Key IDs identify the assignment recorded at the event; the finding keeps its own ID.') }}
        {{ __('Events are paginated in groups of 20; no history is removed.') }}
    </flux:callout.text>
    @if ($finding && $findingEvents)
        <flux:pagination :paginator="$findingEvents" />
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="false">
            <div class="mt-3 max-h-[48rem] overflow-auto">
                <x-translation-workbench::ui.tw-graph
                    :graph-id="'finding-history-'.$finding->id.'-'.$findingEvents->currentPage().'-'.$reloadTick"
                    :dev-counter-auto="true"
                    horizontal-padding="4rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="finding-history.start"
                        direction="top-bottom"
                        color="violet"
                        :start-label="['text' => ['Finding #'.$finding->id, __('History page :page', ['page' => $findingEvents->currentPage()])], 'width' => 'long', 'contextMenu' => \Gunreip\TranslationWorkbench\Support\DatasetLabelContext::menu(['type' => 'finding', 'id' => $finding->id])]"
                    />
                    @foreach ($findingEvents as $findingEvent)
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            :id="'finding-history.event-'.$findingEvent->id"
                            :attach-to="$loop->first ? 'finding-history.start.anchorNode-end' : 'finding-history.event-'.$findingEvents[$loop->index - 1]->id.'.anchorNode-end'"
                            direction="top-bottom"
                            color="violet"
                            :step-label="['text' => \Gunreip\TranslationWorkbench\Support\TwGraph\DataDriven\FindingEventLabel::lines($findingEvent), 'width' => 'long', 'contextMenu' => \Gunreip\TranslationWorkbench\Support\DatasetLabelContext::menu(['type' => 'event', 'id' => $findingEvent->id])]"
                        />
                    @endforeach
                </x-translation-workbench::ui.tw-graph>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        @if ($findingEvents->isEmpty())
            <flux:callout.text>{{ __('No recorded events for this finding.') }}</flux:callout.text>
        @endif
    @else
        <flux:callout.text>{{ __('Select a finding belonging to this dataset.') }}</flux:callout.text>
    @endif
</flux:callout>
