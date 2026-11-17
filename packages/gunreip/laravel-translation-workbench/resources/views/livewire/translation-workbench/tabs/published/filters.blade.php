<x-translation-workbench::ui.common.filter-bar>
    <x-slot name="keyId">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Key ID')"
                :text="__('Enter the exact numeric ID of the translation key. Findings that share a key use the same Key ID.')"
            />
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.search />
                </flux:input.group.prefix>
                <flux:input
                    clearable
                    copyable
                    inputmode="numeric"
                    wire:model.live.debounce.300ms="publishedKeyId"
                    :placeholder="__('Key ID')"
                />
            </flux:input.group>
        </flux:field>
    </x-slot>

    <x-slot name="search">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Translation key')"
                :text="__('Enter a full translation key or part of its name. Use Whole word to match a complete segment, or Match case to distinguish uppercase and lowercase.')"
            />
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.search />
                </flux:input.group.prefix>
                <x-translation-workbench::ui.common.search-input
                    wire:model.live.debounce.300ms="publishedSearch"
                    :placeholder="__('Translation key')"
                    :case-sensitive="$textSearchOptions['publishedSearch']['case'] ?? false"
                    :whole-word="$textSearchOptions['publishedSearch']['word'] ?? false"
                    case-action="toggleTextSearchOption('publishedSearch', 'case')"
                    word-action="toggleTextSearchOption('publishedSearch', 'word')"
                />
            </flux:input.group>
        </flux:field>
    </x-slot>

    <x-slot name="context">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Language')"
                :text="__('Select a locale to show only revisions for that language. Regional variants such as de-AT are separate options. All languages clears the filter.')"
            />
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.search />
                </flux:input.group.prefix>
                <flux:select
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="publishedLanguage"
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
    </x-slot>

    <x-slot name="perPage">
        <div class="flex min-w-0 items-end justify-end">
            <flux:input.group>
                <x-translation-workbench::ui.common.per-page-prefix />
                <flux:select
                    class="w-32"
                    variant="listbox"
                    options:class="min-w-48"
                    wire:model.live="publishedPerPage"
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
    </x-slot>
</x-translation-workbench::ui.common.filter-bar>
