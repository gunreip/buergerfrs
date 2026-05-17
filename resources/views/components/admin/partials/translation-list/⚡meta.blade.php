{{-- resources/views/components/admin/partials/translation-list/⚡meta.blade.php --}}

{{-- Meta / Active filters --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Current result')"
        :description="__('Overview of the active translation filters and the currently matching result set.')"
    />

    <div class="grid flex-1 gap-3 md:grid-cols-4">
        <flux:callout
            color="sky"
            icon="list-filter"
        >
            <flux:callout.heading>
                {{ __('Matching keys') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $filteredTotal }}
            </flux:callout.text>

            <flux:callout.text class="font-extralight">
                {{ __('Translation keys matching the current filters.') }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="orange"
            icon="database"
        >
            <flux:callout.heading>
                {{ __('Total keys') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $total }}
            </flux:callout.text>

            <flux:callout.text class="font-extralight">
                {{ __('Translation keys currently known in the audit table.') }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="{{ $hasActiveFilters ? 'amber' : 'green' }}"
            icon="{{ $hasActiveFilters ? 'funnel' : 'check-circle' }}"
        >
            <flux:callout.heading>
                {{ __('Filter state') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold">
                {{ $hasActiveFilters ? __('Filtered') : __('Unfiltered') }}
            </flux:callout.text>

            <flux:callout.text class="font-extralight">
                {{ $hasActiveFilters ? __('One or more filters are currently active.') : __('No filters are currently active.') }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="purple"
            icon="sliders-horizontal"
        >
            <flux:callout.heading>
                {{ __('Active filters') }}
            </flux:callout.heading>

            <flux:callout.text>
                <div class="flex flex-wrap gap-2">
                    @if (trim($search) !== '')
                        <flux:badge
                            color="blue"
                            variant="subtle"
                        >
                            {{ __('Search') }}: {{ $search }}
                        </flux:badge>
                    @endif

                    @if ($status !== 'all')
                        <flux:badge
                            color="amber"
                            variant="subtle"
                        >
                            {{ __('Status') }}: {{ str($status)->headline() }}
                        </flux:badge>
                    @endif

                    @if ($languageFilter !== '')
                        <flux:badge
                            color="purple"
                            variant="subtle"
                        >
                            {{ __('Language') }}: {{ $languageFilter }}
                        </flux:badge>
                    @endif

                    @if ($fileFilter !== '')
                        <flux:badge
                            color="sky"
                            variant="subtle"
                        >
                            {{ __('File') }}: {{ $fileFilter }}.php
                        </flux:badge>
                    @endif

                    @if ($perPage !== 25)
                        <flux:badge
                            color="zinc"
                            variant="subtle"
                        >
                            {{ __('Per page') }}: {{ $perPage }}
                        </flux:badge>
                    @endif

                    @unless ($hasActiveFilters)
                        <flux:badge
                            color="green"
                            variant="subtle"
                        >
                            {{ __('No active filters') }}
                        </flux:badge>
                    @endunless
                </div>
            </flux:callout.text>
        </flux:callout>
    </div>
</flux:card>
