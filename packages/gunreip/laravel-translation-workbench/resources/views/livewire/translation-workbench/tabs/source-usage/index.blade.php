<livewire:translation-workbench.source-case-review
    :workbench-page-loading="true"
    :test-token="$testToken"
    :key="'source-usage-' . $testToken"
/>
<div class="mt-4">
    @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.observed-values')
</div>
