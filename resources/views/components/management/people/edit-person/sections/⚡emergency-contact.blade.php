{{-- resources/views/components/management/people/edit-person/sections/⚡emergency-contact.blade.php --}}

@php
    $emptyValue = __('Not set');
    $relationshipLabel =
        $emergencyContactRelationship !== ''
            ? __($emergencyContactRelationshipOptions[$emergencyContactRelationship] ?? $emergencyContactRelationship)
            : $emptyValue;
    $emergencyContactPerson = $personNumberOptions->firstWhere('id', $emergencyContactPersonId);
    $emergencyContactPersonLabel =
        $emergencyContactPerson !== null
            ? trim(
                ($emergencyContactPerson->person_number ?: __('No person number')) .
                    ' · ' .
                    $emergencyContactPerson->displayName(),
            )
            : $emptyValue;
    $emergencyContactAvatarPath = trim((string) ($emergencyContactPerson?->avatar_path ?? ''));
    $emergencyContactAvatarUrl =
        $emergencyContactAvatarPath !== '' &&
        \Illuminate\Support\Facades\Storage::disk('public')->exists($emergencyContactAvatarPath)
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($emergencyContactAvatarPath)
            : null;
@endphp

<flux:card>
    <x-ui.headers.card
        :title="__('Person Emergency Contact Information')"
        :description="__('Primary emergency contact assigned to this person.')"
    />

    <div class="space-y-4">

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-8">
            <flux:field class="col-span-4">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.key />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-54 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Emergency Contact Person')"
                                :text="__(
                                    'Select a person from the list of available person numbers to assign as the emergency contact for this person. Search by person number or name.',
                                )"
                            >
                                {{ __('Person number') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'emergencyContactPersonId')
                            <flux:select
                                class="w-full min-w-0 rounded-l-none"
                                class="tabular-nums"
                                id="edit-person-emergency-contact-person-number"
                                name="edit-person-emergency-contact-person-number"
                                variant="listbox"
                                clearable
                                searchable
                                copyable
                                wire:model.live="emergencyContactPersonId"
                                placeholder="{{ __('Choose Person number') }}"
                            >
                                @foreach ($personNumberOptions as $personNumber)
                                    <flux:select.option value="{{ (string) $personNumber->id }}">
                                        @php
                                            $optionAvatarPath = trim((string) ($personNumber->avatar_path ?? ''));
                                            $optionAvatarUrl =
                                                $optionAvatarPath !== '' &&
                                                \Illuminate\Support\Facades\Storage::disk('public')->exists(
                                                    $optionAvatarPath,
                                                )
                                                    ? \Illuminate\Support\Facades\Storage::disk('public')->url(
                                                        $optionAvatarPath,
                                                    )
                                                    : null;
                                        @endphp

                                        <div class="flex min-w-0 items-center gap-2.5">
                                            <flux:avatar
                                                class="mr-2 shrink-0"
                                                :src="$optionAvatarUrl"
                                                :name="$personNumber->displayName()"
                                                color="auto"
                                                size="sm"
                                            />

                                            <span class="min-w-0">
                                                <span class="mr-2 font-mono text-lg text-zinc-700 dark:text-zinc-200">
                                                    {{ $personNumber->person_number ?: __('No person number') }}
                                                </span>
                                                <span
                                                    class="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100"
                                                >
                                                    {{ $personNumber->displayName() }}
                                                </span>
                                            </span>
                                        </div>
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center gap-2 rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                @if ($emergencyContactPerson !== null)
                                    @if ($emergencyContactAvatarUrl !== null)
                                        <img
                                            class="size-7 shrink-0 rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700"
                                            src="{{ $emergencyContactAvatarUrl }}"
                                            alt="{{ __('ui.user_avatar.avatar_for_name', ['name' => $emergencyContactPerson->displayName()]) }}"
                                        >
                                    @else
                                        <flux:avatar
                                            class="size-7 shrink-0"
                                            :name="$emergencyContactPerson->displayName()"
                                        />
                                    @endif
                                @endif

                                <span class="truncate">{{ $emergencyContactPersonLabel }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="emergencyContactPersonId"
                            :title="__('Edit Emergency Contact Person')"
                            :text="__('Click to edit the emergency contact person for this person.')"
                            :changed="$this->isEditingFieldChanged('emergencyContactPersonId')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="emergencyContactPersonId" />
            </flux:field>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-8">

            {{-- Emergency Contact Name --}}
            <flux:field class="col-span-3">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.user />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Emergency Contact Name')"
                                :text="__(
                                    'Enter the name of the emergency contact person. This can be different from the assigned person number.',
                                )"
                            >
                                {{ __('Contact Name') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'emergencyContactName')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-emergency-contact-name"
                                type="text"
                                wire:model.live.debounce.500ms="emergencyContactName"
                                autocomplete="new-password"
                                copyable
                                clearable
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($emergencyContactName) ? $emergencyContactName : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="emergencyContactName"
                            :title="__('Edit Emergency Contact Name')"
                            :text="__('Click to edit the emergency contact name for this person.')"
                            :changed="$this->isEditingFieldChanged('emergencyContactName')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="emergencyContactName" />
            </flux:field>

            {{-- Emergency Contact Relationship --}}
            <flux:field class="col-span-3">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.user-group />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Emergency Contact Relationship')"
                                :text="__(
                                    'Select the relationship of the emergency contact person to this person.',
                                )"
                            >
                                {{ __('Relationship') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'emergencyContactRelationship')
                            <flux:select
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-emergency-contact-relationship"
                                variant="listbox"
                                searchable
                                clearable
                                copyable
                                wire:model.live="emergencyContactRelationship"
                                placeholder="{{ __('Please select') }}"
                            >
                                @foreach ($emergencyContactRelationshipOptions as $value => $label)
                                    <flux:select.option value="{{ $value }}">
                                        {{ __($label) }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span class="truncate">{{ $relationshipLabel }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="emergencyContactRelationship"
                            :title="__('Edit Emergency Contact Relationship')"
                            :text="__('Click to edit the emergency contact relationship for this person.')"
                            :changed="$this->isEditingFieldChanged('emergencyContactRelationship')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="emergencyContactRelationship" />
            </flux:field>

        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-8">

            {{-- Emergency Contact Phone --}}
            <flux:field class="col-span-3">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.phone />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Emergency Contact Phone')"
                                :text="__('Enter the phone number of the emergency contact person.')"
                            >
                                {{ __('management.people.create_person.phone') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'emergencyContactPhone')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-emergency-contact-phone"
                                type="tel"
                                wire:model.live.debounce.500ms="emergencyContactPhone"
                                autocomplete="new-password"
                                copyable
                                clearable
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($emergencyContactPhone) ? $emergencyContactPhone : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="emergencyContactPhone"
                            :title="__('Edit Emergency Contact Phone')"
                            :text="__('Click to edit the emergency contact phone number for this person.')"
                            :changed="$this->isEditingFieldChanged('emergencyContactPhone')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="emergencyContactPhone" />
            </flux:field>

            {{-- Emergency Contact Email --}}
            <flux:field class="col-span-3">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.envelope />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Emergency Contact Email')"
                                :text="__('Enter the email address of the emergency contact person.')"
                            >
                                {{ __('Email') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'emergencyContactEmail')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-emergency-contact-email"
                                type="email"
                                wire:model.live.debounce.500ms="emergencyContactEmail"
                                autocomplete="new-password"
                                copyable
                                clearable
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                <span
                                    class="truncate">{{ filled($emergencyContactEmail) ? $emergencyContactEmail : $emptyValue }}</span>
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="emergencyContactEmail"
                            :title="__('Edit Emergency Contact Email')"
                            :text="__('Click to edit the emergency contact email address for this person.')"
                            :changed="$this->isEditingFieldChanged('emergencyContactEmail')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="emergencyContactEmail" />
            </flux:field>
        </div>

    </div>

</flux:card>
