{{-- resources/views/components/management/people/create-person/sections/⚡contact.blade.php --}}

<flux:card>
    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-2">

            <flux:field class="col-span-2 mb-3">
                <div class="flex items-start justify-between gap-4">
                    <flux:heading size="lg">
                        <span class="border-b-1 border-zinc-800/10 pb-2 pr-4 dark:border-white/20">
                            <flux:icon.phone class="mr-2 inline-block" />
                            {{ __('Person Contact Information') }}
                        </span>
                    </flux:heading>

                    <div class="flex flex-wrap items-center justify-end gap-2">
                        <x-ui.badge.created-password :password="$generatedPassword" />

                        <x-ui.badge.test-data :show="$isTestData" />
                    </div>
                </div>
            </flux:field>

            {{-- Phone --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Phone') }}"
                    field="phone"
                    text="{{ __('The phone number should include the country code, e.g. +49 for Germany.') }}"
                    :required="$this->isRequiredField('phone')"
                >
                    <flux:label for="create-person-phone">
                        {{ __('Phone') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('phone')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.phone />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-phone"
                        type="tel"
                        wire:model.live.debounce.500ms="phone"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="phone" />
            </flux:field>

            {{-- Mobile --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Mobile') }}"
                    field="mobile"
                    text="{{ __('The mobile number should include the country code, e.g. +49 for Germany.') }}"
                    :required="$this->isRequiredField('mobile')"
                >
                    <flux:label for="create-person-mobile">
                        {{ __('Mobile') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('mobile')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.device-phone-mobile />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-mobile"
                        type="tel"
                        wire:model.live.debounce.500ms="mobile"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="mobile" />
            </flux:field>
        </div>

        <div class="grid gap-4 md:grid-cols-2">

            {{-- Private email --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Private email') }}"
                    field="emailPrivate"
                    text="{{ __('The private email should be a valid email address.') }}"
                    :required="$this->isRequiredField('emailPrivate')"
                >
                    <flux:label for="create-person-email-private">
                        {{ __('Private email') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('emailPrivate')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.envelope />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-email-private"
                        type="email"
                        wire:model.live.debounce.500ms="emailPrivate"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="emailPrivate" />
            </flux:field>

            {{-- Work email --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Work email') }}"
                    field="emailWork"
                    text="{{ __('The work email should be a valid email address.') }}"
                    :required="$this->isRequiredField('emailWork')"
                >
                    <flux:label for="create-person-email-work">
                        {{ __('Work email') }}
                        <x-ui.tooltip.badge-required :required="$this->isRequiredField('emailWork')" />
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.briefcase-business />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-email-work"
                        type="email"
                        wire:model.live.debounce.500ms="emailWork"
                        autocomplete="new-password"
                        copyable
                        clearable
                    />
                </flux:input.group>

                <flux:error name="emailWork" />
            </flux:field>
        </div>
    </div>
</flux:card>
