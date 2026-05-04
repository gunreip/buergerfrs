{{-- resources/views/components/admin/partials/role-list/meta.blade.php --}}

{{-- Metablock: Overview --}}
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
            color="sky"
            icon="shield-check"
        >
            <flux:callout.heading>
                {{ __('Total roles') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('The total number of roles currently registered.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $roles->count() }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-4 md:col-span-1"
            color="green"
            icon="check-circle"
        >
            <flux:callout.heading>
                {{ __('Assignable roles') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Roles that can be assigned to users through the UI.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $roles->where('is_assignable', true)->count() }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-4 md:col-span-1"
            color="purple"
            icon="crown"
        >
            <flux:callout.heading>
                {{ __('System roles') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Roles marked as system-level roles.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $roles->where('is_system', true)->count() }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-4 md:col-span-1"
            color="orange"
            icon="users"
        >
            <flux:callout.heading>
                {{ __('Assigned users') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Distinct users currently assigned to at least one role.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $roles->sum('users_count') }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
