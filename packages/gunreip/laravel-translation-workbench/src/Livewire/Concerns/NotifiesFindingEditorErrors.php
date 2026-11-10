<?php

namespace Gunreip\TranslationWorkbench\Livewire\Concerns;

use Illuminate\Validation\ValidationException;

trait NotifiesFindingEditorErrors
{
    public function addError($name, $message)
    {
        $result = parent::addError($name, $message);
        $this->notifyFindingEditorErrors([$name => [$message]]);

        return $result;
    }

    public function exceptionNotifiesFindingEditorErrors($e, $stopPropagation): void
    {
        if ($e instanceof ValidationException) {
            // Livewire still handles the exception and retains the validation error bag.
            $this->notifyFindingEditorErrors($e->errors(), $e->validator->failed());
        }
    }

    private function notifyFindingEditorErrors(array $errors, array $failedRules = []): void
    {
        if (! $this->editorOpen || $this->publicationOpen || $this->sourcePreviewOpen
            || $this->reviewOpen || $this->publicationSelectionOpen || $this->sharedOpen
            || $this->changeReasonOpen || $this->assignmentReasonOpen || $this->translationKeyOpen
            || $this->workflowReasonOpen) {
            return;
        }

        $targets = [
            'assignmentKey' => 'assignmentKey', 'assignmentReason' => 'assignmentReason',
            'assignment' => 'assignmentKey', 'sourceLanguageValue' => 'sourceLanguageValue',
            'value' => 'value', 'locale' => 'value', 'reason' => 'reason',
            'selectedSubLanguages' => 'selectedSubLanguages',
            'submission' => 'translationValues', 'review' => 'translationValues',
            'publication' => 'translationValues', 'sourcePreview' => 'sourcePreview',
        ];
        $messages = [];
        $target = null;
        foreach ($errors as $field => $fieldMessages) {
            $fieldTarget = str_starts_with($field, 'subLanguageValues.')
                ? $field : ($targets[explode('.', $field)[0]] ?? null);
            if ($fieldTarget === null) {
                continue;
            }
            $target ??= $fieldTarget;
            array_push($messages, ...array_values((array) $fieldMessages));
        }
        if ($messages === []) {
            return;
        }

        $workflow = [
            'assignment' => [__('Key assignment blocked'), __('Go to translation key')],
            'submission' => [__('Submission for review blocked'), __('Go to translation drafts')],
            'review' => [__('Review blocked'), __('Go to translation drafts')],
            'publication' => [__('Publication blocked'), __('Go to translation drafts')],
            'sourcePreview' => [__('Source replacement blocked'), __('Go to source preview')],
        ];
        $firstField = array_key_first($errors);
        $workflowMessage = $workflow[$firstField] ?? null;
        $missing = collect($failedRules)->contains(fn ($rules) => count(array_intersect(
            array_keys($rules), ['Required', 'Present', 'RequiredIf', 'RequiredWith']
        )) > 0);
        $onlyReasonMissing = count($errors) === 1
            && in_array($firstField, ['reason', 'assignmentReason'], true)
            && isset($failedRules[$firstField]['Required']);
        $heading = $workflowMessage[0] ?? ($onlyReasonMissing
            ? __('Required reason is missing')
            : ($missing ? __('Required information is missing') : __('Check the field value')));
        $actionLabel = $workflowMessage[1] ?? __('Go to field');

        $scope = 'finding-editor:'.$this->getId();
        $this->dispatch('toast-show',
            slots: ['heading' => $heading, 'text' => implode("\n", array_unique($messages))],
            dataset: ['variant' => 'danger', 'noticeScope' => $scope, 'replaceNoticeScope' => $scope],
            duration: 0,
            action: [
                'label' => $actionLabel,
                'event' => 'workbench-editor-error-focus',
                'params' => ['component' => $this->getId(), 'finding' => $this->selected, 'target' => $target],
            ],
        );
    }
}
