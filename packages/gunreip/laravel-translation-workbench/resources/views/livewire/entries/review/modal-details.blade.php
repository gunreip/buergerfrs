{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/review/modal-details.blade.php --}}

@php
    $reviewLiteralText = trim((string) ($reviewFinding->literal_text ?? ''));
    $reviewLiteralSuggested = trim((string) ($reviewFinding->literal_text_suggested ?? ''));
    $reviewTranslationKey = trim((string) ($reviewFinding->translation_key ?? ''));
    $reviewFindingSuggestedKey = trim((string) ($reviewFinding->suggested_key ?? ''));
    $reviewKeySuggestedKey = trim((string) ($reviewFinding->key_suggested_key ?? ''));
    $reviewExistingKey = trim((string) ($reviewFinding->existing_key ?? ''));
    $reviewFoundTranslationKey = trim((string) ($reviewFinding->found_translation_key ?? ''));
    $reviewEffectiveSuggestedKey = $reviewKeySuggestedKey !== '' ? $reviewKeySuggestedKey : $reviewFindingSuggestedKey;
    $canEditTranslationKey = (bool) $reviewFinding->key_id;
    $reviewSourceAbsolutePath = str_replace('\\', '/', base_path($reviewFinding->source_path));
    $reviewSourceEditorPath = str_replace('%2F', '/', rawurlencode($reviewSourceAbsolutePath));
    $reviewSourceEditorLine = $reviewFinding->source_line ? ':' . $reviewFinding->source_line : '';
    $reviewSourceEditorUrl =
        'vscode://vscode-remote/wsl+' .
        rawurlencode((string) config('translation-workbench.editor.vscode_wsl_distro')) .
        $reviewSourceEditorPath .
        $reviewSourceEditorLine;
@endphp

<div class="mt-3 grid gap-3 xl:grid-cols-4">
    {{-- Callout Source --}}
    <flux:callout
        class="xl:col-span-1"
        color="sky"
        icon="file-code"
    >
        <flux:callout.heading>
            <span class="flex w-full items-center justify-between gap-2">
                <span class="inline-flex items-center gap-1.5">
                    <span>{{ __('Source') }}</span>
                    <flux:tooltip
                        content="{{ __('Scanned file path, line number and translation function found in the code.') }}"
                    >
                        <flux:icon.info class="size-3.5 text-zinc-400" />
                    </flux:tooltip>
                </span>
                <flux:tooltip content="{{ __('Open in VSC') }}">
                    <flux:button
                        class="h-5 w-5 shrink-0"
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="external-link"
                        icon:class="text-sky-500 dark:text-sky-400"
                        :href="$reviewSourceEditorUrl"
                        :aria-label="__('Open source in VS Code')"
                    />
                </flux:tooltip>
            </span>
        </flux:callout.heading>
        <flux:callout.text class="text-xs">
            {{ __('Shows the exact code location that produced this finding and opens it directly in VS Code.') }}
        </flux:callout.text>

        <flux:callout.text>
            <div class="space-y-2 text-sm">
                <div class="space-y-1">
                    <flux:callout.heading class="text-xs uppercase">
                        {{ __('Path') }}
                    </flux:callout.heading>
                    <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                        {{ $reviewFinding->source_path }}
                    </div>
                </div>

                <div class="flex flex-wrap gap-1.5">
                    <flux:badge
                        size="sm"
                        variant="subtle"
                    >
                        {{ __('Line') }} {{ $reviewFinding->source_line ?? 1 }}
                    </flux:badge>
                    @if ($reviewFinding->function_name)
                        <flux:badge
                            size="sm"
                            variant="subtle"
                        >
                            {{ $reviewFinding->function_name }}
                        </flux:badge>
                    @endif
                </div>
            </div>
        </flux:callout.text>
    </flux:callout>

    {{-- Callout Literal --}}
    <flux:callout
        class="xl:col-span-1"
        color="cyan"
        icon="scan-text"
    >
        <flux:callout.heading>
            <span class="inline-flex items-center gap-1.5">
                <span>{{ __('Literal') }}</span>
                <flux:tooltip
                    content="{{ __('Literal text extracted from the translation call or suggested from the raw expression.') }}"
                >
                    <flux:icon.info class="size-3.5 text-zinc-400" />
                </flux:tooltip>
            </span>
        </flux:callout.heading>
        <flux:callout.text class="text-xs">
            {{ __('Shows the source value candidate that translations will be based on, plus the raw code expression when available.') }}
        </flux:callout.text>

        <flux:callout.text>
            <div class="space-y-3 text-sm">
                <div class="space-y-1">
                    <flux:callout.heading class="text-xs uppercase">
                        {{ $reviewLiteralText !== '' ? __('Literal text') : __('Literal suggested') }}
                    </flux:callout.heading>
                    <div class="wrap-anywhere text-wrap text-xs text-zinc-500">
                        {{ $reviewLiteralText ?: $reviewLiteralSuggested ?: __('No literal') }}
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        <flux:badge
                            size="sm"
                            color="{{ $reviewLiteralText !== '' ? 'green' : 'amber' }}"
                        >
                            {{ $reviewLiteralText !== '' ? __('Saved literal') : __('Suggested only') }}
                        </flux:badge>
                    </div>
                </div>

                @if ($reviewFinding->raw_expression)
                    <div class="space-y-1">
                        <flux:callout.heading class="text-xs uppercase">
                            {{ __('Raw expression') }}
                        </flux:callout.heading>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFinding->raw_expression }}
                        </div>
                    </div>
                @endif
            </div>
        </flux:callout.text>
    </flux:callout>

    {{-- Callout Keys --}}
    <flux:callout
        class="xl:col-span-1"
        color="{{ $reviewTranslationKey === '' ? 'red' : 'indigo' }}"
        icon="key-round"
    >
        <flux:callout.heading>
            <span class="flex w-full items-center justify-between gap-2">
                <span class="inline-flex items-center gap-1.5">
                    <span>{{ __('Keys') }}</span>
                    <flux:tooltip
                        content="{{ __('Current, suggested, existing and directly found translation keys for this finding.') }}"
                    >
                        <flux:icon.info class="size-3.5 text-zinc-400" />
                    </flux:tooltip>
                </span>
                <flux:tooltip content="{{ __('Review and edit translation key') }}">
                    <flux:button
                        class="h-5 w-5 shrink-0"
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="square-pen"
                        icon:class="text-indigo-500 dark:text-indigo-400"
                        :disabled="!$canEditTranslationKey"
                        :aria-label="__('Review and edit translation key')"
                        wire:click="openTranslationKeyModal({{ $reviewFinding->id }})"
                    />
                </flux:tooltip>
            </span>
        </flux:callout.heading>
        <flux:callout.text class="text-xs">
            {{ __('Shows the reviewed key state and the alternative key sources that can guide the key decision.') }}
        </flux:callout.text>

        <flux:callout.text>
            <div class="space-y-3 text-sm">
                <div class="space-y-1">
                    <flux:callout.heading class="text-xs uppercase">
                        {{ __('Translation key') }}
                    </flux:callout.heading>
                    <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                        {{ $reviewTranslationKey ?: __('Missing') }}
                    </div>
                </div>

                <div class="space-y-1">
                    <flux:callout.heading class="text-xs uppercase">
                        {{ __('Suggested key') }}
                    </flux:callout.heading>
                    <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                        {{ $reviewEffectiveSuggestedKey ?: __('Missing') }}
                    </div>
                </div>

                @if ($reviewExistingKey !== '')
                    <div class="space-y-1">
                        <flux:callout.heading class="text-xs uppercase">
                            {{ __('Existing key') }}
                        </flux:callout.heading>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewExistingKey }}
                        </div>
                    </div>
                @endif

                @if ($reviewFoundTranslationKey !== '')
                    <div class="space-y-1">
                        <div class="text-[11px] font-semibold uppercase text-zinc-500">
                            {{ __('Found translation key') }}
                        </div>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFoundTranslationKey }}
                        </div>
                    </div>
                @endif
            </div>
        </flux:callout.text>
    </flux:callout>

    {{-- Callout Structure --}}
    <flux:callout
        class="xl:col-span-1"
        color="violet"
        icon="folder-tree"
    >
        <flux:callout.heading>
            <span class="inline-flex items-center gap-1.5">
                <span>{{ __('Structure') }}</span>
                <flux:tooltip
                    content="{{ __('Namespace, group, path key and scope derived from the scanner and linked workbench key.') }}"
                >
                    <flux:icon.info class="size-3.5 text-zinc-400" />
                </flux:tooltip>
            </span>
        </flux:callout.heading>
        <flux:callout.text class="text-xs">
            {{ __('Shows how the finding and linked key are structurally grouped for filtering, review and later language file export.') }}
        </flux:callout.text>

        <flux:callout.text>
            <div class="space-y-3 text-sm">
                <div class="grid gap-2 md:grid-cols-2">
                    <div class="space-y-1">
                        <flux:callout.heading class="text-xs uppercase">
                            {{ __('Namespace') }}
                        </flux:callout.heading>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFinding->namespace ?: __('Missing') }}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <flux:callout.heading class="text-xs uppercase">
                            {{ __('Group') }}
                        </flux:callout.heading>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFinding->group ?: __('Missing') }}
                        </div>
                    </div>
                </div>

                <div class="grid gap-2 md:grid-cols-2">
                    <div class="space-y-1">
                        <flux:callout.heading class="text-xs uppercase">
                            {{ __('Path key') }}
                        </flux:callout.heading>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFinding->path_key ?: __('Missing') }}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <flux:callout.heading class="text-xs uppercase">
                            {{ __('Scope') }}
                        </flux:callout.heading>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFinding->scope ?: __('Missing') }}
                        </div>
                    </div>
                </div>

                @if ($reviewFinding->key_namespace || $reviewFinding->key_group)
                    <div class="border-t border-zinc-200 pt-3 dark:border-zinc-700">
                        <flux:callout.heading class="mb-2 text-xs uppercase">
                            {{ __('Linked key structure') }}
                        </flux:callout.heading>
                        <div class="grid gap-2 md:grid-cols-2">
                            <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                                {{ $reviewFinding->key_namespace ?: __('Missing') }}
                            </div>
                            <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                                {{ $reviewFinding->key_group ?: __('Missing') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </flux:callout.text>
    </flux:callout>
</div>
