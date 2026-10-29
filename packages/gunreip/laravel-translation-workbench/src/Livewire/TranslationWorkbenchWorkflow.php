<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use Gunreip\TranslationWorkbench\Support\BaselineFileFreshness;
use Gunreip\TranslationWorkbench\Support\BaselineKeyCorrectionPreview;
use Gunreip\TranslationWorkbench\Support\BaselineProposedSourceChanges;
use Gunreip\TranslationWorkbench\Support\BaselineReaderPreview;
use Gunreip\TranslationWorkbench\Support\BaselineRehearsalReport;
use Gunreip\TranslationWorkbench\Support\BaselineSourceChanges;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class TranslationWorkbenchWorkflow extends Component
{
    public string $activeTab = 'legacy';

    public string $baselineSourceFile = '';

    public bool $baselineSourceSetChecked = false;

    public string $baselineProposedSourceFile = '';

    #[Locked]
    public ?array $baselineProposedChanges = null;

    public string $baselineKey = 'ui.states.all';

    public string $baselineChain = 'de-at';

    public bool $baselineAllLocales = false;

    public string $baselineFilter = 'all';

    public string $baselineProposedKey = '';

    public bool $baselineProposalChecked = false;

    public function updatedBaselineKey(): void
    {
        $this->baselineProposedKey = '';
        $this->baselineProposalChecked = false;
        $this->baselineProposedSourceFile = '';
        $this->baselineProposedChanges = null;
    }

    public function updatedBaselineProposedKey(): void
    {
        $this->baselineProposalChecked = false;
        $this->baselineProposedSourceFile = '';
        $this->baselineProposedChanges = null;
    }

    public function checkBaselineProposal(): void
    {
        $this->baselineProposalChecked = true;
        $this->baselineProposedChanges = null;
    }

    public function checkBaselineProposedChanges(): void
    {
        $this->baselineProposedChanges = null;
        if ($this->activeTab !== 'baseline-preview') {
            return;
        }
        $reader = app(BaselineReaderPreview::class);
        $snapshot = $reader->load();
        $selected = $reader->selection($snapshot, $this->baselineKey, $this->baselineChain, $this->baselineFilter)['selected'];
        $this->baselineProposalChecked = true;
        if ($selected !== null) {
            $this->baselineProposedChanges = app(BaselineProposedSourceChanges::class)
                ->previewAll($snapshot, $selected['key'], $this->baselineProposedKey, base_path());
        }
    }

    public function checkBaselineSourceSet(): void
    {
        $this->baselineSourceSetChecked = true;
    }

    public function updatedBaselineFilter(): void
    {
        $this->updatedBaselineKey();
        $preview = app(BaselineReaderPreview::class);
        $groups = $preview->keyGroups($preview->load());
        if (! array_key_exists($this->baselineFilter, $groups)) {
            $this->baselineFilter = 'all';
        }
        $keys = $groups[$this->baselineFilter];
        if (! in_array($this->baselineKey, $keys, true)) {
            $this->baselineKey = $keys[0] ?? '';
        }
    }

    public function updatedActiveTab(): void
    {
        if (! in_array($this->activeTab, ['legacy', 'workbench', 'target', 'target-ui', 'write-contract', 'baseline-preview'], true)) {
            $this->activeTab = 'legacy';
        }
    }

    public function selectOpenBaselineKey(string $key): void
    {
        if ($this->activeTab !== 'baseline-preview') {
            return;
        }
        $groups = app(BaselineReaderPreview::class)->keyGroups(app(BaselineReaderPreview::class)->load());
        if (! in_array($key, array_merge($groups['blocked_structure'], $groups['option_source_evidenced'], $groups['source_assignment_open']), true)) {
            return;
        }
        $this->baselineFilter = 'all';
        $this->baselineKey = $key;
        $this->updatedBaselineKey();
    }

    public function render(): View
    {
        $preview = app(BaselineReaderPreview::class);
        $data = $this->activeTab === 'baseline-preview' ? $preview->load() : null;
        if ($this->baselineProposedChanges !== null && ($this->activeTab !== 'baseline-preview'
            || $this->baselineProposedChanges['snapshot_sha256'] !== hash('sha256', json_encode($data, JSON_THROW_ON_ERROR)))) {
            $this->baselineProposedChanges = null;
        }
        $selection = $this->activeTab === 'baseline-preview'
            ? $preview->selection($data, $this->baselineKey, $this->baselineChain, $this->baselineFilter) : null;
        $sourceChanges = app(BaselineSourceChanges::class);
        $sourceSnapshot = $this->activeTab === 'baseline-preview' ? $sourceChanges->load() : null;

        return view('translation-workbench::livewire.workflow.index', [
            'baselinePreview' => $selection,
            'baselineSourceChanges' => $this->activeTab === 'baseline-preview'
                ? $sourceChanges->select($sourceSnapshot, $this->baselineSourceFile) : null,
            'baselineSourceCurrent' => $this->activeTab === 'baseline-preview'
                ? $sourceChanges->currentFile($sourceSnapshot, $this->baselineSourceFile, base_path()) : null,
            'baselineSourceSet' => $this->activeTab === 'baseline-preview' && $this->baselineSourceSetChecked
                ? $sourceChanges->currentFiles($sourceSnapshot, base_path()) : null,
            'baselineRehearsal' => $this->activeTab === 'baseline-preview' ? app(BaselineRehearsalReport::class)->load() : null,
            'baselineFiles' => $this->baselineProposalChecked && ($selection['selected'] ?? null)
                ? app(BaselineFileFreshness::class)->inspect($selection['selected'], base_path())
                : null,
            'baselineCorrection' => $this->baselineProposalChecked && ($selection['selected'] ?? null)
                ? app(BaselineKeyCorrectionPreview::class)->inspect($data, $selection['selected']['key'], $this->baselineProposedKey)
                : null,
            'baselineProposedSourceChanges' => $this->baselineProposalChecked && ($selection['selected'] ?? null)
                && $this->baselineProposedSourceFile !== ''
                ? app(BaselineProposedSourceChanges::class)->preview($data, $selection['selected']['key'],
                    $this->baselineProposedKey, $this->baselineProposedSourceFile, base_path())
                : null,
        ]);
    }
}
