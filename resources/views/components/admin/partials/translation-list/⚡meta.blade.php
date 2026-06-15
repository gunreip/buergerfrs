{{-- resources/views/components/admin/partials/translation-list/⚡meta.blade.php --}}

{{-- Meta / Active filters --}}
<flux:card
    class="mt-6"
    x-data="{ showMeta: true }"
>

    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">
            <x-ui.headers.card
                name="meta"
                :title="__('admin.translation_list.meta.current_result')"
                :description="__(
                    'admin.translation_list.meta.overview_of_the_active_translation_filters_and_the_currently_matching_result_set',
                )"
            />
        </div>
        <div class="ml-auto flex shrink-0 items-center gap-3">
            <x-ui.button.show-hide
                size="xs"
                state="showMeta"
            />
        </div>
    </div>

    <div
        x-show="showMeta"
        x-collapse
    >
        <div class="grid flex-1 gap-3 md:grid-cols-10">
            <flux:callout
                class="col-span-2"
                color="sky"
                icon="list-filter"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.meta.matching_keys') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $filteredTotal }}
                </flux:callout.text>

                <flux:callout.text class="hyphens-auto font-extralight">
                    {{ __('admin.translation_list.meta.translation_keys_matching_the_current_filters') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="orange"
                icon="database"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.meta.total_keys') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $total }}
                </flux:callout.text>

                <flux:callout.text class="hyphens-auto font-extralight">
                    {{ __('admin.translation_list.meta.translation_keys_currently_known_in_the_audit_table') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="{{ $problemCount > 0 ? 'amber' : 'green' }}"
                icon="triangle-alert"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.meta.problems') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $problemCount }}
                </flux:callout.text>

                <flux:callout.text class="hyphens-auto font-extralight">
                    {{ __('admin.translation_list.meta.missing_and_dynamic_translation_entries_requiring_review') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="green"
                icon="check-circle"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.meta.ok') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $statusCounts['ok'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="hyphens-auto font-extralight">
                    {{ __('admin.translation_list.meta.translation_keys_currently_marked_as_ok') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="amber"
                icon="shield-alert"
            >
                <flux:callout.heading>
                    {{ __('admin.app_settings.table_icon_registry.missing') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $statusCounts['missing'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="hyphens-auto font-extralight">
                    {{ __('admin.translation_list.meta.translation_keys_with_missing_values_or_required_follow_up') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="purple"
                icon="package"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.meta.vendor') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $classificationCounts['vendor'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="hyphens-auto font-extralight">
                    {{ __('admin.translation_list.meta.translation_keys_imported_from_vendor_package_sources') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="sky"
                icon="history"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.meta.backfill') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $classificationCounts['backfill_by_translation'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="hyphens-auto font-extralight">
                    {{ __('admin.translation_list.meta.native_texts_reconstructed_from_existing_en_translation_values') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="rose"
                icon="key-round"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.table.key') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $classificationCounts['key'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="hyphens-auto font-extralight">
                    {{ __('admin.translation_list.meta.regular_audit_keys_without_special_origin_classification') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="yellow"
                icon="file-text"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.meta.type_native') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $classificationCounts['native'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="hyphens-auto font-extralight">
                    {{ __('admin.translation_list.meta.translation_keys_classified_as_native_source_entries') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="teal"
                icon="braces"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.meta.status_dynamic') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $statusCounts['dynamic'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="hyphens-auto font-extralight">
                    {{ __('admin.translation_list.meta.translation_keys_currently_marked_with_dynamic_status') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="purple"
                icon="square-dashed-text"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.meta.status_native') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $statusCounts['native'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="hyphens-auto font-extralight">
                    {{ __('admin.translation_list.meta.translation_keys_currently_marked_with_native_status') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="{{ $hasActiveFilters ? 'amber' : 'green' }}"
                icon="{{ $hasActiveFilters ? 'funnel' : 'check-circle' }}"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.meta.filter_state') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold">
                    {{ $hasActiveFilters ? __('admin.translation_list.meta.filtered') : __('admin.translation_list.meta.unfiltered') }}
                </flux:callout.text>

                <flux:callout.text class="hyphens-auto font-extralight">
                    {{ $hasActiveFilters ? __('admin.translation_list.meta.one_or_more_filters_are_currently_active') : __('admin.translation_list.meta.no_filters_are_currently_active') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="amber"
                icon="archive"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.meta.obsolete') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $statusCounts['obsolete'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="hyphens-auto font-extralight">
                    {{ __('admin.translation_list.meta.translation_keys_currently_marked_as_obsolete') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-4"
                color="purple"
                icon="sliders-horizontal"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.meta.active_filters') }}
                </flux:callout.heading>

                <flux:callout.text>
                    <div class="flex flex-wrap gap-2">
                        @if (trim($search) !== '')
                            <flux:badge
                                color="blue"
                                variant="subtle"
                            >
                                {{ __('ui.actions.search') }}: {{ $search }}
                            </flux:badge>
                        @endif

                        @if ($status !== 'all')
                            <flux:badge
                                color="amber"
                                variant="subtle"
                            >
                                {{ __('admin.app_settings.table_icon_registry.status') }}:
                                {{ str($status)->headline() }}
                            </flux:badge>
                        @endif

                        @if ($workflowStatus !== 'open')
                            <flux:badge
                                color="emerald"
                                variant="subtle"
                            >
                                {{ __('admin.translation_list.meta.workflow') }}:
                                {{ str($workflowStatus)->headline() }}
                            </flux:badge>
                        @endif

                        @if ($classification !== 'all')
                            @php
                                $classificationLabel = match ($classification) {
                                    'backfill_by_translation' => __('admin.translation_list.meta.backfill'),
                                    default => str($classification)->headline(),
                                };
                            @endphp

                            <flux:badge
                                color="purple"
                                variant="subtle"
                            >
                                {{ __('admin.client_list.table.type') }}: {{ $classificationLabel }}
                            </flux:badge>
                        @endif

                        @if ($onlyProblems)
                            <flux:badge
                                color="red"
                                variant="subtle"
                            >
                                {{ __('admin.translation_list.meta.only_problems') }}
                            </flux:badge>
                        @endif

                        @if ($languageFilter !== '')
                            <flux:badge
                                color="purple"
                                variant="subtle"
                            >
                                <span class="inline-flex items-center gap-1.5">
                                    <x-ui.locale.flag
                                        :locale="$languageFilter"
                                        size="xs"
                                    />

                                    <span>
                                        {{ __('admin.translation_list.meta.language') }}: {{ $languageFilter }}
                                    </span>
                                </span>
                            </flux:badge>
                        @endif

                        @if ($namespaceFilter !== '')
                            <flux:badge
                                color="sky"
                                variant="subtle"
                            >
                                {{ __('admin.translation_list.modal.namespace') }}: {{ $namespaceFilter }}
                            </flux:badge>
                        @endif

                        @if ($groupFilter !== '')
                            <flux:badge
                                color="sky"
                                variant="subtle"
                            >
                                {{ __('admin.translation_list.modal.group') }}: {{ $groupFilter }}
                            </flux:badge>
                        @endif

                        @if ($perPage !== 25)
                            <flux:badge
                                color="zinc"
                                variant="subtle"
                            >
                                {{ __('ui.table.per_page_selector.per_page') }}: {{ $perPage }}
                            </flux:badge>
                        @endif

                        @unless ($hasActiveFilters)
                            <flux:badge
                                color="green"
                                variant="subtle"
                            >
                                {{ __('admin.translation_list.meta.no_active_filters') }}
                            </flux:badge>
                        @endunless
                    </div>
                </flux:callout.text>
            </flux:callout>
        </div>
    </div>

    {{-- Target languages badges list --}}
    @if ($targetLanguages->isNotEmpty())
        <div class="mt-3 flex flex-wrap items-center gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">
            <span class="mr-1 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                {{ __('admin.translation_list.meta.translated') }}
            </span>

            @foreach ($targetLanguages as $lang)
                @php
                    $cov = $translationCoverage[$lang->locale] ?? null;
                    $translatedCount = (int) ($cov?->translated_count ?? 0);
                    $coveragePct = $total > 0 ? round(($translatedCount / $total) * 100) : 0;
                    $badgeColor = $coveragePct >= 90 ? 'green' : ($coveragePct >= 60 ? 'amber' : 'red');
                @endphp

                <flux:badge
                    color="{{ $badgeColor }}"
                    variant="subtle"
                    size="sm"
                >
                    <span class="inline-flex items-center gap-1">
                        <x-ui.locale.flag
                            :locale="$lang->locale"
                            size="xs"
                        />

                        <span class="font-mono font-semibold uppercase">{{ $lang->locale }}</span>
                        <span class="opacity-70">{{ $translatedCount }}</span>
                    </span>
                </flux:badge>
            @endforeach
        </div>
    @endif

</flux:card>
