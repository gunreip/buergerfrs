{{-- resources/views/components/admin/partials/translation-list/⚡modal.blade.php --}}

<flux:modal
    class="w-full max-w-7xl"
    wire:model="translationKeyModalOpen"
>
    @if ($selectedTranslationKey)
        <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden">
            <div class="flex shrink-0 items-start justify-between gap-4">

                {{-- Card Header with ID badge --}}
                <x-ui.headers.card
                    :title="__('Translation key review')"
                    :description="__('Read-only review of the selected translation key, its values and usage metadata.')"
                />

                <div class="mr-8 mt-2 flex flex-col items-end gap-2">
                    {{-- Badge with translation key ID --}}
                    <flux:badge
                        variant="subtle"
                        color="zinc"
                    >
                        #{{ $selectedTranslationKey->id }}
                    </flux:badge>

                    @if ($nextReviewTranslationKeyId !== null)
                        <flux:button
                            class="h-8 w-8 shrink-0 p-0"
                            type="button"
                            size="sm"
                            variant="ghost"
                            :title="__('Open next review entry')"
                            :aria-label="__('Open next review entry')"
                            wire:click="openNextTranslationKeyFromList"
                        >
                            <flux:icon.arrow-big-right
                                class="size-5"
                                stroke-width="1"
                            />
                        </flux:button>
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
                        {{ __('Status') }}
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
                        {{ __('Group') }}
                    </flux:callout.heading>

                    <flux:callout.text class="text-sm">
                        <div>
                            <span class="inline-block w-24 font-semibold">{{ __('Namespace') }}:</span>
                            {{ $selectedTranslationKey->namespace ?? '—' }}
                        </div>

                        <div>
                            <span class="inline-block w-24 font-semibold">{{ __('Group') }}:</span>
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
                        {{ __('Source') }}
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
                        {{ __('Usage') }}
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
                    $keySuggestionLabel = __('Missing key');
                    $keySuggestionText = __(
                        'No translation key is set. The suggested key can be used as a starting point.',
                    );
                } elseif (
                    $selectedKey !== '' &&
                    $selectedSuggestedKey !== '' &&
                    $selectedKey === $selectedSuggestedKey
                ) {
                    $keySuggestionState = 'matches_suggested_key';
                    $keySuggestionLabel = __('Matches suggested key');
                    $keySuggestionText = __('The current key matches the generated suggestion.');
                } elseif (
                    $selectedKey !== '' &&
                    $selectedSuggestedKey !== '' &&
                    $selectedKey !== $selectedSuggestedKey
                ) {
                    $keySuggestionState = 'differs_from_suggested_key';
                    $keySuggestionLabel = __('Differs from suggested key');
                    $keySuggestionText = __(
                        'The current key differs from the generated suggestion. This can be intentional, but should be reviewed.',
                    );
                } else {
                    $keySuggestionState = 'no_suggestion';
                    $keySuggestionLabel = __('No suggestion');
                    $keySuggestionText = __('No suggested key is available for this entry.');
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
                            {{ __('Key suggestion check') }}
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
                        :title="__('Translation key')"
                        :value="$selectedTranslationKey->key"
                        :mono="true"
                        :showHiddenButton="true"
                        :sync-resize-group="'translation-review-key-fields-' . $selectedTranslationKey->id"
                    >
                        @if (($selectedTranslationKey->status ?? '') === 'obsolete')
                            @php
                                $isObsoleteReviewed =
                                    ($selectedTranslationKey->workflow_status ?? 'open') === 'reviewed';
                            @endphp
                            <x-slot:action>
                                <x-ui.tooltip.trigger
                                    context="obsolete"
                                    :title="__('Obsolete key')"
                                    :text="__(
                                        'This key is currently not found in code usage. It can indicate a legacy export mismatch or a truly unused translation entry.',
                                    )"
                                    :action="$isObsoleteReviewed
                                        ? null
                                        : [
                                            'label' => __('Mark reviewed'),
                                            'text' => __(
                                                'Mark this obsolete entry as reviewed so it is removed from the default open workflow list.',
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
                                        {{ __('Obsolete') }}
                                    </flux:badge>
                                </x-ui.tooltip.trigger>

                                @if ($isObsoleteReviewed)
                                    <flux:badge
                                        color="emerald"
                                        size="sm"
                                        variant="subtle"
                                    >
                                        {{ __('Reviewed') }}
                                    </flux:badge>
                                @endif
                            </x-slot:action>
                        @endif
                    </x-ui.text.copyable-field>
                </div>

                <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                    {{-- Suggested key --}}
                    <x-ui.text.copyable-field
                        :title="__('Suggested key')"
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
                                    variant="ghost"
                                    wire:click="applySuggestedKey({{ $selectedTranslationKey->id }})"
                                >
                                    {{ __('Copy to translation key') }}
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
                            {{ __('Key (obsolete diff)') }}
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
                            {{ __('Wavy underline marks only the differing key block.') }}
                        </div>
                    @endif

                    @if ($showObsoleteNoDiffHint)
                        <div class="mt-2 text-xs text-amber-700 dark:text-amber-300">
                            {{ __('No key-shape diff detected. Obsolete is likely caused by missing in-code usage.') }}
                        </div>
                    @endif
                </div>
            @endif

            <div class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-700">

                {{-- Native text --}}
                <div class="dark:border-zinc-700">

                    {{-- Native Text --}}
                    <x-ui.text.copyable-field
                        :title="__('Native text')"
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
                        {{ __('Values') }}
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
                                        : $englishReviewValue?->status ?? __('Missing')"
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
                                        : $selectedTargetReviewValue?->status ?? __('Missing')"
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
                                {{ __('No translation values available.') }}
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
                        {{ __('Usages') }}
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
                                    <span class="font-semibold">{{ __('Path') }}:</span>
                                    <code class="wrap-anywhere whitespace-normal px-3 text-xs">
                                        {{ $usage->file ?? '—' }}
                                    </code>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if ($usageOriginalMatchesRaw)
                                        {{--
                                        TODO: Z-Index für die Tooltips!
                                        --}}
                                        <x-ui.tooltip.trigger
                                            class="z-9999"
                                            :title="__('Original raw unchanged')"
                                            :text="__(
                                                'The current raw usage snippet matches the original raw reference captured for this usage.',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="green"
                                            >
                                                {{ __('Original unchanged') }}
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    @elseif ($usageOriginalDiffersRaw)
                                        <x-ui.tooltip.trigger
                                            class="z-9999"
                                            :title="__('Original raw differs')"
                                            :text="__(
                                                'The current raw usage snippet differs from the original raw reference. The original raw value is preserved below.',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="amber"
                                            >
                                                {{ __('Original changed') }}
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    @else
                                        <x-ui.tooltip.trigger
                                            class="z-9999"
                                            :title="__('No original raw reference')"
                                            :text="__(
                                                'No original raw reference has been captured for this usage yet.',
                                            )"
                                        >
                                            <flux:badge
                                                size="sm"
                                                variant="subtle"
                                                color="zinc"
                                            >
                                                {{ __('No original raw') }}
                                            </flux:badge>
                                        </x-ui.tooltip.trigger>
                                    @endif

                                    @if (!empty($usage->line))
                                        <flux:badge
                                            size="sm"
                                            variant="subtle"
                                            color="zinc"
                                        >
                                            {{ __('Line') }} {{ $usage->line }}
                                        </flux:badge>
                                    @endif
                                </div>
                            </div>

                            @if ($usageRaw !== '')
                                <x-ui.text.copyable-field
                                    class="mt-2"
                                    :title="__('Current raw')"
                                    :value="$usage->raw"
                                    :mono="true"
                                    content-class="text-xs"
                                />
                            @endif

                            @if ($usageOriginalDiffersRaw)
                                <x-ui.text.copyable-field
                                    class="mt-2"
                                    :title="__('Original raw')"
                                    :value="$usage->original_raw"
                                    :mono="true"
                                    content-class="text-xs"
                                />
                            @endif
                        </div>
                    @empty
                        <div class="px-3 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('No usage records available.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="flex shrink-0 justify-end gap-3">
                <flux:button
                    type="button"
                    variant="primary"
                    color="amber"
                    icon="pen-line"
                    :disabled="$selectedKey === ''"
                    wire:click="openTranslationEditFromReview({{ $selectedTranslationKey->id }})"
                >
                    {{ __('Edit') }}
                </flux:button>

                <x-ui.button.cancel
                    label="{{ __('Close') }}"
                    icon="circle-x"
                    wire:click="closeTranslationKey"
                />
            </div>
        </div>
    @else
        <div class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('No translation key selected.') }}
        </div>
    @endif
</flux:modal>
