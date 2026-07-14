{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/old/entries.blade.php --}}

<flux:card>
    <x-ui.headers.page
        :title="__('Translation Workbench')"
        :description="__('Collected translation-capable literals and translation calls from the new package scanner.')"
    />

    {{-- DEV-Playground Card --}}
    <flux:card class="mt-6">
        <x-ui.headers.card
            :title="__('DEV-Playground')"
            :description="__('This is a sample Text for DEV-playground, testing the review, edit and timline-part.')"
        />
    </flux:card>

    @include('translation-workbench::livewire.old.entries.overview')

    @include('translation-workbench::livewire.old.entries.filters')

    @include('translation-workbench::livewire.old.entries.table-dynamic')

    @include('translation-workbench::livewire.old.entries.table-entries')

    @include('translation-workbench::livewire.old.entries.modal-review', [
        'entry' => $reviewEntry,
        'canCountOccurrences' => $canCountOccurrences,
        'nextReviewEntryId' => $nextReviewEntryId,
    ])

    @include('translation-workbench::livewire.old.entries.modal-edit', [
        'entry' => $editEntry,
        'editLocales' => $editLocales,
        'editValues' => $editValues,
    ])

    @include('translation-workbench::livewire.old.entries.modal-edit-dynamic', [
        'entry' => $dynamicEditEntry,
        'editLocales' => $editLocales,
        'dynamicEditRows' => $dynamicEditRows,
        'nextDynamicTranslationEntryId' => $nextDynamicTranslationEntryId,
    ])

    @include('translation-workbench::livewire.old.entries.modal-edit-translation-key', [
        'entry' => $translationKeyEntry,
    ])
</flux:card>
