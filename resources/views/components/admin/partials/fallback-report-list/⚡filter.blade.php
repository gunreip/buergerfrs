{{-- resources/views/components/admin/partials/fallback-report-list/⚡filter.blade.php --}}

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
            <flux:label for="fallback-report-list-search">
                {{ __('Search') }}
            </flux:label>

            <flux:input.group class="w-full min-w-0">
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass stroke-width="1" />
                </flux:input.group.prefix>

                <flux:input
                    class="w-full min-w-0"
                    id="fallback-report-list-search"
                    name="fallback-report-list-search"
                    type="text"
                    copyable
                    clearable
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by ID, type, key, fallback or context') }}"
                />
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-none basis-1/5">
            <flux:label for="fallback-report-list-status-filter">
                {{ __('Status') }}
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.list-filter stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="fallback-report-list-status-filter"
                    name="fallback-report-list-status-filter"
                    wire:model.live="statusFilter"
                >
                    <flux:select.option value="">
                        {{ __('All') }}
                    </flux:select.option>

                    <flux:select.option value="open">
                        {{ __('Open') }}
                    </flux:select.option>

                    <flux:select.option value="reviewed">
                        {{ __('Reviewed') }}
                    </flux:select.option>
                </flux:select>
            </flux:input.group>
        </div>

        <div class="ml-auto min-w-0 flex-none basis-64">
            <x-ui.table.per-page-selector
                id="fallback-report-list-per-page"
                name="fallback-report-list-per-page"
                model="perPage"
                :options="[10, 25, 50, 100]"
            />
        </div>

        <div class="flex-none">
            <x-ui.button.reset wire:click="clearFilters" />
        </div>

    </div>
</flux:card>
