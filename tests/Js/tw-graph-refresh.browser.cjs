// Generate fixtures with render-tw-graph-refresh-fixtures.php, then pass that directory.
// Uses the installed Livewire runtime and intercepted fixture responses; no credentials or app server.
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const root = path.resolve(__dirname, '../..');
const directory = process.argv[2];
(async () => {
    const browser = await chromium.launch({ channel: 'chrome', headless: true });
    try {
        const page = await browser.newPage();
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        const initial = fs.readFileSync(path.join(directory, 'initial.html'), 'utf8');
        let response = 'same.html';
        await page.route('https://refresh.test/**', async route => {
            const url = new URL(route.request().url());
            if (url.pathname === '/livewire.js') return route.fulfill({ contentType: 'application/javascript', body: fs.readFileSync(path.join(root, 'vendor/livewire/livewire/dist/livewire.js'), 'utf8') });
            if (url.pathname === '/update') {
                const body = route.request().postDataJSON();
                return route.fulfill({ json: { components: body.components.map(c => ({ snapshot: c.snapshot, effects: { html: fs.readFileSync(path.join(directory, response), 'utf8'), returns: [] } })), assets: [] } });
            }
            return route.fulfill({ contentType: 'text/html', body: `<html><body>${initial}<script src="/livewire.js" data-csrf="fixture" data-update-uri="/update"></script></body></html>` });
        });
        await page.goto('https://refresh.test/');
        await page.waitForFunction(() => window.Livewire?.first());
        await page.evaluate(() => {
            window.canvas = document.querySelector('.tw-graph-protocol-canvas');
            window.line = canvas.querySelector('.tw-graph-protocol-primitive-line');
            window.grid = canvas.querySelector('[data-tw-graph-grid]');
            grid.innerHTML = '<line x1="0" y1="0" x2="10" y2="10" />';
            window.events = { removed: 0, added: 0 };
            Livewire.hook('morph.removed', () => events.removed++);
            Livewire.hook('morph.adding', () => events.added++);
        });
        for (const name of ['same.html', 'changed.html', 'changed.html']) {
            response = name;
            const before = await page.evaluate(() => line.getAttribute('style'));
            await page.evaluate(async () => { events = { removed: 0, added: 0 }; await Livewire.first().$refresh(); });
            const result = await page.evaluate(() => ({
                sameLine: line === canvas.querySelector('.tw-graph-protocol-primitive-line'),
                sameGrid: grid === canvas.querySelector('[data-tw-graph-grid]'),
                gridChildren: grid.children.length, style: line.getAttribute('style'), ...events,
            }));
            assert.ok(result.sameLine && result.sameGrid, 'Refresh must retain authored primitives and the client grid');
            assert.equal(result.gridChildren, 1, 'Client-owned grid contents must survive');
            assert.equal(result.removed, 0, 'Unchanged sibling structure must not be torn down');
            assert.equal(result.added, 0, 'Refresh must not reinsert the graph');
            if (name === 'changed.html') assert.match(result.style, /8rem/, 'Authored changes still update geometry');
            else assert.equal(result.style, before);
        }
        assert.deepEqual(errors, []);
        console.log('PASS: native Livewire refresh retains grid/primitives and applies changed lengths');
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
