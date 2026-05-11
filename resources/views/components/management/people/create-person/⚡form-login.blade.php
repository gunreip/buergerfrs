{{-- resources/views/components/management/people/create-person/⚡form-login.blade.php --}}

<flux:card>
    <x-ui.headers.card title="{{ __('Login Account') }}">
        <flux:icon.key
            class="size-12"
            stroke-width="1"
        />
    </x-ui.headers.card>

    <div class="space-y-4">
        <flux:field>
            <flux:label for="create-person-email">
                {{ __('Email / User account') }}
            </flux:label>

            <flux:input.group>
                <flux:input.group.prefix>
                    <flux:icon.mail />
                </flux:input.group.prefix>

                <flux:input
                    id="create-person-email"
                    type="email"
                    wire:model.blur="email"
                    autocomplete="off"
                    clearable
                    {{-- :required="$requiredFields['email'] ?? false" --}}
                />
            </flux:input.group>

            <flux:error name="email" />
        </flux:field>

        <flux:callout
            color="sky"
            icon="info"
        >
            <flux:callout.heading>
                {{ __('Login account will be created automatically') }}
            </flux:callout.heading>

            <flux:callout.text>
                {{ __('The user name is generated from first and last name. A temporary password is generated automatically. The initial role is User if available.') }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
