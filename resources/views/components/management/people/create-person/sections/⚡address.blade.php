{{-- resources/views/components/management/people/create-person/sections/⚡address.blade.php --}}

<flux:card>
    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-5">

            <flux:field class="col-span-5 mb-3">
                <div class="flex items-start justify-between gap-4">
                    <flux:heading size="lg">
                        <span class="border-b-1 border-zinc-800/10 pb-2 pr-4 dark:border-white/20">
                            <flux:icon.map-pin-house class="mr-2 inline-block" />
                            {{ __('Person Address Information') }}
                        </span>
                    </flux:heading>

                    <div class="flex flex-wrap items-center justify-end gap-2">
                        <x-ui.badge.created-password :password="$generatedPassword" />

                        <x-ui.badge.test-data :show="$isTestData" />
                    </div>
                </div>
            </flux:field>

            {{-- Address country --}}
            <flux:field class="col-span-2">
                <x-ui.tooltip.trigger
                    title="{{ __('management.people.create_person.sections.address.country') }}"
                    field="addressCountryId"
                    text="{{ __('Please select the country for the address. This is important for correctly formatting the address and for any country-specific validations.') }}"
                    :required="$this->isRequiredField('addressCountryId')"
                >
                    <flux:label for="create-person-address-country">
                        {{ __('management.people.create_person.sections.address.country') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('addressCountryId')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.globe-alt />
                    </flux:input.group.prefix>

                    <flux:select
                        id="create-person-address-country"
                        name="addressCountryId"
                        variant="listbox"
                        placeholder="{{ __('Please select') }}"
                        searchable
                        copyable
                        clearable
                        wire:model.live="addressCountryId"
                    >
                        @foreach ($addressCountryOptions as $country)
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

                    <flux:select
                        class="tabular-nums"
                        id="create-person-address-postal-code"
                        name="addressPostalCode"
                        variant="listbox"
                        autocomplete="new-password"
                        copyable
                        clearable
                        searchable
                        :disabled="$this->addressCountryId === null"
                        wire:model.live="addressPostalCode"
                    >
                        @foreach ($addressPostalCodeOptions as $postalCode)
                            <flux:select.option :value="$postalCode">
                                {{ $postalCode }}
                            </flux:select.option>
                        @endforeach

                        <flux:select.option.create
                            min-length="1"
                            x-on:click="$wire.useCreatedAddressPostalCode($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '')"
                        >
                            {{ __('Use entered postal code') }}
                        </flux:select.option.create>
                    </flux:select>
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

                    <flux:select
                        id="create-person-address-city"
                        name="addressCity"
                        variant="listbox"
                        placeholder="{{ __('Please select') }}"
                        autocomplete="new-password"
                        copyable
                        clearable
                        searchable
                        :disabled="$this->addressCountryId === null"
                        wire:model.live="addressCity"
                    >
                        @foreach ($addressCityOptions as $city)
                            <flux:select.option :value="$city">
                                {{ $city }}
                            </flux:select.option>
                        @endforeach

                        <flux:select.option.create
                            min-length="1"
                            x-on:click="$wire.useCreatedAddressCity($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '')"
                        >
                            {{ __('Use entered city') }}
                        </flux:select.option.create>
                    </flux:select>
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

                    <flux:select
                        id="create-person-address-street"
                        name="addressStreet"
                        variant="listbox"
                        autocomplete="new-password"
                        copyable
                        clearable
                        searchable
                        :disabled="$this->addressCountryId === null || $this->addressCity === ''"
                        wire:model.live="addressStreet"
                    >
                        @foreach ($addressStreetOptions as $street)
                            <flux:select.option :value="$street">
                                {{ $street }}
                            </flux:select.option>
                        @endforeach

                        <flux:select.option.create
                            min-length="1"
                            x-on:click="$wire.useCreatedAddressStreet($el.closest('ui-select')?.querySelector('[data-flux-select-search] input')?.value ?? '')"
                        >
                            {{ __('Use entered street') }}
                        </flux:select.option.create>
                    </flux:select>
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
                        name="addressHouseNumber"
                        type="text"
                        wire:model.live.debounce.500ms="addressHouseNumber"
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
                        name="addressLine2"
                        type="text"
                        wire:model.live.debounce.500ms="addressLine2"
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
