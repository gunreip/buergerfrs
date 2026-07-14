{{-- resources/views/components/management/people/edit-person/sections/⚡contact.blade.php --}}

@php($emptyValue = __('Not set'))

<flux:card>
    <x-ui.headers.card
        :title="__('Contact')"
        :description="__('Direct person contact data.')"
    />

    <div class="space-y-4">

        {{-- Phone --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-2">

            {{-- Phone --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.phone />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Phone')"
                                :text="__(
                                    'Enter the phone number for the person. This will be used for direct contact purposes.',
                                )"
                            >
                                {{ __('Phone') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'phone')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-phone"
                                wire:model.live="phone"
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                {{ filled($phone) ? $phone : $emptyValue }}
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="phone"
                            :title="__('Edit Phone')"
                            :text="__(
                                    'Edit the phone number for the person. This will be used for direct contact purposes.',
                                )"
                            :changed="$this->isEditingFieldChanged('phone')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="phone" />
            </flux:field>

            {{-- Private Email --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.mail />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Private email')"
                                :text="__(
                                    'Enter the private email for the person. This will be used for personal contact purposes.',
                                )"
                            >
                                {{ __('Private email') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'emailPrivate')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-email-private"
                                type="email"
                                wire:model.live="emailPrivate"
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                {{ filled($emailPrivate) ? $emailPrivate : $emptyValue }}
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="emailPrivate"
                            :title="__('Edit Private Email')"
                            :text="__(
                                    'Edit the private email for the person. This will be used for personal contact purposes.',
                                )"
                            :changed="$this->isEditingFieldChanged('emailPrivate')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="emailPrivate" />
            </flux:field>

        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-2">

            {{-- Mobile --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.phone />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Mobile')"
                                :text="__(
                                    'Enter the mobile number for the person. This will be used for direct contact purposes.',
                                )"
                            >
                                {{ __('Mobile') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'mobile')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-mobile"
                                wire:model.live="mobile"
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                {{ filled($mobile) ? $mobile : $emptyValue }}
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="mobile"
                            :title="__('Edit Mobile')"
                            :text="__(
                                    'Edit the mobile number for the person. This will be used for direct contact purposes.',
                                )"
                            :changed="$this->isEditingFieldChanged('mobile')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="mobile" />
            </flux:field>

            {{-- Work email --}}
            <flux:field>
                <div class="mb-1 flex items-center justify-between gap-2">
                    <flux:input.group class="w-full min-w-0">
                        <flux:input.group.prefix>
                            <flux:icon.mail />
                        </flux:input.group.prefix>
                        <flux:input.group.prefix class="w-64 rounded-l-none">
                            <x-ui.tooltip.trigger
                                :title="__('Work email')"
                                :text="__(
                                    'Enter the work email for the person. This will be used for professional contact purposes.',
                                )"
                            >
                                {{ __('Work email') }}
                            </x-ui.tooltip.trigger>
                        </flux:input.group.prefix>
                        @if ($editingField === 'emailWork')
                            <flux:input
                                class="w-full min-w-0 rounded-l-none"
                                id="edit-person-email-work"
                                type="email"
                                wire:model.live="emailWork"
                            />
                        @else
                            <div
                                class="flex h-10 w-full min-w-0 items-center rounded-l-none border border-zinc-200 px-3 text-sm text-zinc-900 dark:border-zinc-700 dark:text-zinc-100">
                                {{ filled($emailWork) ? $emailWork : $emptyValue }}
                            </div>
                        @endif
                        <x-ui.input.group.suffix-field-edit
                            field="emailWork"
                            :title="__('Edit Work Email')"
                            :text="__(
                                    'Edit the work email for the person. This will be used for professional contact purposes.',
                                )"
                            :changed="$this->isEditingFieldChanged('emailWork')"
                        />
                    </flux:input.group>
                </div>
                <flux:error name="emailWork" />
            </flux:field>
        </div>
    </div>
</flux:card>
