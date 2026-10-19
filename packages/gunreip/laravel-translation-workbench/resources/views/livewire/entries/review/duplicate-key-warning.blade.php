@if ($ownerCount > 1)
    @if ($compactWarning ?? false)
        <flux:callout
            class="p-2!"
            variant="danger"
        >
            <flux:callout.heading>
                <flux:tooltip toggleable>
                    <flux:button
                        class="max-w-full whitespace-normal text-left"
                        type="button"
                        size="xs"
                        color="text-error"
                        variant="ghost"
                        icon="exclamation-triangle"
                    >
                        {{ __('Key conflict (:count)', ['count' => $ownerCount]) }}
                    </flux:button>
                    <flux:tooltip.content class="max-w-sm whitespace-normal">
                        <span class="text-error text-sm">
                            {{ __('This translation key has :count active key records. Multiple findings linked to one key record are valid. Review the duplicate key records before publishing; no record is selected automatically.', ['count' => $ownerCount]) }}
                        </span>
                        @if ($ownerIds !== [])
                            <span class="font-mono">{{ __('Key IDs') }}: {{ implode(', ', $ownerIds) }}</span>
                        @endif
                    </flux:tooltip.content>
                </flux:tooltip>
            </flux:callout.heading>
        </flux:callout>
    @else
        <flux:callout
            variant="danger"
            icon="exclamation-triangle"
        >
            <flux:callout.heading>{{ __('Multiple active key records') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('This translation key has :count active key records. Multiple findings linked to one key record are valid. Review the duplicate key records before publishing; no record is selected automatically.', ['count' => $ownerCount]) }}
                @if ($ownerIds !== [])
                    <span class="font-mono">{{ __('Key IDs') }}: {{ implode(', ', $ownerIds) }}</span>
                @endif
            </flux:callout.text>
        </flux:callout>
    @endif
@endif
