// Keep dataset loading visible across requests and browser-measured layout passes.
export function setupTwGraphLoading() {
    const states = new Map();
    const rootFor = element => element?.closest('[wire\\:id]');
    const stateFor = root => {
        if (!root?.querySelector('[data-tw-graph-loading]')) return null;
        const id = root.getAttribute('wire:id');
        if (!states.has(id)) states.set(id, { root, pending: 0, timer: null });
        const state = states.get(id);
        state.root = root;
        return state;
    };
    const show = (state, visible) => {
        const overlay = state.root.querySelector('[data-tw-graph-loading]');
        if (overlay) overlay.style.display = visible ? 'flex' : 'none';
        state.root.setAttribute('aria-busy', String(visible));
    };
    const settle = state => {
        clearTimeout(state.timer);
        if (state.pending) return;
        state.timer = setTimeout(() => {
            const graphs = [...state.root.querySelectorAll('[data-tw-graph-bounds-model]')];
            const pending = graphs.some(graph => graph.querySelector('[data-tw-graph-horizontal-layout]') &&
                !['complete', 'unresolved', 'failed'].includes(graph.dataset.twGraphHorizontalStatus));
            if (!state.pending && !pending) show(state, false);
        }, 200);
    };
    document.addEventListener('tw-graph-bounds-timed', event => {
        const state = stateFor(rootFor(event.target));
        if (state) settle(state);
    });
    document.addEventListener('tw-graph-layout-failed', event => {
        const state = stateFor(rootFor(event.target));
        if (state) { clearTimeout(state.timer); show(state, false); }
    });
    let installed = false;
    const install = () => {
        if (installed || !window.Livewire?.hook) return;
        installed = true;
        Livewire.hook('commit', ({ component, succeed, fail }) => {
            const state = stateFor(component.el);
            if (!state) return;
            clearTimeout(state.timer);
            state.pending++;
            show(state, true);
            succeed(() => { state.pending--; settle(state); });
            fail(() => { state.pending--; clearTimeout(state.timer); show(state, false); });
        });
        Livewire.hook('component.init', ({ component, cleanup }) => {
            cleanup(() => { clearTimeout(states.get(component.id)?.timer); states.delete(component.id); });
        });
    };
    document.addEventListener('livewire:init', install);
    const initialize = () => {
        document.querySelectorAll('[data-tw-graph-loading]').forEach(overlay => {
            const state = stateFor(rootFor(overlay));
            if (state) settle(state);
        });
    };
    document.addEventListener('livewire:navigated', initialize);
    document.addEventListener('DOMContentLoaded', initialize);
    if (document.readyState !== 'loading') initialize();
    install();
}
