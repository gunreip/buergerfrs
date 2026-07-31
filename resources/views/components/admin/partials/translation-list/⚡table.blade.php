{{-- resources/views/components/admin/partials/translation-list/⚡table.blade.php --}}

{{-- Table part --}}
<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('admin.translation_list.table.translation_list')"
        :description="__(
            'admin.translation_list.table.review_and_manage_translation_keys_their_values_across_languages_and_associated_',
        )"
    >
        @php
            $appLanguages = collect($targetLanguages ?? [])
                ->sortBy(static function ($language): string {
                    $locale = \App\Support\Locale\LocaleCode::normalize((string) ($language->locale ?? ''));

                    return $locale !== ''
                        ? $locale
                        : mb_strtolower((string) ($language->native_name ?: $language->name ?: ''));
                }, SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            $activeTargetSubLanguages = collect($activeTargetSubLanguages ?? []);
        @endphp

        <div class="flex flex-wrap items-center justify-end gap-2">
            @if ($appLanguages->isNotEmpty())
                <div class="flex flex-wrap items-center justify-end gap-1.5">
                    @foreach ($appLanguages as $translationLanguage)
                        <x-ui.tooltip.trigger
                            :title="__('admin.translation_list.table.language_translations', [
                                'language' =>
                                    $translationLanguage->native_name ?:
                                    $translationLanguage->name ?:
                                    $translationLanguage->locale,
                            ])"
                            :text="__(
                                'admin.translation_list.table.review_translation_values_for_the_language_language_including_its_sub_languages_',
                                [
                                    'language' =>
                                        $translationLanguage->native_name ?:
                                        $translationLanguage->name ?:
                                        $translationLanguage->locale,
                                ],
                            )"
                        >
                            <flux:badge
                                label="{{ $translationLanguage->native_name ?: $translationLanguage->name ?: $translationLanguage->locale }}"
                            >
                                <x-ui.locale.flag
                                    :locale="$translationLanguage->locale"
                                    size="lg"
                                />

                                <span class="ml-2 font-mono uppercase">
                                    {{ $translationLanguage->locale }}
                                </span>
                            </flux:badge>
                        </x-ui.tooltip.trigger>
                    @endforeach
                </div>
            @endif

            <x-ui.tooltip.trigger
                :title="__('admin.translation_list.table.reload_translation_list')"
                :text="__('admin.translation_list.table.refresh_the_translation_list_to_see_the_latest_changes')"
            >

                <x-ui.button.reset
                    icon="arrow-path"
                    :label="__('admin.translation_list.table.reload_translation_list')"
                    :aria-label="__('admin.translation_list.table.reload_translation_list')"
                    wire:click="$refresh"
                    wire:loading.attr="disabled"
                    wire:target="$refresh"
                />

            </x-ui.tooltip.trigger>
        </div>
    </x-ui.headers.card>

    @if ($translationKeys->hasPages())
        {{-- Pagination --}}
        <div class="mb-2 mt-2">
            <x-ui.table.pagination
                class="m-0! p-0!"
                id="translation-list-pagination-top"
                :paginator="$translationKeys"
                scroll-to="#translation-list-pagination-top"
            />
        </div>
    @endif

    <div
        class="mx-auto max-w-full scroll-mt-6"
        id="translation-list-table"
    >
        <div class="overflow-hidden rounded-t-lg">

            {{-- Table --}}
            {{-- ID, Status, Key/Suggested Key, Native Text, Values, Usage, Actions --}}
            <flux:table
                container:class="max-h-280 app-table scrollbar-gutter-auto border-b-1 border-zinc-200 dark:border-zinc-700 mb-3 pb-2"
            >

                {{-- Table Headers with tooltips for additional context on each column --}}
                <flux:table.columns
                    class="bg-zinc-800 text-zinc-400"
                    sticky
                >

                    {{-- Sequence Number (not sortable, reflects current order in the list, useful for reference and identification) --}}
                    <flux:table.column
                        class="w-14 tabular-nums"
                        align="center"
                    >
                        <flux:icon.tally-5
                            class="ml-3"
                            stroke-width="1"
                        />

                    </flux:table.column>

                    {{-- Column ID --}}
                    <flux:table.column
                        class="w-32 tabular-nums"
                        sortable
                        :sorted="$sortField === 'id'"
                        :direction="$sortDirection"
                        align="center"
                        wire:click="sortBy('id')"
                    >
                        <x-ui.tooltip.trigger
                            class="ml-3"
                            :title="__('admin.user_list.table.id')"
                            :text="__('admin.translation_list.table.internal_database_id_of_the_translation_key')"
                        >
                            {{ __('admin.user_list.table.id') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Status --}}
                    <flux:table.column
                        class="w-24"
                        sortable
                        :sorted="$sortField === 'status'"
                        :direction="$sortDirection"
                        align="center"
                        wire:click="sortBy('status')"
                    >
                        <x-ui.tooltip.trigger
                            class="ml-3"
                            :title="__('ui.status')"
                            :text="__(
                                'admin.translation_list.table.current_status_of_the_translation_key_useful_for_identification_and_reference',
                            )"
                        >
                            {{ __('ui.status') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Key / Suggested Key --}}
                    <flux:table.column
                        class="w-(--translation-balanced-column-width)"
                        sortable
                        :sorted="$sortField === 'key'"
                        :direction="$sortDirection"
                        wire:click="sortBy('key')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.translation_list.table.key_suggested_key')"
                            :text="__(
                                'admin.translation_list.table.translation_key_or_suggested_key_useful_for_identification_and_reference',
                            )"
                        >
                            {{ __('admin.translation_list.table.key_suggested_key') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Native Text --}}
                    <flux:table.column
                        class="w-(--translation-balanced-column-width)"
                        sortable
                        :sorted="$sortField === 'native_text'"
                        :direction="$sortDirection"
                        wire:click="sortBy('native_text')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.translation_list.table.source_language_value_en_native_text')"
                            :text="__(
                                'admin.translation_list.table.original_text_in_the_source_language_useful_for_identification_and_reference',
                            )"
                        >
                            <div class="flex flex-wrap items-center gap-2">
                                {{ __('admin.translation_list.table.source_language_value_en_native_text') }}
                            </div>
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Values --}}
                    <flux:table.column class="w-(--translation-balanced-column-width)">
                        <x-ui.tooltip.trigger
                            :title="__('admin.translation_list.table.target_sub_language_values')"
                            :text="__(
                                'admin.translation_list.table.translated_values_for_the_key_across_different_main_languages_and_their_respecti',
                            )"
                        >
                            <div class="flex flex-wrap items-center gap-2">
                                <span>{{ __('admin.translation_list.table.target_language_values') }}</span>
                            </div>
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Usage --}}
                    <flux:table.column
                        class="w-36"
                        sortable
                        :sorted="$sortField === 'usages_count'"
                        :direction="$sortDirection"
                        wire:click="sortBy('usages_count')"
                    >
                        <x-ui.tooltip.trigger
                            :title="__('admin.translation_list.modal.usage')"
                            :text="__(
                                'admin.translation_list.table.usage_information_of_the_translation_key_useful_for_identification_and_reference',
                            )"
                        >
                            {{ __('admin.translation_list.modal.usage') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                    {{-- Column Actions --}}
                    <flux:table.column
                        class="app-table-col--actions"
                        align="center"
                    >
                        <x-ui.tooltip.trigger
                            class="mr-3"
                            :title="__('ui.table.headers.actions')"
                            :text="__('admin.translation_list.table.open_the_translation_key_review_modal')"
                        >
                            {{ __('ui.table.headers.actions') }}
                        </x-ui.tooltip.trigger>
                    </flux:table.column>

                </flux:table.columns>

                {{-- Second table header row for active target sub-language badges and layout balance, does not contain data but helps maintain a visually balanced table layout when active target sub-languages are present. --}}
                <flux:table.columns class="bg-zinc-800/70 text-zinc-400">
                    {{-- cloumn 1 to 5 --}}
                    <flux:table.column>
                        {{-- Empty column for spacing and layout balance, does not contain data but helps maintain a visually balanced table layout. --}}
                    </flux:table.column>

                    <flux:table.column>
                        {{-- Empty column for spacing and layout balance, does not contain data but helps maintain a visually balanced table layout. --}}
                    </flux:table.column>

                    <flux:table.column>
                        {{-- Empty column for spacing and layout balance, does not contain data but helps maintain a visually balanced table layout. --}}
                    </flux:table.column>

                    <flux:table.column>
                        {{-- Empty column for spacing and layout balance, does not contain data but helps maintain a visually balanced table layout. --}}
                    </flux:table.column>

                    <flux:table.column>
                        {{-- Empty column for spacing and layout balance, does not contain data but helps maintain a visually balanced table layout. --}}
                    </flux:table.column>

                    {{-- Column 5 --}}
                    <flux:table.column>
                        @if ($activeTargetSubLanguages->isNotEmpty())
                            <div class="flex max-h-9 flex-wrap items-center gap-1.5 overflow-x-hidden pr-3">
                                @foreach ($activeTargetSubLanguages as $subLanguage)
                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                        label="{{ $subLanguage->native_name ?: $subLanguage->name ?: $subLanguage->locale }}"
                                    >
                                        <x-ui.locale.flag
                                            :locale="$subLanguage->locale"
                                            size="sm"
                                        />

                                        <span class="ml-1 font-mono uppercase">
                                            {{ $subLanguage->locale }}
                                        </span>
                                    </flux:badge>
                                @endforeach
                            </div>
                        @endif
                    </flux:table.column>

                    {{-- Column 6 to 7 --}}
                    <flux:table.column>
                        {{-- Empty column for spacing and layout balance, does not contain data but helps maintain a visually balanced table layout. --}}
                    </flux:table.column>

                    <flux:table.column>
                        {{-- Empty column for spacing and layout balance, does not contain data but helps maintain a visually balanced table layout. --}}
                    </flux:table.column>
                </flux:table.columns>

                {{-- Table rows --}}
                <flux:table.rows>
                    @forelse ($translationKeys as $translationKey)

                        {{-- Table row --}}
                        <flux:table.row
                            wire:key="translation-key-{{ $translationKey->id }}"
                            @class([
                                'transition-colors',
                                'bg-sky-50/80 ring-1 ring-inset ring-sky-300 dark:bg-sky-950/30 dark:ring-sky-700' =>
                                    $focusedTranslationKeyId === $translationKey->id,
                            ])
                        >
                            {{-- Sequence Number Cell Values --}}
                            <flux:table.cell
                                class="align-top tabular-nums text-zinc-500 dark:text-zinc-400"
                                align="end"
                            >
                                {{ $translationKeys->firstItem() + $loop->index }}
                            </flux:table.cell>

                            {{-- ID Cell Values --}}
                            <flux:table.cell
                                class="w-32 align-top tabular-nums text-zinc-500 dark:text-zinc-400"
                                align="end"
                            >
                                #{{ $translationKey->id }}
                            </flux:table.cell>

                            {{-- Status Cell Values --}}
                            <flux:table.cell
                                class="w-px whitespace-nowrap align-top"
                                align="center"
                            >
                                @php
                                    $translationKeyNeedsNewKeyManually =
                                        $translationKey->needs_new_key_marked_at !== null &&
                                        $translationKey->needs_new_key_resolved_at === null;

                                    $translationKeyNeedsNewKeyFromAudit =
                                        (int) ($translationKey->needs_key_usage_audit_follow_up_count ?? 0) > 0;

                                    $translationKeyStatusBadgeValue =
                                        ($translationKey->status ?? '') === 'dynamic' &&
                                        (bool) ($translationKey->is_dynamic_multi ?? false)
                                            ? 'dynamic-multi'
                                            : (string) ($translationKey->status ?? '');
                                @endphp

                                <div class="flex flex-col items-center gap-1.5 space-y-2">
                                    <x-ui.badge.context
                                        context="translation.key.status"
                                        :value="$translationKeyStatusBadgeValue"
                                        :label="str($translationKeyStatusBadgeValue)->replace('-', ' ')->headline()"
                                        size="sm"
                                    />

                                    @if ($translationKeyNeedsNewKeyManually)
                                        <x-ui.tooltip.trigger
                                            :title="__('Manual needs new key')"
                                            :text="__(
                                                'This translation key was manually marked as needing a new key and is independent from generated audit results.',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="violet"
                                            >
                                                {{ __('Needs new key') }}
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    @endif

                                    @if ($translationKeyNeedsNewKeyFromAudit)
                                        <x-ui.tooltip.trigger
                                            :title="__('Audit needs new key')"
                                            :text="__(
                                                'This Needs-New-Key follow-up comes from translation usage audit decisions or usage rows.',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="yellow"
                                            >
                                                {{ __('Audit needs key') }}
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    @endif
                                </div>
                            </flux:table.cell>

                            {{-- Key / Suggested Key Cell Values --}}
                            <flux:table.cell class="align-top">
                                @php
                                    $key = trim((string) ($translationKey->key ?? ''));
                                    $suggestedKey = trim((string) ($translationKey->suggested_key ?? ''));
                                    $canEditTranslations = $key !== '';
                                    $isObsoleteReviewed = ($translationKey->workflow_status ?? 'open') === 'reviewed';

                                    $namespace = trim((string) ($translationKey->namespace ?? ''));
                                    $group = trim((string) ($translationKey->group ?? ''));
                                    $prefixParts = array_values(
                                        array_filter(
                                            [$namespace !== '*' ? $namespace : '', $group],
                                            static fn(string $segment): bool => $segment !== '',
                                        ),
                                    );
                                    $namespaceGroupPrefix = implode('.', $prefixParts);
                                    $prefixWithDot = $namespaceGroupPrefix !== '' ? $namespaceGroupPrefix . '.' : '';

                                    $expectedKey = $suggestedKey;

                                    if ($key !== '' && $prefixWithDot !== '' && str_starts_with($key, $prefixWithDot)) {
                                        $expectedKey = substr($key, strlen($prefixWithDot));
                                    }

                                    if ($expectedKey === '') {
                                        $expectedKey = $suggestedKey;
                                    }

                                    if ($expectedKey === '') {
                                        $expectedKey = $key;
                                    }

                                    $showObsoleteDiffHint =
                                        ($translationKey->status ?? '') === 'obsolete' &&
                                        $key !== '' &&
                                        $expectedKey !== '' &&
                                        $key !== $expectedKey;
                                    $showObsoleteNoDiffHint =
                                        ($translationKey->status ?? '') === 'obsolete' &&
                                        $key !== '' &&
                                        $expectedKey !== '' &&
                                        $key === $expectedKey;

                                    if ($key === '' && $suggestedKey !== '') {
                                        $keySuggestionState = 'missing_key';
                                        $keySuggestionLabel = __('admin.translation_list.modal.missing_key');
                                    } elseif ($key !== '' && $suggestedKey !== '' && $key === $suggestedKey) {
                                        $keySuggestionState = 'matches_suggested_key';
                                        $keySuggestionLabel = __('admin.translation_list.modal.matches_suggested_key');
                                    } elseif ($key !== '' && $suggestedKey !== '' && $key !== $suggestedKey) {
                                        $keySuggestionState = 'differs_from_suggested_key';
                                        $keySuggestionLabel = __(
                                            'admin.translation_list.table.differs_from_suggested_key',
                                        );
                                    } else {
                                        $keySuggestionState = 'no_suggestion';
                                        $keySuggestionLabel = __('admin.translation_list.modal.no_suggestion');
                                    }
                                @endphp

                                <div class="space-y-2">
                                    <div class="space-y-1">
                                        <div
                                            class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                            <span>{{ __('admin.translation_list.table.key') }}</span>

                                            @if (($translationKey->status ?? '') === 'obsolete')
                                                <x-ui.tooltip.trigger
                                                    context="obsolete"
                                                    :title="__('admin.translation_list.table.obsolete_key')"
                                                    :text="__(
                                                        'admin.translation_list.table.this_key_is_currently_not_found_in_code_usage_it_can_indicate_a_legacy_export_mi',
                                                    )"
                                                    :action="$isObsoleteReviewed
                                                        ? null
                                                        : [
                                                            'label' => __('admin.translation_list.table.mark_reviewed'),
                                                            'text' => __(
                                                                'admin.translation_list.modal_edit.mark_this_obsolete_entry_as_reviewed_so_it_is_removed_from_the_default_open_work',
                                                            ),
                                                            'event' => 'translation-obsolete-reviewed',
                                                            'detail' => ['translationKeyId' => $translationKey->id],
                                                        ]"
                                                >
                                                    <flux:badge
                                                        color="amber"
                                                        size="sm"
                                                        variant="subtle"
                                                    >
                                                        {{ __('admin.translation_list.meta.obsolete') }}
                                                    </flux:badge>
                                                </x-ui.tooltip.trigger>

                                                @if ($isObsoleteReviewed)
                                                    <flux:badge
                                                        color="emerald"
                                                        size="sm"
                                                        variant="subtle"
                                                    >
                                                        {{ __('admin.translation_list.modal_edit.reviewed') }}
                                                    </flux:badge>
                                                @endif
                                            @endif
                                        </div>

                                        @if ($key !== '')
                                            <x-ui.text.key-segment-diff
                                                :current-key="$key"
                                                :reference-key="($translationKey->status ?? '') === 'obsolete' ? $expectedKey : $suggestedKey"
                                                :highlight-differences="($translationKey->status ?? '') === 'obsolete'"
                                                base-class="hyphens-auto wrap-anywhere whitespace-normal font-mono text-zinc-900 dark:text-zinc-100"
                                            />
                                        @else
                                            <div class="text-zinc-400">
                                                —
                                            </div>
                                        @endif
                                    </div>

                                    <div class="space-y-1">
                                        <div
                                            class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                            {{ __('admin.translation_list.table.suggested_key') }}
                                        </div>

                                        @if ($suggestedKey !== '')
                                            <x-ui.text.key-segment-diff
                                                :current-key="$suggestedKey"
                                                :reference-key="$key"
                                                :highlight-differences="($translationKey->status ?? '') === 'obsolete'"
                                                :highlight-all-when-no-difference="($translationKey->status ?? '') === 'obsolete'"
                                                base-class="hyphens-auto wrap-anywhere whitespace-normal font-mono text-amber-700 dark:text-amber-300"
                                                diff-class="underline decoration-wavy underline-offset-2 decoration-amber-500 dark:decoration-amber-400"
                                            />
                                        @else
                                            <div class="text-zinc-400">
                                                —
                                            </div>
                                        @endif
                                    </div>

                                    @if ($showObsoleteDiffHint)
                                        <div class="text-xs text-amber-700 dark:text-amber-300">
                                            {{ __('admin.translation_list.modal.wavy_underline_marks_only_the_differing_key_block') }}
                                        </div>
                                    @endif

                                    @if ($showObsoleteNoDiffHint)
                                        <div class="text-xs text-amber-700 dark:text-amber-300">
                                            {{ __('admin.translation_list.modal.no_key_shape_diff_detected_obsolete_is_likely_caused_by_missing_in_code_usage') }}
                                        </div>
                                    @endif

                                    <x-ui.badge.context
                                        context="translation.key.suggestion"
                                        :value="$keySuggestionState"
                                        :label="$keySuggestionLabel"
                                        size="sm"
                                    />

                                    <div class="text-zinc-500 dark:text-zinc-400">
                                        {{ $translationKey->namespace ?? '—' }}
                                        @if ($translationKey->group)
                                            / {{ $translationKey->group }}
                                        @endif
                                    </div>
                                </div>
                            </flux:table.cell>

                            @php
                                $displayValues = $translationKey->values
                                    ->sortByDesc(static function ($item): int {
                                        return (int) ($item->updated_at?->getTimestamp() ?? 0);
                                    })
                                    ->groupBy(static function ($item): string {
                                        return \App\Support\Locale\LocaleCode::normalize(
                                            (string) ($item->locale ?? ''),
                                        );
                                    })
                                    ->map(static fn($group) => $group->first())
                                    ->sortBy(static function ($item): string {
                                        $locale = \App\Support\Locale\LocaleCode::normalize(
                                            (string) ($item->locale ?? ''),
                                        );

                                        $rank = $locale === 'en' ? 0 : 1;

                                        return $rank . '|' . $locale;
                                    }, SORT_NATURAL | SORT_FLAG_CASE)
                                    ->values();

                                $englishValue = $displayValues->first(static function ($item): bool {
                                    return \App\Support\Locale\LocaleCode::normalize((string) ($item->locale ?? '')) ===
                                        'en';
                                });

                                $targetLanguageValues = $displayValues
                                    ->filter(static function ($item): bool {
                                        return \App\Support\Locale\LocaleCode::normalize(
                                            (string) ($item->locale ?? ''),
                                        ) !== 'en';
                                    })
                                    ->values();

                                $selectedTargetLocale = \App\Support\Locale\LocaleCode::normalize(
                                    (string) ($languageFilter ?? ''),
                                );
                                $isTargetLanguageFocus = $selectedTargetLocale !== '';
                                $selectedTargetValue = null;

                                if ($isTargetLanguageFocus) {
                                    $selectedTargetValue = $targetLanguageValues->first(static function ($item) use (
                                        $selectedTargetLocale,
                                    ): bool {
                                        return \App\Support\Locale\LocaleCode::normalize(
                                            (string) ($item->locale ?? ''),
                                        ) === $selectedTargetLocale;
                                    });
                                }

                                $selectedTargetMainLocale = $selectedTargetLocale;

                                if (str_contains($selectedTargetMainLocale, '-')) {
                                    $selectedTargetMainLocale = explode('-', $selectedTargetMainLocale, 2)[0];
                                }

                                $selectedTargetSubLanguageValues = collect();

                                if ($selectedTargetMainLocale !== '') {
                                    $selectedTargetSubLanguageLocales = $activeTargetSubLanguages
                                        ->map(static function ($subLanguage): string {
                                            return \App\Support\Locale\LocaleCode::normalize(
                                                (string) ($subLanguage->locale ?? ''),
                                            );
                                        })
                                        ->filter(static function (string $locale) use (
                                            $selectedTargetMainLocale,
                                        ): bool {
                                            return str_starts_with($locale, $selectedTargetMainLocale . '-');
                                        })
                                        ->unique()
                                        ->values();

                                    $selectedTargetSubLanguageValues = $targetLanguageValues
                                        ->filter(static function ($item) use ($selectedTargetSubLanguageLocales): bool {
                                            $locale = \App\Support\Locale\LocaleCode::normalize(
                                                (string) ($item->locale ?? ''),
                                            );

                                            return $selectedTargetSubLanguageLocales->contains($locale) &&
                                                trim((string) ($item->value ?? '')) !== '';
                                        })
                                        ->sortBy(static function ($item): string {
                                            return \App\Support\Locale\LocaleCode::normalize(
                                                (string) ($item->locale ?? ''),
                                            );
                                        }, SORT_NATURAL | SORT_FLAG_CASE)
                                        ->values();
                                }
                            @endphp

                            {{-- Source Language (EN) / Native Text Cell Values --}}
                            <flux:table.cell class="align-top">
                                <div class="space-y-2">
                                    <div
                                        class="max-w-full rounded-lg border border-zinc-200 p-2 dark:border-zinc-200/30">
                                        <div class="mb-1 flex items-center justify-between gap-2">
                                            <span class="min-w-0 font-mono font-semibold">
                                                <x-ui.locale.flag
                                                    class="-mt-1"
                                                    locale="en"
                                                    size="sm"
                                                />

                                                en
                                            </span>

                                            @if ($englishValue)
                                                @if ($englishValue->is_base_duplicate === true)
                                                    <flux:badge
                                                        size="sm"
                                                        color="amber"
                                                        variant="subtle"
                                                    >
                                                        Duplicate
                                                    </flux:badge>
                                                @else
                                                    <x-ui.badge.context
                                                        context="translation.value.status"
                                                        :value="$englishValue->status"
                                                        size="sm"
                                                    />
                                                @endif
                                            @else
                                                <flux:badge
                                                    size="sm"
                                                    color="amber"
                                                    variant="subtle"
                                                >
                                                    {{ __('ui.missing') }}
                                                </flux:badge>
                                            @endif
                                        </div>

                                        <div
                                            class="wrap-anywhere max-h-36 max-w-full overflow-y-auto hyphens-auto whitespace-normal pr-1 text-zinc-600 dark:text-zinc-300"
                                            lang="en"
                                        >
                                            {{ $englishValue?->value ?: '—' }}
                                        </div>
                                    </div>

                                    <div class="max-w-md text-wrap text-xs text-zinc-500 dark:text-zinc-400">
                                        <span
                                            class="font-semibold">{{ __('admin.translation_list.modal.native_text') }}:</span>
                                        {{ $translationKey->native_text ?: '—' }}
                                    </div>
                                </div>
                            </flux:table.cell>

                            {{-- Target Language Cell Values --}}
                            <flux:table.cell class="pr-1 align-top">

                                <div class="max-h-42 space-y-2 overflow-y-auto pr-4">
                                    @if ($isTargetLanguageFocus)
                                        <div
                                            class="max-w-full rounded-lg border border-zinc-200 p-2 dark:border-zinc-200/30">
                                            <div class="mb-1 flex items-center justify-between gap-2">
                                                <span class="min-w-0 font-mono font-semibold">
                                                    <x-ui.locale.flag
                                                        class="-mt-1"
                                                        :locale="$selectedTargetLocale"
                                                        size="sm"
                                                    />

                                                    {{ $selectedTargetLocale }}
                                                </span>

                                                @if ($selectedTargetValue?->is_base_duplicate === true)
                                                    <flux:badge
                                                        size="sm"
                                                        color="amber"
                                                        variant="subtle"
                                                    >
                                                        Duplicate
                                                    </flux:badge>
                                                @elseif ($selectedTargetValue)
                                                    <x-ui.badge.context
                                                        context="translation.value.status"
                                                        :value="$selectedTargetValue->status"
                                                        size="sm"
                                                    />
                                                @else
                                                    <flux:badge
                                                        size="sm"
                                                        color="amber"
                                                        variant="subtle"
                                                    >
                                                        {{ __('ui.missing') }}
                                                    </flux:badge>
                                                @endif
                                            </div>

                                            <div
                                                class="wrap-anywhere max-h-36 max-w-full overflow-y-auto hyphens-auto whitespace-normal pr-1 text-zinc-600 dark:text-zinc-300"
                                                lang="{{ $selectedTargetLocale }}"
                                            >
                                                {{ $selectedTargetValue?->value ?: '—' }}
                                            </div>

                                            @if ($selectedTargetSubLanguageValues->isNotEmpty())
                                                <div class="mt-2 border-t border-zinc-200 pt-2 dark:border-zinc-700">
                                                    <div
                                                        class="mb-1 flex items-center justify-between gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                                                        <span class="font-semibold">
                                                            {{ __('admin.translation_list.modal_edit.sub_language_values') }}
                                                        </span>

                                                        <flux:badge
                                                            size="sm"
                                                            variant="subtle"
                                                            color="sky"
                                                        >
                                                            {{ $selectedTargetSubLanguageValues->count() }}
                                                        </flux:badge>
                                                    </div>

                                                    <div class="flex flex-wrap items-center gap-1.5">
                                                        @foreach ($selectedTargetSubLanguageValues as $subLanguageValue)
                                                            @php
                                                                $subLanguageLocale = \App\Support\Locale\LocaleCode::normalize(
                                                                    (string) ($subLanguageValue->locale ?? ''),
                                                                );
                                                            @endphp

                                                            <x-ui.tooltip.trigger
                                                                :title="strtoupper($subLanguageLocale)"
                                                                :text="$subLanguageValue->value"
                                                            >
                                                                <flux:badge
                                                                    size="sm"
                                                                    variant="subtle"
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
                                    @else
                                        @forelse ($targetLanguageValues as $value)
                                            @php
                                                $displayLocale = \App\Support\Locale\LocaleCode::normalize(
                                                    (string) ($value->locale ?? ''),
                                                );
                                            @endphp

                                            <div
                                                class="max-w-full rounded-lg border border-zinc-200 p-2 dark:border-zinc-200/30">
                                                <div class="mb-1 flex items-center justify-between gap-2">
                                                    <span class="min-w-0 font-mono font-semibold">
                                                        <x-ui.locale.flag
                                                            class="-mt-1"
                                                            :locale="$displayLocale"
                                                            size="sm"
                                                        />

                                                        {{ $displayLocale }}
                                                    </span>

                                                    @if ($value->is_base_duplicate === true)
                                                        <flux:badge
                                                            size="sm"
                                                            color="amber"
                                                            variant="subtle"
                                                        >
                                                            Duplicate
                                                        </flux:badge>
                                                    @else
                                                        <x-ui.badge.context
                                                            context="translation.value.status"
                                                            :value="$value->status"
                                                            size="sm"
                                                        />
                                                    @endif
                                                </div>

                                                <div
                                                    class="wrap-anywhere max-h-36 max-w-full overflow-y-auto hyphens-auto whitespace-normal pr-1 text-zinc-600 dark:text-zinc-300"
                                                    lang="{{ $displayLocale }}"
                                                >
                                                    {{ $value->value ?: '—' }}
                                                </div>
                                            </div>
                                        @empty
                                            <span class="text-zinc-400">
                                                —
                                            </span>
                                        @endforelse
                                    @endif
                                </div>
                            </flux:table.cell>

                            {{-- Cell Last seen --}}
                            <flux:table.cell class="align-top tabular-nums">
                                <div class="space-y-1 text-zinc-500 dark:text-zinc-400">
                                    <div>
                                        <span
                                            class="font-semibold tabular-nums">{{ $translationKey->usages_count }}</span>
                                        {{ __('admin.translation_list.table.usage_s') }}
                                    </div>

                                    @if ($translationKey->last_seen_at)
                                        <div>
                                            <div class="app-table-cell-item-header">
                                                {{ __('admin.translation_list.table.last_seen') }}:
                                            </div>
                                            <div class="app-table-cell-item-timestamp">
                                                <x-ui.date-time.date :value="$translationKey->last_seen_at" />
                                            </div>
                                            <div class="app-table-cell-item-timestamp">
                                                <x-ui.date-time.time :value="$translationKey->last_seen_at" />
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </flux:table.cell>

                            {{-- Cell Actions / Review / Edit --}}
                            <flux:table.cell
                                class="align-top"
                                align="center"
                            >
                                <div class="mr-4 inline-flex flex-col items-center gap-3">
                                    {{-- Review button --}}
                                    <x-ui.button.review
                                        size="sm"
                                        wire:click="openTranslationKey({{ $translationKey->id }})"
                                    />

                                    {{-- Edit button --}}
                                    <x-ui.button.edit
                                        size="sm"
                                        :disabled="!$canEditTranslations"
                                        wire:click="openTranslationEdit({{ $translationKey->id }})"
                                    />

                                    {{-- History button --}}
                                    <flux:button
                                        type="button"
                                        size="sm"
                                        variant="primary"
                                        color="zinc"
                                        icon="history"
                                        wire:click="openTranslationHistory({{ $translationKey->id }})"
                                    >
                                        {{ __('admin.translation_list.table.history') }}
                                    </flux:button>

                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>

                            {{-- No translation records found --}}
                            <flux:table.cell colspan="7">
                                <div class="py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __('admin.translation_list.table.no_translation_records_found') }}
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if ($translationKeys->hasPages())
            <flux:separator
                class=""
                text="{{ __('ui.pagination') }}"
            />

            {{-- Pagination --}}
            <div class="mt-4">
                <x-ui.table.pagination
                    :paginator="$translationKeys"
                    scroll-to="#translation-list-pagination-top"
                />
            </div>
        @endif

    </div>
</flux:card>
