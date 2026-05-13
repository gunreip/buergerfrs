{{-- resources/views/components/management/people/create-person/sections/⚡health-insurance.blade.php --}}

<flux:card>

    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-2">

            {{-- Health insurance provider --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Health insurance provider') }}"
                    field="healthInsuranceProviderId"
                    text="{{ __('Please select the health insurance provider for the person. This is important for correctly identifying the person\'s health insurance and for any health insurance-specific validations.') }}"
                    :required="$this->isRequiredField('healthInsuranceProviderId')"
                >
                    <flux:label for="create-person-health-insurance-provider">
                        {{ __('Health insurance provider') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('healthInsuranceProviderId')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.shield-check />
                    </flux:input.group.prefix>

                    <flux:select
                        id="create-person-health-insurance-provider"
                        wire:model.blur="healthInsuranceProviderId"
                        placeholder="{{ __('Please select') }}"
                    >
                        @foreach ($healthInsuranceProviderOptions as $provider)
                            <flux:select.option :value="$provider->id">
                                {{ $provider->short_name ? $provider->short_name . ' — ' . $provider->name : $provider->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:input.group>

                <flux:error name="healthInsuranceProviderId" />
            </flux:field>

            {{-- Health insurance number --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Health insurance number') }}"
                    field="healthInsuranceNumber"
                    text="{{ __('Please enter the health insurance number for the person. This is important for correctly identifying the person\'s health insurance and for any health insurance-specific validations.') }}"
                    :required="$this->isRequiredField('healthInsuranceNumber')"
                >
                    <flux:label for="create-person-health-insurance-number">
                        {{ __('Health insurance number') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('healthInsuranceNumber')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.shield-check />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-health-insurance-number"
                        type="text"
                        wire:model.blur="healthInsuranceNumber"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="healthInsuranceNumber" />
            </flux:field>
        </div>
    </div>
</flux:card>
