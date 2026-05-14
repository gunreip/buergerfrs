{{-- resources/views/components/ui/table/per-page-selector.blade.php --}}

@props([
    'id' => 'per-page',
    'name' => 'per-page',
    'label' => __('Per Page'),
    'model' => 'perPage',
    'options' => [10, 25, 50, 100],
])

{{-- separated label, cause the inside label got another margin/padding to the select field, which looks weird if the label is on the left side of the select field. So we put the label outside of the select field, so it looks better and more consistent with other select fields in the app. --}}
<flux:label for="{{ $id }}">
    {{ $label }}
</flux:label>
<flux:radio.group
    id="{{ $id }}"
    name="{{ $name }}"
    variant="segmented"
    wire:model.live="{{ $model }}"
>
    @foreach ($options as $option)
        <flux:radio value="{{ (string) $option }}">
            {{ $option }}
        </flux:radio>
    @endforeach
</flux:radio.group>
