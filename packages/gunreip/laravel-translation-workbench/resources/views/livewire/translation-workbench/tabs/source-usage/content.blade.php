<div>
    <flux:callout
        class="relative"
        color="sky"
        icon="star"
    >
        <x-ui.loading.lazy-indicator
            class="absolute inset-0 z-40"
            role="status"
            box-class="h-full w-full rounded-lg bg-white/75 dark:bg-zinc-900/75"
            :text="__('Loading')"
        />
        @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.header')
        @if (!$ready)
            <flux:callout.text>
                {{ __('The writable generation is unavailable. No decision can be saved.') }}
            </flux:callout.text>
        @else
            <flux:callout.heading>
                {{ __('Data source') }}:
                <flux:callout.text>
                    <code>{{ $databaseName }} / {{ $schemaName }}</code>.
                    {{ __('This list contains source-usage review cases. Captured findings and their translation drafts are available in the Findings tab.') }}
                </flux:callout.text>
            </flux:callout.heading>
            @if ($notice !== '')
                <flux:callout
                    class="my-3"
                    color="green"
                >
                    {{ $notice }}
                </flux:callout>
            @endif
            @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.filters')

            @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.table')
        @endif
    </flux:callout>

    @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.decision-modal')

    @include('translation-workbench::livewire.translation-workbench.modals.workflow-reason')

</div>
