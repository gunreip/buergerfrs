<?php

namespace Gunreip\TranslationWorkbench\Livewire\Concerns;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorLocales;
use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Livewire\Attributes\Locked;
use RuntimeException;

trait SelectsWorkflowLanguages
{
    public array $reviewLocales = [];
    public array $publicationLocales = [];
    public bool $publicationSelectionOpen = false;

    #[Locked]
    public array $reviewChoices = [];

    #[Locked]
    public array $publicationChoices = [];

    /** Include English and all saved, enabled variants, even after reopening the editor. */
    private function savedLanguageChoices(): array
    {
        $editor = app(GenerationEditor::class);
        $states = $editor->workflowStates([$this->selected])[$this->selected] ?? [];
        $allowed = ['en', $this->loadedLocale, ...app(TranslationWorkbenchEditorLocales::class)->resolve()['sub']];
        $codes = array_unique(['en', $this->loadedLocale, ...$this->selectedSubLanguages, ...array_column($states, 'locale')]);
        $choices = [];
        foreach ($codes as $code) {
            if (! in_array($code, $allowed, true)) {
                continue;
            }
            // Do not invent a source revision when only captured literal text is available.
            if ($code === 'en' && $code !== $this->loadedLocale && empty($this->context['sourceRevision'])) {
                $captured = (string) ($this->context['sourceText'] ?? $this->context['finding']['raw_literal'] ?? $this->context['finding']['suggested_literal'] ?? '');
                if ($this->sourceLanguageValue !== $captured) {
                    throw new RuntimeException(__('Save or discard all language draft changes before continuing.'));
                }
                continue;
            }
            $current = $code === $this->loadedLocale ? $this->context : $editor->inspect($this->selected, $code);
            if (empty($current['revision']) || empty($current['review'])) {
                if (in_array($code, $this->selectedSubLanguages, true) && ($this->subLanguageValues[$code] ?? '') !== '') {
                    throw new RuntimeException(__('Save or discard all language draft changes before continuing.'));
                }
                continue;
            }
            if (($current['finding']['key_id'] ?? null) !== ($this->context['finding']['key_id'] ?? null)
                || ($current['finding']['link_id'] ?? null) !== ($this->context['finding']['link_id'] ?? null)) {
                throw new RuntimeException(__('The key assignment changed. Reopen the finding.'));
            }
            $choice = [
                'locale' => $code, 'revision' => (int) $current['revision']['id'],
                'value' => $current['revision']['value'], 'fingerprint' => $current['review']['fingerprint'],
                'approved' => (bool) ($current['review']['approved'] ?? false),
            ];
            $choice['unchanged'] = $this->languageDraftMatches($code, $choice);
            $choices[$code] = $choice;
        }

        return $choices;
    }

    private function languageDraftMatches(string $code, array $choice): bool
    {
        if ($code === $this->loadedLocale) {
            return $this->value === $choice['value'] && (int) ($this->context['revision']['id'] ?? 0) === $choice['revision'];
        }
        if ($code === 'en') {
            return $this->sourceLanguageValue === $choice['value'] && (int) ($this->context['sourceRevision'] ?? 0) === $choice['revision'];
        }
        if (in_array($code, $this->selectedSubLanguages, true)) {
            return ($this->subLanguageValues[$code] ?? null) === $choice['value']
                && (int) ($this->subLanguageRevisions[$code] ?? 0) === $choice['revision'];
        }

        return true;
    }

    private function selectedLanguageRevisions(array $locales, array $choices, bool $requireApproval): array
    {
        if ($locales === [] || count($locales) !== count(array_unique($locales))) {
            throw new RuntimeException(__('Select at least one language.'));
        }
        $selected = [];
        foreach ($locales as $code) {
            if (! is_string($code) || ! isset($choices[$code])) {
                throw new RuntimeException(__('Invalid language selection.'));
            }
            $choice = $choices[$code];
            if (! $choice['unchanged'] || ! $this->languageDraftMatches($code, $choice)
                || ($requireApproval && ! $choice['approved'])) {
                throw new RuntimeException(__('Save and review the selected language revisions before continuing.'));
            }
            $selected[$choice['revision']] = $choice['fingerprint'];
        }

        return $selected;
    }

    private function ensureLanguageDraftsSaved(): void
    {
        foreach ($this->savedLanguageChoices() as $choice) {
            if (! $choice['unchanged']) {
                throw new RuntimeException(__('Save or discard all language draft changes before continuing.'));
            }
        }
    }

    public function openPublicationSelection(): void
    {
        $this->resetValidation('publication');
        $this->publicationSelectionOpen = false;
        if (! $this->editorOpen || ($this->context['finding']['status'] ?? '') !== 'active'
            || $this->locale !== $this->loadedLocale) {
            return;
        }
        try {
            $this->publicationChoices = $this->savedLanguageChoices();
            $this->publicationLocales = array_keys(array_filter($this->publicationChoices, fn ($choice) => $choice['approved'] && $choice['unchanged']));
            $this->publicationSelectionOpen = true;
        } catch (RuntimeException $error) {
            $this->addError('publication', $error->getMessage());
        }
    }
}
