{{-- resources/views/components/admin/partials/permission-list/⚡meta-overview.blade.php --}}

{{-- Overview part --}}
<flux:card
    class="mt-6"
    x-data="{ showMeta: true }"
>
    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">
            <x-ui.headers.card
                :title="__('admin.permissions.overview.title')"
                :description="__('admin.permissions.overview.description')"
            />
        </div>

        <div class="ml-auto flex shrink-0 items-center gap-3">
            <x-ui.button.show-hide
                size="xs"
                state="showMeta"
                show-label="{{ __('Show overview') }}"
                hide-label="{{ __('Hide overview') }}"
            />
        </div>
    </div>

    <div
        x-show="showMeta"
        x-collapse
    >
        <div class="grid grid-cols-4 gap-3">
            <flux:callout
                class="col-span-4 hyphens-auto md:col-span-1"
                color="sky"
                icon="key-round"
                heading="{{ __('admin.permissions.overview.total.heading') }}"
                text="{{ __('admin.permissions.overview.total.text') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['totalPermissions'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-4 hyphens-auto md:col-span-1"
                color="purple"
                icon="shield-check"
                heading="{{ __('admin.permissions.overview.guards.heading') }}"
                text="{{ __('admin.permissions.overview.guards.text') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['guardCount'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-4 hyphens-auto md:col-span-1"
                color="green"
                icon="badge-check"
                heading="{{ __('admin.permissions.overview.assigned.heading') }}"
                text="{{ __('admin.permissions.overview.assigned.text') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['assignedPermissions'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-4 hyphens-auto md:col-span-1"
                :color="$summary['unassignedPermissions'] > 0 ? 'orange' : 'zinc'"
                icon="badge-x"
                heading="{{ __('admin.permissions.overview.unassigned.heading') }}"
                text="{{ __('admin.permissions.overview.unassigned.text') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['unassignedPermissions'] }}
                </flux:callout.text>
            </flux:callout>
        </div>
    </div>
</flux:card>
