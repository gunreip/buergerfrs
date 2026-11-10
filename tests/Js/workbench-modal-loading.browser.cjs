// Isolated Flux modal layout check; no application database connection.
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const fs = require('fs');
const assert = require('assert/strict');
const { execFileSync } = require('child_process');
const root = require('path').resolve(__dirname, '../..');
const markup = execFileSync('php', ['-r', `require 'vendor/autoload.php'; $app=require 'bootstrap/app.php'; $app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap(); echo Illuminate\\Support\\Facades\\Blade::render('<flux:modal name="loading-test" class="w-full max-w-xl"><x-translation-workbench::ui.common.modal-loading target="saveDraft, selectedSubLanguages" /><div id="content" class="h-64"><flux:heading>Editor</flux:heading><flux:button id="save">Save</flux:button></div></flux:modal>');`], { cwd: root, encoding: 'utf8' });
(async () => {
    const browser = await chromium.launch({ headless: true });
    try {
        const page = await browser.newPage();
        const manifest = JSON.parse(fs.readFileSync(root + '/public/build/manifest.json'));
        await page.route('https://loading.test/**', route => {
            const name = new URL(route.request().url()).pathname;
            const files = { '/app.css': 'public/build/' + manifest['resources/css/app.css'].file, '/flux.js': 'vendor/livewire/flux-pro/dist/flux.js', '/livewire.js': 'vendor/livewire/livewire/dist/livewire.js' };
            return route.fulfill(files[name] ? { contentType: name.endsWith('.css') ? 'text/css' : 'text/javascript', body: fs.readFileSync(root + '/' + files[name], 'utf8') } : { contentType: 'text/html', body: `<html><head><link rel="stylesheet" href="/app.css"></head><body>${markup}<script src="/flux.js"></script><script src="/livewire.js" data-csrf="test" data-update-uri="/update"></script></body></html>` });
        });
        await page.goto('https://loading.test/');
        await page.locator('dialog').evaluate(el => el.showModal());
        await page.waitForTimeout(400); // Let Flux finish its modal entrance transition.
        const before = await page.locator('#content').boundingBox();
        // Apply precisely the classes toggled by wire:loading; avoid any server write.
        await page.locator('[data-workbench-modal-loading]').evaluate(el => { el.classList.remove('hidden'); el.classList.add('block'); });
        const result = await page.locator('[data-workbench-modal-loading]').evaluate(el => {
            const overlay = el.getBoundingClientRect();
            const content = document.querySelector('#content').getBoundingClientRect();
            const top = document.elementFromPoint(content.x + content.width / 2, content.y + content.height / 2);
            return { inside: !!el.closest('dialog:modal'), covers: overlay.width >= content.width && overlay.height >= content.height, onTop: el.contains(top), targets: el.getAttribute('wire:target') };
        });
        assert.ok(result.inside && result.covers && result.onTop, JSON.stringify(result));
        assert.equal(result.targets, 'saveDraft, selectedSubLanguages');
        assert.deepEqual(await page.locator('#content').boundingBox(), before, 'Loading must not shift content');
        console.log('PASS: modal overlay covers content in the top layer without layout shift; explicit targets preserved');
    } finally { await browser.close(); }
})().catch(error => { console.error(error.message); process.exit(1); });
