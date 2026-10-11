import test from 'node:test';
import assert from 'node:assert/strict';
import { canvasLayout, unionBounds, boundsDifference, boundsIssueDetails, gridTicks, canvasSummary, captionOffset, componentRegions, textLabelGroups, horizontalLabelAdjustment, verticalLabelAdjustment, lineLabelAdjustment, mergeExtensionStagger, mergeExtensionStemClearance, mergeBridgeReductions, coupledMergeCompaction, mergeExtensionCompactions } from '../../resources/js/helper/tw-graph-bounds.js';

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

test('group and strang frames use the same element rectangles without adding padding', () => {
    const parent = { token: 1, id: 'merge' };
    const first = { token: 2, id: 'merge.main', ancestors: [parent] };
    const second = { token: 3, id: 'merge.extension', ancestors: [parent] };
    const regions = componentRegions([
        { region: first, rects: [{ x: 0, y: 0, width: 5, height: 10 }] },
        { region: first, kind: 'text', rects: [{ x: -12, y: 2, width: 8, height: 30 }] },
        { region: second, rects: [{ x: -30, y: -40, width: 10, height: 20 }] },
    ]);
    const bounds = id => regions.find(r => r.id === id).bounds;
    assert.deepEqual(bounds('merge.main'), { minX: -12, maxX: 5, minY: 0, maxY: 32 });
    assert.deepEqual(bounds('merge.extension'), { minX: -30, maxX: -20, minY: -40, maxY: -20 });
    assert.deepEqual(bounds('merge'), { minX: -30, maxX: 5, minY: -40, maxY: 32 });
});


test('label groups follow side runs rather than component boundaries or label height', () => {
    const owner = { token: 1, id: 'trunk', component: 'strang.trunk' };
    const label = (id, side, y, height = 20, x = 0) => ({ id, kind: 'text', region: owner,
        textPlacement: { side, x, y }, rects: [{ x: side === 'left' ? x - 100 : x, y, width: 80, height }] });
    const records = [label('start', 'bottom', 0), label('created', 'left', 100),
        label('updated', 'left', 200), label('value-right', 'right', 200),
        label('value-left', 'left', 300, 60), label('root', 'left', 400), label('end', 'top', 500)];
    // Same column and side: a component boundary must not split the run.
    records[5].region = { token: 2, component: 'strang.other' };
    const groups = textLabelGroups(records);
    assert.deepEqual(groups.map(g => g.sides), ['center', 'left', 'left+right', 'left', 'center']);
    assert.deepEqual(groups.map(g => g.elements), [['start'], ['created'], ['updated', 'value-right'], ['value-left', 'root'], ['end']]);
    assert.deepEqual(groups[3].bounds, { minX: -100, maxX: -20, minY: 300, maxY: 420 });
    assert.deepEqual(textLabelGroups([...records].reverse()).map(g => g.elements),
        [['start'], ['created'], ['value-right', 'updated'], ['value-left', 'root'], ['end']]);
    assert.equal(textLabelGroups([...records, label('parallel', 'left', 300, 20, 500)]).length, 6);
    assert.equal(textLabelGroups([...records, { ...label('hidden', 'right', 300), visible: false }]).length, 5);
    assert.equal(textLabelGroups([...records, { ...label('line', 'right', 300), kind: 'geometry' }]).length, 5);
});


test('horizontal placement extends the current lane only and leaves later lanes for another measurement', () => {
    const group = (id, x, minX, maxX, minY = 0) => ({ elements: [id + '.label'], anchorX: x,
        bounds: { minX, maxX, minY, maxY: minY + 20 } });
    const targets = [{ key: 'first', prefixes: ['first'], side: 'left' }, { key: 'next', prefixes: ['next'], side: 'left' }];
    const groups = [group('trunk', 0, -30, 30), group('first', -40, -60, -20), group('next', -60, -80, -40)];
    assert.deepEqual(horizontalLabelAdjustment(groups, targets, 10), { key: 'first', delta: 20 });
    groups[1] = group('first', -60, -80, -40);
    groups[2] = group('next', -100, -120, -80);
    assert.deepEqual(horizontalLabelAdjustment(groups, targets, 10), { key: 'next', delta: 10 });
    groups[2] = group('next', -110, -130, -90);
    assert.equal(horizontalLabelAdjustment(groups, targets, 10), null);
    assert.equal(horizontalLabelAdjustment([groups[0], group('first', -40, -60, -20, 100)], targets, 10), null);
});


test('vertical label clearance uses the taller side and leaves sufficient defaults alone', () => {
    const label = (id, x, y, height) => ({id, kind: 'text', textPlacement: {x:0, y}, rects:[{x, y:y-height/2, width:30, height}]});
    const targets = [{key:'first', prefix:'trunk.path.1'}, {key:'second', prefix:'trunk.path.2'}];
    const records = [label('trunk.path.1.left', -40, 0, 40), label('trunk.path.1.right', 10, 0, 60),
        label('trunk.path.2.left', -40, 50, 20), label('trunk.path.2.right', 10, 50, 50)];
    assert.deepEqual(verticalLabelAdjustment(records, targets, 16), {key:'second', delta:21});
    for (const r of records.slice(2)) { r.textPlacement.y += 21; r.rects[0].y += 21; }
    assert.equal(verticalLabelAdjustment(records, targets, 16), null);
    assert.equal(verticalLabelAdjustment([records[0], {...records[2], visible:false}], targets, 16), null);
});


test('bridge clearance extends the separating trunk stem, not a shared attachment', () => {
    const targets = [{key:'start',prefix:'trunk.start'}, {key:'path1',prefix:'trunk.path.1'}, {key:'path2',prefix:'trunk.path.2'}];
    const label = {id:'trunk.path.1.label',kind:'text',textPlacement:{side:'left'},rects:[{x:-80,y:80,width:60,height:30}]};
    const line = {id:'merge.bridge',kind:'geometry',rects:[{x:-100,y:100,width:100,height:4}]};
    assert.deepEqual(lineLabelAdjustment([label,line],targets,[{prefix:'merge',row:3}],16),{key:'path2',delta:26});
    assert.deepEqual(lineLabelAdjustment([label,line],targets,[{prefix:'merge',row:1}],16),{key:'path1',delta:40});
    assert.equal(lineLabelAdjustment([label,line],targets,[{prefix:'merge',row:2}],16),null);
    assert.equal(lineLabelAdjustment([label,{...line,id:'merge.bridge.connector'}],targets,[{prefix:'merge',row:3}],16),null);
    assert.equal(lineLabelAdjustment([label,{...line,rects:[{x:20,y:100,width:100,height:4}]}],targets,[{prefix:'merge',row:3}],16),null);
    assert.deepEqual(lineLabelAdjustment([label,{...line,id:'merge.arc.out'}],targets,[{prefix:'merge',row:3}],16),{key:'path2',delta:26});
    assert.equal(lineLabelAdjustment([label,{...line,rects:[{x:-100,y:126,width:100,height:4}]}],targets,[{prefix:'merge',row:3}],16),null);
});

test('vertical clearance separates side lanes and recognizes the promoted step endpoint', () => {
    const label = (id,y) => ({id,kind:'text',textPlacement:{x:0,y},rects:[{x:0,y:y-15,width:50,height:30}]});
    const targets = [{key:'fixed',prefix:'branch.step.label',lane:'branch',fixed:true},
        {key:'afterLength',prefix:'branch.step.stem.after',lane:'branch'},
        {key:'rekeyStem',prefix:'rekey.stem.1',lane:'rekey'}];
        assert.deepEqual(verticalLabelAdjustment([label('branch.step.label',0),label('branch.step.stem.after.label',35),label('rekey.stem.1.label',35)],targets,16),{key:'afterLength',delta:11});
        assert.equal(verticalLabelAdjustment([label('branch.step.label',0),label('branch.step.stem.after.label',46),label('rekey.stem.1.label',35)],targets,16),null);
});


test('different strangs reserve vertical clearance without stretching their internal label rows', () => {
    const groups = [
        {elements:['trunk.label'],anchorX:0,bounds:{minX:-80,maxX:80,minY:0,maxY:40}},
        {elements:['branch.label'],anchorX:-100,bounds:{minX:-160,maxX:-60,minY:56,maxY:96}},
    ];
    const targets = [{key:'branch.bridge_length',prefixes:['branch'],side:'left'}];
    assert.equal(horizontalLabelAdjustment(groups,targets,48),null);
    assert.deepEqual(horizontalLabelAdjustment(groups,targets,48,48),{key:'branch.bridge_length',delta:68});
    groups[1].bounds.minY = 88;
    groups[1].bounds.maxY = 128;
    assert.equal(horizontalLabelAdjustment(groups,targets,48,48),null);
});


test('a bridge clears another branch terminal label by separating their trunk attachments on both sides', () => {
    const targets = [{key:'start',prefix:'trunk.start'}, {key:'path1',prefix:'trunk.path.1'}, {key:'path2',prefix:'trunk.path.2'}];
    for (const side of [-1,1]) {
        const label = {id:'branch-end.3.label.top.1',kind:'text',textPlacement:{side:'top'},rects:[{x:side<0?-80:20,y:80,width:60,height:30}]};
        const bridge = {id:'branch.1.bridge.1',kind:'geometry',rects:[{x:side<0?-100:0,y:100,width:100,height:4}]};
        const attachments = [{prefix:'branch.1',row:3},{prefix:'branch.3',row:2},{prefix:'branch-end.3',row:2}];
        assert.deepEqual(lineLabelAdjustment([label,bridge],targets,attachments,16),{key:'path2',delta:26});
        bridge.rects[0].y += 26;
        assert.equal(lineLabelAdjustment([label,bridge],targets,attachments,16),null);
        bridge.rects[0].y -= 26;
        assert.equal(lineLabelAdjustment([label,bridge],targets,attachments.map(a=>({...a,row:2})),16),null);
    }
});

test('foreign branch bridges are cleared by moving the label branch outward before extending the trunk', () => {
    const targets = [{key:'start',prefix:'trunk.start'}, {key:'path1',prefix:'trunk.path.1'}, {key:'path2',prefix:'trunk.path.2'}];
    for (const side of ['left','right']) {
        const label = {id:'branch-end.3.label.top.1',kind:'text',textPlacement:{side:'top'},rects:[{x:side==='left'?-80:20,y:80,width:60,height:30}]};
        const bridge = {id:'branch.1.bridge.1',kind:'geometry',rects:[{x:side==='left'?-100:0,y:100,width:100,height:4}]};
        const attachments = [{prefix:'branch.1',row:3},{prefix:'branch-end.3',row:2}];
        const horizontal = [
            {key:'branches.0.bridge_length',side,prefixes:['branch.1']},
            {key:'branches.2.bridge_length',side,prefixes:['branch.3','branch-end.3'],terminals:['branch-end.3.path.branch-end']},
        ];
        assert.deepEqual(lineLabelAdjustment([label,bridge],targets,attachments,16,horizontal),{key:'branches.2.bridge_length',delta:96});
        label.rects[0].x += side==='left'?-96:96;
        assert.equal(lineLabelAdjustment([label,bridge],targets,attachments,16,horizontal),null);
    }
});

test('branch terminal stem and cap clear a foreign bridge even when the end label is above it', () => {
    const targets = [{key:'start',prefix:'trunk.start'}, {key:'path1',prefix:'trunk.path.1'}, {key:'path2',prefix:'trunk.path.2'}];
    for (const side of ['left','right']) for (const capOnly of [false,true]) {
        const x = side === 'left' ? -50 : 50;
        const end = {id:'branch-end.3.path.branch-end',kind:'geometry',rects:[
            {x:x-2,y:80,width:4,height:40}, {x:x-14,y:118,width:28,height:4},
        ]};
        const label = {id:end.id+'.label.top.1',kind:'text',textPlacement:{side:'top'},rects:[{x:x-30,y:150,width:60,height:30}]};
        const bridge = {id:'branch.1.bridge.1',kind:'geometry',rects:[
            {x:side==='left'?-100:(capOnly?55:0),y:capOnly?119:100,width:capOnly?45:100,height:4},
        ]};
        const attachments = [{prefix:'branch.1',row:3},{prefix:'branch-end.3',row:2}];
        const horizontal = [{key:'branches.0.bridge_length',side,prefixes:['branch.1']},
            {key:'branches.2.bridge_length',side,prefixes:['branch.3','branch-end.3'],terminals:['branch-end.3.path.branch-end']}];
        assert.deepEqual(lineLabelAdjustment([end,label,bridge],targets,attachments,16,horizontal),{key:'branches.2.bridge_length',delta:96});
        for (const r of [...end.rects,...label.rects]) r.x += side==='left'?-96:96;
        assert.equal(lineLabelAdjustment([end,label,bridge],targets,attachments,16,horizontal),null);
    }
});

test('registered terminals use their owning bridge across component families and both sides', () => {
    for (const family of ['rekeys','merges','branches']) for (const side of ['left','right']) {
        const terminal = {id:'owned.terminal',kind:'geometry',rects:[{x:side==='left'?-52:48,y:80,width:4,height:40}]};
        const bridge = {id:'foreign.bridge',kind:'geometry',rects:[{x:side==='left'?-100:0,y:100,width:100,height:4}]};
        const targets = [{key:'start',prefix:'trunk.start'},{key:'stem1',prefix:'trunk.1'},{key:'stem2',prefix:'trunk.2'}];
        const attachments = [{prefix:'owned',row:2},{prefix:'foreign',row:3}];
        const owners = [{key:family+'.0.bridge_length',side,prefixes:['owned'],terminals:['owned.terminal']},
            {key:'branches.1.bridge_length',side,prefixes:['foreign']}];
        assert.deepEqual(lineLabelAdjustment([terminal,bridge],targets,attachments,16,owners),{key:family+'.0.bridge_length',delta:68});
        assert.equal(lineLabelAdjustment([{...terminal,visible:false},bridge],targets,attachments,16,owners),null);
        assert.equal(lineLabelAdjustment([terminal,{...bridge,id:'owned.bridge'}],targets,attachments,16,owners),null);
    }
});


test('merge staggering lowers only the first extension label block with measured clearance', () => {
    for (const side of [-1, 1]) {
        const label = (id,y) => ({id,kind:'text',rects:[{x:side*100,y,width:50,height:20}]});
        const target = {prefixes:['merge.extension.1'],parentPrefix:'merge',compactStem:'merges.0.extension_stem_continuations.1.1'};
        const records = [label('merge.start.label',100),label('merge.stem1.label',140),
            label('merge.extension.1.start.label',100),label('merge.extension.1.stem1.label',140),
            label('merge.extension.2.stem1.label',140)];
        assert.deepEqual(mergeExtensionStagger(records,[target],48),{key:target.compactStem,delta:108});
        for(const r of records.filter(r=>r.id.startsWith('merge.extension.1.'))) r.rects[0].y-=108;
        assert.equal(mergeExtensionStagger(records,[target],48),null);
        assert.equal(mergeExtensionStagger(records,[{...target,compactStem:null}],48),null);
        assert.equal(records[4].rects[0].y,140);
    }
});


test('merge extension stem clearance moves the extension instead of its parent on both sides', () => {
    for (const side of ['left','right']) {
        const stem = {id:'merge.extension.1.paths.merge-extension.stem2',kind:'geometry',rects:[{x:side==='left'?-52:48,y:0,width:4,height:100}]};
        const label = {id:'merge.stem1.label',kind:'text',rects:[{x:side==='left'?-100:20,y:40,width:80,height:20}]};
        const target = {key:'merges.0.extension_bridge_continuations.1',prefixes:['merge.extension.1'],parentPrefix:'merge',side};
        assert.deepEqual(mergeExtensionStemClearance([stem,label],[target],16),{key:target.key,delta:68});
        assert.equal(mergeExtensionStemClearance([stem,{...label,id:'merge.extension.1.label'}],[target],16),null);
        assert.equal(mergeExtensionStemClearance([stem,{...label,id:'merge.extension.2.label'}],[target],16),null);
        stem.rects[0].x += side==='left'?-68:68;
        assert.equal(mergeExtensionStemClearance([stem,label],[target],16),null);
    }
});


test('merge bridge compaction includes extensions and leaves room for foreign labels and stems', () => {
    for(const side of ['left','right']) {
        const sign=side==='left'?-1:1;
        const text=(id,x)=>({id,kind:'text',region:{token:id,component:'strang.merge'},textPlacement:{x,y:0,side},rects:[{x,y:0,width:20,height:20}]});
        const target={key:'merges.0.bridge_length',side,owner:'merge',prefixes:['merge'],element:'merge.paths.merge.bridge'};
        const moving=text('merge.extension.1.label',sign<0?-100:80), fixed=text('fixed.label',-10);
        const extra={'merges.0.bridge_length':10};
        assert.deepEqual(mergeBridgeReductions([moving,fixed],[target],extra,16,16,16,4),{'merges.0.bridge_length':3.3593});
        const stem={id:'foreign.stem',kind:'geometry',rects:[{x:sign<0?-60:58,y:0,width:2,height:20}]};
        assert.deepEqual(mergeBridgeReductions([moving,fixed,stem],[target],extra,16,16,16,4),{'merges.0.bridge_length':.9843});
        assert.deepEqual(mergeBridgeReductions([moving,fixed],[target],{},16,16,16,4),{});
        const ownStem={...stem,id:'merge.extension.2.stem'};
        assert.deepEqual(mergeBridgeReductions([moving,fixed,ownStem],[target],extra,16,16,16,4),{'merges.0.bridge_length':2.7343});
    }
});




test('coupled merge compensation trades bounded height for width symmetrically and rejects new obstacles', () => {
    for (const side of ['left','right']) {
        const x = side === 'left' ? -240 : 140;
        const label = (id,x,y) => ({id,kind:'text',region:{token:id,component:'strang.merge'},textPlacement:{x,y,side},rects:[{x,y,width:100,height:20}]});
        const target = {key:'merges.0.bridge_length',side,owner:'merge',prefixes:['merge'],element:'merge.paths.merge.bridge',coupledStem:{key:'merges.0.stem_continuation.1',element:'merge.paths.merge.stem2'}};
        const records = [label('merge.paths.merge.start.label',x,0),label('trunk.label',side==='left'?-92:-8,40),
            {id:target.coupledStem.element,kind:'geometry',rects:[{x:side==='left'?-280:278,y:40,width:2,height:40}]}];
        const calc = r => coupledMergeCompaction(r,[target],{[target.key]:10},16,48,48,16,3,8);
        assert.deepEqual(calc(records),{bridge:target.key,reduction:7.9843,stem:target.coupledStem.key,addition:3,extensions:[]});
        const extension = {key:'merges.0.extension_bridge_continuations.1',prefixes:['merge.extension.1'],parentPrefix:'merge',side,
            compactStem:'merges.0.extension_stem_continuations.1.1',compactStemElement:'merge.extension.1.paths.merge-extension.stem2'};
        const extRecords = [...records, label('merge.extension.1.start.label',side==='left'?-600:500,-68),
            {id:extension.compactStemElement,kind:'geometry',rects:[{x:side==='left'?-620:620,y:-48,width:2,height:60}]}];
        const extended = coupledMergeCompaction(extRecords,[target,extension],{[target.key]:10},16,48,48,16,3,8);
        assert.deepEqual(extended?.extensions,[{stem:extension.compactStem,addition:3}]);
        // Horizontal 3rem stem clearance must coexist with the former vertical rule and compaction.
        const both = coupledMergeCompaction(extRecords,[target,extension],{[target.key]:10},16,48,48,16,3,8,() => true,48);
        assert.equal(both?.addition,3);
        assert.equal(both?.reduction,7.9843);
        assert.equal(mergeExtensionStemClearance(extRecords,[target,extension],48,16),null);
        const obstruction = {id:'foreign.bridge',kind:'geometry',rects:[{x:side==='left'?-110:0,y:-40,width:110,height:2}]};
        assert.equal(calc([...records,obstruction]),null);
        assert.equal(coupledMergeCompaction(records,[target],{},16,48,48,16,3,8),null);
        assert.equal(coupledMergeCompaction(records,[target],{[target.key]:10},16,48,48,16,3,8,() => false),null);
        assert.equal(coupledMergeCompaction(records,[target],{[target.key]:10},16,48,48,16,0,8),null);
    }
});

test('merge extension labels keep three rem on either side of a foreign stem', () => {
    for (const side of ['left','right']) {
        const mirror = side === 'left' ? 1 : -1;
        const record = (id,kind,x,width) => ({id,kind,rects:[{x:mirror>0?x:-x-width,y:0,width,height:40}]});
        const stem = record('merge.extension.1.paths.merge-extension.stem2','geometry',0,2);
        const earlier = record('merge.start.label','text',18,20); // 1rem gap.
        const later = record('merge.extension.2.label','text',-52,20); // 2rem gap.
        const targets = [1,2].map(i => ({key:`merges.0.extension_bridge_continuations.${i}`,prefixes:[`merge.extension.${i}`],parentPrefix:'merge',side}));
        assert.deepEqual(mergeExtensionStemClearance([stem,earlier],targets,48),{key:targets[0].key,delta:32});
        assert.deepEqual(mergeExtensionStemClearance([stem,later],targets,48),{key:targets[1].key,delta:16});
        later.rects[0].x -= mirror*16;
        assert.equal(mergeExtensionStemClearance([stem,later],targets,48),null);
    }
});

test('extension compaction lowers only the enabled lane and preserves earlier layout on either side', () => {
    for (const side of ['left','right']) {
        const mirror = side==='left'?1:-1;
        const text=(id,x,y,width)=>({id,kind:'text',region:{token:id,component:'strang.merge'},textPlacement:{x:mirror*x,y,side},rects:[{x:mirror>0?x:-x-width,y,width,height:20}]});
        const target={key:'merges.0.extension_bridge_continuations.3',parentPrefix:'merge',side,prefixes:['merge.extension.3'],element:'merge.extension.3.bridge',extensionCompactionStem:{key:'merges.0.extension_stem_continuations.3.1',element:'merge.extension.3.stem2'}};
        const records=[text('merge.extension.2.label',-200,0,100),text('merge.extension.3.label',-348,0,100),
            {id:'merge.extension.3.stem2',kind:'geometry',rects:[{x:mirror>0?-400:398,y:20,width:2,height:80}]}];
        const original=JSON.stringify(records);
        const result=mergeExtensionCompactions(records,[target],{[target.key]:8},16,48,48,16,48);
        assert.deepEqual(result,[{bridge:target.key,reduction:7.9843,stem:target.extensionCompactionStem.key,addition:4.2657}]);
        assert.equal(JSON.stringify(records),original);
        assert.deepEqual(mergeExtensionCompactions(records,[target],{[target.key]:8},16,48,48,16,48,()=>false),[]);
        assert.deepEqual(mergeExtensionCompactions(records,[{...target,extensionCompactionStem:null}],{[target.key]:8},16,48,48,16,48),[]);
    }
});

test('extension compaction can keep its height when lowering is blocked', () => {
    for (const side of ['left', 'right']) {
        const mirror = side === 'left' ? 1 : -1;
        const text = (id, x) => ({ id, kind: 'text', region: { token: id, component: 'strang.merge' },
            textPlacement: { x: mirror * x, y: 0, side },
            rects: [{ x: mirror > 0 ? x : -x - 100, y: 0, width: 100, height: 20 }] });
        const target = { key: 'merges.0.extension_bridge_continuations.4', parentPrefix: 'merge', side,
            prefixes: ['merge.extension.4'], element: 'merge.extension.4.bridge',
            extensionCompactionStem: { key: 'merges.0.extension_stem_continuations.4.1', element: 'merge.extension.4.stem2' } };
        const records = [text('merge.extension.3.label', -200), text('merge.extension.4.label', -448),
            { id: target.extensionCompactionStem.element, kind: 'geometry',
                rects: [{ x: mirror > 0 ? -500 : 498, y: 20, width: 2, height: 80 }] }];
        const result = mergeExtensionCompactions(records, [target], { [target.key]: 8 }, 16, 48, 48, 16, 48,
            candidate => candidate.find(r => r.id === 'merge.extension.4.label').rects[0].y === 0);
        assert.deepEqual(result, [{ bridge: target.key, reduction: 6.2343, stem: target.extensionCompactionStem.key, addition: 0 }]);
    }
});

test('foreign lanes sharing a trunk attachment still get bridge label clearance', () => {
    const records = [
        { id: 'a.label', kind: 'text', textPlacement: { x: -20, y: 0, side: 'left' },
            rects: [{ x: -20, y: 0, width: 10, height: 20 }] },
        { id: 'b.bridge.1', kind: 'geometry', rects: [{ x: -30, y: 8, width: 30, height: 2 }] },
    ];
    const attachments = [{ prefix: 'a', row: 1 }, { prefix: 'b', row: 1 }];
    const targets = [{ key: 'merges.0.bridge_length', prefixes: ['a'], side: 'left' },
        { key: 'merges.1.bridge_length', prefixes: ['b'], side: 'left' }];
    assert.deepEqual(lineLabelAdjustment(records, [], attachments, 16, targets),
        { key: 'merges.0.bridge_length', delta: 36 });
    assert.equal(lineLabelAdjustment(records, [], attachments, 16, [{ ...targets[0], prefixes: ['a', 'b'] }]), null);
});

test('a bridge near its trunk attachment uses the branch entry stem, not its bridge or trunk', () => {
    const records = [
        { id: 'trunk.path.1.label.left.1', kind: 'text', textPlacement: { x: 0, y: 0, side: 'left' },
            rects: [{ x: -100, y: -30.75, width: 60, height: 61.5 }] },
        { id: 'branch.bridge.1', kind: 'geometry', rects: [{ x: -150, y: 42, width: 106, height: 4 }] },
    ];
    const targets = [{ prefix: 'trunk.start', key: 'trunk.start_length' },
        { prefix: 'trunk.path.1', key: 'trunk.path_lengths.1' }];
    const paths = [{ key: 'branches.0.bridge_length', prefixes: ['branch'], side: 'left', entryStem: 'branches.0.entry_stem_length' }];
    assert.deepEqual(lineLabelAdjustment(records, targets, [{ prefix: 'branch', row: 2 }], 16, paths),
        { key: 'branches.0.entry_stem_length', delta: 4.75 });
});
