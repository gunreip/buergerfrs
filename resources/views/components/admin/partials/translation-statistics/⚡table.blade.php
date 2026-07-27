{{-- resources/views/components/admin/partials/translation-statistics/⚡table.blade.php --}}

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('Language Coverage')"
        :description="__('Translation completeness and review progress per target language.')"
    />

    @if ($languageStats->isEmpty())
        <flux:callout
            class="mt-4"
            color="amber"
            icon="triangle-alert"
        >
            <flux:callout.heading>
                {{ __('No target languages configured') }}
            </flux:callout.heading>

            <flux:callout.text>
                {{ __('Configure available locales in App Settings to see language coverage.') }}
            </flux:callout.text>
        </flux:callout>
    @else
        <div
            class="mx-auto max-w-full scroll-mt-6"
            id="translation-statistics-table"
        >
            <div class="overflow-hidden rounded-t-lg">
                <flux:table class="app-table">
                    <flux:table.columns class="bg-zinc-800 text-zinc-400">
                        <flux:table.column>
                            <x-ui.tooltip.trigger
                                class="ml-3"
                                :title="__('admin.translation_list.meta.language')"
                                :text="__('Locale variant covered by the statistics row.')"
                            >
                                {{ __('admin.translation_list.meta.language') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            {{-- class="w-32" --}}
                            align="center"
                        >
                            <x-ui.tooltip.trigger
                                class="ml-3"
                                :title="__('admin.client_list.table.type')"
                                :text="__(
                                    'Shows whether this locale is a main language or a sub-language variant.',
                                )"
                            >
                                {{ __('admin.client_list.table.type') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            class="w-36"
                            align="center"
                        >
                            <x-ui.tooltip.trigger
                                class="ml-3"
                                :title="__('Sub-Languages')"
                                :text="__(
                                    'Shows activated sub-languages compared to possible sub-languages for this main language.',
                                )"
                            >
                                {{ __('Sub-Languages') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            class="w-44"
                            align="center"
                        >
                            <x-ui.tooltip.trigger
                                class="ml-3"
                                :title="__('ui.active-state')"
                                :text="__(
                                    'Shows whether this language is active, current, or the source language.',
                                )"
                            >
                                {{ __('ui.active-state') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            {{-- class="w-32" --}}
                            align="center"
                        >
                            <x-ui.tooltip.trigger
                                class="mr-3"
                                :title="__('Reference')"
                                :text="__(
                                    'Reference entries from the translation key audit table used as denominator for this row.',
                                )"
                            >
                                {{ __('Reference') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            {{-- class="w-32" --}}
                            align="center"
                        >
                            <x-ui.tooltip.trigger
                                class="mr-3"
                                :title="__('admin.translation_list.modal_edit.translation_values')"
                                :text="__('Existing translation_values rows for this locale.')"
                            >
                                {{ __('admin.translation_list.modal.values') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            {{-- class="w-32" --}}
                            align="center"
                        >
                            <x-ui.tooltip.trigger
                                class="mr-3"
                                :title="__('admin.translation_list.meta.translated')"
                                :text="__('Number of keys translated in the effective language combination.')"
                            >
                                {{ __('admin.translation_list.meta.translated') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            {{-- class="w-32" --}}
                            align="center"
                        >
                            <x-ui.tooltip.trigger
                                class="mr-3"
                                :title="__('admin.app_settings.table_icon_registry.missing')"
                                :text="__('Keys that are not translated yet in this language combination.')"
                            >
                                {{ __('admin.app_settings.table_icon_registry.missing') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            {{-- class="w-32" --}}
                            align="end"
                        >
                            <x-ui.tooltip.trigger
                                class="mr-3"
                                :title="__('admin.translation_list.modal_edit.reviewed')"
                                :text="__('Number of translated keys already marked as reviewed.')"
                            >
                                {{ __('admin.translation_list.modal_edit.reviewed') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            class="w-56"
                            align="center"
                        >
                            <x-ui.tooltip.trigger
                                class="ml-3"
                                :title="__('Coverage')"
                                :text="__('Coverage percentage based on the translated key count.')"
                            >
                                {{ __('Coverage') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($languageStats as $stat)
                            @php
                                $coverageColor =
                                    $stat->coverage_pct >= 90
                                        ? 'text-green-600 dark:text-green-400'
                                        : ($stat->coverage_pct >= 60
                                            ? 'text-amber-600 dark:text-amber-400'
                                            : 'text-red-600 dark:text-red-400');

                                $barColor =
                                    $stat->coverage_pct >= 90
                                        ? 'bg-green-500'
                                        : ($stat->coverage_pct >= 60
                                            ? 'bg-amber-500'
                                            : 'bg-red-500');
                            @endphp

                            <flux:table.row wire:key="language-stat-{{ $stat->locale }}">
                                <flux:table.cell class="align-top">
                                    <span class="ml-3 inline-flex items-center gap-2">
                                        <x-ui.locale.flag
                                            :locale="$stat->locale"
                                            size="lg"
                                        />

                                        <span>
                                            <span
                                                class="font-mono font-semibold uppercase text-zinc-800 dark:text-zinc-200"
                                            >
                                                {{ $stat->locale }}
                                            </span>

                                            <span class="ml-1 font-mono text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $stat->native_name }}
                                            </span>
                                        </span>
                                    </span>
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    <div class="ml-3 flex flex-wrap items-center gap-1">
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="{{ $stat->is_sub_language ? 'purple' : 'blue' }}"
                                        >
                                            {{ $stat->is_sub_language ? __('Sub') : __('Main') }}
                                        </flux:badge>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell
                                    class="align-top"
                                    align="center"
                                >
                                    <div class="flex justify-center">
                                        <x-ui.tooltip.trigger
                                            :title="__('Sub-Languages')"
                                            :text="__(
                                                'Activated sub-languages selected in Sub-Language administration / possible sub-languages for this main language.',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="{{ (int) $stat->sub_language_active_count > 0 ? 'purple' : 'zinc' }}"
                                            >
                                                <span class="tabular-nums">
                                                    {{ (int) $stat->sub_language_active_count }}/{{ (int) $stat->sub_language_possible_count }}
                                                </span>
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    <div class="ml-3 flex flex-wrap items-center gap-1">
                                        {{-- Active Badge --}}
                                        @if ($stat->is_active)
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="lime"
                                            >
                                                {{ __('ui.active') }}
                                            </flux:badge>
                                        @endif
                                        {{-- Source Badge --}}
                                        @if ($stat->is_source)
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="sky"
                                            >
                                                {{ __('admin.translation_list.modal.source') }}
                                            </flux:badge>
                                        @endif
                                        {{-- Current Badge --}}
                                        @if ($stat->is_current)
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="emerald"
                                            >
                                                {{ __('admin.app_settings.locale.current') }}
                                            </flux:badge>
                                        @endif
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell
                                    class="text-right tabular-nums text-zinc-700 dark:text-zinc-300"
                                    align="end"
                                >
                                    {{ number_format($stat->total_keys) }}
                                </flux:table.cell>

                                <flux:table.cell
                                    class="text-right tabular-nums text-zinc-700 dark:text-zinc-300"
                                    align="end"
                                >
                                    {{ number_format($stat->row_count) }}
                                </flux:table.cell>

                                <flux:table.cell
                                    class="text-right tabular-nums text-green-700 dark:text-green-400"
                                    align="end"
                                >
                                    {{ number_format($stat->translated_count) }}
                                </flux:table.cell>

                                <flux:table.cell
                                    class="{{ $stat->missing_count > 0 ? 'text-amber-700 dark:text-amber-400' : 'text-zinc-400' }} text-right tabular-nums"
                                    align="end"
                                >
                                    {{ number_format($stat->missing_count) }}
                                </flux:table.cell>

                                <flux:table.cell
                                    class="text-right tabular-nums text-zinc-500 dark:text-zinc-400"
                                    align="end"
                                >
                                    {{ number_format($stat->reviewed_count) }}

                                    @if ($stat->translated_count > 0)
                                        <span class="ml-1 text-xs opacity-60">{{ $stat->reviewed_pct }}%</span>
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell class="align-top">
                                    <div class="flex items-center gap-3">
                                        <div class="h-2 w-32 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                                            <div
                                                class="{{ $barColor }} h-full rounded-full transition-all"
                                                style="width: {{ $stat->coverage_pct }}%"
                                            ></div>
                                        </div>

                                        <span
                                            class="{{ $coverageColor }} min-w-12 text-right text-sm font-semibold tabular-nums"
                                        >
                                            {{ $stat->coverage_pct }}%
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
