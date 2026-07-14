{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-details.blade.php --}}

<div class="mt-3 grid gap-3 xl:grid-cols-12">
    <flux:callout
        class="xl:col-span-4"
        color="sky"
        icon="scan-search"
    >
        <flux:callout.heading>{{ __('Finding details') }}</flux:callout.heading>
        <flux:callout.text>
            <div class="space-y-3 text-sm">
                <div class="space-y-1">
                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                        {{ __('Literal') }}
                    </div>
                    <div class="wrap-anywhere text-wrap">
                        {{ $reviewFinding->literal_text ?: $reviewFinding->literal_text_suggested ?: __('No literal') }}
                    </div>
                </div>

                @if ($reviewFinding->raw_expression)
                    <div class="space-y-1">
                        <div class="text-[11px] font-semibold uppercase text-zinc-500">
                            {{ __('Raw expression') }}
                        </div>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFinding->raw_expression }}
                        </div>
                    </div>
                @endif
            </div>
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        class="xl:col-span-4"
        color="indigo"
        icon="key-round"
    >
        <flux:callout.heading>{{ __('Key details') }}</flux:callout.heading>
        <flux:callout.text>
            <div class="space-y-3 text-sm">
                <div class="space-y-1">
                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                        {{ __('Translation key') }}
                    </div>
                    <div class="wrap-anywhere text-wrap font-mono text-xs">
                        {{ $reviewFinding->translation_key ?: __('Missing') }}
                    </div>
                </div>

                <div class="grid gap-2 md:grid-cols-2">
                    <div class="space-y-1">
                        <div class="text-[11px] font-semibold uppercase text-zinc-500">
                            {{ __('Suggested') }}
                        </div>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFinding->key_suggested_key ?: $reviewFinding->suggested_key ?: __('Missing') }}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <div class="text-[11px] font-semibold uppercase text-zinc-500">
                            {{ __('Existing') }}
                        </div>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFinding->existing_key ?: __('Missing') }}
                        </div>
                    </div>
                </div>

                @if ($reviewFinding->found_translation_key)
                    <div class="space-y-1">
                        <div class="text-[11px] font-semibold uppercase text-zinc-500">
                            {{ __('Found translation key') }}
                        </div>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFinding->found_translation_key }}
                        </div>
                    </div>
                @endif
            </div>
        </flux:callout.text>
    </flux:callout>

    <flux:callout
        class="xl:col-span-4"
        color="zinc"
        icon="file-code"
    >
        <flux:callout.heading>{{ __('Source details') }}</flux:callout.heading>
        <flux:callout.text>
            <div class="space-y-2 text-sm">
                <div class="wrap-anywhere text-wrap font-mono text-xs">
                    {{ $reviewFinding->source_path }}:{{ $reviewFinding->source_line ?? 1 }}
                </div>

                <div class="flex flex-wrap gap-1.5">
                    @if ($reviewFinding->first_seen_at)
                        <flux:badge
                            size="sm"
                            variant="subtle"
                        >
                            {{ __('First') }}: {{ $reviewFinding->first_seen_at }}
                        </flux:badge>
                    @endif

                    @if ($reviewFinding->last_seen_at)
                        <flux:badge
                            size="sm"
                            variant="subtle"
                        >
                            {{ __('Last') }}: {{ $reviewFinding->last_seen_at }}
                        </flux:badge>
                    @endif
                </div>
            </div>
        </flux:callout.text>
    </flux:callout>
</div>
