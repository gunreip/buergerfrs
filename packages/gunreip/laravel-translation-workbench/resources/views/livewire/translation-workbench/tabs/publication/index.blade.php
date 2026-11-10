<flux:callout
    class="space-y-4"
    color="teal"
    icon="book-open-check"
    heading="{{ __('Publication') }} · {{ number_format($publicationTasks->total()) }} {{ __('Language revisions') }}"
    text="{{ __('Approved language revisions awaiting publication. Inspect the saved text, prepare the file preview and confirm writing separately.') }}"
>

    @include('translation-workbench::livewire.translation-workbench.tabs.publication.filters')

    {{-- <div class="flex justify-end">
        <flux:button variant="outline" icon="eye" wire:click="openPublicationTask" :disabled="count($selectedPublicationRevisions) === 0" wire:loading.attr="disabled">
            {{ __('Prepare selected revisions (:count)', ['count' => count($selectedPublicationRevisions)]) }}
        </flux:button>
    </div> --}}

    <flux:error name="selectedPublicationRevisions" />

    <flux:error name="publicationTask" />

    @include('translation-workbench::livewire.translation-workbench.tabs.publication.table')
</flux:callout>
