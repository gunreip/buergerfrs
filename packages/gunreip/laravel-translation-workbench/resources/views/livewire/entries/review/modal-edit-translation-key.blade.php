{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-edit-translation-key.blade.php --}}

<flux:modal
    class="w-full max-w-[calc(100vw-2rem)] lg:max-w-[calc(100vw-4rem)]"
    name="translation-workbench-edit-translation-key"
    wire:model="translationKeyModalOpen"
>
    @if ($translationKeyFinding)
        @php
            $currentTranslationKey = trim((string) ($translationKeyFinding->translation_key ?? ''));
            $findingSuggestedKey = trim((string) ($translationKeyFinding->suggested_key ?? ''));
            $keySuggestedKey = trim((string) ($translationKeyFinding->key_suggested_key ?? ''));
            $effectiveSuggestedKey = $keySuggestedKey !== '' ? $keySuggestedKey : $findingSuggestedKey;
            $existingKey = trim((string) ($translationKeyFinding->existing_key ?? ''));
            $foundTranslationKey = trim((string) ($translationKeyFinding->found_translation_key ?? ''));
            $sourceAbsolutePath = str_replace('\\', '/', base_path($translationKeyFinding->source_path));
            $sourceEditorPath = str_replace('%2F', '/', rawurlencode($sourceAbsolutePath));
            $sourceEditorLine = $translationKeyFinding->source_line ? ':' . $translationKeyFinding->source_line : '';
            $sourceEditorUrl =
                'vscode://vscode-remote/wsl+' .
                rawurlencode((string) config('translation-workbench.editor.vscode_wsl_distro')) .
                $sourceEditorPath .
                $sourceEditorLine;
            $isUiConfirmed = (bool) ($translationKeyFinding->is_ui_key ?? false);
            $isUiCandidate =
                !$isUiConfirmed &&
                (($translationKeyFinding->reviewed_is_ui_candidate ?? null) !== null
                    ? (bool) $translationKeyFinding->reviewed_is_ui_candidate
                    : $translationKeyFinding->candidate_type === 'ui');
            $isDynamicConfirmed = (bool) ($translationKeyFinding->is_dynamic_key ?? false);
            $isDynamicCandidate =
                !$isDynamicConfirmed &&
                (($translationKeyFinding->reviewed_is_dynamic_candidate ?? null) !== null
                    ? (bool) $translationKeyFinding->reviewed_is_dynamic_candidate
                    : $translationKeyFinding->candidate_type === 'dynamic' ||
                        $translationKeyFinding->entry_type === 'dynamic' ||
                        $translationKeyFinding->kind === 'dynamic_multi');
            $isDynamicMulti =
                ($isDynamicConfirmed && (bool) ($translationKeyFinding->is_dynamic_multi ?? false)) ||
                ($isDynamicCandidate && (bool) ($translationKeyFinding->reviewed_is_dynamic_multi ?? false));
        @endphp

        <form
            class="space-y-5"
            wire:submit.prevent="saveTranslationKeyModal"
        >
            <div class="flex items-start justify-between gap-4">
                <div class="mb-2 min-w-0 space-y-1">
                    <div class="flex min-w-0 flex-wrap items-center gap-x-3 gap-y-1.5">
                        <flux:heading
                            size="xl"
                            level="3"
                        >
                            {{ __('Edit translation key') }}
                        </flux:heading>

                        <div class="flex flex-wrap gap-1.5">
                            <flux:badge
                                size="sm"
                                color="{{ $isUiConfirmed ? 'green' : ($isUiCandidate ? 'cyan' : 'zinc') }}"
                            >
                                {{ $isUiConfirmed ? __('Is UI') : ($isUiCandidate ? __('UI candidate') : __('No UI candidate')) }}
                            </flux:badge>

                            <flux:badge
                                size="sm"
                                color="{{ $isDynamicConfirmed ? 'green' : ($isDynamicCandidate ? 'violet' : 'zinc') }}"
                            >
                                {{ $isDynamicConfirmed ? __('Is dynamic') : ($isDynamicCandidate ? __('Dynamic candidate') : __('No dynamic candidate')) }}
                            </flux:badge>

                            @if ($isDynamicConfirmed || $isDynamicCandidate)
                                <flux:badge
                                    size="sm"
                                    color="{{ $isDynamicMulti ? 'green' : 'zinc' }}"
                                >
                                    {{ $isDynamicMulti ? __('Dynamic multi') : __('No dynamic multi') }}
                                </flux:badge>
                            @endif
                        </div>
                    </div>

                    <flux:text class="-mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Review the current key in context, adjust it deliberately, then save it explicitly.') }}
                    </flux:text>
                </div>

                <flux:badge
                    class="mr-8 mt-2 tabular-nums"
                    variant="subtle"
                >
                    #{{ $translationKeyFinding->id }}
                </flux:badge>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2 grid gap-3">
                    {{-- Callout Current Key --}}
                    <flux:callout
                        class="col-span-2"
                        color="{{ $currentTranslationKey !== '' ? 'green' : 'amber' }}"
                        icon="key-round"
                    >
                        <flux:callout.heading>
                            <span class="inline-flex items-center gap-1.5">
                                <span>{{ __('Current key') }}</span>
                                <flux:tooltip
                                    content="{{ __('The currently reviewed translation key stored on the workbench key record.') }}"
                                >
                                    <flux:icon.info class="size-3.5 text-zinc-400" />
                                </flux:tooltip>
                            </span>
                        </flux:callout.heading>
                        <flux:callout.text class="wrap-anywhere text-wrap font-mono text-xs">
                            <span class="font-mono text-zinc-400">{{ $currentTranslationKey ?: __('Missing') }}</span>
                        </flux:callout.text>
                        <flux:callout.heading>
                            <span class="inline-flex items-center gap-1.5">
                                <span>{{ __('Existing key') }}</span>
                                <flux:tooltip
                                    content="{{ __('A translation key that already exists in the source language files and may be reusable.') }}"
                                >
                                    <flux:icon.info class="size-3.5 text-zinc-400" />
                                </flux:tooltip>
                            </span>
                        </flux:callout.heading>
                        <flux:callout.text class="wrap-anywhere text-wrap font-mono text-xs">
                            <span class="text-zinc-400">{{ $existingKey ?: __('Missing') }}</span>
                        </flux:callout.text>
                        <flux:callout.heading>
                            <span class="inline-flex items-center gap-1.5">
                                <span>{{ __('Found translation key') }}</span>
                                <flux:tooltip
                                    content="{{ __('A translation key detected directly at the scanned code location, for example inside a translation call.') }}"
                                >
                                    <flux:icon.info class="size-3.5 text-zinc-400" />
                                </flux:tooltip>
                            </span>
                        </flux:callout.heading>
                        <flux:callout.text class="wrap-anywhere text-wrap font-mono text-xs">
                            <span class="text-zinc-400">{{ $foundTranslationKey ?: __('Missing') }}</span>
                        </flux:callout.text>
                        <flux:callout.heading>
                            <span class="flex w-full items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5">
                                    <span>{{ __('Suggested key') }}</span>
                                    <flux:tooltip
                                        content="{{ __('The deterministic key suggestion generated from the Workbench key rules and scan context.') }}"
                                    >
                                        <flux:icon.info class="size-3.5 text-zinc-400" />
                                    </flux:tooltip>
                                </span>

                                <span class="inline-flex items-center gap-1">
                                    <flux:tooltip content="{{ __('Copy suggested key into the input field') }}">
                                        <flux:button
                                            class="h-5 w-5 shrink-0"
                                            type="button"
                                            size="xs"
                                            variant="ghost"
                                            icon="copy-plus"
                                            icon:class="text-indigo-500 dark:text-indigo-400"
                                            :disabled="$effectiveSuggestedKey === ''"
                                            :aria-label="__('Copy suggested key into the input field')"
                                            wire:click="copySuggestedKeyToTranslationKeyModal"
                                        />
                                    </flux:tooltip>

                                    <flux:tooltip content="{{ __('Transform suggested key to UI translation key') }}">
                                        <flux:button
                                            class="h-5 w-5 shrink-0"
                                            type="button"
                                            size="xs"
                                            variant="ghost"
                                            icon="wand-sparkles"
                                            icon:class="text-cyan-500 dark:text-cyan-400"
                                            :disabled="$effectiveSuggestedKey === ''"
                                            :aria-label="__('Transform suggested key to UI translation key')"
                                            wire:click="transformSuggestedKeyToUiTranslationKeyModal"
                                        />
                                    </flux:tooltip>
                                </span>
                            </span>
                        </flux:callout.heading>
                        <flux:callout.text class="wrap-anywhere text-wrap font-mono text-xs">
                            <span class="text-zinc-400">{{ $effectiveSuggestedKey ?: __('Missing') }}</span>
                        </flux:callout.text>
                    </flux:callout>

                    {{-- Callout Source --}}
                    <flux:callout
                        class="col-span-2"
                        color="sky"
                        icon="sparkles"
                    >
                        <flux:callout.heading>
                            <span class="flex w-full items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5">
                                    <span>{{ __('Source') }}</span>
                                    <flux:tooltip
                                        content="{{ __('The scanned file path and line number where this finding was detected.') }}"
                                    >
                                        <flux:icon.info class="size-3.5 text-zinc-400" />
                                    </flux:tooltip>
                                </span>

                                <flux:tooltip content="{{ __('Open in VSC') }}">
                                    <flux:button
                                        class="h-5 w-5 shrink-0"
                                        type="button"
                                        size="xs"
                                        variant="ghost"
                                        icon="external-link"
                                        icon:class="text-sky-500 dark:text-sky-400"
                                        :href="$sourceEditorUrl"
                                        :aria-label="__('Open source in VS Code')"
                                    />
                                </flux:tooltip>
                            </span>
                        </flux:callout.heading>
                        <flux:callout.text class="wrap-anywhere text-wrap font-mono text-xs">
                            <span
                                class="text-zinc-400">{{ $translationKeyFinding->source_path }}:{{ $translationKeyFinding->source_line ?? 1 }}</span>
                        </flux:callout.text>
                    </flux:callout>
                </div>

                {{-- Callout Literal Segment Usage --}}
                <flux:callout
                    class="max-h-full overflow-y-auto"
                    color="teal"
                    icon="list-filter"
                >
                    <flux:callout.heading>
                        <span class="inline-flex items-center gap-1.5">
                            <span>{{ __('Literal segment usage') }}</span>
                            <flux:tooltip
                                content="{{ __('Counts how often the last key segment appears as translation, suggested, or existing key segment.') }}"
                            >
                                <flux:icon.info class="size-3.5 text-zinc-400" />
                            </flux:tooltip>
                        </span>
                    </flux:callout.heading>
                    <flux:callout.text class="col-span-2 text-xs">
                        {{ __('The last key segment of the translation key is used in the following translation, suggested, or existing keys.') }}
                    </flux:callout.text>
                    <flux:callout.text>
                        @if ($translationKeySegmentStats !== [])
                            <div class="space-y-3">
                                @foreach ($translationKeySegmentStats as $segmentStat)
                                    <div class="grid grid-cols-2 gap-x-3 gap-y-1.5">
                                        <flux:callout.heading>
                                            <span class="inline-flex items-center gap-1.5">
                                                <span>{{ __('Last segment key') }}</span>
                                                <flux:tooltip content="{{ $segmentStat['segment'] }}">
                                                    <flux:icon.info class="size-3.5 text-zinc-400" />
                                                </flux:tooltip>
                                            </span>
                                        </flux:callout.heading>
                                        <flux:callout.text class="text-xsm truncate text-ellipsis font-mono">
                                            <span class="font-mono text-zinc-400">{{ $segmentStat['segment'] }}</span>
                                        </flux:callout.text>
                                    </div>
                                    <div class="grid grid-cols-3 gap-x-3 gap-y-1.5">
                                        <flux:callout.heading class="col-span-2 align-bottom">
                                            {{ __('Distinct full keys') }}
                                        </flux:callout.heading>
                                        <flux:callout.text class="tabular-nums">
                                            <span
                                                class="text-zinc-400">{{ number_format($segmentStat['distinct_full_key_count']) }}</span>
                                        </flux:callout.text>

                                        <flux:callout.heading class="col-span-2">{{ __('Translation key') }}
                                        </flux:callout.heading>
                                        <flux:callout.text class="tabular-nums">
                                            <span
                                                class="text-zinc-400">{{ number_format($segmentStat['translation_key_count']) }}</span>
                                        </flux:callout.text>

                                        <flux:callout.heading class="col-span-2">{{ __('Suggested key') }}
                                        </flux:callout.heading>
                                        <flux:callout.text class="tabular-nums">
                                            <span
                                                class="text-zinc-400">{{ number_format($segmentStat['suggested_key_count']) }}</span>
                                        </flux:callout.text>

                                        <flux:callout.heading class="col-span-2">{{ __('Existing key') }}
                                        </flux:callout.heading>
                                        <flux:callout.text class="tabular-nums">
                                            <span
                                                class="text-zinc-400">{{ number_format($segmentStat['existing_key_count']) }}</span>
                                        </flux:callout.text>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-sm text-zinc-500">
                                {{ __('No key segment available.') }}
                            </span>
                        @endif
                    </flux:callout.text>
                </flux:callout>

                <div class="col-span-2">
                    {{-- Callout Translation Key Input --}}
                    <flux:callout
                        class="col-span-2"
                        color="red"
                        icon="file-text"
                    >
                        <flux:callout.heading>
                            <span class="inline-flex items-center gap-1.5">
                                <span>{{ __('Translation key') }}</span>
                                <flux:tooltip
                                    content="{{ __('The translation key to use for this finding. It will be saved to the workbench key record and can be used in the source code.') }}"
                                >
                                    <flux:icon.info class="size-3.5 text-zinc-400" />
                                </flux:tooltip>
                            </span>
                        </flux:callout.heading>
                        <flux:callout.text class="grid grid-cols-3">
                            <span class="text-xs">{{ __('Enter a translation key to use for this finding') }}</span>
                            <flux:button
                                class="item-start hover:cursor-pointer"
                                type="button"
                                size="xs"
                                variant="ghost"
                                icon="x"
                                icon:class="text-red-600 dark:text-red-300"
                                :disabled="!$translationKeySegmentControls['can_delete']"
                                :aria-label="__('Delete first segment')"
                                wire:click="deleteFirstTranslationKeySegmentModal"
                            >
                                {{ __('Delete') }}
                                {{ $translationKeySegmentControls['next_delete_segment'] ?: __('segment') }}
                            </flux:button>
                            <flux:button
                                class="item-start hover:cursor-pointer"
                                type="button"
                                size="xs"
                                variant="ghost"
                                icon="rotate-ccw"
                                icon:class="text-amber-600 dark:text-amber-300"
                                :disabled="!$translationKeySegmentControls['can_restore']"
                                :aria-label="__('Restore segment')"
                                wire:click="restoreFirstTranslationKeySegmentModal"
                            >
                                {{ __('Restore') }}
                                {{ $translationKeySegmentControls['next_restore_segment'] ?: __('segment') }}
                            </flux:button>
                        </flux:callout.text>
                        <flux:input.group>
                            <flux:input
                                class="font-mono"
                                wire:model.live="translationKeyValue"
                                placeholder="{{ __('Edit or copy a translation key') }}"
                            />
                        </flux:input.group>
                        <flux:error name="translationKeyValue" />
                    </flux:callout>
                </div>

                {{-- Callout Segments --}}
                <flux:callout
                    color="amber"
                    icon="list-minus"
                >
                    <flux:callout.heading>
                        <span class="inline-flex items-center gap-1.5">
                            <span>{{ __('Segments') }}</span>
                            <flux:tooltip
                                content="{{ __('Remove or restore leading translation key segments while the key has not been manually edited.') }}"
                            >
                                <flux:icon.info class="size-3.5 text-zinc-400" />
                            </flux:tooltip>
                        </span>
                    </flux:callout.heading>
                    <flux:callout.text class="space-y-3 text-xs">

                        @if ($translationKeyDeletedSegments !== [])
                            <div class="space-y-1">
                                <flux:callout.heading>{{ __('Deleted segments') }}</flux:callout.heading>
                                <flux:callout.text class="wrap-anywhere text-wrap font-mono text-xs">
                                    <span
                                        class="text-zinc-400">{{ implode('.', $translationKeyDeletedSegments) }}</span>
                                </flux:callout.text>
                            </div>
                        @else
                            <flux:text class="text-xs">
                                <span class="text-zinc-400">{{ __('No segment changes yet.') }}</span>
                            </flux:text>
                        @endif

                        @if ($translationKeySegmentControls['disable_segment_buttons'])
                            <flux:text class="text-xs text-amber-600 dark:text-amber-300">
                                {{ __('Segment actions are disabled because the translation key was edited manually or transformed.') }}
                            </flux:text>
                        @endif
                    </flux:callout.text>
                </flux:callout>
            </div>

            <div class="flex justify-end border-t border-zinc-200 pt-4 dark:border-zinc-700">
                <flux:button
                    type="submit"
                    variant="primary"
                    size="sm"
                    icon="save"
                    wire:loading.attr="disabled"
                    wire:target="saveTranslationKeyModal"
                >
                    {{ __('Save translation key') }}
                </flux:button>
            </div>
        </form>
    @else
        <flux:text class="text-sm text-zinc-500">
            {{ __('No finding selected.') }}
        </flux:text>
    @endif
</flux:modal>
