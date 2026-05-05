{{-- resources/views/components/account/⚡settings.blade.php --}}

<flux:card>
    <x-ui.headers.page
        :title="__('Account Settings')"
        :description="__('Manage your personal profile and UI preferences.')"
    />

    <flux:card class="mt-6">
        <flux:heading
            class="mb-4"
            size="lg"
        >
            {{ __('Profile') }}
        </flux:heading>

        <div class="grid grid-cols-2 gap-4">
            <flux:input
                class="col-span-2 md:col-span-1"
                type="text"
                label="{{ __('Nickname') }}"
                wire:model.live="nickname"
                placeholder="{{ __('Optional display name') }}"
            />

            <flux:input
                class="col-span-2 md:col-span-1"
                type="text"
                value="{{ auth()->user()?->email }}"
                label="{{ __('Email') }}"
                readonly
            />
        </div>
    </flux:card>

    <flux:card class="mt-6">
        <flux:heading
            class="mb-4"
            size="lg"
        >
            {{ __('Preferences') }}
        </flux:heading>

        <div class="grid grid-cols-3 gap-4">
            <flux:select
                label="{{ __('Locale') }}"
                wire:model.live="locale"
            >
                <flux:select.option value="de">
                    {{ __('German') }}
                </flux:select.option>

                <flux:select.option value="en">
                    {{ __('English') }}
                </flux:select.option>
            </flux:select>

            <flux:select
                label="{{ __('Appearance') }}"
                wire:model.live="appearance"
            >
                <flux:select.option value="system">
                    {{ __('System') }}
                </flux:select.option>

                <flux:select.option value="light">
                    {{ __('Light') }}
                </flux:select.option>

                <flux:select.option value="dark">
                    {{ __('Dark') }}
                </flux:select.option>
            </flux:select>

            <flux:select
                label="{{ __('Admin users per page') }}"
                wire:model.live="adminUsersPerPage"
            >
                @foreach ([10, 25, 50, 100] as $value)
                    <flux:select.option value="{{ $value }}">
                        {{ $value }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </flux:card>

    <flux:card class="mt-6">
        <flux:heading
            class="mb-4"
            size="lg"
        >
            {{ __('Stored Settings') }}
        </flux:heading>

        <flux:text class="mb-3">
            {{ __('These values are stored in your personal users.settings JSONB column.') }}
        </flux:text>

        <pre class="overflow-auto rounded-lg border border-zinc-700/70 bg-zinc-950/60 p-4 text-xs text-zinc-300">{{ json_encode(auth()->user()?->settings ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
    </flux:card>

    <div class="mt-6 flex justify-end gap-3">
        <flux:button
            type="button"
            variant="primary"
            color="green"
            wire:click="save"
        >
            {{ __('Save Settings') }}
        </flux:button>
    </div>
</flux:card>
