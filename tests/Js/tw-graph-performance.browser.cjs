// PLAYWRIGHT_MODULE selects the existing local browser test runtime.
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
(async () => {
 const browser = await chromium.launch({channel:'chrome',headless:true});
 try {
  const page = await browser.newPage();
  await page.setContent(['a','b'].map(id=>`<section wire:id="${id}"><div data-tw-graph-performance data-failed="Failed" data-running="Running">${['begin','end','total','headers','morph','bounds','passes','body','prepare','after','morphs','visited','added','removed'].map(k=>`<span data-tw-graph-time="${k}">—</span>`).join('')}</div><button data-tw-graph-refresh>Refresh</button><div data-test-graph></div></section>`).join(''));
  await page.evaluate(()=>{
   window.hooks={};window.Livewire={hook:(name,fn)=>hooks[name]=fn};
   window.frames=0;const raf=requestAnimationFrame;window.requestAnimationFrame=fn=>{frames++;return raf(fn);};
  });
  for(const name of ['tw-graph-performance','tw-graph-bounds','tw-graph-line-jumps']) {
   const setup={'tw-graph-performance':'setupTwGraphPerformance','tw-graph-bounds':'setupTwGraphBounds','tw-graph-line-jumps':'setupTwGraphLineJumps'}[name];
   const source=fs.readFileSync(path.join(__dirname,'../../resources/js/helper',name+'.js'),'utf8').replace(/export /g,'');
   await page.addScriptTag({content:`(()=>{${source}\n${setup}();})();`});
  }
  await page.waitForTimeout(200);
  const result = await page.evaluate(async()=>{
   const read=id=>Object.fromEntries([...document.querySelector(`[wire\\:id="${id}"]`).querySelectorAll('[data-tw-graph-time]')].map(el=>[el.dataset.twGraphTime,el.textContent]));
   const callbacks={};
   document.querySelector('[data-tw-graph-refresh]').click();
   const clicked=read('a').begin;
   await new Promise(r=>setTimeout(r,60));
   hooks.request({payload:JSON.stringify({components:[{snapshot:JSON.stringify({memo:{id:'a'}})}]}),respond:fn=>callbacks.respond=fn,succeed:fn=>callbacks.decoded=fn,fail:fn=>callbacks.fail=fn});
   hooks.commit({component:{id:'a'},succeed:fn=>callbacks.done=fn,fail:fn=>callbacks.commitFail=fn});
   callbacks.respond();
   await new Promise(r=>setTimeout(r,30));callbacks.decoded();
   await new Promise(r=>setTimeout(r,25));
   hooks.morph({component:{id:'a'}});
   hooks['morph.updating']({component:{id:'a'}});hooks['morph.updating']({component:{id:'a'}});
   hooks['morph.updating']({component:{id:'b'}});
   hooks['morph.adding']({component:{id:'a'}});hooks['morph.removed']({component:{id:'a'}});
   hooks.morphed({component:{id:'a'}});
   hooks.morph({component:{id:'a'}});hooks.morphed({component:{id:'a'}});
   await new Promise(r=>setTimeout(r,100));
   const before=frames;
   const graph=document.querySelector('[wire\\:id="a"] [data-test-graph]');
   graph.dispatchEvent(new CustomEvent('tw-graph-bounds-timed',{bubbles:true,detail:{duration:125}}));
   graph.dispatchEvent(new CustomEvent('tw-graph-bounds-timed',{bubbles:true,detail:{duration:75}}));
   await new Promise(r=>setTimeout(r,150));
   const success=read('a'),other=read('b'),extraFrames=frames-before;
   callbacks.done();
   await new Promise(r=>setTimeout(r,120));
   graph.dispatchEvent(new CustomEvent('tw-graph-bounds-timed',{bubbles:true,detail:{duration:25}}));
   await new Promise(r=>setTimeout(r,130));
   const stillRunning=read('a').end;
   for (let retry=0;retry<40 && read('a').end==='Running';retry++) await new Promise(r=>setTimeout(r,100));
   const finished=read('a');
   graph.dispatchEvent(new CustomEvent('tw-graph-bounds-timed',{bubbles:true,detail:{duration:999}}));
   const frozen=read('a');
   hooks.request({payload:{components:[{snapshot:JSON.stringify({memo:{id:'a'}})}]},respond:()=>{},fail:fn=>callbacks.fail=fn});
   callbacks.fail();const failure=read('a');
   hooks.request({payload:{components:[{snapshot:JSON.stringify({memo:{id:'a'}})}]},respond:()=>{},fail:()=>{}});
   return {success,other,extraFrames,failure,clicked,stillRunning,finished,frozen,reset:read('a')};
  });
  assert.equal(result.finished.morphs,'2');assert.equal(result.finished.visited,'2');
  assert.equal(result.finished.added,'1');assert.equal(result.finished.removed,'1');
  assert.ok(parseFloat(result.finished.body)>=0.025);assert.ok(parseFloat(result.finished.prepare)>=0.02);
  assert.ok(parseFloat(result.finished.after)>=0.2);
  assert.equal(result.reset.morphs,'0');assert.equal(result.reset.visited,'0');assert.equal(result.reset.body,'—');
  assert.equal(result.success.bounds,'0.200 s');assert.equal(result.success.passes,'2');
  assert.match(result.success.headers,/^\d+\.\d{3} s$/);assert.match(result.success.morph,/^\d+\.\d{3} s$/);
  assert.equal(result.other.bounds,'—');assert.equal(result.extraFrames,0);
  assert.equal(result.clicked,result.finished.begin);
  assert.equal(result.stillRunning,'Running');
  assert.match(result.finished.end,/\d{2}:\d{2}:\d{2}/);
  assert.ok(parseFloat(result.finished.total)>=0.5);
  assert.equal(result.finished.passes,'3');assert.equal(result.finished.bounds,'0.225 s');
  assert.deepEqual(result.frozen,result.finished);
  assert.notEqual(result.failure.end,'Running');
  assert.equal(result.failure.headers,'Failed');assert.equal(result.reset.passes,'0');assert.equal(result.reset.headers,'—');
  console.log(JSON.stringify({passed:true,...result}));
 } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
