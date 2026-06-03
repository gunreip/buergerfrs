{{-- resources/views/components/admin/partials/translation-sub-languages/⚡filter.blade.php --}}

<flux:card class="mt-6">

    <x-ui.headers.card
        :title="__('Filtering')"
        :description="__('Refine sub-language variants by locale, base language, and override availability.')"
    />

    <div class="flex flex-wrap items-end gap-3">
        <div class="min-w-0 flex-1 basis-72">
            <flux:label for="translation-sub-language-search">
                {{ __('Search') }}
            </flux:label>

            <flux:input.group class="w-full min-w-0">
                <flux:input.group.prefix>
                    <flux:icon.magnifying-glass stroke-width="1" />
                </flux:input.group.prefix>

                <flux:input
                    id="translation-sub-language-search"
                    name="translation-sub-language-search"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    clearable
                    placeholder="{{ __('Search by locale or name') }}"
                />
            </flux:input.group>
        </div>

        <div class="min-w-0 flex-1 basis-64">
            <flux:label for="translation-sub-language-base-filter">{{ __('Main language') }}</flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.language stroke-width="1" />
                </flux:input.group.prefix>

                <flux:select
                    id="translation-sub-language-base-filter"
                    name="translation-sub-language-base-filter"
                    wire:model.live="baseLocaleFilter"
                >
                    <flux:select.option value="">{{ __('All main languages') }}</flux:select.option>

                    @foreach ($baseLocaleOptions as $baseLocale)
                        <flux:select.option value="{{ $baseLocale->locale }}">
                            {{ $baseLocale->locale }}
                            ·
                            {{ $baseLocale->native_name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:input.group>
        </div>

        <div class="min-w-58 flex items-center rounded-md bg-zinc-50/50 px-4 py-2 dark:bg-zinc-800/50">
            <flux:field
                class="h-6 items-center gap-2"
                variant="inline"
            >
                <flux:switch
                    class="switch-colored mr-3 hover:cursor-pointer"
                    wire:model.live="onlyWithOverrides"
                />

                <flux:label class="text-sm opacity-70 hover:cursor-pointer">
                    {{ __('Only with overrides') }}
                </flux:label>
            </flux:field>
        </div>

        <div class="flex-none">
            <x-ui.button.reset wire:click="clearFilters" />
        </div>
    </div>

    <div class="mt-4 rounded-md border border-zinc-200/70 px-3 py-3 dark:border-zinc-700">
        <div class="mb-2 flex items-center justify-between gap-3">
            <flux:label class="text-sm font-semibold">
                {{ __('Sub-languages') }}
            </flux:label>

            <flux:badge
                color="sky"
                variant="subtle"
                size="sm"
            >
                {{ count($selectedSubLanguageLocales) }} / {{ $maxSelectedSubLanguageFilters }}
            </flux:badge>
        </div>

        @if ($baseLocaleFilter === '')
            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('Select a main language first to choose sub-languages.') }}
            </flux:text>
        @elseif ($availableSubLanguageOptions->isEmpty())
            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('No active sub-languages available for the selected main language.') }}
            </flux:text>
        @else
            <div class="max-h-44 overflow-auto pr-1">
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                    @foreach ($availableSubLanguageOptions as $subLanguage)
                        @php
                            $isChecked = in_array($subLanguage->locale, $selectedSubLanguageLocales, true);
                            $reachedLimit = count($selectedSubLanguageLocales) >= $maxSelectedSubLanguageFilters;
                            $isDisabled = !$isChecked && $reachedLimit;
                        @endphp

                        <label @class([
                            'flex items-center gap-2 rounded-md border px-2.5 py-2 transition',
                            'border-zinc-200 bg-white/70 hover:cursor-pointer hover:bg-zinc-100/70 dark:border-zinc-700 dark:bg-zinc-800/50 dark:hover:bg-zinc-700/50' => !$isDisabled,
                            'border-zinc-200/60 bg-zinc-100/40 opacity-50 dark:border-zinc-700/60 dark:bg-zinc-800/20 cursor-not-allowed' => $isDisabled,
                        ])>
                            <flux:checkbox
                                class="mt-0.5 shrink-0"
                                value="{{ $subLanguage->locale }}"
                                wire:model.live="selectedSubLanguageLocales"
                                :disabled="$isDisabled"
                            />

                            <span class="min-w-0 flex-1 text-sm">
                                <span class="block font-mono font-semibold uppercase text-zinc-800 dark:text-zinc-200">
                                    {{ $subLanguage->locale }}
                                </span>
                                <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $subLanguage->display_name }}
                                </span>
                            </span>

                            <x-ui.country.flag-locale
                                class="ml-auto shrink-0"
                                :locale="$subLanguage->locale"
                                size="lg"
                                :title="$subLanguage->display_name"
                            />
                        </label>
                    @endforeach
                </div>
            </div>

            <flux:text class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('You can select up to :max sub-languages for focused editing.', ['max' => $maxSelectedSubLanguageFilters]) }}
            </flux:text>
        @endif
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-2">
        <flux:badge
            color="sky"
            variant="subtle"
        >
            {{ __('Showing') }} {{ number_format($subLocales->count()) }} {{ __('of') }}
            {{ number_format($activeSubLocalesTotal) }}
        </flux:badge>

        @if ($hasActiveFilters)
            <flux:badge
                color="amber"
                variant="subtle"
            >
                {{ __('Filters active') }}
            </flux:badge>
        @endif
    </div>
</flux:card>
