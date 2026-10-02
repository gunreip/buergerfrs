import test from 'node:test';
import assert from 'node:assert/strict';
import { canvasLayout, unionBounds, boundsDifference, boundsIssueDetails, gridTicks, canvasSummary, captionOffset, componentRegions } from '../../resources/js/helper/tw-graph-bounds.js';

test('bounds diagnostics separate element IDs and measurements without losing general warnings', () => {
    const id = 'literature.overview.deep-reference.strang.flow-while.stem.after';
    assert.deepEqual(boundsIssueDetails(`${id}: minY expected=-1736px actual=-1029.05px, maxY expected=-1672px actual=-965.05px`), {
        id,
        message: 'minY expected=-1736px actual=-1029.05px, maxY expected=-1672px actual=-965.05px',
        values: [
            { axis: 'minY', expected: '-1736', actual: '-1029.05', delta: 706.95 },
            { axis: 'maxY', expected: '-1672', actual: '-965.05', delta: 706.95 },
        ],
    });
    assert.deepEqual(boundsIssueDetails('Bounds coverage: 2 declared / 1 rendered'), {
        id: 'Bounds coverage', message: '2 declared / 1 rendered', values: [],
    });
    assert.deepEqual(boundsIssueDetails('Unresolved geometry'), { id: '', message: 'Unresolved geometry', values: [] });
});

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


test('coordinate summary distinguishes CSS minimum size, tight content and origin-inclusive bounds', () => {
    const records = [{ rects: [{ x: 32, y: 64, width: 160, height: 320 }] }];
    const layout = canvasLayout(unionBounds(records[0].rects), 48, 32);
    assert.deepEqual(canvasSummary(records, layout, 640, 800, 16), {
        canvas: '40rem × 50rem', content: '10rem × 20rem', spacing: '5rem / 25rem',
    });
    assert.deepEqual(canvasSummary(records, null, 640, 800, 16), {
        canvas: '40rem × 50rem', content: '—', spacing: '—',
    });
});


test('visible diagnostic captions stay within canvas edges without changing graph dimensions', () => {
    assert.equal(captionOffset(100, 200, 800), 4);
    assert.equal(captionOffset(700, 200, 800), -100);
    assert.equal(captionOffset(-20, 200, 800), 20);
    assert.equal(captionOffset(700, 800, 800), -700);
});


test('component regions union emitted geometry and measured text, never unrelated calls', () => {
    const outer = { token: 1, id: 'outer', component: 'strang.flow-if' };
    const inner = { token: 2, id: 'inner', component: 'strang.flow-if' };
    const regions = componentRegions([
        { region: outer, rects: [{ x: -40, y: 0, width: 20, height: 100 }] },
        { region: outer, kind: 'text', rects: [{ x: -80, y: 100, width: 100, height: 45 }] },
        { region: inner, rects: [{ x: 200, y: 30, width: 20, height: 60 }] },
        { region: 1, rects: [{ x: -90, y: 30, width: 10, height: 10 }] },
        { rects: [{ x: -1000, y: -1000, width: 2000, height: 2000 }] },
    ]);
    assert.equal(regions.length, 2);
    assert.deepEqual(regions[0].bounds, { minX: -90, minY: 0, maxX: 20, maxY: 145 });
    assert.deepEqual(regions[1].bounds, { minX: 200, minY: 30, maxX: 220, maxY: 90 });
});
