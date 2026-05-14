{{-- resources/views/components/management/people/create-person/⚡actions.blade.php --}}

<div class="mt-6 flex justify-end gap-3">
    {{--
    TODO: x-ui.button.cancel noch implementieren
    --}}
    <x-ui.button.reset
        type="button"
        :label="__('Reset')"
        wire:click="resetForm"
        wire:target="resetForm,create"
        wire:loading.attr="disabled"
    />

    <x-ui.button.save
        type="submit"
        :label="__('Create person')"
        wire:target="create"
        wire:loading.attr="disabled"
    />
</div>
