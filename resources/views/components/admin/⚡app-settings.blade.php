{{-- resources/views/components/admin/⚡app-settings.blade.php --}}

<flux:card>
    <x-ui.headers.page
        :title="__('ui.app-settings')"
        :description="__('admin.app_settings.review_global_application_settings_role_badge_configuration_and_icon_registry_he')"
    />

    {{-- Overview of application settings related to role badges and icon registry. This is intended as a diagnostic tool to quickly identify potential misconfigurations or issues with role badge display and icon availability. --}}
    @include('components.admin.partials.app-settings.⚡meta-overview')

    {{-- Global application language setting. This controls the app locale used for translated interface strings across the application. --}}
    @include('components.admin.partials.app-settings.⚡locale')

    {{-- Settings health and diagnostics related to role badge configuration and icon registry. This section surfaces potential issues such as roles that lack badge configuration or badge settings that reference non-existent roles, which can help identify misconfigurations that may lead to incorrect or missing badge displays in the UI. --}}
    @include('components.admin.partials.app-settings.⚡meta-health')

    {{-- Table role badge settings and icon registry entries for detailed diagnostics. This allows for a granular review of each role badge configuration, including visual previews, to identify specific misconfigurations such as incorrect color/variant/icon settings or missing icons that could affect the display of role badges in the application UI. The icon registry table similarly surfaces issues with registered icons that may impact their availability for use in badges or other UI elements. --}}
    @include('components.admin.partials.app-settings.⚡table-role-badges')

    {{-- Table for icon registry entries with availability status to identify any issues with registered icons that may affect their use in role badges or other UI elements. --}}
    @include('components.admin.partials.app-settings.⚡table-icon-registry')

</flux:card>
