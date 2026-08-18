{{-- resources/views/components/admin/partials/role-list/⚡filter.blade.php --}}

{{-- Filter part --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('ui.title.filter')"
        :description="__('Refine the role list by name, category, assignability, and system status.')"
    />

    <div class="flex w-full items-end gap-3">
        <div class="min-w-0 flex-none basis-1/4">
            <flux:label for="role-list-search">
                <x-ui.tooltip.trigger
                    :title="__('admin.translation_list.filter.filter_by_search')"
                    :text="__('Enter a search term to filter the list of roles.')"
                >
                    {{ __('ui.actions.search') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group class="w-full min-w-0">
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass stroke-width="1" />
                </flux:input.group.prefix>

                <flux:input
                    class="w-full min-w-0"
                    id="role-list-search"
                    name="role-list-search"
                    type="text"
                    copyable
                    clearable
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by name, category or description') }}"
                />
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/6">
            <flux:label for="role-list-category-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.permission_list.filter.filter_by_category')"
                    :text="__('Select a category to filter the list of roles.')"
                >
                    {{ __('ui.labels.category') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.tag stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="role-list-category-filter"
                    name="role-list-category-filter"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="categoryFilter"
                >
                    <flux:select.option value="">
                        {{ __('ui.states.all') }}
                    </flux:select.option>

                    @foreach ($roleCategories as $category)
                        <flux:select.option value="{{ $category }}">
                            {{ Str::headline($category) }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/6">
            <flux:label for="role-list-assignable-filter">
                <x-ui.tooltip.trigger
                    :title="__('Filter by assignable')"
                    :text="__('Select an assignable status to filter the list of roles.')"
                >
                    {{ __('ui.assignable.assignable') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.handshake stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="role-list-assignable-filter"
                    name="role-list-assignable-filter"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="assignableFilter"
                >
                    <flux:select.option value="">
                        {{ __('ui.states.all') }}
                    </flux:select.option>

                    <flux:select.option value="yes">
                        {{ __('ui.assignable.assignable') }}
                    </flux:select.option>

                    <flux:select.option value="no">
                        {{ __('Not assignable') }}
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/6">
            <flux:label for="role-list-system-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.permission_list.filter.filter_by_system')"
                    :text="__('Select a system status to filter the list of roles.')"
                >
                    {{ __('ui.labels.label') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.heart-pulse stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="role-list-system-filter"
                    name="role-list-system-filter"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="systemFilter"
                >
                    <flux:select.option value="">
                        {{ __('ui.states.all') }}
                    </flux:select.option>

                    <flux:select.option value="yes">
                        {{ __('System roles') }}
                    </flux:select.option>

                    <flux:select.option value="no">
                        {{ __('Non-system roles') }}
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </div>

        <div class="ml-auto flex-none">
            <x-ui.table.per-page-selector
                id="role-list-per-page"
                name="role-list-per-page"
                model="perPage"
                :options="[10, 25, 50, 100]"
            />
        </div>

        <div class="flex-none">
            <x-ui.button.reset wire:click="clearFilters" />
        </div>
    </div>
</flux:card>
