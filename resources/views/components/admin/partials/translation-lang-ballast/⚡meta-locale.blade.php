{{-- Audit locales badges list --}}
@php
    $activeLocaleSummary = $activeLocaleSummary ?? [
        'total' => 0,
        'with_translations' => 0,
        'without_translations' => 0,
        'without_translation_locales' => [],
    ];
@endphp

@if ($localeRows->isNotEmpty())
    <flux:callout
        class="mt-3"
        color="zinc"
        icon="languages"
        stroke-width="1"
        x-data="{
            auditLocaleSearch: '',
            matchesAuditLocale(locale) {
                const search = this.auditLocaleSearch.trim().toLowerCase();

                if (search === '') {
                    return true;
                }

                return String(locale).toLowerCase().includes(search);
            },
            filteredAuditLocaleCount() {
                const items = this.$refs.auditLocaleItems?.querySelectorAll('[data-audit-locale]') ?? [];

                return Array.from(items)
                    .filter((item) => this.matchesAuditLocale(item.dataset.auditLocale ?? ''))
                    .length;
            },
        }"
    >
        <flux:callout.heading>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <span class="inline-flex items-center gap-2">
                    <span>
                        {{ __('Audit locales') }}
                    </span>

                    <flux:badge
                        color="zinc"
                        variant="subtle"
                        size="sm"
                    >
                        <span x-text="filteredAuditLocaleCount()"></span>
                        <span class="opacity-60">
                            / {{ number_format($localeRows->count()) }}
                        </span>
                    </flux:badge>

                    @if ((int) data_get($activeLocaleSummary, 'total', 0) > 0)
                        <flux:badge
                            color="emerald"
                            variant="subtle"
                            size="sm"
                        >
                            {{ __('Active languages with translations') }}
                            {{ number_format((int) data_get($activeLocaleSummary, 'with_translations', 0)) }}
                        </flux:badge>

                        <flux:badge
                            color="{{ (int) data_get($activeLocaleSummary, 'without_translations', 0) > 0 ? 'amber' : 'zinc' }}"
                            variant="subtle"
                            size="sm"
                        >
                            {{ __('Active languages without translations') }}
                            {{ number_format((int) data_get($activeLocaleSummary, 'without_translations', 0)) }}
                        </flux:badge>
                    @endif
                </span>

                <div class="w-56 max-w-full">
                    <flux:input.group>
                        <flux:input.group.prefix>
                            <flux:icon.magnifying-glass stroke-width="1" />
                        </flux:input.group.prefix>

                        <flux:input
                            type="search"
                            size="sm"
                            x-model.debounce.150ms="auditLocaleSearch"
                            placeholder="{{ __('Filter locales') }}"
                        />
                    </flux:input.group>
                </div>
            </div>
        </flux:callout.heading>

        <flux:callout.text>
            <div class="scrollbar-gutter-auto mt-2 max-h-32 overflow-y-auto pr-1">
                <div
                    class="flex flex-wrap items-center gap-2"
                    x-ref="auditLocaleItems"
                >
                    @foreach ($localeRows as $localeRow)
                        @php
                            $localeNetFileSurplus = (int) $localeRow['net_file_surplus_entries'];
                            $localeNetColor =
                                $localeNetFileSurplus > 0
                                    ? 'text-red-300'
                                    : ($localeNetFileSurplus < 0
                                        ? 'text-amber-300'
                                        : 'text-green-300');
                        @endphp

                        <span
                            data-audit-locale="{{ $localeRow['locale'] }}"
                            x-show="matchesAuditLocale($el.dataset.auditLocale)"
                        >
                            <x-ui.tooltip.trigger :title="strtoupper($localeRow['locale'])">
                                <x-slot:tooltip>
                                    <div class="min-w-72 space-y-3 text-sm">
                                        <div
                                            class="flex items-center justify-between gap-3 border-b border-zinc-700/70 pb-2">
                                            <span class="inline-flex items-center gap-2 font-semibold">
                                                <x-ui.locale.flag
                                                    :locale="$localeRow['locale']"
                                                    size="sm"
                                                />

                                                <span class="font-mono uppercase">
                                                    {{ $localeRow['locale'] }}
                                                </span>
                                            </span>

                                            <flux:badge
                                                color="{{ $localeRow['color'] }}"
                                                variant="subtle"
                                                size="sm"
                                            >
                                                {{ __('Audit locale') }}
                                            </flux:badge>
                                        </div>

                                        <dl class="grid grid-cols-[minmax(0,1fr)_auto] gap-x-5 gap-y-1.5 tabular-nums">
                                            <dt class="text-zinc-400">
                                                {{ __('Lang entries') }}
                                            </dt>
                                            <dd class="text-right font-semibold text-zinc-100">
                                                {{ number_format((int) $localeRow['lang_entries']) }}
                                            </dd>

                                            <dt class="text-zinc-400">
                                                {{ __('DB exportable') }}
                                            </dt>
                                            <dd class="text-right font-semibold text-zinc-100">
                                                {{ number_format((int) $localeRow['db_exportable_entries']) }}
                                            </dd>

                                            <dt class="text-zinc-400">
                                                {{ __('Matched') }}
                                            </dt>
                                            <dd class="text-right font-semibold text-green-300">
                                                {{ number_format((int) $localeRow['matched_entries']) }}
                                            </dd>

                                            <dt class="text-zinc-400">
                                                {{ __('File-only cleanup') }}
                                            </dt>
                                            <dd
                                                class="{{ (int) $localeRow['file_only_entries'] > 0 ? 'text-red-300' : 'text-zinc-300' }} text-right font-semibold">
                                                {{ number_format((int) $localeRow['file_only_entries']) }}
                                            </dd>

                                            <dt class="text-zinc-400">
                                                {{ __('DB-only missing') }}
                                            </dt>
                                            <dd
                                                class="{{ (int) $localeRow['db_only_entries'] > 0 ? 'text-amber-300' : 'text-zinc-300' }} text-right font-semibold">
                                                {{ number_format((int) $localeRow['db_only_entries']) }}
                                            </dd>
                                        </dl>

                                        <div
                                            class="flex items-center justify-between gap-3 border-t border-zinc-700/70 pt-2">
                                            <span class="text-zinc-400">
                                                {{ __('Net file surplus') }}
                                            </span>

                                            <span class="{{ $localeNetColor }} font-semibold tabular-nums">
                                                {{ number_format($localeNetFileSurplus) }}
                                            </span>
                                        </div>
                                    </div>
                                </x-slot:tooltip>

                                <flux:badge
                                    color="{{ $localeRow['color'] }}"
                                    variant="subtle"
                                    size="sm"
                                >
                                    <span class="inline-flex items-center gap-1">
                                        <x-ui.locale.flag
                                            :locale="$localeRow['locale']"
                                            size="xs"
                                        />

                                        <span class="font-mono font-semibold uppercase">
                                            {{ $localeRow['locale'] }}
                                        </span>

                                        <span class="tabular-nums opacity-70">
                                            {{ number_format((int) $localeRow['lang_entries']) }}
                                        </span>

                                        @if ((int) $localeRow['file_only_entries'] > 0)
                                            <span class="tabular-nums opacity-70">
                                                −{{ number_format((int) $localeRow['file_only_entries']) }}
                                            </span>
                                        @endif

                                        @if ((int) $localeRow['db_only_entries'] > 0)
                                            <span class="tabular-nums opacity-70">
                                                +{{ number_format((int) $localeRow['db_only_entries']) }}
                                            </span>
                                        @endif
                                    </span>
                                </flux:badge>
                            </x-ui.tooltip.trigger>
                        </span>
                    @endforeach
                </div>
            </div>
        </flux:callout.text>
    </flux:callout>
@endif
