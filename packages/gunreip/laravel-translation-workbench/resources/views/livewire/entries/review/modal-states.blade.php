{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-states.blade.php --}}

@php
    $reviewFindingSuggestedKey = trim((string) ($reviewFinding->suggested_key ?? ''));
    $reviewKeySuggestedKey = trim((string) ($reviewFinding->key_suggested_key ?? ''));
    $reviewTranslationKey = trim((string) ($reviewFinding->translation_key ?? ''));
    $reviewEffectiveSuggestedKey = $reviewKeySuggestedKey !== '' ? $reviewKeySuggestedKey : $reviewFindingSuggestedKey;
    $reviewKeyState = match (true) {
        $reviewTranslationKey === '' => [
            'label' => __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_states.translation_key_missing'),
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
        {{-- Callout Heading Finding State --}}
        <flux:callout.heading>
            <span class="inline-flex items-center gap-1.5">
                <span>{{ __('Finding state') }}</span>
                {{-- Tooltip Finding State --}}
                <x-ui.tooltip.simple
                    :header="__('Finding state')"
                    :text="__(
                        'Scanner metadata for this finding: kind, entry type, function and active/stale status.',
                    )"
                />
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
                <span class="mr-1">{{ __('Kind') }}: {{ $reviewFinding->kind }}</span>
                <x-ui.tooltip.simple
                    :header="__('Finding kind')"
                    :text="__(
                        'Scanner classification of this code occurrence: normal, UI, dynamic, or dynamic multi.',
                    )"
                />
            </flux:badge>

            @if ($reviewFinding->entry_type)
                <flux:badge
                    size="sm"
                    variant="subtle"
                >
                    <span class="mr-1">{{ __('Entry') }}: {{ $reviewFinding->entry_type }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Finding entry type')"
                        :text="__(
                            'Scanner classification of this code occurrence: normal, UI, dynamic, or dynamic multi.',
                        )"
                    />
                </flux:badge>
            @endif

            @if ($reviewFinding->function_name)
                <flux:badge
                    size="sm"
                    variant="subtle"
                >
                    <span class="mr-1">{{ __('Function') }}: {{ $reviewFinding->function_name }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Translation function')"
                        :text="__('')"
                    />
                </flux:badge>
            @endif

            <flux:badge
                size="sm"
                color="{{ $reviewFinding->status === 'active' ? 'green' : 'amber' }}"
            >
                <span class="mr-1">{{ __('ui.status') }}: {{ $reviewFinding->status }}</span>
                <x-ui.tooltip.simple
                    :header="__('Finding status')"
                    :text="__(
                        'Scanner classification of this code occurrence: active (still present) or stale (no longer detected).',
                    )"
                />
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
                <x-ui.tooltip.simple
                    :header="__('Key state')"
                    :text="__(
                        'Current workbench key linkage, review status and whether a translation key has already been set.',
                    )"
                />
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
                <span class="mr-1">{{ $reviewFinding->key_id ? __('Key linked') : __('Key missing') }}</span>
                <x-ui.tooltip.simple
                    :header="__('Translation key linkage')"
                    :text="__('Shows whether this finding is linked to a workbench translation key or not.')"
                />
            </flux:badge>

            <flux:badge
                size="sm"
                color="{{ $reviewKeyState['color'] }}"
            >
                <span class="mr-1">{{ $reviewKeyState['label'] }}</span>
                <x-ui.tooltip.simple
                    :header="__('ui.translation.translation-key-state')"
                    :text="__(
                        'Shows whether the translation key is missing, equal to the suggested key, or deliberately different.',
                    )"
                />
            </flux:badge>

            @if ($reviewFinding->key_status)
                <flux:badge
                    size="sm"
                    variant="subtle"
                >
                    <span class="mr-1">{{ __('Key') }}: {{ $reviewFinding->key_status }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Translation key status')"
                        :text="__(
                            'Shows the workbench review status for this finding: accepted, rejected, or pending.',
                        )"
                    />
                </flux:badge>
            @endif

            @if ($reviewFinding->review_status)
                <flux:badge
                    size="sm"
                    variant="subtle"
                >
                    <span class="mr-1">{{ __('Review') }}: {{ $reviewFinding->review_status }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Translation review status')"
                        :text="__(
                            'Shows the workbench review status for this finding: accepted, rejected, or pending.',
                        )"
                    />
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
                <span>{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_dynamic_review.classification') }}</span>
                <x-ui.tooltip.simple
                    :header="__('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_dynamic_review.classification')"
                    :text="__(
                        'Resolved review classification. Confirmed states have priority over scanner candidate states.',
                    )"
                />
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
                    <span class="mr-1">{{ __('Normal translation') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Normal translation')"
                        :text="__(
                            'This finding is treated as a normal translation: no UI or dynamic classification applies.',
                        )"
                    />
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
                    <span class="mr-1">{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings_table.candidate') }}: {{ $reviewFinding->candidate_type }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Scanner candidate')"
                        :text="__(
                            'Scanner candidate classification of this code occurrence: UI or dynamic. This is only a scanner suggestion and may be overridden by workbench review.',
                        )"
                    />
                </flux:badge>
            @elseif (!$reviewIsUiConfirmed && !$reviewIsDynamicConfirmed && !$reviewIsDynamicMultiConfirmed)
                <flux:badge
                    size="sm"
                    variant="subtle"
                >
                    <span class="mr-1">{{ __('No candidate') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('No scanner candidate')"
                        :text="__(
                            'Scanner candidate classification of this code occurrence: UI or dynamic. This is only a scanner suggestion and may be overridden by workbench review.',
                        )"
                    />
                </flux:badge>
            @endif

            @if ($reviewIsUiConfirmed)
                <flux:badge
                    size="sm"
                    color="green"
                >
                    <span class="mr-1">{{ __('isUI') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('ui.ui-translation')"
                        :text="__(
                            'This finding is treated as a UI translation: the displayed value is resolved from runtime data and needs structured UI handling.',
                        )"
                    />
                </flux:badge>
            @elseif ($reviewEffectiveUiCandidate)
                <flux:badge
                    size="sm"
                    color="amber"
                >
                    <span class="mr-1">{{ __('candidateUI') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('UI translation candidate')"
                        :text="__(
                            'This finding is a candidate for UI translation: the displayed value might be resolved from runtime data and may need structured UI handling.',
                        )"
                    />
                </flux:badge>
            @endif

            @if ($reviewIsUiRejected)
                <flux:badge
                    size="sm"
                    color="rose"
                >
                    <span class="mr-1">{{ __('No UI') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('No UI translation')"
                        :text="__(
                            'This finding is explicitly rejected as a UI translation: the displayed value is not resolved from runtime data and does not need structured UI handling.',
                        )"
                    />
                </flux:badge>
            @endif

            @if ($reviewIsDynamicMultiConfirmed)
                <flux:badge
                    size="sm"
                    color="cyan"
                >
                    <span class="mr-1">{{ __('Dynamic option list') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Dynamic option list')"
                        :text="__(
                            'This finding is treated as a dynamic multi entry: multiple runtime option values may need separate translations.',
                        )"
                    />
                </flux:badge>
            @elseif ($reviewIsDynamicConfirmed)
                <flux:badge
                    size="sm"
                    color="teal"
                >
                    <span class="mr-1">{{ __('Dynamic translation') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Dynamic translation')"
                        :text="__(
                            'This finding is treated as dynamic: the displayed value is resolved from runtime data and still needs structured dynamic data handling.',
                        )"
                    />
                </flux:badge>
            @elseif ($reviewEffectiveDynamicMulti)
                <flux:badge
                    size="sm"
                    color="cyan"
                >
                    <span class="mr-1">{{ __('Dynamic option candidate') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Dynamic option list candidate')"
                        :text="__(
                            'This finding is a candidate for dynamic multi: multiple runtime option values may need separate translations.',
                        )"
                    />
                </flux:badge>
            @elseif ($reviewEffectiveDynamicCandidate)
                <flux:badge
                    size="sm"
                    color="amber"
                >
                    <span class="mr-1">{{ __('Dynamic candidate') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Dynamic translation candidate')"
                        :text="__(
                            'This finding is a candidate for dynamic: the displayed value might be resolved from runtime data and may need structured dynamic data handling.',
                        )"
                    />
                </flux:badge>
            @endif

            @if ($reviewIsDynamicRejected)
                <flux:badge
                    size="sm"
                    color="rose"
                >
                    <span class="mr-1">{{ __('No dynamic') }}</span>
                    <x-ui.tooltip.simple
                        wire:click
                        :header="__('No dynamic translation')"
                        :text="__(
                            'This finding is explicitly rejected as a dynamic translation: the displayed value is not resolved from runtime data and does not need structured dynamic data handling.',
                        )"
                    />
                </flux:badge>
            @endif

            @if ($reviewFinding->dynamic_scope)
                <flux:badge
                    size="sm"
                    variant="subtle"
                >
                    <span class="mr-1">{{ __('Scope') }}: {{ $reviewFinding->dynamic_scope }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Dynamic scope')"
                        :text="__(
                            'Shows the scanner classification of this dynamic finding: global, local, or unknown.',
                        )"
                    />
                </flux:badge>
            @endif

            @if (filled($reviewDynamicDataState))
                <x-ui.tooltip.simple
                    :title="__('Dynamic data state')"
                    :text="__(
                        'Unstructured means this dynamic finding is identified, but its runtime values have not yet been normalized into dedicated dynamic translation value records.',
                    )"
                >
                    <flux:badge
                        size="sm"
                        color="{{ $reviewDynamicDataState === 'structured' ? 'green' : 'orange' }}"
                    >
                        <span
                            class="mr-1">{{ $reviewDynamicDataState === 'structured' ? __('Data structured') : __('Data unstructured') }}</span>
                        <x-ui.tooltip.simple
                            :header="__('Dynamic data state')"
                            :text="__(
                                'Shows whether this dynamic finding has structured runtime values or not: structured means the runtime values have been normalized into dedicated dynamic translation value records.',
                            )"
                        />
                    </flux:badge>
                </x-ui.tooltip.simple>
            @endif

            @if ($reviewDynamicQualification)
                <x-ui.tooltip.simple
                    :title="__('Dynamic qualification')"
                    :text="$reviewDynamicQualification['text']"
                >
                    <flux:badge
                        size="sm"
                        color="{{ $reviewDynamicQualification['color'] }}"
                    >
                        <span class="mr-1">{{ $reviewDynamicQualification['label'] }}</span>
                        <x-ui.tooltip.simple
                            :header="__('Dynamic qualification')"
                            :text="$reviewDynamicQualification['text']"
                        />
                    </flux:badge>
                </x-ui.tooltip.simple>
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
                <x-ui.tooltip.simple
                    :header="__('Seen timestamps')"
                    :text="__('First and latest scanner timestamps for this finding.')"
                />
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
                    <span class="mr-1">{{ __('First') }}:
                        {{ $reviewFirstSeenAt?->format('D, d.M.Y H:i') ?? $reviewFinding->first_seen_at }}</span>
                    <x-ui.tooltip.simple
                        :header="__('First seen')"
                        :text="__('Timestamp of the first scanner detection of this finding.')"
                    />
                </flux:badge>
            @endif

            @if ($reviewFinding->last_seen_at)
                <flux:badge
                    size="sm"
                    color="{{ $reviewSeenAtSameTime ? 'green' : 'orange' }}"
                >
                    <span class="mr-1">{{ __('Last') }}:
                        {{ $reviewLastSeenAt?->format('D, d.M.Y H:i') ?? $reviewFinding->last_seen_at }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Last seen')"
                        :text="__('Timestamp of the latest scanner detection of this finding.')"
                    />
                </flux:badge>
            @endif

            @if ($reviewLastSeenAt)
                <flux:badge
                    size="sm"
                    color="{{ $reviewLastSeenAgeColor }}"
                >
                    <span class="mr-1">{{ __('ui.date-time.ago') }}: {{ $reviewLastSeenAt->diffForHumans() }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Last seen age')"
                        :text="__('Time since the latest scanner detection of this finding.')"
                    />
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
                        <span>{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_states.classification_review') }}</span>
                        <x-ui.tooltip.simple
                            :header="__('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_states.classification_review')"
                            :text="__('Review controls for accepting or rejecting UI and dynamic classifications.')"
                        />
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
                                <div class="col-span-1 inline-flex items-center gap-1.5 font-medium">
                                    <span>{{ __('Candidate Evaluation') }}!</span>
                                    <x-ui.tooltip.simple
                                        :header="__('Candidate reason')"
                                        :text="__('Shows the scanner candidate reason for this finding, if any.')"
                                    />
                                </div>
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

            <div class="grid gap-3 self-start md:grid-cols-2 xl:col-span-2">
                <flux:field>
                    <flux:label>
                        <span class="mr-1">{{ __('ui.ui-translation-state') }}</span>
                        <x-ui.tooltip.simple
                            :header="__('ui.ui-translation-state')"
                            :text="__(
                                'UI state is derived from the saved translation key. A key starting with ui. is treated as UI; every other key is not UI.',
                            )"
                        />
                    </flux:label>
                    <div class="grid gap-2">
                        <div class="flex flex-wrap items-center gap-1.5">
                            @if ($reviewIsUiCandidate && !$reviewIsUiConfirmed)
                                <flux:badge
                                    size="sm"
                                    color="violet"
                                >
                                    {{ __('Candidate UI') }}
                                </flux:badge>
                            @endif

                            <flux:badge
                                size="sm"
                                color="{{ $reviewIsUiConfirmed ? 'green' : 'zinc' }}"
                            >
                                {{ $reviewIsUiConfirmed ? __('Is UI') : __('No UI key') }}
                            </flux:badge>

                            @if ($reviewUiReviewDisabled)
                                <flux:badge
                                    size="sm"
                                    color="teal"
                                >
                                    {{ __('Dynamic finding') }}
                                </flux:badge>
                            @endif
                        </div>

                        <flux:callout.text class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ __(
                                'To classify this as UI, edit the translation key and save it with the ui. namespace. The review controls no longer set UI yes/no separately.',
                            ) }}
                        </flux:callout.text>

                        <flux:button
                            class="w-fit"
                            type="button"
                            icon="key-round"
                            size="xs"
                            variant="subtle"
                            wire:click="openTranslationKeyModal({{ $reviewFinding->id }})"
                        >
                            {{ __('Edit translation key') }}
                        </flux:button>
                    </div>
                </flux:field>
                {{--
                    Dynamic type review is intentionally parked.

                    Dynamic text findings are now handled as dynamic values, while
                    numeric dynamic findings are excluded from the translation-value
                    workflow by the scanner/foundation sync. Keep this block here
                    for short-term reference while the review workflow settles.

                @if ($reviewDynamicQualification !== null || $reviewIsDynamicCandidate || filled($reviewDynamicDataState))
                    <flux:field>
                        <flux:label>
                            <span class="mr-1 font-medium">
                                {{ __('Dynamic type') }}?
                            </span>
                            <x-ui.tooltip.simple
                                :header="__('Dynamic translation review')"
                                :text="__(
                                    'Select whether this finding is a single dynamic translation or a dynamic multi option list.',
                                )"
                            />
                        </flux:label>
                        <flux:radio.group
                            class="buergerfrs-checkbox-cards-compact grid grid-cols-2 content-start"
                            variant="cards"
                            x-model="dynamicMode"
                        >
                            <flux:radio
                                class="col-span-1 hover:cursor-pointer"
                                value="single"
                                x-on:click="dynamicMode = 'single'; uiCandidate = 'no'"
                                wire:click="setDynamicReviewMode({{ $reviewFinding->id }}, 'single')"
                                wire:loading.attr="disabled"
                                wire:target="setDynamicReviewMode"
                            >
                                <div class="flex-1">
                                    <flux:heading
                                        class="{{ $reviewIsDynamicConfirmed && !$reviewIsDynamicMultiConfirmed ? 'text-green-500' : '' }} leading-4"
                                    >
                                        <div class="col-span-1 inline-flex items-center gap-1.5 font-medium">
                                            <span class="font-medium">
                                                {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.dynamic') }}
                                            </span>
                                            <x-ui.tooltip.simple
                                                :header="__('Accept dynamic translation')"
                                                :text="__(
                                                    'Accept this finding as a single dynamic translation candidate.',
                                                )"
                                            />
                                        </div>
                                    </flux:heading>
                                </div>
                                <flux:radio.indicator />
                            </flux:radio>

                            <flux:radio
                                class="col-span-1 hover:cursor-pointer"
                                value="multi"
                                x-on:click="dynamicMode = 'multi'; uiCandidate = 'no'"
                                wire:click="setDynamicReviewMode({{ $reviewFinding->id }}, 'multi')"
                                wire:loading.attr="disabled"
                                wire:target="setDynamicReviewMode"
                            >
                                <div class="flex-1">
                                    <flux:heading
                                        class="{{ $reviewIsDynamicMultiConfirmed || $reviewEffectiveDynamicMulti ? 'text-green-500' : '' }} leading-4"
                                    >
                                        <div class="col-span-1 inline-flex items-center gap-1.5 font-medium">
                                            <span class="font-medium">
                                                {{ __('DynamicMulti') }}
                                            </span>
                                            <x-ui.tooltip.simple
                                                :header="__('Accept dynamic multi translation')"
                                                :text="__(
                                                    'Accept this finding as a dynamic multi translation candidate.',
                                                )"
                                            />
                                        </div>
                                    </flux:heading>
                                </div>
                                <flux:radio.indicator />
                            </flux:radio>
                        </flux:radio.group>
                    </flux:field>
                @endif
                --}}
            </div>
        </div>
    </flux:callout>
</div>
