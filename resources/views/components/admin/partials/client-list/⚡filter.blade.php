{{-- resources/views/components/admin/partials/client-list/⚡filter.blade.php --}}

{{-- Filter --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Filtering') }}
    </flux:heading>

    <div class="flex w-full items-end gap-3">
        <div class="min-w-0 flex-none basis-1/4">
            <flux:label for="client-list-search">
                {{ __('Search') }}
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
                {{ __('Type') }}
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
                {{ __('Status') }}
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
                {{ __('People') }}
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
