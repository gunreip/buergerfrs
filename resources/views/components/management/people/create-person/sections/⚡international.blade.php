{{-- resources/views/components/management/people/create-person/sections/⚡international.blade.php --}}

<flux:card>
    <div class="space-y-4">

        <div class="grid gap-4 md:grid-cols-2">

            <flux:field class="col-span-2 mb-3">
                <div class="flex items-start justify-between gap-4">
                    <flux:heading size="lg">
                        <span class="border-b-1 border-zinc-800/10 pb-2 pr-4 dark:border-white/20">
                            <flux:icon.globe-alt class="mr-2 inline-block" />
                            {{ __('Person International Information') }}
                        </span>
                    </flux:heading>

                    <div class="flex flex-wrap items-center justify-end gap-2">
                        <x-ui.badge.created-password :password="$generatedPassword" />

                        <x-ui.badge.test-data :show="$isTestData" />
                    </div>
                </div>
            </flux:field>

            {{-- Primary nationality --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Primary nationality') }}"
                    field="primaryNationalityCountryId"
                    text="{{ __('Please select the primary nationality for the person. This is important for correctly identifying the person\'s nationality and for any nationality-specific validations.') }}"
                    :required="$this->isRequiredField('primaryNationalityCountryId')"
                >
                    <flux:label for="create-person-primary-nationality">
                        {{ __('Nationality') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('primaryNationalityCountryId')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.flag />
                    </flux:input.group.prefix>

                    <flux:select
                        id="create-person-primary-nationality"
                        name="primaryNationalityCountryId"
                        variant="listbox"
                        multiple
                        wire:model.live="primaryNationalityCountryId"
                        placeholder="{{ __('Please select') }}"
                    >
                        @foreach ($nationalityCountryOptions as $country)
                            <flux:select.option :value="$country->id">
                                <x-ui.country.flag
                                    class="mr-2"
                                    size="lg"
                                    :country="$country->iso2"
                                />
                                {{ $country->native_name ?: $country->name }} ({{ $country->iso2 }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:input.group>

                <flux:error name="primaryNationalityCountryId" />
            </flux:field>

            {{-- Primary language --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Primary language') }}"
                    field="primaryLanguageId"
                    text="{{ __('Please select the primary language for the person. This is important for correctly identifying the person\'s primary language and for any language-specific validations.') }}"
                    :required="$this->isRequiredField('primaryLanguageId')"
                >
                    <flux:label for="create-person-primary-language">
                        {{ __('admin.translation_list.meta.language') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('primaryLanguageId')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.language />
                    </flux:input.group.prefix>

                    <flux:select
                        id="create-person-primary-language"
                        name="primaryLanguageId"
                        variant="listbox"
                        multiple
                        wire:model.live="primaryLanguageId"
                        placeholder="{{ __('Please select') }}"
                    >
                        @foreach ($languageOptions as $language)
                            <flux:select.option :value="$language->id">
                                <x-ui.locale.flag
                                    class="mr-2"
                                    :locale="$language->iso639_1 ?: $language->iso639_3"
                                />
                                {{ $language->native_name ?: $language->name }}
                                @if ($language->iso639_1)
                                    ({{ $language->iso639_1 }})
                                @else
                                    ({{ $language->iso639_3 }})
                                @endif
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:input.group>

                <flux:error name="primaryLanguageId" />
            </flux:field>
        </div>

        @if ($selectedNationalityOptions->isNotEmpty() || $selectedLanguageOptions->isNotEmpty())
            <div class="grid gap-4 md:grid-cols-2">
                <div class="h-full">
                    @if ($selectedNationalityOptions->isNotEmpty())
                        @php
                            $primaryNationality = $selectedNationalityOptions->first();
                            $furtherNationalities = $selectedNationalityOptions->skip(1);
                        @endphp

                        <flux:card class="h-full space-y-4">
                            <div class="flex items-center gap-2 text-sm font-medium text-zinc-800 dark:text-zinc-100">
                                <flux:icon.flag class="size-4 text-zinc-500 dark:text-zinc-400" />
                                {{ __('Selected nationalities') }}
                            </div>

                            <div class="space-y-2">
                                <div
                                    class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                    <flux:icon.star class="size-4" />
                                    {{ __('Primary nationality') }}
                                </div>

                                <div
                                    class="flex items-center gap-2 rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-sm text-sky-950 dark:border-sky-400/20 dark:bg-sky-400/10 dark:text-sky-100">
                                    <x-ui.country.flag
                                        size="lg"
                                        :country="$primaryNationality->iso2"
                                    />
                                    <span
                                        class="Text-sm font-medium">{{ $primaryNationality->native_name ?: $primaryNationality->name }}</span>
                                    <span
                                        class="text-sky-700 dark:text-sky-300">({{ $primaryNationality->iso2 }})</span>
                                </div>
                            </div>

                            @if ($furtherNationalities->isNotEmpty())
                                <div class="space-y-2">
                                    <div
                                        class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                        <flux:icon.flag class="size-4" />
                                        {{ __('Further nationalities') }}
                                    </div>

                                    <div
                                        class="divide-y divide-zinc-100 rounded-lg border border-zinc-200 bg-white dark:divide-white/10 dark:border-white/10 dark:bg-white/5">
                                        @foreach ($furtherNationalities as $country)
                                            <div
                                                class="flex items-center gap-2 px-3 py-2 text-sm text-zinc-800 dark:text-zinc-100">
                                                <x-ui.country.flag
                                                    size="lg"
                                                    :country="$country->iso2"
                                                />
                                                <span
                                                    class="font-medium">{{ $country->native_name ?: $country->name }}</span>
                                                <span
                                                    class="text-zinc-500 dark:text-zinc-400">({{ $country->iso2 }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </flux:card>
                    @endif
                </div>

                <div class="h-full">
                    @if ($selectedLanguageOptions->isNotEmpty())
                        @php
                            $primaryLanguage = $selectedLanguageOptions->first();
                            $secondaryLanguages = $selectedLanguageOptions->skip(1);
                        @endphp

                        <flux:card class="h-full space-y-4">
                            <div class="flex items-center gap-2 text-sm font-medium text-zinc-800 dark:text-zinc-100">
                                <flux:icon.language class="size-4 text-zinc-500 dark:text-zinc-400" />
                                {{ __('Selected languages') }}
                            </div>

                            <div class="space-y-2">
                                <div
                                    class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                    <flux:icon.star class="size-4" />
                                    {{ __('Primary language') }}
                                </div>

                                <div
                                    class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 dark:border-sky-400/20 dark:bg-sky-400/10">
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                                        <x-ui.locale.flag
                                            :locale="$primaryLanguage->iso639_1 ?: $primaryLanguage->iso639_3"
                                            size="lg"
                                        />
                                        <span class="font-medium text-sky-950 dark:text-sky-100">
                                            {{ $primaryLanguage->native_name ?: $primaryLanguage->name }}
                                        </span>
                                        <span class="text-sm text-sky-700 dark:text-sky-300">
                                            ({{ $primaryLanguage->iso639_1 ?: $primaryLanguage->iso639_3 }})
                                        </span>

                                        <div class="ms-auto flex flex-wrap items-center gap-2 text-sm">

                                            {{-- Primary Language Speaking --}}
                                            <x-ui.checkbox.icon
                                                id="create-person-language-{{ $primaryLanguage->id }}-speaking"
                                                icon="speech"
                                                :label="__('Speaking')"
                                                :title="__('Primary Language Speaking') .
                                                    ': ' .
                                                    ($primaryLanguage->native_name ?: $primaryLanguage->name)"
                                                :text="__(
                                                    'Select this when the person can speak this language in everyday communication.',
                                                )"
                                                wire:model.live="languageAbilities.{{ $primaryLanguage->id }}.speaking"
                                            />

                                            {{-- Primary Language Reading --}}
                                            <x-ui.checkbox.icon
                                                id="create-person-language-{{ $primaryLanguage->id }}-reading"
                                                icon="book-open-text"
                                                :label="__('Reading')"
                                                :title="__('Primary Language Reading') .
                                                    ': ' .
                                                    ($primaryLanguage->native_name ?: $primaryLanguage->name)"
                                                :text="__(
                                                    'Select this when the person can read and understand written content in this language.',
                                                )"
                                                wire:model.live="languageAbilities.{{ $primaryLanguage->id }}.reading"
                                            />

                                            {{-- Primary Language Writing --}}
                                            <x-ui.checkbox.icon
                                                id="create-person-language-{{ $primaryLanguage->id }}-writing"
                                                icon="pen-line"
                                                :label="__('Writing')"
                                                :title="__('Primary Language Writing') .
                                                    ': ' .
                                                    ($primaryLanguage->native_name ?: $primaryLanguage->name)"
                                                :text="__(
                                                    'Select this when the person can write messages or documents in this language.',
                                                )"
                                                wire:model.live="languageAbilities.{{ $primaryLanguage->id }}.writing"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($secondaryLanguages->isNotEmpty())
                                <div class="space-y-2">
                                    <div
                                        class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                        <flux:icon.language class="size-4" />
                                        {{ __('Secondary languages') }}
                                    </div>

                                    <div
                                        class="divide-y divide-zinc-100 rounded-lg border border-zinc-200 bg-white dark:divide-white/10 dark:border-white/10 dark:bg-white/5">
                                        @foreach ($secondaryLanguages as $language)
                                            <div class="px-3 py-2">
                                                <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                                                    <x-ui.locale.flag
                                                        :locale="$language->iso639_1 ?: $language->iso639_3"
                                                        size="lg"
                                                    />
                                                    <span class="font-medium text-zinc-800 dark:text-zinc-100">
                                                        {{ $language->native_name ?: $language->name }}
                                                    </span>
                                                    <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                                        ({{ $language->iso639_1 ?: $language->iso639_3 }})
                                                    </span>

                                                    <div class="ms-auto flex flex-wrap items-center gap-2 text-sm">

                                                        {{-- Secondary Language Speaking --}}
                                                        <x-ui.checkbox.icon
                                                            id="create-person-language-{{ $language->id }}-speaking"
                                                            icon="speech"
                                                            :label="__('Speaking')"
                                                            :title="__('Secondary Language Speaking') .
                                                                ': ' .
                                                                ($language->native_name ?: $language->name)"
                                                            :text="__(
                                                                'Select this when the person can speak this language in everyday communication.',
                                                            )"
                                                            wire:model.live="languageAbilities.{{ $language->id }}.speaking"
                                                        />

                                                        {{-- Secondary Language Reading --}}
                                                        <x-ui.checkbox.icon
                                                            id="create-person-language-{{ $language->id }}-reading"
                                                            icon="book-open-text"
                                                            :label="__('Reading')"
                                                            :title="__('Secondary Language Reading') .
                                                                ': ' .
                                                                ($language->native_name ?: $language->name)"
                                                            :text="__(
                                                                'Select this when the person can read and understand written content in this language.',
                                                            )"
                                                            wire:model.live="languageAbilities.{{ $language->id }}.reading"
                                                        />

                                                        {{-- Secondary Language Writing --}}
                                                        <x-ui.checkbox.icon
                                                            id="create-person-language-{{ $language->id }}-writing"
                                                            icon="pen-line"
                                                            :label="__('Writing')"
                                                            :title="__('Secondary Language Writing') .
                                                                ': ' .
                                                                ($language->native_name ?: $language->name)"
                                                            :text="__(
                                                                'Select this when the person can write messages or documents in this language.',
                                                            )"
                                                            wire:model.live="languageAbilities.{{ $language->id }}.writing"
                                                        />

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </flux:card>
                    @endif
                </div>
            </div>
        @endif
    </div>
</flux:card>
