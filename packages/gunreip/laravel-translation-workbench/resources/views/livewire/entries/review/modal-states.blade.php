{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-states.blade.php --}}

@php
    $reviewFindingSuggestedKey = trim((string) ($reviewFinding->suggested_key ?? ''));
    $reviewKeySuggestedKey = trim((string) ($reviewFinding->key_suggested_key ?? ''));
    $reviewTranslationKey = trim((string) ($reviewFinding->translation_key ?? ''));
    $reviewEffectiveSuggestedKey = $reviewKeySuggestedKey !== '' ? $reviewKeySuggestedKey : $reviewFindingSuggestedKey;
    $reviewKeyState = match (true) {
        $reviewTranslationKey === '' => [
            'label' => __('Translation key missing'),
            'color' => 'amber',
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
@endphp

<div class="grid gap-3 xl:grid-cols-4">
    <flux:callout
        class="xl:col-span-1"
        color="sky"
        icon="scan-search"
    >
        <flux:callout.heading>{{ __('Finding state') }}</flux:callout.heading>
        <flux:callout.text>
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
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        class="xl:col-span-1"
        color="indigo"
        icon="key-round"
    >
        <flux:callout.heading>{{ __('Key state') }}</flux:callout.heading>
        <flux:callout.text>
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
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        class="xl:col-span-1"
        color="violet"
        icon="tag"
    >
        <flux:callout.heading>{{ __('Classification') }}</flux:callout.heading>
        <flux:callout.text>
            <div class="flex flex-wrap gap-1.5">
                @if ($reviewFinding->candidate_type)
                    <flux:badge
                        size="sm"
                        color="amber"
                    >
                        {{ __('Candidate') }}: {{ $reviewFinding->candidate_type }}
                    </flux:badge>
                @else
                    <flux:badge
                        size="sm"
                        variant="subtle"
                    >
                        {{ __('No candidate') }}
                    </flux:badge>
                @endif

                @if ($reviewFinding->is_ui_key)
                    <flux:badge
                        size="sm"
                        color="green"
                    >
                        {{ __('Is UI') }}
                    </flux:badge>
                @endif

                @if ($reviewFinding->is_dynamic_key)
                    <flux:badge
                        size="sm"
                        color="teal"
                    >
                        {{ __('Is dynamic') }}
                    </flux:badge>
                @endif

                @if ($reviewFinding->is_dynamic_multi)
                    <flux:badge
                        size="sm"
                        color="cyan"
                    >
                        {{ __('Dynamic multi') }}
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
            </div>

            @if ($reviewFinding->candidate_reason)
                <div class="wrap-anywhere mt-3 text-wrap text-xs text-zinc-500">
                    {{ $reviewFinding->candidate_reason }}
                </div>
            @endif
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        class="xl:col-span-1"
        color="zinc"
        icon="clock-check"
    >
        <flux:callout.heading>{{ __('Seen state') }}</flux:callout.heading>
        <flux:callout.text>
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
        </flux:callout.text>
    </flux:callout>
</div>
