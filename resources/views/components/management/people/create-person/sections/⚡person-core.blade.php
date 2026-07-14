{{-- resources/views/components/management/people/create-person/sections/⚡person-core.blade.php --}}

@php
    $salutationIcons = [
        'mr' => 'user',
        'mrs' => 'user',
        'mx' => 'circle-user-round',
    ];

    $genderIcons = [
        'female' => 'user',
        'male' => 'user',
        'diverse' => 'users',
        'unknown' => 'circle-user-round',
    ];
@endphp

<flux:card>
    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-3">

            <flux:field class="col-span-3 mb-3">
                <div class="flex items-start justify-between gap-4">
                    <flux:heading size="lg">
                        <span class="border-b-1 border-zinc-800/10 pb-2 pr-4 dark:border-white/20">
                            <flux:icon.circle-user-round class="mr-2 inline-block" />
                            {{ __('Person Core Information') }}
                        </span>
                    </flux:heading>

                    <div class="flex flex-wrap items-center justify-end gap-2">

                        <x-ui.badge.created-password :password="$generatedPassword" />

                        <x-ui.badge.test-data :show="$isTestData" />

                        <x-ui.tooltip.trigger
                            title="{{ __('Test data') }}"
                            field="isTestData"
                            text="{{ __('Marks this manually created person as test data so it can be filtered, seeded, or deleted separately from real records.') }}"
                            context="warning"
                        >
                            <flux:checkbox
                                id="create-person-is-test-data"
                                name="isTestData"
                                wire:model.live="isTestData"
                                label="{{ __('Test data') }}"
                            />
                        </x-ui.tooltip.trigger>
                    </div>
                </div>
            </flux:field>

            {{-- Radio Group Salutation --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Salutation') }}"
                    field="salutation"
                    text="{{ __('The salutation is used to address the person in a formal way. It is usually based on their title or position.') }}"
                    :required="$this->isRequiredField('salutation')"
                >
                    <flux:label for="create-person-salutation">
                        {{ __('Salutation') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('salutation')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.users />
                    </flux:input.group.prefix>

                    <flux:radio.group
                        class="w-full rounded-l-none"
                        id="create-person-salutation"
                        name="salutation"
                        :invalid="$errors->has('salutation')"
                        wire:model.live="salutation"
                        variant="segmented"
                    >
                        @foreach ($salutationOptions as $value => $label)
                            <flux:radio
                                class="font-semibold"
                                :value="$value"
                                :label="__($label)"
                                :icon="$salutationIcons[$value] ?? 'circle-user-round'"
                            />
                        @endforeach
                    </flux:radio.group>
                </flux:input.group>

                <flux:error name="salutation" />
            </flux:field>

            {{-- Gender --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Gender') }}"
                    field="gender"
                    text="{{ __('The gender is used to specify the person\'s gender. It is usually displayed in forms and reports.') }}"
                    :required="$this->isRequiredField('gender')"
                >
                    <flux:label for="create-person-gender">
                        {{ __('Gender') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('gender')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.users />
                    </flux:input.group.prefix>

                    <flux:radio.group
                        class="rounded-l-none"
                        id="create-person-gender"
                        name="gender"
                        :invalid="$errors->has('gender')"
                        wire:model.live="gender"
                        variant="segmented"
                    >
                        @foreach ($genderOptions as $value => $label)
                            <flux:radio
                                class="font-semibold"
                                :value="$value"
                                :label="__($label)"
                                :icon="$genderIcons[$value] ?? 'circle-user-round'"
                            />
                        @endforeach
                    </flux:radio.group>
                </flux:input.group>

                <flux:error name="gender" />
            </flux:field>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            {{-- Title --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Title') }}"
                    field="nameTitle"
                    text="{{ __('The title is an academic or professional title that the person holds. It is usually displayed before the name.') }}"
                    :required="$this->isRequiredField('nameTitle')"
                >
                    <flux:label for="create-person-name-title">
                        {{ __('Title') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('nameTitle')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.graduation-cap />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-name-title"
                        name="nameTitle"
                        type="text"
                        placeholder="{{ __('e.g. Dr., Prof. Dr.') }}"
                        autocomplete="new-password"
                        searchable
                        copyable
                        clearable
                        wire:model.live.debounce.500ms="nameTitle"
                    />
                </flux:input.group>

                <flux:error name="nameTitle" />
            </flux:field>

        </div>

        <div class="grid gap-4 md:grid-cols-3">

            {{-- First name --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('First name') }}"
                    field="firstName"
                    text="{{ __('The first name is the person\'s given name. It is usually displayed before the last name. The first name is the person\'s given name. It is usually displayed before the last name. The first name is the person\'s given name. It is usually displayed before the last name. The first name is the person\'s given name. It is usually displayed before the last name. The first name is the person\'s given name. It is usually displayed before the last name.') }}"
                    :required="$this->isRequiredField('firstName')"
                >
                    <flux:label for="create-person-first-name">
                        {{ __('First name') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('firstName')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.user />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-first-name"
                        name="firstName"
                        type="text"
                        wire:model.live.debounce.500ms="firstName"
                        autocomplete="new-password"
                        copyable
                        clearable
                        searchable
                    />
                </flux:input.group>

                <flux:error name="firstName" />
            </flux:field>

            {{-- Last name --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Last name') }}"
                    field="lastName"
                    text="{{ __('The last name is the family name or surname of the person. It is usually passed down from one generation to the next.') }}"
                    :required="$this->isRequiredField('lastName')"
                >
                    <flux:label for="create-person-last-name">
                        {{ __('Last name') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('lastName')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.user />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-last-name"
                        name="lastName"
                        type="text"
                        wire:model.live.debounce.500ms="lastName"
                        autocomplete="new-password"
                        copyable
                        clearable
                        searchable
                    />
                </flux:input.group>

                <flux:error name="lastName" />
            </flux:field>

            {{-- Birth name --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Birth name') }}"
                    field="birthName"
                    text="{{ __('The birth name is the name given to the person at birth. It may differ from their current legal name.') }}"
                    :required="$this->isRequiredField('birthName')"
                >
                    <flux:label for="create-person-birth-name">
                        {{ __('Birth name') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('birthName')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.user />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-birth-name"
                        name="birthName"
                        type="text"
                        wire:model.live.debounce.500ms="birthName"
                        autocomplete="new-password"
                        copyable
                        clearable
                        searchable
                    />
                </flux:input.group>

                <flux:error name="birthName" />
            </flux:field>

        </div>

        <div class="grid gap-4 md:grid-cols-3">

            {{-- Middle name --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Middle name') }}"
                    field="middleName"
                    text="{{ __('The middle name is an additional given name that the person may have. It is usually displayed between the first name and the last name.') }}"
                    :required="$this->isRequiredField('middleName')"
                >
                    <flux:label for="create-person-middle-name">
                        {{ __('Middle name') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('middleName')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.user />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-middle-name"
                        name="middleName"
                        type="text"
                        wire:model.live.debounce.500ms="middleName"
                        autocomplete="new-password"
                        copyable
                        clearable
                        searchable
                    />
                </flux:input.group>

                <flux:error name="middleName" />
            </flux:field>

            {{-- Preferred name --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Preferred name') }}"
                    field="preferredName"
                    text="{{ __('The preferred name is the name that the person prefers to be called. It may be different from their legal first name.') }}"
                    :required="$this->isRequiredField('preferredName')"
                >
                    <flux:label for="create-person-preferred-name">
                        {{ __('Preferred name') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('preferredName')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.user />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-preferred-name"
                        name="preferredName"
                        type="text"
                        wire:model.live.debounce.500ms="preferredName"
                        autocomplete="new-password"
                        copyable
                        clearable
                        searchable
                    />
                </flux:input.group>

                <flux:error name="preferredName" />
            </flux:field>

            {{-- Marital status --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Marital status') }}"
                    field="maritalStatus"
                    text="{{ __('The marital status indicates the legal relationship status of the person, such as single, married, or divorced.') }}"
                    :required="$this->isRequiredField('maritalStatus')"
                >
                    <flux:label for="create-person-marital-status">
                        {{ __('Marital status') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('maritalStatus')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.heart />
                    </flux:input.group.prefix>

                    <flux:select
                        id="create-person-marital-status"
                        name="maritalStatus"
                        variant="listbox"
                        searchable
                        copyable
                        clearable
                        wire:model.live="maritalStatus"
                        placeholder="{{ __('Please select') }}"
                    >
                        @foreach ($maritalStatusOptions as $value => $label)
                            <flux:select.option :value="$value">
                                {{ __($label) }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:input.group>

                <flux:error name="maritalStatus" />
            </flux:field>

        </div>

        <div class="grid gap-4 md:grid-cols-3">

            {{-- Birth country --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Birth country') }}"
                    field="birthCountryId"
                    text="{{ __('The birth country is the country where the person was born.') }}"
                    :required="$this->isRequiredField('birthCountryId')"
                >
                    <flux:label for="create-person-birth-country">
                        {{ __('Birth country') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('birthCountryId')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.globe-alt />
                    </flux:input.group.prefix>

                    <flux:select
                        id="create-person-birth-country"
                        name="birthCountryId"
                        variant="listbox"
                        searchable
                        copyable
                        clearable
                        wire:model.live="birthCountryId"
                        placeholder="{{ __('Please select') }}"
                    >
                        @foreach ($birthCountryOptions as $country)
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

                <flux:error name="birthCountryId" />
            </flux:field>

            {{-- Birth place --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Birth place') }}"
                    field="birthPlaceText"
                    text="{{ __('The birth place is the city or town where the person was born.') }}"
                    :required="$this->isRequiredField('birthPlaceText')"
                >
                    <flux:label for="create-person-birth-place">
                        {{ __('Birth place') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('birthPlaceText')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.map-pin />
                    </flux:input.group.prefix>

                    <flux:select
                        id="create-person-birth-place"
                        name="birthPlaceText"
                        variant="listbox"
                        wire:model.live="birthPlaceText"
                        autocomplete="new-password"
                        copyable
                        clearable
                        searchable
                        :disabled="$this->birthCountryId === null"
                    >
                        @foreach ($birthPlaceOptions as $birthPlaceOption)
                            <flux:select.option :value="$birthPlaceOption">
                                {{ $birthPlaceOption }}
                            </flux:select.option>
                        @endforeach

                        @if (filled($birthPlaceText) && !$birthPlaceOptions->containsStrict($birthPlaceText))
                            <flux:select.option :value="$birthPlaceText">
                                {{ $birthPlaceText }}
                            </flux:select.option>
                        @endif

                        <flux:select.option.create
                            min-length="1"
                            x-on:pointerdown="$wire.useCreatedBirthPlaceText($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '')"
                            x-on:click="$wire.useCreatedBirthPlaceText($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '')"
                        >
                            {{ __('Use entered birth place') }}
                        </flux:select.option.create>
                    </flux:select>
                </flux:input.group>

                <flux:error name="birthPlaceText" />
            </flux:field>

            {{-- Date of birth --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Date of birth') }}"
                    field="dateOfBirth"
                    text="{{ __('The date of birth is the day, month, and year when the person was born.') }}"
                    :required="$this->isRequiredField('dateOfBirth')"
                >
                    <flux:label for="create-person-date-of-birth">
                        {{ __('Date of birth') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('dateOfBirth')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.calendar />
                    </flux:input.group.prefix>

                    <flux:date-picker
                        class="w-full tabular-nums placeholder:text-zinc-400 dark:placeholder:text-red-500"
                        id="create-person-date-of-birth"
                        type="input"
                        variant="custom"
                        fixed-weeks
                        week-numbers
                        :invalid="$errors->has('dateOfBirth')"
                        selectable-header
                        clearable
                        with-today
                        open-to="{{ $this->dateOfBirthOpenTo() }}"
                        placeholder="{{ $this->dateOfBirthPlaceholder() }}"
                        wire:model.live="dateOfBirth"
                        x-on:select="$wire.set('dateOfBirth', $el.value || null)"
                        x-on:change="$wire.set('dateOfBirth', $el.value || null)"
                    />
                </flux:input.group>

                <flux:error name="dateOfBirth" />
            </flux:field>
        </div>
    </div>
</flux:card>
