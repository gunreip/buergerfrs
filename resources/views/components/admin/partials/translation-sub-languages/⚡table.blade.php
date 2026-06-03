{{-- resources/views/components/admin/partials/translation-sub-languages/⚡table.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Sub-Language Details')"
        :description="__(
            'Detailed view of sub-languages, their relation to main languages, and translation coverage metrics.',
        )"
    />

    @if ($subLocales->isEmpty())
        <flux:callout
            class="mt-6"
            color="amber"
            icon="triangle-alert"
        >
            @if ($hasActiveFilters)
                <flux:callout.heading>
                    {{ __('No matching sub-languages found') }}
                </flux:callout.heading>

                <flux:callout.text>
                    {{ __('Adjust or reset the filters to see available active sub-languages.') }}
                </flux:callout.text>
            @else
                <flux:callout.heading>
                    {{ __('No active sub-languages found') }}
                </flux:callout.heading>

                <flux:callout.text>
                    {{ __('Activate locale variants such as de_AT in locale management to start adding overrides.') }}
                </flux:callout.text>
            @endif
        </flux:callout>
    @else
        <div
            class="mx-auto max-w-full scroll-mt-6"
            id="translation-sub-languages-table"
        >
            <div class="overflow-hidden rounded-t-lg">
                <flux:table class="app-table">
                    <flux:table.columns class="bg-zinc-800 text-zinc-400">
                        <flux:table.column
                            class="w-28 tabular-nums"
                            sortable
                            :sorted="$sortField === 'id'"
                            :direction="$sortDirection"
                            align="center"
                            wire:click="sortBy('id')"
                        >
                            <x-ui.tooltip.trigger
                                class="ml-3"
                                :title="__('ID')"
                                :text="__('Unique identifier for the sub-language locale entry.')"
                            >
                                {{ __('ID') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            sortable
                            :sorted="$sortField === 'locale'"
                            :direction="$sortDirection"
                            wire:click="sortBy('locale')"
                        >
                            <x-ui.tooltip.trigger
                                class="ml-3"
                                :title="__('Sub-languages')"
                                :text="__(
                                    'Locale variants such as de_AT that extend a main language with specific overrides.',
                                )"
                            >
                                {{ __('Sub-languages') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            sortable
                            :sorted="$sortField === 'base_locale'"
                            :direction="$sortDirection"
                            wire:click="sortBy('base_locale')"
                        >
                            <x-ui.tooltip.trigger
                                class="ml-3"
                                :title="__('Main language')"
                                :text="__('Base language that the sub-language overlays and extends.')"
                            >
                                {{ __('Main language') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            class="w-28"
                            align="end"
                        >
                            <x-ui.tooltip.trigger
                                class="mr-3"
                                :title="__('Overrides')"
                                :text="__(
                                    'Number of translation values that are provided by the sub-language and override the main language.',
                                )"
                            >
                                {{ __('Overrides') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            class="w-36"
                            align="end"
                        >
                            <x-ui.tooltip.trigger
                                class="mr-3"
                                :title="__('Main translated')"
                                :text="__('Translated key count available in the main language alone.')"
                            >
                                {{ __('Main translated') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            class="w-42"
                            align="end"
                        >
                            <x-ui.tooltip.trigger
                                class="mr-3"
                                :title="__('Effective translated')"
                                :text="__(
                                    'Translated key count after combining the main language with the sub-language overrides.',
                                )"
                            >
                                {{ __('Effective translated') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column class="w-56">
                            <x-ui.tooltip.trigger
                                class="ml-3"
                                :title="__('Effective coverage')"
                                :text="__(
                                    'Coverage percentage after merging the main language and the sub-language overrides.',
                                )"
                            >
                                {{ __('Effective coverage') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($subLocales as $entry)
                            @php
                                $coverageColor =
                                    $entry->effective_coverage_pct >= 90
                                        ? 'text-green-600 dark:text-green-400'
                                        : ($entry->effective_coverage_pct >= 60
                                            ? 'text-amber-600 dark:text-amber-400'
                                            : 'text-red-600 dark:text-red-400');

                                $barColor =
                                    $entry->effective_coverage_pct >= 90
                                        ? 'bg-green-500'
                                        : ($entry->effective_coverage_pct >= 60
                                            ? 'bg-amber-500'
                                            : 'bg-red-500');
                            @endphp

                            <flux:table.row wire:key="sub-language-{{ $entry->id }}">
                                <flux:table.cell
                                    class="w-28 align-top tabular-nums text-zinc-500 dark:text-zinc-400"
                                    align="center"
                                >
                                    #{{ $entry->id }}
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    <div class="flex items-start gap-3">
                                        <x-ui.locale.flag
                                            :locale="$entry->locale"
                                            size="lg"
                                        />

                                        <div class="min-w-0 space-y-0.5">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span
                                                    class="font-mono font-semibold uppercase text-zinc-800 dark:text-zinc-200"
                                                >
                                                    {{ $entry->locale }}
                                                </span>
                                            </div>

                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $entry->display_name }}
                                            </div>
                                        </div>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    <span class="font-mono font-semibold uppercase text-zinc-700 dark:text-zinc-300">
                                        {{ $entry->base_locale }}
                                    </span>
                                </flux:table.cell>

                                <flux:table.cell
                                    class="{{ $entry->override_count > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-500 dark:text-zinc-400' }} text-right align-top tabular-nums"
                                    align="end"
                                >
                                    {{ number_format($entry->override_count) }}
                                </flux:table.cell>

                                <flux:table.cell
                                    class="text-right align-top tabular-nums text-zinc-500 dark:text-zinc-400"
                                    align="end"
                                >
                                    {{ number_format($entry->base_translated_count) }}
                                </flux:table.cell>

                                <flux:table.cell
                                    class="text-right align-top tabular-nums text-green-700 dark:text-green-400"
                                    align="end"
                                >
                                    {{ number_format($entry->effective_translated_count) }}
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 w-32 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                                            <div
                                                class="{{ $barColor }} h-full rounded-full transition-all"
                                                style="width: {{ $entry->effective_coverage_pct }}%"
                                            ></div>
                                        </div>

                                        <span
                                            class="{{ $coverageColor }} min-w-12 text-right text-sm font-semibold tabular-nums"
                                        >
                                            {{ $entry->effective_coverage_pct }}%
                                        </span>
                                    </div>
                                </flux:table.cell>

                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    @endif
</flux:card>
