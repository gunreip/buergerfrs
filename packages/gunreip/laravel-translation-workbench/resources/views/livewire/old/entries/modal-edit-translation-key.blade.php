{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-edit-translation-key.blade.php --}}

<flux:modal
    class="w-full max-w-[calc(100vw-2rem)] lg:max-w-[calc(100vw-4rem)]"
    id="translation-workbench-entry-edit-translation-key"
    name="translation-workbench-entry-edit-translation-key"
    wire:model="translationKeyModalOpen"
>
    @if ($entry)
        @php
            $currentTranslationKeyValue = trim((string) ($translationKeyValue ?? ($entry->translation_key ?? '')));
            $translationKeySegments = array_values(array_filter(explode('.', $currentTranslationKeyValue)));
            $firstTranslationKeySegment = $translationKeySegments[0] ?? null;
            $canDeleteFirstTranslationKeySegment = count($translationKeySegments) > 1;
            $deletedSegments = collect((array) ($entry->deleted_segments ?? []))
                ->filter(static fn(mixed $segment): bool => is_array($segment) || filled($segment))
                ->values();
            $lastDeletedSegmentEntry = $deletedSegments->last();
            $lastDeletedSegment = is_array($lastDeletedSegmentEntry)
                ? $lastDeletedSegmentEntry['segment'] ?? null
                : $lastDeletedSegmentEntry;
        @endphp

        <form
            class="space-y-6"
            wire:submit.prevent="saveTranslationKeyModal"
        >
            <div class="flex items-start justify-between gap-4">
                <x-ui.headers.card
                    :title="__('Edit translation key')"
                    :description="__('Set or update the translation key for this workbench entry.')"
                />

                <flux:badge
                    class="mr-8 mt-2 tabular-nums"
                    variant="subtle"
                    color="zinc"
                >
                    #{{ $entry->id }}
                </flux:badge>
            </div>

            <flux:field>
                <flux:label>{{ __('Translation key') }}</flux:label>
                <flux:input.group>
                    <flux:input
                        wire:model.live="translationKeyValue"
                        placeholder="management.people.example.key"
                    />
                    <flux:input.group.suffix>
                        <flux:button
                            type="button"
                            size="xs"
                            variant="ghost"
                            icon="copy-plus"
                            :disabled="blank($entry->suggested_key)"
                            :aria-label="__('Use suggested key')"
                            wire:click="copySuggestedKeyToTranslationKeyModal"
                        />
                    </flux:input.group.suffix>
                </flux:input.group>
                <flux:error name="translationKeyValue" />
            </flux:field>

            <div class="flex flex-wrap items-center gap-2">
                <flux:button
                    type="button"
                    size="xs"
                    variant="ghost"
                    icon="x"
                    icon:class="text-red-700 dark:text-red-300"
                    :disabled="!$canDeleteFirstTranslationKeySegment"
                    wire:click="deleteFirstTranslationKeySegment"
                    wire:loading.attr="disabled"
                    wire:target="deleteFirstTranslationKeySegment"
                >
                    {{ __('Delete') }} {{ $firstTranslationKeySegment ?: __('segment') }}
                </flux:button>

                @if (filled($entry->deleted_segments))
                    <flux:button
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="rotate-ccw"
                        icon:class="text-amber-700 dark:text-amber-300"
                        wire:click="restoreLastDeletedTranslationKeySegment"
                        wire:loading.attr="disabled"
                        wire:target="restoreLastDeletedTranslationKeySegment"
                    >
                        {{ __('Restore') }} {{ $lastDeletedSegment ?: __('segment') }}
                    </flux:button>

                    <flux:badge
                        size="sm"
                        color="amber"
                    >
                        {{ __('Deleted segments') }} {{ count((array) $entry->deleted_segments) }}
                    </flux:badge>
                @endif
            </div>

            @if ($deletedSegments->isNotEmpty())
                <div
                    class="space-y-2 rounded border border-amber-200 bg-amber-50/60 p-3 dark:border-amber-500/30 dark:bg-amber-500/10">
                    <div class="text-xs font-medium text-amber-700 dark:text-amber-200">
                        {{ __('Deleted translation key segments') }}
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($deletedSegments as $deletedSegment)
                            @php
                                $segmentName = is_array($deletedSegment)
                                    ? $deletedSegment['segment'] ?? null
                                    : $deletedSegment;
                            @endphp

                            <flux:badge
                                size="sm"
                                color="amber"
                            >
                                {{ $segmentName ?: __('segment') }}
                            </flux:badge>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        {{ __('Suggested key') }}
                    </div>
                    <div class="break-all font-mono text-xs">
                        {{ $entry->suggested_key ?: '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        {{ __('Current translation key') }}
                    </div>
                    <div class="break-all font-mono text-xs">
                        {{ $entry->translation_key ?: '—' }}
                    </div>
                </div>
            </div>

            {{-- <div class="flex justify-end gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                <flux:button
                    type="button"
                    variant="ghost"
                    wire:click="closeTranslationKeyModal"
                >
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button
                    type="submit"
                    variant="primary"
                    icon="save"
                    wire:loading.attr="disabled"
                    wire:target="saveTranslationKeyModal"
                >
                    {{ __('Save') }}
                </flux:button>
            </div> --}}
        </form>
    @endif
</flux:modal>
