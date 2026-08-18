{{-- resources/views/components/ui/button/field-edit.blade.php --}}

@props([
    'label' => __('ui.button.edit.edit'),
    'changed' => false,
])

<flux:button
    type="button"
    aria-label="{{ $label }}"
    @class([
        'text-zinc-400/30' => !$changed,
        'text-white' => $changed,
    ])
    icon="{{ $changed ? 'save' : 'pencil' }}"
    size="xs"
    variant="{{ $changed ? 'primary' : 'subtle' }}"
    :color="$changed ? 'green' : null"
    {{ $attributes }}
></flux:button>
