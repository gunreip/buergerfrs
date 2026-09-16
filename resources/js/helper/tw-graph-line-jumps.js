// Explicit crossings only. Coordinates are measured after Blade/Livewire and CSS layout.
export function planLineJumps(owner, entries, lookup, radiusInPixels) {
    const jumps = [];
    const errors = [];
    const horizontal = owner.horizontal;
    const length = horizontal ? owner.width : owner.height;
    for (const [index, entry] of entries.entries()) {
        const fail = (reason) => errors.push({ index, over: entry?.over ?? '', reason });
        if (!entry || typeof entry.over !== 'string') { fail('Missing line reference (over).'); continue; }
        const target = lookup(entry.over);
        if (!target) { fail('Reference is missing or is not a line.'); continue; }
        if (target.horizontal === horizontal) { fail('Lines must cross at right angles.'); continue; }
        const side = entry.side ?? (horizontal ? 'top' : 'right');
        if (!(horizontal ? ['top', 'bottom'] : ['left', 'right']).includes(side)) {
            fail('Side does not match the line orientation.'); continue;
        }
        const radius = radiusInPixels(entry.radius ?? '0.5rem');
        if (!Number.isFinite(radius) || radius <= owner.thickness / 2) {
            fail('Radius must exceed half the line width.'); continue;
        }
        const position = horizontal ? target.x + target.width / 2 - owner.x : target.y + target.height / 2 - owner.y;
        const across = horizontal ? owner.y + owner.height / 2 - target.y : owner.x + owner.width / 2 - target.x;
        const targetLength = horizontal ? target.height : target.width;
        if (position <= 0 || position >= length || across <= 0 || across >= targetLength) {
            fail('No crossing inside both lines.'); continue;
        }
        if (position - radius <= owner.thickness / 2 || position + radius >= length - owner.thickness / 2) {
            fail('Not enough room before the line endpoint.'); continue;
        }
        if (jumps.some((jump) => Math.abs(position - jump.position) < radius + jump.radius + owner.thickness)) {
            fail('Jump overlaps another configured jump.'); continue;
        }
        jumps.push({ index, position, radius, side, targetZIndex: target.zIndex ?? 0 });
    }
    jumps.sort((a, b) => a.position - b.position);
    return { jumps, errors };
}

export function jumpDrawing(owner, jumps) {
    const horizontal = owner.horizontal;
    const padding = Math.max(...jumps.map((jump) => jump.radius)) + owner.thickness;
    const width = owner.width + (horizontal ? 0 : padding * 2);
    const height = owner.height + (horizontal ? padding * 2 : 0);
    const center = padding + owner.thickness / 2;
    const paths = jumps.map(({ position: p, radius: r, side }) => horizontal
        ? `M ${p - r} ${center} A ${r} ${r} 0 0 ${side === 'top' ? 1 : 0} ${p + r} ${center}`
        : `M ${center} ${p - r} A ${r} ${r} 0 0 ${side === 'right' ? 1 : 0} ${center} ${p + r}`);
    const stops = ['#000 0px'];
    for (const { position, radius } of jumps) {
        stops.push(`#000 ${position - radius}px`, `transparent ${position - radius}px`, `transparent ${position + radius}px`, `#000 ${position + radius}px`);
    }
    stops.push('#000 100%');
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}" viewBox="0 0 ${width} ${height}"><path d="${paths.join(' ')}" fill="none" stroke="white" stroke-width="${owner.thickness}" stroke-linecap="round"/></svg>`;
    return {
        width, height, padding,
        zIndex: Math.max(owner.zIndex ?? 0, ...jumps.map((jump) => jump.targetZIndex ?? 0)) + 1,
        lineMask: `linear-gradient(to ${horizontal ? 'right' : 'bottom'}, ${stops.join(', ')})`,
        arcMask: `url("data:image/svg+xml,${encodeURIComponent(svg)}")`,
    };
}

export function setupTwGraphLineJumps() {
    const selector = '.tw-graph-protocol-primitive-line[data-tw-graph-line-jumps]';
    const states = new Map();
    let frame = null;
    const schedule = () => { frame ??= requestAnimationFrame(refresh); };
    const generated = (node) => node.nodeType === 1 && node.hasAttribute('data-tw-graph-jump-generated');
    const clear = (line, state) => {
        line.style.maskImage = state.originalMask;
        line.style.maskClip = state.originalMaskClip;
        state.overlay?.remove();
        state.badge?.remove();
        state.overlay = state.badge = null;
    };
    const resize = new ResizeObserver(schedule);
    let observed = new Set();
    function refresh() {
        frame = null;
        const owners = new Set(document.querySelectorAll(selector));
        for (const [line, state] of states) {
            if (!owners.has(line)) { clear(line, state); states.delete(line); }
        }
        const nextObserved = new Set();
        for (const line of owners) {
            const graph = line.closest('.tw-graph-protocol');
            if (!graph) continue;
            let state = states.get(line);
            if (!state) {
                state = { originalMask: line.style.maskImage, originalMaskClip: line.style.maskClip };
                states.set(line, state);
            }
            clear(line, state);
            nextObserved.add(graph);
            const css = getComputedStyle(line);
            const rect = line.getBoundingClientRect();
            const width = parseFloat(css.width), height = parseFloat(css.height);
            if (!rect.width || !rect.height || !width || !height) continue;
            const scaleX = rect.width / width, scaleY = rect.height / height;
            const horizontal = line.classList.contains('tw-graph-protocol-primitive-line-left-right') || line.classList.contains('tw-graph-protocol-primitive-line-right-left');
            const owner = { x: 0, y: 0, width, height, horizontal, thickness: horizontal ? height : width, zIndex: parseInt(css.zIndex, 10) || 0 };
            const targets = new Map();
            for (const target of graph.querySelectorAll('.tw-graph-protocol-primitive-line')) {
                if (target.closest('.tw-graph-protocol') !== graph) continue;
                nextObserved.add(target);
                const box = target.getBoundingClientRect();
                if (!box.width || !box.height) continue;
                targets.set(target.dataset.twGraphPath, {
                    x: (box.left - rect.left) / scaleX, y: (box.top - rect.top) / scaleY,
                    width: box.width / scaleX, height: box.height / scaleY,
                    zIndex: parseInt(getComputedStyle(target).zIndex, 10) || 0,
                    horizontal: target.classList.contains('tw-graph-protocol-primitive-line-left-right') || target.classList.contains('tw-graph-protocol-primitive-line-right-left'),
                });
            }
            let entries;
            try { entries = JSON.parse(line.dataset.twGraphLineJumps); } catch { entries = [null]; }
            if (!Array.isArray(entries)) entries = [null];
            const probe = document.createElement('span');
            probe.dataset.twGraphJumpGenerated = '';
            probe.style.cssText = 'position:absolute;visibility:hidden;pointer-events:none;height:0;';
            line.parentElement.append(probe);
            const result = planLineJumps(owner, entries, (id) => targets.get(id), (value) => {
                if (typeof value !== 'string' || !CSS.supports('width', value)) return NaN;
                probe.style.width = value;
                return parseFloat(getComputedStyle(probe).width);
            });
            probe.remove();
            if (result.jumps.length) {
                const drawing = jumpDrawing(owner, result.jumps);
                line.style.maskImage = drawing.lineMask;
                // Keep existing endpoint dots/caps outside the line's thin border box visible.
                line.style.maskClip = 'no-clip';
                const overlay = document.createElement('span');
                overlay.dataset.twGraphJumpGenerated = '';
                overlay.dataset.twGraphLineJumpFor = line.dataset.twGraphPath;
                overlay.setAttribute('aria-hidden', 'true');
                // Retain the original line paint, including path tone, gradients and deferred return colors.
                Object.assign(overlay.style, {
                    position: 'absolute', pointerEvents: 'none',
                    left: `calc(${css.left} - ${horizontal ? 0 : drawing.padding}px)`,
                    top: `calc(${css.top} - ${horizontal ? drawing.padding : 0}px)`,
                    width: `${drawing.width}px`, height: `${drawing.height}px`,
                    backgroundColor: css.backgroundColor, backgroundImage: css.backgroundImage,
                    backgroundSize: css.backgroundSize, backgroundPosition: css.backgroundPosition,
                    backgroundRepeat: css.backgroundRepeat, opacity: css.opacity,
                    zIndex: drawing.zIndex, maskImage: drawing.arcMask, maskRepeat: 'no-repeat',
                });
                line.after(overlay);
                state.overlay = overlay;
            }
            if (result.errors.length && graph.dataset.twGraphDev === 'true') {
                const badge = document.createElement('span');
                badge.dataset.twGraphJumpGenerated = '';
                badge.dataset.twGraphLineJumpMismatch = line.dataset.twGraphPath;
                badge.className = 'tw-graph-protocol-dev-only tw-graph-line-jump-mismatch';
                const details = result.errors.map(({ index, over, reason }) => `#${index + 1} ${over}: ${reason}`).join('\n');
                badge.textContent = `lineJump-Mismatch (${result.errors.length})`;
                badge.title = `${line.dataset.twGraphPath}\n${details}`;
                badge.setAttribute('aria-label', badge.title);
                badge.tabIndex = 0;
                badge.style.left = css.left;
                badge.style.top = css.top;
                line.after(badge);
                state.badge = badge;
            }
        }
        for (const node of observed) if (!nextObserved.has(node)) resize.unobserve(node);
        for (const node of nextObserved) if (!observed.has(node)) resize.observe(node);
        observed = nextObserved;
    }
    const withoutMask = (text) => {
        const style = document.createElement('span').style;
        style.cssText = text ?? '';
        style.removeProperty('mask-image');
        style.removeProperty('mask-clip');
        return style.cssText;
    };
    new MutationObserver((records) => {
        if (records.some((record) => record.type === 'attributes'
            ? !generated(record.target) && (record.attributeName !== 'style' || withoutMask(record.oldValue) !== withoutMask(record.target.getAttribute('style')))
            : [...record.addedNodes, ...record.removedNodes].some((node) => !generated(node)))) schedule();
    }).observe(document, {
        subtree: true, childList: true, attributes: true, attributeOldValue: true,
        attributeFilter: ['style', 'class', 'data-tw-graph-line-jumps', 'data-tw-graph-path-tone', 'data-tw-graph-dev'],
    });
    window.addEventListener('resize', schedule);
    document.fonts?.ready.then(schedule);
    schedule();
}
