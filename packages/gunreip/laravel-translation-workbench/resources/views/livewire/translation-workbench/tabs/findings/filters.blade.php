<div class="my-4 grid gap-3 md:grid-cols-5">

    {{-- Search Field Finding ID --}}
    <flux:field>
        <flux:label>
            <flux:callout.heading>
                {{ __('Finding ID') }}
            </flux:callout.heading>
        </flux:label>
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:icon.search />
            </flux:input.group.prefix>
            <flux:input
                clearable
                copyable
                wire:model.live.debounce.300ms="findingId"
                placeholder="Finding ID"
            />
        </flux:input.group>
    </flux:field>
    {{-- Search Field Source, Expression Or Translation Key --}}
    <flux:field>
        <flux:label>
            <flux:callout.heading>
                {{ __('Source, expression or translation key') }}
            </flux:callout.heading>
        </flux:label>
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:icon.search />
            </flux:input.group.prefix>
            <flux:input
                class="md:col-span-2"
                clearable
                copyable
                wire:model.live.debounce.300ms="search"
                placeholder="Source, expression or translation key"
            />
        </flux:input.group>
    </flux:field>
    {{-- Select Field Status --}}
    <flux:field>
        <flux:label>
            <flux:callout.heading>
                {{ __('Status') }}
            </flux:callout.heading>
        </flux:label>
        <flux:input.group class="min-w-40 flex-1">
            <flux:input.group.prefix>
                <flux:icon.list-check />
            </flux:input.group.prefix>
            <flux:select
                clearable
                variant="listbox"
                wire:model.live="status"
            >
                <flux:select.option
                    value="active"
                    selected
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

    <flux:field>
        <div class="flex flex-wrap items-end gap-3">
            <flux:toggle
                wire:click="toggleArchived"
                :checked="$includeArchived || $status === 'obsolete'"
                :disabled="$status === 'obsolete'"
                :on-label="__('Archived')"
                :off-label="__('Archived')"
                :tooltip="__('Include archived findings in All views. Active views only show active findings. Selecting Archived always shows archived findings.')"
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

            <flux:toggle
                wire:click="toggleUnassigned"
                :checked="$unassignedOnly"
                :on-label="__('Not assigned')"
                :off-label="__('Not assigned')"
                :tooltip="__('Only show findings without a current key assignment. This filter combines with the selected status and search.')"
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
    </flux:field>

    <div class="ml-auto flex items-end gap-2">
        <flux:input.group>
            <flux:input.group.prefix>
                <flux:callout.text>
                    {{ __('Per page') }}
                </flux:callout.text>
            </flux:input.group.prefix>
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
</div>
