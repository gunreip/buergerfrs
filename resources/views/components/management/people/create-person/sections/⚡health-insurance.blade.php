{{-- resources/views/components/management/people/create-person/sections/⚡health-insurance.blade.php --}}

<flux:card>

    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-2">

            <flux:field class="col-span-2 mb-3">
                <div class="flex items-start justify-between gap-4">
                    <flux:heading size="lg">
                        <span class="border-b-1 border-zinc-800/10 pb-2 pr-4 dark:border-white/20">
                            <flux:icon.heart-pulse class="mr-2 inline-block" />
                            {{ __('Person Health Insurance Information') }}
                        </span>
                    </flux:heading>

                    <div class="flex flex-wrap items-center justify-end gap-2">
                        <x-ui.badge.created-password :password="$generatedPassword" />

                        <x-ui.badge.test-data :show="$isTestData" />
                    </div>
                </div>
            </flux:field>

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
                        name="healthInsuranceProviderId"
                        placeholder="{{ __('Please select') }}"
                        variant="listbox"
                        searchable
                        clearable
                        copyable
                        wire:model.live="healthInsuranceProviderId"
                    >
                        @foreach ($healthInsuranceProviderOptions as $provider)
                            <flux:select.option :value="$provider->id">
                                <div class="flex min-w-0 items-center gap-2">
                                    <flux:badge
                                        class="shrink-0"
                                        size="sm"
                                        variant="subtle"
                                        color="sky"
                                    >
                                        {{ $provider->short_name ?: $provider->code ?: __('Health') }}
                                    </flux:badge>

                                    <span class="truncate">{{ $provider->name }}</span>
                                </div>
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
                        name="healthInsuranceNumber"
                        type="text"
                        autocomplete="new-password"
                        copyable
                        clearable
                        wire:model.live.debounce.500ms="healthInsuranceNumber"
                    />
                </flux:input.group>

                <flux:error name="healthInsuranceNumber" />
            </flux:field>
        </div>
    </div>
</flux:card>
