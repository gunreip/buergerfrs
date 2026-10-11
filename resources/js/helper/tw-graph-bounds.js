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
        for (const owner of [region, ...(region.ancestors || [])]) {
            if (!groups.has(owner.token)) groups.set(owner.token, { ...owner, rects: [] });
            groups.get(owner.token).rects.push(...record.rects);
        }
    }
    return [...groups.values()].map(({ rects, ...region }) => ({ ...region, bounds: unionBounds(rects) }));
}

// Consecutive attachment rows form a group while their side occupancy stays the same.
// Attachment columns separate parallel lanes; component boundaries do not split runs.
export function textLabelGroups(records) {
    const definitions = new Map(records.filter(r => r.region?.token).map(r => [r.region.token, r.region]));
    const lanes = [];
    for (const record of records) {
        if (record.kind !== 'text' || record.visible === false || !record.rects.length || !record.textPlacement) continue;
        const region = typeof record.region === 'number' ? definitions.get(record.region) : record.region;
        const owner = region && [region, ...(region.ancestors || [])]
            .find(candidate => /(?:^|\.)strang\./.test(candidate.component || ''));
        if (!owner) continue;
        const { x, y, side } = record.textPlacement;
        let lane = lanes.find(l => Math.abs(l.x - x) < 0.1);
        if (!lane) { lane = { x, rows: [] }; lanes.push(lane); }
        const central = !['left', 'right'].includes(side);
        let row = !central && lane.rows.find(r => !r.central && Math.abs(r.y - y) < 0.1);
        if (!row) { row = { y, central, labels: [] }; lane.rows.push(row); }
        row.labels.push({ record, owner });
    }
    const groups = [];
    for (const lane of lanes) {
        let active = null;
        for (const row of lane.rows.sort((a, b) => a.y - b.y)) {
            const sides = row.central ? 'center' : [...new Set(row.labels.map(l => l.record.textPlacement.side))].sort().join('+');
            if (!active || row.central || active.sides !== sides) {
                const { record, owner } = row.labels[0];
                active = { ...owner, key: record.id, anchorX: lane.x, sides, elements: [], rects: [] };
                groups.push(active);
            }
            for (const { record } of row.labels) {
                active.elements.push(record.id);
                active.rects.push(...record.rects);
            }
            if (row.central) active = null;
        }
    }
    return groups.map(({ rects, ...group }) => ({ ...group, bounds: unionBounds(rects) }));
}

// Place measured label groups from the trunk outwards. Earlier lanes remain fixed.
export function horizontalLabelAdjustment(groups, targets, gap, verticalGap = 0) {
    const targetOf = group => {
        const keys = group.elements.map(id => targets.flatMap(target => target.prefixes
            .filter(prefix => id === prefix || id.startsWith(prefix + '.'))
            .map(prefix => ({ target, length: prefix.length })))
            .sort((a, b) => b.length - a.length)[0]?.target.key);
        return keys.length && keys.every(key => key === keys[0]) ? keys[0] : undefined;
    };
    const assigned = groups.map(group => ({ ...group, target: Math.abs(group.anchorX) < 0.1 ? undefined : targetOf(group) }));
    const fixed = assigned.filter(group => !group.target);
    const ordered = targets.map(target => ({ ...target, groups: assigned.filter(g => g.target === target.key) }))
        .filter(target => target.groups.length)
        .sort((a, b) => Math.min(...a.groups.map(g => Math.abs(g.anchorX))) - Math.min(...b.groups.map(g => Math.abs(g.anchorX))));
    for (const target of ordered) {
        const sign = target.side === 'left' ? -1 : 1;
        let delta = 0;
        // Each move may encounter another fixed rectangle. A finite outward scan finds the first clear slot.
        for (let pass = 0; pass <= fixed.length * target.groups.length; pass++) {
            let next = delta;
            for (const group of target.groups) for (const obstacle of fixed) {
                const a = group.bounds, b = obstacle.bounds;
                // Separate lanes also need clearance when their labels almost meet vertically.
                if (a.minY >= b.maxY + verticalGap || a.maxY <= b.minY - verticalGap) continue;
                const minX = a.minX + sign * delta, maxX = a.maxX + sign * delta;
                if (minX < b.maxX + gap && maxX > b.minX - gap) {
                    next = Math.max(next, sign < 0 ? a.maxX - b.minX + gap : b.maxX + gap - a.minX);
                }
            }
            if (next <= delta + 0.1) break;
            delta = next;
        }
        if (delta > 0.1) return { key: target.key, delta };
        fixed.push(...target.groups);
    }
    return null;
}

// Reclaim only measured additions, keeping authored defaults and the whole merge subtree together.
export function mergeBridgeReductions(records, targets, increments, rem, labelGap, verticalGap, lineGap, horizontalLineGap = lineGap, includeExtensions = false) {
    const reductions = {};
    for (const target of targets) {
        if (!/^merges\.\d+\.bridge_length$/.test(target.key) && !(includeExtensions && /^merges\.\d+\.extension_bridge_continuations\.\d+$/.test(target.key))) continue;
        let available = (increments[target.key] ?? 0) * rem;
        if (available <= .1) continue;
        const owns = id => target.prefixes.some(p => id === p || id.startsWith(p + '.'));
        const inward = target.side === 'left' ? 1 : -1;
        const alsoMoves = id => targets.some(t => t.key !== target.key && /^merges\.\d+\.bridge_length$/.test(t.key)
            && (increments[t.key] ?? 0) > 0 && t.prefixes.some(p => id === p || id.startsWith(p + '.')));
        const limit = (a, b, gap, yGap, shared = false) => {
            if (a.minY >= b.maxY + yGap || a.maxY <= b.minY - yGap) return;
            // Obstacles behind the moving part cannot be hit by an inward move.
            if (inward > 0 && b.maxX <= a.minX - gap || inward < 0 && b.minX >= a.maxX + gap) return;
            const distance = inward > 0 ? b.minX - gap - a.maxX : a.minX - gap - b.maxX;
            available = Math.min(available, Math.max(0, distance / (shared ? 2 : 1)));
        };
        const groups = textLabelGroups(records);
        const movingGroups = groups.filter(g => g.elements.every(owns));
        const fixedGroups = groups.filter(g => !g.elements.every(owns));
        for (const a of movingGroups) for (const b of fixedGroups) limit(a.bounds, b.bounds, labelGap, verticalGap, b.elements.some(alsoMoves));
        const labels = records.filter(r => r.kind === 'text' && r.visible !== false);
        const geometry = records.filter(r => r.kind === 'geometry' && r.visible !== false &&
            !/connector|\.node\.|joint-arrow|dev-counter/.test(r.id));
        for (const label of labels) for (const line of geometry) {
            if (owns(label.id) === owns(line.id)) continue;
            // Main bridge shrinks and its exit arc stays fixed; neither sweeps towards a label.
            if (owns(line.id) && (line.id === target.element || line.id.startsWith(target.owner + '.paths.merge.arc.out'))) continue;
            for (const rect of line.rects) {
                if (owns(label.id)) limit(unionBounds(label.rects), unionBounds([rect]), horizontalLineGap, lineGap, alsoMoves(line.id));
                else limit(unionBounds([rect]), unionBounds(label.rects), horizontalLineGap, lineGap, alsoMoves(label.id));
            }
        }
        // Small conservative rounding avoids re-extension caused by CSS/subpixel differences.
        const reduction = Math.floor(Math.max(0, available - .25) / rem * 10000) / 10000;
        if (reduction > .01) reductions[target.key] = reduction;
    }
    return reductions;
}

// Preview both prop changes together. A failed candidate leaves both authored lengths untouched.
export function coupledMergeCompaction(records, targets, increments, rem, labelGap, verticalGap, lineGap, stemBudget, bridgeBudget, validate = () => true, horizontalLineGap = lineGap) {
    if (!(stemBudget > 0 && bridgeBudget > 0)) return null;
    for (const target of targets) {
        if (!target.coupledStem || !(increments[target.key] > 0)) continue;
        const stem = records.find(r => r.id === target.coupledStem.element);
        if (!stem?.rects.length) continue;
        const boundary = unionBounds(stem.rects).minY;
        const ownMain = r => r.id.startsWith(target.owner + '.paths.merge.');
        // Budgeted, coupled move; never scan down through the whole trunk to find an empty region.
        const down = stemBudget * rem;
        let lowered = records.map(r => {
            if (!ownMain(r)) return r;
            if (r.id === stem.id) return { ...r, rects: r.rects.map(b => ({ ...b, y: b.y - down, height: b.height + down })) };
            if (r.kind !== 'text' && unionBounds(r.rects).minY > boundary + .1) return r;
            return { ...r, textPlacement:r.textPlacement ? { ...r.textPlacement, y:r.textPlacement.y-down } : undefined, rects: r.rects.map(b => ({ ...b, y: b.y - down })) };
        });
        const extensions = [];
        for (const extension of targets.filter(t => t.parentPrefix === target.owner && t.compactStem && t.compactStemElement)) {
            const adjustment = mergeExtensionStagger(lowered, [extension], verticalGap);
            if (!adjustment) continue;
            const tail = lowered.find(r => r.id === extension.compactStemElement);
            if (!tail?.rects.length || adjustment.delta > down + .1) { extensions.push(null); break; }
            const edge = unionBounds(tail.rects).minY;
            lowered = lowered.map(r => {
                if (!r.id.startsWith(extension.prefixes[0] + '.')) return r;
                if (r.id === tail.id) return { ...r, rects: r.rects.map(b => ({ ...b, y:b.y-adjustment.delta, height:b.height+adjustment.delta })) };
                if (r.kind !== 'text' && unionBounds(r.rects).minY > edge + .1) return r;
                return { ...r, textPlacement:r.textPlacement ? { ...r.textPlacement, y:r.textPlacement.y-adjustment.delta } : undefined, rects:r.rects.map(b => ({ ...b, y:b.y-adjustment.delta })) };
            });
            extensions.push({ stem:adjustment.key, addition:Math.ceil(adjustment.delta/rem*10000)/10000 });
        }
        if (extensions.includes(null)) continue;
        // If the largest gain obstructs a neighbour, try smaller coupled gains within the same budget.
        for (let budget = Math.min(increments[target.key], bridgeBudget); budget > stemBudget; budget -= .5) {
            const extra = { [target.key]: budget };
            const reduction = mergeBridgeReductions(lowered, [target], extra, rem, labelGap, verticalGap, lineGap, horizontalLineGap)[target.key];
            // Spending height must buy more width; otherwise retain the existing arrangement.
            if (!reduction || reduction <= stemBudget) continue;
            const dx = (target.side === 'left' ? 1 : -1) * reduction * rem;
            const moved = lowered.map(r => {
                if (!target.prefixes.some(p => r.id.startsWith(p + '.'))) return r;
                if (r.id.startsWith(target.owner + '.paths.merge.arc.out')) return r;
                if (r.id === target.element) return { ...r, rects: r.rects.map(b => ({ ...b,
                    x: b.x + (dx > 0 ? dx : 0), width: Math.max(0, b.width - Math.abs(dx)) })) };
                return { ...r, textPlacement:r.textPlacement ? { ...r.textPlacement, x:r.textPlacement.x+dx } : undefined, rects: r.rects.map(b => ({ ...b, x: b.x + dx })) };
            });
            // Test internal extension interactions too, not just the external trunk labels.
            if (mergeExtensionStemClearance(moved, targets, horizontalLineGap, lineGap)) continue;
            const groups = textLabelGroups(moved);
            if (horizontalLabelAdjustment(groups, targets, labelGap, verticalGap) || !validate(moved)) continue;
            const original = new Map(textLabelGroups(records).map(g => [g.key, g]));
            const overlap = (a, b) => a.minX < b.maxX + labelGap && a.maxX > b.minX - labelGap
                && a.minY < b.maxY + verticalGap && a.maxY > b.minY - verticalGap;
            if (groups.some((a, i) => groups.slice(i + 1).some(b => overlap(a.bounds, b.bounds)
                && (!original.has(a.key) || !original.has(b.key) || !overlap(original.get(a.key).bounds, original.get(b.key).bounds))))) continue;
            // Reject newly introduced label/line contacts, including the stretched stem itself.
            const old = new Map(records.map(r => [r.id, r]));
            const labels = moved.filter(r => r.kind === 'text' && r.visible !== false);
            const lines = moved.filter(r => r.kind === 'geometry' && r.visible !== false && !/connector|\.node\.|joint-arrow|dev-counter/.test(r.id));
            const contact = (a, b) => a.minX < b.maxX + horizontalLineGap && a.maxX > b.minX - horizontalLineGap
                && a.minY < b.maxY + lineGap && a.maxY > b.minY - lineGap;
            if (labels.some(a => lines.some(b => !b.id.startsWith(a.id + '.') && !a.id.startsWith(b.id + '.')
                && b.rects.some((rect, i) => contact(unionBounds(a.rects), unionBounds([rect]))
                    && !contact(unionBounds(old.get(a.id).rects), unionBounds([old.get(b.id).rects[i]])))))) continue;
            return { bridge: target.key, reduction, stem: target.coupledStem.key, addition: stemBudget, extensions };
        }
    }
    return null;
}

// Optimize explicitly enabled extension lanes after the established layout has settled.
// Earlier lanes stay fixed; later lanes follow their existing chained anchor horizontally.
export function mergeExtensionCompactions(records, targets, increments, rem, labelGap, verticalGap, lineGap, horizontalLineGap, validate = () => true) {
    const candidates = [];
    let current = records;
    for (const target of targets.filter(t => t.extensionCompactionStem)) {
        const available = increments[target.key] ?? 0;
        if (!(available > .01)) continue;
        const index = Number(target.key.split('.').at(-1));
        const followers = targets.filter(t => t.parentPrefix === target.parentPrefix && Number(t.key.split('.').at(-1)) >= index);
        const owns = id => followers.some(t => t.prefixes.some(p => id.startsWith(p + '.')));
        const own = id => id.startsWith(target.prefixes[0] + '.');
        const stem = current.find(r => r.id === target.extensionCompactionStem.element);
        const labels = current.filter(r => r.kind === 'text' && r.visible !== false && own(r.id));
        if (!stem?.rects.length || !labels.length) continue;
        const dx = (target.side === 'left' ? 1 : -1) * available * rem;
        const groups = textLabelGroups(current);
        let down = 0;
        for (const a of groups.filter(g => g.elements.every(own))) for (const b of groups.filter(g => !g.elements.some(owns))) {
            if (a.bounds.minX + dx >= b.bounds.maxX + labelGap || a.bounds.maxX + dx <= b.bounds.minX - labelGap) continue;
            if (a.bounds.maxY <= b.bounds.minY - verticalGap || a.bounds.minY >= b.bounds.maxY + verticalGap) continue;
            down = Math.max(down, a.bounds.maxY - b.bounds.minY + verticalGap);
        }
        // Round conservatively; the authored stem itself is never shortened.
        const loweredAddition = Math.ceil((down + .25) / rem * 10000) / 10000;
        // If lowering blocks compaction, retain the height and reclaim only measured horizontal space.
        for (const addition of [loweredAddition, 0]) {
            if (addition < 0 || addition > 200) continue;
            down = addition * rem;
            const edge = unionBounds(stem.rects).minY;
            const lowered = current.map(r => {
                if (!own(r.id)) return r;
                if (r.id === stem.id) return { ...r, rects:r.rects.map(b => ({ ...b, y:b.y-down, height:b.height+down })) };
                if (r.kind !== 'text' && unionBounds(r.rects).minY > edge + .1) return r;
                return { ...r, textPlacement:r.textPlacement ? { ...r.textPlacement,y:r.textPlacement.y-down } : undefined,
                    rects:r.rects.map(b => ({ ...b,y:b.y-down })) };
            });
            // Reuse the measured clearance sweep, with this extension and its followers as the moving subtree.
            const sweep = { ...target,prefixes:followers.flatMap(t => t.prefixes),owner:target.prefixes[0] };
            const reduction = mergeBridgeReductions(lowered,[sweep],{[sweep.key]:available},rem,labelGap,verticalGap,lineGap,horizontalLineGap,true)[sweep.key];
            if (!(reduction > .01)) continue;
            const shift = (target.side === 'left' ? 1 : -1) * reduction * rem;
            const moved = lowered.map(r => {
                if (!owns(r.id)) return r;
                if (r.id === target.element) return { ...r,rects:r.rects.map(b => ({...b,x:b.x+(shift>0?shift:0),width:Math.max(0,b.width-Math.abs(shift))})) };
                return { ...r,textPlacement:r.textPlacement ? {...r.textPlacement,x:r.textPlacement.x+shift} : undefined,
                    rects:r.rects.map(b => ({...b,x:b.x+shift})) };
            });
            if (mergeExtensionStemClearance(moved,targets,horizontalLineGap,lineGap)
                || horizontalLabelAdjustment(textLabelGroups(moved),targets,labelGap,verticalGap) || !validate(moved)) continue;
            candidates.push({bridge:target.key,reduction,stem:target.extensionCompactionStem.key,addition});
            current = moved;
            break;
        }
    }
    return candidates;
}

// Lower the first extension's label block beneath the main merge before placing lanes horizontally.
export function mergeExtensionStagger(records, targets, gap) {
    const labels = records.filter(r => r.kind === 'text' && r.visible !== false && r.rects.length);
    for (const target of targets) {
        if (!target.compactStem) continue;
        const own = labels.filter(r => target.prefixes.some(p => r.id.startsWith(p + '.')));
        const main = labels.filter(r => r.id.startsWith(target.parentPrefix + '.') &&
            !r.id.startsWith(target.parentPrefix + '.extension.'));
        if (!own.length || !main.length) continue;
        const a = unionBounds(own.flatMap(r => r.rects)), b = unionBounds(main.flatMap(r => r.rects));
        const delta = a.maxY - b.minY + gap;
        if (delta > .1) return { key: target.compactStem, delta };
    }
    return null;
}

// An extension's incoming stem must clear the labels of the main/earlier merge lanes.
// Extend that extension's bridge; moving the main merge would move both obstacles together.
export function mergeExtensionStemClearance(records, targets, gap, verticalGap = gap) {
    for (const target of targets) {
        if (!target.parentPrefix) continue;
        const prefix = target.prefixes[0];
        const index = Number(prefix.slice(prefix.lastIndexOf('.') + 1));
        const labels = records.filter(r => r.kind === 'text' && r.visible !== false && r.rects.length &&
            r.id.startsWith(target.parentPrefix + '.') && !r.id.startsWith(prefix + '.') &&
            (!r.id.startsWith(target.parentPrefix + '.extension.') ||
                Number(r.id.slice((target.parentPrefix + '.extension.').length).split('.')[0]) < index));
        const stems = records.filter(r => r.kind === 'geometry' && r.visible !== false &&
            r.id.startsWith(prefix + '.') && /\.stem\d+$/.test(r.id));
        let delta = 0;
        for (const stem of stems) for (const rect of stem.rects) for (const label of labels) {
            const a = unionBounds([rect]), b = unionBounds(label.rects);
            if (a.minY >= b.maxY + verticalGap || a.maxY <= b.minY - verticalGap) continue;
            if (a.minX >= b.maxX + gap || a.maxX <= b.minX - gap) continue;
            delta = Math.max(delta, target.side === 'left' ? a.maxX - b.minX + gap : b.maxX + gap - a.minX);
        }
        if (delta > .1) return { key: target.key, delta };
        // A later extension's labels can approach this stem from the other side.
        // Move that later lane, because moving this bridge also moves its descendants.
        for (const later of targets.filter(t => t.parentPrefix === target.parentPrefix
            && Number(t.prefixes[0].slice(t.prefixes[0].lastIndexOf('.') + 1)) > index)) {
            const laterLabels = records.filter(r => r.kind === 'text' && r.visible !== false
                && r.id.startsWith(later.prefixes[0] + '.') && r.rects.length);
            let extra = 0;
            for (const stem of stems) for (const rect of stem.rects) for (const label of laterLabels) {
                const a = unionBounds([rect]), b = unionBounds(label.rects);
                if (a.minY >= b.maxY + verticalGap || a.maxY <= b.minY - verticalGap
                    || a.minX >= b.maxX + gap || a.maxX <= b.minX - gap) continue;
                extra = Math.max(extra, later.side === 'left' ? b.maxX - a.minX + gap : a.maxX + gap - b.minX);
            }
            if (extra > .1) return { key: later.key, delta: extra };
        }
    }
    return null;
}

// Measure individual labels, not their enclosing group rectangle. Trunk runs bottom-to-top.
export function verticalLabelAdjustment(records, targets, gap) {
    if (targets.some(t => t.lane)) {
        const lanes = [...new Set(targets.map(t => t.lane ?? 'trunk'))];
        for (const lane of lanes) {
            const next = verticalLabelAdjustment(records, targets.filter(t => (t.lane ?? 'trunk') === lane).map(({lane, ...target}) => target), gap);
            if (next) return next;
        }
        return null;
    }
    const rows = [];
    for (const record of records) {
        if (record.kind !== 'text' || record.visible === false || !record.textPlacement || !record.rects.length) continue;
        const target = [...targets].sort((a,b) => b.prefix.length - a.prefix.length).find(t => record.id === t.prefix || record.id.startsWith(t.prefix + '.'));
        if (!target) continue;
        const { x, y } = record.textPlacement;
        let row = rows.find(r => Math.abs(r.y - y) < .1 && Math.abs(r.x - x) < .1);
        if (!row) { row = { x, y, target, labels: [] }; rows.push(row); }
        row.labels.push(unionBounds(record.rects));
    }
    rows.sort((a, b) => a.y - b.y);
    for (let i = 1; i < rows.length; i++) {
        const upper = rows[i];
        let delta = 0;
        // Also consider tall labels reaching past an intervening row on the other side.
        for (let j = 0; j < i; j++) {
            for (const a of rows[j].labels) for (const b of upper.labels) {
                if (a.minX < b.maxX && a.maxX > b.minX) delta = Math.max(delta, gap - (b.minY - a.maxY));
            }
        }
        if (delta > .1 && !upper.target.fixed) return { key: upper.target.key, delta };
    }
    return null;
}

// A trunk stem moves everything attached after it. Never extend a stem that moves
// both the obstacle and the label: use their attachment order, not screen proximity.
export function lineLabelAdjustment(records, targets, attachments, gap, horizontalTargets = []) {
    const owners = horizontalTargets.flatMap(target => target.prefixes.map(prefix => ({ target, prefix })))
        .sort((a, b) => b.prefix.length - a.prefix.length);
    const ownerOf = id => owners.find(o => id === o.prefix || id.startsWith(o.prefix + '.'))?.target;
    const orderedAttachments = [...attachments].sort((a, b) => b.prefix.length - a.prefix.length);
    const attachmentOf = id => orderedAttachments
        .find(a => id === a.prefix || id.startsWith(a.prefix + '.'));
    const labels = records.filter(r => r.kind === 'text' && r.visible !== false && r.textPlacement && r.rects.length)
        .flatMap(record => {
            const attached = attachmentOf(record.id);
            if (attached) return [{ row: attached.row, bounds: unionBounds(record.rects), owner: ownerOf(record.id) }];
            const index = targets.findIndex(t => record.id.startsWith(t.prefix + '.'));
            if (index < 0) return [];
            // The central start label is at the beginning, side labels at the end of startLength.
            const row = index === 0 && !['left', 'right'].includes(record.textPlacement.side) ? 0 : index + 1;
            return [{ row, bounds: unionBounds(record.rects) }];
        });
    // Terminal stems and caps can cross a bridge even when their label is already above it.
    // Detect against the actual pieces; move the complete terminal including its label clear.
    for (const record of records) {
        if (record.kind !== 'geometry' || record.visible === false || !record.rects.length ||
            !horizontalTargets.some(t => (t.terminals ?? []).includes(record.id))) continue;
        const owner = ownerOf(record.id), attached = attachmentOf(record.id);
        if (!owner || !attached) continue;
        const terminalLabels = records.filter(r => r.kind === 'text' && r.visible !== false &&
            r.id.startsWith(record.id + '.'));
        const clearanceBounds = unionBounds([...record.rects, ...terminalLabels.flatMap(r => r.rects)]);
        for (const rect of record.rects) labels.push({ row: attached.row, owner,
            bounds: unionBounds([rect]), clearanceBounds, terminal: true });
    }
    for (const record of records) {
        if (record.kind !== 'geometry' || record.visible === false || !record.rects.length) continue;
        if (!/\.(?:bridge|arc)(?:[.-]|\d|$)/.test(record.id) || /connector|\.node\.|joint-arrow|dev-counter/.test(record.id)) continue;
        const attachment = attachmentOf(record.id);
        if (!attachment) continue;
        for (const label of labels) {
            const obstacleOwner = ownerOf(record.id);
            const sameRow = label.row === attachment.row;
            // A shared trunk attachment does not make two separate paths the same lane.
            if (sameRow && label.owner?.key === obstacleOwner?.key) continue;
            const target = targets[Math.max(label.row, attachment.row) - 1];
            let delta = 0;
            for (const rect of record.rects) {
                const line = unionBounds([rect]), text = label.bounds;
                if (line.minX >= text.maxX || line.maxX <= text.minX ||
                    line.minY >= text.maxY + gap || line.maxY <= text.minY - gap) continue;
                delta = Math.max(delta, attachment.row >= label.row
                    ? text.maxY + gap - line.minY : line.maxY + gap - text.minY);
            }
            if (delta > .1) {
                if (label.owner && obstacleOwner && label.owner.key !== obstacleOwner.key) {
                    // Move the label's owning path beyond the foreign bridge, not the bridge across the label.
                    const line = unionBounds(record.rects);
                    const clearance = label.clearanceBounds ?? label.bounds;
                    const outward = label.owner.side === 'left'
                        ? clearance.maxX - line.minX + gap
                        : line.maxX + gap - clearance.minX;
                    return { key: label.owner.key, delta: outward };
                }
                if (sameRow && obstacleOwner?.entryStem && !label.owner) {
                    return { key: obstacleOwner.entryStem, delta };
                }
                if (!sameRow && target && !label.terminal) return { key: target.key, delta };
            }
        }
    }
    return null;
}

const horizontalRequests = new WeakMap();
const compactionMeasurements = new WeakMap();
function updateHorizontalLabelLayout(graph, records, issues) {
    const script = graph.querySelector('[data-tw-graph-horizontal-layout]');
    const wireRoot = graph.closest('[wire\\:id]');
    if (!script || !wireRoot || !window.Livewire) return;
    if (issues.length) { graph.dataset.twGraphHorizontalStatus = 'failed'; return; }
    const meta = JSON.parse(script.textContent);
    const rem = parseFloat(getComputedStyle(document.documentElement).fontSize);
    const groups = textLabelGroups(records);
    const vertical = verticalLabelAdjustment(records, meta.verticalTargets ?? [], Math.max(0, meta.verticalGapRem ?? 1) * rem);
    const stagger = vertical ? null : mergeExtensionStagger(records, meta.targets, Math.max(0, meta.interStrangVerticalGapRem ?? 3) * rem);
    const extensionClearance = vertical || stagger ? null : mergeExtensionStemClearance(records, meta.targets, Math.max(0, meta.horizontalLineGapRem ?? 3) * rem, Math.max(0, meta.lineGapRem ?? 1) * rem);
    const line = vertical || stagger || extensionClearance ? null : lineLabelAdjustment(records, (meta.verticalTargets ?? []).filter(t => !t.lane), meta.lineAttachments ?? [], Math.max(0, meta.lineGapRem ?? 1) * rem, meta.targets);
    const next = vertical ?? stagger ?? extensionClearance ?? line ?? horizontalLabelAdjustment(groups, meta.targets, Math.max(0, meta.gapRem ?? 3) * rem, Math.max(0, meta.interStrangVerticalGapRem ?? 3) * rem);
    const remaining = groups.some((a, i) => groups.slice(i + 1).some(b =>
        a.bounds.minX < b.bounds.maxX && a.bounds.maxX > b.bounds.minX &&
        a.bounds.minY < b.bounds.maxY && a.bounds.maxY > b.bounds.minY));
    const recheck = !next && !remaining && meta.recheckTrunk;
    // Layout writes above can still change label wrapping in this frame. Optimize only after
    // a fresh measurement of the settled DOM, never from the rectangles preceding those writes.
    const measurementKey = `${meta.datasetId}:${meta.tick}:${meta.passes}:${meta.compactionRevision ?? 0}:${!!meta.extensionCompactionDone}`;
    if (next || remaining || recheck) compactionMeasurements.delete(graph);
    else if (Object.keys(meta.mergeBridgeExtras ?? {}).some(key => /^merges\.\d+\.(bridge_length|extension_bridge_continuations\.\d+)$/.test(key))) {
        let pending = compactionMeasurements.get(graph);
        if (pending?.key !== measurementKey) {
            pending = { key: measurementKey, ready: false };
            compactionMeasurements.set(graph, pending);
            setTimeout(() => {
                if (!graph.isConnected || compactionMeasurements.get(graph) !== pending) return;
                pending.ready = true;
                const fresh = resolveGraphBounds(graph);
                if (fresh) updateHorizontalLabelLayout(graph, fresh.records, fresh.issues);
            }, 150);
        }
        if (!pending.ready) { graph.dataset.twGraphHorizontalStatus = 'adjusting'; return; }
    }
    const coupled = !next && !remaining && !recheck
        ? coupledMergeCompaction(records, meta.targets, meta.mergeBridgeExtras ?? {}, rem,
            (meta.gapRem ?? 3) * rem, (meta.interStrangVerticalGapRem ?? 3) * rem, (meta.lineGapRem ?? 1) * rem,
            meta.mergeStemBudgetRem ?? 3, meta.mergeBridgeBudgetRem ?? 8,
            moved => !verticalLabelAdjustment(moved, meta.verticalTargets ?? [], (meta.verticalGapRem ?? 1) * rem)
                && !lineLabelAdjustment(moved, (meta.verticalTargets ?? []).filter(t => !t.lane), meta.lineAttachments ?? [], (meta.lineGapRem ?? 1) * rem, meta.targets), (meta.horizontalLineGapRem ?? 3) * rem) : null;
    const reductions = !coupled && !next && !remaining && !recheck
        ? mergeBridgeReductions(records, meta.targets, meta.mergeBridgeExtras ?? {}, rem,
            Math.max(0, meta.gapRem ?? 3) * rem, Math.max(0, meta.interStrangVerticalGapRem ?? 3) * rem, Math.max(0, meta.lineGapRem ?? 1) * rem, Math.max(0, meta.horizontalLineGapRem ?? 3) * rem) : {};
    const extensionCandidates = !next && !remaining && !recheck && !coupled && !Object.keys(reductions).length && !meta.extensionCompactionDone
        ? mergeExtensionCompactions(records, meta.targets, meta.mergeBridgeExtras ?? {}, rem,
            (meta.gapRem ?? 3)*rem, (meta.interStrangVerticalGapRem ?? 3)*rem, (meta.lineGapRem ?? 1)*rem, (meta.horizontalLineGapRem ?? 3)*rem,
            moved => !verticalLabelAdjustment(moved, meta.verticalTargets ?? [], (meta.verticalGapRem ?? 1)*rem)
                && !lineLabelAdjustment(moved, (meta.verticalTargets ?? []).filter(t => !t.lane), meta.lineAttachments ?? [], (meta.lineGapRem ?? 1)*rem, meta.targets)) : [];
    const extensionCompact = extensionCandidates.length > 0;
    const compact = !!coupled || Object.keys(reductions).length > 0 || extensionCompact;
    graph.dataset.twGraphHorizontalStatus = next || recheck || compact ? 'adjusting' : 'complete';
    if (!next && !remaining && !recheck && !compact) return;
    if ((!next && !recheck && !compact) || (meta.passes >= (meta.passLimit ?? 40) && !compact) || (next && next.delta / rem > 200)) {
        graph.dataset.twGraphHorizontalStatus = 'unresolved';
        const warning = graph.querySelector('[data-tw-graph-bounds-warning]');
        if (warning) {
            warning.hidden = false;
            const details = warning.querySelector('[data-tw-graph-bounds-warning-details]');
            const message = 'Measured label layout did not converge; no further lengths were changed.';
            if (details.textContent !== message) details.textContent = message;
        }
        return;
    }
    const signature = `${meta.datasetId}:${meta.tick}:${meta.passes}:${meta.compactionRevision ?? 0}:${!!meta.extensionCompactionDone}`;
    if (horizontalRequests.get(wireRoot) === signature) return;
    horizontalRequests.set(wireRoot, signature);
    const component = window.Livewire.find(wireRoot.getAttribute('wire:id'));
    const request = extensionCompact
        ? component.call('compactMeasuredExtensions', meta.datasetId, meta.tick, extensionCandidates, meta.passes)
        : coupled
        ? component.call('compactMeasuredMerge', meta.datasetId, meta.tick, coupled, meta.passes)
        : compact
        ? component.call('reduceMeasuredMergeBridges', meta.datasetId, meta.tick, reductions, meta.passes)
        : recheck
        ? component.call('recheckMeasuredTrunkClearance', meta.datasetId, meta.tick, meta.passes)
        : component.call('extendMeasuredLabelLayout', meta.datasetId, meta.tick, next.key, Math.ceil(next.delta / rem * 10000) / 10000, meta.passes, vertical ? 'label' : line ? 'line' : 'horizontal');
    request.catch(error => { graph.dataset.twGraphHorizontalStatus = 'failed'; graph.dispatchEvent(new CustomEvent('tw-graph-layout-failed', { bubbles: true })); console.error('Measured label layout', error); });
}

function updateComponentRegions(graph, canvas, records, layout) {
    const regions = new Map(componentRegions(records).map(region => [String(region.token), region]));
    const textGroups = new Map(textLabelGroups(records).map(region => [region.key, region]));
    for (const box of canvas.querySelectorAll('[data-tw-graph-file-region]')) {
        if (box.closest(graphSelector) !== graph) continue;
        const members = JSON.parse(box.dataset.twGraphFileTokens).map(token => regions.get(String(token))).filter(Boolean);
        if (!layout || !members.length) { box.style.visibility = 'hidden'; continue; }
        const minX = Math.min(...members.map(r => r.bounds.minX));
        const minY = Math.min(...members.map(r => r.bounds.minY));
        const maxX = Math.max(...members.map(r => r.bounds.maxX));
        const maxY = Math.max(...members.map(r => r.bounds.maxY));
        const styles = { left: `${layout.originLeft + minX}px`, bottom: `${layout.originBottom + minY}px`,
            width: `${maxX - minX}px`, height: `${maxY - minY}px`, visibility: 'visible' };
        for (const [name, value] of Object.entries(styles)) if (box.style[name] !== value) box.style[name] = value;
    }
    for (const box of canvas.querySelectorAll('[data-tw-graph-region]')) {
        if (box.closest(graphSelector) !== graph) continue;
        const region = box.dataset.twGraphRegionKind === 'strang-text-labels'
            ? textGroups.get(box.dataset.twGraphTextGroupSlot) : regions.get(box.dataset.twGraphRegion);
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
    const captions = [...canvas.querySelectorAll('[data-tw-graph-dev-caption]')]
        .filter(caption => caption.closest(graphSelector) === graph);
    // Batch constraints first, then measure everything before positioning any caption.
    const maxWidth = `${width}px`;
    for (const caption of captions) {
        if (caption.style.maxWidth !== maxWidth) caption.style.maxWidth = maxWidth;
    }
    const measurements = [];
    for (const caption of captions) {
        if (caption.closest(graphSelector) !== graph || !caption.getClientRects().length || getComputedStyle(caption).visibility === 'hidden') continue;
        const box = caption.parentElement;
        const boxLeft = (box.getBoundingClientRect().left - frame.left) / scale + box.clientLeft;
        const captionWidth = caption.getBoundingClientRect().width / scale;
        const boxTop = (box.getBoundingClientRect().top - frame.top) / scaleY + box.clientTop;
        const captionHeight = caption.getBoundingClientRect().height / scaleY;
        measurements.push({ caption, boxLeft, boxTop, captionWidth, captionHeight });
    }
    const placements = [];
    for (const { caption, boxLeft, boxTop, captionWidth, captionHeight } of measurements) {
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
        placements.push({ caption, styles });
    }
    for (const { caption, styles } of placements) {
        for (const [name, value] of Object.entries(styles)) if (caption.style[name] !== value) caption.style[name] = value;
    }
}

export function boundsDifference(expected, actual, tolerance = 1.1) {
    return ['minX', 'minY', 'maxX', 'maxY'].filter(key => Math.abs(expected[key] - actual[key]) > tolerance);
}

// Keep the diagnostic/event payload unchanged; format only its visible presentation.
export function boundsIssueDetails(issue) {
    const separator = issue.indexOf(': ');
    const id = separator < 0 ? '' : issue.slice(0, separator);
    const message = separator < 0 ? issue : issue.slice(separator + 2);
    const values = [...message.matchAll(/(minX|minY|maxX|maxY) expected=(-?[\d.]+)px actual=(-?[\d.]+)px/g)]
        .map(([, axis, expected, actual]) => ({ axis, expected, actual, delta: round(Number(actual) - Number(expected)) }));
    return { id, message, values };
}

export function renderBoundsIssues(container, issues) {
    if (!container) return;
    const doc = container.ownerDocument;
    const sections = issues.map(issue => {
        const { id, message, values } = boundsIssueDetails(issue);
        const section = doc.createElement('section');
        section.className = 'border-t border-amber-300 py-2';
        const heading = doc.createElement('code');
        heading.className = 'block whitespace-normal break-all font-semibold';
        heading.textContent = id || message;
        section.append(heading);
        if (values.length) {
            const table = doc.createElement('table');
            table.className = 'mt-2 w-full text-right font-mono tabular-nums';
            const header = table.createTHead().insertRow();
            for (const label of ['axis', 'expected', 'actual', 'delta']) {
                const cell = doc.createElement('th');
                cell.className = 'px-2 py-1';
                cell.scope = 'col';
                cell.textContent = container.dataset[label] || label;
                header.append(cell);
            }
            const body = table.createTBody();
            for (const value of values) {
                const row = body.insertRow();
                for (const text of [value.axis, `${value.expected}px`, `${value.actual}px`, `${value.delta > 0 ? '+' : ''}${value.delta}px`]) {
                    const cell = row.insertCell();
                    cell.className = 'px-2 py-1 whitespace-nowrap';
                    cell.textContent = text;
                }
            }
            section.append(table);
        } else if (id) {
            const detail = doc.createElement('p');
            detail.textContent = message;
            section.append(detail);
        }
        return section;
    });
    container.replaceChildren(...sections);
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
    const source = JSON.parse(manifest.textContent);
    const elements = [...canvas.querySelectorAll('[data-tw-graph-bounds]')]
        .filter(el => el.closest(graphSelector) === graph && !el.hasAttribute('data-tw-graph-jump-generated'));
    const derived = [...canvas.querySelectorAll('[data-tw-graph-jump-generated][data-tw-graph-bounds]')].filter(el => el.closest(graphSelector) === graph);
    const derivedEntries = derived.map(element => JSON.parse(element.dataset.twGraphBounds));
    const expressions = new Set([...source, ...derivedEntries].flatMap(entry => [...entry.rects.flatMap(rect => Object.values(rect)), ...(entry.textPlacement ? [entry.textPlacement.x, entry.textPlacement.y] : [])]));
    // Resolve CSS in batches: no write/read/write loop that lays out the entire graph per length.
    const probes = new Map();
    const fragment = document.createDocumentFragment();
    for (const expression of expressions) {
        const probe = document.createElement('span');
        probe.dataset.twGraphBoundsResolver = '';
        probe.style.cssText = 'position:absolute;visibility:hidden;pointer-events:none;width:0;height:0;';
        probe.style.setProperty('--tw-graph-bounds-length', expression);
        fragment.append(probe);
        probes.set(expression, probe);
    }
    canvas.append(fragment);
    const lengths = new Map(), resolvedValues = new Map();
    try {
        for (const [expression, probe] of probes) {
            const resolved = getComputedStyle(probe).getPropertyValue('--tw-graph-bounds-length').trim();
            if (!resolved || /^(auto|initial|inherit|unset|revert)$/.test(resolved) || !CSS.supports('left', resolved)) {
                lengths.set(expression, new Error(`Unresolvable length: ${expression}`));
            } else resolvedValues.set(expression, resolved);
        }
        for (const [expression, resolved] of resolvedValues) probes.get(expression).style.left = resolved;
        for (const [expression] of resolvedValues) {
            const value = parseFloat(getComputedStyle(probes.get(expression)).left);
            lengths.set(expression, Number.isFinite(value) ? value : new Error(`Unresolvable length: ${expression}`));
        }
    } finally {
        for (const probe of probes.values()) probe.remove();
    }
    const length = expression => {
        const value = lengths.get(expression);
        if (value instanceof Error) throw value;
        return value;
    };
    const local = rect => ({ x: (rect.left - zero.left) / scaleX, y: (zero.bottom - rect.bottom) / scaleY,
        width: (rect.right - rect.left) / scaleX, height: (rect.bottom - rect.top) / scaleY });
    const records = [], issues = [];
    let unresolved = false;
    const consume = (element, entry) => {
        try {
            const declared = entry.rects.map(rect => Object.fromEntries(Object.entries(rect).map(([key, value]) => [key, length(value)])));
            const expected = unionBounds(declared);
            if (!element) {
                issues.push(`${entry.id}: declared primitive missing from DOM`);
                records.push({ ...entry, visible: false, rects: declared });
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
            records.push({ ...entry, textPlacement: entry.textPlacement ? { ...entry.textPlacement,
                x: length(entry.textPlacement.x), y: length(entry.textPlacement.y) } : null, visible: Boolean(shown) && !element.closest('[aria-hidden="true"]'),
                rects: entry.kind === 'text' && shown ? [local(rect)] : declared });
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
    derived.forEach((element, index) => consume(element, derivedEntries[index]));
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
            const measureStart = performance.now();
            const result = resolveGraphBounds(graph);
            if (!result) continue;
            const { canvas, origin, elements, records, issues, layout } = result;
            next.add(canvas); next.add(origin);
            elements.forEach(element => next.add(element));
            const warning = graph.querySelector('[data-tw-graph-bounds-warning]');
            if (warning && warning.hidden !== (issues.length === 0)) warning.hidden = issues.length === 0;
            const report = JSON.stringify(issues);
            if (reports.get(graph) !== report) {
                renderBoundsIssues(graph.querySelector('[data-tw-graph-bounds-warning-details]'), issues);
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
            updateHorizontalLabelLayout(graph, records, issues);
            positionDevCaptions(graph, canvas);
            const summary = canvasSummary(records, layout, parseFloat(style.width), parseFloat(style.height),
                parseFloat(getComputedStyle(document.documentElement).fontSize));
            for (const label of graph.querySelectorAll('[data-tw-graph-canvas-result]')) {
                if (label.closest(graphSelector) === graph) setText(label, summary[label.dataset.twGraphCanvasResult]);
            }
            graph.dispatchEvent(new CustomEvent('tw-graph-bounds-timed', { bubbles: true, detail: { duration: performance.now() - measureStart } }));
        }
        for (const element of observed) if (!next.has(element)) resize.unobserve(element);
        for (const element of next) if (!observed.has(element)) resize.observe(element);
        observed = next;
    }
    const internal = '[data-tw-graph-performance], [data-tw-graph-file-region], [data-tw-graph-region], [data-tw-graph-dev-caption], [data-tw-graph-bounds-resolver], [data-tw-graph-canvas-summary], [data-tw-graph-bounds-state], [data-tw-graph-bounds-warning]';
    new MutationObserver(records => {
        if (records.some(r => {
            if (r.target.closest?.(internal)) return false;
            if (r.type === 'childList') {
                return [...r.addedNodes, ...r.removedNodes].some(node => !node.matches?.(internal));
            }
            return true;
        })) schedule();
    }).observe(document, { subtree: true, childList: true, characterData: true, attributes: true,
        attributeFilter: ['style', 'class', 'hidden', 'data-boxes', 'data-files', 'data-tw-graph-bounds'] });
    window.addEventListener('resize', schedule);
    document.fonts?.ready.then(schedule);
    document.fonts?.addEventListener('loadingdone', schedule);
    schedule();
}
