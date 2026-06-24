{{-- resources/views/components/admin/partials/user-list/⚡filter.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('admin.permissions.filters.title')"
        :description="__('Filter the list of users by name, email or assigned roles.')"
    />

    <div class="flex w-full items-end gap-3">
        <div class="min-w-0 flex-none basis-1/4">
            <flux:label for="user-list-search">
                <x-ui.tooltip.trigger
                    :title="__('admin.translation_list.filter.filter_by_search')"
                    :text="__(
                        'Enter a search term to filter the list of users by name or email. The search is case-sensitive.',
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
                    id="user-list-search"
                    name="user-list-search"
                    type="text"
                    copyable
                    clearable
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by name or email') }}"
                />
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/4">
            <flux:label for="user-list-role-filter">
                <x-ui.tooltip.trigger
                    :title="__('admin.permission_list.filter.filter_by_role')"
                    :text="__('Select a role to filter the list of users.')"
                >
                    {{ __('ui.labels.role') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.tag stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="user-list-role-filter"
                    name="user-list-role-filter"
                    variant="listbox"
                    searchable
                    clearable
                    wire:model.live="roleFilter"
                >
                    <flux:select.option value="">
                        <div class="flex items-center gap-2">
                            <flux:icon.user-round-key
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('admin.permissions.filters.roles.all') }}
                        </div>
                    </flux:select.option>

                    <flux:select.option value="__none__">
                        <div class="flex items-center gap-2">
                            <flux:icon.user-round-key
                                class="text-zinc-400"
                                variant="mini"
                            />
                            {{ __('admin.user_list.filter.without_role') }}
                        </div>
                    </flux:select.option>

                    @foreach ($roles as $role)
                        <flux:select.option value="{{ $role }}">
                            <div class="flex items-center gap-2">
                                <flux:icon.user-round-key
                                    class="text-zinc-400"
                                    variant="mini"
                                />
                                {{ $role }}
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </div>

        <div class="ml-auto flex-none">
            <x-ui.table.per-page-selector
                id="user-list-per-page"
                name="user-list-per-page"
                model="perPage"
                :options="[10, 25, 50, 100]"
            />
        </div>

        <div class="flex-none">
            {{--
            TODO: Reset-Filter-Button implementieren
            --}}
            {{-- <x-ui.button.reset wire:click="clearFilters" /> --}}
        </div>
    </div>
</flux:card>
