{{-- resources/views/components/admin/partials/permission-list/⚡meta-overview.blade.php --}}

{{-- Overview part --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('admin.permissions.overview.title')"
        :description="__('admin.permissions.overview.description')"
    />

    <div class="grid grid-cols-4 gap-3">
        <flux:callout
            class="col-span-4 md:col-span-1"
            color="sky"
            icon="key-round"
        >
            <flux:callout.heading>
                {{ __('admin.permissions.overview.total.heading') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('admin.permissions.overview.total.text') }}
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
                {{ __('admin.permissions.overview.guards.heading') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('admin.permissions.overview.guards.text') }}
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
                {{ __('admin.permissions.overview.assigned.heading') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('admin.permissions.overview.assigned.text') }}
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
                {{ __('admin.permissions.overview.unassigned.heading') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('admin.permissions.overview.unassigned.text') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['unassignedPermissions'] }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
