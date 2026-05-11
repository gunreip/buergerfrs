{{-- resources/views/components/management/people/create-person/sections/⚡person-core.blade.php --}}

<flux:card>
    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-3">

            {{-- Salutation --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Salutation') }}"
                    text="{{ __('The salutation is used to address the person in a formal way. It is usually based on their title or position.') }}"
                    required
                >
                    <flux:label for="create-person-salutation">
                        {{ __('Salutation') }}
                        <x-ui.tooltip.badge-required />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:radio.group
                    id="create-person-salutation"
                    wire:model="salutation"
                    variant="segmented"
                >
                    @foreach ($salutationOptions as $value => $label)
                        <flux:radio
                            class="font-semibold"
                            :value="$value"
                            :label="__($label)"
                        />
                    @endforeach
                </flux:radio.group>

                <flux:error name="salutation" />
            </flux:field>

            {{-- Title --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Title') }}"
                    text="{{ __('The title is an academic or professional title that the person holds. It is usually displayed before the name.') }}"
                >
                    <flux:label for="create-person-name-title">
                        {{ __('Title') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.badge />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-name-title"
                        type="text"
                        wire:model.blur="nameTitle"
                        autocomplete="new-password"
                        placeholder="{{ __('e.g. Dr., Prof. Dr.') }}"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="nameTitle" />
            </flux:field>

            {{-- Gender --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Gender') }}"
                    text="{{ __('The gender is used to specify the person\'s gender. It is usually displayed in forms and reports.') }}"
                    required
                >
                    <flux:label for="create-person-gender">
                        {{ __('Gender') }}
                    </flux:label>
                    <x-ui.tooltip.badge-required />
                </x-ui.tooltip.trigger>

                <flux:radio.group
                    id="create-person-gender"
                    wire:model="gender"
                    variant="segmented"
                >
                    @foreach ($genderOptions as $value => $label)
                        <flux:radio
                            class="font-semibold"
                            :value="$value"
                            :label="__($label)"
                        />
                    @endforeach
                </flux:radio.group>

                <flux:error name="gender" />
            </flux:field>
        </div>

        <div class="grid gap-4 md:grid-cols-3">

            {{-- First name --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('First name') }}"
                    text="{{ __('The first name is the person\'s given name. It is usually displayed before the last name.') }}"
                    required
                >
                    <flux:label for="create-person-first-name">
                        {{ __('First name') }}
                        <x-ui.tooltip.badge-required />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.user />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-first-name"
                        type="text"
                        wire:model.blur="firstName"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="firstName" />
            </flux:field>

            {{-- Middle name --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Middle name') }}"
                    text="{{ __('The middle name is an additional given name that the person may have. It is usually displayed between the first name and the last name.') }}"
                >
                    <flux:label for="create-person-middle-name">
                        {{ __('Middle name') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.user />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-middle-name"
                        type="text"
                        wire:model.blur="middleName"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="middleName" />
            </flux:field>

            {{-- Preferred name --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Preferred name') }}"
                    text="{{ __('The preferred name is the name that the person prefers to be called. It may be different from their legal first name.') }}"
                >
                    <flux:label for="create-person-preferred-name">
                        {{ __('Preferred name') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.user />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-preferred-name"
                        type="text"
                        wire:model.blur="preferredName"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="preferredName" />
            </flux:field>
        </div>

        <div class="grid gap-4 md:grid-cols-3">

            {{-- Last name --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Last name') }}"
                    text="{{ __('The last name is the family name or surname of the person. It is usually passed down from one generation to the next.') }}"
                    required
                >
                    <flux:label for="create-person-last-name">
                        {{ __('Last name') }}
                        <x-ui.tooltip.badge-required />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.user />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-last-name"
                        type="text"
                        wire:model.blur="lastName"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="lastName" />
            </flux:field>

            {{-- Marital status --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Marital status') }}"
                    text="{{ __('The marital status indicates the legal relationship status of the person, such as single, married, or divorced.') }}"
                >
                    <flux:label for="create-person-marital-status">
                        {{ __('Marital status') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.heart />
                    </flux:input.group.prefix>

                    <flux:select
                        id="create-person-marital-status"
                        wire:model.blur="maritalStatus"
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

            {{-- Birth name --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Birth name') }}"
                    text="{{ __('The birth name is the name given to the person at birth. It may differ from their current legal name.') }}"
                >
                    <flux:label for="create-person-birth-name">
                        {{ __('Birth name') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.user />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-birth-name"
                        type="text"
                        wire:model.blur="birthName"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="birthName" />
            </flux:field>
        </div>

        <div class="grid gap-4 md:grid-cols-3">

            {{-- Birth country --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Birth country') }}"
                    text="{{ __('The birth country is the country where the person was born.') }}"
                    required
                >
                    <flux:label for="create-person-birth-country">
                        {{ __('Birth country') }}
                        <x-ui.tooltip.badge-required />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.globe-alt />
                    </flux:input.group.prefix>

                    <flux:select
                        id="create-person-birth-country"
                        wire:model.blur="birthCountryId"
                        placeholder="{{ __('Please select') }}"
                    >
                        @foreach ($birthCountryOptions as $country)
                            <flux:select.option :value="$country->id">
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
                    text="{{ __('The birth place is the city or town where the person was born.') }}"
                    required
                >
                    <flux:label for="create-person-birth-place">
                        {{ __('Birth place') }}
                        <x-ui.tooltip.badge-required />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.map-pin />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-birth-place"
                        type="text"
                        wire:model.blur="birthPlaceText"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="birthPlaceText" />
            </flux:field>

            {{-- Date of birth --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Date of birth') }}"
                    text="{{ __('The date of birth is the day, month, and year when the person was born.') }}"
                    required
                >
                    <flux:label for="create-person-date-of-birth">
                        {{ __('Date of birth') }}
                        <x-ui.tooltip.badge-required />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.calendar />
                    </flux:input.group.prefix>

                    <flux:input
                        class="tabular-nums"
                        id="create-person-date-of-birth"
                        type="date"
                        wire:model.blur="dateOfBirth"
                        autocomplete="new-password"
                        copyable
                    />
                </flux:input.group>

                <flux:error name="dateOfBirth" />
            </flux:field>
        </div>
    </div>
</flux:card>
