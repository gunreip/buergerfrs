{{-- resources/views/components/management/people/edit-person/sections/⚡identification.blade.php --}}

@php
    $emptyValue = __('Not set');
@endphp

<flux:card>
    <x-ui.headers.card
        :title="__('Person Identification Information')"
        :description="__('Identifiers assigned to this person.')"
    />

    <div class="space-y-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-8">

            {{-- National ID Number --}}
            <flux:field class="col-span-3">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.id-card />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-124 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('National ID number')"
                                :text="__(
                                    'The national ID number is a unique identifier assigned to individuals by the government for identification purposes.',
                                )"
                            >
                                {{ __('National ID number') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'identifierNationalIdNumber')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-national-id-number"
                                type="text"
                                wire:model.live.debounce.500ms="identifierNationalIdNumber"
                                autocomplete="new-password"
                                copyable
                                clearable
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($identifierNationalIdNumber) ? $identifierNationalIdNumber : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="identifierNationalIdNumber"
                            :title="__('Edit National ID number')"
                            :text="__('Click to edit the national ID number for this person.')"
                            :changed="$this->isEditingFieldChanged('identifierNationalIdNumber')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="identifierNationalIdNumber" />
            </flux:field>

            {{-- Issuing Authority --}}
            <flux:field class="col-span-4">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.building-library />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-96 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Issuing authority')"
                                :text="__(
                                    'The issuing authority is the organization or government agency that issued the national ID number.',
                                )"
                            >
                                {{ __('Issuing authority') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'identifierNationalIdIssuingAuthority')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-national-id-issuing-authority"
                                type="text"
                                wire:model.live.debounce.500ms="identifierNationalIdIssuingAuthority"
                                autocomplete="new-password"
                                copyable
                                clearable
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($identifierNationalIdIssuingAuthority) ? $identifierNationalIdIssuingAuthority : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="identifierNationalIdIssuingAuthority"
                            :title="__('Edit Issuing Authority')"
                            :text="__('Click to edit the issuing authority for this person.')"
                            :changed="$this->isEditingFieldChanged('identifierNationalIdIssuingAuthority')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="identifierNationalIdIssuingAuthority" />
            </flux:field>

        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-8">

            {{-- Tax ID --}}
            <flux:field class="col-span-3">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.hash />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-124 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Tax ID')"
                                :text="__(
                                    'The tax identification number (TIN) is a unique identifier assigned to individuals and businesses for tax purposes.',
                                )"
                            >
                                {{ __('Tax ID') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'identifierTaxId')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-tax-id"
                                type="text"
                                wire:model.live.debounce.500ms="identifierTaxId"
                                autocomplete="new-password"
                                copyable
                                clearable
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($identifierTaxId) ? $identifierTaxId : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="identifierTaxId"
                            :title="__('Edit Tax ID')"
                            :text="__('Click to edit the tax identification number for this person.')"
                            :changed="$this->isEditingFieldChanged('identifierTaxId')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="identifierTaxId" />
            </flux:field>

            {{-- Social Security Number --}}
            <flux:field class="col-span-3">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.shield-check />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-124 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Social security number')"
                                :text="__(
                                    'The social security number (SSN) is a unique identifier assigned to individuals for social security and tax purposes.',
                                )"
                            >
                                {{ __('Social security number') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'identifierSocialSecurityNumber')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-social-security-number"
                                type="text"
                                wire:model.live.debounce.500ms="identifierSocialSecurityNumber"
                                autocomplete="new-password"
                                copyable
                                clearable
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($identifierSocialSecurityNumber) ? $identifierSocialSecurityNumber : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="identifierSocialSecurityNumber"
                            :title="__('Edit Social Security Number')"
                            :text="__('Click to edit the social security number for this person.')"
                            :changed="$this->isEditingFieldChanged('identifierSocialSecurityNumber')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="identifierSocialSecurityNumber" />
            </flux:field>

        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-8">

            {{-- Pension Insurance Number --}}
            <flux:field class="col-span-3">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.id-card />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-124 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Pension insurance number')"
                                :text="__(
                                    'The pension insurance number is a unique identifier assigned to individuals for pension and retirement purposes.',
                                )"
                            >
                                {{ __('Pension insurance number') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'identifierPensionInsuranceNumber')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-pension-insurance-number"
                                type="text"
                                wire:model.live.debounce.500ms="identifierPensionInsuranceNumber"
                                autocomplete="new-password"
                                copyable
                                clearable
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($identifierPensionInsuranceNumber) ? $identifierPensionInsuranceNumber : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="identifierPensionInsuranceNumber"
                            :title="__('Edit Pension Insurance Number')"
                            :text="__('Click to edit the pension insurance number for this person.')"
                            :changed="$this->isEditingFieldChanged('identifierPensionInsuranceNumber')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="identifierPensionInsuranceNumber" />
            </flux:field>

            {{-- Residence Permit Number --}}
            <flux:field class="col-span-3">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.file-text />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-124 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Residence permit number')"
                                :text="__(
                                    'The residence permit number is a unique identifier assigned to individuals who have been granted permission to reside in a country for a specific period of time.',
                                )"
                            >
                                {{ __('Residence permit number') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'identifierResidencePermitNumber')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-residence-permit-number"
                                type="text"
                                wire:model.live.debounce.500ms="identifierResidencePermitNumber"
                                autocomplete="new-password"
                                copyable
                                clearable
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($identifierResidencePermitNumber) ? $identifierResidencePermitNumber : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="identifierResidencePermitNumber"
                            :title="__('Edit Residence Permit Number')"
                            :text="__('Click to edit the residence permit number for this person.')"
                            :changed="$this->isEditingFieldChanged('identifierResidencePermitNumber')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="identifierResidencePermitNumber" />
            </flux:field>
        </div>
    </div>
</flux:card>
