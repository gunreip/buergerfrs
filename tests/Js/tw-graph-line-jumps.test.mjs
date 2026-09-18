import test from 'node:test';
import assert from 'node:assert/strict';
import { planLineJumps, jumpDrawing } from '../../resources/js/helper/tw-graph-line-jumps.js';

const bridge = { x: 0, y: 49, width: 200, height: 2, thickness: 2, horizontal: true };
const stem = (x) => ({ x: x - 1, y: 0, width: 2, height: 100, horizontal: false });
const radius = (value) => parseFloat(value) * (value.endsWith('rem') ? 16 : 1);

test('multiple jumps are ordered geometrically, with top and bottom semicircles', () => {
    const { jumps, errors } = planLineJumps(bridge, [
        { over: 'b', radius: '8px', side: 'bottom' },
        { over: 'a', radius: '6px', side: 'top' },
    ], (id) => stem(id === 'a' ? 50 : 150), radius);
    assert.equal(errors.length, 0);
    assert.deepEqual(jumps.map((j) => j.position), [50, 150]);
    const drawing = jumpDrawing(bridge, jumps);
    assert.match(drawing.lineMask, /to right/);
    assert.match(drawing.lineMask, /transparent 44px/);
    assert.deepEqual(drawing.placements.map(p => p.side), ['top', 'bottom']);
    assert.deepEqual(drawing.placements.map(p => p.x), [44, 142]);
});

for (const side of ['left', 'right']) {
    test(`vertical stem supports ${side} jumps`, () => {
        const owner = { ...stem(50), thickness: 2 };
        const { jumps, errors } = planLineJumps(owner, [{ over: 'bridge', side, radius: '8px' }], () => bridge, radius);
        assert.equal(errors.length, 0);
        assert.equal(jumps[0].position, 50);
        const drawing = jumpDrawing(owner, jumps);
        assert.match(drawing.lineMask, /to bottom/);
        assert.equal(drawing.placements[0].side, side);
        assert.equal(drawing.placements[0].y, 42);
    });
}

test('invalid entries leave other valid jumps intact and give specific reasons', () => {
    const targets = { valid: stem(50), overlap: stem(55), end: stem(3), outside: stem(250), short: { ...stem(100), height: 40 }, parallel: bridge };
    const { jumps, errors } = planLineJumps(bridge, [
        { over: 'missing' }, { over: 'parallel' }, { over: 'outside' }, { over: 'short' },
        { over: 'end', radius: '8px' }, { over: 'valid', radius: '8px' },
        { over: 'overlap', radius: '8px' }, { over: 'valid', radius: '8px', side: 'left' },
        { over: 'valid', radius: '-1px' }, null,
    ], (id) => targets[id], radius);
    assert.equal(jumps.length, 1);
    assert.equal(errors.length, 9);
    for (const part of ['missing', 'right angles', 'No crossing', 'endpoint', 'overlaps', 'orientation', 'Radius', 'Missing']) {
        assert.ok(errors.some((error) => error.reason.includes(part)), part);
    }
});

test('touching an endpoint is not a free crossing', () => {
    const result = planLineJumps(bridge, [{ over: 'touch', radius: '5px' }], () => ({ ...stem(100), height: 50 }), radius);
    assert.equal(result.jumps.length, 0);
    assert.match(result.errors[0].reason, /No crossing/);
});


test('jump placements clear every crossed layer', () => {
    const owner = { ...bridge, thickness: 4, zIndex: 7 };
    const { jumps } = planLineJumps(owner, [
        { over: 'low', radius: '8px' }, { over: 'high', radius: '8px' },
    ], (id) => ({ ...stem(id === 'low' ? 50 : 150), zIndex: id === 'low' ? 2 : 15 }), radius);
    const drawing = jumpDrawing(owner, jumps);
    assert.equal(drawing.placements.length, 2);
    assert.equal(drawing.zIndex, 16);
    assert.equal(jumpDrawing({ ...owner, zIndex: 20 }, jumps).zIndex, 21);
});
