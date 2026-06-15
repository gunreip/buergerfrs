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
                            class="w-32"
                            align="end"
                        >
                            <x-ui.tooltip.trigger
                                class="mr-3"
                                :title="__('Total Keys')"
                                :text="__('Total translation keys used as reference for this row.')"
                            >
                                {{ __('Total Keys') }}
                            </x-ui.tooltip.trigger>
                        </flux:table.column>

                        <flux:table.column
                            class="w-32"
                            align="end"
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
                            class="w-32"
                            align="end"
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
                            class="w-32"
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

                        <flux:table.column class="w-56">
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
                                $coverageColor = $stat->coverage_pct >= 90
                                    ? 'text-green-600 dark:text-green-400'
                                    : ($stat->coverage_pct >= 60 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400');

                                $barColor = $stat->coverage_pct >= 90
                                    ? 'bg-green-500'
                                    : ($stat->coverage_pct >= 60 ? 'bg-amber-500' : 'bg-red-500');
                            @endphp

                            <flux:table.row wire:key="language-stat-{{ $stat->locale }}">
                                <flux:table.cell class="align-top">
                                    <span class="inline-flex items-center gap-2">
                                        <x-ui.locale.flag
                                            :locale="$stat->locale"
                                            size="lg"
                                        />

                                        <span>
                                            <span class="font-mono font-semibold uppercase text-zinc-800 dark:text-zinc-200">
                                                {{ $stat->locale }}
                                            </span>

                                            <span class="ml-1 text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $stat->native_name }}
                                            </span>
                                        </span>
                                    </span>
                                </flux:table.cell>

                                <flux:table.cell
                                    class="text-right tabular-nums text-zinc-700 dark:text-zinc-300"
                                    align="end"
                                >
                                    {{ number_format($stat->total_keys) }}
                                </flux:table.cell>

                                <flux:table.cell
                                    class="text-right tabular-nums text-green-700 dark:text-green-400"
                                    align="end"
                                >
                                    {{ number_format($stat->translated_count) }}
                                </flux:table.cell>

                                <flux:table.cell
                                    class="text-right tabular-nums {{ $stat->missing_count > 0 ? 'text-amber-700 dark:text-amber-400' : 'text-zinc-400' }}"
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

                                        <span class="{{ $coverageColor }} min-w-12 text-right text-sm font-semibold tabular-nums">
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
