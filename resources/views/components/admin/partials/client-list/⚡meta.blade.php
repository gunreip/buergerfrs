{{-- resources/views/components/admin/partials/client-list/⚡meta.blade.php --}}

{{-- Overview --}}
<flux:card
    class="mt-6"
    x-data="{ showMeta: true }"
>
    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">
            <x-ui.headers.card
                :title="__('ui.title.filter')"
                :description="__(
                    'admin.client_list.meta.get_a_quick_snapshot_of_client_statistics_including_total_clients_pending_activa',
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
        <div class="grid grid-cols-5 gap-3">
            <flux:callout
                class="col-span-5 hyphens-auto md:col-span-1"
                color="sky"
                icon="building-2"
                heading="{{ __('admin.client_list.meta.total_clients') }}"
                text="{{ __('admin.client_list.meta.total_number_of_registered_clients') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['totalClients'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-5 hyphens-auto md:col-span-1"
                color="orange"
                icon="clock"
                heading="{{ __('admin.client_list.meta.pending_clients') }}"
                text="{{ __('admin.client_list.meta.clients_waiting_for_activation_or_verification') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['pendingClients'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-5 hyphens-auto md:col-span-1"
                color="green"
                icon="badge-check"
                heading="{{ __('admin.client_list.meta.active_clients') }}"
                text="{{ __('admin.client_list.meta.clients_currently_marked_as_active') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['activeClients'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-5 hyphens-auto md:col-span-1"
                color="purple"
                icon="users"
                heading="{{ __('ui.meta.with-people') }}"
                text="{{ __('admin.client_list.meta.clients_assigned_to_at_least_one_person') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['clientsWithPeople'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-5 hyphens-auto md:col-span-1"
                color="zinc"
                icon="user-x"
                heading="{{ __('ui.meta.without-people') }}"
                text="{{ __('admin.client_list.meta.clients_without_assigned_people') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['clientsWithoutPeople'] }}
                </flux:callout.text>
            </flux:callout>
        </div>
    </div>
</flux:card>
