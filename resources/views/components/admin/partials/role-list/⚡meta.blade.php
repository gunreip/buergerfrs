{{-- resources/views/components/admin/partials/role-list/⚡meta.blade.php --}}

{{-- Metablock: Overview --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Overview')"
        :description="__(
            'Get a quick snapshot of role statistics, including total roles, assignable roles, system roles, and user assignments.',
        )"
    />

    <div class="grid gap-3 md:grid-cols-4">
        <flux:callout
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
