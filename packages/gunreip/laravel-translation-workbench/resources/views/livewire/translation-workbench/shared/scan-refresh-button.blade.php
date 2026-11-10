<flux:button.group>
    <flux:button
        class="shrink-0"
        size="sm"
        variant="filled"
        color="green"
        wire:click="$refresh"
        wire:loading.attr="disabled"
        wire:target="$refresh"
    >
        <flux:icon.refresh-ccw size="4" />
    </flux:button>
    <flux:button
        class="shrink-0"
        size="sm"
        variant="filled"
        color="green"
        wire:click="$refresh"
        wire:loading.attr="disabled"
        wire:target="$refresh"
    >
        {{ __('Refresh results') }}
    </flux:button>
</flux:button.group>
