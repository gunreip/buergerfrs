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

                {{-- Badge with translation key ID --}}
                <flux:badge
                    class="mr-8 mt-2"
                    variant="subtle"
                    color="amber"
                >
                    #{{ $editingTranslationKey->id }}
                </flux:badge>
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
                    <flux:heading>
                        {{ __('Key') }}
                    </flux:heading>

                    <flux:text class="text-sm">
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
            <flux:callout
                color="cyan"
                icon="megaphone"
                stroke-width="1"
            >
                <flux:callout.heading>
                    {{ __('Native text') }}
                </flux:callout.heading>
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

                            {{-- @if (!blank($usage->raw))
                                <div class="mt-2">
                                    <div
                                        class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                        {{ __('Raw') }}
                                    </div>

                                    <code class="wrap-anywhere whitespace-normal text-xs">
                                        {{ $usage->raw }}
                                    </code>
                                </div>
                            @endif --}}
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
                    @foreach ($translationLanguages as $translationLanguage)
                        @php
                            $translationValue = $editingTranslationKey->values->firstWhere(
                                'locale',
                                $translationLanguage->locale,
                            );
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
                                    <x-ui.badge.context
                                        context="translation.value.status"
                                        :value="$translationValue->status"
                                        size="sm"
                                    />
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
