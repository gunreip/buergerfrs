{{-- resources/views/components/admin/partials/app-settings/⚡meta-health.blade.php --}}

{{-- Settings health and diagnostics related to role badge configuration and icon registry. This section surfaces potential issues such as roles that lack badge configuration or badge settings that reference non-existent roles, which can help identify misconfigurations that may lead to incorrect or missing badge displays in the UI. --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('admin.app_settings.meta_health.settings_health') }}
    </flux:heading>

    <div class="grid grid-cols-2 gap-3">
        {{-- Roles without badge config --}}
        <flux:callout
            class="col-span-2 md:col-span-1"
            :color="$summary['rolesWithoutBadge'] > 0 ? 'orange' : 'green'"
            icon="shield-alert"
        >
            <flux:callout.heading>
                {{ __('admin.app_settings.meta_health.roles_without_badge_config') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('admin.app_settings.meta_health.roles_that_exist_in_the_database_but_have_no_configured_badge_settings') }}
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
                {{ __('admin.app_settings.meta_health.badge_configs_without_role') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('admin.app_settings.meta_health.badge_settings_that_reference_roles_that_no_longer_exist') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['badgeConfigsWithoutRole'] }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
