{{-- Diagnostic provenance only: the slot remains unwrapped in the graph. --}}
@props(['view', 'layout' => null])
@php
    $fileMarkup = (string) $slot;
    $fileTokens = \Gunreip\TranslationWorkbench\Support\TwGraph\FileRegion::tokens($fileMarkup);
    $fileViewPath = realpath(view($view)->getPath()) ?: view($view)->getPath();
    $fileLayoutPath = $layout ? base_path($layout) : null;
    $fileShort = fn ($path) => '…/' . implode('/', array_slice(explode('/', str_replace('\\', '/', $path)), -2));
@endphp
{!! $fileMarkup !!}
@if ($fileTokens !== [])
    <div
        class="tw-graph-protocol-dev-only pointer-events-none absolute"
        data-tw-graph-file-region="{{ $fileViewPath }}"
        data-tw-graph-file-tokens="{{ json_encode($fileTokens, JSON_THROW_ON_ERROR) }}"
        style="display:none;visibility:hidden;outline:1px dashed #db2777;z-index:85"
    >
        <div data-tw-graph-dev-caption class="pointer-events-auto absolute rounded bg-white px-2 py-1 font-mono text-xs text-pink-700 shadow-sm dark:bg-zinc-900" style="width:max-content;max-width:100%;overflow-wrap:anywhere">
            <flux:tooltip toggleable>
                <button type="button" class="cursor-pointer text-left" aria-label="{{ __('File details') }}">
                    <span class="block">{{ __('View') }}: {{ $fileShort($fileViewPath) }}</span>
                    @if ($fileLayoutPath)
                        <span class="block">{{ __('Layout') }}: {{ $fileShort($fileLayoutPath) }}</span>
                    @endif
                </button>
                <flux:tooltip.content style="max-width:min(42rem, calc(100vw - 2rem));white-space:normal">
                    @foreach ([__('View') => $fileViewPath, __('Layout') => $fileLayoutPath] as $fileKind => $filePath)
                        @if ($filePath)
                            <div class="mb-2">
                                <strong>{{ $fileKind }}</strong>
                                <div class="font-mono" style="overflow-wrap:anywhere">{{ $filePath }}</div>
                                <flux:button size="xs" icon="clipboard" data-copy-text="{{ $filePath }}" x-on:click.stop="navigator.clipboard?.writeText($el.dataset.copyText)">{{ __('Copy path') }}</flux:button>
                            </div>
                        @endif
                    @endforeach
                </flux:tooltip.content>
            </flux:tooltip>
        </div>
    </div>
@endif
