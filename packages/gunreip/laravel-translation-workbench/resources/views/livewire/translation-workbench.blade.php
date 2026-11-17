<div class="relative space-y-6" x-data="{ destroy() { clearTimeout(this.$el.scanExpiryTimer) } }"
    wire:init="refreshScanFreshness"
    wire:poll.60s.visible="refreshScanFreshness"
    x-on:focus.window.debounce.500ms="$wire.refreshScanFreshness()"
    x-on:visibilitychange.document="if (!document.hidden) $wire.refreshScanFreshness()"
    x-effect="clearTimeout($el.scanExpiryTimer); if ($wire.scanFreshness.reusable) $el.scanExpiryTimer = setTimeout(() => $wire.refreshScanFreshness(), Math.max(1000, $wire.scanFreshness.expires * 1000 - Date.now()))"
    x-on:workbench-edit-key-finding.window="if ($event.detail.component === $wire.$id) $wire.editKeyFinding($event.detail.finding)"
    x-on:workbench-open-reserving-publication.window="if ($event.detail.component === $wire.$id) $wire.openPublication($event.detail.run)"
    x-on:workflow-focus.window="$nextTick(() => requestAnimationFrame(() => document.getElementById($event.detail.target)?.scrollIntoView({ block: 'center', behavior: 'smooth' })))">
    <div class="contents" wire:ignore x-data="{
        pending: 0,
        init() {
            const root = this.$root.parentElement;
            this.stopBackground = Livewire.interceptAction(({ action }) => {
                if (root.contains(action.component.el) && document.querySelector('dialog#livewire-error[open]')
                    && (action.metadata?.type === 'poll' || action.name === 'refreshScanFreshness')) action.cancel();
            });
            this.unsubscribe = Livewire.interceptMessage(({ message, onSend, onFinish }) => {
                if (!root.contains(message.component.el)) return;
                let counted = false;
                onSend(({ payload }) => {
                    const background = Object.keys(payload.updates).length === 0
                        && payload.calls.every(call => call.metadata?.type === 'poll' || call.method === 'refreshScanFreshness');
                    if (background || document.querySelector('dialog:modal')) return;
                    counted = true;
                    this.pending++;
                });
                onFinish(() => {
                    if (counted) this.pending--;
                    counted = false;
                });
            });
        },
        destroy() { this.stopBackground?.(); this.unsubscribe?.() }
    }">
        <x-ui.loading.lazy-indicator
            data-workbench-page-loading
            :viewport="true"
            x-show="pending > 0"
            style="display: none"
            role="status"
            :text="__('Loading')"
        />
    </div>

    @include('translation-workbench::livewire.translation-workbench.page.header')

    @include('translation-workbench::livewire.translation-workbench.page.test-environment')

    @include('translation-workbench::livewire.translation-workbench.overview.index')

    @include('translation-workbench::livewire.translation-workbench.tabs.index')

    @include('translation-workbench::livewire.translation-workbench.modals.index')
</div>
