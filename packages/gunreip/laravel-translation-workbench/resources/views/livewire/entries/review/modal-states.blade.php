{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-states.blade.php --}}

<div class="grid gap-3 xl:grid-cols-12">
    <flux:callout
        class="xl:col-span-3"
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
        class="xl:col-span-3"
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
                    color="{{ $reviewFinding->translation_key ? 'green' : 'amber' }}"
                >
                    {{ $reviewFinding->translation_key ? __('Translation key') : __('Translation key missing') }}
                </flux:badge>

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
        class="xl:col-span-3"
        color="violet"
        icon="tag"
    >
        <flux:callout.heading>{{ __('Classification') }}</flux:callout.heading>
        <flux:callout.text>
            <div class="flex flex-wrap gap-1.5">
                @if ($reviewFinding->candidate_type)
                    <flux:badge
                        size="sm"
                        color="violet"
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
                        color="sky"
                    >
                        {{ __('UI') }}
                    </flux:badge>
                @endif

                @if ($reviewFinding->is_dynamic_key)
                    <flux:badge
                        size="sm"
                        color="teal"
                    >
                        {{ __('Dynamic') }}
                    </flux:badge>
                @endif

                @if ($reviewFinding->is_dynamic_multi)
                    <flux:badge
                        size="sm"
                        color="cyan"
                    >
                        {{ __('Multi') }}
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
        class="xl:col-span-3"
        color="zinc"
        icon="clock-check"
    >
        <flux:callout.heading>{{ __('Seen state') }}</flux:callout.heading>
        <flux:callout.text>
            <div class="flex flex-wrap gap-1.5">
                @if ($reviewFinding->first_seen_at)
                    <flux:badge
                        size="sm"
                        variant="subtle"
                    >
                        {{ __('First seen') }}
                    </flux:badge>
                @endif

                @if ($reviewFinding->last_seen_at)
                    <flux:badge
                        size="sm"
                        color="green"
                    >
                        {{ __('Last seen') }}
                    </flux:badge>
                @endif
            </div>
        </flux:callout.text>
    </flux:callout>
</div>

