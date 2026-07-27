{{-- resources/views/components/management/people/partials/person-overview/⚡meta.blade.php --}}

<flux:card
    class="mt-6"
    x-data="{ showMeta: true }"
>
    <div class="flex w-full items-center justify-between gap-3">
        <x-ui.headers.card
            :title="__('admin.permissions.overview.title')"
            :description="__('Current person data volume and relation coverage.')"
        />

        <x-ui.button.show-hide
            size="xs"
            state="showMeta"
        />
    </div>

    <div
        x-show="showMeta"
        x-collapse
    >
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4 xl:grid-cols-8">
            <flux:callout
                color="sky"
                icon="users"
                heading="{{ __('Total') }}"
                text="{{ __('All person records.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['totalPeople'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="blue"
                icon="list-filter"
                heading="{{ __('admin.translation_list.meta.filtered') }}"
                text="{{ __('Records matching the active filters.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['filteredPeople'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="amber"
                icon="sparkles"
                heading="{{ __('ui.badge.test-data') }}"
                text="{{ __('Records marked as test data.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['testPeople'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="green"
                icon="shield-check"
                heading="{{ __('Real data') }}"
                text="{{ __('Records not marked as test data.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['realPeople'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="emerald"
                icon="user-check"
                heading="{{ __('With user') }}"
                text="{{ __('Records linked to a login account.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['peopleWithUser'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="orange"
                icon="user-x"
                heading="{{ __('Without user') }}"
                text="{{ __('Records without login account.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['peopleWithoutUser'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="purple"
                icon="building-2"
                heading="{{ __('With clients') }}"
                text="{{ __('Records assigned to clients.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['peopleWithClients'] }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                color="zinc"
                icon="building"
                heading="{{ __('No clients') }}"
                text="{{ __('Records without client assignment.') }}"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $summary['peopleWithoutClients'] }}
                </flux:callout.text>
            </flux:callout>
        </div>
    </div>
</flux:card>
