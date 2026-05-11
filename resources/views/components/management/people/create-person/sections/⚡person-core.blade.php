{{-- resources/views/components/management/people/create-person/sections/⚡person-core.blade.php --}}

<flux:card>
    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-3">

            {{-- Salutation --}}
            <flux:field>
                <flux:label for="create-person-salutation">
                    {{ __('Salutation') }}
                </flux:label>

                <flux:radio.group
                    id="create-person-salutation"
                    wire:model="salutation"
                    variant="segmented"
                >
                    @foreach ($salutationOptions as $value => $label)
                        <flux:radio
                            :value="$value"
                            :label="__($label)"
                        />
                    @endforeach
                </flux:radio.group>

                <flux:error name="salutation" />
            </flux:field>

            {{-- Title --}}
            <flux:field>
                <flux:label for="create-person-name-title">
                    {{ __('Title') }}
                </flux:label>

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
                <flux:label for="create-person-gender">
                    {{ __('Gender') }}
                </flux:label>

                <flux:radio.group
                    id="create-person-gender"
                    wire:model="gender"
                    variant="segmented"
                >
                    @foreach ($genderOptions as $value => $label)
                        <flux:radio
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
                <flux:label for="create-person-first-name">
                    {{ __('First name') }}
                </flux:label>

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
                <flux:label for="create-person-middle-name">
                    {{ __('Middle name') }}
                </flux:label>

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
                <flux:label for="create-person-preferred-name">
                    {{ __('Preferred name') }}
                </flux:label>

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
                <flux:label for="create-person-last-name">
                    {{ __('Last name') }}
                </flux:label>

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
                <flux:label for="create-person-marital-status">
                    {{ __('Marital status') }}
                </flux:label>

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
                <flux:label for="create-person-birth-name">
                    {{ __('Birth name') }}
                </flux:label>

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
                <flux:label for="create-person-birth-country">
                    {{ __('Birth country') }}
                </flux:label>

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
                <flux:label for="create-person-birth-place">
                    {{ __('Birth place') }}
                </flux:label>

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
                <flux:label for="create-person-date-of-birth">
                    {{ __('Date of birth') }}
                </flux:label>

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
