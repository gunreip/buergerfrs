{{-- resources/views/components/management/people/create-person/sections/⚡international.blade.php --}}

<flux:card>

    <div class="spaxe-y-6">

        <div class="grid gap-4 md:grid-cols-2">

            {{-- Primary nationality --}}
            <flux:field>
                <flux:label for="create-person-primary-nationality">
                    {{ __('Primary nationality') }}
                </flux:label>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.flag />
                    </flux:input.group.prefix>

                    <flux:select
                        id="create-person-primary-nationality"
                        wire:model.blur="primaryNationalityCountryId"
                        placeholder="{{ __('Please select') }}"
                    >
                        @foreach ($nationalityCountryOptions as $country)
                            <flux:select.option :value="$country->id">
                                {{ $country->native_name ?: $country->name }} ({{ $country->iso2 }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:input.group>

                <flux:error name="primaryNationalityCountryId" />
            </flux:field>

            {{-- Primary language --}}
            <flux:field>
                <flux:label for="create-person-primary-language">
                    {{ __('Primary language') }}
                </flux:label>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.language />
                    </flux:input.group.prefix>

                    <flux:select
                        id="create-person-primary-language"
                        wire:model.blur="primaryLanguageId"
                        placeholder="{{ __('Please select') }}"
                    >
                        @foreach ($languageOptions as $language)
                            <flux:select.option :value="$language->id">
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
    </div>
</flux:card>
