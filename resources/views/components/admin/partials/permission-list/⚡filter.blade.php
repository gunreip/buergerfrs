{{-- resources/views/components/admin/partials/permission-list/⚡filter.blade.php --}}

{{-- Filter part --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('ui.title.filter')"
        :description="__('admin.permissions.filters.description')"
    />

    <div class="flex w-full items-end gap-3">
        <div class="min-w-0 flex-none basis-1/4">
            <flux:label for="permission-list-search">
                <x-ui.tooltip.trigger
                    :title="__('ui.actions.search')"
                    :text="__(
                        'admin.permission_list.filter.enter_a_search_term_to_filter_permissions_by_name_guard_category_or_description_',
                    )"
                >
                    {{ __('ui.actions.search') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group class="w-full min-w-0">
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass />
                </flux:input.group.prefix>

                <flux:input
                    class="w-full min-w-0"
                    id="permission-list-search"
                    name="permission-list-search"
                    type="text"
                    copyable
                    clearable
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('admin.permissions.filters.search.placeholder') }}"
                />
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/6">
            <flux:label for="permission-list-guard-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.permission_list.filter.filter_by_guard')"
                    :text="__('admin.permission_list.filter.select_a_guard_to_filter_the_list_of_permissions')"
                >
                    {{ __('ui.labels.guard') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.shield-question-mark />
                </flux:input.group.prefix>

                <flux:select
                    id="permission-list-guard-filter"
                    name="permission-list-guard-filter"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="guardFilter"
                >
                    <flux:select.option value="">
                        {{ __('ui.states.all') }}
                    </flux:select.option>

                    @foreach ($guardOptions as $guard)
                        <flux:select.option value="{{ $guard }}">
                            {{ $guard }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/6">
            <flux:label for="permission-list-category-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.permission_list.filter.filter_by_category')"
                    :text="__('admin.permission_list.filter.select_a_category_to_filter_the_list_of_permissions')"
                >
                    {{ __('ui.labels.category') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.tags />
                </flux:input.group.prefix>

                <flux:select
                    id="permission-list-category-filter"
                    name="permission-list-category-filter"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="categoryFilter"
                >
                    <flux:select.option value="">
                        {{ __('ui.states.all') }}
                    </flux:select.option>

                    @foreach ($categoryOptions as $category)
                        <flux:select.option value="{{ $category }}">
                            {{ Str::headline($category) }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/5">
            <flux:label for="permission-list-role-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.permission_list.filter.filter_by_role')"
                    :text="__('admin.permission_list.filter.select_a_role_to_filter_the_list_of_permissions')"
                >
                    {{ __('ui.labels.role') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.badge-check />
                </flux:input.group.prefix>

                <flux:select
                    id="permission-list-role-filter"
                    name="permission-list-role-filter"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="roleFilter"
                >
                    <flux:select.option value="">
                        {{ __('ui.states.all') }}
                    </flux:select.option>

                    @foreach ($roleOptions as $role)
                        <flux:select.option value="{{ $role }}">
                            {{ $role }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </div>

        <div class="ml-auto min-w-0 flex-none basis-64">
            <x-ui.table.per-page-selector
                id="permission-list-per-page"
                name="permission-list-per-page"
                model="perPage"
                :options="[10, 25, 50, 100]"
            />
        </div>
    </div>

    <div class="mt-3 flex w-full items-end gap-3">
        <div class="min-w-0 flex-none basis-1/4">
            <flux:label for="permission-list-assignment-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.permission_list.filter.filter_by_assignment')"
                    :text="__(
                        'admin.permission_list.filter.select_an_assignment_status_to_filter_the_list_of_permissions',
                    )"
                >
                    {{ __('ui.labels.label') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.link stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="permission-list-assignment-filter"
                    name="permission-list-assignment-filter"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="assignmentFilter"
                >
                    <flux:select.option value="">
                        {{ __('ui.states.all') }}
                    </flux:select.option>

                    <flux:select.option value="assigned">
                        {{ __('ui.states.assigned') }}
                    </flux:select.option>

                    <flux:select.option value="unassigned">
                        {{ __('ui.states.unassigned') }}
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/4">
            <flux:label for="permission-list-system-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.permission_list.filter.filter_by_system')"
                    :text="__(
                        'admin.permission_list.filter.select_a_system_status_to_filter_the_list_of_permissions',
                    )"
                >
                    {{ __('ui.labels.label') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.monitor-cog />
                </flux:input.group.prefix>

                <flux:select
                    id="permission-list-system-filter"
                    name="permission-list-system-filter"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="systemFilter"
                >
                    <flux:select.option value="">
                        {{ __('ui.states.all') }}
                    </flux:select.option>

                    <flux:select.option value="yes">
                        {{ __('ui.states.system') }}
                    </flux:select.option>

                    <flux:select.option value="no">
                        {{ __('ui.states.non_system') }}
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </div>

        <div class="ml-auto flex-none">
            <x-ui.button.reset wire:click="clearFilters" />
        </div>
    </div>
</flux:card>
