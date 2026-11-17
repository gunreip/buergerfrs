<x-translation-workbench::ui.common.filter-bar>
    <x-slot name="findingId">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Finding ID')"
                :text="__(
                    'Enter the exact numeric ID of a source occurrence. Clear the field to include other findings.',
                )"
            />
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.search />
                </flux:input.group.prefix>
                <flux:input
                    clearable
                    copyable
                    wire:model.live.debounce.500ms="findingId"
                    placeholder="Finding ID"
                />
            </flux:input.group>
        </flux:field>
    </x-slot>

    <x-slot name="search">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Source, expression or translation key')"
                :text="__(
                    'Search the source file path, captured expression or assigned translation key. Use Match case, Whole word or Match entire literal to narrow the matches.',
                )"
            />
            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.search />
                </flux:input.group.prefix>
                <x-translation-workbench::ui.common.search-input
                    wire:model.live.debounce.500ms="search"
                    :placeholder="__('Source, expression or translation key')"
                    :case-sensitive="$searchCaseSensitive"
                    :whole-word="$searchWholeWord"
                    :whole-literal="$searchWholeLiteral"
                    literal-action="toggleSearchWholeLiteral"
                    case-action="toggleSearchCaseSensitive"
                    word-action="toggleSearchWholeWord"
                />
            </flux:input.group>
        </flux:field>
    </x-slot>

    <x-slot name="context">
        <flux:field class="min-w-0">
            <x-translation-workbench::ui.common.filter-label
                :heading="__('Status')"
                :text="[
                    __(
                        'Open shows active findings with work remaining for the active target language. Active also includes completed findings.',
                    ),
                    __(
                        'Archived shows retired findings. All shows individual findings and includes retired findings when the Archived toggle is on.',
                    ),
                    __(
                        'Active compact and All compact group active findings with the same literal into one row. Archived findings remain separate.',
                    ),
                ]"
            />
            <flux:input.group class="min-w-0">
                <flux:input.group.prefix>
                    <flux:icon.list-check />
                </flux:input.group.prefix>
                <flux:select
                    variant="listbox"
                    wire:model.live="status"
                >
                    <flux:select.option
                        value="open"
                        icon="list-check"
                        label="{{ __('Open') }}"
                    />
                    <flux:select.option
                        value="active"
                        icon="activity"
                        label="{{ __('Active') }}"
                    />
                    <flux:select.option
                        value="active_compact"
                        icon="list"
                        label="{{ __('Active compact') }}"
                    />
                    <flux:select.option
                        value="obsolete"
                        icon="ghost"
                        label="{{ __('Archived') }}"
                    />
                    <flux:select.option
                        value="all_compact"
                        icon="list"
                        label="{{ __('All compact') }}"
                    />
                    <flux:select.option
                        value="all"
                        icon="graph-hub"
                        label="{{ __('All') }}"
                    />
                </flux:select>
            </flux:input.group>
        </flux:field>
    </x-slot>

    <x-slot name="toggles">
        <flux:field class="min-w-0">
            <div class="flex items-center gap-2">
                <flux:tooltip class="hover:cursor-pointer">
                    <div class="inline-flex">
                        <flux:toggle
                            :aria-label="__('Archived')"
                            wire:click="toggleArchived"
                            :checked="$includeArchived || $status === 'obsolete'"
                            :disabled="$status === 'obsolete'"
                        >
                            <x-slot:icon>
                                <flux:icon.lightbulb
                                    class="group-data-checked:block hidden size-6"
                                    variant="outline"
                                    color="green"
                                />
                                <flux:icon.lightbulb-off
                                    class="group-data-checked:hidden size-6"
                                    variant="outline"
                                    color="red"
                                />
                            </x-slot:icon>
                        </flux:toggle>
                    </div>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Archived')"
                        :text="__(
                            'Include archived findings in All views. Active views only show active findings. Selecting Archived always shows archived findings.',
                        )"
                    />
                </flux:tooltip>

                <flux:tooltip class="hover:cursor-pointer">
                    <div class="inline-flex">
                        <flux:toggle
                            :aria-label="__('Not assigned')"
                            wire:click="toggleUnassigned"
                            :checked="$unassignedOnly"
                        >
                            <x-slot:icon>
                                <flux:icon.lightbulb
                                    class="group-data-checked:block hidden size-6"
                                    variant="outline"
                                    color="green"
                                />
                                <flux:icon.lightbulb-off
                                    class="group-data-checked:hidden size-6"
                                    variant="outline"
                                    color="red"
                                />
                            </x-slot:icon>
                        </flux:toggle>
                    </div>
                    <x-translation-workbench::ui.common.tooltip-content
                        :heading="__('Not assigned')"
                        :text="__(
                            'Only show findings without a current key assignment. This filter combines with the selected status and search.',
                        )"
                    />
                </flux:tooltip>
            </div>
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
