{{-- resources/views/components/management/people/create-person/⚡actions.blade.php --}}

<div class="mt-6 flex justify-end gap-3">
    <x-ui.button.save
        type="submit"
        :label="__('Create person')"
        wire:target="create"
        wire:loading.attr="disabled"
    />
</div>
