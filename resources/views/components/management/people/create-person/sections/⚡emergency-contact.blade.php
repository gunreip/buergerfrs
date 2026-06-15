{{-- resources/views/components/management/people/create-person/sections/⚡emergency-contact.blade.php --}}

<flux:card>
    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-2">

            {{-- Emergency contact name --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('ui.labels.name') }}"
                    field="emergencyContactName"
                    text="{{ __('Please enter the name of the emergency contact. This is important for correctly identifying the person\'s emergency contact and for any name-specific validations.') }}"
                    :required="$this->isRequiredField('emergencyContactName')"
                >
                    <flux:label for="create-person-emergency-contact-name">
                        {{ __('ui.labels.name') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('emergencyContactName')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.user />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-emergency-contact-name"
                        type="text"
                        wire:model.blur="emergencyContactName"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="emergencyContactName" />
            </flux:field>

            {{-- Emergency contact relationship --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Relationship') }}"
                    field="emergencyContactRelationship"
                    text="{{ __('Please select the relationship of the emergency contact to the person. This is important for correctly identifying the person\'s emergency contact and for any relationship-specific validations.') }}"
                    :required="$this->isRequiredField('emergencyContactRelationship')"
                >
                    <flux:label for="create-person-emergency-contact-relationship">
                        {{ __('Relationship') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('emergencyContactRelationship')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.user-group />
                    </flux:input.group.prefix>

                    <flux:select
                        id="create-person-emergency-contact-relationship"
                        wire:model.blur="emergencyContactRelationship"
                        placeholder="{{ __('Please select') }}"
                    >
                        @foreach ($emergencyContactRelationshipOptions as $value => $label)
                            <flux:select.option :value="$value">
                                {{ __($label) }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:input.group>

                <flux:error name="emergencyContactRelationship" />
            </flux:field>
        </div>

        <div class="grid gap-4 md:grid-cols-2">

            {{-- Emergency contact phone --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Phone') }}"
                    field="emergencyContactPhone"
                    text="{{ __('Please enter the phone number of the emergency contact. This is important for correctly identifying the person\'s emergency contact and for any phone-specific validations.') }}"
                    :required="$this->isRequiredField('emergencyContactPhone')"
                >
                    <flux:label for="create-person-emergency-contact-phone">
                        {{ __('Phone') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('emergencyContactPhone')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.phone />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-emergency-contact-phone"
                        type="tel"
                        wire:model.blur="emergencyContactPhone"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="emergencyContactPhone" />
            </flux:field>

            {{-- Emergency contact email --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Email') }}"
                    field="emergencyContactEmail"
                    text="{{ __('Please enter the email address of the emergency contact. This is important for correctly identifying the person\'s emergency contact and for any email-specific validations.') }}"
                    :required="$this->isRequiredField('emergencyContactEmail')"
                >
                    <flux:label for="create-person-emergency-contact-email">
                        {{ __('Email') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('emergencyContactEmail')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.envelope />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-emergency-contact-email"
                        type="email"
                        wire:model.blur="emergencyContactEmail"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="emergencyContactEmail" />
            </flux:field>
        </div>
    </div>
</flux:card>
