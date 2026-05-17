{{-- resources/views/components/admin/partials/html-view-audit/⚡filter.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Filters')"
        :description="__('Filter the audit results by search term, section, or problem type.')"
    />

    <div class="flex flex-wrap items-end gap-3">
        <div class="min-w-64 flex-1">
            <flux:label>
                {{ __('Search') }}
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

        <div class="w-106">
            < <flux:label>
                {{ __('Section') }}
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
                        <flux:select.option value="all">{{ __('All sections') }}</flux:select.option>
                        <flux:select.option value="native_html">{{ __('Native HTML') }}</flux:select.option>
                        <flux:select.option value="custom_components">{{ __('Custom components') }}</flux:select.option>
                    </flux:select>
                </flux:input.group>
        </div>

        <div class="w-106">
            <flux:label>
                {{ __('Problem type') }}
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
                    <flux:select.option value="all">{{ __('All types') }}</flux:select.option>
                    <flux:select.option value="unclosed">{{ __('Unclosed') }}</flux:select.option>
                    <flux:select.option value="mismatched">{{ __('Mismatched') }}</flux:select.option>
                    <flux:select.option value="unexpected_closing">{{ __('Unexpected closing') }}</flux:select.option>
                </flux:select>
            </flux:input.group>
        </div>

        <div class="flex-none">
            <x-ui.button.reset wire:click="clearFilters" />
        </div>
    </div>
</flux:card>
