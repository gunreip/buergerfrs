import test from 'node:test';
import assert from 'node:assert/strict';
import { canvasLayout, unionBounds, boundsDifference, gridTicks } from '../../resources/js/helper/tw-graph-bounds.js';

test('canvas uses the same origin-inclusive policy as the server, without centering', () => {
    const layout = canvasLayout({ minX: -320, maxX: 416, minY: 0, maxY: 384 }, 48, 32);
    assert.equal(layout.originLeft, 368);
    assert.equal(layout.width, 832);
    assert.equal(layout.originBottom, 32);
    assert.equal(layout.height, 448);
});

test('padding changes the outer frame and origin but never component coordinates', () => {
    const bounds = { minX: -300, maxX: 100, minY: -10, maxY: 500 };
    const small = canvasLayout(bounds, 16, 32), large = canvasLayout(bounds, 96, 32);
    assert.equal(large.width - small.width, 160);
    assert.equal(large.originLeft - small.originLeft, 80);
    assert.equal(large.minX, small.minX);
    assert.equal(large.height, small.height);
});

test('an unresolved mismatch is reported without changing either geometry', () => {
    const expected = { minX: -100, maxX: 40, minY: 0, maxY: 300 };
    const actual = { ...expected, maxY: 340 };
    assert.deepEqual(boundsDifference(expected, actual), ['maxY']);
    assert.equal(expected.maxY, 300);
    assert.deepEqual(boundsDifference(expected, { ...expected, minX: -100.5 }), []);
});

test('text and stroke extents contribute to the same union', () => {
    assert.deepEqual(unionBounds([
        { x: -2, y: 0, width: 4, height: 800 },
        { x: 10, y: 750, width: 200, height: 80 },
        { x: -24, y: -24, width: 48, height: 48 },
    ]), { minX: -24, minY: -24, maxX: 210, maxY: 830 });
});

test('grid ticks include negative coordinates and follow the shifted origin at rem spacing', () => {
    const ticks = gridTicks(240, 96, 16);
    assert.equal(ticks[0].value, -6);
    assert.equal(ticks.at(-1).value, 9);
    assert.deepEqual(ticks.find(t => t.value === 0), { value: 0, position: 96, major: true });
    assert.equal(ticks.find(t => t.value === -5).major, true);
    assert.equal(ticks.find(t => t.value === 1).position, 112);
    assert.equal(gridTicks(240, 112, 16).find(t => t.value === 0).position, 112);
});
