// Runs Flux/Livewire selection against an isolated fixture; no application server or database.
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const fs = require('fs');
const assert = require('assert/strict');
const path = require('path');
const root = path.resolve(__dirname, '../..');
(async () => {
 const browser = await chromium.launch({headless:true});
 try {
  for (const selectionField of ['sharedSelection', 'selectedReviewRevisions', 'reviewLocales', 'publicationLocales']) {
  const page = await browser.newPage();
  const errors=[];page.on('pageerror',e=>errors.push(e.message));
  const snapshot=JSON.stringify({data:{[selectionField]:[['99','12'],{s:'arr'}]},memo:{id:'shared-test',name:'shared-test',path:'/',method:'GET',children:[],scripts:[],assets:[],errors:[],locale:'en'},checksum:'fixture'}).replaceAll('&','&amp;').replaceAll('"','&quot;');
  await page.route('https://checkbox.test/**', async route=>{
   const pathname=new URL(route.request().url()).pathname;
   if(pathname==='/flux.js')return route.fulfill({contentType:'application/javascript',body:fs.readFileSync(path.join(root, 'vendor/livewire/flux-pro/dist/flux.js'),'utf8')});
   if(pathname==='/livewire.js')return route.fulfill({contentType:'application/javascript',body:fs.readFileSync(path.join(root, 'vendor/livewire/livewire/dist/livewire.js'),'utf8')});
   if(pathname==='/update') {
    const req=route.request().postDataJSON();
    return route.fulfill({json:{components:req.components.map(c=>{const s=JSON.parse(c.snapshot);if(c.updates[selectionField])s.data[selectionField]=[c.updates[selectionField],{s:'arr'}];return {snapshot:JSON.stringify(s),effects:{}}}),assets:[]}});
   }
   return route.fulfill({contentType:'text/html',body:`<html><body><div wire:id="shared-test" wire:snapshot="${snapshot}" wire:effects="{}"><ui-checkbox-group><table><thead><tr><th><ui-checkbox all id="all"></ui-checkbox></th></tr></thead><tbody>${['11','12','13','14'].map(id=>`<tr><td><ui-checkbox value="${id}" wire:model.live="${selectionField}" ${['11','14'].includes(id)?'disabled':''}></ui-checkbox></td></tr>`).join('')}</tbody></table></ui-checkbox-group></div><script src="/flux.js"></script><script src="/livewire.js" data-csrf="fixture" data-update-uri="/update"></script></body></html>`});
  });
  await page.goto('https://checkbox.test/');
  await page.waitForFunction(()=>window.Livewire?.first());
  const state=()=>page.evaluate(field=>Array.from(Livewire.first().get(field)).sort(), selectionField);
  assert.deepEqual(await state(),['12','99']);
  assert.equal(await page.locator('#all').evaluate(el=>el.indeterminate),true);
  await page.locator('#all').evaluate(el=>el.click());
  await page.waitForTimeout(400);
  assert.deepEqual(await state(),['12','13','99']);
  assert.equal(await page.locator('#all').getAttribute('aria-checked'),'true');
  assert.equal(await page.locator('ui-checkbox[value="11"]').getAttribute('aria-checked'),'false');
  await page.locator('#all').evaluate(el=>el.click());
  await page.waitForTimeout(400);
  assert.deepEqual(await state(),['99']);
  assert.equal(await page.locator('#all').getAttribute('aria-checked'),'false');
  assert.deepEqual(errors,[]);
  await page.close();
  console.log(selectionField + ': PASS: partial selection, select all, disabled exclusions, clear page, retained off-page selection with Flux and Livewire');
  }
 } finally {await browser.close();}
})().catch(e=>{console.error(e);process.exit(1)});
