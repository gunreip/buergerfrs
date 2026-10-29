<div class="relative space-y-6" x-data
    x-on:workflow-focus.window="$nextTick(() => requestAnimationFrame(() => document.getElementById($event.detail.target)?.scrollIntoView({ block: 'center', behavior: 'smooth' })))">
    @include('translation-workbench::livewire.translation-workbench.page.header')

    @include('translation-workbench::livewire.translation-workbench.page.test-environment')

    @include('translation-workbench::livewire.translation-workbench.overview.index')

    @include('translation-workbench::livewire.translation-workbench.tabs.index')

    @include('translation-workbench::livewire.translation-workbench.modals.index')
</div>
