{{-- resources/views/components/admin/partials/client-list/⚡meta.blade.php --}}

{{-- Overview --}}
<flux:card class="mt-6">
    <flux:heading
        class="mb-4"
        size="lg"
    >
        {{ __('Overview') }}
    </flux:heading>

    <div class="grid grid-cols-5 gap-3">
        <flux:callout
            class="col-span-5 md:col-span-1"
            color="sky"
            icon="building-2"
        >
            <flux:callout.heading>
                {{ __('Total clients') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Total number of registered clients.') }}
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
                {{ __('Pending clients') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Clients waiting for activation or verification.') }}
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
                {{ __('Active clients') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Clients currently marked as active.') }}
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
                {{ __('With people') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Clients assigned to at least one person.') }}
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
                {{ __('Without people') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Clients without assigned people.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['clientsWithoutPeople'] }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
