<flux:callout
    class="space-y-4"
    color="amber"
    icon="scan-eye"
    heading="{{ __('Review') }} · {{ number_format($reviewTasks->total()) }} {{ __('Language revisions') }}"
    text="{{ __('Explicitly submitted saved revisions. Approval does not publish files. Return a revision to Translations when corrections are needed.') }}"
>

    @include('translation-workbench::livewire.translation-workbench.tabs.review.filters')

    @include('translation-workbench::livewire.translation-workbench.tabs.review.table')
</flux:callout>
