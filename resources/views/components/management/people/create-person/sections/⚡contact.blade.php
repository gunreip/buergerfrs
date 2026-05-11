{{-- resources/views/components/management/people/create-person/sections/⚡contact.blade.php --}}

<flux:card>
    <div class="space-y-6">

        <div class="grid gap-4 md:grid-cols-2">

            {{-- Phone --}}
            <flux:field>
                <x-ui.tooltip.trigger
                    title="{{ __('Phone') }}"
                    text="{{ __('The phone number should include the country code, e.g. +49 for Germany.') }}"
                >
                    <flux:label for="create-person-phone">
                        {{ __('Phone') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.phone />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-phone"
                        type="tel"
                        wire:model.blur="phone"
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
                    text="__('The mobile number should include the country code, e.g. +49 for Germany.')"
                >
                    <flux:label for="create-person-mobile">
                        {{ __('Mobile') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.device-phone-mobile />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-mobile"
                        type="tel"
                        wire:model.blur="mobile"
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
                    text="{{ __('The private email should be a valid email address.') }}"
                >
                    <flux:label for="create-person-email-private">
                        {{ __('Private email') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.envelope />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-email-private"
                        type="email"
                        wire:model.blur="emailPrivate"
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
                    text="__('The work email should be a valid email address.')"
                >
                    <flux:label for="create-person-email-work">
                        {{ __('Work email') }}
                    </flux:label>
                </x-ui.tooltip.trigger>

                <flux:input.group>
                    <flux:input.group.prefix>
                        <flux:icon.briefcase-business />
                    </flux:input.group.prefix>

                    <flux:input
                        id="create-person-email-work"
                        type="email"
                        wire:model.blur="emailWork"
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
