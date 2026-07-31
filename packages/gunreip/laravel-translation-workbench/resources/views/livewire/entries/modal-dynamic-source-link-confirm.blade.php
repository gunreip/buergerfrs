{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-dynamic-source-link-confirm.blade.php --}}

<flux:modal
    class="w-full max-w-7xl"
    name="translation-workbench-dynamic-source-link-confirm"
    wire:model="dynamicSourceLinkConfirmModalOpen"
>
    <div class="space-y-4">
        <div class="flex items-start gap-3">
            <div class="min-w-0 space-y-1">
                <flux:heading
                    size="xl"
                    level="3"
                >
                    {{ __('Confirm dynamic source link') }}
                </flux:heading>

                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Confirm that the observed runtime options belong to the selected related dynamic finding.') }}
                </flux:text>
            </div>
        </div>

        @if ($dynamicSourceLinkPreview)
            @php
                $linkPreviewRuntimeSources = collect($dynamicSourceLinkPreview['runtime_sources'] ?? []);
                $linkPreviewRelatedSource = $dynamicSourceLinkPreview['related_source'] ?? [];
                $linkPreviewFinding = $dynamicSourceLinkPreview['finding'] ?? null;
                $linkPreviewIsLinked = ($linkPreviewRelatedSource['link_review_status'] ?? null) === 'confirmed';
            @endphp

            <flux:callout
                color="{{ $linkPreviewIsLinked ? 'green' : 'amber' }}"
                icon="{{ $linkPreviewIsLinked ? 'link' : 'circle-question-mark' }}"
            >
                <div class="flex flex-wrap items-center gap-2">
                    <flux:callout.heading>{{ __('Current state') }}</flux:callout.heading>
                    <flux:badge
                        size="sm"
                        color="{{ $linkPreviewIsLinked ? 'green' : 'amber' }}"
                    >
                        {{ $linkPreviewIsLinked ? __('Link confirmed') : __('Not linked') }}
                    </flux:badge>
                </div>

                <flux:callout.text>
                    {{ $linkPreviewIsLinked
                        ? __('This relation is currently confirmed. You can keep it as-is or unlink it. No translation values will be changed.')
                        : __('This relation is not confirmed yet. Confirming it will store a reviewed relation without changing translation values.') }}
                </flux:callout.text>
            </flux:callout>

            <div class="grid gap-3 lg:grid-cols-2">
                <flux:callout
                    color="cyan"
                    icon="database-zap"
                >
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:callout.heading>{{ __('Runtime options') }}</flux:callout.heading>
                        <flux:badge
                            size="sm"
                            variant="subtle"
                        >
                            {{ __('Rows') }}: {{ $linkPreviewRuntimeSources->count() }}
                        </flux:badge>
                    </div>

                    <flux:callout.text class="text-xs">
                        {{ __('These option rows were observed while the application rendered the dynamic option source.') }}
                    </flux:callout.text>

                    <div class="mt-3 space-y-2">
                        @foreach ($linkPreviewRuntimeSources as $runtimeSource)
                            <div class="space-y-1">
                                <div class="flex flex-wrap gap-1">
                                    <flux:badge
                                        size="sm"
                                        color="sky"
                                    >
                                        {{ __('ui.source') }} #{{ $runtimeSource['id'] }}
                                    </flux:badge>

                                    @if ($runtimeSource['key_id'])
                                        <flux:badge
                                            size="sm"
                                            color="cyan"
                                        >
                                            {{ __('Key') }} #{{ $runtimeSource['key_id'] }}
                                        </flux:badge>
                                    @endif

                                    <flux:badge size="sm">
                                        {{ __('ui.values.values') }}: {{ $runtimeSource['values_count'] }}
                                    </flux:badge>
                                </div>

                                @if ($runtimeSource['suggested_key'])
                                    <div class="wrap-anywhere font-mono text-xs text-cyan-700 dark:text-cyan-300">
                                        {{ $runtimeSource['suggested_key'] }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </flux:callout>

                <flux:callout
                    color="sky"
                    icon="scan-search"
                >
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:callout.heading>{{ __('Related dynamic finding') }}</flux:callout.heading>
                        <flux:badge
                            size="sm"
                            variant="subtle"
                        >
                            {{ __('ui.source') }} #{{ $linkPreviewRelatedSource['id'] ?? '—' }}
                        </flux:badge>
                    </div>

                    <flux:callout.text class="text-xs">
                        {{ __('This scanner/discovery source will be linked to the runtime options above.') }}
                    </flux:callout.text>

                    <div class="mt-3 space-y-2">
                        <div class="flex flex-wrap gap-1">
                            @if ($linkPreviewRelatedSource['key_id'] ?? null)
                                <flux:badge
                                    size="sm"
                                    color="cyan"
                                >
                                    {{ __('Key') }} #{{ $linkPreviewRelatedSource['key_id'] }}
                                </flux:badge>
                            @endif

                            @if ($linkPreviewRelatedSource['classification'] ?? null)
                                <flux:badge
                                    size="sm"
                                    color="green"
                                >
                                    {{ $linkPreviewRelatedSource['classification'] }}
                                </flux:badge>
                            @endif

                            @if ($linkPreviewRelatedSource['values_count'] ?? null)
                                <flux:badge size="sm">
                                    {{ __('ui.values.values') }}: {{ $linkPreviewRelatedSource['values_count'] }}
                                </flux:badge>
                            @endif
                        </div>

                        @if ($linkPreviewRelatedSource['suggested_key'] ?? null)
                            <div class="wrap-anywhere font-mono text-xs text-sky-700 dark:text-sky-300">
                                {{ $linkPreviewRelatedSource['suggested_key'] }}
                            </div>
                        @endif

                        @if ($linkPreviewFinding)
                            <div class="wrap-anywhere text-xs text-zinc-500 dark:text-zinc-400">
                                {{ __('Review finding') }} #{{ $linkPreviewFinding->id }}
                            </div>
                        @endif
                    </div>
                </flux:callout>
            </div>

            <flux:callout
                color="{{ $linkPreviewIsLinked ? 'green' : 'amber' }}"
                icon="{{ $linkPreviewIsLinked ? 'link' : 'circle-question-mark' }}"
            >
                <flux:callout.heading>
                    {{ $linkPreviewIsLinked ? __('Review link decision') : __('Save this link?') }}
                </flux:callout.heading>
                <flux:callout.text>
                    {{ $linkPreviewIsLinked
                        ? __('Choose Unlink to remove the active relation, or Keep link to leave the confirmed decision unchanged.')
                        : __('Choose Confirm link to store that the runtime options belong to the selected related dynamic finding.') }}
                </flux:callout.text>
            </flux:callout>

            <div class="flex justify-end gap-2">
                <flux:button
                    type="button"
                    variant="ghost"
                    wire:click="closeDynamicSourceLinkConfirm"
                >
                    {{ $linkPreviewIsLinked ? __('Keep link') : __('ui.cancel') }}
                </flux:button>

                @if ($linkPreviewIsLinked)
                    <flux:button
                        type="button"
                        variant="danger"
                        icon="unlink"
                        wire:click="unlinkDynamicSourceLink"
                    >
                        {{ __('Unlink') }}
                    </flux:button>
                @else
                    <flux:button
                        type="button"
                        variant="primary"
                        color="cyan"
                        icon="check"
                        wire:click="confirmDynamicSourceLink"
                    >
                        {{ __('Confirm link') }}
                    </flux:button>
                @endif
            </div>
        @else
            <flux:callout
                color="amber"
                icon="info"
            >
                <flux:callout.heading>{{ __('No link context available') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('The runtime options or related dynamic finding are no longer available.') }}
                </flux:callout.text>
            </flux:callout>
        @endif
    </div>
</flux:modal>
