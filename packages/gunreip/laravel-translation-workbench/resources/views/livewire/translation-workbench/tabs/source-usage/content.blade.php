<div>
    <flux:callout
        class="relative"
        color="sky"
        icon="star"
    >
        @if (!$modalOpen && !$usageWorkflowOpen && !$workflowReasonOpen)
            <x-ui.loading.lazy-indicator
                :overlay="true"
                role="status"
                :text="__('Loading')"
            />
        @endif
        @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.header')
        @if (!$ready)
            <flux:callout.text>
                {{ __('The writable generation is unavailable. No decision can be saved.') }}
            </flux:callout.text>
        @else
            <x-translation-workbench::ui.common.details>
                <x-translation-workbench::ui.common.details.item :label="__('Data source')">
                    <flux:callout.text>
                        <code>{{ $databaseName }} / {{ $schemaName }}
                        </code>
                    </flux:callout.text>
                </x-translation-workbench::ui.common.details.item>
            </x-translation-workbench::ui.common.details>
            <flux:callout.text>
                {{ __('This list contains source-usage review cases. Captured findings and their translation drafts are available in the Findings tab.') }}
            </flux:callout.text>


            @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.filters')

            @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.table')
        @endif
    </flux:callout>

    @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.decision-modal')
    @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.workflow-modal')
    @include('translation-workbench::livewire.translation-workbench.modals.workflow-reason')

</div>
