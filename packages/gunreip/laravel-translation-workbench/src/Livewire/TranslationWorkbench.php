<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess;
use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorLocales;
use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Gunreip\TranslationWorkbench\Generation\GenerationPublisher;
use Gunreip\TranslationWorkbench\Generation\InvalidPublicationKey;
use Gunreip\TranslationWorkbench\Generation\PublicationFileReserved;
use Gunreip\TranslationWorkbench\Generation\SourceReplacementWriter;
use Gunreip\TranslationWorkbench\Support\RuntimeOptionPreview;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Gunreip\TranslationWorkbench\Support\WorkbenchTestEnvironment;

class TranslationWorkbench extends Component
{
    use Concerns\HasTextSearchOptions;

    public function updatedSourceReplacementSearch(): void
    {
        $this->resetEmptyTextSearch('sourceReplacementSearch');
        $this->resetPage('sourceReplacementPage');
    }

    public string $workFindingId = '';

    public function updatedWorkFindingId(): void
    {
        $this->resetPage('workPage');
    }

    public string $workKeyId = '';

    public function updatedWorkKeyId(): void
    {
        $this->resetPage('workPage');
    }

    public string $sourceReplacementFindingId = '';

    public function updatedSourceReplacementFindingId(): void
    {
        $this->resetPage('sourceReplacementPage');
    }

    public string $sourceReplacementKeyId = '';

    public function updatedSourceReplacementKeyId(): void
    {
        $this->resetPage('sourceReplacementPage');
    }

    public string $sourceIssueSearch = '';
    public array $sourceIssueFilters = ['finding' => '', 'key' => '', 'observation' => '', 'perPage' => 25];

    public function updatedSourceIssueFilters(): void
    {
        $this->resetPage('sourceIssuesPage');
    }

    public function updatedSourceIssueSearch(): void
    {
        $this->resetEmptyTextSearch('sourceIssueSearch');
        $this->resetPage('sourceIssuesPage');
    }

    private function sourceIssuePage(GenerationEditor $repository, array $cases): \Illuminate\Pagination\LengthAwarePaginator
    {
        $finding = trim($this->sourceIssueFilters['finding']);
        $key = trim($this->sourceIssueFilters['key']);
        $search = trim($this->sourceIssueSearch);
        $options = $this->textSearchOptions['sourceIssueSearch'] ?? [];
        $literal = preg_quote($search, '~');
        $pattern = !empty($options['word']) ? '(?<![\pL\pN])'.$literal.'(?![\pL\pN])' : $literal;
        $pattern = '~'.$pattern.'~u'.(empty($options['case']) ? 'i' : '');
        $cases = array_values(array_filter($cases, static function ($case) use ($finding, $key, $search, $pattern) {
            $affected = $case['affected'] ?? [];
            if ($finding !== '' && $finding !== (string) $case['id'] && !isset($affected[$finding])) return false;
            if ($key !== '' && !in_array($key, array_map('strval', $affected), true)) return false;
            return $search === '' || preg_match($pattern, $case['path']) || preg_match($pattern, $case['raw_expression'] ?? '');
        }));
        $observation = $this->sourceIssueFilters['observation'];
        if ($observation !== '' && $cases) {
            $cases = array_values(array_filter($repository->describeSourceIssueRows($cases), static fn ($case) =>
                $observation === 'duplicate' ? !empty($case['retained']) : !empty($case['observations'][$observation])));
        }
        $size = (int) $this->sourceIssueFilters['perPage'];
        $size = in_array($size, [10, 25, 50, 100], true) ? $size : 25;
        $page = max(1, min((int) $this->getPage('sourceIssuesPage'), (int) ceil(count($cases) / $size)));
        $rows = array_slice($cases, ($page - 1) * $size, $size);
        if ($observation === '' && $rows) $rows = $repository->describeSourceIssueRows($rows);
        return new \Illuminate\Pagination\LengthAwarePaginator($rows, count($cases), $size, $page, ['pageName' => 'sourceIssuesPage']);
    }

    protected function textSearchPages(): array
    {
        return [
            'workSearch' => 'workPage',
            'translationSearch' => 'translationsPage',
            'reviewSearch' => 'reviewPage',
            'publicationSearch' => 'publicationPage',
            'publishedSearch' => 'publishedPage',
            'sourceReplacementSearch' => 'sourceReplacementPage',
            'sourceIssueSearch' => 'sourceIssuesPage',
        ];
    }

    use WithPagination;
    use Concerns\HasWorkflowReasons;
    use Concerns\SelectsWorkflowLanguages;
    use Concerns\BrowsesTranslationKeySegments;
    use Concerns\HasReasonLanguages;
    use Concerns\NotifiesFindingEditorErrors;

    protected function workflowReasonFields(): array
    {
        return [
            'reviewReason' => 'review', 'reviewBatchReason' => 'review',
            'publicationReason' => 'publication', 'closureReason' => 'closure',
            'sourceReason' => 'source', 'sourceCloseReason' => 'source_closure',
            'scanUsageReason' => 'scan_usage', 'sourcePositionReason' => 'source_position',
        ];
    }

    protected function savedWorkflowReasons(string $field, string $kind): array
    {
        return $field === 'scanUsageReason'
            ? array_values($this->scanUsageReasonOptions)
            : app(GenerationEditor::class)->workflowReasons($kind);
    }


    #[Locked]
    public string $testToken = '';

    public function mount(): void
    {
        $environment = app(WorkbenchTestEnvironment::class);
        $this->testToken = $environment->currentToken();
        $environment->activate($this->testToken);
    }

    public function hydrate(): void
    {
        app(WorkbenchTestEnvironment::class)->activate($this->testToken);
    }

    public function toggleTestcase(): void
    {
        $environment = app(WorkbenchTestEnvironment::class);
        try {
            if ($this->testToken === '') {
                $token = $environment->start();
            } else {
                $environment->stop($this->testToken);
                $token = '';
            }
        } catch (\Throwable $error) {
            report($error);
            $this->addError('testcase', __('The UI test environment could not be switched. Check the application log.'));
            return;
        }
        // Close all dialogs and discard IDs, drafts and pagination from the previous dataset.
        $this->reset();
        $this->resetValidation();
        $this->resetPage('findingsPage');
        $this->testToken = $token;
    }

    #[Url]
    public string $tab = 'findings';

    public bool $overviewOpen = false;

    public string $workSearch = '';
    public int $workPerPage = 25;
    public bool $workOnlyMine = true;

    public function updatedWorkSearch(): void
    {
        $this->resetEmptyTextSearch('workSearch');
        $this->resetPage('workPage');
    }

    public function updatedWorkOnlyMine(): void
    {
        $this->resetPage('workPage');
    }

    public function updatedWorkPerPage(): void
    {
        if (!in_array($this->workPerPage, [10, 25, 50, 100], true)) {
            $this->workPerPage = 25;
        }
        $this->resetPage('workPage');
    }

    public function resumeWork(int $key): void
    {
        $this->resetValidation('resumeWork');
        try {
            $task = app(GenerationEditor::class)->workItem($key, $this->reasonTargetLocale());
            if (!$task || !$task['finding_id']) {
                throw new \RuntimeException(__('No finding is available for this work item.'));
            }
            if (!empty($task['open_publication'])) {
                $this->inspectPublicationRun($task['open_publication'], $key, (int) $task['finding_id']);
                return;
            }
            if (!empty($task['open_source'])) {
                $this->openSourceRun($task['open_source']);
                return;
            }
            $this->openWorkflowStatus((int) $task['finding_id']);
            if (!in_array($task['step'], ['archived', 'complete'], true)) {
                $this->continueWorkflow();
                if ($this->tab === 'source-replacement') {
                    $this->sourceReplacementSearch = $task['full_name'];
                }
            }
        } catch (\RuntimeException $error) {
            $this->addError('resumeWork', $error->getMessage());
        }
    }

    #[Locked]
    public string $findingsSort = 'id';

    #[Locked]
    public string $findingsDirection = 'desc';

    #[Locked]
    public string $selectedScan = '';

    public bool $scanOpen = false;

    public bool $scanUsageOpen = false;

    #[Locked]
    public string $scanReviewPath = '';

    #[Locked]
    public bool $scanCaptureView = false;

    #[Locked]
    public array $scanUsageContext = [];

    public string $scanUsageChoice = '';

    public string $scanUsageReason = '';

    #[Locked]
    public array $scanUsageReasonOptions = [];


    public string $scanUsageNotice = '';

    #[Locked]
    public string $scanDecisionReturnRun = '';

    public string $scanCategory = 'created';

    public string $scanSearch = '';

    public int $scanPerPage = 25;

    public string $search = '';

    public bool $searchCaseSensitive = false;

    public bool $searchWholeWord = false;
    public bool $searchWholeLiteral = false;

    public string $translationKeyId = '';

    public function updatedTranslationKeyId(): void
    {
        $this->resetPage('translationsPage');
    }

    public string $translationSearch = '';

    public int $translationPerPage = 25;

    #[Locked]
    public string $translationSort = 'key';

    #[Locked]
    public string $translationDirection = 'desc';

    public function sortTranslations(string $column): void
    {
        if (! in_array($column, GenerationEditor::TRANSLATION_SORTS, true)) {
            return;
        }
        $this->translationDirection = $this->translationSort === $column && $this->translationDirection === 'asc' ? 'desc' : 'asc';
        $this->translationSort = $column;
        $this->resetPage('translationsPage');
    }

    public function updatedTranslationSearch(): void
    {
        $this->resetEmptyTextSearch('translationSearch');
        $this->resetPage('translationsPage');
    }

    public function updatedTranslationPerPage(): void
    {
        if (! in_array($this->translationPerPage, [10, 25, 50, 100], true)) {
            $this->translationPerPage = 25;
        }
        $this->resetPage('translationsPage');
    }

    public string $findingId = '';

    public string $status = 'open';

    public bool $includeArchived = false;

    public bool $unassignedOnly = false;

    public bool $editorOpen = false;

    public bool $confirmOpen = false;

    public string $locale = 'en';

    public array $selectedSubLanguages = [];

    public array $subLanguageValues = [];

    #[Locked]
    public array $subLanguageRevisions = [];

    #[Locked]
    public array $subLanguageSavedValues = [];

    public string $value = '';

    public string $sourceLanguageValue = '';


    public string $reason = '';

    public string $newChangeReason = '';

    public bool $changeReasonOpen = false;

    #[Locked]
    public array $changeReasonOptions = [];

    public string $assignmentReason = '';

    public string $newAssignmentReason = '';

    public bool $assignmentReasonOpen = false;

    #[Locked]
    public array $assignmentReasonOptions = [];

    public string $assignmentKey = '';

    public bool $translationKeyOpen = false;

    public string $newTranslationKey = '';

    #[Locked]
    public array $assignmentSelection = [];

    public bool $reviewOpen = false;

    public string $reviewReason = '';
    public string $reviewReasonLocale = 'en';

    public function updatedReviewReasonLocale(): void
    {
        $this->validate(['reviewReasonLocale' => ['required', Rule::in(['en', $this->reasonTargetLocale()])]]);
    }

    #[Locked]
    public array $reviewDecision = [];

    #[Locked]
    public int $selected = 0;

    #[Locked]
    public array $context = [];

    #[Locked]
    public string $loadedLocale = '';

    public bool $publicationOpen = false;

    public string $publicationReason = '';
    public string $publicationReasonLocale = 'en';

    public function updatedPublicationReasonLocale(): void
    {
        $this->validate(['publicationReasonLocale' => ['required', Rule::in(['en', $this->reasonTargetLocale()])]]);
    }

    public string $closureReason = '';

    #[Locked]
    public array $publication = [];

    #[Locked]
    public array $closure = [];

    public bool $sourcePreviewOpen = false;

    #[Locked]
    public array $sourcePreview = [];

    public bool $sourceRunOpen = false;

    public string $sourceReason = '';

    public string $sourceCloseReason = '';

    #[Locked]
    public array $sourceRun = [];

    public function boot(): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
    }

    public function sortFindings(string $column): void
    {
        if (! isset(GenerationEditor::FINDING_SORTS[$column])) {
            return;
        }
        $this->findingsDirection = $this->findingsSort === $column && $this->findingsDirection === 'asc' ? 'desc' : 'asc';
        $this->findingsSort = $column;
        $this->resetPage('findingsPage');
    }

    public int $perPage = 25;

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, [10, 25, 50, 100], true)) {
            $this->perPage = 25;
        }
        $this->resetPage('findingsPage');
    }

    public function updatedSearch(): void
    {
        if (trim($this->search) === '') {
            $this->searchCaseSensitive = false;
            $this->searchWholeWord = false;
            $this->searchWholeLiteral = false;
        }
        $this->resetPage('findingsPage');
    }

    public function toggleSearchCaseSensitive(): void
    {
        $this->searchCaseSensitive = ! $this->searchCaseSensitive;
        $this->resetPage('findingsPage');
    }

    public function toggleSearchWholeWord(): void
    {
        $this->searchWholeWord = ! $this->searchWholeWord;
        $this->resetPage('findingsPage');
    }

    public function toggleSearchWholeLiteral(): void
    {
        $this->searchWholeLiteral = ! $this->searchWholeLiteral;
        $this->resetPage('findingsPage');
    }

    public function updatedFindingId(): void
    {
        $this->resetPage('findingsPage');
    }

    public function updatedStatus(): void
    {
        if ($this->status === '') $this->status = 'open';
        $this->resetPage('findingsPage');
    }

    public function toggleUnassigned(): void
    {
        $this->unassignedOnly = ! $this->unassignedOnly;
        $this->resetPage('findingsPage');
    }

    public function toggleArchived(): void
    {
        if ($this->status === 'obsolete') {
            return;
        }
        $this->includeArchived = ! $this->includeArchived;
        $this->resetPage('findingsPage');
    }

    private function locales(): array
    {
        $locales = app(TranslationWorkbenchEditorLocales::class)->resolve();

        return array_values(array_unique([$locales['active'], ...$locales['sub']]));
    }

    /** Flux renders country flags; keep the existing configured language mapping. */
    private function translationFlagCountries(array $additionalLocales = []): array
    {
        $path = base_path('vendor/outhebox/blade-flags/config/language-countries.json');
        $defaults = is_file($path) ? json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR) : [];
        $result = [];
        foreach (array_unique(['en', ...$this->locales(), ...$additionalLocales]) as $locale) {
            $parts = \App\Support\Locale\LocaleCode::parts($locale);
            $language = $parts['language'];
            $country = $parts['country']
                ?? config('blade-flags.language_overrides.'.$language.'.default')
                ?? ($defaults[$language]['default'] ?? null);
            $result[$locale] = is_string($country) && preg_match('/^[a-z]{2}$/i', $country)
                ? strtoupper($country) : null;
        }

        return $result;
    }

    public bool $sharedOpen = false;

    #[Locked]
    public int $sharedFinding = 0;

    #[Locked]
    public array $sharedReference = [];

    public array $sharedSelection = [];

    public int $sharedPerPage = 25;

    public string $sharedSort = '';

    public string $sharedDirection = 'asc';

    public function sortSharedFindings(string $column): void
    {
        if (! in_array($column, ['finding', 'literal', 'source', 'suggested_key', 'translation_key'], true)) {
            return;
        }
        $this->sharedDirection = $this->sharedSort === $column && $this->sharedDirection === 'asc' ? 'desc' : 'asc';
        $this->sharedSort = $column;
        $this->resetPage('sharedFindingsPage');
    }

    public function updatedSharedPerPage(): void
    {
        if (! in_array($this->sharedPerPage, [10, 25, 50, 100], true)) {
            $this->sharedPerPage = 25;
        }
        $this->resetPage('sharedFindingsPage');
    }

    public string $sharedReason = '';

    #[Locked]
    public bool $assignmentReasonForShared = false;

    public bool $sharedConfirmOpen = false;

    #[Locked]
    public array $sharedAssignmentPlan = [];

    #[Locked]
    public string $sharedConfirmedReason = '';

    #[Locked]
    public ?string $sharedConfirmedReasonLocale = null;

    private function selectableSharedIds(iterable $rows): array
    {
        return collect($rows)->filter(fn ($match) =>
            ($this->sharedReference['status'] ?? null) === 'active'
            && !empty($this->sharedReference['key_id'])
            && $match['key_id'] != $this->sharedReference['key_id']
            && trim((string) ($this->sharedReference['literal'] ?? '')) !== ''
            && $match['literal'] === $this->sharedReference['literal'])
            ->map(fn ($match) => (string) $match['id'])->values()->all();
    }

    public function updatedSharedSelection(): void
    {
        $this->sharedConfirmOpen = false;
        $this->sharedAssignmentPlan = [];
    }

    public function reviewSharedAssignment(): void
    {
        $this->sharedConfirmOpen = false;
        $this->sharedAssignmentPlan = [];
        $this->resetValidation('sharedAssignment');
        $this->sharedReason = trim($this->sharedReason);
        $this->validate([
            'sharedSelection' => 'required|array|min:1|max:100',
            'sharedSelection.*' => 'required|integer|min:1|distinct',
            'sharedReason' => 'required|string|min:3|max:2000',
        ]);
        try {
            $this->sharedAssignmentPlan = app(GenerationEditor::class)->previewSharedAssignment($this->sharedFinding, $this->sharedSelection);
            $this->sharedConfirmedReason = $this->sharedReason;
            $this->sharedConfirmedReasonLocale = $this->selectedReasonLocale('assignment', $this->sharedReason);
            $this->sharedConfirmOpen = true;
        } catch (\RuntimeException $error) {
            $this->addError('sharedAssignment', $error->getMessage());
        }
    }

    public function updatedSharedReason(): void
    {
        $this->resetValidation('sharedReason');
        $this->sharedConfirmOpen = false;
        $this->sharedAssignmentPlan = [];
    }

    public function assignSharedFindings(): void
    {
        $ids = array_map('intval', $this->sharedSelection);
        sort($ids, SORT_NUMERIC);
        if (! $this->sharedConfirmOpen || $this->sharedAssignmentPlan === []
            || $ids !== array_map('intval', array_column($this->sharedAssignmentPlan['findings'], 'id'))
            || $this->sharedReason !== $this->sharedConfirmedReason
            || $this->selectedReasonLocale('assignment', $this->sharedReason) !== $this->sharedConfirmedReasonLocale) {
            $this->addError('sharedAssignment', __('The proposed assignment changed. Review it again before confirming.'));
            $this->sharedConfirmOpen = false;
            return;
        }
        try {
            $assignment = app(GenerationEditor::class)->assignShared($this->sharedAssignmentPlan, (int) auth()->id(), $this->sharedConfirmedReason, ...$this->reasonLocaleArguments('assignment', $this->sharedConfirmedReason));
            $this->sharedSelection = [];
            $this->sharedAssignmentPlan = [];
            $this->sharedConfirmOpen = false;
            $this->sharedOpen = false;
            $this->sharedReference = app(GenerationEditor::class)->sharedSummaries([$this->sharedFinding])[$this->sharedFinding] ?? [];
            if ($this->editorOpen && $this->selected === $this->sharedFinding) {
                $freshContext = app(GenerationEditor::class)->inspect($this->selected, $this->locale);
                // Refresh the displayed history without replacing unsaved drafts or revision snapshots.
                $this->context['history'] = $freshContext['history'];
            }
            $this->successToast(__('Shared key assignment confirmed'),
                __('Findings linked: :count · Key record #:key.', ['count' => count($ids), 'key' => $assignment['key']]));
            $this->resetValidation('sharedAssignment');
        } catch (\RuntimeException $error) {
            $this->addError('sharedAssignment', $error->getMessage());
        }
    }

    public function openShared(int $finding): void
    {
        $this->sharedReference = app(GenerationEditor::class)->sharedSummaries([$finding])[$finding] ?? [];
        abort_if($this->sharedReference === [], 404);
        $this->sharedSelection = [];
        $this->sharedReason = '';
        $this->sharedAssignmentPlan = [];
        $this->sharedConfirmOpen = false;
        $this->resetValidation(['sharedAssignment', 'sharedSelection', 'sharedReason']);
        $this->sharedFinding = $finding;
        $this->resetPage('sharedFindingsPage');
        $this->sharedOpen = true;
    }

    public function open(int $finding): void
    {
        $this->selected = $finding;
        $this->createdReasonLocales = [];
        $this->locale = $this->locales()[0];
        $this->selectedSubLanguages = [];
        $this->subLanguageValues = [];
        $this->subLanguageRevisions = [];
        $this->subLanguageSavedValues = [];
        $this->reason = $this->assignmentReason = '';
        $this->assignmentReasonOpen = false;
        $this->newAssignmentReason = '';
        $this->changeReasonOpen = false;
        $this->newChangeReason = '';
        $this->changeReasonOptions = array_values(array_unique([
            __('Add an initial translation.'),
            __('Correct spelling or grammar.'),
            __('Improve wording for this context.'),
            __('Adapt the translation for this regional language.'),
            ...app(GenerationEditor::class)->changeReasons(),
        ]));
        $this->assignmentReasonOptions = array_values(array_unique([
            __('Use an existing key with the same meaning.'),
            __('Correct an incorrect key assignment.'),
            __('Use a separate key for this context.'),
            ...app(GenerationEditor::class)->assignmentReasons(),
        ]));
        $this->assignmentSelection = [];
        $this->confirmOpen = false;
        $this->resetValidation();
        $this->reload();
        $this->editorOpen = true;
    }

    private function reload(): void
    {
        $this->translationKeyOpen = false;
        $this->newTranslationKey = '';
        $this->sourcePreviewOpen = false;
        $this->sourcePreview = [];
        $this->reviewOpen = false;
        $this->reviewDecision = [];
        $this->reviewChoices = [];
        $this->reviewLocales = [];
        $this->publicationSelectionOpen = false;
        $this->publicationChoices = [];
        $this->publicationLocales = [];
        $this->reviewReason = '';
        $this->publicationReason = '';
        $this->publicationReasonLocale = $this->reasonTargetLocale();
        $this->context = app(GenerationEditor::class)->inspect($this->selected, $this->locale);
        $this->loadedLocale = $this->locale;
        $finding = $this->context['finding'];
        $this->context['uiUsage'] = \Gunreip\TranslationWorkbench\Support\FindingUsage::forFinding(
            $finding, app(WorkbenchTestEnvironment::class)->root()
        );
        $this->assignmentKeySearch = '';
        $this->sourceLanguageValue = (string) ($this->context['sourceText'] ?? $finding['raw_literal'] ?? $finding['suggested_literal'] ?? '');
        $this->assignmentKey = $finding['key_id']
            ? (($finding['namespace'] ?? '') !== '' ? $finding['namespace'].'::' : '').$finding['full_name']
            : '';
        $this->assignmentSelection = [];
        $this->confirmOpen = false;
        // A draft and published evidence are deliberately distinct. Copying evidence is explicit.
        $this->value = $this->context['revision']['value'] ?? '';
        $this->subLanguageValues = [];
        $this->subLanguageRevisions = [];
        $this->subLanguageSavedValues = [];
        $allowed = app(TranslationWorkbenchEditorLocales::class)->resolve()['sub'];
        $saved = ! empty($finding['key_id'])
            ? app(GenerationEditor::class)->savedLanguageVariants((int) $finding['key_id'], $allowed)
            : [];
        // Restore persisted variants on opening/reloading, in the configured language order.
        $this->selectedSubLanguages = array_values(array_filter($allowed,
            fn ($code) => isset($saved[$code]) || in_array($code, $this->selectedSubLanguages, true)));
        foreach ($saved as $code => $revision) {
            $this->subLanguageValues[$code] = (string) $revision['value'];
            $this->subLanguageSavedValues[$code] = $revision['value'];
            $this->subLanguageRevisions[$code] = (int) $revision['id'];
        }
        $this->updatedSelectedSubLanguages();
    }

    public function updatedSelectedSubLanguages(): void
    {
        $this->resetValidation('selectedSubLanguages');
        $this->resetValidation('selectedSubLanguages.*');
        $this->validate([
            'selectedSubLanguages' => 'array',
            'selectedSubLanguages.*' => ['string', 'distinct', Rule::in(app(TranslationWorkbenchEditorLocales::class)->resolve()['sub'])],
        ]);
        if (! $this->editorOpen || empty($this->context['finding']['key_id'])) {
            return;
        }
        foreach ($this->selectedSubLanguages as $code) {
            if (! array_key_exists($code, $this->subLanguageValues)) {
                $variant = app(GenerationEditor::class)->inspect($this->selected, $code);
                $this->subLanguageValues[$code] = (string) ($variant['revision']['value'] ?? '');
                $this->subLanguageSavedValues[$code] = $variant['revision']['value'] ?? null;
                $this->subLanguageRevisions[$code] = isset($variant['revision']['id']) ? (int) $variant['revision']['id'] : null;
            }
        }
    }

    public function updatedLocale(): void
    {
        $this->validate(['locale' => ['required', Rule::in($this->locales())]]);
        if ($this->selected > 0) {
            $this->resetValidation();
            $this->reload();
        }
    }

    public function copySourceToTarget(): void
    {
        abort_unless($this->editorOpen && ($this->context['finding']['status'] ?? null) === 'active'
            && ! empty($this->context['finding']['key_id']), 403);
        $this->validate([
            'locale' => ['required', Rule::in($this->locales())],
            'sourceLanguageValue' => 'present|string|max:100000',
        ]);
        if ($this->locale !== $this->loadedLocale) {
            $this->addError('locale', __('Reload the locale before copying.'));
            return;
        }
        $this->value = $this->sourceLanguageValue;
        $this->dispatch('workbench-select-translation-draft');
        $this->resetValidation('value');
    }

    public function copyTargetLanguageToAllSelectedSubLanguages(): void
    {
        abort_unless($this->editorOpen && ($this->context['finding']['status'] ?? null) === 'active'
            && ! empty($this->context['finding']['key_id']), 403);
        $locales = app(TranslationWorkbenchEditorLocales::class)->resolve();
        $this->validate([
            'locale' => ['required', Rule::in([$locales['active']])],
            'value' => 'required|string|max:100000',
            'selectedSubLanguages' => 'required|array|min:1',
            'selectedSubLanguages.*' => ['string', 'distinct', Rule::in($locales['sub'])],
        ]);
        if ($this->locale !== $this->loadedLocale) {
            $this->addError('locale', __('Reload the locale before copying.'));
            return;
        }
        foreach ($this->selectedSubLanguages as $code) {
            $this->subLanguageValues[$code] = $this->value;
            $this->resetValidation('subLanguageValues.'.$code);
        }
    }

    public function copyPublished(): void
    {
        $current = app(GenerationEditor::class)->inspect($this->selected, $this->locale);
        if ($this->loadedLocale !== $this->locale || count($current['published']) !== 1 || ! $current['published'][0]['current']) {
            $this->addError('value', __('No unique, current published value is available.'));

            return;
        }
        $this->value = $current['published'][0]['value'];
    }

    public function openChangeReason(string $search = ''): void
    {
        abort_unless(($this->context['finding']['status'] ?? null) === 'active', 403);
        $this->newChangeReason = trim($search);
        $this->newChangeReasonLocale = $this->selectedReasonLocale('change', $search) ?? $this->reasonTargetLocale();
        $this->resetValidation('newChangeReason');
        $this->changeReasonOpen = true;
    }

    public function createChangeReason(): void
    {
        abort_unless(($this->context['finding']['status'] ?? null) === 'active', 403);
        $this->newChangeReason = trim($this->newChangeReason);
        $this->validate(['newChangeReason' => 'required|string|min:3|max:2000']);
        $this->rememberReasonLocale('change', $this->newChangeReason, 'newChangeReasonLocale');
        $this->changeReasonOptions = array_values(array_unique([...$this->changeReasonOptions, $this->newChangeReason]));
        $this->reason = $this->newChangeReason;
        $this->newChangeReason = '';
        $this->changeReasonOpen = false;
        $this->resetValidation('reason');
    }

    public function openAssignmentReason(string $search = '', bool $shared = false): void
    {
        abort_unless(($this->context['finding']['status'] ?? null) === 'active', 403);
        $this->assignmentReasonForShared = $shared;
        $this->newAssignmentReason = trim($search);
        $this->newAssignmentReasonLocale = $this->selectedReasonLocale('assignment', $search) ?? $this->reasonTargetLocale();
        $this->resetValidation('newAssignmentReason');
        $this->assignmentReasonOpen = true;
    }

    public function createAssignmentReason(): void
    {
        $this->newAssignmentReason = trim($this->newAssignmentReason);
        $this->validate(['newAssignmentReason' => 'required|string|min:3|max:2000']);
        $this->rememberReasonLocale('assignment', $this->newAssignmentReason, 'newAssignmentReasonLocale');
        $this->assignmentReasonOptions = array_values(array_unique([
            ...$this->assignmentReasonOptions, $this->newAssignmentReason,
        ]));
        if ($this->assignmentReasonForShared) {
            $this->sharedReason = $this->newAssignmentReason;
            $this->updatedSharedReason();
        } else {
            $this->assignmentReason = $this->newAssignmentReason;
        }
        $this->newAssignmentReason = '';
        $this->assignmentReasonOpen = false;
        $this->resetValidation('assignmentReason');
    }

    public function openTranslationKey(string $search = ''): void
    {
        abort_unless(($this->context['finding']['status'] ?? null) === 'active', 403);
        $this->newTranslationKey = trim($search) !== '' ? trim($search) : $this->assignmentKey;
        $this->resetValidation('newTranslationKey');
        $this->translationKeyOpen = true;
    }

    public function appendSuggestedKeySegment(): void
    {
        $finding = $this->context['finding'] ?? [];
        if (!$this->translationKeyOpen || ($finding['status'] ?? null) !== 'active'
            || !\Gunreip\TranslationWorkbench\Generation\TranslationKeyInput::canAppendSegment($this->newTranslationKey)) return;

        $segment = Str::afterLast(trim($finding['suggested_key'] ?? ''), '.');
        if ($segment === '') return;

        if (preg_match('/_(?:[0-9a-f]{10}|[0-9a-f]{12}|[2-9]|[1-9][0-9]+)$/D', $segment)) {
            $factory = new \Gunreip\TranslationWorkbench\Scanner\SuggestedKeyFactory;
            $path = $finding['path'];
            $base = match (true) {
                isset($finding['raw_literal']) => $factory->forLiteral($finding['raw_literal'], $path),
                isset($finding['raw_source_code_literal']) => $factory->forExistingKeyAtSource($finding['raw_source_code_literal'], $path),
                default => $factory->forDynamicExpressionAtSource($finding['raw_expression'] ?? '', $path),
            };
            $baseLeaf = Str::afterLast($base->key, '.');
            if ($segment !== $baseLeaf) {
                $segment = preg_replace('/_(?:[2-9]|[1-9][0-9]+)$/D', '', $segment);
            }
            if ($segment === $baseLeaf) {
                $segment = $base->copyableLeaf;
            }
        }
        $prefix = rtrim(trim($this->newTranslationKey), '.');
        $this->newTranslationKey = $prefix.'.'.$segment;
        $this->resetValidation('newTranslationKey');
    }

    public function createTranslationKey(): void
    {
        abort_unless(($this->context['finding']['status'] ?? null) === 'active', 403);
        $this->newTranslationKey = trim($this->newTranslationKey);
        $this->validate(['newTranslationKey' => ['required', 'string', 'max:255', 'regex:'.\Gunreip\TranslationWorkbench\Generation\TranslationKeyInput::PATTERN]]);
        $this->validateAssignedKey($this->newTranslationKey, 'newTranslationKey');
        $this->assignmentKey = $this->newTranslationKey;
        $this->assignmentSelection = [];
        $this->confirmOpen = false;
        $this->translationKeyOpen = false;
        $this->newTranslationKey = '';
        $this->resetValidation('assignmentKey');
    }

    private function resolveAssignment(): array
    {
        $this->assignmentKey = trim($this->assignmentKey);
        $this->validate([
            'assignmentKey' => 'required|string|max:255',
            'assignmentReason' => 'required|string|min:3|max:2000',
        ]);
        $this->validateAssignedKey($this->assignmentKey, 'assignmentKey');
        $matches = array_values(array_filter(app(GenerationEditor::class)->keys(), function ($key) {
            $name = ($key['namespace'] !== '' ? $key['namespace'].'::' : '').$key['full_name'];
            return $name === $this->assignmentKey;
        }));
        if (count($matches) > 1) {
            throw \Illuminate\Validation\ValidationException::withMessages(['assignmentKey' => __('More than one current key matches. Review the key records first.')]);
        }
        if ($matches === []) {
            $this->validate(['assignmentKey' => ['regex:'.\Gunreip\TranslationWorkbench\Generation\TranslationKeyInput::PATTERN]]);
        }
        return ['key' => $matches === [] ? null : (int) $matches[0]['key_id'],
            'name' => $this->assignmentKey, 'reason' => $this->assignmentReason,
            'reason_locale' => $this->selectedReasonLocale('assignment', $this->assignmentReason)];
    }

    public function confirmAssignment(): void
    {
        $this->assignmentSelection = $this->resolveAssignment();
        $this->confirmOpen = true;
    }

    private function successToast(string $heading, string $text): void
    {
        $this->dispatch('buergerfrs:clear-notices', scope: 'finding-editor:'.$this->getId());
        \Flux\Flux::toast(heading: $heading, text: $text, variant: 'success', duration: 6000);
    }

    public function assign(): void
    {
        $selection = $this->resolveAssignment();
        if (! $this->confirmOpen || $selection !== $this->assignmentSelection) {
            $this->addError('assignment', __('The proposed assignment changed. Review it again before confirming.'));
            $this->confirmOpen = false;
            return;
        }
        $finding = $this->context['finding'] ?? [];
        try {
            $assignment = app(GenerationEditor::class)->assign($this->selected, (int) $selection['key'],
                isset($finding['link_id']) ? (int) $finding['link_id'] : null, (int) auth()->id(), $selection['reason'],
                $selection['key'] === null ? $selection['name'] : null, ...$this->reasonLocaleArguments('assignment', $selection['reason']));
            $this->reload();
            $this->confirmOpen = false;
            $this->successToast(__('Translation key assignment confirmed'),
                __('Finding #:finding → Key record #:key. Translation key: :name', [
                    'finding' => $this->selected, 'key' => $assignment['key'], 'name' => $selection['name'],
                ]));
        } catch (\RuntimeException $error) {
            $this->addError('assignment', $error->getMessage());
        }
    }

    public function saveDraft(): void
    {
        $locales = app(TranslationWorkbenchEditorLocales::class)->resolve();
        $rules = [
            'locale' => ['required', Rule::in([$locales['active']])],
            'value' => 'present|string|max:100000',
            'sourceLanguageValue' => 'present|string|max:100000',
            'reason' => 'required|string|min:3|max:2000',
            'selectedSubLanguages' => 'array',
            'selectedSubLanguages.*' => ['string', 'distinct', Rule::in($locales['sub'])],
        ];
        $this->validate($rules, [
            'reason.required' => __('Select a reason for translation change.'),
            'reason.min' => __('The reason for translation change must contain at least 3 characters.'),
            'reason.max' => __('The reason for translation change must not exceed 2000 characters.'),
        ]);
        foreach ($this->selectedSubLanguages as $code) {
            $this->validate(['subLanguageValues.'.$code => 'present|string|max:100000']);
            if (! array_key_exists($code, $this->subLanguageRevisions)) {
                $this->addError('subLanguageValues.'.$code, __('Reload the language variant before saving.'));
                return;
            }
        }
        if ($this->locale !== $this->loadedLocale) {
            $this->addError('locale', __('Reload the locale before saving.'));
            return;
        }
        if ($this->locale === 'en' && $this->sourceLanguageValue !== $this->value) {
            $this->addError('value', __('Source and target both use English. Their text must match before saving.'));
            return;
        }
        $finding = $this->context['finding'] ?? [];
        $drafts = [
            'en' => ['value' => $this->sourceLanguageValue, 'revision' => isset($this->context['sourceRevision']) ? (int) $this->context['sourceRevision'] : null],
            $this->locale => ['value' => $this->value, 'revision' => isset($this->context['revision']['id']) ? (int) $this->context['revision']['id'] : null],
        ];
        foreach ($this->selectedSubLanguages as $code) {
            $drafts[$code] = ['value' => $this->subLanguageValues[$code], 'revision' => $this->subLanguageRevisions[$code]];
        }
        try {
            app(GenerationEditor::class)->saveDrafts($this->selected, (int) ($finding['key_id'] ?? 0),
                isset($finding['link_id']) ? (int) $finding['link_id'] : null, $drafts, (int) auth()->id(), $this->reason, ...$this->reasonLocaleArguments('change', $this->reason));
            $this->reload();
            $this->successToast(__('Translation drafts saved'),
                __('Key record #:key · Languages: :languages. Publication is a separate step.', [
                    'key' => $finding['key_id'], 'languages' => implode(', ', array_keys($drafts)),
                ]));
        } catch (\RuntimeException $error) {
            $this->addError('value', $error->getMessage());
        }
    }

    #[Locked]
    public string $reviewSort = 'submitted';

    #[Locked]
    public string $reviewDirection = 'asc';

    public function sortReview(string $column): void
    {
        if (! isset(\Gunreip\TranslationWorkbench\Generation\ReviewWorklist::SORTS[$column])) {
            return;
        }
        $this->reviewDirection = $this->reviewSort === $column && $this->reviewDirection === 'asc' ? 'desc' : 'asc';
        $this->reviewSort = $column;
        $this->resetPage('reviewPage');
    }

    public string $reviewKeyId = '';

    public function updatedReviewKeyId(): void
    {
        $this->resetPage('reviewPage');
    }

    public string $reviewLanguage = '';

    public function updatedReviewLanguage(): void
    {
        $this->resetPage('reviewPage');
    }

    public string $reviewSearch = '';
    public int $reviewPerPage = 25;

    public function updatedReviewSearch(): void
    {
        $this->resetEmptyTextSearch('reviewSearch');
        $this->resetPage('reviewPage');
    }

    public function updatedReviewPerPage(): void
    {
        if (! in_array($this->reviewPerPage, [10, 25, 50, 100], true)) {
            $this->reviewPerPage = 25;
        }
        $this->resetPage('reviewPage');
    }

    public function submitForReview(): void
    {
        $this->resetValidation('submission');
        if (! $this->editorOpen || ($this->context['finding']['status'] ?? '') !== 'active') {
            return;
        }
        $this->validate([
            'sourceLanguageValue' => 'required|string',
            'value' => 'required|string',
            'selectedSubLanguages' => 'array',
            'selectedSubLanguages.*' => ['string', 'distinct', Rule::in(app(TranslationWorkbenchEditorLocales::class)->resolve()['sub'])],
        ], [
            'sourceLanguageValue.required' => __('Enter the English source text before submitting for review.'),
            'value.required' => __('Enter the main target-language translation before submitting for review.'),
        ]);
        foreach ($this->selectedSubLanguages as $code) {
            $this->validate(['subLanguageValues.'.$code => 'required|string'], [
                'subLanguageValues.'.$code.'.required' => __('Enter the translation for :locale or deselect this language variant before submitting for review.', ['locale' => $code]),
            ]);
        }
        $drafts = [
            'en' => ['value' => $this->sourceLanguageValue, 'revision' => $this->context['sourceRevision'] ?? null],
            $this->loadedLocale => ['value' => $this->value, 'revision' => $this->context['revision']['id'] ?? null],
        ];
        foreach ($this->selectedSubLanguages as $code) {
            $drafts[$code] = ['value' => $this->subLanguageValues[$code] ?? null, 'revision' => $this->subLanguageRevisions[$code] ?? null];
        }
        try {
            $editor = app(GenerationEditor::class);
            $expected = [];
            foreach ($drafts as $code => $draft) {
                $current = $editor->inspect($this->selected, $code);
                if (($current['finding']['key_id'] ?? null) !== ($this->context['finding']['key_id'] ?? null)
                    || ($current['finding']['link_id'] ?? null) !== ($this->context['finding']['link_id'] ?? null)
                    || ($current['finding']['full_name'] ?? null) !== ($this->context['finding']['full_name'] ?? null)
                    || ($current['review']['context']['usages'] ?? []) !== ($this->context['review']['context']['usages'] ?? [])) {
                    throw new \RuntimeException(__('The key or source assignments changed. Reload before submitting for review.'));
                }
                if ($draft['revision'] === null || (int) ($current['revision']['id'] ?? 0) !== (int) $draft['revision']
                    || $current['revision']['value'] !== $draft['value']) {
                    throw new \RuntimeException(__('Save or reload all selected language drafts before submitting them for review.'));
                }
                $expected[(int) $draft['revision']] = $current['review']['fingerprint'];
            }
            $editor->reviewWorklist()->submit($expected, (int) auth()->id());
            $this->successToast(__('Translation drafts submitted for review'),
                __('Key record #:key · Languages: :languages.', [
                    'key' => $this->context['finding']['key_id'], 'languages' => implode(', ', array_keys($drafts)),
                ]));
            $this->editorOpen = false;
            $this->tab = 'review';
            $this->resetPage('reviewPage');
        } catch (\RuntimeException $error) {
            $this->addError('submission', $error->getMessage());
        }
    }

    public bool $workflowStatusOpen = false;

    #[Locked]
    public array $workflowStatus = [];

    public function openWorkflowStatus(int $finding): void
    {
        $this->sourceComparisonOpen = false;
        $this->workflowStatusOpen = true;
        $this->workflowStatus = [];
        $this->resetValidation('workflowStatus');
        try {
            $this->workflowStatus = app(GenerationEditor::class)->workflowOverview($finding, $this->reasonTargetLocale());
        } catch (\RuntimeException $error) {
            $this->addError('workflowStatus', $error->getMessage());
        }
    }

    public function continueWorkflow(): void
    {
        $finding = (int) ($this->workflowStatus['finding']['id'] ?? 0);
        if (! $this->workflowStatusOpen || ! $finding) {
            return;
        }
        $this->openWorkflowStatus($finding);
        $step = collect($this->workflowStatus['steps'] ?? [])->firstWhere('status', 'current');
        if (! $step) {
            return;
        }

        // When continuing from the preview's status dialog, do not leave the preview over the destination.
        $this->sourcePreviewOpen = false;
        $this->sourceComparisonOpen = false;

        $key = (string) ($this->workflowStatus['finding']['key_id'] ?? '');
        $reviewReady = $step['id'] === 'review'
            && collect($step['languages'])->contains('state', 'review');
        if ($reviewReady || $step['id'] === 'publication') {
            $tab = $reviewReady ? 'review' : 'publication';
            $this->{$tab.'KeyId'} = $key;
            $this->{$tab.'Language'} = '';
            $this->{$tab.'Search'} = '';
            $this->resetPage($tab.'Page');
            $this->tab = $tab;
            $this->editorOpen = false;
            $this->workflowStatusOpen = false;
            $this->dispatch('workflow-focus', target: 'workbench-task-tabs');
            return;
        }
        if ($step['id'] === 'scan') {
            $this->workflowStatusOpen = false;
            $this->editorOpen = false;
            $this->dispatch('workflow-focus', target: 'workbench-scan-results');
            return;
        }

        if ($step['id'] === 'source') {
            $this->sourceReplacementSearch = (string) $finding;
            $this->tab = 'source-replacement';
            $this->workflowStatusOpen = false;
            $this->editorOpen = false;
            return;
        }

        // Keep any unsaved input when returning to the already open finding.
        if (! $this->editorOpen || $this->selected !== $finding) {
            $this->open($finding);
        }
        $this->workflowStatusOpen = false;
        $this->dispatch('workflow-focus', target: $step['id'] === 'assignment'
            ? 'workbench-key-assignment' : 'workbench-translation-values');
    }

    public function openKeyWorkflowStatus(int $key): void
    {
        try {
            $this->openWorkflowStatus(app(GenerationEditor::class)->workflowFindingForKey($key));
        } catch (\RuntimeException $error) {
            $this->workflowStatus = [];
            $this->workflowStatusOpen = true;
            $this->addError('workflowStatus', $error->getMessage());
        }
    }

    public array $selectedReviewRevisions = [];
    public bool $reviewBatchOpen = false;
    public string $reviewBatchReason = '';
    public string $reviewBatchReasonLocale = 'en';

    #[Locked]
    public array $reviewBatch = [];

    public function updatedReviewBatchReasonLocale(): void
    {
        $this->validate(['reviewBatchReasonLocale' => ['required', Rule::in(['en', $this->reasonTargetLocale()])]]);
    }

    public function openReviewBatch(?int $revision = null): void
    {
        $this->resetValidation(['reviewBatch', 'reviewBatchReason']);
        $this->reviewBatch = [];
        $this->reviewBatchReason = '';
        $this->reviewBatchReasonLocale = $this->reasonTargetLocale();
        $this->reviewBatchOpen = false;
        try {
            if ($revision === null) {
                $this->validate(['selectedReviewRevisions' => 'required|array|min:1', 'selectedReviewRevisions.*' => 'required|integer|min:1'], [
                    'selectedReviewRevisions.required' => __('Select at least one revision to review.'),
                ]);
            }
            $revisions = $revision === null ? array_unique($this->selectedReviewRevisions) : [$revision];
            $tasks = [];
            foreach ($revisions as $revision) {
                $tasks[] = app(GenerationEditor::class)->reviewWorklist()->inspect((int) $revision);
            }
            $this->reviewBatch = $tasks;
            $this->reviewBatchOpen = true;
        } catch (\Illuminate\Validation\ValidationException $error) {
            \Flux\Flux::toast(heading: __('Check the review selection'), text: $error->validator->errors()->first(), variant: 'danger', duration: 8000);
            throw $error;
        } catch (\RuntimeException $error) {
            $this->addError('reviewBatch', $error->getMessage());
            \Flux\Flux::toast(heading: __('Review could not be opened'), text: $error->getMessage(), variant: 'danger', duration: 8000);
        }
    }

    public function decideReviewBatch(bool $approve): void
    {
        $this->validate(['reviewBatchReason' => 'required|string|min:3|max:2000']);
        if (! $this->reviewBatchOpen || $this->reviewBatch === []) {
            return;
        }
        try {
            $revisions = [];
            foreach ($this->reviewBatch as $task) {
                $revisions[(int) $task['context']['revision_id']] = $task['fingerprint'];
            }
            app(GenerationEditor::class)->reviewWorklist()->decideMany($revisions, $approve, (int) auth()->id(), $this->reviewBatchReason, ...$this->reasonLocaleArguments('review', $this->reviewBatchReason));
            $this->selectedReviewRevisions = array_values(array_diff($this->selectedReviewRevisions, array_keys($revisions)));
            $this->reviewBatchOpen = false;
            $this->reviewBatch = [];
            $this->successToast($approve ? __('Revisions approved') : __('Revisions returned for correction'),
                __('Revision IDs: :ids', ['ids' => '#' . implode(', #', array_keys($revisions))]));
        } catch (\RuntimeException $error) {
            $this->addError('reviewBatch', $error->getMessage());
        }
    }

    public function confirmReview(bool $approve): void
    {
        $this->resetValidation('review');
        $review = $this->context['review'] ?? null;
        if (! $this->editorOpen || ! $review || ($this->context['finding']['status'] ?? '') !== 'active'
            || $this->locale !== $this->loadedLocale) {
            $this->addError('review', __('Open a current saved draft before reviewing.'));

            return;
        }
        if ($this->value !== $this->context['revision']['value']) {
            $this->addError('review', __('Save or discard your draft changes before reviewing the saved revision.'));

            return;
        }
        try {
            $this->reviewChoices = $this->savedLanguageChoices();
            if (! $approve) {
                $this->reviewChoices = array_intersect_key($this->reviewChoices, [$this->loadedLocale => true]);
            }
            $this->reviewLocales = array_keys(array_filter($this->reviewChoices, fn ($choice) => $choice['unchanged'] && (! $approve || ! $choice['approved'])));
        } catch (\RuntimeException $error) {
            $this->addError('review', $error->getMessage());
            return;
        }
        $this->reviewDecision = ['revision' => (int) $this->context['revision']['id'], 'fingerprint' => $review['fingerprint'], 'approve' => $approve];
        $this->reviewReason = '';
        $this->reviewOpen = true;
        $this->reviewReasonLocale = $this->reasonTargetLocale();
    }

    public function review(): void
    {
        $this->validate(['reviewReason' => 'required|string|min:3|max:2000']);
        if (! $this->editorOpen || ! $this->reviewOpen || ! $this->reviewDecision
            || $this->locale !== $this->loadedLocale || $this->value !== ($this->context['revision']['value'] ?? null)) {
            $this->addError('review', __('Reopen the review confirmation for the saved revision.'));

            return;
        }
        try {
            $this->ensureLanguageDraftsSaved();
            $revisions = $this->selectedLanguageRevisions($this->reviewLocales, $this->reviewChoices, false);
            if (count($revisions) === 1) {
                app(GenerationEditor::class)->review((int) array_key_first($revisions), reset($revisions),
                    $this->reviewDecision['approve'], (int) auth()->id(), $this->reviewReason, ...$this->reasonLocaleArguments('review', $this->reviewReason));
            } else {
                app(GenerationEditor::class)->reviewMany($revisions, $this->reviewDecision['approve'], (int) auth()->id(), $this->reviewReason, ...$this->reasonLocaleArguments('review', $this->reviewReason));
            }
            $approved = $this->reviewDecision['approve'];
            $this->reload();
            $this->successToast($approved ? __('Revisions approved') : __('Revision approval revoked'),
                __('Key record #:key · Revision IDs: :ids', [
                    'key' => $this->context['finding']['key_id'], 'ids' => '#' . implode(', #', array_keys($revisions)),
                ]));
        } catch (\RuntimeException $error) {
            $this->addError('review', $error->getMessage());
        }
    }

    #[Locked]
    public string $publishedSort = 'published';

    #[Locked]
    public string $publishedDirection = 'desc';

    public function sortPublished(string $column): void
    {
        if (! isset(\Gunreip\TranslationWorkbench\Generation\PublishedWorklist::SORTS[$column])) {
            return;
        }
        $this->publishedDirection = $this->publishedSort === $column && $this->publishedDirection === 'asc' ? 'desc' : 'asc';
        $this->publishedSort = $column;
        $this->resetPage('publishedPage');
    }

    public string $publishedKeyId = '';

    public function updatedPublishedKeyId(): void
    {
        $this->resetPage('publishedPage');
    }

    public string $publishedLanguage = '';

    public function updatedPublishedLanguage(): void
    {
        $this->resetPage('publishedPage');
    }

    public string $publishedSearch = '';
    public int $publishedPerPage = 25;

    public function updatedPublishedSearch(): void
    {
        $this->resetEmptyTextSearch('publishedSearch');
        $this->resetPage('publishedPage');
    }

    public function updatedPublishedPerPage(): void
    {
        if (! in_array($this->publishedPerPage, [10, 25, 50, 100], true)) {
            $this->publishedPerPage = 25;
        }
        $this->resetPage('publishedPage');
    }

    #[Locked]
    public string $publicationSort = 'revision';

    #[Locked]
    public string $publicationDirection = 'asc';

    public function sortPublication(string $column): void
    {
        if (! isset(\Gunreip\TranslationWorkbench\Generation\ReviewWorklist::PUBLICATION_SORTS[$column])) {
            return;
        }
        $this->publicationDirection = $this->publicationSort === $column && $this->publicationDirection === 'asc' ? 'desc' : 'asc';
        $this->publicationSort = $column;
        $this->resetPage('publicationPage');
    }

    public string $publicationKeyId = '';

    public function updatedPublicationKeyId(): void
    {
        $this->resetPage('publicationPage');
    }

    public string $publicationLanguage = '';

    public function updatedPublicationLanguage(): void
    {
        $this->resetPage('publicationPage');
    }

    public string $publicationSearch = '';
    public int $publicationPerPage = 25;
    #[Locked]
    public array $sourceReplacementGroup = [];

    #[Locked]
    public array $sourceGroupCheck = [];

    public function checkSelectedSourceFindings(): void
    {
        $this->resetValidation('sourceReplacementGroup');
        $this->sourceGroupCheck = [];
        if ($this->sourceReplacementGroup === []) {
            $this->addError('sourceReplacementGroup', __('Select a translation key first.'));
            return;
        }
        try {
            $this->sourceGroupCheck = app(GenerationEditor::class)->checkSourceReplacementGroup(
                $this->sourceReplacementGroup['key_id'], $this->sourceReplacementGroup['finding_ids']);
            $this->sourceReplacementCheckVersion++;
        } catch (\RuntimeException $error) {
            $this->addError('sourceReplacementGroup', $error->getMessage());
        }
    }

    #[Locked]
    public string $sourcePositionFailure = '';

    public string $sourcePositionTarget = '';
    public string $sourcePositionChoice = '';
    public string $sourcePositionReason = '';
    public bool $sourcePositionConfirmed = false;
    #[Locked]
    public string $sourcePositionOperation = '';

    public function selectSourcePosition(int $line, int $column): void
    {
        $coordinate = $line.':'.$column;
        $decision = $this->sourceComparison['decision'] ?? [];
        $positions = $this->sourceComparison['check']['positions'] ?? [];
        if (!$this->sourceComparisonOpen || empty($decision['can_move']) || isset($decision['occupied'][$coordinate])
            || !collect($positions)->contains(fn ($position) => $position['line'].':'.$position['column'] === $coordinate)) {
            $this->reportSourcePositionFailure(__('This position is unavailable. Reopen the source comparison.'));
            return;
        }
        $this->sourcePositionChoice = 'moved';
        $this->sourcePositionTarget = $coordinate;
        $this->sourcePositionConfirmed = false;
        $this->sourcePositionFailure = '';
        $this->resetValidation(['sourcePositionChoice', 'sourcePositionTarget']);
        $this->successToast(__('Position selected'), __('Finding #:finding · :position. Review the selection and choose Apply decision to save it.', [
            'finding' => $this->sourceComparison['finding']['id'], 'position' => $coordinate,
        ]));
    }

    public function applySourcePositionDecision(): void
    {
        $finding = (int) ($this->sourceComparison['finding']['id'] ?? 0);
        if (! $this->sourceComparisonOpen || (!($this->sourceComparison['from_issue'] ?? false) && ! in_array($finding, $this->sourceReplacementGroup['finding_ids'] ?? [], true))
            || $this->sourcePositionOperation === '' || empty($this->sourceComparison['decision']['fingerprint'])) {
            $this->reportSourcePositionFailure(__('Reopen the source comparison before applying a decision.'));
            return;
        }
        try {
            $this->validate(['sourcePositionChoice' => 'required|in:moved,removed,duplicate',
                'sourcePositionReason' => 'required|string|min:3|max:2000', 'sourcePositionConfirmed' => 'accepted']);
            $result = app(GenerationEditor::class)->applySourcePositionDecision($this->sourcePositionOperation,
                $finding, ($this->sourceComparison['from_issue'] ?? false) ? $this->sourceComparison['finding']['key_id'] : $this->sourceReplacementGroup['key_id'], $this->sourceComparison['decision']['fingerprint'],
                $this->sourcePositionChoice, (int) auth()->id(), $this->sourcePositionReason, $this->sourcePositionTarget);
            $this->releaseSourceReplacementGroup();
            $message = isset($result['successors'][0])
                ? __('Finding #:old archived; successor #:new created with the same suggested key and translation key assignment.',
                    ['old' => $finding, 'new' => $result['successors'][0]['successor']])
                : (isset($result['retained'])
                    ? __('translation-workbench.literal.finding_old_archived_as_a_duplicate_finding_active_remains_active', ['old' => $finding, 'active' => $result['retained']])
                    : __('Finding #:id archived as removed. The translation key and translations were retained.', ['id' => $finding]));
            $this->successToast(__('Source position decision saved'), $message);
        } catch (\Illuminate\Validation\ValidationException $error) {
            $this->reportSourcePositionFailure($error->validator->errors()->first());
            throw $error;
        } catch (\RuntimeException $error) {
            $this->reportSourcePositionFailure($error->getMessage());
        }
    }

    private function reportSourcePositionFailure(string $message, ?string $heading = null): void
    {
        $this->sourcePositionFailure = $message;
        $this->dispatch('toast-show', slots: ['heading' => $heading ?? __('Decision not saved'), 'text' => $message],
            dataset: ['variant' => 'danger']);
    }

    public function inspectSourcePositionOccupant(int $finding): void
    {
        if (!$this->sourceComparisonOpen) return;
        foreach ($this->sourceComparison['decision']['occupied'] ?? [] as $occupants) {
            if (in_array($finding, array_column($occupants, 'id'), true)) {
                $this->open($finding);
                $this->sourceComparisonOpen = false;
                return;
            }
        }
    }

    public bool $sourceComparisonOpen = false;

    #[Locked]
    public array $sourceComparison = [];

    public function openSourceComparison(int $finding): void
    {
        if (!in_array($finding, $this->sourceReplacementGroup['finding_ids'] ?? [], true)) {
            $this->addError('sourceReplacementGroup', __('Select this translation key before inspecting its findings.'));
            return;
        }
        $this->showSourceComparison($finding, $this->sourceReplacementGroup['key_id']);
    }

    public function openSourceIssue(int $finding): void
    {
        $cases = app(GenerationEditor::class)->sourceIssues($this->testToken);
        $previewCauses = array_column(array_column($this->sourcePreview['items'] ?? [], 'blocking_finding'), 'finding');
        if (!in_array($finding, array_column($cases, 'id')) && !in_array($finding, $previewCauses)) {
            $this->reportSourcePositionFailure(__('This finding no longer has an open source issue.'), __('Source comparison'));
            return;
        }
        $this->showSourceComparison($finding, null);
    }

    private function showSourceComparison(int $finding, ?int $key): void
    {
        $this->resetValidation(['sourcePositionChoice', 'sourcePositionReason', 'sourcePositionConfirmed', 'sourcePositionDecision']);
        $this->sourcePositionFailure = '';
        $this->sourcePositionTarget = '';
        $this->sourcePositionChoice = '';
        $this->sourcePositionReason = '';
        $this->sourcePositionConfirmed = false;
        $this->sourcePositionOperation = (string) \Illuminate\Support\Str::uuid();
        $this->resetValidation('sourceReplacementGroup');
        $this->sourceComparisonOpen = false;
        $this->sourceComparison = [];
        try {
            $this->sourceComparison = app(GenerationEditor::class)->sourceReplacementComparison($finding, $key);
            $this->sourceComparison['from_issue'] = $key === null;
            $this->sourceComparisonOpen = true;
            $this->sourcePreviewOpen = false;
        } catch (\RuntimeException $error) {
            $this->reportSourcePositionFailure($error->getMessage(), __('Source comparison'));
        }
    }

    public function selectSourceReplacementGroup(int $finding): void
    {
        $this->resetValidation('sourceReplacementGroup');
        // A group stays fixed until explicitly released, including across pages and filters.
        if ($this->sourceReplacementGroup !== []) {
            return;
        }
        try {
            $this->sourceReplacementGroup = app(GenerationEditor::class)->sourceReplacementGroup($finding);
        } catch (\RuntimeException $error) {
            $this->addError('sourceReplacementGroup', $error->getMessage());
        }
    }

    public function releaseSourceReplacementGroup(): void
    {
        $this->sourcePreviewOpen = false;
        $this->sourcePreview = [];
        $this->sourceComparisonOpen = false;
        $this->sourceComparison = [];
        $this->sourceReplacementGroup = [];
        $this->sourceGroupCheck = [];
        $this->resetValidation('sourceReplacementGroup');
    }

    #[Locked]
    public int $sourceReplacementCheckVersion = 0;

    public string $sourceReplacementSearch = '';
    public int $sourceReplacementPerPage = 25;

    public function updatedSourceReplacementPerPage(): void
    {
        if (! in_array($this->sourceReplacementPerPage, [10, 25, 50, 100], true)) {
            $this->sourceReplacementPerPage = 25;
        }
    }

    public array $selectedPublicationRevisions = [];

    #[Locked]
    public array $publicationBatch = [];

    public bool $publicationTaskOpen = false;

    #[Locked]
    public array $publicationTask = [];

    public function updatedPublicationSearch(): void
    {
        $this->resetEmptyTextSearch('publicationSearch');
        $this->resetPage('publicationPage');
    }

    public function updatedPublicationPerPage(): void
    {
        if (! in_array($this->publicationPerPage, [10, 25, 50, 100], true)) {
            $this->publicationPerPage = 25;
        }
        $this->resetPage('publicationPage');
    }

    public function openPublicationTask(?int $revision = null): void
    {
        $this->resetValidation(['publicationTask', 'publicationReason', 'selectedPublicationRevisions']);
        $this->publicationBatch = [];
        $this->publicationTask = [];
        $this->publicationTaskOpen = false;
        $this->publicationReason = '';
        $this->publicationReasonLocale = $this->reasonTargetLocale();
        try {
            if ($revision === null) {
                $this->validate(['selectedPublicationRevisions' => 'required|array|min:1', 'selectedPublicationRevisions.*' => 'required|integer|min:1']);
            }
            $tasks = [];
            foreach ($revision === null ? array_unique($this->selectedPublicationRevisions) : [$revision] as $id) {
                $tasks[] = app(GenerationEditor::class)->reviewWorklist()->inspectPublication((int) $id);
            }
            $this->publicationBatch = $tasks;
            $this->publicationTask = $tasks[0];
            $this->publicationTaskOpen = true;
        } catch (\RuntimeException $error) {
            $this->publicationPreparationError($error, $revision === null ? $this->selectedPublicationRevisions : [$revision]);
        }
    }

    public function preparePublicationTask(): void
    {
        try {
            $this->validate(['publicationReason' => 'required|string|min:3|max:2000']);
        } catch (\Illuminate\Validation\ValidationException $error) {
            \Flux\Flux::toast(heading: __('Check the reason for publication'),
                text: $error->validator->errors()->first('publicationReason'), variant: 'danger', duration: 8000);
            throw $error;
        }
        if (! $this->publicationTaskOpen || $this->publicationTask === []) {
            return;
        }
        try {
            $revisions = [];
            foreach ($this->publicationBatch ?: [$this->publicationTask] as $task) {
                $revision = (int) $task['context']['revision_id'];
                app(GenerationEditor::class)->reviewWorklist()->inspectPublication($revision);
                $revisions[$revision] = $task['fingerprint'];
            }
            $run = app(GenerationPublisher::class)->prepare((string) Str::uuid(), array_keys($revisions),
                (int) auth()->id(), $this->publicationReason, array_values($revisions), ...$this->reasonLocaleArguments('publication', $this->publicationReason));
            $this->publicationTaskOpen = false;
            $this->selectedPublicationRevisions = array_values(array_diff($this->selectedPublicationRevisions, array_keys($revisions)));
            $this->openPublication($run['run']);
        } catch (PublicationFileReserved $error) {
            $this->notifyPublicationReservation($error);
        } catch (\RuntimeException $error) {
            $this->publicationPreparationError($error, array_column(array_column($this->publicationBatch ?: [$this->publicationTask], 'context'), 'revision_id'));
        }
    }

    public function preparePublication(): void
    {
        $this->validate(['publicationReason' => 'required|string|min:3|max:2000', 'locale' => ['required', Rule::in($this->locales())]]);
        if (! $this->editorOpen || (! $this->publicationSelectionOpen && ! ($this->context['review']['approved'] ?? false))
            || ($this->context['finding']['status'] ?? '') !== 'active' || $this->locale !== $this->loadedLocale
            || (! $this->publicationSelectionOpen && $this->value !== ($this->context['revision']['value'] ?? null))) {
            $this->addError('publication', __('Open an approved saved revision without unsaved changes.'));

            return;
        }
        try {
            $this->ensureLanguageDraftsSaved();
            $revisions = $this->publicationSelectionOpen
                ? $this->selectedLanguageRevisions($this->publicationLocales, $this->publicationChoices, true)
                : [(int) $this->context['revision']['id'] => $this->context['review']['fingerprint']];
            $run = app(GenerationPublisher::class)->prepare((string) Str::uuid(), array_keys($revisions),
                (int) auth()->id(), $this->publicationReason, array_values($revisions), ...$this->reasonLocaleArguments('publication', $this->publicationReason));
            $this->publicationSelectionOpen = false;
            $this->openPublication($run['run']);
        } catch (PublicationFileReserved $error) {
            $this->notifyPublicationReservation($error);
        } catch (\RuntimeException $error) {
            $this->publicationPreparationError($error, array_keys($revisions ?? []));
        }
    }

    private function publicationPreparationError(\RuntimeException $error, array $revisions): void
    {
        \Illuminate\Support\Facades\Log::warning('Workbench publication preparation failed', [
            'actor_id' => auth()->id(),
            'revision_ids' => $revisions,
            'test_mode' => $this->testToken !== '',
            'error' => $error->getMessage(),
        ]);
        if ($error instanceof InvalidPublicationKey) {
            $this->notifyInvalidPublicationKey($error, $this->editorOpen ? (int) ($this->context['finding']['key_id'] ?? 0) : null, $this->selected);
            return;
        }
        \Flux\Flux::toast(heading: __('Publication preview could not be prepared'), text: $error->getMessage(), variant: 'danger', duration: 0);
    }

    private function notifyInvalidPublicationKey(InvalidPublicationKey $error, ?int $originKey, ?int $originFinding): void
    {
        $finding = $originFinding;
        if (!$finding || $originKey !== $error->keyId) {
            try {
                $finding = app(GenerationEditor::class)->workflowFindingForKey($error->keyId);
            } catch (\RuntimeException $unavailable) {
                \Flux\Flux::toast(heading: $error->heading, text: $error->detail.' '.$unavailable->getMessage(), variant: 'danger', duration: 0);
                return;
            }
        }
        $this->dispatch('toast-show', slots: ['heading' => $error->heading, 'text' => $error->detail],
            dataset: ['variant' => 'danger'], duration: 0, action: [
                'label' => __('Edit finding #:id', ['id' => $finding]),
                'event' => 'workbench-edit-key-finding',
                'params' => ['component' => $this->getId(), 'finding' => $finding],
            ]);
    }

    public function editKeyFinding(int $finding): void
    {
        if (!$this->editorOpen || $this->selected !== $finding) {
            $this->open($finding);
        }
        $this->publicationOpen = $this->publicationTaskOpen = $this->publicationSelectionOpen = $this->workflowStatusOpen = false;
        $this->dispatch('workflow-focus', target: 'workbench-key-assignment');
    }

    private function notifyPublicationReservation(PublicationFileReserved $error): void
    {
        $scope = 'publication-reservation:'.$this->getId();
        $this->dispatch('toast-show',
            slots: [
                'heading' => __('Language file reserved'),
                'text' => __('An open publication reserves lang/:path. Open that run to continue publication or release the file reservation.', ['path' => $error->relativePath]),
            ],
            dataset: ['variant' => 'danger', 'noticeScope' => $scope, 'replaceNoticeScope' => $scope],
            duration: 0,
            action: [
                'label' => __('Open reserving publication'),
                'event' => 'workbench-open-reserving-publication',
                'params' => ['component' => $this->getId(), 'run' => $error->runId],
            ],
        );
    }

    public function openPublication(string $run): void
    {
        $this->inspectPublicationRun($run);
    }

    private function inspectPublicationRun(string $run, ?int $originKey = null, ?int $originFinding = null): void
    {
        $this->resetValidation('publication');
        try {
            $this->publication = app(GenerationPublisher::class)->inspect($run);
            $this->publicationTaskOpen = false;
            $this->publicationSelectionOpen = false;
            $this->closure = [];
            $this->closureReason = '';
            $this->publicationOpen = true;
        } catch (InvalidPublicationKey $error) {
            $this->notifyInvalidPublicationKey($error, $originKey, $originFinding);
        } catch (\RuntimeException $error) {
            $this->publicationActionError($error->getMessage(), __('Publication could not be opened'));
        }
    }

    public function publish(): void
    {
        if (! $this->publicationOpen || ! $this->publication || (int) $this->publication['actor'] !== (int) auth()->id()) {
            $this->publicationActionError(__('Only the preparing user can confirm or resume this publication.'));

            return;
        }
        $run = $this->publication['run'];
        try {
            app(GenerationPublisher::class)->publish($run, $this->publication['fingerprint']);
            $this->openPublication($run);
            if ($this->editorOpen) {
                $this->reload();
            }
            \Flux\Flux::toast(heading: __('Publication completed'),
                text: __('Run: :run. Language files written and file hashes verified.', ['run' => $run]),
                variant: 'success', duration: 0);
        } catch (\RuntimeException $error) {
            $this->publication = app(GenerationPublisher::class)->inspect($run);
            $this->publicationActionError($error instanceof InvalidPublicationKey ? $error->detail : $error->getMessage(),
                $error instanceof InvalidPublicationKey ? $error->heading : __('Publication failed'));
        }
    }

    public function previewClosure(): void
    {
        if (! $this->publicationOpen || ! $this->publication) {
            return;
        }
        try {
            $this->closure = app(GenerationPublisher::class)->closurePreview($this->publication['run']);
        } catch (\RuntimeException $error) {
            $this->publicationActionError($error->getMessage(), __('Publication closure could not be checked'));
        }
    }

    public function closePublication(): void
    {
        try {
            $this->validate(['closureReason' => 'required|string|min:3|max:2000']);
        } catch (\Illuminate\Validation\ValidationException $error) {
            \Flux\Flux::toast(heading: __('Publication closure reason is invalid'), variant: 'danger', text: collect($error->errors())->flatten()->first(), duration: 8000);
            return;
        }
        if (! $this->publicationOpen || ! $this->publication || ! $this->closure) {
            $this->publicationActionError(__('Inspect the current files before closing this run.'));

            return;
        }
        try {
            app(GenerationPublisher::class)->close($this->publication['run'], $this->closure['fingerprint'], (int) auth()->id(), $this->closureReason);
            $this->openPublication($this->publication['run']);
            $this->successToast(__('Publication run closed'),
                __('Run: :run. Closed without further writes.', ['run' => $this->publication['run']]));
        } catch (\RuntimeException $error) {
            $this->publicationActionError($error->getMessage(), __('Publication run could not be closed'));
        }
    }

    private function publicationActionError(string $message, ?string $heading = null): void
    {
        $this->addError('publication', $message);
        \Flux\Flux::toast(heading: $heading ?? __('Publication action could not be completed'), variant: 'danger', text: $message, duration: 8000);
    }

    public function previewSelectedSourceGroup(): void
    {
        $this->resetValidation('sourcePreview');
        $this->sourcePreviewOpen = false;
        $this->sourcePreview = [];
        $this->sourceReason = '';
        if ($this->sourceReplacementGroup === []) {
            $this->addError('sourcePreview', __('Select a translation key first.'));
            return;
        }
        try {
            $writer = app(SourceReplacementWriter::class);
            $pending = $writer->pendingBatchForKey($this->sourceReplacementGroup['key_id']);
            if ($pending !== null) {
                $this->editorOpen = $this->sourceComparisonOpen = false;
                $this->openSourceRun($pending);
                return;
            }
            $this->sourcePreview = $writer->previewBatch(
                $this->sourceReplacementGroup['key_id'], $this->sourceReplacementGroup['finding_ids']);
            $languages = app(TranslationWorkbenchEditorLocales::class)->resolve();
            $this->sourcePreview['translation_files'] = app(GenerationEditor::class)->translationFiles(
                (int) $this->sourcePreview['key_id'], $this->sourcePreview['key'],
                array_values(array_unique(['en', $languages['active'], ...$languages['sub']])));
            $this->editorOpen = $this->sourceComparisonOpen = false;
            $this->sourcePreviewOpen = true;
        } catch (\RuntimeException $error) {
            $this->addError('sourcePreview', $error->getMessage());
        }
    }

    public function previewSelectedSource(int $finding): void
    {
        $this->resetValidation('sourcePreview');
        $this->sourcePreview = [];
        $this->sourcePreviewOpen = false;
        $this->sourceReason = '';
        if (! in_array($finding, $this->sourceReplacementGroup['finding_ids'] ?? [], true)) {
            $this->addError('sourcePreview', __('Select this translation key before opening its source preview.'));
            return;
        }
        try {
            $this->sourcePreview = app(GenerationEditor::class)->sourceReplacementPreviewForGroup(
                $finding, $this->sourceReplacementGroup['key_id'], $this->sourceReplacementGroup['finding_ids']);
            $this->editorOpen = false;
            $this->sourceComparisonOpen = false;
            $languages = app(TranslationWorkbenchEditorLocales::class)->resolve();
            $this->sourcePreview['translation_files'] = app(GenerationEditor::class)->translationFiles(
                (int) $this->sourcePreview['key_id'], $this->sourcePreview['key'],
                array_values(array_unique(['en', $languages['active'], ...$languages['sub']])));
            $this->sourcePreviewOpen = true;
        } catch (\RuntimeException $error) {
            $this->addError('sourcePreview', $error->getMessage());
        }
    }

    public function previewSource(): void
    {
        $this->resetValidation('sourcePreview');
        $this->sourcePreview = [];
        $this->sourcePreviewOpen = false;
        if (! $this->editorOpen || ! ($this->context['finding']['key_id'] ?? null)) {
            $this->addError('sourcePreview', __('Open a finding with an explicit key assignment first.'));

            return;
        }
        try {
            $this->sourcePreview = app(GenerationEditor::class)->sourcePreview($this->selected,
                (int) $this->context['finding']['key_id'], (int) $this->context['finding']['link_id'], 'en');
            $languages = app(TranslationWorkbenchEditorLocales::class)->resolve();
            $this->sourcePreview['translation_files'] = app(GenerationEditor::class)->translationFiles(
                (int) $this->sourcePreview['key_id'], $this->sourcePreview['key'],
                array_values(array_unique(['en', $languages['active'], ...$languages['sub']])));
            $this->sourcePreviewOpen = true;
        } catch (\RuntimeException $error) {
            $this->addError('sourcePreview', $error->getMessage());
        }
    }

    public function prepareSource(): void
    {
        $this->validate(['sourceReason' => 'required|string|min:3|max:2000']);
        if (! $this->sourcePreviewOpen || ! ($this->sourcePreview['ready'] ?? false)) {
            $this->addError('sourcePreview', __('Open a ready source preview first.'));

            return;
        }
        try {
            $run = ($this->sourcePreview['batch'] ?? false)
                ? app(SourceReplacementWriter::class)->prepareBatch($this->sourcePreview['key_id'], $this->sourcePreview['finding_ids'],
                    $this->sourcePreview['fingerprint'], (int) auth()->id(), $this->sourceReason)
                : app(SourceReplacementWriter::class)->prepare($this->sourcePreview['finding'], $this->sourcePreview['source_locale'],
                    $this->sourcePreview['fingerprint'], (int) auth()->id(), $this->sourceReason);
            $this->sourcePreviewOpen = false;
            $this->openSourceRun($run);
            if ($this->sourceRunOpen && ($this->sourceRun['batch'] ?? false)) {
                $this->successToast(__('Selected source replacements prepared'),
                    __('Review the file operations and confirm to write the changes.'));
            }
        } catch (\RuntimeException $error) {
            if ($error instanceof \Gunreip\TranslationWorkbench\Generation\SourceScanRequired) {
                $this->sourcePreview['ready'] = false;
                $this->sourcePreview['blocker'] = $error->getMessage();
                $this->sourcePreview['scan_required'] = $error->details();
            }
            $this->addError('sourcePreview', $error->getMessage());
        }
    }

    public function openSourceRun(string $run): void
    {
        $this->resetValidation('sourceRun');
        $this->sourceRun = [];
        $this->sourceRunOpen = false;
        try {
            $this->sourceRun = app(SourceReplacementWriter::class)->inspect($run);
            $this->sourceRunOpen = true;
            $this->sourceCloseReason = '';
        } catch (\RuntimeException $error) {
            $this->addError('sourceRun', $error->getMessage());
        }
    }

    public function applySource(): void
    {
        if (! $this->sourceRunOpen || ! $this->sourceRun) {
            return;
        }
        $run = $this->sourceRun['run'];
        $batch = $this->sourceRun['batch'] ?? false;
        $writer = app(SourceReplacementWriter::class);
        $this->resetValidation('sourceRun');
        try {
            $result = $writer->apply($run, (int) auth()->id());
            if ($batch) {
                \Flux\Flux::toast(heading: __('Selected source replacements completed'),
                    text: __('Key #:key · Files: :files. Run a new scan to confirm the updated source positions.', [
                        'key' => $result['key'], 'files' => count($this->sourceRun['files']),
                    ]), variant: 'success', duration: 0);
            } else {
                $this->successToast(__('Source replacement completed'),
                    __('Run: :run · Successor finding IDs: :ids', [
                        'run' => $this->sourceRun['run'],
                        'ids' => '#' . implode(', #', array_column($result['successors'], 'successor')),
                    ]));
            }
            $this->sourceRun = $writer->inspect($run);
            $this->sourceCloseReason = '';
            if ($this->editorOpen) {
                $this->open((int) $result['successors'][0]['successor']);
            }
            if ($this->sourceReplacementGroup !== []) {
                $this->sourceGroupCheck = [];
                try {
                    $remaining = app(GenerationEditor::class)->remainingSourceGroupIds((int) $this->sourceReplacementGroup['key_id']);
                    $this->sourceReplacementGroup = $remaining
                        ? array_replace($this->sourceReplacementGroup, ['finding_ids' => $remaining]) : [];
                } catch (\RuntimeException) {
                    $this->sourceReplacementGroup = [];
                }
            }
        } catch (\RuntimeException $error) {
            try {
                $this->sourceRun = $writer->inspect($run);
            } catch (\RuntimeException) {
                // Keep the original error visible if the file has disappeared.
            }
            if ($batch) {
                \Flux\Flux::toast(heading: __('Check source replacement status'), text: $error->getMessage(),
                    variant: 'danger', duration: 0);
            } else {
                $this->addError('sourceRun', $error->getMessage());
            }
        }
    }

    public function acknowledgeSourceBatch(): void
    {
        if (!$this->sourceRunOpen || !($this->sourceRun['batch'] ?? false) || $this->sourceRun['state'] !== 'complete') {
            return;
        }

        try {
            app(SourceReplacementWriter::class)->acknowledgeBatch($this->sourceRun['run'], (int) auth()->id());
            $this->sourceRunOpen = false;
            $this->sourceRun = [];
        } catch (\RuntimeException $error) {
            \Flux\Flux::toast(heading: __('Could not confirm the batch result'), text: $error->getMessage(), variant: 'danger');
        }
    }

    public function closeSource(): void
    {
        $this->validate(['sourceCloseReason' => 'required|string|min:3|max:2000']);
        if (! $this->sourceRunOpen || ! $this->sourceRun) {
            return;
        }
        try {
            app(SourceReplacementWriter::class)->close($this->sourceRun['run'], (int) auth()->id(), $this->sourceCloseReason);
            $this->openSourceRun($this->sourceRun['run']);
        } catch (\RuntimeException $error) {
            $this->addError('sourceRun', $error->getMessage());
        }
    }

    public function openScan(string $run, string $category = 'created'): void
    {
        $this->scanDecisionReturnRun = '';
        $this->resetErrorBag('scan');
        $this->scanOpen = false;
        try {
            app(GenerationEditor::class)->scanPage($run, $category, 1);
            $this->scanReviewPath = '';
            $this->scanUsageContext = [];
            $this->scanUsageNotice = '';
            $this->selectedScan = $run;
            $this->scanCategory = $category;
            $this->scanSearch = '';
            $this->resetPage('scanResultsPage');
            $this->scanOpen = true;
        } catch (\RuntimeException $error) {
            $this->addError('scan', $error->getMessage());
        }
    }

    public function updatedScanPerPage(): void
    {
        if (!in_array($this->scanPerPage, [10, 25, 50, 100], true)) $this->scanPerPage = 25;
        $this->resetPage('scanResultsPage');
    }

    public function updatedScanSearch(): void
    {
        $this->resetPage('scanResultsPage');
    }

    public function updatedScanCategory(): void
    {
        $this->scanReviewPath = '';
        $this->scanUsageContext = [];
        $this->scanUsageNotice = '';
        if (! in_array($this->scanCategory, ['created', 'confirmed', 'moved', 'removed', 'review_files', 'usage_decisions', 'missing_immutable'], true)) {
            $this->scanCategory = 'created';
        }
        $this->resetPage('scanResultsPage');
    }

    public function openPreservedFindings(string $path): void
    {
        $this->resetErrorBag('scanReview');
        try {
            app(GenerationEditor::class)->preservedFindingsPage($this->selectedScan, $path, 1);
            $this->scanCaptureView = false;
            $this->scanReviewPath = $path;
            $this->resetPage('preservedFindingsPage');
        } catch (\RuntimeException $error) {
            $this->addError('scanReview', $error->getMessage());
        }
    }

    public function openConsideredScanUsage(int $event): void
    {
        $this->resetErrorBag('scanReview');
        try {
            $context = app(GenerationEditor::class)->consideredScanUsage($this->selectedScan, $event);
            $this->scanDecisionReturnRun = $this->selectedScan;
            $this->selectedScan = $context['run'];
            $this->scanReviewPath = $context['path'];
            $this->scanCaptureView = true;
            $this->setPage((int) floor($context['index'] / 25) + 1, 'scanCapturesPage');
            $this->scanUsageContext = $context;
            $this->scanUsageChoice = $context['decision']['decision'] ?? '';
            $this->scanUsageReason = $context['decision']['reason'] ?? '';
            $this->scanUsageNotice = '';
            $this->resetErrorBag('scanUsage');
            $this->loadScanUsageReasons();
            $this->resetValidation(['scanUsageChoice', 'scanUsageReason']);
            $this->scanOpen = false;
            $this->scanUsageOpen = true;
        } catch (\RuntimeException $error) {
            $this->addError('scanReview', $error->getMessage());
        }
    }

    private function loadScanUsageReasons(): void
    {
        $this->scanUsageReasonOptions = [];
        if ($this->scanUsageContext === [] || ! in_array($this->scanUsageChoice, ['translate', 'database_value'], true)) {
            return;
        }
        $defaults = $this->scanUsageChoice === 'database_value'
            ? [__('Value comes from the database and is displayed unchanged.'),
                __('User-entered content is displayed unchanged.'),
                __('Runtime value is a technical identifier and does not require translation.')]
            : [__('translation-workbench.literal.runtime_value_contains_text_that_requires_translation'),
                __('Dynamic translation key needs an explicit assignment.'),
                __('Source usage requires further review before translation.')];
        foreach ([...$defaults, ...app(GenerationEditor::class)->scanUsageReasons($this->scanUsageChoice)] as $reason) {
            $reason = trim($reason);
            if ($reason !== '') {
                $this->scanUsageReasonOptions[hash('sha256', $reason)] = $reason;
            }
        }
    }

    public function updatedScanUsageChoice(): void
    {
        $this->loadScanUsageReasons();
    }

    public function openScanUsage(int $index): void
    {
        $this->scanUsageContext = [];
        $this->scanUsageNotice = '';
        $this->resetErrorBag('scanUsage');
        try {
            $this->scanUsageContext = app(GenerationEditor::class)->scanUsage($this->selectedScan, $this->scanReviewPath, $index);
            $this->scanUsageChoice = $this->scanUsageContext['decision']['decision'] ?? '';
            $this->scanUsageReason = $this->scanUsageContext['decision']['reason'] ?? '';
            $this->loadScanUsageReasons();
            $this->resetValidation(['scanUsageChoice', 'scanUsageReason']);
            $this->scanOpen = false;
            $this->scanUsageOpen = true;
        } catch (\RuntimeException $error) {
            $this->addError('scanUsage', $error->getMessage());
        }
    }

    public function updatedScanUsageOpen(bool $open): void
    {
        if (! $open && $this->scanUsageContext !== []) {
            $this->scanOpen = true;
            $this->scanUsageContext = [];
            $this->resetValidation(['scanUsage', 'scanUsageChoice', 'scanUsageReason']);
        }
    }

    public function saveScanUsage(): void
    {
        $this->validate(['scanUsageChoice' => 'required|in:translate,database_value', 'scanUsageReason' => 'required|string|min:3|max:2000']);
        if ($this->scanUsageContext === []) {
            $this->addError('scanUsage', __('Open a captured source occurrence first.'));
            return;
        }
        try {
            $context = $this->scanUsageContext;
            app(GenerationEditor::class)->decideScanUsage($context['run'], $context['path'], $context['index'],
                $this->scanUsageChoice, $this->scanUsageReason, (int) auth()->id(), (int) ($context['decision']['id'] ?? 0));
            $this->scanUsageOpen = false;
            $this->scanOpen = true;
            $this->scanUsageContext = [];
            $this->scanUsageNotice = __('Usage decision saved. The next scan checks the unchanged source before applying it. No translation or source file was changed.');
        } catch (\RuntimeException $error) {
            $this->addError('scanUsage', $error->getMessage());
        }
    }

    public function openScanCaptures(string $path): void
    {
        $this->scanUsageContext = [];
        $this->scanUsageNotice = '';
        $this->resetErrorBag('scanReview');
        try {
            app(GenerationEditor::class)->scanCapturesPage($this->selectedScan, $path, 1);
            $this->scanReviewPath = $path;
            $this->scanCaptureView = true;
            $this->resetPage('scanCapturesPage');
        } catch (\RuntimeException $error) {
            $this->addError('scanReview', $error->getMessage());
        }
    }

    public function closePreservedFindings(): void
    {
        if ($this->scanDecisionReturnRun !== '') {
            $this->selectedScan = $this->scanDecisionReturnRun;
            $this->scanDecisionReturnRun = '';
            $this->scanCategory = 'usage_decisions';
        }
        $this->scanReviewPath = '';
        $this->scanUsageContext = [];
        $this->scanUsageNotice = '';
        $this->resetErrorBag('scanReview');
    }

    #[Locked]
    public array $scanStorage = [];

    public function refreshScanStorage(): void
    {
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess::class)->authorize();
        if (!$this->scanWorkflowOpen || $this->testToken !== '') return;
        try {
            $this->scanStorage = app(GenerationEditor::class)->scanStorageSummary();
            $this->scanStorage['cache'] = app(\Gunreip\TranslationWorkbench\Generation\ScanCacheMaintenance::class)->summary();
        } catch (\Throwable $error) { $this->addError('scanWorkflow', $error->getMessage()); }
    }

    public bool $scanWorkflowOpen = false;
    #[Locked]
    public string $scanWorkflowId = '';
    #[Locked]
    public array $scanWorkflow = [];

    #[Locked]
    public array $scanFreshness = [];

    public function refreshScanFreshness(): void
    {
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess::class)->authorize();
        $this->scanFreshness = ['reusable' => false];
        if ($this->testToken !== '') return;
        try {
            $this->scanFreshness = app(\Gunreip\TranslationWorkbench\Generation\ScanResultFreshness::class)->inspect();
        } catch (\Throwable $error) {
            $this->scanFreshness['reason'] = __('The latest scan could not be verified. Open the scan dialog to check the source.');
            report($error);
        }
    }

    private function scanAction(array $requirement): array
    {
        if ($requirement['required']) {
            return ['reusable' => false, 'reason' => __('translation-workbench.literal.stored_finding_positions_need_to_be_checked_by_a_new_scan')];
        }
        return $this->scanFreshness;
    }

    public function openCurrentScan(): void
    {
        $this->refreshScanFreshness();
        $action = $this->scanAction(app(GenerationEditor::class)->scanRequirementSummary());
        if ($action['reusable'] ?? false) {
            $this->openScan($action['run']);
            return;
        }
        $this->openNewSourceScan();
    }

    public function openNewSourceScan(): void
    {
        $this->openScanWorkflow();
        if ($this->testToken !== '') return;
        // Keep running work or a prepared preview; do not reopen an obsolete completed result.
        if (($this->scanWorkflow['state'] ?? '') === 'complete') {
            $this->scanWorkflowId = '';
            session()->forget('workbench.scan_workflow');
            $this->scanWorkflow = ['state' => 'idle', 'target' => app(\Gunreip\TranslationWorkbench\Generation\WorkingScanService::class)->target()];
        }
    }

    public function openScanWorkflow(): void
    {
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess::class)->authorize();
        $this->resetValidation('scanWorkflow');
        $this->scanWorkflowOpen = true;
        if ($this->testToken !== '') {
            $this->addError('scanWorkflow', __('Regular source scans are available for working data. UI test fixtures are reset through the testcase toggle.'));
            return;
        }
        try {
            if ($this->scanWorkflowId === '') $this->scanWorkflowId = (string) session('workbench.scan_workflow', '');
            if ($this->scanWorkflowId !== '') { $this->pollScanWorkflow(); return; }
            $this->scanWorkflow = ['state' => 'idle', 'target' => app(\Gunreip\TranslationWorkbench\Generation\WorkingScanService::class)->target()];
        } catch (\Throwable $error) { $this->addError('scanWorkflow', $error->getMessage()); }
    }

    public string $scanPositionPath = '';

    #[Locked]
    public ?int $scanPositionFinding = null;

    public function previewComparisonPositions(): void
    {
        if (!$this->sourceComparisonOpen || empty($this->sourceComparison['finding']) || $this->testToken !== '') return;

        $finding = $this->sourceComparison['finding'];
        $this->openScanWorkflow();
        $this->sourceComparisonOpen = false;
        if (!in_array($this->scanWorkflow['state'] ?? '', ['queued_preview', 'scanning', 'preview_ready', 'queued_apply', 'applying'], true)) {
            $this->startScanPreview();
        }
        $this->scanPositionPath = $finding['path'];
        $this->scanPositionFinding = (int) $finding['id'];
        $this->resetPage('scanPositionPage');
    }

    public function updatedScanPositionPath(): void
    {
        $this->scanPositionFinding = null;
        $this->resetPage('scanPositionPage');
    }

    public function startScanPreview(): void
    {
        $this->startScan(false);
    }

    public function startScanAndSave(): void
    {
        $this->startScan(true);
    }

    private function startScan(bool $save): void
    {
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess::class)->authorize();
        if (!$this->scanWorkflowOpen || $this->testToken !== '') return;
        if (in_array($this->scanWorkflow['state'] ?? '', ['queued_preview', 'scanning', 'queued_apply', 'applying'], true)) return;
        try {
            $this->resetValidation('scanWorkflow');
            $this->scanPositionPath = '';
            $this->scanPositionFinding = null;
            $this->resetPage('scanPositionPage');
            $this->scanWorkflowId = app(\Gunreip\TranslationWorkbench\Generation\ScanRunCoordinator::class)->start((int) auth()->id(), $save);
            session(['workbench.scan_workflow' => $this->scanWorkflowId]);
            $this->pollScanWorkflow();
        } catch (\Throwable $error) { $this->addError('scanWorkflow', $error->getMessage()); }
    }

    public function confirmScan(): void
    {
        app(\Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess::class)->authorize();
        if (!$this->scanWorkflowOpen || $this->testToken !== '') return;
        try {
            $this->resetValidation('scanWorkflow');
            app(\Gunreip\TranslationWorkbench\Generation\ScanRunCoordinator::class)->confirm($this->scanWorkflowId, (int) auth()->id());
            $this->pollScanWorkflow();
        } catch (\Throwable $error) { $this->addError('scanWorkflow', $error->getMessage()); }
    }

    public function pollScanWorkflow(): void
    {
        if (!$this->scanWorkflowOpen || $this->scanWorkflowId === '' || $this->testToken !== '') return;
        try {
            $previous = $this->scanWorkflow['state'] ?? '';
            $this->scanWorkflow = app(\Gunreip\TranslationWorkbench\Generation\ScanRunCoordinator::class)->status($this->scanWorkflowId, (int) auth()->id());
            if ($this->scanWorkflow['state'] === 'complete' && $previous !== 'complete') {
                $this->sourceReplacementCheckVersion++;
                app(GenerationEditor::class)->scanRequirementSummary(true);
                $this->releaseSourceReplacementGroup();
                $this->refreshScanFreshness();
            }
        } catch (\Throwable $error) {
            $this->scanWorkflowId = '';
            session()->forget('workbench.scan_workflow');
            $this->scanWorkflow = ['state' => 'failed', 'error' => $error->getMessage()];
        }
    }

    public function showWorkflowScanResult(): void
    {
        $this->pollScanWorkflow();
        if (($this->scanWorkflow['state'] ?? '') !== 'complete') return;
        $run = $this->scanWorkflow['result']['scan'];
        $this->scanWorkflowOpen = false;
        $this->openScan($run);
    }

    public function refreshScanRequirement(): void
    {
        app(GenerationEditor::class)->scanRequirementSummary(true);
    }

    public function render()
    {
        $repository = app(GenerationEditor::class);

        $workTarget = $this->reasonTargetLocale();
        $recentWork = $this->overviewOpen ? $repository->recentWork($workTarget, (int) auth()->id()) : [];
        $workTasks = $this->tab === 'in-progress'
            ? $repository->workTasks($workTarget, $this->workOnlyMine ? (int) auth()->id() : null,
                $this->workSearch, (int) $this->getPage('workPage'), $this->workPerPage, caseSensitive: $this->textSearchOptions['workSearch']['case'] ?? false, wholeWord: $this->textSearchOptions['workSearch']['word'] ?? false, findingId: $this->workFindingId, keyId: $this->workKeyId)
            : null;
        $workActors = [];
        $workLocales = [];
        foreach ([...$recentWork, ...($workTasks?->items() ?? [])] as $work) {
            array_push($workLocales, ...array_filter(array_map('trim', explode(',', $work['locales'] ?? ''))));
            $actor = (int) $work['actor_id'];
            if (!array_key_exists($actor, $workActors)) {
                $workActors[$actor] = app(\Gunreip\TranslationWorkbench\Support\WorkbenchUserSummary::class)->find($actor);
            }
        }

        if ($this->tab === 'in-progress' && !array_key_exists((int) auth()->id(), $workActors)) {
            $workActors[(int) auth()->id()] = app(\Gunreip\TranslationWorkbench\Support\WorkbenchUserSummary::class)->find((int) auth()->id());
        }

        $sharedRows = $this->sharedOpen ? $repository->sharedFindings($this->sharedFinding, (int) $this->getPage('sharedFindingsPage'), $this->sharedPerPage, $this->sharedSort, $this->sharedDirection) : null;

        $rows = $this->tab === 'findings' ? $repository->page($this->search, $this->findingId, $this->status, (int) $this->getPage('findingsPage'), $this->findingsSort, $this->findingsDirection, $this->perPage, $this->unassignedOnly, $this->includeArchived, $this->searchCaseSensitive, $this->searchWholeWord, $workTarget, $this->searchWholeLiteral) : null;
        $translationTasks = $this->tab === 'translations' ? $repository->translationTasks($this->translationSearch, (int) $this->getPage('translationsPage'), $this->translationPerPage, $this->translationSort, $this->translationDirection, $this->translationKeyId, caseSensitive: $this->textSearchOptions['translationSearch']['case'] ?? false, wholeWord: $this->textSearchOptions['translationSearch']['word'] ?? false) : null;
        $publishedTasks = $this->tab === 'published' ? $repository->publishedWorklist()->page($this->publishedSearch, (int) $this->getPage('publishedPage'), $this->publishedPerPage, $this->publishedKeyId, $this->publishedLanguage, $this->publishedSort, $this->publishedDirection, caseSensitive: $this->textSearchOptions['publishedSearch']['case'] ?? false, wholeWord: $this->textSearchOptions['publishedSearch']['word'] ?? false) : null;
        $filterLanguages = in_array($this->tab, ['review', 'publication', 'published'], true) ? $this->locales() : [];
        $publicationTasks = $this->tab === 'publication' ? $repository->reviewWorklist()->publicationPage($this->publicationSearch, (int) $this->getPage('publicationPage'), $this->publicationPerPage, $this->publicationKeyId, $this->publicationLanguage, $this->publicationSort, $this->publicationDirection, caseSensitive: $this->textSearchOptions['publicationSearch']['case'] ?? false, wholeWord: $this->textSearchOptions['publicationSearch']['word'] ?? false) : null;
        $taskLocales = [...array_column($publishedTasks?->items() ?? [], 'locale'), ...array_column($publicationTasks?->items() ?? [], 'locale')];
        foreach ($this->publicationBatch as $task) {
            $taskLocales[] = $task['context']['locale'];
        }
        foreach ($this->reviewBatch as $task) {
            $taskLocales[] = $task['context']['locale'];
        }
        foreach ($translationTasks?->items() ?? [] as $task) {
            array_push($taskLocales, ...array_column($task['locales'], 'locale'));
        }
        $findingIds = array_column($rows?->items() ?? [], 'id');
        $findingStates = $findingIds ? $repository->workflowStates($findingIds) : [];
        foreach ($findingStates as $states) {
            foreach ($states as $state) {
                $taskLocales[] = $state['locale'];
                $taskLocales[] = explode('-', str_replace('_', '-', $state['locale']))[0];
            }
        }
        array_push($taskLocales, ...array_keys($this->reviewChoices), ...array_keys($this->publicationChoices));
        $scanCategoryCounts = $this->scanOpen ? $repository->scanCategoryCounts($this->selectedScan) : [];
        $scanRows = $this->scanOpen && $this->scanReviewPath === '' ? $repository->scanPage($this->selectedScan, $this->scanCategory, (int) $this->getPage('scanResultsPage'), ...($this->scanPerPage !== 25 ? [$this->scanSearch, $this->scanPerPage] : ($this->scanSearch !== '' ? [$this->scanSearch] : []))) : null;
        $summaryIds = [...$findingIds, ...array_filter(array_column($scanRows?->items() ?? [], 'id'))];
        if ($this->editorOpen) {
            $summaryIds[] = $this->selected;
        }
        $captureRows = $this->scanOpen && $this->scanReviewPath !== '' && $this->scanCaptureView ? $repository->scanCapturesPage($this->selectedScan, $this->scanReviewPath, (int) $this->getPage('scanCapturesPage')) : null;
        $decisionActors = [];
        foreach ($captureRows ?? [] as $capture) {
            $actorId = $capture['usage_decision']['actor_id'] ?? null;
            if ($actorId !== null && ($capture['can_classify'] ?? false) && ! array_key_exists($actorId, $decisionActors)) {
                $decisionActors[$actorId] = app(\Gunreip\TranslationWorkbench\Support\WorkbenchUserSummary::class)->find((int) $actorId);
            }
        }
        $scanPositionRows = null;
        if ($this->scanWorkflowOpen && in_array($this->scanWorkflow['state'] ?? '', ['preview_ready', 'changed'], true)
            && in_array($this->scanPositionPath, $this->scanWorkflow['preview']['position_files'] ?? [], true)) {
            $positions = app(\Gunreip\TranslationWorkbench\Generation\ScanRunCoordinator::class)
                ->positionPreview($this->scanWorkflowId, (int) auth()->id(), $this->scanPositionPath);
            if (isset($positions[$this->scanPositionFinding])) {
                $positions = [$this->scanPositionFinding => $positions[$this->scanPositionFinding]] + $positions;
            }
            $page = max(1, min((int) $this->getPage('scanPositionPage'), (int) ceil(count($positions) / 25)));
            $scanPositionRows = new \Illuminate\Pagination\LengthAwarePaginator(
                array_slice($positions, ($page - 1) * 25, 25, true), count($positions), 25, $page, ['pageName' => 'scanPositionPage']);
        }

        $sourceIssues = null;
        if ($this->tab === 'source-issues') {
            $cases = $repository->sourceIssues($this->testToken);
            $sourceIssues = $this->sourceIssuePage($repository, $cases);
        }
        $scanRequirement = $repository->scanRequirementSummary();
        return view('translation-workbench::livewire.translation-workbench', [
            'tabCounts' => $repository->tabCounts($workTarget, $this->workOnlyMine ? (int) auth()->id() : null, $this->testToken),
            'sourceIssues' => $sourceIssues,
            'sourceIssueTotal' => $sourceIssues ? count($cases) : 0,
            'workTasks' => $workTasks,
            'recentWork' => $recentWork,
            'workActors' => $workActors,
            'captureRows' => $captureRows,
            'decisionActors' => $decisionActors,
            'scanPositionRows' => $scanPositionRows,
            'preservedRows' => $this->scanOpen && $this->scanReviewPath !== '' && ! $this->scanCaptureView ? $repository->preservedFindingsPage($this->selectedScan, $this->scanReviewPath, (int) $this->getPage('preservedFindingsPage')) : null,
            'scanRequirement' => $scanRequirement,
            'scanAction' => $this->scanAction($scanRequirement),
            'scanRuns' => $this->overviewOpen ? $repository->recentScans() : [],
            'scanCategoryCounts' => $scanCategoryCounts,
            'scanCategoryTotal' => $scanCategoryCounts[$this->scanCategory] ?? 0,
            'scanRows' => $scanRows,
            'publications' => $this->overviewOpen ? app(GenerationPublisher::class)->recent() : [],
            'publicationActor' => $this->publicationOpen && $this->publication
                ? app(\Gunreip\TranslationWorkbench\Support\WorkbenchUserSummary::class)->find((int) $this->publication['actor']) : null,
            'sourceRuns' => $this->overviewOpen ? app(SourceReplacementWriter::class)->recent() : [],
            'rows' => $rows,
            'findingWorkflowStates' => $findingStates,
            'translationTasks' => $translationTasks,
            'publishedTasks' => $publishedTasks,
            'filterLanguages' => $filterLanguages,
            'sourceReplacementLastEvent' => in_array($this->tab, ['source-replacement', 'completed'], true) ? $repository->latestWorkingEventId() : null,
            'publicationTasks' => $publicationTasks,
            'reviewTasks' => $this->tab === 'review' ? $repository->reviewWorklist()->page($this->reviewSearch, (int) $this->getPage('reviewPage'), $this->reviewPerPage, $this->reviewSort, $this->reviewDirection, $this->reviewKeyId, $this->reviewLanguage, caseSensitive: $this->textSearchOptions['reviewSearch']['case'] ?? false, wholeWord: $this->textSearchOptions['reviewSearch']['word'] ?? false) : null,
            'sharedSummaries' => $summaryIds ? $repository->sharedSummaries(array_values(array_unique($summaryIds))) : [],
            'sharedRows' => $sharedRows,
            'selectableSharedIds' => $this->selectableSharedIds($sharedRows?->items() ?? []),
            'totals' => $this->overviewOpen ? $repository->totals() : [],
            'locales' => $this->editorOpen ? $this->locales() : [],
            'subLanguages' => $this->editorOpen ? app(TranslationWorkbenchEditorLocales::class)->resolve()['sub'] : [],
            'translationFlagCountries' => $this->sourcePreviewOpen || $this->publicationTaskOpen || $this->reviewBatchOpen || $this->publicationOpen || $this->workflowStatusOpen || $this->editorOpen || $translationTasks !== null || $publishedTasks !== null || $publicationTasks !== null || $filterLanguages !== [] || $findingStates !== [] || $workLocales !== [] ? $this->translationFlagCountries([...array_column($this->sourcePreview['translation_files'] ?? [], 'locale'), ...$taskLocales, ...$workLocales, ...array_column($this->workflowStatus['languages'] ?? [], 'locale'), ...array_column($this->publication['changes'] ?? [], 'locale')]) : [],
            'runtimeOptions' => $this->tab === 'source-usage' ? ($this->testToken !== '' ? app(WorkbenchTestEnvironment::class)->runtimeOptions() : app(RuntimeOptionPreview::class)->load()) : null,
        ]);
    }
}
