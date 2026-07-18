{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-states.blade.php --}}

@php
    $reviewFindingSuggestedKey = trim((string) ($reviewFinding->suggested_key ?? ''));
    $reviewKeySuggestedKey = trim((string) ($reviewFinding->key_suggested_key ?? ''));
    $reviewTranslationKey = trim((string) ($reviewFinding->translation_key ?? ''));
    $reviewEffectiveSuggestedKey = $reviewKeySuggestedKey !== '' ? $reviewKeySuggestedKey : $reviewFindingSuggestedKey;
    $reviewKeyState = match (true) {
        $reviewTranslationKey === '' => [
            'label' => __('Translation key missing'),
            'color' => 'red',
        ],
        $reviewEffectiveSuggestedKey !== '' && $reviewTranslationKey === $reviewEffectiveSuggestedKey => [
            'label' => __('Translation key equal'),
            'color' => 'green',
        ],
        $reviewEffectiveSuggestedKey !== '' && $reviewTranslationKey !== $reviewEffectiveSuggestedKey => [
            'label' => __('Translation key different'),
            'color' => 'sky',
        ],
        default => [
            'label' => __('Translation key set'),
            'color' => 'green',
        ],
    };
    $reviewFirstSeenAt = $reviewFinding->first_seen_at
        ? \Illuminate\Support\Carbon::parse($reviewFinding->first_seen_at)
        : null;
    $reviewLastSeenAt = $reviewFinding->last_seen_at
        ? \Illuminate\Support\Carbon::parse($reviewFinding->last_seen_at)
        : null;
    $reviewSeenAtSameTime = $reviewFirstSeenAt && $reviewLastSeenAt && $reviewFirstSeenAt->equalTo($reviewLastSeenAt);
    $reviewLastSeenAgeSeconds = $reviewLastSeenAt ? $reviewLastSeenAt->diffInSeconds(now()) : null;
    $reviewLastSeenAgeColor = match (true) {
        $reviewLastSeenAgeSeconds === null => 'zinc',
        $reviewLastSeenAgeSeconds <= 3600 => 'green',
        $reviewLastSeenAgeSeconds <= 86400 => 'sky',
        $reviewLastSeenAgeSeconds <= 604800 => 'amber',
        $reviewLastSeenAgeSeconds <= 2592000 => 'orange',
        default => 'red',
    };
    $reviewHasTranslationKey = $reviewTranslationKey !== '';
    $reviewIsUiConfirmed = (bool) ($reviewFinding->is_ui_key ?? false);
    $reviewIsUiRejected = (bool) ($reviewFinding->is_ui_candidate_rejected ?? false) && !$reviewIsUiConfirmed;
    $reviewIsDynamicConfirmed = (bool) ($reviewFinding->is_dynamic_key ?? false);
    $reviewIsDynamicRejected =
        (bool) ($reviewFinding->is_dynamic_candidate_rejected ?? false) && !$reviewIsDynamicConfirmed;
    $reviewIsDynamicMultiConfirmed = $reviewIsDynamicConfirmed && (bool) ($reviewFinding->is_dynamic_multi ?? false);
    $reviewIsUiCandidate = $reviewFinding->candidate_type === 'ui';
    $reviewIsDynamicCandidate =
        $reviewFinding->candidate_type === 'dynamic' ||
        $reviewFinding->entry_type === 'dynamic' ||
        $reviewFinding->kind === 'dynamic_multi';
    $reviewIsNormalTranslation =
        !$reviewIsUiConfirmed && !$reviewIsUiRejected && !$reviewIsDynamicConfirmed && !$reviewIsDynamicRejected;
    $reviewEffectiveUiCandidate =
        !$reviewIsUiConfirmed && ($reviewFinding->reviewed_is_ui_candidate ?? $reviewIsUiCandidate);
    $reviewEffectiveDynamicCandidate =
        !$reviewIsDynamicConfirmed &&
        !$reviewIsDynamicMultiConfirmed &&
        ($reviewFinding->reviewed_is_dynamic_candidate ?? !$reviewEffectiveUiCandidate && $reviewIsDynamicCandidate);
    $reviewEffectiveDynamicMulti =
        $reviewIsDynamicMultiConfirmed ||
        ($reviewEffectiveDynamicCandidate && (bool) ($reviewFinding->reviewed_is_dynamic_multi ?? false));
    $reviewUiControlValue = $reviewIsUiConfirmed || $reviewEffectiveUiCandidate;
    $reviewDynamicControlValue =
        $reviewIsDynamicConfirmed || $reviewIsDynamicMultiConfirmed || $reviewEffectiveDynamicCandidate;
    $reviewDynamicDataState = trim(
        (string) ($reviewFinding->key_dynamic_data_state ?? '' ?: $reviewFinding->dynamic_data_state ?? ''),
    );
    $reviewDynamicQualification = match (true) {
        $reviewIsDynamicMultiConfirmed || $reviewEffectiveDynamicMulti => [
            'label' => __('Dynamic option list'),
            'color' => 'cyan',
            'text' => __(
                'This finding is treated as a dynamic multi entry: multiple runtime option values may need separate translations.',
            ),
        ],
        $reviewIsDynamicConfirmed || $reviewEffectiveDynamicCandidate || filled($reviewDynamicDataState) => [
            'label' => __('Dynamic translation'),
            'color' => 'teal',
            'text' => __(
                'This finding is treated as dynamic: the displayed value is resolved from runtime data and still needs structured dynamic data handling.',
            ),
        ],
        default => null,
    };
    $reviewUiReviewDisabled = $reviewDynamicQualification !== null;
@endphp

<div class="grid gap-3 xl:grid-cols-4">
    {{-- Callout Finding State --}}
    <flux:callout
        class="xl:col-span-1"
        color="sky"
        icon="scan-search"
    >
        <flux:callout.heading>
            <span class="inline-flex items-center gap-1.5">
                <span>{{ __('Finding state') }}</span>
                <flux:tooltip
                    content="{{ __('Scanner metadata for this finding: kind, entry type, function and active/stale status.') }}"
                >
                    <flux:icon.info class="size-3.5 text-zinc-400" />
                </flux:tooltip>
            </span>
        </flux:callout.heading>
        <flux:callout.text class="text-xs">
            {{ __('Shows how the scanner classified this code occurrence and whether it is still present in the latest scan.') }}
        </flux:callout.text>

        <div class="flex flex-wrap gap-1.5">
            <flux:badge
                size="sm"
                color="sky"
            >
                {{ __('Kind') }}: {{ $reviewFinding->kind }}
            </flux:badge>

            @if ($reviewFinding->entry_type)
                <flux:badge
                    size="sm"
                    variant="subtle"
                >
                    {{ __('Entry') }}: {{ $reviewFinding->entry_type }}
                </flux:badge>
            @endif

            @if ($reviewFinding->function_name)
                <flux:badge
                    size="sm"
                    variant="subtle"
                >
                    {{ $reviewFinding->function_name }}
                </flux:badge>
            @endif

            <flux:badge
                size="sm"
                color="{{ $reviewFinding->status === 'active' ? 'green' : 'amber' }}"
            >
                {{ $reviewFinding->status }}
            </flux:badge>
        </div>
    </flux:callout>

    {{-- Callout Key State --}}
    <flux:callout
        class="xl:col-span-1"
        color="indigo"
        icon="key-round"
    >
        <flux:callout.heading>
            <span class="inline-flex items-center gap-1.5">
                <span>{{ __('Key state') }}</span>
                <flux:tooltip
                    content="{{ __('Current workbench key linkage, review status and whether a translation key has already been set.') }}"
                >
                    <flux:icon.info class="size-3.5 text-zinc-400" />
                </flux:tooltip>
            </span>
        </flux:callout.heading>
        <flux:callout.text class="text-xs">
            {{ __('Shows whether this finding is connected to a workbench key and whether the key is missing, equal to the suggestion, or deliberately different.') }}
        </flux:callout.text>

        <div class="flex flex-wrap gap-1.5">
            <flux:badge
                size="sm"
                color="{{ $reviewFinding->key_id ? 'green' : 'amber' }}"
            >
                {{ $reviewFinding->key_id ? __('Key linked') : __('Key missing') }}
            </flux:badge>

            <flux:badge
                size="sm"
                color="{{ $reviewKeyState['color'] }}"
            >
                {{ $reviewKeyState['label'] }}
            </flux:badge>

            @if ($reviewFinding->key_status)
                <flux:badge
                    size="sm"
                    variant="subtle"
                >
                    {{ __('Key') }}: {{ $reviewFinding->key_status }}
                </flux:badge>
            @endif

            @if ($reviewFinding->review_status)
                <flux:badge
                    size="sm"
                    variant="subtle"
                >
                    {{ __('Review') }}: {{ $reviewFinding->review_status }}
                </flux:badge>
            @endif
        </div>
    </flux:callout>

    {{-- Callout Classification --}}
    <flux:callout
        class="xl:col-span-1"
        color="violet"
        icon="tag"
    >
        <flux:callout.heading>
            <span class="inline-flex items-center gap-1.5">
                <span>{{ __('Classification') }}</span>
                <flux:tooltip
                    content="{{ __('Resolved review classification. Confirmed states have priority over scanner candidate states.') }}"
                >
                    <flux:icon.info class="size-3.5 text-zinc-400" />
                </flux:tooltip>
            </span>
        </flux:callout.heading>
        <flux:callout.text class="text-xs">
            {{ __('Shows the effective type decision: normal, UI, dynamic, dynamic multi, or still only a scanner candidate.') }}
        </flux:callout.text>

        <div class="flex flex-wrap gap-1.5">
            @if ($reviewIsNormalTranslation)
                <flux:badge
                    size="sm"
                    color="green"
                >
                    {{ __('Normal translation') }}
                </flux:badge>
            @endif

            @if (
                !$reviewIsUiConfirmed &&
                    !$reviewIsDynamicConfirmed &&
                    !$reviewIsDynamicMultiConfirmed &&
                    $reviewFinding->candidate_type)
                <flux:badge
                    size="sm"
                    color="amber"
                >
                    {{ __('candidate') }}: {{ $reviewFinding->candidate_type }}
                </flux:badge>
            @elseif (!$reviewIsUiConfirmed && !$reviewIsDynamicConfirmed && !$reviewIsDynamicMultiConfirmed)
                <flux:badge
                    size="sm"
                    variant="subtle"
                >
                    {{ __('No candidate') }}
                </flux:badge>
            @endif

            @if ($reviewIsUiConfirmed)
                <flux:badge
                    size="sm"
                    color="green"
                >
                    {{ __('isUI') }}
                </flux:badge>
            @elseif ($reviewEffectiveUiCandidate)
                <flux:badge
                    size="sm"
                    color="amber"
                >
                    {{ __('candidateUI') }}
                </flux:badge>
            @endif

            @if ($reviewIsUiRejected)
                <flux:badge
                    size="sm"
                    color="rose"
                >
                    {{ __('No UI') }}
                </flux:badge>
            @endif

            @if ($reviewIsDynamicMultiConfirmed)
                <flux:badge
                    size="sm"
                    color="cyan"
                >
                    {{ __('Dynamic option list') }}
                </flux:badge>
            @elseif ($reviewIsDynamicConfirmed)
                <flux:badge
                    size="sm"
                    color="teal"
                >
                    {{ __('Dynamic translation') }}
                </flux:badge>
            @elseif ($reviewEffectiveDynamicMulti)
                <flux:badge
                    size="sm"
                    color="cyan"
                >
                    {{ __('Dynamic option candidate') }}
                </flux:badge>
            @elseif ($reviewEffectiveDynamicCandidate)
                <flux:badge
                    size="sm"
                    color="amber"
                >
                    {{ __('Dynamic candidate') }}
                </flux:badge>
            @endif

            @if ($reviewIsDynamicRejected)
                <flux:badge
                    size="sm"
                    color="rose"
                >
                    {{ __('No dynamic') }}
                </flux:badge>
            @endif

            @if ($reviewFinding->dynamic_scope)
                <flux:badge
                    size="sm"
                    variant="subtle"
                >
                    {{ __('Scope') }}: {{ $reviewFinding->dynamic_scope }}
                </flux:badge>
            @endif

            @if (filled($reviewDynamicDataState))
                <x-ui.tooltip.trigger
                    :title="__('Dynamic data state')"
                    :text="__(
                        'Unstructured means this dynamic finding is identified, but its runtime values have not yet been normalized into dedicated dynamic translation value records.',
                    )"
                >
                    <flux:badge
                        size="sm"
                        color="{{ $reviewDynamicDataState === 'structured' ? 'green' : 'orange' }}"
                    >
                        {{ $reviewDynamicDataState === 'structured' ? __('Data structured') : __('Data unstructured') }}
                    </flux:badge>
                </x-ui.tooltip.trigger>
            @endif

            @if ($reviewDynamicQualification)
                <x-ui.tooltip.trigger
                    :title="__('Dynamic qualification')"
                    :text="$reviewDynamicQualification['text']"
                >
                    <flux:badge
                        size="sm"
                        color="{{ $reviewDynamicQualification['color'] }}"
                    >
                        {{ __('Qualification') }}: {{ $reviewDynamicQualification['label'] }}
                    </flux:badge>
                </x-ui.tooltip.trigger>
            @endif
        </div>
    </flux:callout>

    {{-- Callout Seen State --}}
    <flux:callout
        class="xl:col-span-1"
        color="zinc"
        icon="clock-check"
    >
        <flux:callout.heading>
            <span class="inline-flex items-center gap-1.5">
                <span>{{ __('Seen state') }}</span>
                <flux:tooltip content="{{ __('First and latest scanner timestamps for this finding.') }}">
                    <flux:icon.info class="size-3.5 text-zinc-400" />
                </flux:tooltip>
            </span>
        </flux:callout.heading>
        <flux:callout.text class="text-xs">
            {{ __('Shows when this occurrence was first detected, last confirmed, and how old the latest scan observation is.') }}
        </flux:callout.text>

        <div class="flex flex-wrap gap-1.5">
            @if ($reviewFinding->first_seen_at)
                <flux:badge
                    size="sm"
                    color="green"
                >
                    {{ __('First') }}:
                    {{ $reviewFirstSeenAt?->format('D, d.M.Y H:i') ?? $reviewFinding->first_seen_at }}
                </flux:badge>
            @endif

            @if ($reviewFinding->last_seen_at)
                <flux:badge
                    size="sm"
                    color="{{ $reviewSeenAtSameTime ? 'green' : 'orange' }}"
                >
                    {{ __('Last') }}:
                    {{ $reviewLastSeenAt?->format('D, d.M.Y H:i') ?? $reviewFinding->last_seen_at }}
                </flux:badge>
            @endif

            @if ($reviewLastSeenAt)
                <flux:badge
                    size="sm"
                    color="{{ $reviewLastSeenAgeColor }}"
                >
                    {{ __('Ago') }}: {{ $reviewLastSeenAt->diffForHumans() }}
                </flux:badge>
            @endif
        </div>
    </flux:callout>
</div>

<div class="grid gap-3 xl:grid-cols-4">

    {{-- Callout Classification Review --}}
    <flux:callout
        class="col-span-4 mt-3"
        color="amber"
        icon="badge-check"
    >
        <div class="grid gap-3 xl:grid-cols-4">
            <flux:field class="xl:col-span-2">
                <flux:callout.heading>
                    <span class="inline-flex items-center gap-1.5">
                        <span>{{ __('Classification review') }}</span>
                        <flux:tooltip
                            content="{{ __('Review controls for accepting or rejecting UI and dynamic classifications.') }}"
                        >
                            <flux:icon.info class="size-3.5 text-zinc-400" />
                        </flux:tooltip>
                    </span>
                </flux:callout.heading>
                <flux:callout.text class="text-xs">
                    <div class="space-y-2">
                        @if (!$reviewHasTranslationKey)
                            <div class="text-amber-600 dark:text-amber-300">
                                {{ __('Set or accept a translation key before confirming this entry as UI.') }}
                            </div>
                        @endif

                        @if ($reviewUiReviewDisabled)
                            <div class="text-teal-600 dark:text-teal-300">
                                {{ __('UI review is disabled for dynamic findings. Dynamic qualification is scanner/data-state driven.') }}
                            </div>
                        @endif

                        @if ($reviewFinding->candidate_reason)
                            <div class="wrap-anywhere grid grid-cols-3 text-wrap">
                                <span class="col-span-1 font-medium">{{ __('Candidate Evaluation') }}!</span>
                                <span class="col-span-2 font-semibold text-zinc-200">
                                    {{ $reviewFinding->candidate_reason }}
                                </span>
                            </div>
                        @else
                            <div class="text-zinc-500">
                                {{ __('No scanner candidate reason was recorded for this finding.') }}
                            </div>
                        @endif
                    </div>
                </flux:callout.text>
            </flux:field>

            <div
                class="grid self-start gap-3 md:grid-cols-2 xl:col-span-2"
                x-data="{
                    uiCandidate: @js($reviewUiControlValue ? 'yes' : 'no'),
                }"
                wire:key="translation-workbench-review-classification-{{ $reviewFinding->id }}-{{ (int) $reviewIsUiConfirmed }}-{{ (int) $reviewIsUiRejected }}-{{ (int) $reviewEffectiveUiCandidate }}"
            >
                <flux:field>
                    <flux:radio.group
                        class="buergerfrs-checkbox-cards-compact grid grid-cols-3 content-start"
                        label="{{ __('UI translation') }}?"
                        variant="cards"
                        x-model="uiCandidate"
                    >
                        <flux:radio
                            @class([
                                'col-span-1',
                                'hover:cursor-not-allowed' => $reviewUiReviewDisabled,
                                'hover:cursor-pointer' => ! $reviewUiReviewDisabled,
                            ])
                            value="no"
                            :disabled="$reviewUiReviewDisabled"
                            x-on:click="uiCandidate = 'no'"
                            wire:click="setUiCandidateClassification({{ $reviewFinding->id }}, false)"
                            wire:loading.attr="disabled"
                            wire:target="setUiCandidateClassification"
                        >
                            <div class="flex-1">
                                <flux:heading class="{{ $reviewIsUiRejected ? 'text-red-500' : '' }} leading-4">
                                    {{ __('NO') }}
                                </flux:heading>
                            </div>
                            <flux:radio.indicator />
                        </flux:radio>
                        <flux:radio
                            @class([
                                'col-span-1',
                                'hover:cursor-not-allowed' => $reviewUiReviewDisabled,
                                'hover:cursor-pointer' => ! $reviewUiReviewDisabled,
                            ])
                            value="yes"
                            :disabled="$reviewUiReviewDisabled"
                            x-on:click="uiCandidate = 'yes'"
                            wire:click="setUiCandidateClassification({{ $reviewFinding->id }}, true)"
                            wire:loading.attr="disabled"
                            wire:target="setUiCandidateClassification"
                        >
                            <div class="flex-1">
                                <flux:heading class="{{ $reviewIsUiConfirmed ? 'text-green-500' : '' }} leading-4">
                                    {{ __('YES') }}
                                </flux:heading>
                            </div>
                            <flux:radio.indicator />
                        </flux:radio>
                    </flux:radio.group>
                </flux:field>
                {{--
                    Dynamic classification is now scanner/data-state driven.
                    Keep this block inactive until the structured dynamic-value workflow is implemented.

                <flux:field>
                    <flux:radio.group
                        class="buergerfrs-checkbox-cards-compact grid grid-cols-3"
                        label="{{ __('Dynamic translation') }}?"
                        variant="cards"
                        x-model="dynamicCandidate"
                    >
                        <flux:radio
                            class="col-span-1 hover:cursor-pointer"
                            value="no"
                            x-on:click="dynamicCandidate = 'no'; dynamicMulti = false"
                            wire:click="setDynamicCandidateClassification({{ $reviewFinding->id }}, false)"
                            wire:loading.attr="disabled"
                            wire:target="setUiCandidateClassification,setDynamicCandidateClassification"
                        >
                            <div class="flex-1">
                                <flux:heading class="leading-4 {{ $reviewIsDynamicRejected ? 'text-red-500' : '' }}">
                                    {{ __('NO') }}
                                </flux:heading>
                            </div>
                            <flux:radio.indicator />
                        </flux:radio>
                        <flux:radio
                            class="col-span-1 hover:cursor-pointer"
                            value="yes"
                            x-on:click="dynamicCandidate = 'yes'; uiCandidate = 'no'"
                            wire:click="setDynamicCandidateClassification({{ $reviewFinding->id }}, true)"
                            wire:loading.attr="disabled"
                            wire:target="setUiCandidateClassification,setDynamicCandidateClassification,setDynamicMultiKeyReview"
                        >
                            <div class="flex-1">
                                <flux:heading class="leading-4 {{ $reviewIsDynamicConfirmed ? 'text-green-500' : '' }}">
                                    {{ __('YES') }}
                                </flux:heading>
                            </div>
                            <flux:radio.indicator />
                        </flux:radio>
                    </flux:radio.group>
                </flux:field>
                <flux:field>
                    <flux:checkbox.group
                        class="buergerfrs-checkbox-cards-compact grid grid-cols-3"
                        label="{{ __('Dynamic-Multi') }}?"
                        variant="cards"
                    >
                        <flux:checkbox
                            class="col-span-1 hover:cursor-pointer"
                            value="is-candidate-dynamic-multi-yes"
                            x-model="dynamicMulti"
                            x-bind:disabled="dynamicCandidate !== 'yes'"
                            wire:change="setDynamicMultiKeyReview({{ $reviewFinding->id }}, $event.target.checked)"
                            wire:loading.attr="disabled"
                            wire:target="setDynamicCandidateClassification,setDynamicMultiKeyReview"
                        >
                            <div class="flex-1">
                                <flux:heading class="leading-4 {{ $reviewIsDynamicMultiConfirmed ? 'text-green-500' : '' }}">
                                    {{ __('MULTI') }}
                                </flux:heading>
                            </div>
                            <flux:checkbox.indicator />
                        </flux:checkbox>
                    </flux:checkbox.group>
                </flux:field>
                --}}
            </div>
        </div>
    </flux:callout>
</div>
