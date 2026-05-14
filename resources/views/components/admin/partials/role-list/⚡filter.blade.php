{{-- resources/views/components/admin/partials/role-list/filter.blade.php --}}

{{-- Filter part --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Filtering')"
        :description="__('Refine the role list by name, category, assignability, and system status.')"
    />

    {{-- <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Filtering') }}
    </flux:heading> --}}

    <div class="flex w-full items-end gap-3">
        <div class="min-w-0 flex-none basis-1/4">
            <flux:label for="role-list-search">
                {{ __('Search') }}
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
                {{ __('Category') }}
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.tag stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="role-list-category-filter"
                    name="role-list-category-filter"
                    wire:model.live="categoryFilter"
                >
                    <flux:select.option value="">
                        {{ __('All categories') }}
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
                {{ __('Assignable') }}
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.handshake stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="role-list-assignable-filter"
                    name="role-list-assignable-filter"
                    wire:model.live="assignableFilter"
                >
                    <flux:select.option value="">
                        {{ __('All') }}
                    </flux:select.option>

                    <flux:select.option value="yes">
                        {{ __('Assignable') }}
                    </flux:select.option>

                    <flux:select.option value="no">
                        {{ __('Not assignable') }}
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/6">
            <flux:label for="role-list-system-filter">
                {{ __('System') }}
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.heart-pulse stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="role-list-system-filter"
                    name="role-list-system-filter"
                    wire:model.live="systemFilter"
                >
                    <flux:select.option value="">
                        {{ __('All') }}
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

        <div class="ml-auto min-w-0 flex-none basis-32">
            <x-ui.table.per-page-selector
                id="role-list-per-page"
                name="role-list-per-page"
                model="perPage"
                :options="[10, 25, 50, 100]"
            />
        </div>

        <div class="ml-auto flex-none">
            <flux:button
                type="button"
                variant="filled"
                icon="refresh-ccw"
                wire:click="clearFilters"
            >
                {{ __('Reset') }}
            </flux:button>
        </div>
    </div>
</flux:card>
