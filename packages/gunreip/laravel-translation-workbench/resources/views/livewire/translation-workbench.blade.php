<div class="relative space-y-6" x-data="{ destroy() { clearTimeout(this.$el.scanExpiryTimer) } }"
    wire:init="refreshScanFreshness"
    wire:poll.60s.visible="refreshScanFreshness"
    x-on:focus.window.debounce.500ms="$wire.refreshScanFreshness()"
    x-on:visibilitychange.document="if (!document.hidden) $wire.refreshScanFreshness()"
    x-effect="clearTimeout($el.scanExpiryTimer); if ($wire.scanFreshness.reusable) $el.scanExpiryTimer = setTimeout(() => $wire.refreshScanFreshness(), Math.max(1000, $wire.scanFreshness.expires * 1000 - Date.now()))"
    x-on:workbench-open-reserving-publication.window="if ($event.detail.component === $wire.$id) $wire.openPublication($event.detail.run)"
    x-on:workflow-focus.window="$nextTick(() => requestAnimationFrame(() => document.getElementById($event.detail.target)?.scrollIntoView({ block: 'center', behavior: 'smooth' })))">
    @include('translation-workbench::livewire.translation-workbench.page.header')

    @include('translation-workbench::livewire.translation-workbench.page.test-environment')

    @include('translation-workbench::livewire.translation-workbench.overview.index')

    @include('translation-workbench::livewire.translation-workbench.tabs.index')

    @include('translation-workbench::livewire.translation-workbench.modals.index')
</div>
