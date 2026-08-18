{{-- resources/views/components/admin/partials/translation-list/⚡modal-review.blade.php --}}

{{-- Modal (Review) --}}
<flux:modal
    class="w-full max-w-7xl"
    name="translation-list-review"
    wire:model="translationKeyModalOpen"
>
    @if ($selectedTranslationKey)
        <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden">
            <div class="flex shrink-0 items-start justify-between gap-4">

                {{-- Card Header with ID badge and next button --}}
                <x-ui.headers.card
                    :title="__('admin.translation_list.modal.translation_key_review')"
                    :description="__(
                        'admin.translation_list.modal.read_only_review_of_the_selected_translation_key_its_values_and_usage_metadata',
                    )"
                />

                <div class="mr-8 mt-2 flex flex-col items-end gap-2">
                    {{-- Badge with translation key ID --}}
                    <flux:badge
                        class="tabular-nums"
                        variant="subtle"
                        color="zinc"
                    >
                        #{{ $selectedTranslationKey->id }}
                    </flux:badge>

                    @if ($nextReviewTranslationKeyId !== null)
                        {{-- Button Open Next Review Entry --}}
                        <x-ui.button.next-edit
                            :loading="true"
                            wire:click="openNextTranslationKeyFromList"
                            :aria-label="__('admin.translation_list.modal.open_next_review_entry')"
                        />
                    @endif
                </div>
            </div>

            <div class="grid gap-3 md:grid-cols-4">

                {{-- Callout components for key metadata --}}
                <flux:callout
                    color="sky"
                    icon="tag"
                >
                    {{-- Status --}}
                    <flux:callout.heading>
                        {{ __('ui.state.status') }}
                    </flux:callout.heading>

                    <flux:callout.text class="space-y-2">

                        {{-- Badge with the translation key status --}}
                        <x-ui.badge.context
                            context="translation.key.status"
                            :value="$selectedTranslationKey->status"
                            :label="str($selectedTranslationKey->status)->headline()"
                        />

                    </flux:callout.text>
                </flux:callout>

                {{-- Group Callout with namespace and group information --}}
                <flux:callout
                    color="violet"
                    icon="folder"
                >
                    {{-- Group --}}
                    <flux:callout.heading>
                        {{ __('admin.translation_list.modal.group') }}
                    </flux:callout.heading>

                    <flux:callout.text class="text-sm">
                        <div>
                            <span
                                class="inline-block w-24 font-semibold">{{ __('admin.translation_list.modal.namespace') }}:</span>
                            {{ $selectedTranslationKey->namespace ?? '—' }}
                        </div>

                        <div>
                            <span
                                class="inline-block w-24 font-semibold">{{ __('admin.translation_list.modal.group') }}:</span>
                            {{ $selectedTranslationKey->group ?? '—' }}
                        </div>
                    </flux:callout.text>
                </flux:callout>

                {{-- Source and usage callouts --}}
                <flux:callout
                    color="amber"
                    icon="scan-search"
                >
                    {{-- Source --}}
                    <flux:callout.heading>
                        {{ __('admin.translation_list.modal.source') }}
                    </flux:callout.heading>

                    {{-- Source-Path information --}}
                    <flux:callout.text class="text-sm">
                        {{ $selectedTranslationKey->source ?? '—' }}
                    </flux:callout.text>
                </flux:callout>

                {{-- Usage callout --}}
                <flux:callout
                    color="green"
                    icon="route"
                >
                    {{-- Usage --}}
                    <flux:callout.heading>
                        {{ __('admin.translation_list.modal.usage') }}
                    </flux:callout.heading>

                    {{-- Counter --}}
                    <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                        {{ $selectedTranslationKey->usages->count() }}
                    </flux:callout.text>
                </flux:callout>
            </div>

            @php
                $selectedKey = trim((string) ($selectedTranslationKey->key ?? ''));
                $selectedSuggestedKey = trim((string) ($selectedTranslationKey->suggested_key ?? ''));

                if ($selectedKey === '' && $selectedSuggestedKey !== '') {
                    $keySuggestionState = 'missing_key';
                    $keySuggestionLabel = __('admin.translation_list.modal.missing_key');
                    $keySuggestionText = __(
                        'admin.translation_list.modal.no_translation_key_is_set_the_suggested_key_can_be_used_as_a_starting_point',
                    );
                } elseif (
                    $selectedKey !== '' &&
                    $selectedSuggestedKey !== '' &&
                    $selectedKey === $selectedSuggestedKey
                ) {
                    $keySuggestionState = 'matches_suggested_key';
                    $keySuggestionLabel = __('admin.translation_list.modal.matches_suggested_key');
                    $keySuggestionText = __(
                        'admin.translation_list.modal.the_current_key_matches_the_generated_suggestion',
                    );
                } elseif (
                    $selectedKey !== '' &&
                    $selectedSuggestedKey !== '' &&
                    $selectedKey !== $selectedSuggestedKey
                ) {
                    $keySuggestionState = 'differs_from_suggested_key';
                    $keySuggestionLabel = __('admin.translation_list.table.differs_from_suggested_key');
                    $keySuggestionText = __(
                        'admin.translation_list.modal.the_current_key_differs_from_the_generated_suggestion_this_can_be_intentional_bu',
                    );
                } else {
                    $keySuggestionState = 'no_suggestion';
                    $keySuggestionLabel = __('admin.translation_list.modal.no_suggestion');
                    $keySuggestionText = __(
                        'admin.translation_list.modal.no_suggested_key_is_available_for_this_entry',
                    );
                }

                $selectedNamespace = trim((string) ($selectedTranslationKey->namespace ?? ''));
                $selectedGroup = trim((string) ($selectedTranslationKey->group ?? ''));
                $selectedPrefixParts = array_values(
                    array_filter(
                        [$selectedNamespace !== '*' ? $selectedNamespace : '', $selectedGroup],
                        static fn(string $segment): bool => $segment !== '',
                    ),
                );
                $selectedNamespaceGroupPrefix = implode('.', $selectedPrefixParts);
                $selectedPrefixWithDot =
                    $selectedNamespaceGroupPrefix !== '' ? $selectedNamespaceGroupPrefix . '.' : '';

                $selectedExpectedKey = $selectedSuggestedKey;

                if (
                    $selectedKey !== '' &&
                    $selectedPrefixWithDot !== '' &&
                    str_starts_with($selectedKey, $selectedPrefixWithDot)
                ) {
                    $selectedExpectedKey = substr($selectedKey, strlen($selectedPrefixWithDot));
                }

                if ($selectedExpectedKey === '') {
                    $selectedExpectedKey = $selectedSuggestedKey;
                }

                if ($selectedExpectedKey === '') {
                    $selectedExpectedKey = $selectedKey;
                }

                $showObsoleteDiffHint =
                    ($selectedTranslationKey->status ?? '') === 'obsolete' &&
                    $selectedKey !== '' &&
                    $selectedExpectedKey !== '' &&
                    $selectedKey !== $selectedExpectedKey;

                $showObsoleteNoDiffHint =
                    ($selectedTranslationKey->status ?? '') === 'obsolete' &&
                    $selectedKey !== '' &&
                    $selectedExpectedKey !== '' &&
                    $selectedKey === $selectedExpectedKey;

                $showObsoleteReasonBox =
                    ($selectedTranslationKey->status ?? '') === 'obsolete' &&
                    $selectedKey !== '' &&
                    $selectedExpectedKey !== '';
            @endphp

            <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('admin.translation_list.modal.key_suggestion_check') }}
                        </div>

                        <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $keySuggestionText }}
                        </div>
                    </div>

                    <x-ui.badge.context
                        context="translation.key.suggestion"
                        :value="$keySuggestionState"
                        :label="$keySuggestionLabel"
                    />
                </div>
            </div>

            <div class="grid gap-3 lg:grid-cols-2">

                {{-- Translation Key and Native Text --}}
                <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                    {{-- Key --}}
                    <x-ui.text.copyable-field
                        :title="__('admin.translation_list.modal_edit.translation_key')"
                        :value="$selectedTranslationKey->key"
                        :mono="true"
                        :showHiddenButton="true"
                        :sync-resize-group="'translation-review-key-fields-' . $selectedTranslationKey->id"
                    >
                        @php
                            $selectedTranslationKeyNeedsNewKeyManually =
                                $selectedTranslationKey->needs_new_key_marked_at !== null &&
                                $selectedTranslationKey->needs_new_key_resolved_at === null;

                            $selectedTranslationKeyIsDynamic =
                                ($selectedTranslationKey->classification ?? null) === 'dynamic';
                            $selectedTranslationKeyActionsDisabled = $selectedKey === '';

                            $isObsoleteReviewed = ($selectedTranslationKey->workflow_status ?? 'open') === 'reviewed';
                        @endphp

                        <x-slot:action>
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                @if ($selectedTranslationKeyIsDynamic)
                                    <label
                                        @class([
                                            'flex h-8 items-center gap-2 rounded-md border border-orange-200 bg-orange-50 px-2 text-sm text-orange-700 dark:border-orange-900/60 dark:bg-orange-950/30 dark:text-orange-300',
                                            'hover:cursor-pointer' => ! $selectedTranslationKeyActionsDisabled,
                                            'opacity-50' => $selectedTranslationKeyActionsDisabled,
                                        ])
                                    >
                                        <flux:checkbox
                                            :disabled="$selectedTranslationKeyActionsDisabled"
                                            wire:model.live="selectedTranslationKeyDynamicMulti"
                                            wire:loading.attr="disabled"
                                            wire:target="selectedTranslationKeyDynamicMulti"
                                        />

                                        <flux:icon.braces class="size-4" />

                                        <span>{{ __('Dynamic multi') }}</span>
                                    </label>
                                @endif

                                @if ($selectedTranslationKeyNeedsNewKeyManually)
                                    <x-ui.tooltip.trigger
                                        :title="__('Needs new key')"
                                        :text="__(
                                            'This translation key was manually marked as needing a new key and is independent from generated audit results.',
                                        )"
                                    >
                                        <flux:badge
                                            color="amber"
                                            size="sm"
                                            variant="subtle"
                                        >
                                            {{ __('Needs new key') }}
                                        </flux:badge>
                                    </x-ui.tooltip.trigger>

                                    <flux:button
                                        class="hover:cursor-pointer"
                                        type="button"
                                        size="sm"
                                        icon="rotate-ccw-key"
                                        variant="ghost"
                                        color="amber"
                                        :disabled="$selectedTranslationKeyActionsDisabled"
                                        wire:click="clearNeedsNewKeyManually({{ $selectedTranslationKey->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="clearNeedsNewKeyManually"
                                    >
                                        {{ __('Resolve') }}
                                    </flux:button>
                                @else
                                    <flux:button
                                        class="hover:cursor-pointer"
                                        type="button"
                                        size="sm"
                                        icon="rotate-ccw-key"
                                        variant="ghost"
                                        color="amber"
                                        :disabled="$selectedTranslationKeyActionsDisabled"
                                        wire:click="markNeedsNewKeyManually({{ $selectedTranslationKey->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="markNeedsNewKeyManually"
                                    >
                                        {{ __('Needs new key') }}
                                    </flux:button>
                                @endif

                                @if (($selectedTranslationKey->status ?? '') === 'obsolete')
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
                                                'detail' => ['translationKeyId' => $selectedTranslationKey->id],
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
                        </x-slot:action>
                    </x-ui.text.copyable-field>
                </div>

                <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                    {{-- Suggested key --}}
                    <x-ui.text.copyable-field
                        :title="__('admin.translation_list.table.suggested_key')"
                        :value="$selectedTranslationKey->suggested_key"
                        :mono="true"
                        :sync-resize-group="'translation-review-key-fields-' . $selectedTranslationKey->id"
                    >
                        @if ($selectedSuggestedKey !== '')
                            <x-slot:action>
                                <flux:button
                                    class="h-8 shrink-0"
                                    type="button"
                                    size="sm"
                                    icon="arrow-left"
                                    :loading="true"
                                    variant="ghost"
                                    wire:click="applySuggestedKey({{ $selectedTranslationKey->id }})"
                                >
                                    {{ __('admin.translation_list.modal.copy_to_translation_key') }}
                                </flux:button>
                            </x-slot:action>
                        @endif
                    </x-ui.text.copyable-field>
                </div>
            </div>

            @if ($showObsoleteReasonBox)
                <div
                    class="rounded-xl border border-amber-200 bg-amber-50/70 p-3 dark:border-amber-700/50 dark:bg-amber-950/30">
                    <div class="space-y-1">
                        <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            {{ __('admin.translation_list.modal.key_obsolete_diff') }}
                        </div>

                        <x-ui.text.key-segment-diff
                            :current-key="$selectedKey"
                            :reference-key="$selectedExpectedKey"
                            :highlight-differences="true"
                            base-class="wrap-anywhere text-xs whitespace-normal font-mono text-zinc-900 dark:text-zinc-100"
                        />
                    </div>

                    @if ($showObsoleteDiffHint)
                        <div class="mt-2 text-xs text-amber-700 dark:text-amber-300">
                            {{ __('admin.translation_list.modal.wavy_underline_marks_only_the_differing_key_block') }}
                        </div>
                    @endif

                    @if ($showObsoleteNoDiffHint)
                        <div class="mt-2 text-xs text-amber-700 dark:text-amber-300">
                            {{ __('admin.translation_list.modal.no_key_shape_diff_detected_obsolete_is_likely_caused_by_missing_in_code_usage') }}
                        </div>
                    @endif
                </div>
            @endif

            <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                {{-- Native text --}}
                <div class="dark:border-zinc-700">

                    {{-- Native Text --}}
                    <x-ui.text.copyable-field
                        :title="__('admin.translation_list.modal.native_text')"
                        :value="$selectedTranslationKey->native_text"
                    />
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">
                @php
                    $normalizedTargetLocale = \App\Support\Locale\LocaleCode::normalize(
                        (string) ($languageFilter ?? ''),
                    );
                    $isTargetLanguageFocus = $normalizedTargetLocale !== '';
                    $targetMainLocale = str_contains($normalizedTargetLocale, '-')
                        ? explode('-', $normalizedTargetLocale, 2)[0]
                        : $normalizedTargetLocale;

                    $reviewValues = $selectedTranslationKey->values
                        ->sortByDesc(static function ($item): int {
                            return (int) ($item->updated_at?->getTimestamp() ?? 0);
                        })
                        ->groupBy(static function ($item): string {
                            return \App\Support\Locale\LocaleCode::normalize((string) ($item->locale ?? ''));
                        })
                        ->map(static fn($group) => $group->first())
                        ->sortBy(static function ($item) use ($normalizedTargetLocale, $targetMainLocale): string {
                            $normalizedLocale = \App\Support\Locale\LocaleCode::normalize(
                                (string) ($item->locale ?? ''),
                            );

                            $localeMain = str_contains($normalizedLocale, '-')
                                ? explode('-', $normalizedLocale, 2)[0]
                                : $normalizedLocale;

                            $rank = match (true) {
                                $normalizedLocale === 'en' => 0,
                                $targetMainLocale !== '' && $normalizedLocale === $targetMainLocale => 1,
                                $normalizedTargetLocale !== '' && $normalizedLocale === $normalizedTargetLocale => 2,
                                $targetMainLocale !== '' &&
                                    $localeMain === $targetMainLocale &&
                                    str_contains($normalizedLocale, '-')
                                    => 3,
                                default => 4,
                            };

                            return $rank . '|' . $normalizedLocale;
                        }, SORT_NATURAL | SORT_FLAG_CASE)
                        ->values();

                    $englishReviewValue = $reviewValues->first(static function ($item): bool {
                        return \App\Support\Locale\LocaleCode::normalize((string) ($item->locale ?? '')) === 'en';
                    });

                    $selectedTargetReviewValue = $isTargetLanguageFocus
                        ? $reviewValues->first(static function ($item) use ($normalizedTargetLocale): bool {
                            return \App\Support\Locale\LocaleCode::normalize((string) ($item->locale ?? '')) ===
                                $normalizedTargetLocale;
                        })
                        : null;

                    $showEnglishReviewValue = !$isTargetLanguageFocus || $normalizedTargetLocale !== 'en';
                    $showTargetReviewValue = !$isTargetLanguageFocus || $normalizedTargetLocale !== 'en';
                    $reviewValueCardCount = $isTargetLanguageFocus
                        ? ($showEnglishReviewValue ? 1 : 0) + ($showTargetReviewValue ? 1 : 0)
                        : $reviewValues->count();
                @endphp

                {{-- Values and Usages --}}
                <div class="mb-3 flex items-center justify-between gap-3">

                    {{-- Values --}}
                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('admin.translation_list.modal.values') }}
                    </div>

                    {{-- Counter --}}
                    <flux:badge
                        variant="subtle"
                        color="zinc"
                    >
                        {{ $reviewValueCardCount }}
                    </flux:badge>
                </div>

                {{-- Values List --}}
                <div class="grid gap-3 md:grid-cols-2">
                    @if ($isTargetLanguageFocus)
                        @if ($showEnglishReviewValue)
                            @php
                                $displayLocale = 'en';
                                $isDuplicate = $englishReviewValue?->is_base_duplicate === true;
                            @endphp

                            <div class="rounded-lg border border-zinc-200 p-3 text-sm dark:border-zinc-700">
                                <x-ui.text.copyable-field
                                    :value="$englishReviewValue?->value"
                                    :badge="$isDuplicate
                                        ? 'Duplicate'
                                        : $englishReviewValue?->status ??
                                            __('ui.state.missing')"
                                    :badge-context="$englishReviewValue && !$isDuplicate ? 'translation.value.status' : null"
                                    :badge-color="$isDuplicate || $englishReviewValue === null ? 'amber' : 'zinc'"
                                    :badge-variant="$isDuplicate ? 'subtle' : 'subtle'"
                                >
                                    <x-slot:label>
                                        <span class="inline-flex items-center gap-2">
                                            <x-ui.locale.flag
                                                :locale="$displayLocale"
                                                size="sm"
                                            />

                                            <code>{{ $displayLocale }}</code>
                                        </span>
                                    </x-slot:label>
                                </x-ui.text.copyable-field>
                            </div>
                        @endif

                        @if ($showTargetReviewValue)
                            <div class="rounded-lg border border-zinc-200 p-3 text-sm dark:border-zinc-700">
                                <x-ui.text.copyable-field
                                    :value="$selectedTargetReviewValue?->value"
                                    :badge="$selectedTargetReviewValue?->is_base_duplicate === true
                                        ? 'Duplicate'
                                        : $selectedTargetReviewValue?->status ??
                                            __('ui.state.missing')"
                                    :badge-context="$selectedTargetReviewValue &&
                                    $selectedTargetReviewValue->is_base_duplicate !== true
                                        ? 'translation.value.status'
                                        : null"
                                    :badge-color="$selectedTargetReviewValue?->is_base_duplicate === true ||
                                    $selectedTargetReviewValue === null
                                        ? 'amber'
                                        : 'zinc'"
                                    :badge-variant="'subtle'"
                                >
                                    <x-slot:label>
                                        <span class="inline-flex items-center gap-2">
                                            <x-ui.locale.flag
                                                :locale="$normalizedTargetLocale"
                                                size="sm"
                                            />

                                            <code>{{ $normalizedTargetLocale }}</code>
                                        </span>
                                    </x-slot:label>
                                </x-ui.text.copyable-field>
                            </div>
                        @endif
                    @else
                        @forelse ($reviewValues as $value)
                            @php
                                $displayLocale = \App\Support\Locale\LocaleCode::normalize(
                                    (string) ($value->locale ?? ''),
                                );
                                $isDuplicate = $value->is_base_duplicate === true;
                            @endphp

                            <div class="rounded-lg border border-zinc-200 p-3 text-sm dark:border-zinc-700">

                                {{-- Value locale --}}
                                <x-ui.text.copyable-field
                                    :value="$value->value"
                                    :badge="$isDuplicate ? 'Duplicate' : $value->status"
                                    :badge-context="$isDuplicate ? null : 'translation.value.status'"
                                    :badge-color="$isDuplicate ? 'amber' : 'zinc'"
                                    :badge-variant="$isDuplicate ? 'subtle' : 'subtle'"
                                >
                                    <x-slot:label>
                                        <span class="inline-flex items-center gap-2">

                                            {{-- Locale flag --}}
                                            <x-ui.locale.flag
                                                :locale="$displayLocale"
                                                size="sm"
                                            />

                                            {{-- Locale code --}}
                                            <code>{{ $displayLocale }}</code>
                                        </span>
                                    </x-slot:label>
                                </x-ui.text.copyable-field>
                            </div>

                            {{-- No translation values available. --}}
                        @empty
                            <div class="px-3 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('admin.translation_list.modal.no_translation_values_available') }}
                            </div>
                        @endforelse
                    @endif
                </div>
            </div>

            <div class="flex min-h-0 flex-1 flex-col rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">

                {{-- Usages --}}
                <div class="mb-3 flex shrink-0 items-center justify-between gap-3">

                    {{-- Usages --}}
                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ __('admin.translation_list.modal.usages') }}
                    </div>

                    {{-- Counter --}}
                    <flux:badge
                        variant="subtle"
                        color="zinc"
                    >
                        {{ $selectedTranslationKey->usages->count() }}
                    </flux:badge>
                </div>

                <div class="min-h-0 flex-1 space-y-2 overflow-y-auto pr-2">
                    @forelse ($selectedTranslationKey->usages as $usage)
                        @php
                            $usageRaw = trim((string) ($usage->raw ?? ''));
                            $usageOriginalRaw = trim((string) ($usage->original_raw ?? ''));

                            $usageHasOriginalRaw = $usageOriginalRaw !== '';
                            $usageOriginalMatchesRaw = $usageHasOriginalRaw && $usageRaw === $usageOriginalRaw;
                            $usageOriginalDiffersRaw = $usageHasOriginalRaw && $usageRaw !== $usageOriginalRaw;
                        @endphp

                        <div class="text-sm dark:border-zinc-700">
                            <div
                                class="flex flex-wrap items-center justify-between gap-2 border-t pt-1 dark:border-zinc-700">
                                <div class="">
                                    <span class="font-semibold">{{ __('admin.translation_list.modal.path') }}:</span>
                                    <code class="wrap-anywhere whitespace-normal px-3 text-xs">
                                        {{ $usage->file ?? '—' }}
                                    </code>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if ($usageOriginalMatchesRaw)
                                        <x-ui.tooltip.trigger
                                            class="z-9999"
                                            :title="__('admin.translation_list.modal.original_raw_unchanged')"
                                            :text="__(
                                                'admin.translation_list.modal.the_current_raw_usage_snippet_matches_the_original_raw_reference_captured_for_th',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="green"
                                            >
                                                {{ __('admin.translation_list.modal.original_unchanged') }}
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    @elseif ($usageOriginalDiffersRaw)
                                        <x-ui.tooltip.trigger
                                            class="z-9999"
                                            :title="__('admin.translation_list.modal.original_raw_differs')"
                                            :text="__(
                                                'admin.translation_list.modal.the_current_raw_usage_snippet_differs_from_the_original_raw_reference_the_origin',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="amber"
                                            >
                                                {{ __('admin.translation_list.modal.original_changed') }}
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    @else
                                        <x-ui.tooltip.trigger
                                            class="z-9999"
                                            :title="__('admin.translation_list.modal.no_original_raw_reference')"
                                            :text="__(
                                                'admin.translation_list.modal.no_original_raw_reference_has_been_captured_for_this_usage_yet',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="zinc"
                                            >
                                                {{ __('admin.translation_list.modal.no_original_raw') }}
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    @endif

                                    @if (!empty($usage->line))
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="zinc"
                                        >
                                            {{ __('admin.translation_list.modal.line') }} {{ $usage->line }}
                                        </flux:badge>
                                    @endif
                                </div>
                            </div>

                            @if ($usageRaw !== '')
                                <x-ui.text.copyable-field
                                    class="mt-2"
                                    :title="__('admin.translation_list.modal.current_raw')"
                                    :value="$usage->raw"
                                    :mono="true"
                                    content-class="text-xs"
                                />
                            @endif

                            @if ($usageOriginalDiffersRaw)
                                <x-ui.text.copyable-field
                                    class="mt-2"
                                    :title="__('admin.translation_list.modal.original_raw')"
                                    :value="$usage->original_raw"
                                    :mono="true"
                                    content-class="text-xs"
                                />
                            @endif
                        </div>
                    @empty
                        <div class="px-3 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('admin.translation_list.modal_edit.no_usage_records_available') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="flex shrink-0 justify-end gap-3">
                {{-- Button Edit --}}
                <x-ui.button.edit
                    :loading="true"
                    :disabled="$selectedKey === ''"
                    wire:click="openTranslationEditFromReview({{ $selectedTranslationKey->id }})"
                />

                {{-- Button Cancel --}}
                <x-ui.button.cancel
                    label="{{ __('ui.actions.close') }}"
                    icon="circle-x"
                    :loading="true"
                    wire:click="closeTranslationKey"
                />

                @if ($nextReviewTranslationKeyId !== null)
                    {{-- Button Open Next Review Entry --}}
                    <x-ui.button.next-edit
                        class="h-10 w-10"
                        :loading="true"
                        wire:click="openNextTranslationKeyFromList"
                        :aria-label="__('admin.translation_list.modal.open_next_review_entry')"
                    />
                @endif
            </div>
        </div>
    @else
        <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('admin.translation_list.modal.no_translation_key_selected') }}
        </div>
    @endif
</flux:modal>
