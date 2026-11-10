<div class="my-4 grid items-end gap-4 md:grid-cols-4">
    <flux:field>
        <flux:tooltip>
            <flux:label>
                <flux:callout.heading>
                    {{ __('Key ID') }}
                </flux:callout.heading>
            </flux:label>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('Key ID')"
                :text="__('Show publication tasks for this exact translation key ID. Enter the database ID of the key, not a finding or revision ID. Clear the field to show all keys.')"
            />
        </flux:tooltip>
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:icon.search />
            </flux:input.group.prefix>
            <flux:input
                clearable
                copyable
                inputmode="numeric"
                wire:model.live.debounce.300ms="publicationKeyId"
                :placeholder="__('Key ID')"
            />
        </flux:input.group>
    </flux:field>
    <flux:field>
        <flux:tooltip>
            <flux:label>
                <flux:callout.heading>
                    {{ __('Translation key') }}
                </flux:callout.heading>
            </flux:label>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('Translation key')"
                :text="__('Find publication tasks by translation key name. Enter a complete key or part of its name to narrow the list.')"
            />
        </flux:tooltip>
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:icon.search />
            </flux:input.group.prefix>
            <flux:input
                clearable
                copyable
                wire:model.live.debounce.300ms="publicationSearch"
                :placeholder="__('Translation key')"
            />
        </flux:input.group>
    </flux:field>
    <flux:field>
        <flux:tooltip>
            <flux:label>
                <flux:callout.heading>
                    {{ __('Language') }}
                </flux:callout.heading>
            </flux:label>
            <x-translation-workbench::ui.common.tooltip-content
                :heading="__('Language')"
                :text="__('Show approved revisions waiting for publication in the selected language. A regional variant is a separate language entry. Choose All languages to remove this filter.')"
            />
        </flux:tooltip>
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:icon.search />
            </flux:input.group.prefix>
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
            <flux:input.group.prefix>
                <flux:callout.text>{{ __('Per page') }}</flux:callout.text>
            </flux:input.group.prefix>
            <flux:select
                class="w-32"
                variant="listbox"
                options:class="min-w-48"
                wire:model.live="publicationPerPage"
                :aria-label="__('Per page')"
            >
                @foreach ([10, 25, 50, 100] as $pageSize)
                    <flux:select.option
                        :value="$pageSize"
                        icon="list"
                    >{{ $pageSize }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:input.group>
    </div>
</div>
