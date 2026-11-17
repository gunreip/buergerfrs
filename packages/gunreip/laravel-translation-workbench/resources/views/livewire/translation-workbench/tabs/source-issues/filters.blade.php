<x-translation-workbench::ui.common.filter-bar>
    <x-slot name="findingId">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Finding ID')"
                :text="__('Find the issue for this finding or a replacement it blocks.')"
            />
            <flux:input.group>
                <flux:input.group.prefix><flux:icon.search /></flux:input.group.prefix>
                <flux:input clearable copyable inputmode="numeric"
                    wire:model.live.debounce.300ms="sourceIssueFilters.finding"
                    :placeholder="__('Finding ID')"
                />
            </flux:input.group>
        </flux:field>
    </x-slot>

    <x-slot name="keyId">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Blocked key ID')"
                :text="__('Show issues blocking replacements for this translation key.')"
            />
            <flux:input.group>
                <flux:input.group.prefix><flux:icon.search /></flux:input.group.prefix>
                <flux:input clearable copyable inputmode="numeric"
                    wire:model.live.debounce.300ms="sourceIssueFilters.key"
                    :placeholder="__('Blocked key ID')"
                />
            </flux:input.group>
        </flux:field>
    </x-slot>

    <x-slot name="search">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Source path or expression')"
                :text="__('Search the captured expression or source file path. Use Match case or Whole word to narrow the matches.')"
            />
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.eye size="5" />
                </flux:input.group.prefix>
                <x-translation-workbench::ui.common.search-input
                    wire:model.live.debounce.300ms="sourceIssueSearch"
                    :case-sensitive="$textSearchOptions['sourceIssueSearch']['case'] ?? false"
                    :whole-word="$textSearchOptions['sourceIssueSearch']['word'] ?? false"
                    case-action="toggleTextSearchOption('sourceIssueSearch', 'case')"
                    word-action="toggleTextSearchOption('sourceIssueSearch', 'word')"
                />
            </flux:input.group>
        </flux:field>
    </x-slot>

    <x-slot name="context">
        <flux:field>
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Source observation')"
                :text="__('Show findings with the selected observation. A finding can have several observations.')"
            />
            <flux:select variant="listbox" wire:model.live="sourceIssueFilters.observation">
                <flux:select.option value="">{{ __('All observations') }}</flux:select.option>
                @foreach ($observationLabels as $value => $label)
                    <flux:select.option :value="$value">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
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
                    wire:model.live="sourceIssueFilters.perPage"
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
