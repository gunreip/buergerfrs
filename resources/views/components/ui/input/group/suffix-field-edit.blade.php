{{-- resources/views/components/ui/input/group/suffix-field-edit.blade.php --}}

@props(['field', 'title', 'text', 'label' => __('admin.translation_list.modal.edit'), 'changed' => false])

@if ($changed)
<flux:input.group.suffix
    data-buergerfrs-field-edit-suffix
    data-buergerfrs-field-edit-suffix-changed
>
    <x-ui.tooltip.trigger
        :title="$title"
        :text="$text"
    >
        <x-ui.button.field-edit
            :changed="$changed"
            wire:click="editField('{{ $field }}')"
            :label="$label"
        />
    </x-ui.tooltip.trigger>
</flux:input.group.suffix>
@else
<flux:input.group.suffix data-buergerfrs-field-edit-suffix>
    <x-ui.tooltip.trigger
        :title="$title"
        :text="$text"
    >
        <x-ui.button.field-edit
            :changed="$changed"
            wire:click="editField('{{ $field }}')"
            :label="$label"
        />
    </x-ui.tooltip.trigger>
</flux:input.group.suffix>
@endif
