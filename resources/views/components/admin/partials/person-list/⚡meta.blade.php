{{-- resources/views/components/admin/partials/person-list/⚡meta.blade.php --}}

{{-- Overview --}}
<flux:card
    class="mt-6"
    x-data="{ showMeta: true }"
>
    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">
            <x-ui.headers.card
                :title="__('ui.title.filter')"
                :description="__('Summary of people in the system, their linked user accounts and client assignments.')"
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
                icon="id-card"
                heading="{{ __('Total people') }}"
                text="{{ __('Total number of natural persons.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['totalPeople'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-5 hyphens-auto md:col-span-1"
                color="green"
                icon="user-check"
                heading="{{ __('With user') }}"
                text="{{ __('People linked to a login user account.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['peopleWithUser'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-5 hyphens-auto md:col-span-1"
                :color="$summary['peopleWithoutUser'] > 0 ? 'orange' : 'zinc'"
                icon="user-x"
                heading="{{ __('Without user') }}"
                text="{{ __('People without a linked login account.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['peopleWithoutUser'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-5 hyphens-auto md:col-span-1"
                color="purple"
                icon="building-2"
                heading="{{ __('With clients') }}"
                text="{{ __('People assigned to at least one client.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['peopleWithClients'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-5 hyphens-auto md:col-span-1"
                color="zinc"
                icon="building"
                heading="{{ __('Without clients') }}"
                text="{{ __('People not assigned to a client yet.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['peopleWithoutClients'] }}
                </flux:callout.text>
            </flux:callout>
        </div>
    </div>
</flux:card>
