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
    $canAcceptSuggestedKey = $reviewFinding->key_id && $reviewEffectiveSuggestedKey !== '';
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
    <flux:callout
        class="xl:col-span-1"
        color="sky"
        icon="file-code"
    >
        <flux:callout.heading>
            <span class="flex w-full items-center justify-between gap-2">
                <span>{{ __('Source') }}</span>
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
        <flux:callout.text>
            <div class="space-y-2 text-sm">
                <div class="space-y-1">
                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                        {{ __('Path') }}
                    </div>
                    <div class="wrap-anywhere text-wrap font-mono text-xs">
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

    <flux:callout
        class="xl:col-span-1"
        color="cyan"
        icon="scan-text"
    >
        <flux:callout.heading>{{ __('Literal') }}</flux:callout.heading>
        <flux:callout.text>
            <div class="space-y-3 text-sm">
                <div class="space-y-1">
                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                        {{ $reviewLiteralText !== '' ? __('Literal text') : __('Literal suggested') }}
                    </div>
                    <div class="wrap-anywhere text-wrap">
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
        class="xl:col-span-1"
        color="indigo"
        icon="key-round"
    >
        <flux:callout.heading>
            <span class="flex w-full items-center justify-between gap-2">
                <span>{{ __('Keys') }}</span>
                <flux:tooltip content="{{ __('Use suggested key as translation key') }}">
                    <flux:button
                        class="h-5 w-5 shrink-0"
                        type="button"
                        size="xs"
                        variant="ghost"
                        icon="copy-plus"
                        icon:class="text-indigo-500 dark:text-indigo-400"
                        :disabled="!$canAcceptSuggestedKey"
                        :aria-label="__('Use suggested key as translation key')"
                        wire:click="acceptSuggestedTranslationKey({{ $reviewFinding->id }})"
                    />
                </flux:tooltip>
            </span>
        </flux:callout.heading>
        <flux:callout.text>
            <div class="space-y-3 text-sm">
                <div class="space-y-1">
                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                        {{ __('Translation key') }}
                    </div>
                    <div class="wrap-anywhere text-wrap font-mono text-xs">
                        {{ $reviewTranslationKey ?: __('Missing') }}
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="text-[11px] font-semibold uppercase text-zinc-500">
                        {{ __('Suggested key') }}
                    </div>
                    <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                        {{ $reviewEffectiveSuggestedKey ?: __('Missing') }}
                    </div>
                </div>

                @if ($reviewExistingKey !== '')
                    <div class="space-y-1">
                        <div class="text-[11px] font-semibold uppercase text-zinc-500">
                            {{ __('Existing key') }}
                        </div>
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

    <flux:callout
        class="xl:col-span-1"
        color="violet"
        icon="folder-tree"
    >
        <flux:callout.heading>{{ __('Structure') }}</flux:callout.heading>
        <flux:callout.text>
            <div class="space-y-3 text-sm">
                <div class="grid gap-2 md:grid-cols-2">
                    <div class="space-y-1">
                        <div class="text-[11px] font-semibold uppercase text-zinc-500">
                            {{ __('Namespace') }}
                        </div>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFinding->namespace ?: __('Missing') }}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <div class="text-[11px] font-semibold uppercase text-zinc-500">
                            {{ __('Group') }}
                        </div>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFinding->group ?: __('Missing') }}
                        </div>
                    </div>
                </div>

                <div class="grid gap-2 md:grid-cols-2">
                    <div class="space-y-1">
                        <div class="text-[11px] font-semibold uppercase text-zinc-500">
                            {{ __('Path key') }}
                        </div>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFinding->path_key ?: __('Missing') }}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <div class="text-[11px] font-semibold uppercase text-zinc-500">
                            {{ __('Scope') }}
                        </div>
                        <div class="wrap-anywhere text-wrap font-mono text-xs text-zinc-500">
                            {{ $reviewFinding->scope ?: __('Missing') }}
                        </div>
                    </div>
                </div>

                @if ($reviewFinding->key_namespace || $reviewFinding->key_group)
                    <div class="border-t border-zinc-200 pt-3 dark:border-zinc-700">
                        <div class="mb-2 text-[11px] font-semibold uppercase text-zinc-500">
                            {{ __('Linked key structure') }}
                        </div>
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
