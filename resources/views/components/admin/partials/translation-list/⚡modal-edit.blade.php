{{-- resources/views/components/admin/partials/translation-list/⚡modal-edit.blade.php --}}

<flux:modal
    class="scrollbar-gutter-stable w-full max-w-7xl bg-amber-50 dark:bg-amber-950/30"
    wire:model="translationEditModalOpen"
>
    @if ($editingTranslationKey)
        <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden">
            <div class="flex shrink-0 items-start justify-between gap-4">

                {{-- Card Header with ID badge --}}
                <x-ui.headers.card
                    :title="__('admin.translation_list.modal_edit.translation_values_edit')"
                    :description="__(
                        'admin.translation_list.modal_edit.edit_translation_values_for_the_selected_key_key_metadata_is_read_only',
                    )"
                />

                <div class="mr-8 mt-2 flex flex-col items-end gap-2">
                    {{-- Badge with translation key ID --}}
                    <flux:badge
                        variant="subtle"
                        color="amber"
                    >
                        #{{ $editingTranslationKey->id }}
                    </flux:badge>

                    @if (($nextEditTranslationKeyId ?? null) !== null)
                        {{-- Button Next-Edit --}}
                        <x-ui.button.next-edit
                            :loading="true"
                            wire:click="openNextTranslationEditFromList"
                        />
                    @endif
                </div>
            </div>

            {{-- Edit mode info callout --}}
            <flux:callout
                color="emerald"
                icon="pen-line"
                stroke-width="1"
            >
                <flux:callout.heading>
                    {{ __('admin.translation_list.modal_edit.edit_mode') }}
                </flux:callout.heading>

                <flux:callout.text>
                    {{ __('admin.translation_list.modal_edit.only_translation_values_are_editable_keys_native_text_and_usage_metadata_are_rea') }}
                </flux:callout.text>
            </flux:callout>

            {{-- Key and group information callouts --}}
            <div class="grid gap-4 md:grid-cols-6">

                {{-- Key --}}
                <flux:callout
                    class="md:col-span-3"
                    icon="key"
                    stroke-width="1"
                >
                    @php
                        $editingTranslationKeyNeedsNewKeyManually =
                            $editingTranslationKey->needs_new_key_marked_at !== null &&
                            $editingTranslationKey->needs_new_key_resolved_at === null;

                        $isObsoleteReviewed = ($editingTranslationKey->workflow_status ?? 'open') === 'reviewed';
                    @endphp

                    <div class="flex items-start justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:heading>
                                {{ __('admin.translation_list.modal_edit.translation_key') }}
                            </flux:heading>

                            @if (($editingTranslationKey->status ?? '') === 'obsolete')
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
                                            'detail' => ['translationKeyId' => $editingTranslationKey->id],
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

                        <div class="flex flex-wrap items-center justify-end gap-2">
                            @if ($editingTranslationKeyNeedsNewKeyManually)
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
                                    type="button"
                                    inset
                                    size="sm"
                                    variant="ghost"
                                    color="amber"
                                    icon="rotate-ccw-key"
                                    wire:click="clearNeedsNewKeyManually({{ $editingTranslationKey->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="clearNeedsNewKeyManually"
                                >
                                    {{ __('Resolve') }}
                                </flux:button>
                            @else
                                <flux:button
                                    type="button"
                                    inset
                                    size="sm"
                                    variant="ghost"
                                    color="amber"
                                    icon="rotate-ccw-key"
                                    wire:click="markNeedsNewKeyManually({{ $editingTranslationKey->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="markNeedsNewKeyManually"
                                >
                                    {{ __('Needs new key') }}
                                </flux:button>
                            @endif
                        </div>
                    </div>

                    <flux:text class="wrap-anywhere whitespace-normal text-sm">
                        {{ $editingTranslationKey->key ?: '—' }}
                    </flux:text>
                </flux:callout>

                {{-- Group --}}
                <flux:callout
                    class="md:col-span-3"
                    icon="group"
                    stroke-width="1"
                >
                    <flux:heading>
                        {{ __('admin.translation_list.modal.group') }}
                    </flux:heading>

                    <div class="mt-2 flex flex-wrap gap-x-8 gap-y-1 text-sm">
                        <div>
                            <span class="font-semibold">{{ __('admin.translation_list.modal.namespace') }}:</span>
                            <span class="ml-2">{{ $editingTranslationKey->namespace ?: '—' }}</span>
                        </div>

                        <div>
                            <span class="font-semibold">{{ __('admin.translation_list.modal.group') }}:</span>
                            <span class="ml-2">{{ $editingTranslationKey->group ?: '—' }}</span>
                        </div>
                    </div>
                </flux:callout>

            </div>

            {{-- Native Text --}}
            @php
                $normalizedEditValues = $editingTranslationKey->values
                    ->sortByDesc(static function ($item): int {
                        return (int) ($item->updated_at?->getTimestamp() ?? 0);
                    })
                    ->groupBy(static function ($item): string {
                        return \App\Support\Locale\LocaleCode::normalize((string) ($item->locale ?? ''));
                    })
                    ->map(static fn($group) => $group->first());

                $normalizedTargetLocale = \App\Support\Locale\LocaleCode::normalize((string) ($languageFilter ?? ''));
                $isTargetLanguageFocus = $normalizedTargetLocale !== '';
                $targetMainLocale = str_contains($normalizedTargetLocale, '-')
                    ? explode('-', $normalizedTargetLocale, 2)[0]
                    : $normalizedTargetLocale;

                $orderedTranslationLanguages = $translationLanguages
                    ->sortBy(function ($translationLanguage) use ($normalizedTargetLocale, $targetMainLocale): string {
                        $normalizedLocale = \App\Support\Locale\LocaleCode::normalize(
                            (string) ($translationLanguage->locale ?? ''),
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

                $displayTranslationLanguages = $isTargetLanguageFocus
                    ? $orderedTranslationLanguages
                        ->filter(function ($translationLanguage) use (
                            $normalizedTargetLocale,
                            $targetMainLocale,
                        ): bool {
                            $normalizedLocale = \App\Support\Locale\LocaleCode::normalize(
                                (string) ($translationLanguage->locale ?? ''),
                            );

                            return $normalizedLocale === 'en' ||
                                $normalizedLocale === $normalizedTargetLocale ||
                                ($targetMainLocale !== '' && $normalizedLocale === $targetMainLocale);
                        })
                        ->values()
                    : $orderedTranslationLanguages;

                $activeEditSubLanguages = collect($activeTargetSubLanguages ?? [])->values();
                $selectedEditSubLanguageLocales = collect($translationEditSubLanguageLocales ?? [])
                    ->map(static function ($locale): string {
                        return \App\Support\Locale\LocaleCode::normalize((string) $locale);
                    })
                    ->filter()
                    ->unique()
                    ->values();

                if ($selectedEditSubLanguageLocales->isNotEmpty()) {
                    $displayLocaleSet = $displayTranslationLanguages
                        ->map(static function ($translationLanguage): string {
                            return \App\Support\Locale\LocaleCode::normalize(
                                (string) ($translationLanguage->locale ?? ''),
                            );
                        })
                        ->filter()
                        ->values();

                    if ($selectedEditSubLanguageLocales->diff($displayLocaleSet)->isNotEmpty()) {
                        $displayLocaleSet = $displayLocaleSet
                            ->merge($selectedEditSubLanguageLocales)
                            ->unique()
                            ->values();

                        $displayTranslationLanguages = $orderedTranslationLanguages
                            ->filter(function ($translationLanguage) use ($displayLocaleSet): bool {
                                $normalizedLocale = \App\Support\Locale\LocaleCode::normalize(
                                    (string) ($translationLanguage->locale ?? ''),
                                );

                                return $displayLocaleSet->contains($normalizedLocale);
                            })
                            ->values();
                    }
                }

                $nativeTextValue = (string) ($editingTranslationKey->native_text ?? '');
                $hasEnglishLocale = $displayTranslationLanguages->contains(
                    fn($translationLanguage): bool => (string) ($translationLanguage->locale ?? '') === 'en',
                );

                $englishEditValue = trim((string) data_get($translationEditValues ?? [], 'en', ''));

                $hasNativeTextToCopy = trim($nativeTextValue) !== '';
                $canShowCopyToEnglishValue = $hasEnglishLocale && $hasNativeTextToCopy;
                $canCopyToEnglishValue = $canShowCopyToEnglishValue && $englishEditValue === '';

                $translationEditRequiredLocales = $displayTranslationLanguages
                    ->map(static fn($translationLanguage): string => (string) ($translationLanguage->locale ?? ''))
                    ->filter()
                    ->values();

                $translationEditCanSave = $translationEditRequiredLocales->every(
                    static fn(string $locale): bool => trim(
                        (string) data_get($translationEditValues ?? [], $locale, ''),
                    ) !== '',
                );

                $translationEditValueLocales = collect($translationEditValues ?? [])
                    ->keys()
                    ->map(static function ($locale): string {
                        return \App\Support\Locale\LocaleCode::normalize((string) $locale);
                    })
                    ->filter()
                    ->unique()
                    ->values();

                $translationEditHasUnsavedChanges = $translationEditValueLocales->contains(static function (
                    string $locale,
                ) use ($translationEditValues, $normalizedEditValues): bool {
                    $currentValue = trim((string) data_get($translationEditValues ?? [], $locale, ''));

                    $storedValue = trim((string) ($normalizedEditValues->get($locale)?->value ?? ''));

                    return $currentValue !== $storedValue;
                });
            @endphp

            <flux:field class="scrollbar-gutter-auto -mr-4 overflow-x-hidden pr-4">
                <flux:callout
                    class="mb-3"
                    color="cyan"
                    icon="megaphone"
                    stroke-width="1"
                >
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <flux:callout.heading>
                            {{ __('admin.translation_list.modal.native_text') }}
                        </flux:callout.heading>

                        <div class="flex items-center gap-2">
                            @if ($canShowCopyToEnglishValue)
                                <x-ui.button.confirm
                                    class="inset font-semibold"
                                    type="button"
                                    :label="__('admin.translation_list.modal_edit.copy_to_en_value')"
                                    size="xs"
                                    :disabled="!$canCopyToEnglishValue"
                                    wire:click="copyNativeTextToEnglishValue"
                                    wire:loading.attr="disabled"
                                    wire:target="copyNativeTextToEnglishValue"
                                />
                            @endif
                        </div>
                    </div>

                    <flux:callout.text>
                        {{ __('admin.translation_list.modal_edit.the_native_text_is_the_original_string_in_the_source_language_it_serves_as_a_ref') }}
                    </flux:callout.text>
                    <flux:text class="wrap-anywhere mt-3 font-mono text-sm">
                        {{ $editingTranslationKey->native_text ?: '—' }}

                    </flux:text>
                </flux:callout>

                {{-- Usage locations --}}
                <flux:callout
                    class="mb-3"
                    color="sky"
                    icon="route"
                    stroke-width="1"
                >
                    <div class="flex items-center justify-between gap-3">
                        <flux:callout.heading>
                            {{ __('admin.translation_list.modal_edit.usage_locations') }}
                        </flux:callout.heading>

                        <flux:badge
                            variant="subtle"
                            color="sky"
                        >
                            {{ $editingTranslationKey->usages->count() }}
                        </flux:badge>
                    </div>

                    <flux:callout.text>
                        {{ __('admin.translation_list.modal_edit.readonly_source_locations_where_this_translation_key_is_currently_used') }}
                    </flux:callout.text>

                    <div class="space-y-2">
                        @forelse ($editingTranslationKey->usages as $usage)
                            <div
                                class="rounded-lg border border-sky-200 bg-white/60 p-3 text-sm dark:border-sky-800 dark:bg-zinc-950/30">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div
                                            class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                            {{ __('admin.translation_list.modal.path') }}
                                        </div>

                                        <code class="wrap-anywhere whitespace-normal text-xs">
                                            {{ $usage->file ?: '—' }}
                                        </code>
                                    </div>

                                    <flux:badge
                                        size="sm"
                                        variant="subtle"
                                        color="zinc"
                                    >
                                        {{ __('admin.translation_list.modal.line') }} {{ $usage->line ?: '—' }}
                                    </flux:badge>
                                </div>
                            </div>
                        @empty
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('admin.translation_list.modal_edit.no_usage_records_available') }}
                            </flux:text>
                        @endforelse
                    </div>
                </flux:callout>

                {{-- Translation values --}}
                <flux:callout
                    class="mb-3"
                    color="red"
                    icon="file-pen-line"
                    stroke-width="1"
                >

                    @if ($activeEditSubLanguages->isNotEmpty())
                        @php
                            $subLanguageTranslationValueCount = $activeEditSubLanguages
                                ->filter(static function ($subLanguage) use ($normalizedEditValues): bool {
                                    $locale = \App\Support\Locale\LocaleCode::normalize(
                                        (string) ($subLanguage->locale ?? ''),
                                    );

                                    $translationValue = $normalizedEditValues->get($locale);

                                    return $translationValue && trim((string) ($translationValue->value ?? '')) !== '';
                                })
                                ->count();
                        @endphp
                    @endif

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <flux:callout.heading>
                            {{ __('admin.translation_list.modal_edit.translation_values') }}
                        </flux:callout.heading>

                        @if ($activeEditSubLanguages->isNotEmpty())
                            <div class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                                <span class="font-semibold">
                                    {{ __('admin.translation_list.modal_edit.sub_language_values') }}
                                </span>

                                <flux:badge
                                    size="sm"
                                    variant="subtle"
                                    color="{{ $subLanguageTranslationValueCount > 0 ? 'emerald' : 'zinc' }}"
                                >
                                    {{ $subLanguageTranslationValueCount }} / {{ $activeEditSubLanguages->count() }}
                                </flux:badge>
                            </div>
                        @endif
                    </div>

                    <flux:callout.text>
                        {{ __('admin.translation_list.modal_edit.below_are_the_translation_values_for_each_language_you_can_edit_the_values_and_s') }}
                    </flux:callout.text>

                    @if ($activeEditSubLanguages->isNotEmpty())

                        <div
                            class="scrollbar-gutter-stable mt-3 flex max-w-full gap-2 overflow-x-auto rounded-lg bg-white/40 p-2 dark:bg-zinc-950/20">
                            @foreach ($activeEditSubLanguages as $subLanguage)
                                @php
                                    $subLanguageLocale = \App\Support\Locale\LocaleCode::normalize(
                                        (string) ($subLanguage->locale ?? ''),
                                    );

                                    $isSelectedSubLanguage =
                                        $subLanguageLocale !== '' &&
                                        $selectedEditSubLanguageLocales->contains($subLanguageLocale);

                                    $subLanguageTranslationValue = $normalizedEditValues->get($subLanguageLocale);

                                    $hasSubLanguageTranslationValue =
                                        $subLanguageTranslationValue &&
                                        trim((string) ($subLanguageTranslationValue->value ?? '')) !== '';

                                    $subLanguageButtonColor = $hasSubLanguageTranslationValue ? 'emerald' : null;
                                @endphp

                                @if ($subLanguageLocale !== '')
                                    <flux:button
                                        class="h-8 shrink-0 gap-2 px-3"
                                        type="button"
                                        size="sm"
                                        :icon="$isSelectedSubLanguage ? 'minus' : 'plus'"
                                        :variant="$isSelectedSubLanguage || $hasSubLanguageTranslationValue ? 'primary' : 'ghost'"
                                        :color="$subLanguageButtonColor"
                                        wire:click="selectTranslationEditSubLanguage('{{ $subLanguageLocale }}')"
                                        wire:key="translation-edit-sublanguage-{{ $subLanguageLocale }}"
                                    >
                                        <x-ui.locale.flag
                                            :locale="$subLanguageLocale"
                                            size="sm"
                                        />

                                        <span class="font-mono uppercase">
                                            {{ $subLanguageLocale }}
                                        </span>
                                    </flux:button>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div class="grid gap-3 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-2">

                        @foreach ($displayTranslationLanguages as $translationLanguage)
                            @php
                                $normalizedLocale = \App\Support\Locale\LocaleCode::normalize(
                                    (string) ($translationLanguage->locale ?? ''),
                                );

                                $isSelectedSubLanguageValue =
                                    $normalizedLocale !== '' &&
                                    $selectedEditSubLanguageLocales->contains($normalizedLocale);

                                $isFirstSelectedSubLanguageValue =
                                    $isSelectedSubLanguageValue &&
                                    $selectedEditSubLanguageLocales->search($normalizedLocale) === 0;

                                $translationValue = $normalizedEditValues->get($normalizedLocale);
                                $isDuplicate = $translationValue?->is_base_duplicate === true;
                            @endphp

                            @if ($isFirstSelectedSubLanguageValue)
                                <div class="lg:col-span-2 xl:col-span-2">
                                    <flux:separator
                                        text="{{ __('admin.translation_list.modal_edit.language_variations') }}"
                                    />
                                </div>
                            @endif

                            <flux:callout>
                                <div class="mb-3 flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        <x-ui.locale.flag
                                            :locale="$translationLanguage->locale"
                                            size="lg"
                                        />

                                        <span class="font-mono font-semibold uppercase">
                                            {{ $translationLanguage->locale }}
                                        </span>

                                        <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $translationLanguage->native_name ?: $translationLanguage->name }}
                                        </span>
                                    </div>

                                    @if ($translationValue)
                                        @if ($isDuplicate)
                                            <flux:badge
                                                variant="subtle"
                                                color="amber"
                                                size="sm"
                                                icon="sticky-notes"
                                            >
                                                Duplicate
                                            </flux:badge>
                                        @else
                                            <x-ui.badge.context
                                                context="translation.value.status"
                                                :value="$translationValue->status"
                                                size="sm"
                                                icon="bike"
                                            />
                                        @endif
                                    @else
                                        <flux:badge
                                            inset="top bottom"
                                            variant="subtle"
                                            color="amber"
                                            icon="pen-line"
                                        >
                                            {{ __('admin.app_settings.table_icon_registry.missing') }}
                                        </flux:badge>
                                    @endif
                                </div>

                                <flux:textarea
                                    id="translation-edit-value-{{ $translationLanguage->locale }}"
                                    data-translation-value-locale="{{ $translationLanguage->locale }}"
                                    rows="1"
                                    wire:model.live.debounce.300ms="translationEditValues.{{ $translationLanguage->locale }}"
                                    wire:loading.attr="disabled"
                                    wire:target="saveTranslationEdit"
                                />

                                @if ($isFirstSelectedSubLanguageValue)
                                    <div class="mt-2 flex flex-wrap justify-end gap-2">
                                        {{-- Clear all sub-languages variations --}}
                                        <x-ui.button.clear
                                            :size="'sm'"
                                            label="{{ __('admin.translation_list.modal_edit.clear_all_language_variations') }}"
                                            wire:click="clearSelectedSubLanguageValues"
                                            wire:loading.attr="disabled"
                                            wire:target="clearSelectedSubLanguageValues"
                                        />

                                        {{-- Copy to all sub-languages variations --}}
                                        <x-ui.button.copy
                                            :size="'sm'"
                                            label="{{ __('admin.translation_list.modal_edit.copy_to_all_language_variations') }}"
                                            wire:click="copyFirstSelectedSubLanguageValueToAllSelectedSubLanguages"
                                            wire:loading.attr="disabled"
                                            wire:target="copyFirstSelectedSubLanguageValueToAllSelectedSubLanguages"
                                        />
                                    </div>
                                @endif

                                @error('translationEditValues.' . $translationLanguage->locale)
                                    <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </flux:text>
                                @enderror
                            </flux:callout>
                        @endforeach
                    </div>
                </flux:callout>
            </flux:field>

            <div class="flex shrink-0 justify-end gap-3">
                @if ($translationEditHasUnsavedChanges)
                    {{-- Button Cancel     --}}
                    <x-ui.button.cancel
                        wire:click="closeTranslationEdit"
                        wire:loading.attr="disabled"
                        wire:target="saveTranslationEdit"
                        :loading="true"
                    />
                @else
                    {{-- Button Close --}}
                    <x-ui.button.close
                        wire:click="closeTranslationEdit"
                        wire:loading.attr="disabled"
                        wire:target="saveTranslationEdit"
                        :loading="true"
                    />
                @endif

                {{-- Button Save --}}
                <x-ui.button.save
                    :disabled="!$translationEditCanSave"
                    wire:click="saveTranslationEdit"
                    wire:loading.attr="disabled"
                    wire:target="saveTranslationEdit"
                    :loading="true"
                />
                @if (($nextEditTranslationKeyId ?? null) !== null)
                    {{-- Button Next-Edit --}}
                    <x-ui.button.next-edit
                        class="h-10"
                        {{-- size="lg" --}}
                        :loading="true"
                        wire:click="openNextTranslationEditFromList"
                    />
                @endif
            </div>
        </div>
    @endif

</flux:modal>
