<?php

namespace Gunreip\TranslationWorkbench\Livewire\Concerns;

use Livewire\Attributes\Locked;

trait HasTextSearchOptions
{
    #[Locked]
    public array $textSearchOptions = [];

    abstract protected function textSearchPages(): array;

    public function toggleTextSearchOption(string $field, string $option): void
    {
        abort_unless(isset($this->textSearchPages()[$field]) && in_array($option, ['case', 'word'], true), 422);
        $this->textSearchOptions[$field][$option] = ! ($this->textSearchOptions[$field][$option] ?? false);
        $this->resetPage($this->textSearchPages()[$field]);
    }

    protected function resetEmptyTextSearch(string $field): void
    {
        if (trim($this->{$field}) === '') {
            unset($this->textSearchOptions[$field]);
        }
    }
}
