{{-- resources/views/components/account/partials/preferences/⚡stored-settings.blade.php --}}

{{-- Stored settings (Admin only): --}}
@role('Admin|Super-Admin')
    <flux:card class="mt-6">
        <flux:heading
            class="mb-4"
            size="lg"
        >
            {{ __('account.preferences.stored_settings.stored_settings') }}
        </flux:heading>

        <flux:text class="mb-3">
            {{ __('account.preferences.stored_settings.these_values_are_stored_in_your_personal_users_settings_jsonb_column') }}
        </flux:text>

        <pre class="overflow-auto rounded-lg border border-zinc-700/70 bg-zinc-950/60 p-4 text-xs text-zinc-300">{{ json_encode(auth()->user()?->settings ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
    </flux:card>
@endrole
