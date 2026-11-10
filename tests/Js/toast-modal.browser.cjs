// Isolated browser regression: actual Flux markup and CSS, no database writes.
// Run after npm run build; set PLAYWRIGHT_MODULE if Playwright is installed elsewhere.
const {chromium}=require(process.env.PLAYWRIGHT_MODULE || 'playwright');const fs=require('fs');const assert=require('assert/strict');const root=require('path').resolve(__dirname, '../..');
const {execFileSync}=require('child_process');
const markup=execFileSync('php',['-r',`require 'vendor/autoload.php'; $app=require 'bootstrap/app.php'; $app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap(); echo Illuminate\\Support\\Facades\\Blade::render('<flux:modal name="editor-test"><input id="reason"><flux:modal.close><flux:button id="close">Close</flux:button></flux:modal.close></flux:modal><flux:toast.group position="top end" expanded><flux:toast /></flux:toast.group>');`],{cwd:root,encoding:'utf8'});
(async()=>{const browser=await chromium.launch({headless:true});try {const page=await browser.newPage();page.setDefaultTimeout(4000);const errors=[];page.on('pageerror',e=>errors.push(e.message));
const manifest=JSON.parse(fs.readFileSync(root+'/public/build/manifest.json'));const css=manifest['resources/css/app.css'].file;
await page.route('https://notice.test/**',r=>{const p=new URL(r.request().url()).pathname;const paths={'/flux.js':'vendor/livewire/flux-pro/dist/flux.js','/livewire.js':'vendor/livewire/livewire/dist/livewire.js','/core.js':'resources/js/notices/notice-core.js','/app.css':'public/build/'+css};if(paths[p])return r.fulfill({contentType:p.endsWith('.css')?'text/css':'text/javascript',body:fs.readFileSync(root+'/'+paths[p],'utf8')});return r.fulfill({contentType:'text/html',body:`<html><head><link rel="stylesheet" href="/app.css"></head><body>${markup}<script type="module" src="/core.js"></script><script src="/flux.js"></script><script src="/livewire.js" data-csrf="fixture" data-update-uri="/update"></script></body></html>`});});
await page.goto('https://notice.test/');await page.waitForFunction(()=>document.querySelector('ui-toast-group')?.showToast);
await page.evaluate(()=>{document.querySelector('dialog').showModal();window.addEventListener('test-focus',()=>document.querySelector('#reason').focus());document.dispatchEvent(new CustomEvent('toast-show',{detail:{duration:0,slots:{heading:'Reason required',text:'Select a reason.'},dataset:{noticeScope:'editor:1'},action:{label:'Go to field',event:'test-focus'}}}));});
// Simulate a modal DOM update detaching the relocated host while a toast is open.
await page.waitForTimeout(400);
const beforeDetach = await page.locator('[data-flux-toast-dialog]').boundingBox();
await page.locator('ui-toast-group').evaluate(el => el.remove());
await page.waitForFunction(()=>document.querySelector('ui-toast-group')?.matches(':popover-open'));
assert.equal(await page.locator('ui-toast-group').evaluate(el=>!!el.closest('dialog')),true);
const afterDetach = await page.locator('[data-flux-toast-dialog]').boundingBox();
assert.ok(Math.abs(beforeDetach.x-afterDetach.x)<2 && Math.abs(beforeDetach.y-afterDetach.y)<2, 'Toast must keep its viewport position after host restoration');
await page.locator('[data-flux-toast-action-button]').click();await page.waitForFunction(()=>document.activeElement.id==='reason');assert.equal(await page.locator('dialog').evaluate(el=>el.open),true);
await page.locator('#close').click();await page.waitForTimeout(200);assert.equal(await page.locator('ui-toast-group').evaluate(el=>!!el.closest('dialog')),false);assert.deepEqual(errors,[]);console.log('PASS: physical toast click focuses input, modal stays open, host restored on close');
}finally{await browser.close()}})().catch(e=>{console.error(e.message);process.exit(1)});
