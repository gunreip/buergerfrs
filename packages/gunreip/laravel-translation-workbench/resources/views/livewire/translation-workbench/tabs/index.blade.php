<flux:tab.group
    class="relative"
    id="workbench-task-tabs"
    findable
>
    @if (
        !(
            $editorOpen ||
            $sharedOpen ||
            $confirmOpen ||
            $assignmentReasonOpen ||
            $changeReasonOpen ||
            $translationKeyOpen ||
            $reviewOpen ||
            $reviewBatchOpen ||
            $publicationTaskOpen ||
            $publicationOpen ||
            $sourcePreviewOpen ||
            $sourceRunOpen ||
            $scanOpen ||
            $scanUsageOpen ||
            $sharedConfirmOpen ||
            $workflowReasonOpen ||
            $publicationSelectionOpen ||
            $workflowStatusOpen ||
            $sourceComparisonOpen ||
            $scanWorkflowOpen
        ))
        <x-ui.loading.lazy-indicator
            role="status"
            :overlay="true"
            target="openWorkflowStatus, resumeWork, workSearch, workOnlyMine, workPerPage, sourceReplacementSearch, sourceReplacementPerPage, sortPublication, sortPublished, publishedKeyId, publishedLanguage, publishedSearch, publishedPerPage, openPublication, publicationKeyId, publicationLanguage, publicationSearch, publicationPerPage, openPublicationTask, preparePublicationTask, sortReview, reviewKeyId, reviewLanguage, reviewSearch, reviewPerPage, openReviewBatch, tab, translationKeyId, translationSearch, translationPerPage, findingId, search, status, toggleArchived, toggleUnassigned, perPage, sortFindings, sortTranslations, gotoPage, nextPage, previousPage, setPage, open, openShared"
            :text="__('Loading')"
        />
    @endif
    {{-- Tabs --}}
    <flux:tabs wire:model.live="tab">
        {{-- Tab Findings --}}
        <flux:tooltip>
            <flux:tab
                class="hover:cursor-pointer"
                name="findings"
            >
                <flux:icon.eye class="size-5" />
                {{ __('Findings') }}
            </flux:tab>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('Findings')"
                :text="__(
                    'Browse captured source occurrences, inspect their context and assign translation keys. Shared findings can use the same key and translations.',
                )"
            />
        </flux:tooltip>
        {{-- Tab In Progress --}}
        <flux:tooltip>
            <flux:tab
                class="hover:cursor-pointer"
                name="in-progress"
            >
                <flux:icon.metronome class="size-5" />
                {{ __('In progress') }}
            </flux:tab>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('In progress')"
                :text="__(
                    'Resume manually started work. Shared keys appear once; scanner activity does not change the order of your last edits.',
                )"
            />
        </flux:tooltip>
        {{-- Tab Translations --}}
        <flux:tooltip>
            <flux:tab
                class="hover:cursor-pointer"
                name="translations"
            >
                <flux:icon.language class="size-5" />
                {{ __('Translations') }}
            </flux:tab>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('Translations')"
                :text="__(
                    'View assigned translation keys and their saved language revisions. Edit translation values, save drafts and submit saved drafts for review.',
                )"
            />
        </flux:tooltip>
        {{-- Tab Review --}}
        <flux:tooltip>
            <flux:tab
                class="hover:cursor-pointer"
                name="review"
            >
                <flux:icon.eye-dashed class="size-5" />
                {{ __('Review') }}
            </flux:tab>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('Review')"
                :text="__(
                    'Check submitted translation revisions and approve them for publication or return them for changes. Approval does not write language files.',
                )"
            />
        </flux:tooltip>
        {{-- Tab Publication --}}
        <flux:tooltip>
            <flux:tab
                class="hover:cursor-pointer"
                name="publication"
            >
                <flux:icon.book-open class="size-5" />
                {{ __('Publication') }}
            </flux:tab>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('Publication')"
                :text="__(
                    'Prepare publication of approved language revisions, individually or together. Inspect the file preview before confirming writes to the language files.',
                )"
            />
        </flux:tooltip>
        {{-- Tab Published --}}
        <flux:tooltip>
            <flux:tab
                class="hover:cursor-pointer"
                name="published"
            >
                <flux:icon.check-check class="size-5" />
                {{ __('Published') }}
            </flux:tab>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('Published')"
                :text="__(
                    'Inspect published translations, their language revisions and publication results. Publication of language files is separate from replacement of calls in source code.',
                )"
            />
        </flux:tooltip>
        {{-- Tab Source replacement --}}
        <flux:tooltip>
            <flux:tab
                class="hover:cursor-pointer"
                name="source-replacement"
            >
                <flux:icon.replace-all class="size-5" />
                {{ __('Source replacement') }}
            </flux:tab>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('Source replacement')"
                :text="__(
                    'Check current source occurrences for published keys and compare planned replacements. Resolve source discrepancies before preparing and confirming source changes.',
                )"
            />
        </flux:tooltip>
        {{-- Tab Source usage decisions --}}
        <flux:tooltip>
            <flux:tab
                class="hover:cursor-pointer"
                name="source-usage"
            >
                <flux:icon.monitor-activity class="size-5" />
                {{ __('Source usage decisions') }}
            </flux:tab>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('Source usage decisions')"
                :text="__(
                    'Classify source expressions that require a usage decision, including database values and dynamic expressions. Inspect observed values and record how each source should be handled.',
                )"
            />
        </flux:tooltip>
    </flux:tabs>

    <flux:tab.panel name="findings">
        @include('translation-workbench::livewire.translation-workbench.tabs.findings.index')
    </flux:tab.panel>

    <flux:tab.panel name="in-progress">
        @if ($tab === 'in-progress')
            @include('translation-workbench::livewire.translation-workbench.tabs.in-progress.index')
        @endif
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

    <flux:tab.panel name="source-replacement">
        @if ($tab === 'source-replacement')
            @include('translation-workbench::livewire.translation-workbench.tabs.source-replacement.index')
        @endif
    </flux:tab.panel>

    <flux:tab.panel name="source-usage">
        @if ($tab === 'source-usage')
            @include('translation-workbench::livewire.translation-workbench.tabs.source-usage.index')
        @endif
    </flux:tab.panel>
</flux:tab.group>
