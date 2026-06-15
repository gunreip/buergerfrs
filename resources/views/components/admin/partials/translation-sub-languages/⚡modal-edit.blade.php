{{-- resources/views/components/admin/partials/translation-sub-languages/⚡modal-edit.blade.php --}}

<flux:modal
    class="w-full max-w-6xl"
    wire:model.self="translationEntryEditModalOpen"
>
    <div class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <x-ui.headers.card
                :title="__('Edit Translation Entries')"
                :description="__('Update values for en, the selected main language and selected sub-languages.')"
            />

            <div class="mr-8 mt-1 flex flex-col items-end gap-2">
                <flux:badge
                    color="zinc"
                    variant="subtle"
                >
                    #{{ $editingTranslationKeyId ?? '—' }}
                </flux:badge>

                @if (($nextEditTranslationKeyId ?? null) !== null)
                    <flux:button
                        class="h-8 w-8 shrink-0 p-0"
                        type="button"
                        size="sm"
                        variant="ghost"
                        {{-- :title="__('admin.translation_list.modal_edit.open_next_editable_entry')" --}}
                        :aria-label="__('admin.translation_list.modal_edit.open_next_editable_entry')"
                        wire:click="openNextTranslationEntryEditFromList"
                    >
                        <flux:icon.arrow-big-right
                            class="size-5"
                            stroke-width="1"
                        />
                    </flux:button>
                @endif
            </div>
        </div>

        <flux:separator :text="__('Source translation values')" />

        <flux:field class="grid gap-4 md:grid-cols-2">
            <flux:callout
                icon="key"
                color="sky"
                :heading="__('admin.translation_list.modal_edit.translation_key')"
                :text="$editingTranslationKeyName ? : '—'"
            />

            <flux:callout
                icon="language"
                color="teal"
                :heading="__('Locales in scope')"
                :text="$editingTranslationLocales !== [] ? implode(', ', array_map('strtoupper', $editingTranslationLocales)) :
                    '—'"
            />
        </flux:field>

        <flux:field class="grid gap-4 md:grid-cols-2">
            @foreach (array_slice($editingTranslationLocales, 0, 2) as $readonlyLocale)
                <flux:callout
                    icon="language"
                    color="amber"
                    :heading="strtoupper($readonlyLocale)"
                    :text="trim((string)($translationEntryEditValues[$readonlyLocale] ?? '')) !== '' ?
                        $translationEntryEditValues[$readonlyLocale] : '—'"
                />
            @endforeach
        </flux:field>

        @if (count($editingTranslationLocales) > 2)
            <flux:separator :text="__('Edit locale specific translations')" />

            <flux:field class="grid gap-4 md:grid-cols-2">
                @foreach (array_slice($editingTranslationLocales, 2) as $editingLocale)
                    @php
                        $normalizedInputLocale = str_replace('_', '-', strtolower((string) $editingLocale));
                        $spellcheckLanguage = explode('-', $normalizedInputLocale)[0] ?: 'en';
                    @endphp

                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            <flux:text class="text-sm font-semibold uppercase text-zinc-700 dark:text-zinc-200">
                                {{ strtoupper($editingLocale) }}
                            </flux:text>

                            <x-ui.country.flag-locale
                                :locale="$editingLocale"
                                size="sm"
                                :title="strtoupper($editingLocale)"
                            />
                        </div>

                        <flux:textarea
                            wire:model.live="translationEntryEditValues.{{ $editingLocale }}"
                            :lang="$spellcheckLanguage"
                            spellcheck="true"
                            rows="2"
                        />
                    </div>
                @endforeach
            </flux:field>
        @endif

        <flux:separator />

        <div class="flex justify-end gap-3">
            <x-ui.button.cancel wire:click="closeTranslationEntryEditModal" />

            <x-ui.button.save
                :label="__('ui.actions.save_changes')"
                wire:click="saveTranslationEntryEdit"
                :disabled="!$this->translationEntryHasChanges"
            />
        </div>
    </div>
</flux:modal>
