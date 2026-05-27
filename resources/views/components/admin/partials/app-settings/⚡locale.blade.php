{{-- resources/views/components/admin/partials/app-settings/⚡locale.blade.php --}}

<flux:card class="mt-6">
    <div class="flex items-start justify-between gap-4">
        <x-ui.headers.card
            :title="__('Application languages')"
            :description="__(
                'Configure which languages are available for the App/UI and choose the global default language.',
            )"
        />

        <flux:badge
            variant="subtle"
            color="sky"
        >
            {{ __('Current') }}:
            <x-ui.locale.flag
                class="ml-2"
                :locale="$locale"
            />
        </flux:badge>
    </div>

    <div class="mt-6 space-y-6">

        {{-- Narrow down radio-buttons --}}
        <div class="grid grid-cols-1 gap-3 md:grid-cols-5 md:items-end">
            <flux:field class="md:col-span-2">
                <x-ui.tooltip.trigger
                    :title="__('Top languages')"
                    field="primaryLanguageScope"
                    :text="__('Select the top languages by their usage.')"
                >
                    <flux:label
                        class="mb-3 text-sm font-semibold"
                        for="primary-language-scope"
                    >
                        {{-- {{ __('Top languages') }} --}}
                        {{ __('Narrow down your selection ...') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:radio.group
                    class="w-full"
                    id="primary-language-scope"
                    name="primary-language-scope"
                    variant="segmented"
                    wire:model.live="primaryLanguageScope"
                >
                    @foreach ($primaryLanguageScopeOptions as $primaryLanguageScopeOption)
                        <flux:radio
                            value="{{ $primaryLanguageScopeOption['value'] }}"
                            icon="{{ $primaryLanguageScopeOption['icon'] }}"
                        >
                            {{ $primaryLanguageScopeOption['label'] }}
                        </flux:radio>
                    @endforeach
                </flux:radio.group>

            </flux:field>

            @php
                $selectedPrimaryLocaleRow = $localeRows->firstWhere('code', $selectedPrimaryLanguageCode);
                $selectedPrimaryLanguageLabel =
                    $selectedPrimaryLocaleRow?->localized_display_name ??
                    ($selectedPrimaryLocaleRow?->display_name ?? strtoupper($selectedPrimaryLanguageCode));
                $isSelectedPrimaryLanguageActive =
                    $selectedPrimaryLanguageCode !== '' &&
                    in_array($selectedPrimaryLanguageCode, $availableLocales, true);
                $canToggleSelectedPrimaryLanguage =
                    $selectedPrimaryLanguageCode !== '' &&
                    !in_array(strtolower($selectedPrimaryLanguageCode), ['en', 'de'], true);
            @endphp

            {{-- Select Primary language --}}
            <div class="md:col-span-3">
                <div class="grid grid-cols-1 gap-3 lg:grid-cols-5">
                    <div class="lg:col-span-3">
                        <flux:field>
                            <x-ui.tooltip.trigger
                                :title="__('Select available languages')"
                                field="selectedPrimaryLanguageCode"
                                :text="__(
                                    'Select which languages should be available for the App/UI. You can choose from all languages that have translations in the system. The global default language must be among the available languages.',
                                )"
                            >
                                <flux:label
                                    class="mb-3 text-sm font-semibold"
                                    for="available-locales-select"
                                >
                                    {{ __('Primary languages') }}
                                </flux:label>
                            </x-ui.tooltip.trigger>

                            <flux:input.group class="w-full">
                                <flux:input.group.prefix>
                                    @if ($selectedPrimaryLanguageCode !== '')
                                        <x-ui.locale.flag
                                            :locale="$selectedPrimaryLanguageCode"
                                            size="lg"
                                        />
                                    @else
                                        <flux:icon.language stroke-width="1" />
                                    @endif
                                </flux:input.group.prefix>

                                <flux:select
                                    id="primary-language-select"
                                    wire:model.live="selectedPrimaryLanguageCode"
                                    placeholder="{{ __('Please select') }}"
                                >
                                    @foreach ($primaryLocaleRows as $localeRow)
                                        @php
                                            $localizedLabel =
                                                $localeRow->localized_display_name ?:
                                                ($localeRow->display_name ?:
                                                strtoupper($localeRow->code));

                                            $nativeLabel = trim((string) ($localeRow->native_display_name ?? ''));

                                            $labelSuffix =
                                                $nativeLabel !== '' &&
                                                mb_strtolower($nativeLabel) !== mb_strtolower((string) $localizedLabel)
                                                    ? $nativeLabel . ', ' . $localeRow->code
                                                    : (string) $localeRow->code;

                                            $label = $localizedLabel . ' (' . $labelSuffix . ')';
                                        @endphp

                                        <flux:select.option value="{{ $localeRow->code }}">
                                            {{ $label }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:input.group>
                        </flux:field>
                    </div>

                    <div class="lg:col-span-2 lg:flex lg:items-end">
                        @if ($canToggleSelectedPrimaryLanguage)
                            <x-ui.button.activate-deactivate
                                :active="$isSelectedPrimaryLanguageActive"
                                :language="$selectedPrimaryLanguageLabel"
                                wire:click="toggleAvailableLocale('{{ $selectedPrimaryLanguageCode }}')"
                            />
                        @endif
                    </div>
                </div>

                <flux:error name="selectedPrimaryLanguageId" />
            </div>

        </div>

        {{-- Sub-Languages --}}
        <div class="mt-5 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
            @php
                $subLocaleCodes = $subLocaleRows
                    ->pluck('code')
                    ->map(static fn(string $code): string => strtolower($code))
                    ->all();
                $selectedSubLocaleCodesNormalized = collect($selectedSubLocaleCodes)
                    ->map(static fn(string $code): string => strtolower($code))
                    ->all();

                $allSubLocalesSelected =
                    $subLocaleCodes !== [] &&
                    count(array_intersect($subLocaleCodes, $selectedSubLocaleCodesNormalized)) ===
                        count($subLocaleCodes);
            @endphp

            <x-ui.headers.card
                :title="__('Sub languages')"
                :description="__(
                    'Select the entries you want from the sub-languages listed below that correspond to the main language.',
                )"
            >
                @if ($selectedPrimaryLanguageCode !== '' && $subLocaleRows->isNotEmpty())
                    <flux:button
                        class="w-32 hover:cursor-pointer"
                        type="button"
                        size="sm"
                        variant="filled"
                        wire:click="toggleAllSelectedSubLocales"
                    >
                        {{ $allSubLocalesSelected ? __('Deselect all') : __('Select all') }}
                    </flux:button>
                @endif
            </x-ui.headers.card>

            @if ($selectedPrimaryLanguageCode !== '')
                @if ($subLocaleRows->isEmpty())
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('No sub languages are available for the selected primary language.') }}
                    </div>
                @else
                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($subLocaleRows as $subLocaleRow)
                            @php
                                $label =
                                    $subLocaleRow->native_display_name ?:
                                    $subLocaleRow->display_name ?:
                                    strtoupper($subLocaleRow->code);

                                $isSelected = in_array(
                                    strtolower($subLocaleRow->code),
                                    $selectedSubLocaleCodesNormalized,
                                    true,
                                );
                            @endphp

                            <div
                                class="min-w-58 relative flex items-center rounded-md bg-zinc-50/50 px-6 py-2 dark:bg-zinc-800/50">

                                <flux:field
                                    class="w-full items-center pr-10"
                                    variant="inline"
                                >
                                    <x-ui.tooltip.trigger
                                        class="inline-flex w-full items-center"
                                        :title="$label"
                                        field="sub-locale-{{ $subLocaleRow->code }}"
                                        :text="__('Toggle the availability of this sub-language for the App/UI.')"
                                    >
                                        <flux:switch
                                            class="switch-colored mr-3 hover:cursor-pointer"
                                            wire:key="sub-locale-switch-{{ strtolower($subLocaleRow->code) }}"
                                            :checked="$isSelected"
                                            align="right"
                                            wire:change="toggleSelectedSubLocale('{{ $subLocaleRow->code }}')"
                                        />

                                        <flux:label class="text-sm opacity-70 hover:cursor-pointer">
                                            <span>
                                                <span class="block font-medium text-zinc-800 dark:text-zinc-100">
                                                    {{ $label }}
                                                </span>

                                                <span
                                                    class="inline-flex items-center gap-2 font-mono text-xs text-zinc-500 dark:text-zinc-400"
                                                >
                                                    <span>{{ $subLocaleRow->code }}</span>
                                                </span>
                                            </span>
                                        </flux:label>
                                    </x-ui.tooltip.trigger>

                                </flux:field>

                                <x-ui.country.flag-locale
                                    class="mr-3"
                                    :locale="$subLocaleRow->code"
                                    size="md"
                                />

                                <flux:badge
                                    class="pointer-events-none absolute -right-1 -top-1 z-10 inline-flex h-6 w-6 items-center justify-center rounded-full p-0 text-xs font-semibold tabular-nums leading-none"
                                    size="sm"
                                    variant="subtle"
                                >
                                    {{ $loop->iteration }}
                                </flux:badge>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>

        <div class="mt-5 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
            <x-ui.headers.card
                :title="__('Global App / UI language')"
                :description="__(
                    'Enable the language and any sub-languages you may have enabled above that should apply globally to the APP/UI. The order of the selected languages, after activating a new language, will reorder the buttons!',
                )"
            />

            <div class="grid grid-cols-1 items-center gap-2 md:grid-cols-6">
                @foreach ($localeRows as $localeRow)
                    @continue(!in_array($localeRow->code, $availableLocales, true))

                    @php
                        $localeCodeNormalized = strtolower($localeRow->code);
                        $label =
                            $localeRow->native_display_name ?: $localeRow->display_name ?: strtoupper($localeRow->code);

                        $subLocaleStats = $subLocaleStatsByPrimary[$localeCodeNormalized] ?? [
                            'total' => 0,
                            'selected' => 0,
                        ];
                        $subLocaleAllCount = (int) ($subLocaleStats['total'] ?? 0);
                        $subLocaleSelectedCount = (int) ($subLocaleStats['selected'] ?? 0);

                        if ($localeCodeNormalized === strtolower($selectedPrimaryLanguageCode)) {
                            $subLocaleAllCount = count($subLocaleCodes);
                            $subLocaleSelectedCount = count(
                                array_intersect($subLocaleCodes, $selectedSubLocaleCodesNormalized),
                            );
                        }
                    @endphp

                    <flux:button.group class="flex w-full">
                        <x-ui.tooltip.trigger
                            :title="$label"
                            field="locale-{{ $localeRow->code }}-sub-locale-stats"
                            :text="__('Number of sub-languages available for this primary language.')"
                        >
                            <flux:button
                                class="w-12 justify-center tabular-nums"
                                type="button"
                                size="sm"
                                :variant="$locale === $localeRow->code ? 'primary' : 'filled'"
                                wire:click="selectPrimaryLanguage('{{ $localeRow->code }}')"
                            >
                                {{ $subLocaleAllCount }}
                            </flux:button>
                        </x-ui.tooltip.trigger>

                        <flux:separator vertical />

                        <x-ui.tooltip.trigger
                            :title="$label"
                            field="locale-{{ $localeRow->code }}"
                            :text="__('The country- / language-flag of the global available App/UI language. ')"
                        >

                            <flux:button
                                class="w-12 justify-center"
                                size="sm"
                                :variant="$locale === $localeRow->code ? 'primary' : 'filled'"
                                wire:click="setLocaleAndSelectPrimary('{{ $localeRow->code }}')"
                            >
                                <x-ui.locale.flag
                                    :locale="$localeRow->code"
                                    size="lg"
                                />
                            </flux:button>
                        </x-ui.tooltip.trigger>

                        <flux:separator vertical />

                        <x-ui.tooltip.trigger
                            :title="$label"
                            field="locale-{{ $localeRow->code }}-label"
                            :text="__(
                                'Select this primary language to manage its sub-languages and activation state.',
                            )"
                        >
                            <flux:button
                                class="min-w-0 flex-1 justify-start overflow-hidden"
                                size="sm"
                                :variant="$locale === $localeRow->code ? 'primary' : 'filled'"
                                wire:click="selectPrimaryLanguage('{{ $localeRow->code }}')"
                            >
                                <span class="block w-full overflow-hidden text-ellipsis text-left">
                                    {{ $label }}
                                </span>
                            </flux:button>
                        </x-ui.tooltip.trigger>

                        <flux:separator vertical />

                        <x-ui.tooltip.trigger
                            :title="$label"
                            field="locale-{{ $localeRow->code }}-sub-locale-stats"
                            :text="__('The number of sub-languages selected for this primary language.')"
                        >
                            <flux:button
                                class="w-12 justify-center tabular-nums"
                                type="button"
                                size="sm"
                                :variant="$locale === $localeRow->code ? 'primary' : 'filled'"
                                wire:click="selectPrimaryLanguage('{{ $localeRow->code }}')"
                            >
                                {{ $subLocaleSelectedCount }}
                            </flux:button>
                        </x-ui.tooltip.trigger>
                    </flux:button.group>
                @endforeach
            </div>
        </div>

        <div class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('This setting defines the global default App/UI language. User-specific language settings may override it later.') }}
        </div>
    </div>
</flux:card>
