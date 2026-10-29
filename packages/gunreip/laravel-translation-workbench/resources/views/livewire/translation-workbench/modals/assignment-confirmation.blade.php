<flux:modal
    class="max-w-md"
    name="generation-assignment-confirmation"
    wire:model="confirmOpen"
>
    <div class="space-y-4">
        <flux:heading size="lg">{{ __('Confirm key assignment') }}</flux:heading>
        <flux:text>{{ __('Finding') }} #{{ $selected }} →
            {{ $assignmentSelection['name'] ?? '' }}</flux:text>
        <flux:text>
            {{ ($assignmentSelection['key'] ?? null) === null
                ? __('Create a new key and link this finding.')
                : __('Link this finding to existing key #:id.', ['id' => $assignmentSelection['key']]) }}
        </flux:text>
        <flux:text>
            {{ __('Only this finding is assigned. Its source capture and other findings remain unchanged.') }}
        </flux:text>
        <flux:error name="assignment" />
        <flux:button
            wire:click="assign"
            variant="primary"
        >{{ __('Confirm assignment') }}</flux:button>
    </div>
</flux:modal>
