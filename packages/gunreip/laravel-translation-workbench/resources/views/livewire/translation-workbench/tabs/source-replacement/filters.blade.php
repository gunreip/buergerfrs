<x-translation-workbench::ui.common.filter-bar>
    <x-slot name="findingId">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Finding ID')"
                :text="__('Enter the exact numeric ID of a source occurrence. Clear the field to include other findings.')"
            />
            <flux:input.group>
                <flux:input.group.prefix><flux:icon.search /></flux:input.group.prefix>
                <flux:input clearable copyable inputmode="numeric"
                    wire:model.live.debounce.300ms="sourceReplacementFindingId"
                    :placeholder="__('Finding ID')"
                />
            </flux:input.group>
        </flux:field>
    </x-slot>

    <x-slot name="keyId">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Key ID')"
                :text="__('Enter the exact numeric ID of the translation key. Findings that share a key use the same Key ID.')"
            />
            <flux:input.group>
                <flux:input.group.prefix><flux:icon.search /></flux:input.group.prefix>
                <flux:input clearable copyable inputmode="numeric"
                    wire:model.live.debounce.300ms="sourceReplacementKeyId"
                    :placeholder="__('Key ID')"
                />
            </flux:input.group>
        </flux:field>
    </x-slot>

    <x-slot name="search">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Translation key or source path')"
                :text="__('Search for a translation key or part of a source file path. Use Match case or Whole word to narrow the matches.')"
            />
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.eye size="5" />
                </flux:input.group.prefix>
                <x-translation-workbench::ui.common.search-input
                    wire:model.live.debounce.300ms="sourceReplacementSearch"
                    :case-sensitive="$textSearchOptions['sourceReplacementSearch']['case'] ?? false"
                    :whole-word="$textSearchOptions['sourceReplacementSearch']['word'] ?? false"
                    case-action="toggleTextSearchOption('sourceReplacementSearch', 'case')"
                    word-action="toggleTextSearchOption('sourceReplacementSearch', 'word')"
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
                    :aria-label="__('Per page')"
                    variant="listbox"
                    options:class="min-w-48"
                    wire:model.live="sourceReplacementPerPage"
                >
                    @foreach ([10, 25, 50, 100] as $size)
                        <flux:select.option
                            :value="$size"
                            icon="list"
                        >
                            {{ $size }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </div>
    </x-slot>
</x-translation-workbench::ui.common.filter-bar>
