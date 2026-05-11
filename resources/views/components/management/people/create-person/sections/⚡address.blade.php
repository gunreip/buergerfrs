{{-- resources/views/components/management/people/create-person/sections/⚡address.blade.php --}}

<flux:card>
    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-5">

            {{-- Address country --}}
            <flux:field class="col-span-2">
                <flux:label for="create-person-address-country">
                    {{ __('Country') }}
                </flux:label>

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
                <flux:label for="create-person-address-postal-code">
                    {{ __('Postal code') }}
                </flux:label>

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
                <flux:label for="create-person-address-city">
                    {{ __('City') }}
                </flux:label>

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
                <flux:label for="create-person-address-street">
                    {{ __('Street') }}
                </flux:label>

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
            {{-- </div> --}}

            {{-- House number --}}
            <flux:field>
                <flux:label for="create-person-address-house-number">
                    {{ __('House number') }}
                </flux:label>

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
                <flux:label for="create-person-address-line-2">
                    {{ __('Address line 2') }}
                </flux:label>

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
