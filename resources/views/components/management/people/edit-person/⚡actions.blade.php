{{-- resources/views/components/management/people/edit-person/⚡actions.blade.php --}}

<div class="mt-4 flex items-center justify-end gap-3">
    <x-ui.button.cancel
        :href="route('management.people.index')"
        wire:navigate
    />

    {{-- @if ($editingField !== null) --}}
    <x-ui.button.save
        type="submit"
        icon="check"
        disabled="{{ $editingField === null }}"
    />
    {{-- @endif --}}
</div>
