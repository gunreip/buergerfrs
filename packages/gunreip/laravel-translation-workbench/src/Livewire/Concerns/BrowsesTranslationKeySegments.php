<?php

namespace Gunreip\TranslationWorkbench\Livewire\Concerns;

use Gunreip\TranslationWorkbench\Generation\GenerationEditor;
use Gunreip\TranslationWorkbench\Generation\TranslationKeySegments;

trait BrowsesTranslationKeySegments
{
    public string $assignmentKeySearch = '';

    public function updatedAssignmentKey(): void
    {
        $this->assignmentKeySearch = '';
        $this->assignmentSelection = [];
        $this->confirmOpen = false;
    }

    public function assignmentKeyOptionPrefix(): string
    {
        $search = trim($this->assignmentKeySearch);
        $cursor = $search !== '' ? $search : (str_ends_with($this->assignmentKey, '.') ? $this->assignmentKey : '');
        $dot = strrpos($cursor, '.');
        return $dot === false ? '' : substr($cursor, 0, $dot + 1);
    }

    /** Offer one segment at a time inside the existing assignment select. */
    public function assignmentKeyOptions(): array
    {
        $search = trim($this->assignmentKeySearch);
        $children = app(GenerationEditor::class)->assignmentKeyChildren($this->assignmentKeyOptionPrefix());
        return array_values(array_filter($children, static fn ($value) => str_starts_with($value, $search)));
    }

    private function validateAssignedKey(string $key, string $field): void
    {
        try {
            TranslationKeySegments::configured()->assertAllowed($key);
        } catch (\RuntimeException $error) {
            throw \Illuminate\Validation\ValidationException::withMessages([$field => $error->getMessage()]);
        }
    }
}
