{{-- resources/views/components/admin/partials/user-list/⚡meta.blade.php --}}

<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Overview') }}
    </flux:heading>

    <div class="grid grid-cols-4 gap-3">
        <flux:callout
            class="col-span-4 md:col-span-1"
            color="red"
            icon="users"
        >
            <flux:callout.heading>
                {{ __('Total users') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('The total number of registered users in the system.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['totalUsers'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-4 md:col-span-1"
            color="sky"
            icon="shield-alert"
        >
            <flux:callout.heading>
                {{ __('Users without roles') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('The number of users without assigned roles.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['withoutRoleUsers'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-4 md:col-span-1"
            color="green"
            icon="user-check"
        >
            <flux:callout.heading>
                {{ __('Assigned users') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Users grouped by assigned role category.') }}
            </flux:callout.text>

            <div class="mt-3 space-y-1 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <span class="text-zinc-300">
                        {{ __('System') }}
                    </span>
                    <span class="font-semibold tabular-nums text-zinc-100">
                        {{ $summary['assignedUsersByRoleCategory']['system'] ?? 0 }}
                    </span>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <span class="text-zinc-300">
                        {{ __('User') }}
                    </span>
                    <span class="font-semibold tabular-nums text-zinc-100">
                        {{ $summary['assignedUsersByRoleCategory']['user'] ?? 0 }}
                    </span>
                </div>
            </div>
        </flux:callout>

        <flux:callout
            class="col-span-4 md:col-span-1"
            color="orange"
            icon="shield-alert"
        >
            <flux:callout.heading>
                {{ __('Assignable roles') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Assignable roles grouped by role category.') }}
            </flux:callout.text>

            <div class="mt-3 space-y-1 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <span class="text-zinc-300">
                        {{ __('System') }}
                    </span>
                    <span class="font-semibold tabular-nums text-zinc-100">
                        {{ $summary['assignableRolesByCategory']['system'] ?? 0 }}
                    </span>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <span class="text-zinc-300">
                        {{ __('User') }}
                    </span>
                    <span class="font-semibold tabular-nums text-zinc-100">
                        {{ $summary['assignableRolesByCategory']['user'] ?? 0 }}
                    </span>
                </div>
            </div>
        </flux:callout>
    </div>

    <div class="mt-3 grid grid-cols-2 gap-3">
        <flux:callout
            class="col-span-2 md:col-span-1"
            color="purple"
            icon="crown"
        >
            <flux:callout.heading>
                {{ __('System roles / Users') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Number of users assigned to each system role.') }}
            </flux:callout.text>

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
                        {{ __('No system roles available.') }}
                    </flux:text>
                @endforelse
            </div>
        </flux:callout>

        <flux:callout
            class="col-span-2 md:col-span-1"
            color="fuchsia"
            icon="users"
        >
            <flux:callout.heading>
                {{ __('User roles / Users') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Number of users assigned to each user role.') }}
            </flux:callout.text>

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
                        {{ __('No user roles available.') }}
                    </flux:text>
                @endforelse
            </div>
        </flux:callout>
    </div>
</flux:card>
