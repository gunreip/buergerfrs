<?php

use Tests\TestCase;

uses(TestCase::class);

it('renders each public component reference with nested fields and the shared visual sections', function (string $component, string $nestedField) {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.props-and-connections.'.$component)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML('<meta charset="utf-8">'.$html);
    $xpath = new DOMXPath($dom);

    expect($dom->textContent)->toContain($component.' — Deep Reference', 'Public component props', 'Array props', 'Inheritance and calculated geometry', 'Connections', 'Validation and practical limits', $nestedField);
    expect($xpath->query('//*[@data-flux-accordion-item]')->length)->toBeGreaterThan(0);
    expect($xpath->query('//pre/code')->length)->toBeGreaterThan(0);
    expect($dom->textContent)->not->toContain('Individual Parts references will be added');
})->with([
    ['strang.flow-start', 'start-node-labels.left.connectorLength'],
    ['strang.flow-step', 'node-labels.end.right.align'],
    ['strang.flow-if', 'if-start.lineJumps[].radius'],
    ['strang.flow-if-else', 'if-end.returnLength'],
    ['strang.flow-if-ternary', 'if-end.stemLength'],
    ['strang.flow-if-elseif', 'elseifs[].conditionLabel.text'],
    ['strang.flow-if-elseif-multi', 'elseifs[].actionLabel.stemLineJumps[].over'],
    ['strang.trunk', 'stem-lengths[n].length'],
    ['strang.merge-left', 'extension-node-labels[n][n].left.align'],
    ['strang.merge-right', 'extension-stem-continuations[n][].labels.right.text'],
    ['strang.branch-left', 'branch-extension[n].step.stepLabel.text'],
    ['strang.branch-right', 'branch-extension[n].returnBridge[m].nodeLabels[n].left.text'],
    ['strang.branch-end', 'end-label.offset'],
    ['strang.rekey-source-left', 'compressed-stem-parts.gapLength'],
    ['strang.rekey-source-right', 'arc-radiuss.in'],
    ['strang.rekey-target-left', 'end-label.text'],
    ['strang.rekey-target-right', 'stem-continuation[].labels.left.connectorLength'],
    ['parts.start', 'node-image.source'],
    ['parts.end', 'end-label.badgeColor'],
    ['parts.sideways', 'bridge-label.lineJumps[].side'],
    ['parts.chain', 'parts[].nodeLabelLeft.align'],
    ['parts.fusion', 'inputs[].anchor.x'],
]);
