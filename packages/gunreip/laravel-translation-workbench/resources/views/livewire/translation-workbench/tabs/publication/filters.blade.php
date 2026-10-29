<div class="my-4 grid items-end gap-4 md:grid-cols-4">
    <flux:field>
        <flux:label><flux:callout.heading>{{ __('Key ID') }}</flux:callout.heading></flux:label>
        <flux:input.group>
            <flux:input.group.prefix><flux:icon.search /></flux:input.group.prefix>
            <flux:input
                clearable
                copyable inputmode="numeric"
                wire:model.live.debounce.300ms="publicationKeyId"
                :placeholder="__('Key ID')"
            />
        </flux:input.group>
    </flux:field>
    <flux:field>
        <flux:label><flux:callout.heading>{{ __('Translation key') }}</flux:callout.heading></flux:label>
        <flux:input.group>
            <flux:input.group.prefix><flux:icon.search /></flux:input.group.prefix>
            <flux:input
                clearable
                copyable
                wire:model.live.debounce.300ms="publicationSearch"
                :placeholder="__('Translation key')"
            />
        </flux:input.group>
    </flux:field>
    <flux:field>
        <flux:label><flux:callout.heading>{{ __('Language') }}</flux:callout.heading></flux:label>
        <flux:input.group>
            <flux:input.group.prefix><flux:icon.search /></flux:input.group.prefix>
            <flux:select
                variant="listbox"
                searchable
                clearable
                wire:model.live="publicationLanguage"
                :placeholder="__('All languages')"
            >
                <flux:select.option value="">{{ __('All languages') }}</flux:select.option>
                @foreach ($filterLanguages as $language)
                    <flux:select.option :value="$language">
                        <span class="inline-flex items-center gap-2">
                            <flux:flag
                                :country="$translationFlagCountries[$language] ?? null"
                                size="xs"
                                circle
                            />
                            <span class="font-mono">{{ $language }}</span>
                        </span>
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:input.group>
    </flux:field>
    <div class="ml-auto flex items-end gap-2">
        <flux:input.group>
            <flux:input.group.prefix><flux:callout.text>{{ __('Per page') }}</flux:callout.text></flux:input.group.prefix>
            <flux:select
                class="w-32"
                variant="listbox"
                options:class="min-w-48"
                wire:model.live="publicationPerPage"
                :aria-label="__('Per page')"
            >
                @foreach ([10, 25, 50, 100] as $pageSize)
                    <flux:select.option :value="$pageSize" icon="list">{{ $pageSize }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:input.group>
    </div>
</div>
