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
                    wire:model.live.debounce.300ms="translationKeyId"
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
                    wire:model.live.debounce.300ms="translationSearch"
                    :placeholder="__('Translation key')"
                    :case-sensitive="$textSearchOptions['translationSearch']['case'] ?? false"
                    :whole-word="$textSearchOptions['translationSearch']['word'] ?? false"
                    case-action="toggleTextSearchOption('translationSearch', 'case')"
                    word-action="toggleTextSearchOption('translationSearch', 'word')"
                />
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
                    wire:model.live="translationPerPage"
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
