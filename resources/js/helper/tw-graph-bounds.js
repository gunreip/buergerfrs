// One bounds model: declared primitive geometry + explicitly measured text dimensions.
// Geometry is audited against the DOM. A mismatch NEVER changes the declared geometry.
const prefix = '--tw-graph-protocol-';
const graphSelector = '[data-tw-graph-bounds-model="true"]';
const excluded = '.tw-graph-protocol-dev-only, [data-tw-graph-dev-box], .tw-graph-protocol-primitive-dev-node-counter, template';
const round = value => Math.round(value * 1000) / 1000;

// Diagnostic grid uses the same origin as primitives, never contributing bounds records.
export function gridTicks(extent, origin, rem) {
    const ticks = [];
    for (let value = Math.ceil(-origin / rem); value <= Math.floor((extent - origin) / rem); value++) {
        ticks.push({ value, position: origin + value * rem, major: value % 5 === 0 });
    }
    return ticks;
}

function updateCoordinateGrid(canvas, originLeft, originBottom, rem) {
    const style = getComputedStyle(canvas);
    const width = parseFloat(style.width), height = parseFloat(style.height);
    if (!(width > 0 && height > 0 && rem > 0)) return;
    let grid = canvas.querySelector(':scope > [data-tw-graph-grid]');
    const signature = [width, height, originLeft, originBottom, rem].join(',');
    if (grid?.dataset.geometry === signature) return;
    const ns = 'http://www.w3.org/2000/svg';
    if (!grid) {
        grid = document.createElementNS(ns, 'svg');
        grid.setAttribute('data-tw-graph-grid', '');
        grid.setAttribute('aria-hidden', 'true');
        canvas.prepend(grid);
    }
    grid.dataset.geometry = signature;
    grid.setAttribute('viewBox', `0 0 ${width} ${height}`);
    const children = [];
    const label = (x, y, text) => {
        const el = document.createElementNS(ns, 'text');
        el.setAttribute('x', x); el.setAttribute('y', y);
        el.textContent = text; children.push(el);
    };
    for (const axis of ['x', 'y']) {
        for (const tick of gridTicks(axis === 'x' ? width : height, axis === 'x' ? originLeft : originBottom, rem)) {
            const line = document.createElementNS(ns, 'line');
            const position = axis === 'x' ? tick.position : height - tick.position;
            line.setAttribute('x1', axis === 'x' ? position : 0);
            line.setAttribute('x2', axis === 'x' ? position : width);
            line.setAttribute('y1', axis === 'y' ? position : 0);
            line.setAttribute('y2', axis === 'y' ? position : height);
            line.setAttribute('class', tick.value === 0 ? 'grid-axis' : tick.major ? 'grid-major' : 'grid-minor');
            children.push(line);
            if (tick.major && tick.value !== 0) {
                label(axis === 'x' ? Math.min(width - 24, position + 3) : Math.min(width - 24, originLeft + 4),
                    axis === 'y' ? Math.max(12, position - 4) : Math.max(12, height - originBottom - 4), `${tick.value}`);
            }
        }
    }
    label(originLeft + 4, height - originBottom + 14, '(0|0)');
    label(Math.max(0, width - 44), Math.max(12, height - originBottom - 8), 'X [rem]');
    label(originLeft + 6, 14, 'Y [rem]');
    grid.replaceChildren(...children);
}

export function unionBounds(rects) {
    return rects.reduce((b, r) => ({ minX: Math.min(b.minX, r.x), minY: Math.min(b.minY, r.y),
        maxX: Math.max(b.maxX, r.x + r.width), maxY: Math.max(b.maxY, r.y + r.height) }),
    { minX: Infinity, minY: Infinity, maxX: -Infinity, maxY: -Infinity });
}

export function canvasLayout(bounds, horizontalPadding, verticalPadding) {
    // Same origin-inclusive policy as BoundsRegistry::canvasMetrics, without automatic centering.
    const minX = Math.min(0, bounds.minX), minY = Math.min(0, bounds.minY);
    const maxX = Math.max(0, bounds.maxX), maxY = Math.max(0, bounds.maxY);
    return { minX, minY, maxX, maxY, width: maxX - minX + horizontalPadding * 2,
        height: maxY - minY + verticalPadding * 2,
        originLeft: horizontalPadding - minX, originBottom: verticalPadding - minY };
}

export function boundsDifference(expected, actual, tolerance = 1.1) {
    return ['minX', 'minY', 'maxX', 'maxY'].filter(key => Math.abs(expected[key] - actual[key]) > tolerance);
}

// Line dots/caps are pseudo-elements and therefore absent from getBoundingClientRect().
function lineEndpointRects(element, rect, scaleX, scaleY) {
    if (!element.matches('.tw-graph-protocol-primitive-line, .tw-graph-protocol-primitive-path')) return [];
    const className = element.className;
    const horizontal = /-(left-right|right-left)(?:\s|$)/.test(className);
    const reversed = /-(top-bottom|right-left)(?:\s|$)/.test(className);
    const result = [];
    for (const [pseudo, start] of [['::before', true], ['::after', false]]) {
        const style = getComputedStyle(element, pseudo);
        if (style.content === 'none' || style.content === 'normal' || style.display === 'none') continue;
        const width = parseFloat(style.width) * scaleX;
        const height = parseFloat(style.height) * scaleY;
        if (!Number.isFinite(width + height)) continue;
        const low = start !== reversed;
        const x = horizontal ? (low ? rect.left : rect.right) : (rect.left + rect.right) / 2;
        const y = horizontal ? (rect.top + rect.bottom) / 2 : (low ? rect.bottom : rect.top);
        result.push({ left: x - width / 2, right: x + width / 2, top: y - height / 2, bottom: y + height / 2 });
    }
    return result;
}


export function resolveGraphBounds(graph) {
    const canvas = graph.querySelector(':scope > .tw-graph-protocol-canvas');
    const origin = canvas?.querySelector(':scope > [data-tw-graph-bounds-origin]');
    const manifest = [...graph.querySelectorAll('[data-tw-graph-bounds-records]')].find(el => el.closest(graphSelector) === graph);
    if (!origin || !manifest || !canvas.getClientRects().length) return null;
    const frame = canvas.getBoundingClientRect(), css = getComputedStyle(canvas);
    const scaleX = frame.width / parseFloat(css.width), scaleY = frame.height / parseFloat(css.height);
    if (!(scaleX > 0 && scaleY > 0)) return null;
    const zero = origin.getBoundingClientRect();
    let resolver = canvas.querySelector(':scope > [data-tw-graph-bounds-resolver]');
    if (!resolver) {
        resolver = document.createElement('span');
        resolver.dataset.twGraphBoundsResolver = '';
        resolver.style.cssText = 'position:absolute;visibility:hidden;pointer-events:none;width:0;height:0;';
        canvas.append(resolver);
    }
    const lengths = new Map();
    const length = expression => {
        if (lengths.has(expression)) return lengths.get(expression);
        // Invalid var() substitutions otherwise fall back to an 'auto' used value (often 0px).
        // Resolve through a custom property first, so missing variables cannot masquerade as zero.
        resolver.style.setProperty('--tw-graph-bounds-length', expression);
        const resolved = getComputedStyle(resolver).getPropertyValue('--tw-graph-bounds-length').trim();
        if (!resolved || /^(auto|initial|inherit|unset|revert)$/.test(resolved) || !CSS.supports('left', resolved)) {
            throw new Error(`Unresolvable length: ${expression}`);
        }
        resolver.style.left = resolved;
        const value = parseFloat(getComputedStyle(resolver).left);
        if (!Number.isFinite(value)) throw new Error(`Unresolvable length: ${expression}`);
        lengths.set(expression, value);
        return value;
    };
    const local = rect => ({ x: (rect.left - zero.left) / scaleX, y: (zero.bottom - rect.bottom) / scaleY,
        width: (rect.right - rect.left) / scaleX, height: (rect.bottom - rect.top) / scaleY });
    const source = JSON.parse(manifest.textContent);
    const elements = [...canvas.querySelectorAll('[data-tw-graph-bounds]')]
        .filter(el => el.closest(graphSelector) === graph && !el.hasAttribute('data-tw-graph-jump-generated'));
    const derived = [...canvas.querySelectorAll('[data-tw-graph-jump-generated][data-tw-graph-bounds]')].filter(el => el.closest(graphSelector) === graph);
    const records = [], issues = [];
    let unresolved = false;
    const consume = (element, entry) => {
        try {
            const declared = entry.rects.map(rect => Object.fromEntries(Object.entries(rect).map(([key, value]) => [key, length(value)])));
            const expected = unionBounds(declared);
            if (!element) {
                issues.push(`${entry.id}: declared primitive missing from DOM`);
                records.push({ ...entry, rects: declared });
                return;
            }
            const rect = element.getBoundingClientRect();
            const shown = element.getClientRects().length && getComputedStyle(element).visibility !== 'hidden';
            const actualRects = [rect, ...lineEndpointRects(element, rect, scaleX, scaleY)].map(local);
            // SVG viewBox bounds omit its non-scaling stroke; the primitive declares that extension.
            if (element.matches('.tw-graph-protocol-primitive-line-jump')) {
                const stroke = parseFloat(getComputedStyle(element.querySelector('path')).strokeWidth) / 2;
                actualRects[0].x -= stroke; actualRects[0].y -= stroke;
                actualRects[0].width += stroke * 2; actualRects[0].height += stroke * 2;
            }
            const actual = unionBounds(actualRects);
            if (entry.kind !== 'text' && shown) {
                const different = boundsDifference(expected, actual);
                if (different.length) issues.push(`${entry.id}: ${different.map(k => `${k} expected=${round(expected[k])}px actual=${round(actual[k])}px`).join(', ')}`);
            }
            records.push({ ...entry, rects: entry.kind === 'text' && shown ? [local(rect)] : declared });
        } catch (error) { unresolved = true; issues.push(`${entry.id}: ${error.message}`); }
    };
    const byId = new Map();
    for (const element of elements) {
        const id = JSON.parse(element.dataset.twGraphBounds).id;
        if (!byId.has(id)) byId.set(id, []);
        byId.get(id).push(element);
    }
    for (const entry of source) consume(byId.get(entry.id)?.shift(), entry);
    for (const [id, extra] of byId) if (extra.length) issues.push(`${id}: primitive missing from declared model`);
    for (const element of derived) consume(element, JSON.parse(element.dataset.twGraphBounds));
    if (source.length !== elements.length) issues.push(`Bounds coverage: ${source.length} declared / ${elements.length} rendered`);
    for (const element of canvas.querySelectorAll('.tw-graph-protocol-primitive')) {
        if (element.closest(graphSelector) === graph && !element.closest(excluded) && !element.hasAttribute('data-tw-graph-bounds')) issues.push(`${element.dataset.twGraphPath || element.className}: primitive without bounds contract`);
    }
    const rects = records.flatMap(record => record.rects);
    if (unresolved || !rects.length) return { canvas, origin, elements, records, issues, layout: null };
    return { canvas, origin, elements, records, issues,
        layout: canvasLayout(unionBounds(rects), zero.width / scaleX, zero.height / scaleY) };
}

export function setupTwGraphBounds() {
    let frame = null;
    let observed = new Set();
    const reports = new WeakMap();
    const schedule = () => { frame ??= requestAnimationFrame(refresh); };
    const resize = new ResizeObserver(schedule);
    const set = (graph, key, value) => {
        const previous = parseFloat(graph.style.getPropertyValue(prefix + key));
        if (Math.abs(previous - value) >= .05 || !Number.isFinite(previous)) graph.style.setProperty(prefix + key, `${round(value)}px`);
    };
    const setText = (element, text) => { if (element && element.textContent !== text) element.textContent = text; };
    function refresh() {
        frame = null;
        const next = new Set();
        for (const graph of document.querySelectorAll(graphSelector)) {
            next.add(graph);
            const result = resolveGraphBounds(graph);
            if (!result) continue;
            const { canvas, origin, elements, records, issues, layout } = result;
            next.add(canvas); next.add(origin);
            elements.forEach(element => next.add(element));
            const warning = graph.querySelector('[data-tw-graph-bounds-warning]');
            if (warning && warning.hidden !== (issues.length === 0)) warning.hidden = issues.length === 0;
            setText(graph.querySelector('[data-tw-graph-bounds-warning-details]'), issues.join('\n'));
            const report = JSON.stringify(issues);
            if (reports.get(graph) !== report) {
                reports.set(graph, report);
                graph.dataset.twGraphBoundsIssues = report;
                if (issues.length) console.warn(`[tw-graph bounds] ${graph.id}`, issues);
                graph.dispatchEvent(new CustomEvent('tw-graph-bounds-checked', { detail: { issues } }));
            }
            setText(graph.querySelector('[data-tw-graph-bounds-state]'), issues.length ? `Bounds: ${issues.length} mismatch(es)` : 'Bounds: primitive geometry verified; text measured');
            if (!layout) continue;
            const values = { 'trunk-x': layout.originLeft, 'origin-bottom': layout.originBottom,
                'calculated-width': layout.width, 'calculated-height': layout.height,
                'content-min-x': layout.minX, 'content-max-x': layout.maxX, 'content-min-y': layout.minY,
                'content-width': layout.maxX - layout.minX, 'content-height': layout.maxY - layout.minY };
            for (const [key, value] of Object.entries(values)) set(graph, key, value);
            const rem = parseFloat(getComputedStyle(document.documentElement).fontSize);
            updateCoordinateGrid(canvas, layout.originLeft, layout.originBottom, rem);
            const metrics = { left: layout.originLeft, bottom: layout.originBottom, width: layout.width,
                height: layout.height, minX: layout.minX, maxX: layout.maxX };
            for (const label of graph.querySelectorAll('[data-tw-graph-bound-value]')) {
                const key = label.dataset.twGraphBoundValue;
                setText(label, `${key}=${round(metrics[key] / rem)}rem`);
            }
            let largest = null, largestHeight = -1;
            for (const side of ['left', 'center', 'right']) {
                const rects = records.filter(r => (r.side || 'center') === side).flatMap(r => r.rects);
                const top = rects.length ? Math.max(...rects.map(r => r.y + r.height)) : 0;
                const height = rects.length ? Math.max(...rects.map(r => r.height)) : 0;
                if (rects.length && height > largestHeight) { largest = side; largestHeight = height; }
                set(graph, `side-${side}-top`, top);
                setText(graph.querySelector(`[data-tw-graph-side-value="${side}.count"]`), `n=${rects.length}`);
                setText(graph.querySelector(`[data-tw-graph-side-value="${side}.top"]`), `top=${round(top / rem)}rem`);
                setText(graph.querySelector(`[data-tw-graph-side-value="${side}.height"]`), `h=${round(height / rem)}rem`);
            }
            for (const badge of graph.querySelectorAll('[data-tw-graph-side-largest]')) {
                const hidden = badge.dataset.twGraphSideLargest !== largest;
                if (badge.hidden !== hidden) badge.hidden = hidden;
            }
        }
        for (const element of observed) if (!next.has(element)) resize.unobserve(element);
        for (const element of next) if (!observed.has(element)) resize.observe(element);
        observed = next;
    }
    const internal = '[data-tw-graph-bounds-resolver], [data-tw-graph-bound-value], [data-tw-graph-side-value], [data-tw-graph-side-largest], [data-tw-graph-bounds-state], [data-tw-graph-bounds-warning]';
    new MutationObserver(records => {
        if (records.some(r => !r.target.closest?.(internal))) schedule();
    }).observe(document, { subtree: true, childList: true, characterData: true, attributes: true,
        attributeFilter: ['style', 'class', 'hidden', 'data-tw-graph-bounds'] });
    window.addEventListener('resize', schedule);
    document.fonts?.ready.then(schedule);
    document.fonts?.addEventListener('loadingdone', schedule);
    schedule();
}
