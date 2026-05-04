{{-- resources/views/components/account/partials/preferences/⚡application-preferences.blade.php --}}

{{-- Application preferences: --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Application Preferences') }}
    </flux:heading>

    <div class="grid grid-cols-2 gap-4">
        <flux:select
            label="{{ __('Preferred locale') }}"
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

    <flux:callout
        class="mt-4"
        color="sky"
        icon="info"
    >
        <flux:callout.heading>
            {{ __('Stored preference') }}
        </flux:callout.heading>

        <flux:callout.text>
            {{ __('The locale preference is stored now and can be connected to application locale handling later.') }}
        </flux:callout.text>
    </flux:callout>
</flux:card>
