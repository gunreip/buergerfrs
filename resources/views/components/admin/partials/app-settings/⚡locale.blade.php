{{-- resources/views/components/admin/partials/app-settings/⚡locale.blade.php --}}

<flux:card
    class="mt-6"
    x-data="{}"
    x-on:tooltip-locale-deactivate-confirm.window="$wire.armLocaleDeactivation($event.detail.locale ?? '')"
>
    <div class="flex items-start justify-between gap-4">
        <x-ui.headers.card
            :title="__('admin.app_settings.locale.application_languages')"
            :description="__('admin.app_settings.locale.configure_which_languages_are_available_for_the_app_ui_and_choose_the_global_def',
            )"
        />

        <flux:badge
            variant="subtle"
            color="sky"
            size="lg"
        >
            {{ __('admin.app_settings.locale.current') }}:
            <x-ui.locale.flag
                class="ml-2"
                size="lg"
                :locale="$locale"
            />
        </flux:badge>
    </div>

    <div class="mt-6 space-y-6">

        {{-- Narrow down radio-buttons --}}
        <div class="grid grid-cols-1 gap-3 md:grid-cols-5 md:items-end">
            <flux:field class="md:col-span-2">
                <x-ui.tooltip.trigger
                    :title="__('admin.app_settings.locale.top_languages')"
                    field="primaryLanguageScope"
                    :text="__('admin.app_settings.locale.select_the_top_languages_by_their_usage')"
                >
                    <flux:label
                        class="mb-3 text-sm font-semibold"
                        for="primary-language-scope"
                    >
                        {{-- {{ __('admin.app_settings.locale.top_languages') }} --}}
                        {{ __('admin.app_settings.locale.narrow_down_your_selection') }}
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
                $canActivateSelectedPrimaryLanguage =
                    $selectedPrimaryLanguageCode !== '' && !$isSelectedPrimaryLanguageActive;
                $availablePrimaryLocaleCodesNormalized = array_map(
                    static fn(string $localeCode): string => strtolower($localeCode),
                    $availableLocales,
                );
            @endphp

            {{-- Select Primary language --}}
            <div class="md:col-span-3">
                <div class="grid grid-cols-1 gap-3 lg:grid-cols-5">
                    <div class="lg:col-span-3">
                        <flux:field>
                            <x-ui.tooltip.trigger
                                :title="__('admin.app_settings.locale.select_available_languages')"
                                field="selectedPrimaryLanguageCode"
                                :text="__('admin.app_settings.locale.select_which_languages_should_be_available_for_the_app_ui_you_can_choose_from_al',
                                )"
                            >
                                <flux:label
                                    class="mb-3 text-sm font-semibold"
                                    for="available-locales-select"
                                >
                                    {{ __('admin.app_settings.locale.primary_languages') }}
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
                                    placeholder="{{ __('admin.app_settings.locale.please_select_a_primary_language') }}"
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
                                            $isAlreadyAvailable = in_array(
                                                strtolower((string) $localeRow->code),
                                                $availablePrimaryLocaleCodesNormalized,
                                                true,
                                            );
                                        @endphp

                                        <flux:select.option
                                            value="{{ $localeRow->code }}"
                                            :disabled="$isAlreadyAvailable"
                                        >
                                            {{ $label }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:input.group>
                        </flux:field>
                    </div>

                    <div class="lg:col-span-2 lg:flex lg:items-end">
                        @if ($canActivateSelectedPrimaryLanguage)
                            <x-ui.button.activate-deactivate
                                :active="false"
                                :language="$selectedPrimaryLanguageLabel"
                                wire:click="activateAvailableLocale('{{ $selectedPrimaryLanguageCode }}')"
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
                :title="__('admin.app_settings.locale.sub_languages')"
                :description="__('admin.app_settings.locale.select_the_entries_you_want_from_the_sub_languages_listed_below_that_correspond_',
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
                        {{ $allSubLocalesSelected ? __('admin.app_settings.locale.deselect_all') : __('admin.app_settings.locale.select_all') }}
                    </flux:button>
                @endif
            </x-ui.headers.card>

            @if ($selectedPrimaryLanguageCode !== '')
                @if ($subLocaleRows->isEmpty())
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('admin.app_settings.locale.no_sub_languages_are_available_for_the_selected_primary_language') }}
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
                                        :text="__('admin.app_settings.locale.toggle_the_availability_of_this_sub_language_for_the_app_ui')"
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
                :title="__('admin.app_settings.locale.global_app_ui_language')"
                :description="__('admin.app_settings.locale.enable_the_language_and_any_sub_languages_you_may_have_enabled_above_that_should',
                )"
            />

            <div class="grid grid-cols-1 items-center gap-2 md:grid-cols-6">
                @foreach ($localeRows as $localeRow)
                    @continue(!in_array($localeRow->code, $availableLocales, true))

                    @php
                        $localeCodeNormalized = strtolower($localeRow->code);
                        $label =
                            $localeRow->native_display_name ?: $localeRow->display_name ?: strtoupper($localeRow->code);
                        $canDeactivateFromGlobal =
                            !in_array($localeCodeNormalized, ['en', 'de'], true) &&
                            $localeCodeNormalized !== strtolower($locale);
                        $isPendingDeactivate =
                            $canDeactivateFromGlobal &&
                            strtolower((string) $pendingLocaleDeactivationCode) === $localeCodeNormalized;

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

                    {{-- Button group with flag, label and sub-locale stats --}}
                    <flux:button.group class="flex w-full">
                        {{-- Button sublocale overall --}}
                        <x-ui.tooltip.trigger
                            :title="$label"
                            field="locale-{{ $localeRow->code }}-sub-locale-stats"
                            :text="__('admin.app_settings.locale.number_of_sub_languages_available_for_this_primary_language')"
                        >
                            <flux:button
                                class="w-12 justify-center tabular-nums"
                                type="button"
                                size="sm"
                                :variant="$locale === $localeRow->code ? 'primary' : 'filled'"
                                wire:click="setLocaleAndSelectPrimary('{{ $localeRow->code }}')"
                            >
                                {{ $subLocaleAllCount }}
                            </flux:button>
                        </x-ui.tooltip.trigger>

                        <flux:separator vertical />

                        {{-- Button locale flag --}}
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

                        {{-- Button locale label and select primary language (which also shows sub-locale management and stats) --}}
                        <x-ui.tooltip.trigger
                            :title="$label"
                            field="locale-{{ $localeRow->code }}-label"
                            :text="__('admin.app_settings.locale.select_this_primary_language_to_manage_its_sub_languages_and_activation_state',
                            )"
                        >
                            <flux:button
                                class="min-w-0 flex-1 justify-start overflow-hidden"
                                size="sm"
                                :variant="$locale === $localeRow->code ? 'primary' : 'filled'"
                                wire:click="setLocaleAndSelectPrimary('{{ $localeRow->code }}')"
                            >
                                <span class="block w-full overflow-hidden text-ellipsis text-left">
                                    {{ $label }}
                                </span>
                            </flux:button>
                        </x-ui.tooltip.trigger>

                        <flux:separator vertical />

                        {{-- Button sublocale active summary --}}
                        @if ($isPendingDeactivate)
                            <flux:button
                                class="w-12 justify-center hover:cursor-pointer"
                                type="button"
                                size="sm"
                                icon="x-mark"
                                variant="danger"
                                wire:click="deactivateAvailableLocale('{{ $localeRow->code }}')"
                            ></flux:button>
                        @else
                            <x-ui.tooltip.trigger
                                :title="$label"
                                field="locale-{{ $localeRow->code }}-sub-locale-selected"
                                :text="__('admin.app_settings.locale.the_number_of_sub_languages_selected_for_this_primary_language')"
                                :action-text="$canDeactivateFromGlobal
                                    ? __('admin.app_settings.locale.do_you_really_want_to_deactivate_this_language')
                                    : null"
                                :action="$canDeactivateFromGlobal
                                    ? [
                                        'label' => 'Yes',
                                        'event' => 'tooltip-locale-deactivate-confirm',
                                        'detail' => ['locale' => $localeRow->code],
                                    ]
                                    : null"
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
                        @endif
                    </flux:button.group>
                @endforeach
            </div>
        </div>

        <div class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('admin.app_settings.locale.this_setting_defines_the_global_default_app_ui_language_user_specific_language_s') }}
        </div>
    </div>
</flux:card>
