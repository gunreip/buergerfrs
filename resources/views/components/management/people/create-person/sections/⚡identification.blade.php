{{-- resources/views/components/management/people/create-person/sections/⚡identification.blade.php --}}

<flux:card>
    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-3">

            {{-- National ID number --}}
            <flux:field>
                <flux:label for="create-person-national-id-number">
                    {{ __('National ID number') }}
                </flux:label>

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
                <flux:label for="create-person-national-id-issuing-authority">
                    {{ __('Issuing authority') }}
                </flux:label>

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
                <flux:label for="create-person-tax-id">
                    {{ __('Tax ID') }}
                </flux:label>

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
                <flux:label for="create-person-social-security-number">
                    {{ __('Social security number') }}
                </flux:label>

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
                <flux:label for="create-person-pension-insurance-number">
                    {{ __('Pension insurance number') }}
                </flux:label>

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
                <flux:label for="create-person-residence-permit-number">
                    {{ __('Residence permit number') }}
                </flux:label>

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
