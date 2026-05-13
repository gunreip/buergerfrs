{{-- resources/views/components/management/people/create-person/sections/⚡address.blade.php --}}

<flux:card>
    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-5">

            {{-- Address country --}}
            <flux:field class="col-span-2">
                <x-ui.tooltip.trigger
                    title="{{ __('Country') }}"
                    field="addressCountryId"
                    text="{{ __('Please select the country for the address. This is important for correctly formatting the address and for any country-specific validations.') }}"
                    :required="$this->isRequiredField('addressCountryId')"
                >
                    <flux:label for="create-person-address-country">
                        {{ __('Country') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('addressCountryId')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.globe-alt />
                    </flux:input.group.prefix>

                    <flux:select
                        id="create-person-address-country"
                        wire:model.blur="addressCountryId"
                        placeholder="{{ __('Please select') }}"
                    >
                        @foreach ($addressCountryOptions as $country)
                            <flux:select.option :value="$country->id">
                                {{ $country->native_name ?: $country->name }} ({{ $country->iso2 }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:input.group>

                <flux:error name="addressCountryId" />
            </flux:field>

            {{-- Postal code --}}
            <flux:field class="col-span-1">
                <x-ui.tooltip.trigger
                    title="{{ __('Postal code') }}"
                    field="addressPostalCode"
                    text="{{ __('Please enter the postal code for the address. This is important for correctly formatting the address and for any postal code-specific validations.') }}"
                    :required="$this->isRequiredField('addressPostalCode')"
                >
                    <flux:label for="create-person-address-postal-code">
                        {{ __('Postal code') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('addressPostalCode')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.map-pin />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-address-postal-code"
                        type="text"
                        wire:model.blur="addressPostalCode"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="addressPostalCode" />
            </flux:field>

            {{-- City --}}
            <flux:field class="col-span-2">
                <x-ui.tooltip.trigger
                    title="{{ __('City') }}"
                    field="addressCity"
                    text="{{ __('Please enter the city for the address. This is important for correctly formatting the address and for any city-specific validations.') }}"
                    :required="$this->isRequiredField('addressCity')"
                >
                    <flux:label for="create-person-address-city">
                        {{ __('City') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('addressCity')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.building-office-2 />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-address-city"
                        type="text"
                        wire:model.blur="addressCity"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="addressCity" />
            </flux:field>
        </div>

        <div class="grid gap-4 md:grid-cols-5">

            {{-- Street --}}
            <flux:field class="col-span-2">
                <x-ui.tooltip.trigger
                    title="{{ __('Street') }}"
                    field="addressStreet"
                    text="{{ __('Please enter the street for the address. This is important for correctly formatting the address and for any street-specific validations.') }}"
                    :required="$this->isRequiredField('addressStreet')"
                >
                    <flux:label for="create-person-address-street">
                        {{ __('Street') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('addressStreet')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.map />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-address-street"
                        type="text"
                        wire:model.blur="addressStreet"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="addressStreet" />
            </flux:field>

            {{-- House number --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('House number') }}"
                    field="addressHouseNumber"
                    text="{{ __('Please enter the house number for the address. This is important for correctly formatting the address and for any house number-specific validations.') }}"
                    :required="$this->isRequiredField('addressHouseNumber')"
                >
                    <flux:label for="create-person-address-house-number">
                        {{ __('House number') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('addressHouseNumber')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.home />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-address-house-number"
                        type="text"
                        wire:model.blur="addressHouseNumber"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="addressHouseNumber" />
            </flux:field>

            {{-- Address line 2 --}}
            <flux:field class="col-span-2">
                <x-ui.tooltip.trigger
                    title="{{ __('Address line 2') }}"
                    field="addressLine2"
                    text="{{ __('Please enter additional address information, such as apartment number, floor, etc. This is optional but can be helpful for correctly delivering mail and for any address-specific validations.') }}"
                    :required="$this->isRequiredField('addressLine2')"
                >
                    <flux:label for="create-person-address-line-2">
                        {{ __('Address line 2') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('addressLine2')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.queue-list />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-address-line-2"
                        type="text"
                        wire:model.blur="addressLine2"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="addressLine2" />
            </flux:field>
        </div>
    </div>
</flux:card>
