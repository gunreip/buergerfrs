{{-- resources/views/components/management/people/edit-person/sections/⚡person-core.blade.php --}}

@php
    $emptyValue = __('Not set');
    $birthCountry = $birthCountryOptions->firstWhere('id', $birthCountryId);
    $birthCountryLabel = $birthCountry !== null ? "{$birthCountry->name} ({$birthCountry->iso2})" : null;
    $salutationIcons = [
        'mr' => 'user',
        'mrs' => 'user',
        'mx' => 'circle-user-round',
    ];
    $genderIcons = [
        'female' => 'venus',
        'male' => 'mars',
        'diverse' => 'transgender',
        'unknown' => 'circle-user-round',
    ];
@endphp

<flux:card>
    <x-ui.headers.card
        :title="__('Core data')"
        :description="__('Person core record.')"
    />

    <div class="space-y-4">

        {{-- Row 1: Radio Buttons Gender and Salutation --}}
        <div class="grid grid-cols-1 gap-3 xl:grid-cols-2">

            {{-- Gender Radio-Buttons --}}
            <flux:field>
                <flux:input.group class="w-full min-w-0">
                    <flux:input.group.prefix>
                        <flux:icon.venus-and-mars />
                    </flux:input.group.prefix>
                    <flux:input.group.prefix class="w-48 rounded-l-none">
                        <x-ui.tooltip.trigger
                            :title="__('Gender')"
                            :text="__('Select the gender of the person.')"
                        >
                            {{ __('Gender') }}
                        </x-ui.tooltip.trigger>
                    </flux:input.group.prefix>
                    <flux:radio.group
                        class="w-full rounded-l-none"
                        id="edit-person-gender"
                        name="gender"
                        :disabled="$editingField !== 'gender'"
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

                        <x-ui.input.group.suffix-field-edit
                            field="gender"
                            :title="__('Edit Gender')"
                            :text="__('Click to edit the gender of the person.')"
                            :changed="$this->isEditingFieldChanged('gender')"
                        />
                    </flux:radio.group>
                </flux:input.group>
                <flux:error name="gender" />
            </flux:field>

            {{-- Salutation Radio-Buttons --}}
            <flux:field>
                <flux:input.group class="w-full min-w-0">
                    <flux:input.group.prefix>
                        <flux:icon.speech />
                    </flux:input.group.prefix>
                    <flux:input.group.prefix class="w-48 rounded-l-none">
                        <x-ui.tooltip.trigger
                            :title="__('Salutation')"
                            :text="__('Select the salutation of the person.')"
                        >
                            {{ __('Salutation') }}
                        </x-ui.tooltip.trigger>
                    </flux:input.group.prefix>
                    <flux:radio.group
                        class="w-full rounded-l-none"
                        id="edit-person-salutation"
                        name="salutation"
                        :disabled="$editingField !== 'salutation'"
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

                        <x-ui.input.group.suffix-field-edit
                            field="salutation"
                            :title="__('Edit Salutation')"
                            :text="__('Click to edit the salutation of the person.')"
                            :changed="$this->isEditingFieldChanged('salutation')"
                        />
                    </flux:radio.group>
                </flux:input.group>
                <flux:error name="salutation" />
            </flux:field>
        </div>

        {{-- Row 2: Titel Field --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

            {{-- Titel field --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.speech />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Name title')"
                                :text="__('Enter the title of the person.')"
                            >
                                {{ __('Name title') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'nameTitle')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-name-title"
                                wire:model.live="nameTitle"
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                {{ filled($nameTitle) ? $nameTitle : $emptyValue }}
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="nameTitle"
                            :title="__('Edit Name Title')"
                            :text="__('Click to edit the name title of the person.')"
                            :changed="$this->isEditingFieldChanged('nameTitle')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="nameTitle" />
            </flux:field>

        </div>

        {{-- Row 3: First Name, Last Name, Birth Name --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

            {{-- First Name field --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.user />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('First name')"
                                :text="__('Enter the first name of the person.')"
                            >
                                {{ __('First name') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'firstName')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-first-name"
                                wire:model.live="firstName"
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                {{ filled($firstName) ? $firstName : $emptyValue }}
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="firstName"
                            :title="__('Edit First Name')"
                            :text="__('Click to edit the first name of the person.')"
                            :changed="$this->isEditingFieldChanged('firstName')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="firstName" />
            </flux:field>

            {{-- Last Name field --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.user />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Last name')"
                                :text="__('Enter the last name of the person.')"
                            >
                                {{ __('Last name') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'lastName')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-last-name"
                                wire:model.live="lastName"
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                {{ filled($lastName) ? $lastName : $emptyValue }}
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="lastName"
                            :title="__('Edit Last Name')"
                            :text="__('Click to edit the last name of the person.')"
                            :changed="$this->isEditingFieldChanged('lastName')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="lastName" />
            </flux:field>

            {{-- Birth Name field --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.user />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Birth name')"
                                :text="__('Enter the birth name of the person.')"
                            >
                                {{ __('Birth name') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'birthName')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-birth-name"
                                wire:model.live="birthName"
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                {{ filled($birthName) ? $birthName : $emptyValue }}
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="birthName"
                            :title="__('Edit Birth Name')"
                            :text="__('Click to edit the birth name of the person.')"
                            :changed="$this->isEditingFieldChanged('birthName')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="birthName" />
            </flux:field>

            {{-- Middle Name field --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.user />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Middle name')"
                                :text="__('Enter the middle name of the person.')"
                            >
                                {{ __('Middle name') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'middleName')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-middle-name"
                                wire:model.live="middleName"
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                {{ filled($middleName) ? $middleName : $emptyValue }}
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="middleName"
                            :title="__('Edit Middle Name')"
                            :text="__('Click to edit the middle name of the person.')"
                            :changed="$this->isEditingFieldChanged('middleName')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="middleName" />
            </flux:field>

            {{-- Preferred Name field --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.user />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Preferred name')"
                                :text="__('Enter the preferred name of the person.')"
                            >
                                {{ __('Preferred name') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'preferredName')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-preferred-name"
                                wire:model.live="preferredName"
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                {{ filled($preferredName) ? $preferredName : $emptyValue }}
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="preferredName"
                            :title="__('Edit Preferred Name')"
                            :text="__('Click to edit the preferred name of the person.')"
                            :changed="$this->isEditingFieldChanged('preferredName')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="preferredName" />
            </flux:field>

            {{-- Empty --}}
            <div></div>

            {{-- Marital Status Field --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.heart />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Marital status')"
                                :text="__('Select the marital status of the person.')"
                            >
                                {{ __('Marital status') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'maritalStatus')
                            <flux:select
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-marital-status"
                                variant="listbox"
                                wire:model.live="maritalStatus"
                            >
                                @foreach ($maritalStatusOptions as $value => $label)
                                    <flux:select.option value="{{ $value }}">{{ $label }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                {{ __($maritalStatusOptions[$maritalStatus] ?? $emptyValue) }}
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="maritalStatus"
                            :title="__('Edit Marital Status')"
                            :text="__('Click to edit the marital status of the person.')"
                            :changed="$this->isEditingFieldChanged('maritalStatus')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="maritalStatus" />
            </flux:field>

            {{-- Birth Country Field --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.globe />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Birth country')"
                                :text="__('Select the birth country of the person.')"
                            >
                                {{ __('Birth country') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'birthCountryId')
                            <flux:select
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-birth-country"
                                variant="listbox"
                                searchable
                                wire:model.live="birthCountryId"
                            >
                                @foreach ($birthCountryOptions as $country)
                                    <flux:select.option value="{{ (string) $country->id }}">
                                        {{ $country->name }} ({{ $country->iso2 }})
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                {{ $birthCountryLabel ?? $emptyValue }}
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="birthCountryId"
                            :title="__('Edit Birth Country')"
                            :text="__('Click to edit the birth country of the person.')"
                            :changed="$this->isEditingFieldChanged('birthCountryId')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="birthCountryId" />
            </flux:field>

            {{-- Birth Place Field --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.map-pin />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Birth place')"
                                :text="__('Enter the birth place of the person.')"
                            >
                                {{ __('Birth place') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'birthPlaceText')
                            <flux:select
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-birth-place"
                                variant="listbox"
                                searchable
                                clearable
                                wire:model.live="birthPlaceText"
                                :disabled="$birthCountryId === null"
                            >
                                @foreach ($birthPlaceOptions as $birthPlaceOption)
                                    <flux:select.option value="{{ $birthPlaceOption }}">
                                        {{ $birthPlaceOption }}
                                    </flux:select.option>
                                @endforeach

                                @if (filled($birthPlaceText) && !$birthPlaceOptions->containsStrict($birthPlaceText))
                                    <flux:select.option value="{{ $birthPlaceText }}">
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
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                {{ filled($birthPlaceText) ? $birthPlaceText : $emptyValue }}
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="birthPlaceText"
                            :title="__('Edit Birth Place')"
                            :text="__('Click to edit the birth place of the person.')"
                            :changed="$this->isEditingFieldChanged('birthPlaceText')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="birthCountryId" />
            </flux:field>

            {{-- Date Of Birth Field --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.calendar />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Date of birth')"
                                :text="__('Enter the date of birth of the person.')"
                            >
                                {{ __('Date of birth') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'dateOfBirth')
                            <flux:date-picker
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-date-of-birth"
                                wire:model.live="dateOfBirth"
                                with-today
                                max="{{ now()->toDateString() }}"
                                open-to="{{ $this->dateOfBirthOpenTo() }}"
                                placeholder="{{ $this->dateOfBirthPlaceholder() }}"
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <x-ui.date-time.date
                                    :value="$dateOfBirth"
                                    color="default"
                                />
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="dateOfBirth"
                            :title="__('Edit Date of Birth')"
                            :text="__('Click to edit the date of birth of the person.')"
                            :changed="$this->isEditingFieldChanged('dateOfBirth')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="dateOfBirth" />
            </flux:field>
        </div>

    </div>

</flux:card>
