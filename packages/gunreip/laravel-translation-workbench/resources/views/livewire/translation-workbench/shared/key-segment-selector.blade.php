@php
    $segmentOptions = $this->assignmentKeyOptions($keys);
@endphp

<flux:input.group>
    <flux:input.group.prefix>
        <flux:icon.key />
    </flux:input.group.prefix>
    <flux:select
        wire:model.live="assignmentKey"
        variant="listbox"
        searchable
        clearable
        clear="none"
        filter="manual"
        :placeholder="__('Choose a translation key')"
        x-data="{ optionsLoading: false, optionsRequest: 0 }"
        x-on:open="
            optionsLoading = true;
            const request = ++optionsRequest;
            const input = $el.querySelector('[data-translation-key-input]');
            input.value = $wire.assignmentKey;
            try {
                await $wire.$set('assignmentKeySearch', input.value);
                await $nextTick();
            } finally {
                if (request === optionsRequest) {
                    optionsLoading = false;
                    if ($el.hasAttribute('data-open')) {
                        input.focus();
                        input.setSelectionRange(input.value.length, input.value.length);
                    }
                }
            }
        "
    >
        <x-slot name="trigger">
            <flux:select.button clearable>
                <span
                    class="min-w-0 flex-1 truncate text-start text-zinc-700 dark:text-zinc-300 data-flux-select-placeholder:text-zinc-400 dark:data-flux-select-placeholder:text-zinc-400"
                    x-bind:data-selected="$wire.assignmentKey !== '' ? '' : null"
                    x-bind:data-flux-select-placeholder="$wire.assignmentKey === '' ? '' : null"
                    x-text="$wire.assignmentKey || @js(__('Choose a translation key'))"
                >
                    {{ $assignmentKey !== '' ? $assignmentKey : __('Choose a translation key') }}
                </span>
            </flux:select.button>
        </x-slot>
        <x-slot name="search">
            <flux:select.search
                data-translation-key-input
                wire:model.live.debounce.200ms="assignmentKeySearch"
                autocapitalize="off"
                :spellcheck="false"
            />
            <div
                x-cloak
                x-show="optionsLoading"
                class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white dark:bg-zinc-700"
                role="status"
                aria-live="polite"
            >
                <flux:icon.loading class="size-5" />
                <span class="sr-only">{{ __('Loading...') }}</span>
            </div>
        </x-slot>
        <flux:select.option.create
            x-on:click="$wire.openTranslationKey($el.closest('ui-select').querySelector('[data-translation-key-input]').value || $wire.assignmentKey)"
        >
            {{ __('Create a translation key') }}
        </flux:select.option.create>
        @foreach ($segmentOptions as $segment)
            @if ($segment !== $assignmentKey)
                <flux:select.option :value="$segment">{{ $segment }}</flux:select.option>
            @endif
        @endforeach
    </flux:select>
</flux:input.group>
