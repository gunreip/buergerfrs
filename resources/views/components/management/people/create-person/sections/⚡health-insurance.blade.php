{{-- resources/views/components/management/people/create-person/sections/⚡health-insurance.blade.php --}}

<flux:card>

    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-2">

            {{-- Health insurance provider --}}
            <flux:field>
                <flux:label for="create-person-health-insurance-provider">
                    {{ __('Health insurance provider') }}
                </flux:label>

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
                <flux:label for="create-person-health-insurance-number">
                    {{ __('Health insurance number') }}
                </flux:label>

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
