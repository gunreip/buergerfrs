{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-edit.blade.php --}}

<flux:modal
    class="w-full max-w-5xl"
    name="translation-workbench-finding-edit"
    wire:model="editModalOpen"
>
    <div class="space-y-4">
        <div class="flex items-start justify-between gap-4">
            <x-ui.headers.card
                :title="__('Edit translation values')"
                :description="__('Edit static translation values for the selected finding key.')"
            />

            @if ($editFinding)
                <flux:badge
                    class="mr-8 tabular-nums"
                    variant="subtle"
                >
                    #{{ $editFinding->id }}
                </flux:badge>
            @endif
        </div>

        @if ($editFinding)
            <flux:callout
                color="{{ $editFinding->translation_key ? 'green' : 'amber' }}"
                icon="square-pen"
            >
                <flux:callout.heading>{{ __('Edit workflow shell') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ $editFinding->translation_key ? __('Ready for translation value editing.') : __('Set or link a translation key in Review before editing values.') }}
                </flux:callout.text>
            </flux:callout>
        @else
            <flux:text class="text-sm text-zinc-500">
                {{ __('No finding selected.') }}
            </flux:text>
        @endif
    </div>
</flux:modal>

