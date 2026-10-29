<flux:button.group>
    <flux:button
        class="shrink-0"
        size="sm"
        variant="outline"
        icon="refresh-ccw"
        wire:click="$refresh"
        wire:loading.attr="disabled"
        wire:target="$refresh"
    />
    <flux:button
        class="shrink-0"
        size="sm"
        variant="outline"
        {{-- icon="refresh-ccw" --}}
        wire:click="$refresh"
        wire:loading.attr="disabled"
        wire:target="$refresh"
    >
        {{ __('Refresh results') }}
    </flux:button>
</flux:button.group>
