{{-- resources/views/components/admin/partials/client-list/⚡filter.blade.php --}}

{{-- Filter --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Filtering')"
        :description="__(
            'Refine the client list with powerful filters: search by name, legal name, client number or description; filter by type, status, and people associations.',
        )"
    />

    <div class="flex w-full items-end gap-3">
        <div class="min-w-0 flex-none basis-1/4">
            <flux:label for="client-list-search">
                <x-ui.tooltip.trigger
                    :title="__('Search clients')"
                    :text="__(
                        'Enter a search term to filter clients by name, legal name, client number or description. The search is case-sensitive.',
                    )"
                >
                    {{ __('Search') }}
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
                    placeholder="{{ __('Search by name, legal name, client number or description') }}"
                />
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/6">
            <flux:label for="client-list-type-filter">
                <x-ui.tooltip.trigger
                    :title="__('Filter by type')"
                    :text="__('Select a type to filter the client list.')"
                >
                    {{ __('Type') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.tags stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="client-list-type-filter"
                    name="client-list-type-filter"
                    wire:model.live="typeFilter"
                >
                    <flux:select.option value="">
                        {{ __('All types') }}
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
                    :title="__('Filter by status')"
                    :text="__('Select a status to filter the client list.')"
                >
                    {{ __('Status') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.heart-pulse stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="client-list-status-filter"
                    name="client-list-status-filter"
                    wire:model.live="statusFilter"
                >
                    <flux:select.option value="">
                        {{ __('All statuses') }}
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
                    :title="__('Filter by people')"
                    :text="__('Select a people filter to filter the client list.')"
                >
                    {{ __('People') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.users stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="client-list-people-filter"
                    name="client-list-people-filter"
                    wire:model.live="peopleFilter"
                >
                    <flux:select.option value="">
                        {{ __('All') }}
                    </flux:select.option>

                    <flux:select.option value="with_people">
                        {{ __('With people') }}
                    </flux:select.option>

                    <flux:select.option value="without_people">
                        {{ __('Without people') }}
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
