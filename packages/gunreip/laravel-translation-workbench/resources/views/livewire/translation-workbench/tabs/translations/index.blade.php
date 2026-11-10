<flux:callout
    class="space-y-4"
    color="sky"
    icon="key"
    heading="{{ __('Translations') }} · {{ number_format($translationTasks->total()) }} {{ __('Keys') }}"
    text="{{ __('One entry per assigned key. Multiple findings can use the same translation. Saved language revisions are listed here; saving a draft does not submit it for review.') }}"
>
    @include('translation-workbench::livewire.translation-workbench.tabs.translations.filters')

    @include('translation-workbench::livewire.translation-workbench.tabs.translations.table')
</flux:callout>
