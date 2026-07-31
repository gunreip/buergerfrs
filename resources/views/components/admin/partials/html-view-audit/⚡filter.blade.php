{{-- resources/views/components/admin/partials/html-view-audit/⚡filter.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Filters')"
        :description="__('Filter the audit history by status, search term, section, or problem type.')"
    />

    <div class="flex flex-wrap items-end gap-3">
        <div class="min-w-84 flex-1">
            <flux:label for="html-view-audit-filter-search">
                <x-ui.tooltip.trigger
                    :title="__('Search the audit history')"
                    :text="__(
                        'Enter a search term to filter the audit history by file name, tag name, or other relevant information. The search is case-sensitive.',
                    )"
                >
                    {{ __('ui.actions.search') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group class="w-full">
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass stroke-width="1" />
                </flux:input.group.prefix>

                <flux:input
                    class="w-full min-w-0"
                    id="html-view-audit-filter-search"
                    name="html-view-audit-filter-search"
                    type="text"
                    copyable
                    clearable
                    wire:model.live.debounce.300ms="search"
                    :placeholder="__('File, tag, closing tag ...')"
                />
            </flux:input.group>
        </div>

        <div class="w-74">
            <flux:label for="html-view-audit-filter-status">
                <x-ui.tooltip.trigger
                    :title="__('ui.filters.filter-by-status')"
                    :text="__('Select a status to filter the audit history.')"
                >
                    {{ __('ui.status') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.list-filter stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="html-view-audit-filter-status"
                    name="html-view-audit-filter-status"
                    wire:model.live="statusFilter"
                >
                    <flux:select.option value="open">{{ __('admin.translation_list.filter.open') }}</flux:select.option>
                    <flux:select.option value="changed">{{ __('Changed / moved') }}</flux:select.option>
                    <flux:select.option value="resolved">{{ __('Resolved') }}</flux:select.option>
                    <flux:select.option value="ignored">{{ __('Ignored') }}</flux:select.option>
                    <flux:select.option value="all">{{ __('ui.all-statuses') }}</flux:select.option>
                </flux:select>
            </flux:input.group>
        </div>

        <div class="w-74">
            <flux:label for="html-view-audit-filter-section">
                <x-ui.tooltip.trigger
                    :title="__('Filter by section')"
                    :text="__('Select a section to filter the audit history.')"
                >
                    {{ __('Section') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.code-xml stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="html-view-audit-filter-section"
                    name="html-view-audit-filter-section"
                    wire:model.live="sectionFilter"
                >
                    <flux:select.option value="all">{{ __('ui.filters.all-sections') }}</flux:select.option>
                    <flux:select.option value="native_html">{{ __('Native HTML') }}</flux:select.option>
                    <flux:select.option value="custom_components">{{ __('Custom components') }}</flux:select.option>
                </flux:select>
            </flux:input.group>
        </div>

        <div class="w-74">
            <flux:label for="html-view-audit-filter-type">
                <x-ui.tooltip.trigger
                    :title="__('Filter by problem type')"
                    :text="__('Select a problem type to filter the audit history.')"
                >
                    {{ __('Problem type') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.bug stroke-width="1" />
                </flux:input.group.prefix>
                <flux:select
                    id="html-view-audit-filter-type"
                    name="html-view-audit-filter-type"
                    wire:model.live="typeFilter"
                >
                    <flux:select.option value="all">{{ __('ui.all-types') }}</flux:select.option>
                    <flux:select.option value="unclosed">{{ __('Unclosed') }}</flux:select.option>
                    <flux:select.option value="mismatched">{{ __('Mismatched') }}</flux:select.option>
                    <flux:select.option value="unexpected_closing">{{ __('Unexpected closing') }}</flux:select.option>
                </flux:select>
            </flux:input.group>
        </div>

        <div class="ml-auto flex-none">
            <x-ui.table.per-page-selector
                id="html-view-audit-per-page"
                name="html-view-audit-per-page"
                model="perPage"
                :options="[10, 25, 50, 100]"
            />
        </div>

        <div class="flex-none">
            <x-ui.button.reset wire:click="clearFilters" />
        </div>
    </div>
</flux:card>
