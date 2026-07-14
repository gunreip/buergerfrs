{{-- resources/views/components/admin/partials/user-list/⚡meta.blade.php --}}

<flux:card
    class="mt-6"
    x-data="{ showMeta: true }"
>
    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">
            <x-ui.headers.card
                :title="__('admin.permissions.overview.title')"
                :description="__(
                    'admin.user_list.meta.summary_of_users_in_the_system_their_assigned_roles_and_role_categories',
                )"
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
        <div class="grid gap-3 md:grid-cols-4">
            <flux:callout
                class="col-span-4 md:col-span-1"
                color="red"
                icon="users"
                heading="{{ __('admin.user_list.meta.total_users') }}"
                text="{{ __('admin.user_list.meta.the_total_number_of_registered_users_in_the_system') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['totalUsers'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-4 md:col-span-1"
                color="sky"
                icon="shield-alert"
                heading="{{ __('admin.user_list.meta.users_without_roles') }}"
                text="{{ __('admin.user_list.meta.the_number_of_users_without_assigned_roles') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['withoutRoleUsers'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-4 min-h-6 hyphens-auto md:col-span-1"
                color="green"
                icon="user-check"
                heading="{{ __('admin.roles.labels.assigned_users') }}"
                text="{{ __('admin.user_list.meta.users_grouped_by_assigned_role_category') }}"
            >
                <flux:field class="space-y-1 text-sm">
                    <div class="-mb-1 flex items-center justify-between gap-3">
                        <span class="text-zinc-300">
                            {{ __('admin.permissions.filters.system.label') }}
                        </span>
                        <span class="font-semibold tabular-nums text-zinc-100">
                            {{ $summary['assignedUsersByRoleCategory']['system'] ?? 0 }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-zinc-300">
                            {{ __('admin.user_list.meta.user') }}
                        </span>
                        <span class="font-semibold tabular-nums text-zinc-100">
                            {{ $summary['assignedUsersByRoleCategory']['user'] ?? 0 }}
                        </span>
                    </div>
                </flux:field>
            </flux:callout>

            <flux:callout
                class="col-span-4 hyphens-auto md:col-span-1"
                color="orange"
                icon="shield-alert"
                heading="{{ __('admin.user_list.meta.assignable_roles') }}"
                text="{{ __('admin.user_list.meta.assignable_roles_grouped_by_role_category') }}"
            >
                <flux:field class="space-y-1 text-sm">
                    <div class="-mb-1 flex items-center justify-between gap-4">
                        <span class="text-zinc-300">
                            {{ __('admin.permissions.filters.system.label') }}
                        </span>
                        <span class="font-semibold tabular-nums text-zinc-100">
                            {{ $summary['assignableRolesByCategory']['system'] ?? 0 }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-zinc-300">
                            {{ __('admin.user_list.meta.user') }}
                        </span>
                        <span class="font-semibold tabular-nums text-zinc-100">
                            {{ $summary['assignableRolesByCategory']['user'] ?? 0 }}
                        </span>
                    </div>
                </flux:field>
            </flux:callout>
        </div>

        <div class="mt-3 grid grid-cols-2 gap-3">
            <flux:callout
                class="col-span-2 hyphens-auto md:col-span-1"
                color="purple"
                icon="crown"
                heading="{{ __('admin.user_list.meta.system_roles_users') }}"
                text="{{ __('admin.user_list.meta.number_of_users_assigned_to_each_system_role') }}"
            >
                <div class="mt-3 grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                    @forelse (($summary['assignedUsersByRole']['system'] ?? []) as $role)
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-zinc-300">
                                {{ $role['name'] }}
                            </span>
                            <span class="font-semibold tabular-nums text-zinc-100">
                                {{ $role['usersCount'] }}
                            </span>
                        </div>
                    @empty
                        <flux:text class="col-span-2 text-sm text-zinc-400">
                            {{ __('admin.user_list.meta.no_system_roles_available') }}
                        </flux:text>
                    @endforelse
                </div>
            </flux:callout>

            <flux:callout
                class="col-span-2 md:col-span-1"
                color="fuchsia"
                icon="users"
                heading="{{ __('admin.user_list.meta.user_roles_users') }}"
                text="{{ __('admin.user_list.meta.number_of_users_assigned_to_each_user_role') }}"
            >
                <div class="mt-3 grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                    @forelse (($summary['assignedUsersByRole']['user'] ?? []) as $role)
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-zinc-300">
                                {{ $role['name'] }}
                            </span>
                            <span class="font-semibold tabular-nums text-zinc-100">
                                {{ $role['usersCount'] }}
                            </span>
                        </div>
                    @empty
                        <flux:text class="col-span-2 text-sm text-zinc-400">
                            {{ __('admin.user_list.meta.no_user_roles_available') }}
                        </flux:text>
                    @endforelse
                </div>
            </flux:callout>

        </div>
    </div>
</flux:card>
