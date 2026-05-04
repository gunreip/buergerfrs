{{-- resources/views/components/admin/partials/app-settings/⚡meta-health.blade.php --}}

{{-- Settings health and diagnostics related to role badge configuration and icon registry. This section surfaces potential issues such as roles that lack badge configuration or badge settings that reference non-existent roles, which can help identify misconfigurations that may lead to incorrect or missing badge displays in the UI. --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Settings Health') }}
    </flux:heading>

    <div class="grid grid-cols-2 gap-3">
        {{-- Roles without badge config --}}
        <flux:callout
            class="col-span-2 md:col-span-1"
            :color="$summary['rolesWithoutBadge'] > 0 ? 'orange' : 'green'"
            icon="shield-alert"
        >
            <flux:callout.heading>
                {{ __('Roles without badge config') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Roles that exist in the database but have no configured badge settings.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['rolesWithoutBadge'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Badge config entries that reference non-existent roles --}}
        <flux:callout
            class="col-span-2 md:col-span-1"
            :color="$summary['badgeConfigsWithoutRole'] > 0 ? 'orange' : 'green'"
            icon="shield-alert"
        >
            <flux:callout.heading>
                {{ __('Badge configs without role') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Badge settings that reference roles that no longer exist.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['badgeConfigsWithoutRole'] }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
