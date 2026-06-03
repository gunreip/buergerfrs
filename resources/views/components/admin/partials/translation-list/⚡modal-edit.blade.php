{{-- resources/views/components/admin/partials/translation-list/⚡modal-edit.blade.php --}}

<flux:modal
    class="w-full max-w-7xl bg-amber-50 dark:bg-amber-950/30"
    wire:model="translationEditModalOpen"
>
    @if ($editingTranslationKey)
        <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden">
            <div class="flex shrink-0 items-start justify-between gap-4">

                {{-- Card Header with ID badge --}}
                <x-ui.headers.card
                    :title="__('Translation values edit')"
                    :description="__('Edit translation values for the selected key. Key metadata is read-only.')"
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
                        <flux:button
                            class="h-8 w-8 shrink-0 p-0"
                            type="button"
                            size="sm"
                            variant="ghost"
                            :title="__('Open next editable entry')"
                            :aria-label="__('Open next editable entry')"
                            wire:click="openNextTranslationEditFromList"
                        >
                            <flux:icon.arrow-big-right
                                class="size-5"
                                stroke-width="1"
                            />
                        </flux:button>
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
                    {{ __('Edit mode') }}
                </flux:callout.heading>

                <flux:callout.text>
                    {{ __('Only translation values are editable. Keys, native text and usage metadata are read-only.') }}
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
                    <div class="flex items-center gap-2">
                        <flux:heading>
                            {{ __('Translation key') }}
                        </flux:heading>

                        @if (($editingTranslationKey->status ?? '') === 'obsolete')
                            @php
                                $isObsoleteReviewed =
                                    ($editingTranslationKey->workflow_status ?? 'open') === 'reviewed';
                            @endphp
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
                                        'detail' => ['translationKeyId' => $editingTranslationKey->id],
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
                        @endif
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
                        {{ __('Group') }}
                    </flux:heading>

                    <div class="mt-2 flex flex-wrap gap-x-8 gap-y-1 text-sm">
                        <div>
                            <span class="font-semibold">{{ __('Namespace') }}:</span>
                            <span class="ml-2">{{ $editingTranslationKey->namespace ?: '—' }}</span>
                        </div>

                        <div>
                            <span class="font-semibold">{{ __('Group') }}:</span>
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
                        ->filter(function ($translationLanguage) use ($normalizedTargetLocale): bool {
                            $normalizedLocale = \App\Support\Locale\LocaleCode::normalize(
                                (string) ($translationLanguage->locale ?? ''),
                            );

                            return $normalizedLocale === 'en' || $normalizedLocale === $normalizedTargetLocale;
                        })
                        ->values()
                    : $orderedTranslationLanguages;

                $nativeTextValue = (string) ($editingTranslationKey->native_text ?? '');
                $hasEnglishLocale = $displayTranslationLanguages->contains(
                    fn($translationLanguage): bool => (string) ($translationLanguage->locale ?? '') === 'en',
                );

                $englishValueModel = (string) ($translationEditValues['en'] ?? '');
                $englishValueAudit = (string) ($normalizedEditValues->get('en')?->value ?? '');
                $currentEnglishValue = trim($englishValueModel !== '' ? $englishValueModel : $englishValueAudit);

                $hasNativeTextToCopy = trim($nativeTextValue) !== '';
                $canCopyToEnglishValue =
                    $hasEnglishLocale && $hasNativeTextToCopy && $currentEnglishValue !== trim($nativeTextValue);
            @endphp

            <flux:callout
                color="cyan"
                icon="megaphone"
                stroke-width="1"
            >
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <flux:callout.heading>
                        {{ __('Native text') }}
                    </flux:callout.heading>

                    <div class="flex items-center gap-2">
                        {{-- <flux:button
                            class="h-8 px-3 text-xs font-semibold"
                            type="button"
                            title="{{ __('Copy to clipboard') }}"
                            size="sm"
                            variant="subtle"
                            x-on:click.prevent='navigator.clipboard.writeText(@js($nativeTextValue))'
                        >
                            {{ __('Copy') }}
                        </flux:button> --}}

                        @if ($canCopyToEnglishValue)
                            <flux:button
                                class="inset h-8 px-3 text-xs font-semibold"
                                type="button"
                                size="xs"
                                variant="primary"
                                wire:click="copyNativeTextToEnglishValue"
                                wire:loading.attr="disabled"
                                wire:target="copyNativeTextToEnglishValue"
                            >
                                {{ __('Copy to values EN') }}
                            </flux:button>
                        @endif
                    </div>
                </div>

                <flux:callout.text>
                    {{ __('The native text is the original string in the source language. It serves as a reference for translators and may be used in the application when a translation is missing.') }}
                </flux:callout.text>
                <flux:text class="wrap-anywhere mt-3 font-mono text-sm">
                    {{ $editingTranslationKey->native_text ?: '—' }}

                </flux:text>
            </flux:callout>

            {{-- Usage locations --}}
            <flux:callout
                color="sky"
                icon="route"
                stroke-width="1"
            >
                <div class="mb-3 flex items-center justify-between gap-3">
                    <div>
                        <flux:callout.heading>
                            {{ __('Usage locations') }}
                        </flux:callout.heading>

                        <flux:callout.text>
                            {{ __('Readonly source locations where this translation key is currently used.') }}
                        </flux:callout.text>
                    </div>

                    <flux:badge
                        variant="subtle"
                        color="sky"
                    >
                        {{ $editingTranslationKey->usages->count() }}
                    </flux:badge>
                </div>

                <div class="space-y-2">
                    @forelse ($editingTranslationKey->usages as $usage)
                        <div
                            class="rounded-lg border border-sky-200 bg-white/60 p-3 text-sm dark:border-sky-800 dark:bg-zinc-950/30">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div
                                        class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                        {{ __('Path') }}
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
                                    {{ __('Line') }} {{ $usage->line ?: '—' }}
                                </flux:badge>
                            </div>
                        </div>
                    @empty
                        <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('No usage records available.') }}
                        </flux:text>
                    @endforelse
                </div>
            </flux:callout>

            {{-- Translation values --}}
            <flux:callout
                color="red"
                icon="file-pen-line"
                stroke-width="1"
            >
                <flux:heading>
                    {{ __('Translation values') }}
                </flux:heading>

                <flux:text class="text-sm">
                    {{ __('Below are the translation values for each language. You can edit the values and save your changes.') }}
                </flux:text>

                <div class="grid gap-3 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-2">
                    @foreach ($displayTranslationLanguages as $translationLanguage)
                        @php
                            $normalizedLocale = \App\Support\Locale\LocaleCode::normalize(
                                (string) ($translationLanguage->locale ?? ''),
                            );
                            $translationValue = $normalizedEditValues->get($normalizedLocale);
                            $isDuplicate = $translationValue?->is_base_duplicate === true;
                        @endphp
                        <flux:callout>
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <x-ui.locale.flag
                                        :locale="$translationLanguage->locale"
                                        size="sm"
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
                                        >
                                            Duplicate
                                        </flux:badge>
                                    @else
                                        <x-ui.badge.context
                                            context="translation.value.status"
                                            :value="$translationValue->status"
                                            size="sm"
                                        />
                                    @endif
                                @else
                                    <flux:badge
                                        variant="subtle"
                                        color="amber"
                                    >
                                        {{ __('Missing') }}
                                    </flux:badge>
                                @endif
                            </div>

                            <flux:textarea
                                id="translation-edit-value-{{ $translationLanguage->locale }}"
                                data-translation-value-locale="{{ $translationLanguage->locale }}"
                                rows="1"
                                wire:model.blur="translationEditValues.{{ $translationLanguage->locale }}"
                                wire:loading.attr="disabled"
                                wire:target="saveTranslationEdit"
                            />

                            @error('translationEditValues.' . $translationLanguage->locale)
                                <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                            @enderror
                        </flux:callout>
                    @endforeach
                </div>
            </flux:callout>

            <div class="flex shrink-0 justify-end gap-3">
                <x-ui.button.cancel
                    wire:click="closeTranslationEdit"
                    wire:loading.attr="disabled"
                    wire:target="saveTranslationEdit"
                />

                <x-ui.button.save
                    wire:click="saveTranslationEdit"
                    wire:loading.attr="disabled"
                    wire:target="saveTranslationEdit"
                />

            </div>
        </div>
    @endif
</flux:modal>
