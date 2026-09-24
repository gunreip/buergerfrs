// First: php tests/Js/render-tw-graph-bounds-fixtures.php /tmp/tw-graph-bounds-audit
// Then: node tests/Js/tw-graph-bounds.browser.cjs /tmp/tw-graph-bounds-audit
// PLAYWRIGHT_MODULE and CHROMIUM_PATH may point to an external browser runtime.
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const root = path.resolve(__dirname, '../..');
const directory = process.argv[2] || path.join(require('node:os').tmpdir(), 'tw-graph-bounds-audit');

(async () => {
    const browser = await chromium.launch(process.env.CHROMIUM_PATH
        ? { executablePath: process.env.CHROMIUM_PATH, headless: true } : { channel: 'chrome', headless: true });
    const manifest = JSON.parse(fs.readFileSync(path.join(root, 'public/build/manifest.json')));
    const css = Object.values(manifest).filter(x => x.isEntry && x.file.endsWith('.css'))
        .map(x => fs.readFileSync(path.join(root, 'public/build', x.file), 'utf8')).join('\n');
    const scripts = ['tw-graph-line-jumps', 'tw-graph-bounds'].map(name =>
        fs.readFileSync(path.join(root, 'resources/js/helper', name + '.js'), 'utf8').replace(/export /g, '') + '\n'
        + (name.endsWith('bounds') ? 'setupTwGraphBounds' : 'setupTwGraphLineJumps') + '();');
    const results = [];
    let regressionChecked = false;
    try {
        for (const item of JSON.parse(fs.readFileSync(path.join(directory, 'index.json')))) {
            if (item.error) { results.push(item); continue; }
            const page = await browser.newPage({ viewport: { width: 1600, height: 1200 } });
            const errors = [];
            page.on('pageerror', error => errors.push(error.message));
            try {
                await page.setContent(fs.readFileSync(path.join(directory, item.file), 'utf8'));
                await page.addStyleTag({ content: css });
                for (const content of scripts) await page.addScriptTag({ content });
                await page.evaluate(() => new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r))));
                const graphs = await page.evaluate(() => [...document.querySelectorAll('[data-tw-graph-bounds-model]')].map(graph => {
                    const result = resolveGraphBounds(graph);
                    return { id: graph.id, issues: result?.issues || [], count: result?.records.length || 0,
                        checked: !!graph.dataset.twGraphBoundsIssues };
                }));
                results.push({ name: item.name, graphs, errors });
                // A deliberately broken geometry must be reported, and must not redefine the canvas bounds.
                if (!regressionChecked && item.name.endsWith('flow-while-test')) {
                    const before = await page.evaluate(() => {
                        const graph = document.querySelector('[data-tw-graph-bounds-model]');
                        const result = resolveGraphBounds(graph);
                        window.originalBoundsLineStyle = graph.querySelector('.tw-graph-protocol-primitive-line').getAttribute('style');
                        graph.querySelector('.tw-graph-protocol-primitive-line').style.setProperty('--tw-graph-protocol-local-length', '100rem');
                        return result.layout;
                    });
                    await page.waitForTimeout(150);
                    const broken = await page.evaluate(() => {
                        const graph = document.querySelector('[data-tw-graph-bounds-model]');
                        const result = resolveGraphBounds(graph);
                        return { layout: result.layout, issues: result.issues,
                            hidden: graph.querySelector('[data-tw-graph-bounds-warning]')?.hidden };
                    });
                    assert.ok(broken.issues.length > 0, 'Geometry mismatch must be reported');
                    assert.equal(broken.hidden, false, 'DEV mismatch must be visible');
                    assert.ok(Math.abs(broken.layout.height - before.height) < .1, 'No hidden geometry correction');
                    await page.evaluate(() => {
                        const graph = document.querySelector('[data-tw-graph-bounds-model]');
                        graph.querySelector('.tw-graph-protocol-primitive-line').setAttribute('style', window.originalBoundsLineStyle);
                        graph.style.zoom = '.8';
                        graph.classList.add('tw-graph-protocol-dev-disabled');
                    });
                    await page.waitForTimeout(150);
                    const restored = await page.evaluate(() => resolveGraphBounds(document.querySelector('[data-tw-graph-bounds-model]')).issues);
                    assert.deepEqual(restored, [], 'Zoom and disabling DEV must not affect geometry correctness');
                    await page.evaluate(() => document.querySelector('[data-tw-graph-bounds-model]').style.display = 'none');
                    await page.waitForTimeout(50);
                    await page.evaluate(() => {
                        const graph = document.querySelector('[data-tw-graph-bounds-model]');
                        graph.style.display = '';
                        graph.querySelector('.tw-graph-protocol-primitive-text-label').style.width = '100rem';
                    });
                    await page.waitForTimeout(150);
                    const text = await page.evaluate(() => {
                        const result = resolveGraphBounds(document.querySelector('[data-tw-graph-bounds-model]'));
                        return { width: result.layout.width, issues: result.issues };
                    });
                    assert.ok(text.width > before.width, 'Only text dimensions may refine the declared model after layout');
                    assert.deepEqual(text.issues, [], 'Text refinement must retain all declared path geometry');
                    const invalid = await page.evaluate(() => {
                        const graph = document.querySelector('[data-tw-graph-bounds-model]');
                        const output = graph.querySelector('[data-tw-graph-bounds-records]');
                        const records = JSON.parse(output.textContent);
                        const width = graph.style.getPropertyValue('--tw-graph-protocol-calculated-width');
                        records[0].rects[0].x = 'var(--intentionally-missing-coordinate)';
                        output.textContent = JSON.stringify(records);
                        const result = resolveGraphBounds(graph);
                        return { width, layout: result.layout, issues: result.issues };
                    });
                    assert.equal(invalid.layout, null, 'Unknown coordinates must not produce partial bounds');
                    assert.ok(invalid.issues.some(issue => issue.includes('Unresolvable length')));
                    await page.waitForTimeout(100);
                    const retained = await page.evaluate(() => document.querySelector('[data-tw-graph-bounds-model]').style.getPropertyValue('--tw-graph-protocol-calculated-width'));
                    assert.equal(retained, invalid.width, 'An incomplete model must not replace the last complete calculation');
                    regressionChecked = true;
                }
                console.log(item.name, graphs.length, graphs.reduce((n, g) => n + g.issues.length, 0), errors.length);
            } catch (error) {
                results.push({ name: item.name, error: error.message });
            } finally { await page.close(); }
            fs.writeFileSync(path.join(directory, 'result.json'), JSON.stringify(results, null, 2));
        }
        assert.ok(regressionChecked, 'The intentional-mismatch regression ran');
        const failures = results.filter(r => r.error || r.errors?.length || r.graphs?.some(g => g.issues.length || !g.checked));
        assert.deepEqual(failures, [], 'No render errors, unchecked graphs or geometry mismatches');
        console.log('PASS: all saved examples and deliberate geometry mismatch regression');
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
