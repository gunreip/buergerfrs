<?php

namespace Gunreip\TranslationWorkbench\Livewire\Concerns;

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

    /** Offer one segment at a time inside the existing assignment select. */
    public function assignmentKeyOptions(array $keys): array
    {
        $search = trim($this->assignmentKeySearch);
        $cursor = $search !== '' ? $search : (str_ends_with($this->assignmentKey, '.') ? $this->assignmentKey : '');
        $dot = strrpos($cursor, '.');
        $prefix = $dot === false ? '' : substr($cursor, 0, $dot);
        $options = [];
        foreach (TranslationKeySegments::configured()->options($keys, $prefix) as $segment) {
            $name = $prefix === '' ? $segment : $prefix.'.'.$segment;
            if ($search !== '' && !str_starts_with($name, $search)) {
                continue;
            }
            $complete = collect($keys)->contains(fn ($key) => ($key['namespace'] ?? '') === '' && $key['full_name'] === $name);
            $options[] = $complete ? $name : $name.'.';
        }

        return $options;
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
