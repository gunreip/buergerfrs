{{-- resources/views/components/admin/partials/app-settings/meta-overview.blade.php --}}

{{-- Overview of application settings related to role badges and icon registry. This is intended as a diagnostic tool to quickly identify potential misconfigurations or issues with role badge display and icon availability. --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Overview') }}
    </flux:heading>

    <div class="grid grid-cols-4 gap-3">
        {{-- Settings group --}}
        <flux:callout
            class="col-span-4 md:col-span-1"
            color="sky"
            icon="settings"
        >
            <flux:callout.heading>
                {{ __('Settings group') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Primary Spatie settings group used for display settings.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold">
                {{ $summary['settingsGroup'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Role badge entries --}}
        <flux:callout
            class="col-span-4 md:col-span-1"
            color="green"
            icon="badge-check"
        >
            <flux:callout.heading>
                {{ __('Role badge entries') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Configured role badge mappings stored in app settings.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['roleBadgeEntries'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Registered icons --}}
        <flux:callout
            class="col-span-4 md:col-span-1"
            color="purple"
            icon="shield-exclamation"
        >
            <flux:callout.heading>
                {{ __('Registered icons') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Allowed role/user-management icons registered in config.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['registeredRoleUserIcons'] }}
            </flux:callout.text>
        </flux:callout>

        {{-- Missing icons --}}
        <flux:callout
            class="col-span-4 md:col-span-1"
            :color="$summary['missingRegisteredIcons'] > 0 ? 'red' : 'zinc'"
            icon="file-x"
        >
            <flux:callout.heading>
                {{ __('Missing icons') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Registered icons whose Flux view is currently unavailable.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['missingRegisteredIcons'] }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
