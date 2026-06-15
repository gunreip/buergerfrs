{{-- resources/views/components/account/partials/preferences/⚡current-user.blade.php --}}

{{-- Current user preferences: --}}
<flux:card class="mt-6">
    <div class="flex items-center justify-between gap-4">
        <div class="flex min-w-0 items-center gap-3">
            <x-ui.user-avatar
                size="lg"
                :name="$currentUser?->name"
                :user="auth()->user()"
            />

            <div class="min-w-0">
                <flux:heading
                    class="truncate"
                    size="md"
                >
                    {{ $currentUser?->name }}
                </flux:heading>

                @if ($nickname !== '')
                    <flux:text class="truncate">
                        <span class="font-semibold">{{ __('account.preferences.current_user.nickname') }}:</span> {{ $nickname }}
                    </flux:text>
                @endif

                <flux:text class="truncate">
                    {{ $currentUser?->email }}
                </flux:text>
            </div>
        </div>

        <flux:button
            type="button"
            variant="ghost"
            icon="user-cog"
            :href="route('profile.edit')"
            wire:navigate
        >
            {{ __('account.preferences.current_user.edit_profile') }}
        </flux:button>
    </div>
</flux:card>
