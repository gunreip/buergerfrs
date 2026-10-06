<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\MergeTraversal;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('reverses a merge junction without moving its geometry or duplicating its arrow', function () {
    $segments = [
        ['component' => 'path', 'segment' => ['id' => 'stem', 'direction' => 'bottom-top', 'anchorStart' => ['x' => 0, 'y' => 0], 'anchorEnd' => ['x' => 0, 'y' => 4], 'nodeStart' => false, 'nodeEnd' => true, 'nodeEndDot' => false, 'jointArrowEnd' => true, 'devCounterEnd' => 2]],
        ['component' => 'arc', 'segment' => ['id' => 'arc', 'startAnchor' => 'w', 'endAnchor' => 'n', 'anchorStart' => ['x' => 0, 'y' => 4], 'anchorEnd' => ['x' => 2, 'y' => 6], 'nodeStart' => false, 'nodeEnd' => true]],
    ];
    expect(MergeTraversal::orient($segments, 'bottom-top'))->toBe($segments);
    $reverse = MergeTraversal::orient($segments, 'top-bottom');
    expect($reverse[0]['segment']['anchorEnd'])->toBe($segments[1]['segment']['anchorStart']);
    expect($reverse[0]['segment']['startAnchor'])->toBe('n');
    expect($reverse[0]['segment']['endAnchor'])->toBe('w');
    expect($reverse[0]['segment']['jointArrowEnd'])->toBeTrue();
    expect($reverse[0]['segment']['jointArrowEndDirection'])->toBe('bottom');
    expect($reverse[0]['segment']['devCounterEnd'])->toBe(2);
    expect($reverse[1]['segment']['jointArrowStart'])->toBeFalse();
    expect($reverse[1]['segment']['nodeStart'])->toBeFalse();
    expect($reverse[1]['segment']['direction'])->toBe('top-bottom');
});

it('forwards public merge direction on either side and renders the extension arrow at arc end', function ($side) {
    $html = Blade::render('<x-translation-workbench::ui.tw-graph graph-id="test-merge-direction"><x-translation-workbench::ui.tw-graph.strang.merge-'.$side.' id="test.merge" direction="top-bottom" :extension-count="1" /></x-translation-workbench::ui.tw-graph>');
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
    $xpath = new DOMXPath($dom);
    $bounds = [];
    foreach ($xpath->query('//*[@data-tw-graph-bounds]') as $n) {
        $b = json_decode($n->getAttribute('data-tw-graph-bounds'), true);
        $bounds[$b['id']] = $b;
    }
    expect($bounds)->toHaveKey('test.merge.extension.1.paths.merge-extension.arc.end.joint-arrow');
    expect($bounds)->not->toHaveKey('test.merge.extension.1.paths.merge-extension.stem1.end.joint-arrow');
    expect($bounds)->not->toHaveKey('test.merge.extension.1.paths.merge-extension.stem1.start.joint-arrow');
})->with(['left', 'right']);

it('preserves labelled junctions and terminal decorations while reversing compressed stems', function () {
    $terminal = ['component'=>'start','segment'=>['id'=>'start','direction'=>'bottom-top','anchorStart'=>['x'=>0,'y'=>0],'anchorEnd'=>['x'=>0,'y'=>1],'startLabel'=>['text'=>['Example']]]];
    $stem = ['component'=>'stem-compressed','segment'=>['id'=>'stem','direction'=>'bottom-top','anchorStart'=>['x'=>0,'y'=>1],'anchorEnd'=>['x'=>0,'y'=>5],'nodeEnd'=>['right'=>'Note'],'nodeEndDot'=>true,'jointArrowEnd'=>false,'beforeLength'=>'1rem','afterLength'=>'2rem']];
    $arc = ['component'=>'arc','segment'=>['id'=>'arc','startAnchor'=>'w','endAnchor'=>'n','anchorStart'=>['x'=>0,'y'=>5],'anchorEnd'=>['x'=>2,'y'=>7],'nodeStart'=>false,'nodeEnd'=>true]];
    $r = MergeTraversal::orient([$terminal,$stem,$arc],'top-bottom');
    expect($r[2])->toBe($terminal);
    expect($r[1]['segment']['nodeStart'])->toBe(['right'=>'Note']);
    expect($r[1]['segment']['nodeStartDot'])->toBeTrue();
    expect($r[1]['segment']['beforeLength'])->toBe('2rem');
    expect($r[1]['segment']['afterLength'])->toBe('1rem');
    expect($r[0]['segment']['jointArrowEnd'] ?? false)->toBeFalse();
});
