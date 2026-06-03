{{-- resources/views/components/admin/partials/flag-reference-list/⚡filter.blade.php --}}

<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Filtering') }}
    </flux:heading>

    <div class="flex w-full flex-wrap items-end gap-3">
        <div class="min-w-0 flex-1">
            <flux:label for="flag-reference-search">
                {{ __('Search') }}
            </flux:label>

            <flux:input.group class="w-full">
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass stroke-width="1" />
                </flux:input.group.prefix>

                <flux:input
                    id="flag-reference-search"
                    name="flag-reference-search"
                    type="text"
                    clearable
                    copyable
                    wire:model.live="search"
                    placeholder="{{ __('Search code, type, source, resolved icon or comments') }}"
                />
            </flux:input.group>
        </div>

        <div class="min-w-44">
            <flux:label for="flag-reference-type-filter">
                {{ __('Type') }}
            </flux:label>

            <flux:select
                id="flag-reference-type-filter"
                name="flag-reference-type-filter"
                wire:model.live="typeFilter"
            >
                <flux:select.option value="">
                    {{ __('All') }}
                </flux:select.option>

                <flux:select.option value="ll">ll</flux:select.option>
                <flux:select.option value="ll-CC">ll-CC</flux:select.option>
                <flux:select.option value="ll-###">ll-###</flux:select.option>
                <flux:select.option value="ll-special">ll-special</flux:select.option>
                <flux:select.option value="other">other</flux:select.option>
            </flux:select>
        </div>

        <div class="min-w-44">
            <flux:label for="flag-reference-status-filter">
                {{ __('Status') }}
            </flux:label>

            <flux:select
                id="flag-reference-status-filter"
                name="flag-reference-status-filter"
                wire:model.live="statusFilter"
            >
                <flux:select.option value="">
                    {{ __('All') }}
                </flux:select.option>

                <flux:select.option value="resolved">
                    {{ __('Resolved') }}
                </flux:select.option>

                <flux:select.option value="needs_review">
                    {{ __('Needs review') }}
                </flux:select.option>
            </flux:select>
        </div>

        <div class="ml-auto min-w-44">
            <x-ui.table.per-page-selector
                id="flag-reference-per-page"
                name="flag-reference-per-page"
                model="perPage"
                :options="[10, 25, 50, 100, 200]"
            />
        </div>

        <div class="flex-none">
            <x-ui.button.reset wire:click="clearFilters" />
        </div>
    </div>
</flux:card>
