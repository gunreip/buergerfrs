{{-- resources/views/components/account/⚡preferences.blade.php --}}

@php
    $currentUser = auth()->user();
    $nickname = trim((string) $currentUser?->setting('profile.nickname', ''));
@endphp

<flux:card>
    <x-ui.headers.page
        title="{{ __('User Preferences') }}"
        description="{{ __('Manage your personal application preferences.') }}"
    />

    {{-- Current user preferences: --}}
    @include('components.account.partials.preferences.⚡current-user')

    {{-- Application preferences: --}}
    @include('components.account.partials.preferences.⚡application-preferences')

    {{-- Stored settings (Admin only): --}}
    @include('components.account.partials.preferences.⚡stored-settings')

    <div class="mt-6 flex justify-end gap-3">
        <flux:button
            type="button"
            variant="primary"
            color="green"
            wire:click="save"
        >
            {{ __('Save Preferences') }}
        </flux:button>
    </div>

</flux:card>
