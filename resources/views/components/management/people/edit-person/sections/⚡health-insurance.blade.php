{{-- resources/views/components/management/people/edit-person/sections/⚡health-insurance.blade.php --}}

@php
    $emptyValue = __('Not set');
    $healthInsuranceProvider = $healthInsuranceProviderOptions->firstWhere('id', $healthInsuranceProviderId);
    $healthInsuranceProviderLabel = $healthInsuranceProvider?->name;
@endphp

<flux:card>
    <x-ui.headers.card
        :title="__('Person Health Insurance Information')"
        :description="__('Primary health insurance assigned to this person.')"
    />

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-8">

        {{-- Health Insurance Provider --}}
        <flux:field class="col-span-5">
            <div class="mb-1 flex items-center justify-between gap-2">
                <flux:input.group class="w-full min-w-0">
                    <flux:input.group.prefix>
                        <flux:icon.shield-check />
                    </flux:input.group.prefix>
                    <flux:input.group.prefix class="w-96 rounded-l-none">
                        <x-ui.tooltip.trigger
                            :title="__('Health insurance provider')"
                            :text="__('The health insurance provider assigned to this person.')"
                        >
                            {{ __('Health insurance provider') }}
                        </x-ui.tooltip.trigger>
                    </flux:input.group.prefix>
                    @if ($editingField === 'healthInsuranceProviderId')
                        <flux:select
                            class="w-full min-w-0 rounded-l-none"
                            id="edit-person-health-insurance-provider"
                            name="healthInsuranceProviderId"
                            placeholder="{{ __('Please select') }}"
                            variant="listbox"
                            searchable
                            clearable
                            copyable
                            wire:model.live="healthInsuranceProviderId"
                        >
                            @foreach ($healthInsuranceProviderOptions as $provider)
                                <flux:select.option value="{{ (string) $provider->id }}">
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
                    @else
                        <div
                            class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                            <span
                                class="truncate">{{ filled($healthInsuranceProviderLabel) ? $healthInsuranceProviderLabel : $emptyValue }}</span>
                        </div>
                    @endif
                    <x-ui.input.group.suffix-field-edit
                            field="healthInsuranceProviderId"
                            :title="__('Edit health insurance provider')"
                            :text="__('Click to edit the health insurance provider assigned to this person.')"
                            :changed="$this->isEditingFieldChanged('healthInsuranceProviderId')"
                        />
                </flux:input.group>
            </div>
            <flux:error name="healthInsuranceProviderId" />
        </flux:field>

        {{-- Health Insurance Number --}}
        <flux:field class="col-span-3">
            <div class="mb-1 flex items-center justify-between gap-2">
                <flux:input.group class="w-full min-w-0">
                    <flux:input.group.prefix>
                        <flux:icon.shield-check />
                    </flux:input.group.prefix>
                    <flux:input.group.prefix class="w-124 rounded-l-none">
                        <x-ui.tooltip.trigger
                            :title="__('Health insurance number')"
                            :text="__('The health insurance number assigned to this person.')"
                        >
                            {{ __('Health insurance number') }}
                        </x-ui.tooltip.trigger>
                    </flux:input.group.prefix>
                    @if ($editingField === 'healthInsuranceNumber')
                        <flux:input
                            class="w-full min-w-0 rounded-l-none"
                            id="edit-person-health-insurance-number"
                            name="healthInsuranceNumber"
                            type="text"
                            autocomplete="new-password"
                            copyable
                            clearable
                            wire:model.live.debounce.500ms="healthInsuranceNumber"
                        />
                    @else
                        <div
                            class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                            <span
                                class="truncate">{{ filled($healthInsuranceNumber) ? $healthInsuranceNumber : $emptyValue }}</span>
                        </div>
                    @endif
                    <x-ui.input.group.suffix-field-edit
                            field="healthInsuranceNumber"
                            :title="__('Edit health insurance number')"
                            :text="__('Click to edit the health insurance number assigned to this person.')"
                            :changed="$this->isEditingFieldChanged('healthInsuranceNumber')"
                        />
                </flux:input.group>
            </div>
            <flux:error name="healthInsuranceNumber" />
        </flux:field>
    </div>
</flux:card>
