{{-- resources/views/components/admin/partials/person-list/⚡meta.blade.php --}}

{{-- Overview --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Overview')"
        :description="__('Summary of people in the system, their linked user accounts and client assignments.')"
    />

    <div class="grid grid-cols-5 gap-3">
        <flux:callout
            class="col-span-5 md:col-span-1"
            color="sky"
            icon="id-card"
        >
            <flux:callout.heading>
                {{ __('Total people') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('Total number of natural persons.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['totalPeople'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-5 md:col-span-1"
            color="green"
            icon="user-check"
        >
            <flux:callout.heading>
                {{ __('With user') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('People linked to a login user account.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['peopleWithUser'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-5 md:col-span-1"
            :color="$summary['peopleWithoutUser'] > 0 ? 'orange' : 'zinc'"
            icon="user-x"
        >
            <flux:callout.heading>
                {{ __('Without user') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('People without a linked login account.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['peopleWithoutUser'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-5 md:col-span-1"
            color="purple"
            icon="building-2"
        >
            <flux:callout.heading>
                {{ __('With clients') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('People assigned to at least one client.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['peopleWithClients'] }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-5 md:col-span-1"
            color="zinc"
            icon="building"
        >
            <flux:callout.heading>
                {{ __('Without clients') }}
            </flux:callout.heading>

            <flux:callout.text class="font-extralight">
                {{ __('People not assigned to a client yet.') }}
            </flux:callout.text>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $summary['peopleWithoutClients'] }}
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
