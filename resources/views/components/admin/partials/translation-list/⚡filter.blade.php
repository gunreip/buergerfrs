{{-- resources/views/components/admin/partials/translation-list/⚡filter.blade.php --}}

{{-- Filter Part for Translation List --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Filtering')"
        :description="__('Refine the translation list by key, value, language, and translation file.')"
    >

        <div class="flex flex-wrap gap-2">
            @foreach ($statusOptions as $option)
                @php
                    $count = $option === 'all' ? $total : $statusCounts[$option] ?? 0;
                @endphp

                <flux:button
                    type="button"
                    size="sm"
                    variant="{{ $status === $option ? 'primary' : 'ghost' }}"
                    wire:click="setStatus('{{ $option }}')"
                >
                    {{ str($option)->headline() }}
                    <span class="ml-1 opacity-70">
                        {{ $count }}
                    </span>
                </flux:button>
            @endforeach
        </div>

    </x-ui.headers.card>

    <div class="flex w-full flex-wrap items-end gap-3">
        <div class="min-w-0 flex-1 basis-72">
            <flux:label for="translation-list-search">
                <x-ui.tooltip.trigger
                    :title="__('Filter by search')"
                    :text="__(
                        'Enter a search term to filter the list of translations by key or value. The search is case-sensitive.',
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
                    id="translation-list-search"
                    name="translation-list-search"
                    type="text"
                    copyable
                    clearable
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by key or value') }}"
                />
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-1 basis-64">
            <flux:label for="translation-list-language-filter">
                <x-ui.tooltip.trigger
                    :title="__('Filter by language')"
                    :text="__('Select a language to filter the list of translations.')"
                >
                    {{ __('Language') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.language stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="translation-list-language-filter"
                    name="translation-list-language-filter"
                    wire:model.live="languageFilter"
                >
                    <flux:select.option value="">
                        {{ __('All languages') }}
                    </flux:select.option>

                    @foreach ($locales as $locale)
                        <flux:select.option value="{{ $locale }}">
                            {{ $locale }}
                        </flux:select.option>
                    @endforeach

                </flux:select>
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-1 basis-64">
            <flux:label for="translation-list-file-filter">
                <x-ui.tooltip.trigger
                    :title="__('Filter by translation file')"
                    :text="__('Select a translation file to filter the list of translations.')"
                >
                    {{ __('Translation file') }}
                </x-ui.tooltip.trigger>
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.document-text stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="translation-list-file-filter"
                    name="translation-list-file-filter"
                    wire:model.live="fileFilter"
                >
                    <flux:select.option value="">
                        {{ __('All files') }}
                    </flux:select.option>

                    @foreach ($translationFiles as $translationFile)
                        <flux:select.option value="{{ $translationFile }}">
                            {{ $translationFile }}.php
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </div>

        <div class="ml-auto min-w-0 flex-none basis-56">
            <x-ui.table.per-page-selector
                id="translation-list-per-page"
                name="translation-list-per-page"
                model="perPage"
                :options="[10, 25, 50, 100]"
            />
        </div>

        <div class="flex-none">
            <x-ui.button.reset wire:click="clearFilters" />
        </div>
    </div>
</flux:card>
