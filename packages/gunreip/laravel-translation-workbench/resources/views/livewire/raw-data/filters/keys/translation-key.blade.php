{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/filters/keys/translation-key.blade.php --}}

<flux:field class="{{ $fieldClass ?? '' }}">
    <flux:label>{{ __('Translation key') }}</flux:label>
    <flux:input.group>
        <flux:input.group.prefix>
            <flux:icon.key />
        </flux:input.group.prefix>
        <flux:select
            wire:model.live="keysTranslationKey"
            variant="listbox"
            searchable
            :disabled="$keysNamespace === 'all' || $keysGroup === 'all'"
        >
            <x-ui.input.select-option
                value="all"
                icon="key"
            >
                {{ $keysNamespace === 'all' || $keysGroup === 'all' ? __('Select namespace and group first') : __('All translation keys') }}
            </x-ui.input.select-option>

            @foreach ($keyOptions['translation_keys'] ?? [] as $translationKey)
                <x-ui.input.select-option
                    value="{{ $translationKey }}"
                    icon="key"
                >
                    {{ $translationKey }}
                </x-ui.input.select-option>
            @endforeach
        </flux:select>
    </flux:input.group>
</flux:field>
