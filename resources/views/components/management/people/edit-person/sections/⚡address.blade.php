{{-- resources/views/components/management/people/edit-person/sections/⚡address.blade.php --}}

@php
    $emptyValue = __('Not set');
    $addressCountry = $addressCountryOptions->firstWhere('id', $addressCountryId);
    $addressCountryLabel =
        $addressCountry !== null
            ? ($addressCountry->native_name ?: $addressCountry->name) . ' (' . $addressCountry->iso2 . ')'
            : null;
@endphp

<flux:card>
    <x-ui.headers.card
        :title="__('ui.labels.address.address')"
        :description="__('Primary person address.')"
    />

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

        {{-- Country Name --}}
        <flux:field>
            <div class="mb-1 flex items-center justify-between gap-2">
                <flux:input.group class="w-full min-w-0">
                    <flux:input.group.prefix>
                        <flux:icon.globe />
                    </flux:input.group.prefix>
                    <flux:input.group.prefix class="w-64 rounded-l-none">
                        <x-ui.tooltip.trigger
                            :title="__('management.people.create_person.sections.address.country')"
                            :text="__(
                                'Select the country for the address. This will help in formatting the address correctly.',
                            )"
                        >
                            {{ __('management.people.create_person.sections.address.country') }}
                        </x-ui.tooltip.trigger>
                    </flux:input.group.prefix>
                    @if ($editingField === 'addressCountryId')
                        <flux:select
                            class="w-full min-w-0 rounded-l-none"
                            id="edit-person-address-country"
                            variant="listbox"
                            searchable
                            clearable
                            wire:model.live="addressCountryId"
                        >
                            @foreach ($addressCountryOptions as $country)
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
                            @if ($addressCountry !== null)
                                <x-ui.country.flag
                                    class="shrink-0"
                                    size="lg"
                                    :country="$addressCountry->iso2"
                                />
                            @endif

                            <span class="truncate">{{ $addressCountryLabel ?? $emptyValue }}</span>
                        </div>
                    @endif
                    <x-ui.input.group.suffix-field-edit
                            field="addressCountryId"
                            :title="__('Edit Country')"
                            :text="__(
                                'Select the country for the address. This will help in formatting the address correctly.',
                            )"
                            :changed="$this->isEditingFieldChanged('addressCountryId')"
                        />
                </flux:input.group>
            </div>
            <flux:error name="addressCountryId" />
        </flux:field>

        {{-- Postal Code --}}
        <flux:field>
            <div class="mb-1 flex items-center justify-between gap-2">
                <flux:input.group class="w-full min-w-0">
                    <flux:input.group.prefix>
                        <flux:icon.map-pin />
                    </flux:input.group.prefix>
                    <flux:input.group.prefix class="w-64 rounded-l-none">
                        <x-ui.tooltip.trigger
                            :title="__('Postal Code')"
                            :text="__(
                                'Enter the postal code for the address. This will help in locating the address accurately.',
                            )"
                        >
                            {{ __('Postal code') }}
                        </x-ui.tooltip.trigger>
                    </flux:input.group.prefix>
                    @if ($editingField === 'addressPostalCode')
                        <flux:select
                            class="w-full min-w-0 rounded-l-none tabular-nums"
                            id="edit-person-address-postal-code"
                            variant="listbox"
                            searchable
                            clearable
                            :disabled="$addressCountryId === null"
                            wire:model.live="addressPostalCode"
                        >
                            @foreach ($addressPostalCodeOptions as $postalCode)
                                <flux:select.option value="{{ $postalCode }}">
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
                    @else
                        <div
                            class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                            {{ filled($addressPostalCode) ? $addressPostalCode : $emptyValue }}
                        </div>
                    @endif
                    <x-ui.input.group.suffix-field-edit
                            field="addressPostalCode"
                            :title="__('Edit Postal Code')"
                            :text="__(
                                'Enter the postal code for the address. This will help in locating the address accurately.',
                            )"
                            :changed="$this->isEditingFieldChanged('addressPostalCode')"
                        />
                </flux:input.group>
            </div>
            <flux:error name="addressPostalCode" />
        </flux:field>

        {{-- City Name --}}
        <flux:field>
            <div class="mb-1 flex items-center justify-between gap-2">
                <flux:input.group class="w-full min-w-0">
                    <flux:input.group.prefix>
                        <flux:icon.building-office-2 />
                    </flux:input.group.prefix>
                    <flux:input.group.prefix class="w-64 rounded-l-none">
                        <x-ui.tooltip.trigger
                            :title="__('City')"
                            :text="__(
                                'Enter the city for the address. This will help in locating the address accurately.',
                            )"
                        >
                            {{ __('City') }}
                        </x-ui.tooltip.trigger>
                    </flux:input.group.prefix>
                    @if ($editingField === 'addressCity')
                        <flux:select
                            class="w-full min-w-0 rounded-l-none"
                            id="edit-person-address-city"
                            variant="listbox"
                            searchable
                            clearable
                            :disabled="$addressCountryId === null"
                            wire:model.live="addressCity"
                        >
                            @foreach ($addressCityOptions as $city)
                                <flux:select.option value="{{ $city }}">
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
                    @else
                        <div
                            class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                            {{ filled($addressCity) ? $addressCity : $emptyValue }}
                        </div>
                    @endif
                    <x-ui.input.group.suffix-field-edit
                            field="addressCity"
                            :title="__('Edit City')"
                            :text="__(
                                'Edit the city for the address. This will help in locating the address accurately.',
                            )"
                            :changed="$this->isEditingFieldChanged('addressCity')"
                        />
                </flux:input.group>
            </div>
            <flux:error name="addressCity" />
        </flux:field>

        {{-- Street Name --}}
        <flux:field>
            <div class="mb-1 flex items-center justify-between gap-2">
                <flux:input.group class="w-full min-w-0">
                    <flux:input.group.prefix>
                        <flux:icon.map />
                    </flux:input.group.prefix>
                    <flux:input.group.prefix class="w-64 rounded-l-none">
                        <x-ui.tooltip.trigger
                            :title="__('Street')"
                            :text="__(
                                'Enter the street for the address. This will help in locating the address accurately.',
                            )"
                        >
                            {{ __('Street') }}
                        </x-ui.tooltip.trigger>
                    </flux:input.group.prefix>
                    @if ($editingField === 'addressStreet')
                        <flux:select
                            class="w-full min-w-0 rounded-l-none"
                            id="edit-person-address-street"
                            variant="listbox"
                            searchable
                            clearable
                            :disabled="$addressCountryId === null || $addressCity === ''"
                            wire:model.live="addressStreet"
                        >
                            @foreach ($addressStreetOptions as $street)
                                <flux:select.option value="{{ $street }}">
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
                    @else
                        <div
                            class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                            {{ filled($addressStreet) ? $addressStreet : $emptyValue }}
                        </div>
                    @endif
                    <x-ui.input.group.suffix-field-edit
                            field="addressStreet"
                            :title="__('Edit Street')"
                            :text="__(
                                'Edit the street for the address. This will help in locating the address accurately.',
                            )"
                            :changed="$this->isEditingFieldChanged('addressStreet')"
                        />
                </flux:input.group>
            </div>
            <flux:error name="addressStreet" />
        </flux:field>

        {{-- House Number --}}
        <flux:field>
            <div class="mb-1 flex items-center justify-between gap-2">
                <flux:input.group class="w-full min-w-0">
                    <flux:input.group.prefix>
                        <flux:icon.home />
                    </flux:input.group.prefix>
                    <flux:input.group.prefix class="w-64 rounded-l-none">
                        <x-ui.tooltip.trigger
                            :title="__('House Number')"
                            :text="__(
                                'Enter the house number for the address. This will help in locating the address accurately.',
                            )"
                        >
                            {{ __('House number') }}
                        </x-ui.tooltip.trigger>
                    </flux:input.group.prefix>
                    @if ($editingField === 'addressHouseNumber')
                        <flux:input
                            class="w-full min-w-0 rounded-l-none"
                            id="edit-person-address-house-number"
                            wire:model.live="addressHouseNumber"
                        />
                    @else
                        <div
                            class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                            {{ filled($addressHouseNumber) ? $addressHouseNumber : $emptyValue }}
                        </div>
                    @endif
                    <x-ui.input.group.suffix-field-edit
                            field="addressHouseNumber"
                            :title="__('Edit House Number')"
                            :text="__(
                                'Edit the house number for the address. This will help in locating the address accurately.',
                            )"
                            :changed="$this->isEditingFieldChanged('addressHouseNumber')"
                        />
                </flux:input.group>
            </div>
            <flux:error name="addressHouseNumber" />
        </flux:field>

        {{-- Address Line 2 --}}
        <flux:field>
            <div class="mb-1 flex items-center justify-between gap-2">
                <flux:input.group class="w-full min-w-0">
                    <flux:input.group.prefix>
                        <flux:icon.queue-list />
                    </flux:input.group.prefix>
                    <flux:input.group.prefix class="w-64 rounded-l-none">
                        <x-ui.tooltip.trigger
                            :title="__('ui.address.address.address-line-2')"
                            :text="__(
                                'Enter the second line of the address. This can include apartment numbers, suite numbers, or other secondary address information.',
                            )"
                        >
                            {{ __('ui.address.address.address-line-2') }}
                        </x-ui.tooltip.trigger>
                    </flux:input.group.prefix>
                    @if ($editingField === 'addressLine2')
                        <flux:input
                            class="w-full min-w-0 rounded-l-none"
                            id="edit-person-address-line-2"
                            wire:model.live="addressLine2"
                        />
                    @else
                        <div
                            class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                            {{ filled($addressLine2) ? $addressLine2 : $emptyValue }}
                        </div>
                    @endif
                    <x-ui.input.group.suffix-field-edit
                            field="addressLine2"
                            :title="__('Edit Address Line 2')"
                            :text="__(
                                'Edit the second line of the address. This can include apartment numbers, suite numbers, or other secondary address information.',
                            )"
                            :changed="$this->isEditingFieldChanged('addressLine2')"
                        />
                </flux:input.group>
            </div>
            <flux:error name="addressLine2" />
        </flux:field>
    </div>
</flux:card>
