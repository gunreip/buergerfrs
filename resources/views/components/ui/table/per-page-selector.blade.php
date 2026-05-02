{{-- resources/views/components/ui/table/per-page-selector.blade.php --}}

@props([
    'id' => 'per-page',
    'name' => 'per-page',
    'label' => __('Per Page'),
    'model' => 'perPage',
    'options' => [10, 25, 50, 100],
])

<flux:radio.group
    id="{{ $id }}"
    name="{{ $name }}"
    label="{{ $label }}"
    variant="segmented"
    wire:model.live="{{ $model }}"
>
    @foreach ($options as $option)
        <flux:radio value="{{ (string) $option }}">
            {{ $option }}
        </flux:radio>
    @endforeach
</flux:radio.group>
