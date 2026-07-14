{{-- resources/views/components/management/people/create-person/⚡actions.blade.php --}}

<div class="mt-6 flex justify-end gap-3">
    <flux:button
        type="button"
        variant="ghost"
        icon="rotate-ccw"
        wire:click="resetForm"
        wire:target="resetForm,create"
        wire:loading.attr="disabled"
    >
        {{ __('ui.button.reset.reset') }}
    </flux:button>

    <flux:button
        type="submit"
        variant="primary"
        icon="user-plus"
        wire:target="create"
        wire:loading.attr="disabled"
    >
        {{ __('Create person') }}
    </flux:button>
</div>
