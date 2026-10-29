<?php

namespace Gunreip\TranslationWorkbench\Livewire\Concerns;

use Gunreip\TranslationWorkbench\Foundation\TranslationWorkbenchEditorLocales;
use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;

trait HasReasonLanguages
{
    public string $newChangeReasonLocale = 'en';
    public string $newAssignmentReasonLocale = 'en';

    #[Locked]
    public array $createdReasonLocales = [];

    public function reasonTargetLocale(): string
    {
        return app(TranslationWorkbenchEditorLocales::class)->resolve()['active'];
    }

    private function selectedReasonLocale(string $kind, string $text): ?string
    {
        $text = trim($text);
        $key = hash('sha256', $text);
        if (isset($this->createdReasonLocales[$kind][$key])) {
            return $this->createdReasonLocales[$kind][$key];
        }

        return app(GenerationEditor::class)->reasonLocales($kind)[$text] ?? null;
    }

    private function reasonLocaleArguments(string $kind, string $text): array
    {
        $locale = $this->selectedReasonLocale($kind, $text);

        return $locale === null ? [] : ['reasonLocale' => $locale];
    }

    private function rememberReasonLocale(string $kind, string $text, string $property): void
    {
        $this->validate([$property => ['required', Rule::in(array_unique(['en', $this->reasonTargetLocale()]))]]);
        $this->createdReasonLocales[$kind][hash('sha256', trim($text))] = $this->{$property};
    }
}
