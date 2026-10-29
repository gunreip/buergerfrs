<?php

namespace Gunreip\TranslationWorkbench\Livewire\Concerns;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorAccess;
use Livewire\Attributes\Locked;

trait HasWorkflowReasons
{
    public bool $workflowReasonOpen = false;
    public string $newWorkflowReason = '';
    public string $newWorkflowReasonLocale = 'en';

    #[Locked]
    public string $workflowReasonField = '';

    #[Locked]
    public array $createdWorkflowReasons = [];

    abstract protected function workflowReasonFields(): array;
    abstract protected function savedWorkflowReasons(string $field, string $kind): array;

    private function workflowReasonKind(string $field): string
    {
        $fields = $this->workflowReasonFields();
        abort_unless(isset($fields[$field]), 422);

        return $fields[$field];
    }

    public function workflowReasonTitle(): string
    {
        if ($this->workflowReasonField === '') return __('Create a reason');

        return match ($this->workflowReasonKind($this->workflowReasonField)) {
            'review' => __('Create a review reason'),
            'publication' => __('Create a publication reason'),
            'source' => __('Create a source replacement reason'),
            'closure' => __('Create a publication closure reason'),
            'source_closure' => __('Create a source replacement closure reason'),
            'usage', 'scan_usage' => __('Create a source usage decision reason'),
            default => __('Create a reason'),
        };
    }

    public function workflowReasonHasLanguage(): bool
    {
        return $this->workflowReasonField !== ''
            && in_array($this->workflowReasonKind($this->workflowReasonField), ['review', 'publication'], true);
    }

    public function workflowReasonOptions(string $field): array
    {
        $kind = $this->workflowReasonKind($field);
        $defaults = match ($kind) {
            'review' => [__('Translation checked for meaning and placeholders.'), __('Return for corrections.'), __('Revoke approval because further changes are required.')],
            'publication' => [__('Publish the reviewed translation.'), __('Publish the corrected translation.')],
            'source' => [__('Replace the captured literal with its approved translation key.')],
            'closure', 'source_closure' => [__('Cancel this operation without writing files.'), __('The prepared operation is no longer required.')],
            'usage' => [__('Value comes from the database and is displayed unchanged.'), __('Runtime value contains text that requires translation.')],
            default => [],
        };
        $options = [...$defaults, ...$this->savedWorkflowReasons($field, $kind),
            ...($this->createdWorkflowReasons[$kind] ?? []), $this->{$field}];

        return array_values(array_unique(array_filter(array_map('trim', $options), fn ($value) => $value !== '')));
    }

    public function openWorkflowReason(string $field, string $search = ''): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        $this->workflowReasonKind($field);
        $this->workflowReasonField = $field;
        $this->newWorkflowReason = trim($search);
        if ($this->workflowReasonHasLanguage()) {
            $this->newWorkflowReasonLocale = $this->selectedReasonLocale($this->workflowReasonKind($field), $search)
                ?? ($field === 'reviewBatchReason' ? $this->reviewBatchReasonLocale : $this->reasonTargetLocale());
        }
        $this->resetValidation('newWorkflowReason');
        $this->workflowReasonOpen = true;
    }

    public function createWorkflowReason(): void
    {
        app(TranslationWorkbenchEditorAccess::class)->authorize();
        abort_unless($this->workflowReasonOpen, 422);
        $kind = $this->workflowReasonKind($this->workflowReasonField);
        $this->newWorkflowReason = trim($this->newWorkflowReason);
        $this->validate(['newWorkflowReason' => 'required|string|min:3|max:2000']);
        if ($this->workflowReasonHasLanguage()) {
            $this->rememberReasonLocale($kind, $this->newWorkflowReason, 'newWorkflowReasonLocale');
        }
        $this->createdWorkflowReasons[$kind] = array_values(array_unique([
            ...($this->createdWorkflowReasons[$kind] ?? []), $this->newWorkflowReason,
        ]));
        $this->{$this->workflowReasonField} = $this->newWorkflowReason;
        $this->resetValidation($this->workflowReasonField);
        $this->newWorkflowReason = '';
        $this->workflowReasonOpen = false;
    }
}
