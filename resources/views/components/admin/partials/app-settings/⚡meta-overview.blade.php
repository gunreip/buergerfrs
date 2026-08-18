{{-- resources/views/components/admin/partials/app-settings/meta-overview.blade.php --}}

{{-- Overview of application settings related to role badges and icon registry. This is intended as a diagnostic tool to quickly identify potential misconfigurations or issues with role badge display and icon availability. --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('ui.title.filter') }}
    </flux:heading>

    <div class="grid grid-cols-5 gap-3">
        {{-- Settings group --}}
        <flux:callout
            class="col-span-4 hyphens-auto md:col-span-1"
            color="sky"
            icon="settings"
            heading="{{ __('admin.app_settings.meta_overview.settings_group') }}"
            text="{{ __('admin.app_settings.meta_overview.primary_spatie_settings_group_used_for_display_settings') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold">
                {{ $summary['settingsGroup'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Application language --}}
        <flux:callout
            class="col-span-6 hyphens-auto md:col-span-1"
            color="sky"
            icon="languages"
            heading="{{ __('admin.app_settings.meta_overview.app_language') }}"
            text="{{ __('admin.app_settings.meta_overview.global_locale_currently_used_by_the_application') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold">
                {{ $summary['locale'] ?? app()->getLocale() }}
            </flux:callout.text>
        </flux:callout>

        {{-- Role badge entries --}}
        <flux:callout
            class="col-span-4 hyphens-auto md:col-span-1"
            color="green"
            icon="badge-check"
            heading="{{ __('admin.app_settings.meta_overview.role_badge_entries') }}"
            text="{{ __('admin.app_settings.meta_overview.configured_role_badge_mappings_stored_in_app_settings') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['roleBadgeEntries'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Registered icons --}}
        <flux:callout
            class="col-span-4 hyphens-auto md:col-span-1"
            color="purple"
            icon="shield-exclamation"
            heading="{{ __('admin.app_settings.meta_overview.registered_icons') }}"
            text="{{ __('admin.app_settings.meta_overview.allowed_role_user_management_icons_registered_in_config') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['registeredRoleUserIcons'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Missing icons --}}
        <flux:callout
            class="col-span-4 hyphens-auto md:col-span-1"
            :color="$summary['missingRegisteredIcons'] > 0 ? 'red' : 'zinc'"
            icon="file-x"
            heading="{{ __('admin.app_settings.meta_overview.missing_icons') }}"
            text="{{ __('admin.app_settings.meta_overview.registered_icons_whose_flux_view_is_currently_unavailable') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['missingRegisteredIcons'] }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
