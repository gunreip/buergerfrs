{{-- resources/views/components/management/people/edit-person/sections/⚡international.blade.php --}}

@php
    $emptyValue = __('Not set');
    $nationalityLabel = $selectedNationalityOptions->isNotEmpty()
        ? $selectedNationalityOptions
            ->map(fn($country) => ($country->native_name ?: $country->name) . ' (' . $country->iso2 . ')')
            ->implode(', ')
        : $emptyValue;
    $languageLabel = $selectedLanguageOptions->isNotEmpty()
        ? $selectedLanguageOptions
            ->map(
                fn($language) => ($language->native_name ?: $language->name) .
                    ' (' .
                    ($language->iso639_1 ?: $language->iso639_3) .
                    ')',
            )
            ->implode(', ')
        : $emptyValue;
@endphp

<flux:card>
    <x-ui.headers.card
        :title="__('Person International Information')"
        :description="__('Nationalities and languages assigned to this person.')"
    />

    <div class="space-y-4">

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">

            {{-- Country Field/Select --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.flag />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Nationality')"
                                :text="__(
                                    'Select the nationality or nationalities of the person. This information is important for identification and legal purposes.',
                                )"
                            >
                                {{ __('Nationality') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'primaryNationalityCountryId')
                            <flux:select
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-primary-nationality"
                                variant="listbox"
                                multiple
                                searchable
                                clearable
                                wire:model.live="primaryNationalityCountryId"
                                placeholder="{{ __('Please select') }}"
                            >
                                @foreach ($nationalityCountryOptions as $country)
                                    <flux:select.option value="{{ (string) $country->id }}">
                                        <x-ui.country.flag
                                            class="mr-2"
                                            size="lg"
                                            :country="$country->iso2"
                                        />
                                        {{ $country->native_name ?: $country->name }} ({{ $country->iso2 }})
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center gap-2 rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                @if ($selectedNationalityOptions->isNotEmpty())
                                    <span class="flex shrink-0 items-center -space-x-1">
                                        @foreach ($selectedNationalityOptions->take(3) as $country)
                                            <x-ui.country.flag
                                                class="mr-2"
                                                :country="$country->iso2"
                                            />
                                            <span class="mr-2">{{ $country->native_name ?: $country->name }}
                                                ({{ $country->iso2 }})
                                            </span>
                                        @endforeach
                                    </span>
                                @endif

                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="primaryNationalityCountryId"
                            :title="__('Edit Nationality')"
                            :text="__(
                                    'Edit the nationality or nationalities of the person. This information is important for identification and legal purposes.',
                                )"
                            :changed="$this->isEditingFieldChanged('primaryNationalityCountryId')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="primaryNationalityCountryId" />
            </flux:field>

            {{-- Language Field/Select --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.language />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('admin.translation_list.meta.language')"
                                :text="__(
                                    'Edit the primary language or languages of the person. This information is important for communication and localization purposes.',
                                )"
                            >
                                {{ __('admin.translation_list.meta.language') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'primaryLanguageId')
                            <flux:select
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-primary-language"
                                variant="listbox"
                                multiple
                                searchable
                                clearable
                                wire:model.live="primaryLanguageId"
                                placeholder="{{ __('Please select') }}"
                            >
                                @foreach ($languageOptions as $language)
                                    <flux:select.option value="{{ (string) $language->id }}">
                                        <x-ui.locale.flag
                                            class="mr-2"
                                            :locale="$language->iso639_1 ?: $language->iso639_3"
                                        />
                                        {{ $language->native_name ?: $language->name }}
                                        ({{ $language->iso639_1 ?: $language->iso639_3 }})
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center gap-2 rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                @if ($selectedLanguageOptions->isNotEmpty())
                                    <span class="flex shrink-0 items-center -space-x-1">
                                        @foreach ($selectedLanguageOptions->take(3) as $language)
                                            <x-ui.locale.flag
                                                class="mr-2"
                                                :locale="$language->iso639_1 ?: $language->iso639_3"
                                            />
                                            <span class="mr-2">{{ $language->native_name ?: $language->name }}
                                                ({{ $language->iso639_1 ?: $language->iso639_3 }})
                                            </span>
                                        @endforeach
                                    </span>
                                @endif

                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="primaryLanguageId"
                            :title="__('Edit Language')"
                            :text="__(
                                    'Edit the primary language or languages of the person. This information is important for communication and localization purposes.',
                                )"
                            :changed="$this->isEditingFieldChanged('primaryLanguageId')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="primaryLanguageId" />
            </flux:field>
        </div>

        @if ($selectedNationalityOptions->isNotEmpty() || $selectedLanguageOptions->isNotEmpty())
            <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">

                @if ($selectedNationalityOptions->isNotEmpty())
                    <div class="space-y-2">
                        <div
                            class="flex items-center gap-2 text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">
                            <flux:icon.flag class="size-5" />
                            {{ __('Selected nationalities') }}
                        </div>

                        <div
                            class="divide-y divide-zinc-100 rounded-lg border border-zinc-200 bg-white dark:divide-white/10 dark:border-white/10 dark:bg-white/5">
                            @foreach ($selectedNationalityOptions as $index => $country)
                                <div class="flex items-center gap-2 px-3 py-2 text-sm text-zinc-800 dark:text-zinc-100">
                                    <x-ui.country.flag
                                        size="sm"
                                        :country="$country->iso2"
                                    />
                                    <span class="font-medium">{{ $country->native_name ?: $country->name }}</span>
                                    <span class="text-zinc-500 dark:text-zinc-400">({{ $country->iso2 }})</span>
                                    @if ($index === 0)
                                        <flux:badge
                                            class="ms-auto"
                                            color="sky"
                                            size="sm"
                                        >
                                            {{ __('Primary') }}
                                        </flux:badge>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($selectedLanguageOptions->isNotEmpty())
                    <div class="space-y-2">
                        <div
                            class="flex items-center gap-2 text-xs font-medium uppercase text-zinc-500 dark:text-zinc-400">
                            <flux:icon.language class="size-5" />
                            {{ __('Selected languages') }}
                        </div>

                        <div
                            class="divide-y divide-zinc-100 rounded-lg border border-zinc-200 bg-white dark:divide-white/10 dark:border-white/10 dark:bg-white/5">
                            @foreach ($selectedLanguageOptions as $index => $language)
                                <div class="px-3 py-2">
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-sm">
                                        <x-ui.locale.flag :locale="$language->iso639_1 ?: $language->iso639_3" />
                                        <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                            {{ $language->native_name ?: $language->name }}
                                        </span>
                                        <span class="text-zinc-500 dark:text-zinc-400">
                                            ({{ $language->iso639_1 ?: $language->iso639_3 }})
                                        </span>
                                        @if ($index === 0)
                                            <flux:badge
                                                color="sky"
                                                size="sm"
                                            >
                                                {{ __('Primary') }}
                                            </flux:badge>
                                        @endif

                                        <div class="ms-auto flex flex-wrap items-center gap-2">
                                            <x-ui.checkbox.icon
                                                id="edit-person-language-{{ $language->id }}-speaking"
                                                icon="speech"
                                                :label="__('Speaking')"
                                                :title="($index === 0
                                                    ? __('Primary Language Speaking')
                                                    : __('Secondary Language Speaking')) .
                                                    ': ' .
                                                    ($language->native_name ?: $language->name)"
                                                :text="__(
                                                    'Select this when the person can speak this language in everyday communication.',
                                                )"
                                                :disabled="$editingField !== 'primaryLanguageId'"
                                                wire:model.live="languageAbilities.{{ $language->id }}.speaking"
                                            />
                                            <x-ui.checkbox.icon
                                                id="edit-person-language-{{ $language->id }}-reading"
                                                icon="book-open-text"
                                                :label="__('Reading')"
                                                :title="($index === 0
                                                    ? __('Primary Language Reading')
                                                    : __('Secondary Language Reading')) .
                                                    ': ' .
                                                    ($language->native_name ?: $language->name)"
                                                :text="__(
                                                    'Select this when the person can read and understand written content in this language.',
                                                )"
                                                :disabled="$editingField !== 'primaryLanguageId'"
                                                wire:model.live="languageAbilities.{{ $language->id }}.reading"
                                            />
                                            <x-ui.checkbox.icon
                                                id="edit-person-language-{{ $language->id }}-writing"
                                                icon="pen-line"
                                                :label="__('Writing')"
                                                :title="($index === 0
                                                    ? __('Primary Language Writing')
                                                    : __('Secondary Language Writing')) .
                                                    ': ' .
                                                    ($language->native_name ?: $language->name)"
                                                :text="__(
                                                    'Select this when the person can write messages or documents in this language.',
                                                )"
                                                :disabled="$editingField !== 'primaryLanguageId'"
                                                wire:model.live="languageAbilities.{{ $language->id }}.writing"
                                            />
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

    </div>
</flux:card>
