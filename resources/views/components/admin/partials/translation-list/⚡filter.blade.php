{{-- resources/views/components/admin/partials/translation-list/⚡filter.blade.php --}}

{{-- Filter Part for Translation List --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Filtering')"
        :description="__('Refine the translation list by key, value, language, and translation file.')"
    >

        <div class="mb-6 grid gap-2 xl:grid-cols-[1fr_auto]">
            <div class="space-y-2">
                {{-- Status filters --}}
                <div class="flex flex-wrap items-center gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">
                    <span
                        class="mr-2 w-24 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        {{ __('Status') }}
                    </span>

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

                {{-- Classification filters --}}
                <div class="flex flex-wrap items-center gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">
                    <span
                        class="mr-2 w-24 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        {{ __('Type') }}
                    </span>

                    @foreach ($classificationOptions as $option)
                        @php
                            $count = $option === 'all' ? $total : $classificationCounts[$option] ?? 0;
                            $label = match ($option) {
                                'all' => __('All types'),
                                'backfill_by_translation' => __('Backfill'),
                                default => str($option)->headline(),
                            };
                        @endphp

                        <flux:button
                            type="button"
                            size="sm"
                            variant="{{ $classification === $option ? 'primary' : 'ghost' }}"
                            wire:click="setClassification('{{ $option }}')"
                        >
                            {{ $label }}
                            <span class="ml-1 opacity-70">
                                {{ $count }}
                            </span>
                        </flux:button>
                    @endforeach
                </div>
            </div>

            {{-- Problems only toggle --}}
            <div
                class="min-w-58 flex items-center justify-center rounded-md bg-zinc-50/50 px-6 py-2 dark:bg-zinc-800/50">
                <flux:field variant="inline">
                    <flux:switch
                        class="switch-colored mr-3 hover:cursor-pointer"
                        wire:click="toggleOnlyProblems"
                    />

                    <flux:label
                        class="text-sm opacity-70 hover:cursor-pointer"
                        wire:click="toggleOnlyProblems"
                    >
                        {{ __('Only problems') }}

                        <span class="ml-1 opacity-70">
                            {{ $problemCount }}
                        </span>
                    </flux:label>
                </flux:field>
            </div>
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
                    :title="__('Select a target language')"
                    :text="__('Select a target language to filter the list of translations.')"
                >
                    {{ __('Target Language') }}
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

                    @foreach ($translationLanguages as $translationLanguage)
                        <flux:select.option value="{{ $translationLanguage->locale }}">
                            {{ $translationLanguage->locale }}
                            ·
                            {{ $translationLanguage->native_name }}
                            @if (!$translationLanguage->is_enabled_for_app)
                                · {{ __('translation only') }}
                            @endif
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
