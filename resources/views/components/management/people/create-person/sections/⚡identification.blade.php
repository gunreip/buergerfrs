{{-- resources/views/components/management/people/create-person/sections/⚡identification.blade.php --}}

<flux:card>
    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-3">

            {{-- National ID number --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('National ID number') }}"
                    text="{{ __('Please enter the national ID number for the person. This is important for correctly identifying the person and for any national ID-specific validations.') }}"
                    required
                >
                    <flux:label for="create-person-national-id-number">
                        {{ __('National ID number') }}
                        <x-ui.tooltip.badge-required />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.identification />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-national-id-number"
                        type="text"
                        wire:model.blur="nationalIdNumber"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="nationalIdNumber" />
            </flux:field>

            {{-- Issuing authority --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Issuing authority') }}"
                    text="{{ __('Please enter the issuing authority for the national ID. This is important for correctly identifying the person and for any national ID-specific validations.') }}"
                >
                    <flux:label for="create-person-national-id-issuing-authority">
                        {{ __('Issuing authority') }}
                        <x-ui.tooltip.badge-required />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.building-library />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-national-id-issuing-authority"
                        type="text"
                        wire:model.blur="nationalIdIssuingAuthority"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="nationalIdIssuingAuthority" />
            </flux:field>

            {{-- Tax ID --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Tax ID') }}"
                    text="{{ __('Please enter the tax ID for the person. This is important for correctly identifying the person and for any tax-specific validations.') }}"
                >
                    <flux:label for="create-person-tax-id">
                        {{ __('Tax ID') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.hashtag />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-tax-id"
                        type="text"
                        wire:model.blur="taxId"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="taxId" />
            </flux:field>
        </div>

        <div class="grid gap-4 md:grid-cols-3">

            {{-- Social security number --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Social security number') }}"
                    text="{{ __('Please enter the social security number for the person. This is important for correctly identifying the person and for any social security-specific validations.') }}"
                >
                    <flux:label for="create-person-social-security-number">
                        {{ __('Social security number') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.shield-check />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-social-security-number"
                        type="text"
                        wire:model.blur="socialSecurityNumber"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="socialSecurityNumber" />
            </flux:field>
            {{-- </div> --}}
            {{-- </div> --}}

            {{-- Pension insurance number --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Pension insurance number') }}"
                    text="{{ __('Please enter the pension insurance number for the person. This is important for correctly identifying the person and for any pension insurance-specific validations.') }}"
                >
                    <flux:label for="create-person-pension-insurance-number">
                        {{ __('Pension insurance number') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.identification />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-pension-insurance-number"
                        type="text"
                        wire:model.blur="pensionInsuranceNumber"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="pensionInsuranceNumber" />
            </flux:field>

            {{-- <div class="grid gap-4 md:grid-cols-2"> --}}
            {{-- Residence permit number --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Residence permit number') }}"
                    text="{{ __('Please enter the residence permit number for the person. This is important for correctly identifying the person and for any residence permit-specific validations.') }}"
                >
                    <flux:label for="create-person-residence-permit-number">
                        {{ __('Residence permit number') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.document-text />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-residence-permit-number"
                        type="text"
                        wire:model.blur="residencePermitNumber"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="residencePermitNumber" />
            </flux:field>
        </div>
    </div>
</flux:card>
