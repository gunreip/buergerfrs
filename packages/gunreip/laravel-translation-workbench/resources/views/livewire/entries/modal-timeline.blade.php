{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-timeline.blade.php --}}

<flux:modal
    class="w-full max-w-5xl"
    name="translation-workbench-finding-timeline"
    wire:model="timelineModalOpen"
>
    <div class="space-y-4">
        <div class="flex items-start justify-between gap-4">
            <x-ui.headers.card
                :title="__('Timeline')"
                :description="__('Timeline events for the selected finding and its linked key.')"
            />

            @if ($timelineFinding)
                <flux:badge
                    class="mr-8 tabular-nums"
                    variant="subtle"
                >
                    #{{ $timelineFinding->id }}
                </flux:badge>
            @endif
        </div>

        @if ($timelineFinding)
            <flux:callout
                color="sky"
                icon="activity"
            >
                <flux:callout.heading>{{ __('Timeline workflow shell') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('Timeline rows will be listed here in the next step.') }}
                </flux:callout.text>
            </flux:callout>
        @else
            <flux:text class="text-sm text-zinc-500">
                {{ __('No finding selected.') }}
            </flux:text>
        @endif
    </div>
</flux:modal>
