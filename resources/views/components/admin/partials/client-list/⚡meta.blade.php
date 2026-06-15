{{-- resources/views/components/admin/partials/client-list/⚡meta.blade.php --}}

{{-- Overview --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('admin.permissions.overview.title')"
        :description="__('admin.client_list.meta.get_a_quick_snapshot_of_client_statistics_including_total_clients_pending_activa',
        )"
    />

    <div class="grid grid-cols-5 gap-3">
        <flux:callout
            class="col-span-5 md:col-span-1"
            color="sky"
            icon="building-2"
        >
            <flux:callout.heading>
                {{ __('admin.client_list.meta.total_clients') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('admin.client_list.meta.total_number_of_registered_clients') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['totalClients'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-5 md:col-span-1"
            color="orange"
            icon="clock"
        >
            <flux:callout.heading>
                {{ __('admin.client_list.meta.pending_clients') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('admin.client_list.meta.clients_waiting_for_activation_or_verification') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['pendingClients'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-5 md:col-span-1"
            color="green"
            icon="badge-check"
        >
            <flux:callout.heading>
                {{ __('admin.client_list.meta.active_clients') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('admin.client_list.meta.clients_currently_marked_as_active') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['activeClients'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-5 md:col-span-1"
            color="purple"
            icon="users"
        >
            <flux:callout.heading>
                {{ __('admin.client_list.meta.with_people') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('admin.client_list.meta.clients_assigned_to_at_least_one_person') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['clientsWithPeople'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-5 md:col-span-1"
            color="zinc"
            icon="user-x"
        >
            <flux:callout.heading>
                {{ __('admin.client_list.meta.without_people') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('admin.client_list.meta.clients_without_assigned_people') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['clientsWithoutPeople'] }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
