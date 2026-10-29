<flux:callout
    class="space-y-4"
    color="teal"
    icon="book-open-check"
    heading="{{ __('Publication') }} · {{ number_format($publicationTasks->total()) }} {{ __('Language revisions') }}"
    text="{{ __('Approved language revisions awaiting publication. Inspect the saved text, prepare the file preview and confirm writing separately.') }}"
>
    {{-- <flux:callout.heading>
        {{ __('Publication') }} · {{ number_format($publicationTasks->total()) }}
        {{ __('Language revisions') }}
    </flux:callout.heading>
    <flux:callout.text>
        {{ __('Approved language revisions awaiting publication. Inspect the saved text, prepare the file preview and confirm writing separately.') }}
    </flux:callout.text> --}}

    @include('translation-workbench::livewire.translation-workbench.tabs.publication.filters')

    <flux:error name="publicationTask" />

    @include('translation-workbench::livewire.translation-workbench.tabs.publication.table')
</flux:callout>
