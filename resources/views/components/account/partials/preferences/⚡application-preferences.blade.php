{{-- resources/views/components/account/partials/preferences/⚡application-preferences.blade.php --}}

{{-- Application preferences: --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('account.preferences.application_preferences.application_preferences') }}
    </flux:heading>

    <div class="grid grid-cols-2 gap-4">
        <flux:select
            label="{{ __('account.preferences.application_preferences.preferred_locale') }}"
            wire:model.live="locale"
        >
            <flux:select.option value="de">
                {{ __('account.preferences.application_preferences.german') }}
            </flux:select.option>

            <flux:select.option value="en">
                {{ __('account.preferences.application_preferences.english') }}
            </flux:select.option>
        </flux:select>

        <flux:select
            label="{{ __('account.preferences.application_preferences.admin_users_per_page') }}"
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
            {{ __('account.preferences.application_preferences.stored_preference') }}
        </flux:callout.heading>

        <flux:callout.text>
            {{ __('account.preferences.application_preferences.the_locale_preference_is_stored_now_and_can_be_connected_to_application_locale_h') }}
        </flux:callout.text>
    </flux:callout>
</flux:card>
