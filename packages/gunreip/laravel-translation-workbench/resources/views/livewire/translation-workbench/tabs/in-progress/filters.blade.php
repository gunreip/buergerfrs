<x-translation-workbench::ui.common.filter-bar>
    <x-slot name="findingId">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Finding ID')"
                :text="__('Enter the numeric ID of a finding to show work on its linked translation key.')"
            />
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.search />
                </flux:input.group.prefix>
                <flux:input
                    clearable
                    copyable
                    inputmode="numeric"
                    wire:model.live.debounce.300ms="workFindingId"
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
                <flux:input.group.prefix>
                    <flux:icon.search />
                </flux:input.group.prefix>
                <flux:input
                    clearable
                    copyable
                    inputmode="numeric"
                    wire:model.live.debounce.300ms="workKeyId"
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
                    <flux:icon.magnifying-glass class="size-5" />
                </flux:input.group.prefix>
                <x-translation-workbench::ui.common.search-input
                    wire:model.live.debounce.300ms="workSearch"
                    :case-sensitive="$textSearchOptions['workSearch']['case'] ?? false"
                    :whole-word="$textSearchOptions['workSearch']['word'] ?? false"
                    case-action="toggleTextSearchOption('workSearch', 'case')"
                    word-action="toggleTextSearchOption('workSearch', 'word')"
                />
            </flux:input.group>
        </flux:field>
    </x-slot>

    <x-slot name="context">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Edited by')"
                :text="__('Choose your account to see work you have edited. All users includes work edited by anyone.')"
            />
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.user class="size-5" />
                </flux:input.group.prefix>
                <flux:select
                    wire:model.live="workOnlyMine"
                    variant="listbox"
                >
                    @php
                        $currentWorkActor = $workActors[(int) auth()->id()] ?? null;
                        $currentWorkActorName = $currentWorkActor['name'] ?? '' ?: (auth()->user()?->name ?: __('Me'));
                    @endphp
                    <flux:select.option value="1">
                        <div class="flex min-w-0 items-center gap-2">
                            <flux:avatar
                                class="shrink-0"
                                size="xs"
                                circle
                                :src="$currentWorkActor['avatar_url'] ?? null"
                                :name="$currentWorkActorName"
                            />
                            <span class="truncate">{{ $currentWorkActorName }}</span>
                        </div>
                    </flux:select.option>
                    <flux:select.option value="0">{{ __('All users') }}</flux:select.option>
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
                    :aria-label="__('Per page')"
                    variant="listbox"
                    options:class="min-w-48"
                    wire:model.live="workPerPage"
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
