{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-suspicious-key-review.blade.php --}}

@php
    $context = $suspiciousKeyReview ?? null;
    $row = is_array($context['row'] ?? null) ? $context['row'] : null;
    $finding = $context['finding'] ?? null;
    $key = $context['key'] ?? null;
    $latestReview = $context['latest_review'] ?? null;
@endphp

<flux:modal
    class="w-[min(76rem,calc(100vw-4rem))] max-w-full"
    wire:model.self="suspiciousKeyReviewModalOpen"
>
    <div class="space-y-4">
        <x-ui.headers.card
            :title="__('Review suspicious key provenance')"
            :description="__(
                'Decide whether this direct translation-key call is an accepted existing key, needs key review, or must later be restored to a literal.',
            )"
        >
            <div class="mr-8 flex flex-wrap items-center justify-end gap-2">
                @if ($finding)
                    <flux:badge size="sm">F#{{ $finding->id }}</flux:badge>
                @endif
                @if ($key)
                    <flux:badge size="sm">K#{{ $key->id }}</flux:badge>
                @endif
                @if ($latestReview)
                    <flux:badge
                        size="sm"
                        color="green"
                    >
                        {{ __('Reviewed') }}:
                        {{ str((string) $latestReview->decision)->replace('_', ' ')->headline() }}
                    </flux:badge>
                @else
                    <flux:badge
                        size="sm"
                        color="red"
                    >
                        {{ __('Review needed') }}
                    </flux:badge>
                @endif
            </div>
        </x-ui.headers.card>

        @if (!$row)
            <flux:callout
                color="amber"
                icon="file-exclamation-point"
            >
                <flux:callout.heading>{{ __('Review context unavailable') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('Refresh the suspicious-key report and open this review again.') }}
                </flux:callout.text>
            </flux:callout>
        @else
            <div class="grid grid-cols-1 gap-3 xl:grid-cols-3">
                <flux:callout
                    class="xl:col-span-2"
                    color="amber"
                    icon="key-round"
                >
                    <flux:callout.heading>{{ __('ui.translation.translation-key') }}</flux:callout.heading>
                    <flux:callout.text>
                        <div class="wrap-anywhere font-mono text-xs leading-relaxed">
                            {{ $row['translation_key'] }}
                        </div>
                    </flux:callout.text>
                </flux:callout>

                <flux:callout
                    color="{{ !empty($row['source_lang_value_exists']) ? 'green' : 'red' }}"
                    icon="{{ !empty($row['source_lang_value_exists']) ? 'circle-check' : 'octagon-alert' }}"
                >
                    <flux:callout.heading>{{ __('Source lang value') }}</flux:callout.heading>
                    <flux:callout.text>
                        <div class="flex flex-wrap gap-1">
                            <flux:badge
                                size="sm"
                                color="{{ !empty($row['source_lang_value_exists']) ? 'green' : 'red' }}"
                            >
                                {{ $row['source_locale'] ?? 'en' }}:
                                {{ !empty($row['source_lang_value_exists']) ? __('available') : __('missing') }}
                            </flux:badge>
                            @if (!empty($row['source_lang_status']))
                                <flux:badge size="sm">
                                    {{ __('ui.state.state') }}: {{ $row['source_lang_status'] }}
                                </flux:badge>
                            @endif
                        </div>
                    </flux:callout.text>
                </flux:callout>
            </div>

            <div class="grid grid-cols-1 gap-3 xl:grid-cols-2">
                <flux:callout
                    color="sky"
                    icon="map-pin"
                >
                    <flux:callout.heading>{{ __('ui.source.source') }}</flux:callout.heading>
                    <flux:callout.text>
                        <div class="space-y-2">
                            <div class="wrap-anywhere font-mono text-xs leading-relaxed">
                                {{ $row['source_path'] }}@if (!empty($row['source_line']))
                                    :{{ $row['source_line'] }}
                                @endif
                            </div>
                            <div
                                class="wrap-anywhere border-t border-zinc-200 pt-2 font-mono text-xs leading-relaxed text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                                {{ $row['raw_expression'] }}
                            </div>
                        </div>
                    </flux:callout.text>
                </flux:callout>

                <flux:callout
                    color="zinc"
                    icon="languages"
                >
                    <flux:callout.heading>{{ __('Suggested context') }}</flux:callout.heading>
                    <flux:callout.text>
                        <div class="space-y-2">
                            <div class="wrap-anywhere font-mono text-xs leading-relaxed">
                                {{ $row['suggested_key'] }}
                            </div>
                            @if (!empty($row['literal_text_suggested']))
                                <div class="wrap-anywhere text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ __('Literal hint') }}: {{ $row['literal_text_suggested'] }}
                                </div>
                            @endif
                        </div>
                    </flux:callout.text>
                </flux:callout>
            </div>

            <div class="grid grid-cols-1 gap-3 xl:grid-cols-4">
                @foreach ([['title' => __('Key records'), 'count' => $row['key_record_count'] ?? 0, 'color' => ((int) ($row['key_record_count'] ?? 0)) > 0 ? 'sky' : 'amber', 'icon' => 'key-round'], ['title' => __('Active usage'), 'count' => $row['active_usage_count'] ?? 0, 'color' => ((int) ($row['active_usage_count'] ?? 0)) > 1 ? 'green' : 'amber', 'icon' => 'link'], ['title' => __('Reviewed usage'), 'count' => $row['reviewed_usage_count'] ?? 0, 'color' => ((int) ($row['reviewed_usage_count'] ?? 0)) > 0 ? 'green' : 'zinc', 'icon' => 'badge-check'], ['title' => __('Direct code usage'), 'count' => $row['direct_code_usage_count'] ?? 0, 'color' => ((int) ($row['direct_code_usage_count'] ?? 0)) > 1 ? 'sky' : 'zinc', 'icon' => 'code']] as $callout)
                    <flux:callout
                        color="{{ $callout['color'] }}"
                        icon="{{ $callout['icon'] }}"
                    >
                        <flux:callout.heading>{{ $callout['title'] }}</flux:callout.heading>
                        <flux:callout.text>
                            <span class="text-lg font-semibold tabular-nums">
                                {{ number_format((int) $callout['count']) }}
                            </span>
                        </flux:callout.text>
                    </flux:callout>
                @endforeach
            </div>

            @if (!empty($row['source_lang_value']))
                <flux:callout
                    color="green"
                    icon="scroll-text"
                >
                    <flux:callout.heading>{{ __('Source value') }}</flux:callout.heading>
                    <flux:callout.text>
                        <div class="wrap-anywhere text-sm leading-relaxed">
                            {{ $row['source_lang_value'] }}
                        </div>
                    </flux:callout.text>
                </flux:callout>
            @endif

            <flux:callout
                color="amber"
                icon="clipboard-check"
            >
                <flux:callout.heading>{{ __('Provenance decision') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('This decision documents how the current direct translation-key call should be interpreted. It does not change source files or lang files.') }}
                </flux:callout.text>

                <div class="mt-3 grid grid-cols-1 gap-2 xl:grid-cols-4">
                    <flux:button
                        type="button"
                        size="sm"
                        variant="primary"
                        color="green"
                        icon="badge-check"
                        wire:click="reviewSuspiciousKeyProvenance('mark_as_valid_existing_key')"
                    >
                        {{ __('Valid existing key') }}
                    </flux:button>
                    <flux:button
                        type="button"
                        size="sm"
                        variant="primary"
                        color="amber"
                        icon="key-round"
                        wire:click="reviewSuspiciousKeyProvenance('needs_key_review')"
                    >
                        {{ __('Needs key review') }}
                    </flux:button>
                    <flux:button
                        type="button"
                        size="sm"
                        variant="primary"
                        color="red"
                        icon="undo-2"
                        wire:click="reviewSuspiciousKeyProvenance('needs_literal_restore')"
                    >
                        {{ __('Restore literal later') }}
                    </flux:button>
                    <flux:button
                        type="button"
                        size="sm"
                        variant="subtle"
                        color="zinc"
                        icon="clock"
                        wire:click="reviewSuspiciousKeyProvenance('ignore_for_now')"
                    >
                        {{ __('Ignore for now') }}
                    </flux:button>
                </div>
            </flux:callout>
        @endif
    </div>
</flux:modal>
