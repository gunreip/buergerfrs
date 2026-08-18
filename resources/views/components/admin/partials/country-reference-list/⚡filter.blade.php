{{-- resources/views/components/admin/partials/country-reference-list/⚡filter.blade.php --}}

{{-- Filter --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('ui.title.filter')"
        :description="__('admin.country_reference_list.filter.refine_the_list_of_reference_countries_by_searching_for_specific_attributes_or_a',
        )"
    />

    <div class="grid gap-3 xl:grid-cols-7">
        <div class="col-span-2">
            <flux:label for="country-reference-list-search">
                <x-ui.tooltip.trigger
                    :title="__('ui.actions.search')"
                    :text="__('admin.country_reference_list.filter.enter_a_search_term_to_filter_countries_by_iso_code_name_phone_code_capital_city',
                    )"
                >
                    {{ __('ui.actions.search') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass stroke-width="1" />
                </flux:input.group.prefix>

                <flux:input
                    id="country-reference-list-search"
                    name="country-reference-list-search"
                    type="text"
                    clearable
                    copyable
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('admin.country_reference_list.filter.iso_name_phone_capital_region') }}"
                />
            </flux:input.group>
        </div>

        <div>
            <flux:label for="country-reference-list-region-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.country_reference_list.filter.filter_by_region')"
                    :text="__('admin.country_reference_list.filter.select_a_region_to_filter_the_list_of_reference_countries')"
                >
                    {{ __('ui.filters.region') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:select
                id="country-reference-list-region-filter"
                name="country-reference-list-region-filter"
                wire:model.live="regionFilter"
            >
                <flux:select.option value="">
                    {{ __('ui.states.all') }}
                </flux:select.option>

                @foreach ($regions as $region)
                    <flux:select.option value="{{ $region }}">
                        {{ $region }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div>
            <flux:label for="country-reference-list-status-filter">
                <x-ui.tooltip.trigger
                    :title="__('ui.filters.filter-by-status')"
                    :text="__('admin.country_reference_list.filter.select_a_status_to_filter_the_list_of_reference_countries')"
                >
                    {{ __('ui.state.status') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:select
                id="country-reference-list-status-filter"
                name="country-reference-list-status-filter"
                wire:model.live="statusFilter"
            >
                <flux:select.option value="">
                    {{ __('ui.states.all') }}
                </flux:select.option>

                <flux:select.option value="active">
                    {{ __('ui.state.active') }}
                </flux:select.option>

                <flux:select.option value="inactive">
                    {{ __('ui.filters.inactive') }}
                </flux:select.option>
            </flux:select>
        </div>

        <div>
            <flux:label for="country-reference-list-membership-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.country_reference_list.filter.filter_by_membership')"
                    :text="__('admin.country_reference_list.filter.select_a_membership_to_filter_the_list_of_reference_countries')"
                >
                    {{ __('admin.country_reference_list.filter.membership') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:select
                id="country-reference-list-membership-filter"
                name="country-reference-list-membership-filter"
                wire:model.live="membershipFilter"
            >
                <flux:select.option value="">
                    {{ __('ui.states.all') }}
                </flux:select.option>

                <flux:select.option value="eu">
                    {{ __('ui.filters.eu') }}
                </flux:select.option>

                <flux:select.option value="eea">
                    {{ __('ui.filters.eea') }}
                </flux:select.option>

                <flux:select.option value="schengen">
                    {{ __('admin.country_reference_list.filter.schengen') }}
                </flux:select.option>
            </flux:select>
        </div>

        <div>
            <flux:label for="country-reference-list-data-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.country_reference_list.filter.filter_by_data_quality')"
                    :text="__('admin.country_reference_list.filter.select_a_data_quality_filter_to_filter_the_list_of_reference_countries')"
                >
                    {{ __('admin.country_reference_list.filter.data_quality') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:select
                id="country-reference-list-data-filter"
                name="country-reference-list-data-filter"
                wire:model.live="dataFilter"
            >
                <flux:select.option value="">
                    {{ __('ui.states.all') }}
                </flux:select.option>

                <flux:select.option value="missing_capital">
                    {{ __('ui.filters.missing-capital') }}
                </flux:select.option>

                <flux:select.option value="missing_phone_code">
                    {{ __('admin.country_reference_list.filter.missing_phone_code') }}
                </flux:select.option>

                <flux:select.option value="missing_address_format">
                    {{ __('admin.country_reference_list.filter.missing_address_format') }}
                </flux:select.option>

                <flux:select.option value="with_subdivisions">
                    {{ __('admin.country_reference_list.filter.with_subdivisions') }}
                </flux:select.option>

                <flux:select.option value="without_subdivisions">
                    {{ __('admin.country_reference_list.filter.without_subdivisions') }}
                </flux:select.option>
            </flux:select>
        </div>

        <div class="ml-auto flex-none">
            <x-ui.table.per-page-selector
                id="country-reference-list-per-page"
                name="country-reference-list-per-page"
                model="perPage"
                :options="[10, 25, 50, 100]"
            />
        </div>
    </div>
</flux:card>
