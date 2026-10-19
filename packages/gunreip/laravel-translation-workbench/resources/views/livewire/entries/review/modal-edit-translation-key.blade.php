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
            $findingCaptureMeta = json_decode($translationKeyFinding->finding_meta ?? '{}', true) ?? [];
            $existingKey = trim((string) ($translationKeyFinding->existing_key ?? ''));
            $foundTranslationKey = trim((string) ($translationKeyFinding->found_translation_key ?? ''));
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
            $isDynamicContext = $isDynamicConfirmed || $isDynamicCandidate || $isDynamicMulti;
            $langNodeType = trim((string) ($translationKeyFinding->lang_node_type ?? 'unknown')) ?: 'unknown';
            $langNodeColor = match ($langNodeType) {
                'leaf' => 'green',
                'container' => 'sky',
                'conflict' => 'red',
                default => 'zinc',
            };
            $langNodeLabel = match ($langNodeType) {
                'leaf' => __('Leaf key'),
                'container' => __('Container key'),
                'conflict' => __('Key conflict'),
                default => __('Unknown key shape'),
            };
            $langNodeText = match ($langNodeType) {
                'leaf' => __(
                    'This translation key currently resolves to a scalar translation value in the lang files.',
                ),
                'container' => __(
                    'This translation key is currently used as a container path for nested translation values.',
                ),
                'conflict' => __(
                    'This translation key is both a scalar value and a container path. It needs review before exporting safely.',
                ),
                default => __('The Workbench has not classified this translation key against the lang-file tree yet.'),
            };
            $translationKeyCandidate = trim((string) ($translationKeyValue ?? ''));
            $translationKeyAlreadySaved = $translationKeyCandidate !== ''
                && $translationKeyCandidate === $currentTranslationKey
                && ($translationKeyFinding->review_status ?? null) === 'reviewed';
            $translationKeyIsValid =
                preg_match('/^[a-z0-9][a-z0-9_-]*(\.[a-z0-9][a-z0-9_-]*)+$/', $translationKeyCandidate) === 1;
            $translationKeyCandidateNodeReview = $translationKeyCandidateReview ?? [];
            $translationKeyCandidateBlocked = (bool) ($translationKeyCandidateNodeReview['is_blocked'] ?? false);
            $translationKeyCandidateProposedLeafKey = trim(
                (string) ($translationKeyCandidateNodeReview['proposed_leaf_key'] ?? ''),
            );
            $translationKeyCandidateChildKeys = $translationKeyCandidateNodeReview['child_keys'] ?? [];
            $translationKeyCandidateChildKeyRows = $translationKeyCandidateNodeReview['child_key_rows'] ?? [];
            $translationKeySegments = array_values(array_filter(explode('.', trim($translationKeyCandidate, '.'))));
            $translationKeyLastSegment =
                (string) ($translationKeySegments[array_key_last($translationKeySegments)] ?? '');
            $translationKeyHasHashSuffix =
                preg_match('/^[a-f0-9]{8,64}$/', $translationKeyLastSegment) === 1 ||
                preg_match('/[_-][a-f0-9]{8,64}$/', $translationKeyLastSegment) === 1;
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
                                {{ $isUiConfirmed ? __('Is UI') : ($isUiCandidate ? __('ui.ui.ui-candidate') : __('No UI candidate')) }}
                            </flux:badge>

                            <flux:badge
                                size="sm"
                                color="{{ $isDynamicConfirmed ? 'green' : ($isDynamicCandidate ? 'violet' : 'zinc') }}"
                            >
                                {{ $isDynamicConfirmed ? __('Dynamic values') : ($isDynamicCandidate ? __('Dynamic values candidate') : __('No dynamic values')) }}
                            </flux:badge>

                            @if ($isDynamicConfirmed || $isDynamicCandidate)
                                <flux:badge
                                    size="sm"
                                    color="{{ $isDynamicMulti ? 'green' : 'zinc' }}"
                                >
                                    {{ $isDynamicMulti ? __('Dynamic values') : __('Dynamic values pending') }}
                                </flux:badge>
                            @endif

                            <x-ui.tooltip.simple
                                :header="$langNodeLabel"
                                :text="$langNodeText"
                            >
                                <flux:badge
                                    size="sm"
                                    color="{{ $langNodeColor }}"
                                >
                                    {{ $langNodeLabel }}
                                </flux:badge>
                            </x-ui.tooltip.simple>
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

            @include('translation-workbench::livewire.entries.review.duplicate-key-warning', [
                'ownerCount' => count($translationKeyCandidateNodeReview['active_owner_ids'] ?? []),
                'ownerIds' => $translationKeyCandidateNodeReview['active_owner_ids'] ?? [],
            ])
            @if (!$isDynamicMulti && $langNodeType !== 'container')
                @include('translation-workbench::livewire.entries.review.root-scalar-warning', [
                    'structureKey' => $translationKeyCandidate,
                ])
            @endif

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2 grid gap-3">
                    {{-- Callout Current Key --}}
                    <flux:callout
                        class="col-span-2"
                        color="{{ $currentTranslationKey !== '' ? 'green' : 'amber' }}"
                        icon="key-round"
                    >
                        <flux:callout.heading>
                            {{ __('Key context') }}
                        </flux:callout.heading>
                        <flux:accordion transition>
                            <flux:accordion.item>
                                <flux:accordion.heading>
                                    <flux:callout.heading>
                                        <flux:icon.panel-top-open
                                            class="group-data-open/accordion-heading:hidden"
                                            aria-hidden="true"
                                        />
                                        <flux:icon.panel-top-close
                                            class="group-data-open/accordion-heading:block hidden"
                                            aria-hidden="true"
                                        />
                                        {{ __('Current, existing, found and suggested keys') }}
                                    </flux:callout.heading>
                                </flux:accordion.heading>
                                <flux:accordion.content>
                                    {{-- Heading Current Key --}}
                                    <flux:callout.heading>
                                        <span class="inline-flex items-center gap-1.5">
                                            <span>{{ __('Current key') }}</span>
                                            <x-ui.tooltip.simple
                                                :header="__('Current key')"
                                                :text="__(
                                                    'The translation key currently saved on the Workbench key record. Unsaved input below is not included. Saving a key does not by itself update source code or publish lang files.',
                                                )"
                                            />
                                        </span>
                                    </flux:callout.heading>
                                    {{-- Text Current Key --}}
                                    <flux:callout.text class="wrap-anywhere mb-1 text-wrap font-mono text-xs">
                                        {{ $currentTranslationKey ?: __('ui.state.missing') }}
                                    </flux:callout.text>

                                    {{-- Heading Existing Key --}}
                                    <flux:callout.heading>
                                        <span class="inline-flex items-center gap-1.5">
                                            <span>{{ __('Existing key') }}</span>
                                            <x-ui.tooltip.simple
                                                :header="__('Existing key')"
                                                :text="__(
                                                    'The existing key recorded in the finding by the scanner. This is stored scan context, not a live lookup of lang files. Saving or publishing the Current key does not automatically synchronize this field. Empty means no existing key was detected.',
                                                )"
                                            />
                                        </span>
                                    </flux:callout.heading>
                                    {{-- Text Existing Key --}}
                                    <flux:callout.text class="wrap-anywhere mb-1 text-wrap font-mono text-xs">
                                        {{ $existingKey ?: __('No existing key') }}
                                    </flux:callout.text>

                                    {{-- Heading Found Translation Key --}}
                                    <flux:callout.heading>
                                        <span class="inline-flex items-center gap-1.5">
                                            <span>{{ __('Found translation key') }}</span>
                                            <x-ui.tooltip.simple
                                                :header="__('Found translation key')"
                                                :text="__(
                                                    'The translation key detected at this source location and stored in the finding, for example from a translation call. It describes the recorded scan state, which can differ from the Current key after editing.',
                                                )"
                                            />
                                        </span>
                                    </flux:callout.heading>
                                    {{-- Text Found Translation Key --}}
                                    <flux:callout.text class="wrap-anywhere mb-1 text-wrap font-mono text-xs">
                                        {{ $foundTranslationKey ?: __('ui.state.missing') }}
                                    </flux:callout.text>

                                    {{-- Heading Suggested Key --}}
                                    <flux:callout.heading>
                                        <span class="flex w-full items-center justify-between gap-2">
                                            <span class="inline-flex items-center gap-1.5">
                                                <span>{{ __('ui.key.suggested-key') }}</span>
                                                <x-ui.tooltip.simple
                                                    :header="__('ui.key.suggested-key')"
                                                    :text="__(
                                                        'The original suggestion stored when this finding was first captured. Later scans, key edits and changes to its key assignment do not replace it. This value belongs to the finding, not to the linked key record.',
                                                    )"
                                                />
                                            </span>

                                            <span class="inline-flex items-center gap-1">
                                                <flux:tooltip
                                                    content="{{ __('Copy suggested key into the input field') }}"
                                                >
                                                    <flux:button
                                                        class="h-5 w-5 shrink-0"
                                                        type="button"
                                                        size="xs"
                                                        variant="ghost"
                                                        icon="copy-plus"
                                                        icon:class="text-indigo-500 dark:text-indigo-400"
                                                        :disabled="$findingSuggestedKey === ''"
                                                        :aria-label="__('Copy suggested key into the input field')"
                                                        wire:click="copySuggestedKeyToTranslationKeyModal"
                                                    />
                                                </flux:tooltip>

                                                <flux:tooltip
                                                    content="{{ $isDynamicContext
                                                        ? __('Dynamic translation keys cannot be transformed into UI keys.')
                                                        : __('ui.translation.transform-suggested-key-to-ui-translation-key') }}"
                                                >
                                                    <flux:button
                                                        class="h-5 w-5 shrink-0"
                                                        type="button"
                                                        size="xs"
                                                        variant="ghost"
                                                        icon="wand-sparkles"
                                                        icon:class="text-cyan-500 dark:text-cyan-400"
                                                        :disabled="$findingSuggestedKey === '' || $isDynamicContext"
                                                        :aria-label="__('ui.translation.transform-suggested-key-to-ui-translation-key')"
                                                        wire:click="transformSuggestedKeyToUiTranslationKeyModal"
                                                    />
                                                </flux:tooltip>
                                            </span>
                                        </span>
                                    </flux:callout.heading>
                                    {{-- Text Suggested Key --}}
                                    <flux:callout.text class="wrap-anywhere text-wrap font-mono text-xs">
                                        {{ $findingSuggestedKey ?: __('ui.state.missing') }}
                                    </flux:callout.text>
                                    @if (($findingCaptureMeta['suggested_key_source'] ?? null) === 'existing_key_at_source')
                                        <flux:callout.text>{{ __('Already present in source at initial capture; no new key was generated from a literal.') }}</flux:callout.text>
                                    @elseif (($findingCaptureMeta['suggested_key_source'] ?? null) === 'literal')
                                        <flux:callout.text>{{ __('Generated from the literal and its source context at initial capture.') }}</flux:callout.text>
                                    @endif
                                </flux:accordion.content>
                            </flux:accordion.item>
                        </flux:accordion>
                    </flux:callout>

                    @include('translation-workbench::livewire.entries.review.key-sources')

                    {{-- Callout Translation Key Input --}}
                    <flux:callout
                        class="col-span-2"
                        color="{{ $translationKeyIsValid && !$translationKeyCandidateBlocked ? 'green' : 'red' }}"
                        icon="file-text"
                    >
                        <flux:callout.heading>
                            <span class="flex w-full items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5">
                                    <span>{{ __('ui.translation.translation-key') }}</span>
                                    <x-ui.tooltip.simple
                                        :header="__('ui.translation.translation-key')"
                                        :text="__(
                                            'The translation key to use for this finding. It will be saved to the workbench key record and can be used in the source code.',
                                        )"
                                    />
                                </span>

                                <span></span>
                            </span>
                        </flux:callout.heading>
                        <flux:callout.text class="grid grid-cols-4 gap-2">
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
                            @if ($translationKeyHasHashSuffix)
                                <flux:button
                                    class="item-start hover:cursor-pointer"
                                    type="button"
                                    size="xs"
                                    variant="ghost"
                                    icon="hash"
                                    icon:class="text-amber-600 dark:text-amber-300"
                                    :aria-label="__('ui.remove.remove.remove-hash-suffix')"
                                    wire:click="removeTranslationKeyHashSuffixModal"
                                >
                                    {{ __('ui.remove.remove.remove-hash-suffix') }}
                                </flux:button>
                            @endif
                        </flux:callout.text>
                        @if ($translationKeyCandidateBlocked)
                            <flux:callout
                                class="my-3"
                                color="red"
                                icon="triangle-alert"
                            >
                                <flux:callout.heading>
                                    <span class="inline-flex items-center gap-1.5">
                                        <span>{{ __('Scalar/container conflict') }}</span>
                                        <x-ui.tooltip.simple
                                            :header="__('Scalar/container conflict')"
                                            :text="__(
                                                'The edited translation key is currently a container path because active child keys or lang values exist below it. It cannot be saved as a scalar translation key; add a final leaf segment first.',
                                            )"
                                        />
                                    </span>
                                </flux:callout.heading>
                                <flux:callout.text class="space-y-2">
                                    <flux:text class="text-xs text-red-700 dark:text-red-300">
                                        {{ __('The edited translation key is a scalar entry candidate, but the lang tree already contains nested entries below this path. Saving it would recreate the array/scalar conflict.') }}
                                    </flux:text>

                                    @if ($translationKeyCandidateProposedLeafKey !== '')
                                        <div class="flex flex-wrap items-center gap-2">
                                            <flux:badge
                                                class="wrap-anywhere max-w-full text-wrap font-mono"
                                                size="sm"
                                                color="amber"
                                            >
                                                {{ $translationKeyCandidateProposedLeafKey }}
                                            </flux:badge>

                                            <flux:button
                                                type="button"
                                                size="xs"
                                                variant="primary"
                                                color="sky"
                                                icon="copy-plus"
                                                wire:click="useProposedLeafTranslationKeyModal"
                                            >
                                                {{ __('Use proposed leaf key') }}
                                            </flux:button>
                                        </div>
                                    @endif

                                    @if ($translationKeyCandidateChildKeyRows !== [])
                                        <flux:accordion>
                                            <flux:accordion.item>
                                                <flux:accordion.heading>
                                                    {{ __('Blocking child keys') }}
                                                </flux:accordion.heading>
                                                <flux:accordion.content>
                                                    <div
                                                        class="grid max-h-40 grid-cols-2 gap-x-3 gap-y-2 overflow-y-auto pr-1">
                                                        <flux:callout.heading>
                                                            {{ __('ui.translation.translation-key') }}
                                                        </flux:callout.heading>
                                                        <flux:callout.heading>
                                                            {{ __('Translation values') }}
                                                        </flux:callout.heading>

                                                        @foreach ($translationKeyCandidateChildKeyRows as $childKeyRow)
                                                            <flux:callout.text
                                                                class="wrap-anywhere truncate text-wrap font-mono"
                                                            >
                                                                {{ $childKeyRow['translation_key'] ?? '' }}
                                                            </flux:callout.text>
                                                            <div class="grid grid-cols-2 space-y-1">
                                                                @forelse (($childKeyRow['values'] ?? []) as $childKeyValue)
                                                                    <flux:callout.text class="wrap-anywhere text-wrap">
                                                                        <span
                                                                            class="inline-flex items-center gap-1.5 truncate text-ellipsis"
                                                                        >
                                                                            <x-ui.locale.flag
                                                                                class="size-3.5"
                                                                                :locale="$childKeyValue['locale'] ??
                                                                                    ''"
                                                                            />
                                                                            <x-ui.tooltip.simple :text="$childKeyValue['value'] ?? ''" />
                                                                            {{ $childKeyValue['value'] ?? '' }}
                                                                        </span>
                                                                    </flux:callout.text>
                                                                @empty
                                                                    <flux:callout.text class="col-span-2 text-xs">
                                                                        {{ __('No active translation value found.') }}
                                                                    </flux:callout.text>
                                                                @endforelse
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </flux:accordion.content>
                                            </flux:accordion.item>
                                        </flux:accordion>
                                    @endif
                                </flux:callout.text>
                            </flux:callout>
                        @endif
                        <flux:input.group>
                            <flux:input
                                class="font-mono dark:bg-zinc-800"
                                autocomplete="off"
                                wire:model.live="translationKeyValue"
                                placeholder="{{ __('Edit or copy a translation key') }}"
                                :invalid="!$translationKeyIsValid || $translationKeyCandidateBlocked"
                            />
                            <flux:button
                                type="submit"
                                variant="outline"
                                :icon="$translationKeyAlreadySaved ? 'save-check' : 'save-plus'"
                                icon:variant="outline"
                                :aria-label="__('Save translation key')"
                                :tooltip="$translationKeyAlreadySaved
                                    ? __('This translation key is already saved and reviewed.')
                                    : __('Save and review the entered translation key.')"
                                :disabled="!$translationKeyIsValid || $translationKeyCandidateBlocked"
                                wire:loading.attr="disabled"
                                wire:target="saveTranslationKeyModal"
                            />
                        </flux:input.group>

                        <flux:error name="translationKeyValue" />
                    </flux:callout>

                    {{-- Callout Segments --}}
                    <flux:callout
                        class="col-span-2"
                        color="amber"
                        icon="list-minus"
                    >
                        <flux:callout.heading>
                            <span class="inline-flex items-center gap-1.5">
                                <span>{{ __('Segments') }}</span>
                                <x-ui.tooltip.simple
                                    :header="__('Segments')"
                                    :text="__(
                                        'ui.remove.remove.remove-or-restore-leading-translation-key-segments-while-the-key-has-not-been-manually-edited',
                                    )"
                                />
                            </span>
                        </flux:callout.heading>
                        <flux:callout.text class="space-y-3 text-xs">

                            @if ($translationKeyDeletedSegments !== [])
                                <div class="space-y-1">
                                    <flux:callout.heading>
                                        {{ __('Deleted segments') }}
                                    </flux:callout.heading>
                                    <flux:callout.text class="wrap-anywhere text-wrap font-mono text-xs">
                                        {{ implode('.', $translationKeyDeletedSegments) }}
                                    </flux:callout.text>
                                </div>
                            @else
                                <flux:callout.text class="text-xs">
                                    {{ __('No segment changes yet.') }}
                                </flux:callout.text>
                            @endif

                            @if ($translationKeySegmentControls['disable_segment_buttons'])
                                <flux:text class="text-xs text-amber-600 dark:text-amber-300">
                                    {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_edit_translation_key.segment_actions_are_disabled_because_the_translation_key_was_edited_manually_or_transformed') }}
                                </flux:text>
                            @endif
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
                            <x-ui.tooltip.simple
                                :header="__('Literal segment usage')"
                                :text="__(
                                    'Counts how often the last key segment appears as translation, suggested, or existing key segment.',
                                )"
                            />
                        </span>
                    </flux:callout.heading>
                    <flux:callout.text class="col-span-2">
                        {{ __('The last key segment of the translation key is used in the following translation, suggested, or existing keys.') }}
                    </flux:callout.text>

                    <flux:callout.text>
                        @if ($translationKeySegmentStats !== [])
                            <flux:accordion
                                transition
                                exclusive
                            >
                                @foreach ($translationKeySegmentStats as $segmentStat)
                                    @php
                                        $sectionLabel = match ($segmentStat['label']) {
                                            __('Current key') => __('ui.translation.translation-key'),
                                            __('ui.key.suggested-key') => __('ui.key.suggested-key'),
                                            default => $segmentStat['label'],
                                        };
                                    @endphp

                                    <flux:accordion.item :expanded="$loop->first">
                                        <flux:accordion.heading>
                                            <flux:callout.heading>
                                                <flux:icon.panel-top-open
                                                    class="group-data-open/accordion-heading:hidden"
                                                    aria-hidden="true"
                                                />
                                                <flux:icon.panel-top-close
                                                    class="group-data-open/accordion-heading:block hidden"
                                                    aria-hidden="true"
                                                />
                                                {{ $sectionLabel }}
                                            </flux:callout.heading>
                                        </flux:accordion.heading>
                                        <flux:accordion.content>
                                            <div class="grid grid-cols-3 gap-x-3 gap-y-1.5">
                                                {{-- Text Last Segment Key --}}
                                                <flux:callout.text class="col-span-2 text-xs">
                                                    <span class="inline-flex items-center gap-1.5">
                                                        <span>{{ __('Last segment key') }}</span>
                                                        <flux:tooltip content="{{ $segmentStat['segment'] }}">
                                                            <flux:icon.info class="size-3.5 text-zinc-400" />
                                                        </flux:tooltip>
                                                    </span>
                                                </flux:callout.text>
                                                <flux:callout.text class="text-xsm truncate text-ellipsis font-mono">
                                                    {{ $segmentStat['segment'] }}
                                                </flux:callout.text>

                                                {{-- Text Distinct Full Keys --}}
                                                <flux:callout.text class="col-span-2 align-bottom text-xs">
                                                    {{ __('Distinct full keys') }}
                                                </flux:callout.text>
                                                <flux:callout.text class="text-xs tabular-nums">
                                                    {{ number_format($segmentStat['distinct_full_key_count']) }}
                                                </flux:callout.text>

                                                {{-- Text Translation Key --}}
                                                <flux:callout.text class="col-span-2 text-xs">
                                                    {{ __('ui.translation.translation-key') }}
                                                </flux:callout.text>
                                                <flux:callout.text class="text-xs tabular-nums">
                                                    {{ number_format($segmentStat['translation_key_count']) }}
                                                </flux:callout.text>

                                                {{-- Text Suggested Key --}}
                                                <flux:callout.text class="col-span-2 text-xs">
                                                    {{ __('ui.key.suggested-key') }}
                                                </flux:callout.text>
                                                <flux:callout.text class="text-xs tabular-nums">
                                                    {{ number_format($segmentStat['suggested_key_count']) }}
                                                </flux:callout.text>

                                                {{-- Text Existing Key --}}
                                                <flux:callout.text class="col-span-2 text-xs">
                                                    {{ __('Existing key') }}
                                                </flux:callout.text>
                                                <flux:callout.text class="text-xs tabular-nums">
                                                    {{ number_format($segmentStat['existing_key_count']) }}
                                                </flux:callout.text>
                                            </div>
                                        </flux:accordion.content>
                                    </flux:accordion.item>
                                @endforeach
                            </flux:accordion>
                        @else
                            <span class="text-sm text-zinc-500">
                                {{ __('No key segment available.') }}
                            </span>
                        @endif
                    </flux:callout.text>
                </flux:callout>
            </div>

            {{-- Key inspection and explicit finding assignment --}}
            <div class="grid grid-cols-1 items-stretch gap-3 xl:grid-cols-3">
                <flux:callout
                    class="min-w-0 xl:col-span-2"
                    color="amber"
                    icon="information-circle"
                >
                    <flux:accordion transition>
                        <flux:accordion.item wire:model.live="keyConflictInspectionOpen">
                            <flux:accordion.heading>
                                <flux:callout.heading>
                                    <flux:icon.panel-top-open
                                        class="group-data-open/accordion-heading:hidden"
                                        aria-hidden="true"
                                    />
                                    <flux:icon.panel-top-close
                                        class="group-data-open/accordion-heading:block hidden"
                                        aria-hidden="true"
                                    />
                                    {{ __('Key assignments and stored values') }}
                                </flux:callout.heading>
                            </flux:accordion.heading>
                            <flux:accordion.content>
                                <div
                                    class="items-center gap-2 py-4"
                                    role="status"
                                    aria-live="polite"
                                    wire:loading.flex
                                    wire:target="keyConflictInspectionOpen"
                                >
                                    <flux:icon.loading
                                        class="size-5 text-sky-600 dark:text-sky-400"
                                        aria-hidden="true"
                                    />
                                    <flux:text>{{ __('Loading key assignments and stored values…') }}</flux:text>
                                </div>
                                @include('translation-workbench::livewire.entries.review.key-conflict-inspection')
                            </flux:accordion.content>
                        </flux:accordion.item>
                    </flux:accordion>
                </flux:callout>

                @include('translation-workbench::livewire.entries.review.key-finding-assignment')

            </div>

        </form>
    @else
        <flux:text class="text-sm text-zinc-500">
            {{ __('No finding selected.') }}
        </flux:text>
    @endif
</flux:modal>
