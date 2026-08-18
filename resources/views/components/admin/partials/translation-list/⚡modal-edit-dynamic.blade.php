{{-- resources/views/components/admin/partials/translation-list/⚡modal-edit-dynamic.blade.php --}}

<flux:modal
    class="scrollbar-gutter-stable w-full max-w-7xl bg-orange-50 dark:bg-orange-950/30"
    wire:model="translationEditModalOpen"
>
    @if ($editingTranslationKey)
        @php
            $normalizedEditValues = $editingTranslationKey->values
                ->sortByDesc(static function ($item): int {
                    return (int) ($item->updated_at?->getTimestamp() ?? 0);
                })
                ->groupBy(static function ($item): string {
                    return \App\Support\Locale\LocaleCode::normalize((string) ($item->locale ?? ''));
                })
                ->map(static fn($group) => $group->first());

            $orderedTranslationLanguages = collect($translationLanguages ?? [])
                ->sortBy(function ($translationLanguage): string {
                    $normalizedLocale = \App\Support\Locale\LocaleCode::normalize(
                        (string) ($translationLanguage->locale ?? ''),
                    );

                    return ($normalizedLocale === 'en' ? '0' : '1') . '|' . $normalizedLocale;
                }, SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            $translationEditRequiredLocales = $orderedTranslationLanguages
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

        <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden">
            <div class="flex shrink-0 items-start justify-between gap-4">
                <x-ui.headers.card
                    :title="__('Dynamic multi values edit')"
                    :description="__('Edit translation values for the selected dynamic multi key.')"
                />

                <div class="mr-8 mt-2 flex flex-col items-end gap-2">
                    <flux:badge
                        variant="subtle"
                        color="orange"
                    >
                        #{{ $editingTranslationKey->id }}
                    </flux:badge>

                    @if (($nextEditTranslationKeyId ?? null) !== null)
                        <x-ui.button.next-edit
                            :loading="true"
                            wire:click="openNextTranslationEditFromList"
                        />
                    @endif
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-6">
                <flux:callout
                    class="md:col-span-3"
                    icon="braces"
                    color="orange"
                    stroke-width="1"
                >
                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                        <flux:heading>
                            {{ __('admin.translation_list.modal_edit.translation_key') }}
                        </flux:heading>

                        <flux:badge
                            color="orange"
                            size="sm"
                            variant="subtle"
                        >
                            {{ __('Dynamic multi') }}
                        </flux:badge>
                    </div>

                    <flux:text class="wrap-anywhere whitespace-normal font-mono text-sm">
                        {{ $editingTranslationKey->key ?: '—' }}
                    </flux:text>
                </flux:callout>

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

            <flux:field class="scrollbar-gutter-auto -mr-4 overflow-x-hidden pr-4">
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

                    <div class="space-y-2">
                        @forelse ($editingTranslationKey->usages as $usage)
                            <div
                                class="rounded-lg border border-sky-200 bg-white/60 p-3 text-sm dark:border-sky-800 dark:bg-zinc-950/30">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <code class="wrap-anywhere whitespace-normal text-xs">
                                        {{ $usage->file ?: '—' }}
                                    </code>

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

                <flux:callout
                    class="mb-3"
                    color="orange"
                    icon="file-pen-line"
                    stroke-width="1"
                >
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <flux:callout.heading>
                            {{ __('admin.translation_list.modal_edit.translation_values') }}
                        </flux:callout.heading>

                        <flux:badge
                            variant="subtle"
                            color="orange"
                        >
                            {{ $orderedTranslationLanguages->count() }}
                        </flux:badge>
                    </div>

                    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                        @foreach ($orderedTranslationLanguages as $translationLanguage)
                            @php
                                $locale = \App\Support\Locale\LocaleCode::normalize(
                                    (string) ($translationLanguage->locale ?? ''),
                                );
                                $translationValue = $normalizedEditValues->get($locale);
                            @endphp

                            <flux:callout
                                color="zinc"
                                icon="languages"
                                stroke-width="1"
                            >
                                <div class="mb-2 flex items-center justify-between gap-2">
                                    <span class="inline-flex items-center gap-2 font-mono font-semibold">
                                        <x-ui.locale.flag
                                            :locale="$locale"
                                            size="sm"
                                        />

                                        {{ $locale }}
                                    </span>

                                    @if ($translationValue)
                                        <x-ui.badge.context
                                            context="translation.value.status"
                                            :value="$translationValue->status"
                                            size="sm"
                                        />
                                    @else
                                        <flux:badge
                                            inset="top bottom"
                                            variant="subtle"
                                            color="amber"
                                            icon="pen-line"
                                        >
                                            {{ __('ui.state.missing') }}
                                        </flux:badge>
                                    @endif
                                </div>

                                <flux:textarea
                                    id="dynamic-translation-edit-value-{{ $locale }}"
                                    rows="3"
                                    wire:model.live.debounce.300ms="translationEditValues.{{ $locale }}"
                                    wire:loading.attr="disabled"
                                    wire:target="saveTranslationEdit"
                                />

                                @error('translationEditValues.' . $locale)
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
                    <x-ui.button.cancel
                        wire:click="closeTranslationEdit"
                        wire:loading.attr="disabled"
                        wire:target="saveTranslationEdit"
                        :loading="true"
                    />
                @else
                    <x-ui.button.close
                        wire:click="closeTranslationEdit"
                        wire:loading.attr="disabled"
                        wire:target="saveTranslationEdit"
                        :loading="true"
                    />
                @endif

                <x-ui.button.save
                    :disabled="!$translationEditCanSave"
                    wire:click="saveTranslationEdit"
                    wire:loading.attr="disabled"
                    wire:target="saveTranslationEdit"
                    :loading="true"
                />
            </div>
        </div>
    @endif
</flux:modal>
