    <div
        class="z-100 fixed inset-0 flex items-center justify-center bg-white/60 backdrop-blur-sm dark:bg-zinc-950/60"
        role="status"
        aria-live="polite"
        @if ($trackGraphLoading ?? true) data-tw-graph-loading @endif
        wire:ignore
    >
        <div
            class="flex items-center gap-3 rounded-xl border border-zinc-200 bg-white px-5 py-4 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
            {{-- <div class="w-32" aria-hidden="true">
                <span class="three-red-rings-loader mx-auto block"></span>
            </div> --}}
            <flux:icon.loading class="size-12 text-sky-600 dark:text-sky-400" />
            <flux:text>{{ __('Loading graph and calculating layout…') }}</flux:text>
        </div>
    </div>
