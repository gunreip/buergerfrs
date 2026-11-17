<x-translation-workbench::ui.common.filter-bar>
    <x-slot name="search">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Search source or expression')"
                :text="__('Search the source file path or captured expression. Use Match case or Whole word to narrow the matches.')"
            />
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.search />
                </flux:input.group.prefix>
                <x-translation-workbench::ui.common.search-input
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search source or expression') }}"
                    :case-sensitive="$textSearchOptions['search']['case'] ?? false"
                    :whole-word="$textSearchOptions['search']['word'] ?? false"
                    case-action="toggleTextSearchOption('search', 'case')"
                    word-action="toggleTextSearchOption('search', 'word')"
                />
            </flux:input.group>
        </flux:field>
    </x-slot>

    <x-slot name="context">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Review status')"
                :text="__('Open decisions shows cases awaiting a source usage decision. All cases including decisions also shows cases with a saved decision.')"
            />
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.list-check />
                </flux:input.group.prefix>
                <flux:select
                    clearable
                    variant="listbox"
                    wire:model.live="filter"
                >
                    <flux:select.option
                        value="open"
                        selected
                        icon="scale"
                        label="{{ __('Open decisions') }}"
                    />
                    <flux:select.option
                        value="all"
                        icon="gavel"
                        label="{{ __('All cases including decisions') }}"
                    />
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
                    {{-- size="sm" --}}
                    variant="listbox"
                    options:class="min-w-48"
                    wire:model.live="perPage"
                    :aria-label="__('Per page')"
                >
                    @foreach ([10, 25, 50, 100] as $pageSize)
                        <flux:select.option
                            :value="$pageSize"
                            icon="list"
                        >
                            {{ $pageSize }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </div>
    </x-slot>
</x-translation-workbench::ui.common.filter-bar>
