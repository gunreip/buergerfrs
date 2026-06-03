{{-- resources/views/components/admin/partials/translation-list/⚡meta.blade.php --}}

{{-- Meta / Active filters --}}
<flux:card class="mt-6">
    <x-ui.headers.details
        name="meta"
        :title="__('Current result')"
        :description="__('Overview of the active translation filters and the currently matching result set.')"
    >

        <div class="grid flex-1 gap-3 md:grid-cols-10">
            <flux:callout
                class="col-span-2"
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
                class="col-span-2"
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
                class="col-span-2"
                color="{{ $problemCount > 0 ? 'amber' : 'green' }}"
                icon="triangle-alert"
            >
                <flux:callout.heading>
                    {{ __('Problems') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $problemCount }}
                </flux:callout.text>

                <flux:callout.text class="font-extralight">
                    {{ __('Missing and dynamic translation entries requiring review.') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="green"
                icon="check-circle"
            >
                <flux:callout.heading>
                    {{ __('OK') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $statusCounts['ok'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="font-extralight">
                    {{ __('Translation keys currently marked as OK.') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="amber"
                icon="shield-alert"
            >
                <flux:callout.heading>
                    {{ __('Missing') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $statusCounts['missing'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="font-extralight">
                    {{ __('Translation keys with missing values or required follow-up.') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="purple"
                icon="package"
            >
                <flux:callout.heading>
                    {{ __('Vendor') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $classificationCounts['vendor'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="font-extralight">
                    {{ __('Translation keys imported from vendor/package sources.') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="sky"
                icon="history"
            >
                <flux:callout.heading>
                    {{ __('Backfill') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $classificationCounts['backfill_by_translation'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="font-extralight">
                    {{ __('Native texts reconstructed from existing EN translation values.') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="rose"
                icon="key-round"
            >
                <flux:callout.heading>
                    {{ __('Key') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $classificationCounts['key'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="font-extralight">
                    {{ __('Regular audit keys without special origin classification.') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="yellow"
                icon="file-text"
            >
                <flux:callout.heading>
                    {{ __('Type native') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $classificationCounts['native'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="font-extralight">
                    {{ __('Translation keys classified as native source entries.') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="teal"
                icon="braces"
            >
                <flux:callout.heading>
                    {{ __('Status dynamic') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $statusCounts['dynamic'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="font-extralight">
                    {{ __('Translation keys currently marked with dynamic status.') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
                color="purple"
                icon="square-dashed-text"
            >
                <flux:callout.heading>
                    {{ __('Status native') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $statusCounts['native'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="font-extralight">
                    {{ __('Translation keys currently marked with native status.') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2"
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
                class="col-span-2"
                color="amber"
                icon="archive"
            >
                <flux:callout.heading>
                    {{ __('Obsolete') }}
                </flux:callout.heading>

                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ $statusCounts['obsolete'] ?? 0 }}
                </flux:callout.text>

                <flux:callout.text class="font-extralight">
                    {{ __('Translation keys currently marked as obsolete.') }}
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-4"
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

                        @if ($workflowStatus !== 'open')
                            <flux:badge
                                color="emerald"
                                variant="subtle"
                            >
                                {{ __('Workflow') }}: {{ str($workflowStatus)->headline() }}
                            </flux:badge>
                        @endif

                        @if ($classification !== 'all')
                            @php
                                $classificationLabel = match ($classification) {
                                    'backfill_by_translation' => __('Backfill'),
                                    default => str($classification)->headline(),
                                };
                            @endphp

                            <flux:badge
                                color="purple"
                                variant="subtle"
                            >
                                {{ __('Type') }}: {{ $classificationLabel }}
                            </flux:badge>
                        @endif

                        @if ($onlyProblems)
                            <flux:badge
                                color="red"
                                variant="subtle"
                            >
                                {{ __('Only problems') }}
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
                                        {{ __('Language') }}: {{ $languageFilter }}
                                    </span>
                                </span>
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

        @if ($targetLanguages->isNotEmpty())
            <div class="mt-3 flex flex-wrap items-center gap-2 rounded-md bg-zinc-50/50 px-3 py-2 dark:bg-zinc-800/50">
                <span class="mr-1 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                    {{ __('Translated') }}
                </span>

                @foreach ($targetLanguages as $lang)
                    @php
                        $cov = $translationCoverage[$lang->locale] ?? null;
                        $translatedCount = (int) ($cov?->translated_count ?? 0);
                        $coveragePct = $total > 0 ? round($translatedCount / $total * 100) : 0;
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
    </x-ui.headers.details>
</flux:card>
