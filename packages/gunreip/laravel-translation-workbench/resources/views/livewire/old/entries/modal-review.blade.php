{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-review.blade.php --}}

<flux:modal
    class="w-full max-w-[calc(100vw-2rem)] lg:max-w-[calc(100vw-4rem)]"
    id="translation-workbench-entry-review"
    name="translation-workbench-entry-review"
    wire:model="reviewModalOpen"
>
    @if ($entry)
        @php
            $isUiConfirmed = (bool) $entry->is_ui_key && !(bool) $entry->is_dynamic_key;
            $isUiRejected = (bool) $entry->is_ui_candidate_rejected && !$isUiConfirmed;
            $isDynamicConfirmed = (bool) $entry->is_dynamic_key;
            $isDynamicRejected = (bool) $entry->is_dynamic_candidate_rejected && !$isDynamicConfirmed;
            $isDynamicMultiConfirmed = $isDynamicConfirmed && (bool) $entry->is_dynamic_multi;
            $isUiCandidateVisible =
                $entry->candidate_type === 'ui' && !$isUiConfirmed && !$isUiRejected && !$isDynamicConfirmed;
            $isDynamicCandidateVisible =
                ($entry->candidate_type === 'dynamic' || $entry->entry_type === 'dynamic') &&
                !$isDynamicConfirmed &&
                !$isDynamicRejected &&
                !$isUiConfirmed;
        @endphp

        <div class="flex max-h-[calc(100vh-8rem)] flex-col gap-6 overflow-hidden">
            <div class="flex shrink-0 items-start justify-between gap-4">
                <x-ui.headers.card
                    :title="__('Review')"
                    :description="__('Review the selected translation workbench entry and its collected scan metadata.')"
                />

                <div class="mr-8 mt-2 flex items-center gap-2">
                    {{-- Badge ID --}}
                    <flux:badge
                        class="tabular-nums"
                        variant="subtle"
                        color="zinc"
                    >
                        #{{ $entry->id }}
                    </flux:badge>

                    {{-- Open Next Review Modal Button --}}
                    <flux:button
                        class="h-7 w-7"
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="chevron-right"
                        :disabled="$nextReviewEntryId === null"
                        :aria-label="__('Next review entry')"
                        wire:click="openReviewModal({{ $nextReviewEntryId ?? 0 }})"
                    />

                    {{-- Open Edit Modal Button --}}
                    <flux:button
                        class="h-7 w-7"
                        type="button"
                        title="{{ blank($entry->translation_key) ? __('Set a translation key before editing translation values.') : __('Edit') }}"
                        size="xs"
                        variant="ghost"
                        icon="pencil"
                        :disabled="blank($entry->translation_key)"
                        :aria-label="__('Edit')"
                        wire:click="openEditModal({{ $entry->id }})"
                    />

                </div>
            </div>

            <div class="min-h-0 overflow-y-auto pr-2">
                <div class="grid grid-cols-10 gap-3 md:grid-cols-10">
                    {{-- Callout Type --}}
                    <flux:callout
                        class="col-span-4"
                        color="sky"
                        icon="tag"
                    >
                        {{-- Callout Type --}}
                        <flux:callout.heading>{{ __('Type') }}</flux:callout.heading>
                        <flux:callout.text class="col-span-2 space-y-2">
                            <div class="flex flex-wrap gap-1.5">
                                <flux:badge
                                    size="sm"
                                    color="sky"
                                >
                                    {{ __('Kind') }}: {{ str($entry->kind)->headline() }}
                                </flux:badge>
                                @if ($entry->entry_type)
                                    <flux:badge
                                        size="sm"
                                        color="zinc"
                                    >
                                        {{ __('Entry') }}: {{ str($entry->entry_type)->headline() }}
                                    </flux:badge>
                                @endif
                                @if ($isUiCandidateVisible)
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('Candidate') }}: {{ __('UI') }}
                                    </flux:badge>
                                @endif
                                @if ($isDynamicCandidateVisible)
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('Candidate') }}: {{ __('Dynamic') }}
                                    </flux:badge>
                                @endif
                                @if ($isUiConfirmed)
                                    <flux:badge
                                        size="sm"
                                        color="emerald"
                                    >
                                        {{ __('Is UI') }}
                                    </flux:badge>
                                @endif
                                @if ($isUiRejected)
                                    <flux:badge
                                        size="sm"
                                        color="rose"
                                    >
                                        {{ __('No UI') }}
                                    </flux:badge>
                                @endif
                                @if ($isDynamicConfirmed)
                                    <flux:badge
                                        size="sm"
                                        color="sky"
                                    >
                                        {{ __('Is dynamic') }}
                                    </flux:badge>
                                @endif
                                @if ($isDynamicRejected)
                                    <flux:badge
                                        size="sm"
                                        color="rose"
                                    >
                                        {{ __('Is no dynamic') }}
                                    </flux:badge>
                                @endif
                                @if ($isDynamicMultiConfirmed)
                                    <flux:badge
                                        size="sm"
                                        color="violet"
                                    >
                                        {{ __('Is dynamic multi') }}
                                    </flux:badge>
                                @endif
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $entry->candidate_reason ?: __('No candidate reason') }}
                            </div>
                        </flux:callout.text>
                    </flux:callout>

                    {{-- Callout Occurances --}}
                    <flux:callout
                        class="col-span-2"
                        color="amber"
                        icon="map-pin"
                    >
                        <flux:callout.heading>{{ __('Occurrences') }}</flux:callout.heading>
                        <flux:callout.text class="space-y-2">
                            <div class="flex flex-wrap gap-1.5">
                                <flux:badge
                                    size="sm"
                                    color="sky"
                                >{{ __('Total') }} {{ number_format($entry->occurrences_count ?? 0) }}</flux:badge>
                                <flux:badge
                                    size="sm"
                                    color="emerald"
                                >{{ __('Active') }} {{ number_format($entry->active_occurrences_count ?? 0) }}
                                </flux:badge>
                                <flux:badge
                                    size="sm"
                                    color="amber"
                                >{{ __('Stale') }} {{ number_format($entry->stale_occurrences_count ?? 0) }}
                                </flux:badge>
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ __('Scans') }} {{ number_format($entry->scan_count ?? 0) }}
                            </div>
                        </flux:callout.text>
                    </flux:callout>

                    {{-- Callout Status --}}
                    <flux:callout
                        class="col-span-1"
                        color="emerald"
                        icon="activity"
                    >
                        <flux:callout.heading>{{ __('Status') }}</flux:callout.heading>
                        <flux:callout.text class="space-y-2">
                            {{-- BadgeStatus --}}
                            <flux:badge
                                size="sm"
                                :color="$entry->status === 'obsolete' ? 'zinc' : 'emerald'"
                            >
                                {{ str($entry->status)->headline() }}
                            </flux:badge>
                        </flux:callout.text>
                    </flux:callout>

                    {{-- Callout Review --}}
                    <flux:callout
                        class="col-span-2"
                        color="lime"
                        icon="badge-check"
                    >
                        <flux:callout.heading>{{ __('Review') }}</flux:callout.heading>
                        <flux:callout.text class="space-y-2">
                            {{-- Badge ReviewStatus --}}
                            <flux:badge
                                size="sm"
                                :color="match ($entry->review_status) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    'reviewed', 'approved' => 'emerald',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    'needs_attention' => 'amber',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    default => 'zinc',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }"
                            >
                                {{ str($entry->review_status)->headline() }}
                            </flux:badge>

                            {{-- Badge Key --}}
                            <flux:badge
                                size="sm"
                                :color="match ($entry->translation_key_source) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    'manual' => 'sky',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    'suggested' => 'emerald',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    'code' => 'violet',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    default => 'zinc',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }"
                            >
                                {{ __('Key') }}:
                                {{ match ($entry->translation_key_source) {
                                    'manual' => __('Manual'),
                                    'suggested' => __('Suggested'),
                                    'code' => __('Code'),
                                    default => __('Undecided'),
                                } }}
                            </flux:badge>
                        </flux:callout.text>
                    </flux:callout>

                    {{-- Callout Namespace --}}
                    <flux:callout
                        class="col-span-1"
                        color="violet"
                        icon="folder"
                    >
                        <flux:callout.heading>{{ __('Namespace') }}</flux:callout.heading>
                        <flux:callout.text class="space-y-1 text-sm">
                            <div class="truncate font-mono text-xs">{{ $entry->namespace ?: '—' }}</div>
                            <div class="truncate font-mono text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $entry->group ?: '—' }}
                            </div>
                        </flux:callout.text>
                    </flux:callout>
                </div>

                <div class="mt-6 grid gap-3 lg:grid-cols-5">
                    {{-- Card Keys --}}
                    <flux:card class="col-span-3">
                        @php
                            $suggestedKey = trim((string) $entry->suggested_key);
                            $translationKey = trim((string) $entry->translation_key);
                            $keyState = match (true) {
                                $suggestedKey !== '' && $translationKey === '' => [
                                    'label' => __('Missing translation key'),
                                    'color' => 'amber',
                                ],
                                $suggestedKey !== '' && $translationKey === $suggestedKey => [
                                    'label' => __('Equal translation key'),
                                    'color' => 'emerald',
                                ],
                                $suggestedKey !== '' && $translationKey !== '' && $translationKey !== $suggestedKey => [
                                    'label' => __('Different translation key'),
                                    'color' => 'sky',
                                ],
                                default => [
                                    'label' => __('No suggested key'),
                                    'color' => 'zinc',
                                ],
                            };
                        @endphp

                        <x-ui.headers.card
                            :title="__('Keys')"
                            :description="__('Suggested, existing and current translation key metadata.')"
                        >
                            @php
                                $hasTranslationKey = filled($entry->translation_key);
                                $canConfirmDynamicMulti = $hasTranslationKey && $isDynamicConfirmed;
                            @endphp

                            <div
                                class="grid w-[36rem] max-w-full grid-cols-5 items-center gap-x-2 gap-y-1.5"
                                wire:key="translation-workbench-review-flags-{{ $entry->id }}-{{ (int) $isUiConfirmed }}-{{ (int) $isUiRejected }}-{{ (int) $isDynamicConfirmed }}-{{ (int) $isDynamicRejected }}-{{ (int) $isDynamicMultiConfirmed }}"
                            >

                                {{-- Checkbox Candidate UI: No UI --}}
                                <flux:checkbox
                                    class="col-span-1 justify-self-end hover:cursor-pointer"
                                    wire:key="translation-workbench-review-is-no-ui-{{ $entry->id }}-{{ (int) $isUiConfirmed }}-{{ (int) $isUiRejected }}-{{ (int) $isDynamicConfirmed }}"
                                    label="{{ __('No') }}"
                                    :checked="$isUiRejected"
                                    :disabled="$isUiConfirmed || $isDynamicConfirmed"
                                    wire:change="setUiCandidateRejected({{ $entry->id }}, $event.target.checked)"
                                    wire:loading.attr="disabled"
                                    wire:target="setUiCandidateRejected"
                                />
                                {{-- Badge Candidate UI --}}
                                <flux:badge
                                    class="col-span-2 w-48 min-w-0"
                                    size="sm"
                                    :color="$isUiRejected ? 'rose' : ($isUiCandidateVisible ? 'amber' : 'zinc')"
                                >
                                    {{ __('Candidate: UI') }}
                                    {{ $isUiRejected ? __('Rejected') : ($isUiCandidateVisible ? __('Yes') : __('No')) }}
                                </flux:badge>
                                {{-- Checkbox Candidate UI: Is UI --}}
                                <flux:checkbox
                                    class="col-span-1 hover:cursor-pointer"
                                    wire:key="translation-workbench-review-is-ui-{{ $entry->id }}-{{ (int) $isUiConfirmed }}-{{ (int) $isUiRejected }}-{{ (int) $isDynamicConfirmed }}"
                                    label="{{ __('Is UI') }}"
                                    :checked="$isUiConfirmed"
                                    :disabled="!$hasTranslationKey || $isDynamicConfirmed || $isUiRejected"
                                    wire:change="setUiCandidate({{ $entry->id }}, $event.target.checked)"
                                    wire:loading.attr="disabled"
                                    wire:target="setUiCandidate"
                                />
                                <div class="col-span-1">
                                    {{-- EMPTY --}}
                                </div>

                                {{-- Checkbox Candidate Dynamic: No Dynamic --}}
                                <flux:checkbox
                                    class="col-span-1 justify-self-end hover:cursor-pointer"
                                    wire:key="translation-workbench-review-is-no-dynamic-{{ $entry->id }}-{{ (int) $isDynamicConfirmed }}-{{ (int) $isDynamicRejected }}-{{ (int) $isUiConfirmed }}"
                                    label="{{ __('No') }}"
                                    :checked="$isDynamicRejected"
                                    :disabled="$isDynamicConfirmed || $isUiConfirmed"
                                    wire:change="setDynamicCandidateRejected({{ $entry->id }}, $event.target.checked)"
                                    wire:loading.attr="disabled"
                                    wire:target="setDynamicCandidateRejected"
                                />
                                {{-- Badge Candidate Dynamic --}}
                                <flux:badge
                                    class="col-span-2 w-48 min-w-0"
                                    size="sm"
                                    :color="$isDynamicRejected ? 'rose' : ($isDynamicCandidateVisible ? 'amber' : 'zinc')"
                                >
                                    {{ __('Candidate: dynamic') }}
                                    {{ $isDynamicRejected ? __('Rejected') : ($isDynamicCandidateVisible ? __('Yes') : __('No')) }}
                                </flux:badge>

                                {{-- Checkbox Candidate Dynamic: Is Dynamic --}}
                                <flux:checkbox
                                    class="col-span-1 hover:cursor-pointer"
                                    wire:key="translation-workbench-review-is-dynamic-{{ $entry->id }}-{{ (int) $isDynamicConfirmed }}-{{ (int) $isDynamicRejected }}-{{ (int) $isUiConfirmed }}"
                                    label="{{ __('Is DY') }}"
                                    :checked="$isDynamicConfirmed"
                                    :disabled="!$hasTranslationKey || $isUiConfirmed || $isDynamicRejected"
                                    wire:change="setDynamicCandidate({{ $entry->id }}, $event.target.checked)"
                                    wire:loading.attr="disabled"
                                    wire:target="setDynamicCandidate"
                                />

                                {{-- Checkbox Candidate Dynamic: Is Dynamic Multi --}}
                                <flux:checkbox
                                    class="col-span-1 hover:cursor-pointer"
                                    wire:key="translation-workbench-review-is-dynamic-multi-{{ $entry->id }}-{{ (int) $isDynamicConfirmed }}-{{ (int) $isDynamicMultiConfirmed }}"
                                    label="{{ __('Is DY-m') }}"
                                    :checked="$isDynamicMultiConfirmed"
                                    :disabled="!$canConfirmDynamicMulti"
                                    wire:change="setDynamicMultiCandidate({{ $entry->id }}, $event.target.checked)"
                                    wire:loading.attr="disabled"
                                    wire:target="setDynamicMultiCandidate"
                                />
                            </div>
                        </x-ui.headers.card>

                        <div class="space-y-3 text-sm">
                            <div>
                                <flux:badge
                                    size="sm"
                                    :color="$keyState['color']"
                                >
                                    {{ $keyState['label'] }}
                                </flux:badge>

                                @if (filled($entry->deleted_segments))
                                    <flux:badge
                                        class="ms-1"
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('Deleted segments') }} {{ count((array) $entry->deleted_segments) }}
                                    </flux:badge>
                                @endif
                            </div>

                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <div
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                        <span>{{ __('Translation key') }}</span>
                                        <x-ui.tooltip.trigger
                                            :title="__('Translation key')"
                                            :text="__(
                                                'The key that will be used by the application and translation database after review. This value may be accepted from the suggestion or edited manually.',
                                            )"
                                        >
                                            <flux:icon.information-circle class="size-3.5" />
                                        </x-ui.tooltip.trigger>
                                    </div>

                                    <flux:button
                                        type="button"
                                        title="{{ __('Edit translation key.') }}"
                                        size="xs"
                                        variant="ghost"
                                        icon="pencil"
                                        :aria-label="__('Edit translation key')"
                                        wire:click="openTranslationKeyModal({{ $entry->id }})"
                                    />
                                </div>
                                <div class="break-all font-mono text-xs">{{ $entry->translation_key ?: '—' }}</div>
                            </div>
                            <div>
                                <div
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                    <span>{{ __('Existing key') }}</span>
                                    <x-ui.tooltip.trigger
                                        :title="__('Existing key')"
                                        :text="__(
                                            'A translation key that was already found in the scanned source code. It represents the current code state before this workbench review decides whether to keep or change it.',
                                        )"
                                    >
                                        <flux:icon.information-circle class="size-3.5" />
                                    </x-ui.tooltip.trigger>
                                </div>
                                <div class="break-all font-mono text-xs">{{ $entry->existing_key ?: '—' }}</div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <div
                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                        <span>{{ __('Suggested key') }}</span>
                                        <x-ui.tooltip.trigger
                                            :title="__('Suggested key')"
                                            :text="__(
                                                'A generated proposal based on the source path, namespace rules and scanned literal. It is only a review aid until it is accepted as translation key.',
                                            )"
                                        >
                                            <flux:icon.information-circle class="size-3.5" />
                                        </x-ui.tooltip.trigger>
                                    </div>

                                    <flux:button
                                        type="button"
                                        title="{{ __('Copy the suggested key to translation key.') }}"
                                        size="xs"
                                        variant="ghost"
                                        icon="copy-plus"
                                        :disabled="blank($entry->suggested_key) || $entry->translation_key === $entry->suggested_key"
                                        :aria-label="__('Copy suggested key to translation key')"
                                        wire:click="acceptSuggestedKey({{ $entry->id }})"
                                    />
                                </div>
                                <div class="break-all font-mono text-xs">{{ $entry->suggested_key ?: '—' }}</div>
                            </div>
                        </div>
                    </flux:card>

                    {{-- Card Source Values --}}
                    <flux:card class="col-span-2">
                        <x-ui.headers.card
                            :title="__('Source value')"
                            :description="__('Literal, suggested literal and raw expression from the scanner.')"
                        />

                        <div class="space-y-3 text-sm">
                            <div>
                                <div
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                    <span>{{ __('Literal text') }}</span>
                                    <x-ui.tooltip.trigger
                                        :title="__('Literal text')"
                                        :text="__(
                                            'The literal text found directly by the scanner. It is a source candidate and can later become the source-language translation value during the edit workflow.',
                                        )"
                                    >
                                        <flux:icon.information-circle class="size-3.5" />
                                    </x-ui.tooltip.trigger>
                                </div>
                                <div class="break-words">{{ $entry->literal_text ?: '—' }}</div>
                            </div>
                            <div>
                                <div
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                    <span>{{ __('Literal text suggested') }}</span>
                                    <x-ui.tooltip.trigger
                                        :title="__('Literal text suggested')"
                                        :text="__(
                                            'A readable source-value proposal derived from a scanned translation key or expression when no direct literal text is available.',
                                        )"
                                    >
                                        <flux:icon.information-circle class="size-3.5" />
                                    </x-ui.tooltip.trigger>
                                </div>
                                <div class="break-words">{{ $entry->literal_text_suggested ?: '—' }}</div>
                            </div>
                            <div>
                                <div
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                    <span>{{ __('Raw expression') }}</span>
                                    <x-ui.tooltip.trigger
                                        :title="__('Raw expression')"
                                        :text="__(
                                            'The original scanned code expression before workbench normalization. It is kept for traceability and source-code review, not as the final translation value.',
                                        )"
                                    >
                                        <flux:icon.information-circle class="size-3.5" />
                                    </x-ui.tooltip.trigger>
                                </div>
                                <div
                                    class="max-h-32 overflow-auto rounded border border-zinc-200 bg-zinc-50 p-2 font-mono text-xs dark:border-zinc-700 dark:bg-zinc-900">
                                    {{ $entry->raw_expression ?: '—' }}
                                </div>
                            </div>
                        </div>
                    </flux:card>
                </div>

                {{-- Card Occurances --}}
                <flux:card class="mt-6">
                    <x-ui.headers.card
                        :title="__('Occurrences')"
                        :description="__('Collected source locations for this entry.')"
                    />

                    @if ($canCountOccurrences && $entry->occurrences->isNotEmpty())
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('Status') }}</flux:table.column>
                                <flux:table.column>{{ __('Source') }}</flux:table.column>
                                <flux:table.column>{{ __('Function') }}</flux:table.column>
                                <flux:table.column>{{ __('Suggested key') }}</flux:table.column>
                                <flux:table.column>{{ __('Seen') }}</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach ($entry->occurrences as $occurrence)
                                    @php
                                        $occurrenceSourcePath = trim((string) $occurrence->source_path);
                                        $occurrenceEditorUrl = null;

                                        if ($occurrenceSourcePath !== '') {
                                            $occurrenceAbsolutePath = str_replace(
                                                '\\',
                                                '/',
                                                base_path($occurrenceSourcePath),
                                            );
                                            $occurrenceEditorPath = str_replace(
                                                '%2F',
                                                '/',
                                                rawurlencode($occurrenceAbsolutePath),
                                            );
                                            $occurrenceEditorLine = $occurrence->source_line
                                                ? ':' . $occurrence->source_line
                                                : '';
                                            $occurrenceEditorUrl =
                                                'vscode://vscode-remote/wsl+' .
                                                rawurlencode(
                                                    (string) config('translation-workbench.editor.vscode_wsl_distro'),
                                                ) .
                                                $occurrenceEditorPath .
                                                $occurrenceEditorLine;
                                        }
                                    @endphp

                                    <flux:table.row :key="'translation-workbench-occurrence-' . $occurrence->id">
                                        <flux:table.cell>
                                            <flux:badge
                                                size="sm"
                                                :color="$occurrence->status === 'active' ? 'emerald' : 'amber'"
                                            >
                                                {{ str($occurrence->status)->headline() }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <div class="flex items-start gap-1.5">
                                                <flux:button
                                                    class="h-6 w-6 shrink-0"
                                                    type="button"
                                                    size="xs"
                                                    variant="ghost"
                                                    icon="external-link"
                                                    icon:class="text-red-500 dark:text-red-400"
                                                    :href="$occurrenceEditorUrl"
                                                    :disabled="$occurrenceEditorUrl === null"
                                                    :aria-label="__('Open source in VS Code')"
                                                />

                                                <div
                                                    class="wrap-anywhere max-w text-wrap pt-1 font-mono text-xs"
                                                    title="{{ $occurrence->source_path }}{{ $occurrence->source_line ? ':' . $occurrence->source_line : '' }}"
                                                >
                                                    {{ $occurrence->source_path }}{{ $occurrence->source_line ? ':' . $occurrence->source_line : '' }}
                                                </div>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <span
                                                class="font-mono text-xs">{{ $occurrence->function_name ?: '—' }}</span>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <div
                                                class="max-w truncate font-mono text-xs"
                                                title="{{ $occurrence->suggested_key ?: '—' }}"
                                            >
                                                {{ $occurrence->suggested_key ?: '—' }}
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <div class="text-xs">
                                                {{ $occurrence->last_seen_at?->format('Y-m-d H:i') ?: '—' }}
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    @else
                        <div
                            class="rounded border border-dashed border-zinc-200 p-4 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            {{ __('No occurrences are available for this entry yet.') }}
                        </div>
                    @endif
                </flux:card>

            </div>
        </div>
    @endif
</flux:modal>
