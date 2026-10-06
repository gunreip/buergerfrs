// Observational timings only: never change graph geometry or Livewire error handling.
export function setupTwGraphPerformance() {
    const states = new Map();
    let installed = false;
    const owner = el => el?.closest?.('[wire\\:id]');
    const counts = ['passes', 'morphs', 'visited', 'added', 'removed'];
    const fields = ['begin', 'end', 'total', 'headers', 'body', 'prepare', 'morph', 'after', 'bounds', ...counts];
    const empty = () => ({ headers: null, body: null, prepare: null, after: null,
        morph: 0, bounds: 0, passes: 0, morphs: 0, visited: 0, added: 0, removed: 0, failed: false,
        begin: null, end: null, total: null, active: false, rendered: false, timer: null, revision: 0 });
    const state = id => {
        if (!states.has(id)) states.set(id, empty());
        return states.get(id);
    };
    const display = id => {
        const value = state(id);
        for (const panel of document.querySelectorAll('[data-tw-graph-performance]')) {
            if (owner(panel)?.getAttribute('wire:id') !== id) continue;
            for (const key of fields) {
                const target = panel.querySelector(`[data-tw-graph-time="${key}"]`);
                const text = ['end', 'total'].includes(key) && value.active ? panel.dataset.running
                    : key === 'headers' && value.failed ? panel.dataset.failed
                    : value[key] === null ? '—'
                    : ['begin', 'end'].includes(key) ? new Date(value[key]).toLocaleTimeString([], { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit', fractionalSecondDigits: 3 })
                    : counts.includes(key) ? String(value[key]) : `${(value[key] / 1000).toFixed(3)} s`;
                if (target && target.textContent !== text) target.textContent = text;
            }
        }
    };
    const begin = id => {
        clearTimeout(states.get(id)?.timer);
        const value = { ...empty(), begin: Date.now(), started: performance.now(), active: true };
        states.set(id, value);
        display(id);
        return value;
    };
    const finish = (id, value) => {
        if (states.get(id) !== value || !value.active) return;
        clearTimeout(value.timer);
        value.total = performance.now() - value.started;
        value.end = value.begin + value.total;
        if (value.morphEnd !== undefined) value.after = performance.now() - value.morphEnd;
        value.active = false;
        display(id);
    };
    // A browser-observed settling point, not a claim that the browser has no further work.
    const settle = (id, value) => {
        if (!value.active || !value.rendered) return;
        clearTimeout(value.timer);
        const revision = ++value.revision;
        requestAnimationFrame(() => requestAnimationFrame(() => {
            if (states.get(id) !== value || !value.active || value.revision !== revision) return;
            value.timer = setTimeout(() => {
                if (value.revision === revision) finish(id, value);
            }, 200);
        }));
    };
    document.addEventListener('click', event => {
        const button = event.target.closest?.('[data-tw-graph-refresh]');
        const id = owner(button)?.getAttribute('wire:id');
        if (id) begin(id);
    }, true);
    document.addEventListener('tw-graph-bounds-timed', event => {
        const id = owner(event.target)?.getAttribute('wire:id');
        if (!id) return;
        const value = state(id);
        if (value.begin !== null && !value.active) return;
        value.bounds += event.detail.duration;
        value.passes++;
        display(id);
        settle(id, value);
    });
    const install = () => {
        if (installed || !window.Livewire?.hook) return;
        installed = true;
        Livewire.hook('component.init', ({ component, cleanup }) => {
            cleanup(() => {
                clearTimeout(states.get(component.id)?.timer);
                states.delete(component.id);
            });
        });
        Livewire.hook('request', ({ payload, respond, succeed, fail }) => {
            let body;
            try { body = typeof payload === 'string' ? JSON.parse(payload) : payload; } catch { return; }
            const ids = (body?.components ?? []).flatMap(item => {
                try { return [JSON.parse(item.snapshot).memo.id]; } catch { return []; }
            }).filter(id => [...document.querySelectorAll('[data-tw-graph-performance]')]
                .some(panel => owner(panel)?.getAttribute('wire:id') === id));
            const start = performance.now();
            const pending = new Map();
            for (const id of ids) {
                const current = states.get(id);
                const value = current?.active && !current.requested ? current : begin(id);
                value.requested = true;
                pending.set(id, value); display(id);
            }
            respond(() => {
                for (const id of ids) {
                    if (states.get(id) !== pending.get(id)) continue;
                    state(id).headersAt = performance.now();
                    state(id).headers = state(id).headersAt - start;
                    display(id);
                }
            });
            succeed?.(() => {
                for (const id of ids) {
                    const value = pending.get(id);
                    if (states.get(id) !== value || !value.active) continue;
                    value.decodedAt = performance.now();
                    if (value.headersAt !== undefined) value.body = value.decodedAt - value.headersAt;
                    display(id);
                }
            });
            fail(() => {
                for (const id of ids) {
                    if (states.get(id) !== pending.get(id)) continue;
                    state(id).failed = true; finish(id, state(id));
                }
            });
        });
        Livewire.hook('commit', ({ component, succeed, fail }) => {
            // Livewire initializes request interceptors before message/commit interceptors.
            const value = states.get(component.id);
            if (!value?.active) return;
            succeed(() => {
                if (states.get(component.id) !== value) return;
                value.rendered = true;
                settle(component.id, value);
            });
            fail(() => {
                if (states.get(component.id) !== value) return;
                value.failed = true;
                finish(component.id, value);
            });
        });
        Livewire.hook('morph', ({ component }) => {
            const value = states.get(component.id);
            if (value?.active) {
                value.morphStart = performance.now();
                value.morphs++;
                if (value.morphs === 1 && value.decodedAt !== undefined) value.prepare = value.morphStart - value.decodedAt;
            }
        });
        Livewire.hook('morphed', ({ component }) => {
            const value = states.get(component.id);
            if (!value?.active) return;
            if (value.morphStart !== undefined) {
                value.morphEnd = performance.now();
                value.morph += value.morphEnd - value.morphStart;
                delete value.morphStart;
            }
            display(component.id);
        });
        // Count hook events, not whole subtrees. Never read layout or update the panel per node.
        for (const [hook, field] of [['morph.updating', 'visited'], ['morph.adding', 'added'], ['morph.removed', 'removed']]) {
            Livewire.hook(hook, ({ component }) => {
                const value = states.get(component?.id);
                if (value?.active) value[field]++;
            });
        }
    };
    document.addEventListener('livewire:init', install);
    install();
}
