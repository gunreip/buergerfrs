{{-- resources/views/components/admin/partials/person-list/⚡filter.blade.php --}}

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
            <flux:label for="person-list-search">
                {{ __('Search') }}
            </flux:label>

            <flux:input.group class="w-full min-w-0">
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass stroke-width="1" />
                </flux:input.group.prefix>

                <flux:input
                    class="w-full min-w-0"
                    id="person-list-search"
                    name="person-list-search"
                    type="text"
                    copyable
                    clearable
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by name, person number, user name or email') }}"
                />
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/5">
            <flux:label for="person-list-user-filter">
                {{ __('User') }}
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.user stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="person-list-user-filter"
                    name="person-list-user-filter"
                    wire:model.live="userFilter"
                >
                    <flux:select.option value="">
                        {{ __('All') }}
                    </flux:select.option>

                    <flux:select.option value="with_user">
                        {{ __('With user') }}
                    </flux:select.option>

                    <flux:select.option value="without_user">
                        {{ __('Without user') }}
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/5">
            <flux:label for="person-list-client-filter">
                {{ __('Client') }}
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.building-2 stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="person-list-client-filter"
                    name="person-list-client-filter"
                    wire:model.live="clientFilter"
                >
                    <flux:select.option value="">
                        {{ __('All') }}
                    </flux:select.option>

                    <flux:select.option value="with_client">
                        {{ __('With client') }}
                    </flux:select.option>

                    <flux:select.option value="without_client">
                        {{ __('Without client') }}
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </div>

        <div class="ml-auto flex-none">
            <x-ui.table.per-page-selector
                id="person-list-per-page"
                name="person-list-per-page"
                model="perPage"
                :options="[10, 25, 50, 100]"
            />
        </div>

        <div class="flex-none">
            <x-ui.button.reset wire:click="clearFilters" />
        </div>
    </div>
</flux:card>
