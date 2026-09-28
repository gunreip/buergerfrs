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

// Tight content bounds are distinct from the origin-inclusive layout and CSS minimum size.
export function canvasSummary(records, layout, width, height, rem) {
    const format = value => `${round(value / rem)}rem`;
    const result = { canvas: `${format(width)} × ${format(height)}`, content: '—', spacing: '—' };
    const rects = records.flatMap(record => record.rects);
    if (!layout || !rects.length) return result;
    const bounds = unionBounds(rects);
    result.content = `${format(bounds.maxX - bounds.minX)} × ${format(bounds.maxY - bounds.minY)}`;
    result.spacing = `${format(layout.originLeft + bounds.minX)} / ${format(width - layout.originLeft - bounds.maxX)}`;
    return result;
}

// Public calls own regions; internal composition contributes to its caller's region.
export function componentRegions(records) {
    const groups = new Map();
    const definitions = new Map(records.filter(r => r.region?.token).map(r => [r.region.token, r.region]));
    for (const record of records) {
        const region = typeof record.region === 'number' ? definitions.get(record.region) : record.region;
        if (!region?.token || !record.rects.length) continue;
        if (!groups.has(region.token)) groups.set(region.token, { ...region, rects: [] });
        groups.get(region.token).rects.push(...record.rects);
    }
    return [...groups.values()].map(({ rects, ...region }) => ({ ...region, bounds: unionBounds(rects) }));
}

function updateComponentRegions(graph, canvas, records, layout) {
    const regions = new Map(componentRegions(records).map(region => [String(region.token), region]));
    for (const box of canvas.querySelectorAll('[data-tw-graph-region]')) {
        if (box.closest(graphSelector) !== graph) continue;
        const region = regions.get(box.dataset.twGraphRegion);
        if (!region || !layout) {
            box.style.visibility = 'hidden';
            continue;
        }
        const { minX, minY, maxX, maxY } = region.bounds;
        const styles = { left: `${layout.originLeft + minX}px`, bottom: `${layout.originBottom + minY}px`,
            width: `${maxX - minX}px`, height: `${maxY - minY}px`, visibility: 'visible' };
        for (const [name, value] of Object.entries(styles)) if (box.style[name] !== value) box.style[name] = value;
    }
}

// Keep diagnostic captions visible without extending the canvas scroll area.
export function captionOffset(boxLeft, captionWidth, canvasWidth, preferred = 4) {
    return Math.max(-boxLeft, Math.min(preferred, canvasWidth - boxLeft - captionWidth));
}

function positionDevCaptions(graph, canvas) {
    const frame = canvas.getBoundingClientRect();
    const width = parseFloat(getComputedStyle(canvas).width);
    const height = parseFloat(getComputedStyle(canvas).height);
    const scaleY = frame.height / height;
    const scale = frame.width / width;
    if (!(scale > 0)) return;
    const occupied = [];
    const summary = canvas.querySelector('[data-tw-graph-canvas-summary]');
    if (summary?.getClientRects().length) {
        const r = summary.getBoundingClientRect();
        occupied.push({ x: (r.left - frame.left) / scale, y: (r.top - frame.top) / scaleY,
            width: r.width / scale, height: r.height / scaleY });
    }
    for (const caption of canvas.querySelectorAll('[data-tw-graph-dev-caption]')) {
        if (caption.closest(graphSelector) !== graph || !caption.getClientRects().length) continue;
        const maxWidth = `${width}px`;
        if (caption.style.maxWidth !== maxWidth) caption.style.maxWidth = maxWidth;
        const box = caption.parentElement;
        const boxLeft = (box.getBoundingClientRect().left - frame.left) / scale + box.clientLeft;
        const captionWidth = caption.getBoundingClientRect().width / scale;
        const boxTop = (box.getBoundingClientRect().top - frame.top) / scaleY + box.clientTop;
        const captionHeight = caption.getBoundingClientRect().height / scaleY;
        let x = boxLeft + captionOffset(boxLeft, captionWidth, width);
        let y = Math.max(0, Math.min(boxTop - captionHeight, height - captionHeight));
        // Keep region names separate from the summary and previously placed captions.
        for (const obstacle of occupied) {
            if (x < obstacle.x + obstacle.width && x + captionWidth > obstacle.x &&
                y < obstacle.y + obstacle.height && y + captionHeight > obstacle.y) {
                if (obstacle.x + obstacle.width + 4 + captionWidth <= width) x = obstacle.x + obstacle.width + 4;
                else y = Math.min(height - captionHeight, obstacle.y + obstacle.height + 4);
            }
        }
        occupied.push({ x, y, width: captionWidth, height: captionHeight });
        const styles = { left: `${x - boxLeft}px`, top: `${y - boxTop}px`, transform: 'none' };
        for (const [name, value] of Object.entries(styles)) if (caption.style[name] !== value) caption.style[name] = value;
    }
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
            if (layout) {
                const values = { 'trunk-x': layout.originLeft, 'origin-bottom': layout.originBottom,
                    'calculated-width': layout.width, 'calculated-height': layout.height,
                    'content-min-x': layout.minX, 'content-max-x': layout.maxX, 'content-min-y': layout.minY,
                    'content-width': layout.maxX - layout.minX, 'content-height': layout.maxY - layout.minY };
                for (const [key, value] of Object.entries(values)) set(graph, key, value);
                const rem = parseFloat(getComputedStyle(document.documentElement).fontSize);
                updateCoordinateGrid(canvas, layout.originLeft, layout.originBottom, rem);
            }
            const style = getComputedStyle(canvas);
            updateComponentRegions(graph, canvas, records, layout);
            positionDevCaptions(graph, canvas);
            const summary = canvasSummary(records, layout, parseFloat(style.width), parseFloat(style.height),
                parseFloat(getComputedStyle(document.documentElement).fontSize));
            for (const label of graph.querySelectorAll('[data-tw-graph-canvas-result]')) {
                if (label.closest(graphSelector) === graph) setText(label, summary[label.dataset.twGraphCanvasResult]);
            }
        }
        for (const element of observed) if (!next.has(element)) resize.unobserve(element);
        for (const element of next) if (!observed.has(element)) resize.observe(element);
        observed = next;
    }
    const internal = '[data-tw-graph-region], [data-tw-graph-dev-caption], [data-tw-graph-bounds-resolver], [data-tw-graph-canvas-summary], [data-tw-graph-bounds-state], [data-tw-graph-bounds-warning]';
    new MutationObserver(records => {
        if (records.some(r => !r.target.closest?.(internal))) schedule();
    }).observe(document, { subtree: true, childList: true, characterData: true, attributes: true,
        attributeFilter: ['style', 'class', 'hidden', 'data-boxes', 'data-tw-graph-bounds'] });
    window.addEventListener('resize', schedule);
    document.fonts?.ready.then(schedule);
    document.fonts?.addEventListener('loadingdone', schedule);
    schedule();
}
