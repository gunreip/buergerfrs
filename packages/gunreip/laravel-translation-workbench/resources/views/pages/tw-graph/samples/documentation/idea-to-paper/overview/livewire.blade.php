<section class="relative min-w-0" data-tw-graph-overview>
    <div
        style="display: none;"
        wire:loading.flex.delay
        wire:target="$refresh"
        class="absolute inset-0 z-100 items-center justify-center bg-white/60 backdrop-blur-sm dark:bg-zinc-950/60"
        role="status"
        aria-live="polite"
    >
        <flux:icon.loading class="size-6 text-sky-600 dark:text-sky-400" />
        <span class="sr-only">{{ __('Loading overview…') }}</span>
    </div>
    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.index')
</section>
