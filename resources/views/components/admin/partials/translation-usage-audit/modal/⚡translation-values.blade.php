{{-- resources/views/components/admin/partials/translation-usage-audit/modal/⚡translation-values.blade.php --}}

{{-- Translation values --}}
<flux:callout
    icon="file-text"
    color="violet"
    stroke-width="1"
    x-data="{ showTranslationValues: false }"
>
    <div class="flex w-full items-center justify-between gap-3">
        <div class="min-w-0">
            <flux:callout.heading>
                {{ __('admin.translation_list.modal_edit.translation_values') }}
            </flux:callout.heading>
        </div>

        <div class="ml-auto flex shrink-0 items-center gap-3">
            <flux:badge
                variant="subtle"
                color="violet"
            >
                {{ $selectedMainLanguageValueGroups->count() }}
            </flux:badge>

            <x-ui.button.show-hide
                size="xs"
                state="showTranslationValues"
            />
        </div>
    </div>

    <div
        {{-- class="-mr-3 mt-3 max-h-72 space-y-2 overflow-y-auto pr-2" --}}
        x-show="showTranslationValues"
        x-collapse
    >
        <div
            class="-mr-3 mt-3 max-h-72 space-y-2 overflow-y-auto pr-2"
            {{-- x-show="showTranslationValues" --}}
            {{-- x-collapse --}}
        >
            @forelse ($selectedMainLanguageValueGroups as $valueGroup)
                @php
                    $sourceValue = $valueGroup['source_value'] ?? null;
                    $targetValues = collect($valueGroup['target_values'] ?? []);
                    $translationKeyId = $valueGroup['translation_key_id'] ?? null;
                @endphp

                <div
                    class="grid gap-2 rounded-lg border border-zinc-200 bg-white/60 p-3 text-sm lg:grid-cols-2 dark:border-zinc-700 dark:bg-zinc-950/20">
                    {{-- Source language --}}
                    <div
                        class="rounded-md border border-sky-200 bg-sky-50/50 p-3 dark:border-sky-800 dark:bg-sky-950/20">
                        @php
                            $sourceLocale = \App\Support\Locale\LocaleCode::normalize(
                                (string) ($sourceValue['locale'] ?? 'en'),
                            );
                            $sourceTranslationValue = trim((string) ($sourceValue['value'] ?? ''));
                            $sourceValueStatus = trim((string) ($sourceValue['status'] ?? ''));
                            $sourceValueSource = trim((string) ($sourceValue['source'] ?? ''));
                        @endphp

                        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <x-ui.locale.flag
                                    :locale="$sourceLocale"
                                    size="sm"
                                />

                                <span class="font-mono font-semibold uppercase">
                                    {{ $sourceLocale ?: '—' }}
                                </span>

                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                    color="{{ $sourceValueStatus === 'ok' ? 'emerald' : 'amber' }}"
                                >
                                    {{ $sourceValueStatus ?: '—' }}
                                </flux:badge>

                                @if ($sourceValueSource !== '')
                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                        color="zinc"
                                    >
                                        {{ $sourceValueSource }}
                                    </flux:badge>
                                @endif
                            </div>

                            <flux:badge
                                class="tabular-nums"
                                size="sm"
                                variant="subtle"
                                color="sky"
                            >
                                #{{ $translationKeyId ?? '—' }}
                            </flux:badge>
                        </div>

                        <div class="wrap-anywhere text-zinc-700 dark:text-zinc-300">
                            {{ $sourceTranslationValue !== '' ? $sourceTranslationValue : '—' }}
                        </div>
                    </div>

                    {{-- Target main languages --}}
                    <div class="space-y-2">
                        @forelse ($targetValues as $targetValue)
                            @php
                                $targetLocale = \App\Support\Locale\LocaleCode::normalize(
                                    (string) ($targetValue['locale'] ?? ''),
                                );
                                $targetTranslationValue = trim((string) ($targetValue['value'] ?? ''));
                                $targetValueStatus = trim((string) ($targetValue['status'] ?? ''));
                                $targetValueSource = trim((string) ($targetValue['source'] ?? ''));
                                $targetIsBaseDuplicate = (bool) ($targetValue['is_base_duplicate'] ?? false);

                                $targetSubLanguageValues = collect($valueGroup['sub_language_values'] ?? [])
                                    ->filter(static function (array $subLanguageValue) use ($targetLocale): bool {
                                        $subLanguageLocale = \App\Support\Locale\LocaleCode::normalize(
                                            (string) ($subLanguageValue['locale'] ?? ''),
                                        );

                                        return $targetLocale !== '' &&
                                            str_starts_with($subLanguageLocale, $targetLocale . '-');
                                    })
                                    ->values();
                            @endphp

                            <div
                                class="rounded-md border border-violet-200 bg-violet-50/50 p-3 dark:border-violet-800 dark:bg-violet-950/20">
                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <x-ui.locale.flag
                                            :locale="$targetLocale"
                                            size="sm"
                                        />

                                        <span class="font-mono font-semibold uppercase">
                                            {{ $targetLocale ?: '—' }}
                                        </span>

                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="{{ $targetValueStatus === 'ok' ? 'emerald' : 'amber' }}"
                                        >
                                            {{ $targetValueStatus ?: '—' }}
                                        </flux:badge>

                                        @if ($targetIsBaseDuplicate)
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="amber"
                                            >
                                                {{ __('Duplicate') }}
                                            </flux:badge>
                                        @endif

                                        @if ($targetValueSource !== '')
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="zinc"
                                            >
                                                {{ $targetValueSource }}
                                            </flux:badge>
                                        @endif
                                    </div>

                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                        color="violet"
                                    >
                                        #{{ $translationKeyId ?? '—' }}
                                    </flux:badge>
                                </div>

                                <div class="wrap-anywhere text-zinc-700 dark:text-zinc-300">
                                    {{ $targetTranslationValue !== '' ? $targetTranslationValue : '—' }}
                                </div>

                                @if ($targetSubLanguageValues->isNotEmpty())
                                    <div class="mt-3 border-t border-violet-200 pt-2 dark:border-violet-800">
                                        <div
                                            class="mb-1 flex items-center justify-between gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                                            <span class="font-semibold">
                                                {{ __('admin.translation_list.modal_edit.language_variations') }}
                                            </span>

                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="violet"
                                            >
                                                {{ $targetSubLanguageValues->count() }}
                                            </flux:badge>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-1.5">
                                            @foreach ($targetSubLanguageValues as $subLanguageValue)
                                                @php
                                                    $subLanguageLocale = \App\Support\Locale\LocaleCode::normalize(
                                                        (string) ($subLanguageValue['locale'] ?? ''),
                                                    );

                                                    $subLanguageTranslationValue = trim(
                                                        (string) ($subLanguageValue['value'] ?? ''),
                                                    );
                                                    $subLanguageStatus = trim(
                                                        (string) ($subLanguageValue['status'] ?? ''),
                                                    );
                                                    $subLanguageSource = trim(
                                                        (string) ($subLanguageValue['source'] ?? ''),
                                                    );

                                                    $subLanguageTooltipText =
                                                        $subLanguageTranslationValue !== ''
                                                            ? $subLanguageTranslationValue
                                                            : trim(
                                                                implode(
                                                                    ' · ',
                                                                    array_filter([
                                                                        __('No value available.'),
                                                                        $subLanguageStatus !== ''
                                                                            ? __(
                                                                                    'admin.app_settings.table_icon_registry.status',
                                                                                ) .
                                                                                ': ' .
                                                                                $subLanguageStatus
                                                                            : null,
                                                                        $subLanguageSource !== ''
                                                                            ? __(
                                                                                    'admin.translation_list.modal.source',
                                                                                ) .
                                                                                ': ' .
                                                                                $subLanguageSource
                                                                            : null,
                                                                    ]),
                                                                ),
                                                            );
                                                @endphp

                                                <x-ui.tooltip.trigger
                                                    :title="strtoupper($subLanguageLocale)"
                                                    :text="$subLanguageTooltipText"
                                                >
                                                    <flux:badge
                                                        size="sm"
                                                        variant="subtle"
                                                        color="{{ $subLanguageTranslationValue !== '' ? 'emerald' : 'zinc' }}"
                                                    >
                                                        <x-ui.locale.flag
                                                            :locale="$subLanguageLocale"
                                                            size="sm"
                                                        />

                                                        <span class="ml-1 font-mono uppercase">
                                                            {{ $subLanguageLocale }}
                                                        </span>
                                                    </flux:badge>
                                                </x-ui.tooltip.trigger>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div
                                class="rounded-md border border-violet-200 bg-violet-50/50 p-3 text-sm text-zinc-500 dark:border-violet-800 dark:bg-violet-950/20 dark:text-zinc-400">
                                {{ __('No target main language value available.') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('admin.translation_list.modal.no_translation_values_available') }}
                </div>
            @endforelse
        </div>
    </div>
</flux:callout>
