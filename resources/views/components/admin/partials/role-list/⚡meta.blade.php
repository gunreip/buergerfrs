{{-- resources/views/components/admin/partials/role-list/⚡meta.blade.php --}}

{{-- Metablock: Overview --}}
<flux:card
    class="mt-6"
    x-data="{ showMeta: true }"
>
    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">
            <x-ui.headers.card
                :title="__('ui.title.overview')"
                :description="__(
                    'Get a quick snapshot of role statistics, including total roles, assignable roles, system roles, and user assignments.',
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
                class="hyphens-auto md:col-span-1"
                color="sky"
                icon="shield-check"
                heading="{{ __('Total roles') }}"
                text="{{ __('The total number of roles currently registered.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $roles->count() }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="hyphens-auto md:col-span-1"
                color="green"
                icon="check-circle"
                heading="{{ __('ui.meta.assignable-roles') }}"
                text="{{ __('Roles that can be assigned to users through the UI.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $roles->where('is_assignable', true)->count() }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="hyphens-auto md:col-span-1"
                color="purple"
                icon="crown"
                heading="{{ __('System roles') }}"
                text="{{ __('Roles marked as system-level roles.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $roles->where('is_system', true)->count() }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="hyphens-auto md:col-span-1"
                color="orange"
                icon="users"
                heading="{{ __('ui.assign.assigned.assigned-users') }}"
                text="{{ __('Distinct users currently assigned to at least one role.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $roles->sum('users_count') }}
                </flux:callout.text>
            </flux:callout>
        </div>
    </div>
</flux:card>
