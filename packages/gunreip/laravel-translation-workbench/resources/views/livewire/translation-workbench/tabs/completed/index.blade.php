<flux:callout color="green" icon="check-check" :heading="__('Completed')">
    @include('translation-workbench::livewire.translation-workbench.tabs.source-replacement.filters')

    <livewire:translation-workbench.source-replacement-table
        :search="$sourceReplacementSearch"
        :finding-id="$sourceReplacementFindingId"
        :key-id="$sourceReplacementKeyId"
        :case-sensitive="$textSearchOptions['sourceReplacementSearch']['case'] ?? false"
        :whole-word="$textSearchOptions['sourceReplacementSearch']['word'] ?? false"
        :per-page="$sourceReplacementPerPage"
        :completed="true"
        :last-event="$sourceReplacementLastEvent"
        :check-version="$sourceReplacementCheckVersion"
        :test-token="$testToken"
        :key="'completed-table-' . $testToken"
    />

</flux:callout>
