{{-- resources/views/components/management/people/edit-person/⚡meta.blade.php --}}

<flux:card class="mt-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0 space-y-2">
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.headers.card
                    class="font-mono"
                    title="{{ $person->person_number ?: __('No person number') }}"
                    description="{{ __('admin.user_list.table.id') }}: {{ $person->id }} - {{ __('ui.labels.name') }}: {{ $person->displayName() }}"
                    size="xl"
                >
                </x-ui.headers.card>

            </div>

            <flux:text class="text-sm">
                <flux:field class="grid grid-cols-8">
                    <span class="font-semibold">{{ __('admin.client_list.table.created') }}:</span>
                    <x-ui.date-time.date
                        :format="'dd, DD. MMM. YYYY'"
                        :value="$person->created_at"
                    />
                    <x-ui.date-time.time :value="$person->created_at" />
                    <span class="font-semibold">{{ __('Updated') }}:</span>
                    <x-ui.date-time.date
                        :format="'dd, DD. MMM. YYYY'"
                        :value="$person->updated_at"
                    />
                    <x-ui.date-time.time :value="$person->updated_at" />
                </flux:field>
            </flux:text>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-ui.badge.test-data :show="$person->is_test_data" />

            <flux:button
                icon="arrow-left"
                variant="ghost"
                :href="route('management.people.index')"
                wire:navigate
            >
                {{ __('pages.settings.two_factor_setup_modal.back') }}
            </flux:button>
        </div>
    </div>
</flux:card>
