{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/modal-resolve-export-conflict.blade.php --}}

@php
    $context = $exportConflictResolveContext ?? null;
    $conflict = is_array($context['conflict'] ?? null) ? $context['conflict'] : null;
    $blockedKey = $context['blocked_key'] ?? null;
    $blockingKey = $context['blocking_key'] ?? null;
    $blockingFindings = collect($context['blocking_findings'] ?? []);
    $blockedTranslationKey = (string) ($conflict['translation_key'] ?? '');
    $blockingTranslationKey = (string) ($conflict['blocking_translation_key'] ?? '');
    $blockingTranslationKeySegments = $blockingTranslationKey !== '' ? explode('.', $blockingTranslationKey) : [];
    $blockingTranslationKeyLastSegment =
        (string) ($blockingTranslationKeySegments[array_key_last($blockingTranslationKeySegments)] ?? '');
    $hasNestedConflictProposal =
        $blockedTranslationKey !== '' &&
        $blockingTranslationKey !== '' &&
        str_starts_with($blockedTranslationKey, $blockingTranslationKey . '.') &&
        $blockingTranslationKeyLastSegment !== '';
    $proposedBlockingTranslationKey = $hasNestedConflictProposal
        ? $blockingTranslationKey . '.' . $blockingTranslationKeyLastSegment
        : null;
    $usageStatus = (string) ($conflict['blocking_usage_status'] ?? '');
    $usageColor = match ($usageStatus) {
        'active' => 'green',
        'obsolete' => 'amber',
        'lang_file_only', 'missing_active_usage' => 'red',
        default => 'zinc',
    };
@endphp

{{-- Modal Export Resolved Conflicts --}}
<flux:modal
    class="w-[calc(100vw-4rem)] max-w-full"
    wire:model.self="exportConflictResolveModalOpen"
    scroll="body"
>
    <div class="space-y-4">
        <x-ui.headers.card
            :title="__('Resolve export conflict')"
            :description="__('Review why a Workbench translation value cannot be written into the Laravel lang file.')"
        >
            <div class="mr-8 flex flex-wrap items-center justify-end gap-2">
                @if ($conflict)
                    <flux:badge
                        size="sm"
                        color="red"
                    >
                        {{ __('Conflict') }}
                    </flux:badge>

                    <flux:badge size="sm">
                        {{ $conflict['locale'] ?? '—' }}
                    </flux:badge>

                    <flux:badge size="sm">
                        {{ $conflict['namespace'] ?? '—' }}
                    </flux:badge>

                    @if ($blockedKey)
                        <flux:badge
                            size="sm"
                            color="sky"
                        >
                            Blocked K#{{ $blockedKey->id }}
                        </flux:badge>
                    @endif

                    @if ($blockingKey)
                        <flux:badge
                            size="sm"
                            color="amber"
                        >
                            Blocking K#{{ $blockingKey->id }}
                        </flux:badge>
                    @endif
                @endif
            </div>
        </x-ui.headers.card>

        @if ($conflict)
            {{-- Callout Note --}}
            <flux:callout
                color="red"
                icon="triangle-alert"
            >
                <flux:callout.text>
                    {{ __('The existing lang key/value blocks the planned write because both entries would need the same scalar/array position in the lang file.') }}
                </flux:callout.text>
            </flux:callout>

            <div class="grid grid-cols-1 gap-3 xl:grid-cols-2">
                {{-- Callout Blocking Existing Value --}}
                <flux:callout
                    color="amber"
                    icon="ban"
                    heading="{{ __('Blocking existing value') }}"
                    text="{{ __('The existing lang key/value that blocks the planned write.') }}"
                >
                    <flux:field>
                        <div class="space-y-3">
                            <div>
                                <flux:callout.heading>
                                    {{ __('Blocking translation key') }}
                                </flux:callout.heading>
                                <flux:callout.text class="wrap-anywhere font-mono text-xs">
                                    {{ $conflict['blocking_translation_key'] ?? __('No matching workbench key') }}
                                </flux:callout.text>
                            </div>

                            <div>
                                <flux:callout.heading>
                                    {{ __('Blocking lang key') }}
                                </flux:callout.heading>
                                <flux:callout.text class="wrap-anywhere font-mono text-xs">
                                    {{ $conflict['blocking_lang_key'] ?? '—' }}
                                </flux:callout.text>
                            </div>

                            <div>
                                <flux:callout.heading>
                                    {{ __('Existing value') }}
                                </flux:callout.heading>
                                <flux:callout.text class="wrap-anywhere text-sm">
                                    @if (is_array($conflict['blocking_value'] ?? null))
                                        {{ $conflict['blocking_value']['type'] ?? 'array' }}
                                        ({{ number_format((int) ($conflict['blocking_value']['count'] ?? 0)) }})
                                    @else
                                        {{ $conflict['blocking_value'] ?? '—' }}
                                    @endif
                                </flux:callout.text>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <flux:badge
                                    size="sm"
                                    color="{{ $usageColor }}"
                                >
                                    {{ $usageStatus !== '' ? str($usageStatus)->replace('_', ' ')->title() : __('Unknown') }}
                                </flux:badge>

                                @if ($conflict['blocking_lang_value_id'] ?? null)
                                    <flux:badge size="sm">
                                        LV#{{ $conflict['blocking_lang_value_id'] }}
                                    </flux:badge>
                                @endif

                                @if ($blockingKey)
                                    <flux:badge
                                        size="sm"
                                        color="{{ $blockingKey->status === 'obsolete' ? 'amber' : 'green' }}"
                                    >
                                        {{ __('Blocking key') }}: {{ $blockingKey->status }}
                                    </flux:badge>
                                @endif
                            </div>
                        </div>
                    </flux:field>
                </flux:callout>

                {{-- Callout Blocked Planned Write --}}
                <flux:callout
                    color="red"
                    icon="file-x"
                    heading="{{ __('Blocked write') }}"
                    text="{{ __('The planned write is blocked by an existing translation key/value.') }}"
                >
                    {{-- <flux:callout.heading>{{ __('Blocked write') }}</flux:callout.heading> --}}
                    <flux:field>
                        <div class="space-y-3">
                            <div>
                                <flux:callout.heading>
                                    {{ __('ui.translation.translation-key') }}
                                </flux:callout.heading>
                                <flux:callout.text class="wrap-anywhere font-mono text-xs">
                                    {{ $conflict['translation_key'] ?? '—' }}
                                </flux:callout.text>
                            </div>

                            <div>
                                <flux:callout.heading>
                                    {{ __('Lang key') }}
                                </flux:callout.heading>
                                <flux:callout.text class="wrap-anywhere font-mono text-xs">
                                    {{ $conflict['lang_key'] ?? '—' }}
                                </flux:callout.text>
                            </div>

                            <div>
                                <flux:callout.heading>
                                    {{ __('Value to write') }}
                                </flux:callout.heading>
                                <flux:callout.text class="wrap-anywhere text-xs">
                                    @if (is_array($conflict['blocked_value'] ?? null))
                                        {{ $conflict['blocked_value']['type'] ?? 'array' }}
                                        ({{ number_format((int) ($conflict['blocked_value']['count'] ?? 0)) }})
                                    @else
                                        {{ $conflict['blocked_value'] ?? '—' }}
                                    @endif
                                </flux:callout.text>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <flux:badge
                                    size="sm"
                                    color="red"
                                >
                                    {{ str((string) ($conflict['reason'] ?? ''))->replace('_', ' ')->title() }}
                                </flux:badge>

                                @if ($blockedKey)
                                    <flux:badge
                                        size="sm"
                                        color="{{ $blockedKey->status === 'obsolete' ? 'amber' : 'green' }}"
                                    >
                                        {{ __('Blocked key') }}: {{ $blockedKey->status }}
                                    </flux:badge>
                                @endif
                            </div>
                        </div>
                    </flux:field>
                </flux:callout>
            </div>

            {{-- Callout Suggested Resolution --}}
            <flux:callout
                color="{{ $proposedBlockingTranslationKey ? 'green' : 'amber' }}"
                icon="{{ $proposedBlockingTranslationKey ? 'git-branch' : 'circle-question-mark' }}"
                heading="{{ __('Suggested resolution') }}"
                text="{{ __('After that move, the blocked translation key can be written as planned without colliding with the existing scalar value.') }}"
            >
                <flux:callout.text>
                    @if ($proposedBlockingTranslationKey)
                        <flux:field class="grid grid-cols-1 gap-3 xl:grid-cols-2">
                            <flux:field class="col-span-1">
                                <flux:callout.heading>
                                    {{ __('Move blocking key') }}
                                </flux:callout.heading>
                                <flux:callout.text class="wrap-anywhere font-mono text-xs">
                                    {{ $blockingTranslationKey }}
                                </flux:callout.text>
                            </flux:field>

                            <flux:field class="col-span-1">
                                <flux:callout.heading>
                                    {{ __('Proposed new key') }}
                                </flux:callout.heading>
                                <flux:callout.text class="wrap-anywhere font-mono text-xs">
                                    {{ $proposedBlockingTranslationKey }}
                                </flux:callout.text>
                            </flux:field>
                        </flux:field>
                    @else
                        {{ __('No automatic proposal is available for this conflict yet. Review the blocking and blocked keys manually.') }}
                    @endif
                </flux:callout.text>
            </flux:callout>

            {{-- Callout Blocking Key Usage --}}
            <flux:callout
                color="{{ $blockingFindings->isNotEmpty() ? 'sky' : 'amber' }}"
                icon="{{ $blockingFindings->isNotEmpty() ? 'map-pin' : 'circle-question-mark' }}"
            >
                <flux:callout.heading>
                    <span class="inline-flex flex-wrap items-center gap-2">
                        <span>{{ __('Blocking key usage') }}</span>
                        <flux:badge
                            size="sm"
                            color="{{ $blockingFindings->isNotEmpty() ? 'sky' : 'amber' }}"
                        >
                            {{ number_format($blockingFindings->count()) }}
                        </flux:badge>
                    </span>
                </flux:callout.heading>
                <flux:callout.text>
                    @if ($blockingFindings->isNotEmpty())
                        {{-- Table Blocking Key Usage --}}
                        <flux:table container:class="max-h-80">
                            {{-- Table Header Blocking Key Usage --}}
                            <flux:table.columns
                                class="bg-white dark:bg-zinc-700"
                                sticky
                            >
                                <flux:table.column class="w-24">{{ __('Finding') }}</flux:table.column>
                                <flux:table.column>{{ __('ui.source.source') }}</flux:table.column>
                                <flux:table.column class="w-32">{{ __('Kind') }}</flux:table.column>
                                <flux:table.column align="center">{{ __('ui.table.headers.actions') }}</flux:table.column>
                            </flux:table.columns>
                            {{-- Table Body Blocking Key Usage --}}
                            <flux:table.rows>
                                @foreach ($blockingFindings as $finding)
                                    @php
                                        $sourcePath = (string) ($finding->sourceFile?->path ?? '');
                                        $sourceLine = $finding->source_line ? ':' . $finding->source_line : '';
                                        $sourceAbsolutePath = base_path($sourcePath);
                                        $sourceEditorPath = str_replace('%2F', '/', rawurlencode($sourceAbsolutePath));
                                        $sourceEditorUrl =
                                            'vscode://vscode-remote/wsl+' .
                                            rawurlencode(
                                                (string) config('translation-workbench.editor.vscode_wsl_distro'),
                                            ) .
                                            $sourceEditorPath .
                                            $sourceLine;
                                    @endphp

                                    {{-- Table Body Row Blocking Key Usage --}}
                                    <flux:table.row>
                                        <flux:table.cell>
                                            <flux:badge size="sm">F#{{ $finding->id }}</flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <div class="flex min-w-0 items-start gap-2">
                                                @if ($sourcePath !== '')
                                                    <x-ui.tooltip.simple
                                                        :title="__('Open in VSC')"
                                                        :text="__(
                                                            'Opens the code location that currently uses the blocking translation key.',
                                                        )"
                                                    >
                                                        <flux:button
                                                            type="button"
                                                            size="xs"
                                                            variant="subtle"
                                                            icon="square-arrow-out-up-right"
                                                            :href="$sourceEditorUrl"
                                                        />
                                                    </x-ui.tooltip.simple>
                                                @endif

                                                <div class="min-w-0">
                                                    <div class="wrap-anywhere font-mono text-xs">
                                                        {{ $sourcePath !== '' ? $sourcePath : __('No source file') }}{{ $sourceLine }}
                                                    </div>
                                                    <div class="wrap-anywhere text-xs text-zinc-500 dark:text-zinc-400">
                                                        {{ $finding->raw_expression }}
                                                    </div>
                                                </div>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge size="sm">{{ $finding->kind }}</flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell align="center">
                                            <flux:button
                                                type="button"
                                                align="center"
                                                size="xs"
                                                variant="primary"
                                                color="sky"
                                                icon="scan-search"
                                                wire:click="openBlockingFindingReviewFromExportConflict({{ $finding->id }})"
                                            >
                                                {{ __('Review') }}
                                            </flux:button>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    @else
                        {{ __('No active Workbench finding currently points to the blocking key. The value may exist only in the lang file or may be stale.') }}
                    @endif
                </flux:callout.text>
            </flux:callout>
        @else
            {{-- Callout Conflict Context Unavailable --}}
            <flux:callout
                color="amber"
                icon="triangle-alert"
            >
                <flux:callout.heading>{{ __('Conflict context unavailable') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('Refresh the export report and open the conflict again.') }}
                </flux:callout.text>
            </flux:callout>
        @endif

        <div class="flex justify-end">
            {{-- Button Close --}}
            <flux:button
                type="button"
                variant="subtle"
                wire:click="closeExportConflictResolve"
            >
                {{ __('Close') }}
            </flux:button>
        </div>
    </div>
</flux:modal>
