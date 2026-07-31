{{-- resources/views/components/admin/partials/client-list/⚡filter.blade.php --}}

{{-- Filter --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('admin.client_list.filter.filtering')"
        :description="__(
            'admin.client_list.filter.refine_the_client_list_with_powerful_filters_search_by_name_legal_name_client_nu',
        )"
    />

    <div class="flex w-full items-end gap-3">
        <div class="min-w-0 flex-none basis-1/4">
            <flux:label for="client-list-search">
                <x-ui.tooltip.trigger
                    :title="__('admin.client_list.filter.search_clients')"
                    :text="__(
                        'admin.client_list.filter.enter_a_search_term_to_filter_clients_by_name_legal_name_client_number_or_descri',
                    )"
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
                    id="client-list-search"
                    name="client-list-search"
                    type="text"
                    copyable
                    clearable
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('admin.client_list.filter.search_by_name_legal_name_client_number_or_description') }}"
                />
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/6">
            <flux:label for="client-list-type-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.client_list.filter.filter_by_type')"
                    :text="__('admin.client_list.filter.select_a_type_to_filter_the_client_list')"
                >
                    {{ __('ui.type') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.tags stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="client-list-type-filter"
                    name="client-list-type-filter"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="typeFilter"
                >
                    <flux:select.option value="">
                        {{ __('ui.all-types') }}
                    </flux:select.option>

                    @foreach ($typeOptions as $type)
                        <flux:select.option value="{{ $type }}">
                            {{ Str::headline($type) }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/6">
            <flux:label for="client-list-status-filter">
                <x-ui.tooltip.trigger
                    :title="__('ui.filters.filter-by-status')"
                    :text="__('admin.client_list.filter.select_a_status_to_filter_the_client_list')"
                >
                    {{ __('ui.status') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.heart-pulse stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="client-list-status-filter"
                    name="client-list-status-filter"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="statusFilter"
                >
                    <flux:select.option value="">
                        {{ __('ui.all-statuses') }}
                    </flux:select.option>

                    @foreach ($statusOptions as $status)
                        <flux:select.option value="{{ $status }}">
                            {{ Str::headline($status) }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/6">
            <flux:label for="client-list-people-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.client_list.filter.filter_by_people')"
                    :text="__('admin.client_list.filter.select_a_people_filter_to_filter_the_client_list')"
                >
                    {{ __('ui.people') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.users stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="client-list-people-filter"
                    name="client-list-people-filter"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="peopleFilter"
                >
                    <flux:select.option value="">
                        {{ __('ui.states.all') }}
                    </flux:select.option>

                    <flux:select.option value="with_people">
                        {{ __('ui.meta.with-people') }}
                    </flux:select.option>

                    <flux:select.option value="without_people">
                        {{ __('ui.meta.without-people') }}
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </div>

        <div class="ml-auto flex-none">
            <x-ui.table.per-page-selector
                id="client-list-per-page"
                name="client-list-per-page"
                model="perPage"
                :options="[10, 25, 50, 100]"
            />
        </div>

        <div class="flex-none">
            <x-ui.button.reset wire:click="clearFilters" />
        </div>
    </div>
</flux:card>
