{{-- resources/views/components/admin/partials/app-settings/⚡locale.blade.php --}}

<flux:card class="mt-6">
    <div class="flex items-start justify-between gap-4">
        <x-ui.headers.card
            :title="__('Application language')"
            :description="__(
                'Global application language used for interface translations. This setting applies to all users.',
            )"
        />

        <flux:badge
            variant="subtle"
            color="sky"
        >
            {{ __('Current') }}: {{ $locale }}
        </flux:badge>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-2">
        @foreach ($availableLocales as $availableLocale)
            <flux:button
                type="button"
                size="sm"
                :variant="$locale === $availableLocale ? 'primary' : 'ghost'"
                wire:click="setLocale('{{ $availableLocale }}')"
            >
                {{ match ($availableLocale) {
                    'de' => __('Deutsch'),
                    'en' => __('English'),
                    default => strtoupper($availableLocale),
                } }}
            </flux:button>
        @endforeach
    </div>

    <div class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
        {{ __('Changing this setting affects the global app locale for subsequent requests and rendered views.') }}
    </div>
</flux:card>
