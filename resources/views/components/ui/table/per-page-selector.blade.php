{{-- resources/views/components/ui/table/per-page-selector.blade.php --}}

@props([
    'id' => 'per-page',
    'name' => 'per-page',
    'label' => __('ui.table.per_page_selector.per_page'),
    'model' => 'perPage',
    'action' => null,
    'options' => [10, 25, 50, 100],
])

{{-- separated label, cause the inside label got another margin/padding to the select field, which looks weird if the label is on the left side of the select field. So we put the label outside of the select field, so it looks better and more consistent with other select fields in the app. --}}
<flux:field>
    <flux:label for="{{ $id }}">
        <x-ui.tooltip.trigger
            :title="__('Items per page')"
            :text="__('Select how many items to show per page in the table.')"
        >
            {{ $label }}
        </x-ui.tooltip.trigger>
    </flux:label>

    <flux:radio.group
        id="{{ $id }}"
        name="{{ $name }}"
        variant="segmented"
        wire:model.live="{{ $model }}"
    >
        @foreach ($options as $option)
            @if ($action)
                <flux:radio
                    value="{{ (string) $option }}"
                    wire:click="{{ $action }}({{ (int) $option }})"
                >
                    {{ $option }}
                </flux:radio>
            @else
                <flux:radio value="{{ (string) $option }}">
                    {{ $option }}
                </flux:radio>
            @endif
        @endforeach
    </flux:radio.group>
</flux:field>
