<flux:tab.group id="workbench-task-tabs"
    class="relative"
    findable
>
    <x-ui.loading.lazy-indicator
        class="absolute inset-0 z-40"
        role="status"
        box-class="h-full w-full rounded-lg bg-white/75 dark:bg-zinc-900/75"
        target="sortPublication, sortPublished, publishedKeyId, publishedLanguage, publishedSearch, publishedPerPage, openPublication, publicationKeyId, publicationLanguage, publicationSearch, publicationPerPage, openPublicationTask, preparePublicationTask, sortReview, reviewKeyId, reviewLanguage, reviewSearch, reviewPerPage, openReviewTask, tab, translationKeyId, translationSearch, translationPerPage, findingId, search, status, toggleArchived, toggleUnassigned, perPage, sortFindings, sortTranslations, gotoPage, nextPage, previousPage, setPage, open, openShared"
        :text="__('Loading')"
    />
    {{-- Tabs --}}
    <flux:tabs wire:model.live="tab">
        <flux:tab name="findings">{{ __('Findings') }}</flux:tab>
        <flux:tab name="translations">{{ __('Translations') }}</flux:tab>
        <flux:tab name="review">{{ __('Review') }}</flux:tab>
        <flux:tab name="publication">{{ __('Publication') }}</flux:tab>
        <flux:tab name="published">{{ __('Published') }}</flux:tab>
        <flux:tab name="source-usage">{{ __('Source usage decisions') }}</flux:tab>
    </flux:tabs>

    <flux:tab.panel name="findings">
        @include('translation-workbench::livewire.translation-workbench.tabs.findings.index')
    </flux:tab.panel>

    <flux:tab.panel name="translations">
        @if ($tab === 'translations')
            @include('translation-workbench::livewire.translation-workbench.tabs.translations.index')
        @endif
    </flux:tab.panel>

    <flux:tab.panel name="review">
        @if ($tab === 'review')
            @include('translation-workbench::livewire.translation-workbench.tabs.review.index')
        @endif
    </flux:tab.panel>

    <flux:tab.panel name="publication">
        @if ($tab === 'publication')
            @include('translation-workbench::livewire.translation-workbench.tabs.publication.index')
        @endif
    </flux:tab.panel>

    <flux:tab.panel name="published">
        @if ($tab === 'published')
            @include('translation-workbench::livewire.translation-workbench.tabs.published.index')
        @endif
    </flux:tab.panel>

    <flux:tab.panel name="source-usage">
        @if ($tab === 'source-usage')
            @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.index')
        @endif
    </flux:tab.panel>
</flux:tab.group>
