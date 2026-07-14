{{-- resources/views/components/admin/partials/translation-usage-audit/⚡meta.blade.php --}}

@php
    $activeSummary = $activeTab === 'duplicate' ? $duplicateSummary : $frequentSummary;

    $summaryLabels =
        $activeTab === 'duplicate'
            ? [
                'candidate_literals' => __('Candidate literals'),
                'candidate_translation_keys' => __('Translation keys'),
                'candidate_usages' => __('Usages total'),
                'candidate_current_usages' => __('Current usages'),
                'candidate_stale_usages' => __('Stale usages'),
            ]
            : [
                'reported_literals' => __('Reported literals'),
                'reported_translation_keys' => __('Translation keys'),
                'reported_usages' => __('Usages total'),
                'reported_current_usages' => __('Current usages'),
                'reported_stale_usages' => __('Stale usages'),
            ];

    $sourceLocale = data_get($activeSummary, 'options.source_locale', '—');
    $generatedAt = data_get($activeSummary, 'generated_at', '—');
@endphp

<flux:card
    class="mt-6"
    x-data="{ showMeta: true }"
>
    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">
            <x-ui.headers.card
                name="meta"
                :title="__('Meta')"
                :description="__('Last generated audit metadata for the selected usage report.')"
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
        class="md:grid-cols-15 grid flex-1 gap-3"
        x-show="showMeta"
        x-collapse
    >
        <flux:callout
            class="col-span-5 hyphens-auto"
            color="emerald"
            icon="scan-search"
            heading="{{ __('Audit') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $activeTab === 'duplicate' ? 'duplicate' : 'frequent' }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-5 hyphens-auto"
            color="purple"
            icon="globe"
            heading="{{ __('Source locale') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $sourceLocale }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-5 hyphens-auto"
            color="sky"
            icon="calendar"
            heading="{{ __('Generated at') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                <div>
                    <span class="mr-2">
                        <x-ui.date-time.date
                            class="text-2xl! font-semibold tabular-nums"
                            :value="$generatedAt"
                            color="callout-text-sky"
                        />
                    </span>
                    <span>
                        <x-ui.date-time.time
                            class="text-2xl! font-semibold tabular-nums"
                            :value="$generatedAt"
                            color="callout-text-sky"
                        />
                    </span>
                </div>
            </flux:callout.text>
        </flux:callout>

        @foreach ($summaryLabels as $summaryKey => $summaryLabel)
            <flux:callout
                class="col-span-3 hyphens-auto"
                color="amber"
                icon="chart-bar"
                stroke-width="1"
                :heading="$summaryLabel"
            >
                <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                    {{ number_format((int) data_get($activeSummary, $summaryKey, 0)) }}
                </flux:callout.text>
            </flux:callout>
        @endforeach
    </div>
</flux:card>
