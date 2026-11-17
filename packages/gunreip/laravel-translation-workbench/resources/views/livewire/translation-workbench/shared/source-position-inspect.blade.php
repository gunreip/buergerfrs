<div class="mt-2 flex flex-wrap justify-end gap-2">
    @foreach ($occupants as $occupant)
        <flux:button size="sm" variant="outline" wire:click="inspectSourcePositionOccupant({{ $occupant['id'] }})">
            {{ __('Inspect finding #:id at :position', ['id' => $occupant['id'], 'position' => $coordinate]) }}
        </flux:button>
    @endforeach
</div>
