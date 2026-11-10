<flux:callout
    class="space-y-4"
    color="green"
    icon="book-check"
    heading="{{ __('Published') }} · {{ number_format($publishedTasks->total()) }} {{ __('Language revisions') }}"
    text="{{ __('Latest completed publication per translation key and language. New drafts do not replace the published text until publication. Open the publication result to check the current files.') }}"
>

    @include('translation-workbench::livewire.translation-workbench.tabs.published.filters')

    @include('translation-workbench::livewire.translation-workbench.tabs.published.table')
</flux:callout>
