{{-- resources/views/components/admin/partials/permission-list/⚡meta-overview.blade.php --}}

{{-- Overview part --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Overview')"
        :description="__('Summary of permissions, guards, and role assignments.')"
    />

    <div class="grid grid-cols-4 gap-3">
        <flux:callout
            class="col-span-4 md:col-span-1"
            color="sky"
            icon="key-round"
        >
            <flux:callout.heading>
                {{ __('Total permissions') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('The total number of registered permissions.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['totalPermissions'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-4 md:col-span-1"
            color="purple"
            icon="shield-check"
        >
            <flux:callout.heading>
                {{ __('Guards') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Distinct guards used by registered permissions.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['guardCount'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-4 md:col-span-1"
            color="green"
            icon="badge-check"
        >
            <flux:callout.heading>
                {{ __('Assigned permissions') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Permissions assigned to at least one role.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['assignedPermissions'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-4 md:col-span-1"
            :color="$summary['unassignedPermissions'] > 0 ? 'orange' : 'zinc'"
            icon="badge-x"
        >
            <flux:callout.heading>
                {{ __('Unassigned permissions') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Permissions not currently assigned to any role.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['unassignedPermissions'] }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
