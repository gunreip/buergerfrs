<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\DevIdentifier;

it('keeps concise hand authored strang labels readable', function (): void {
    expect(DevIdentifier::label('literature.left.1.notes.paths.branch.bridge1'))
        ->toBe('strang.branch.left.1.bridge-1')
        ->and(DevIdentifier::label('migration.center.1.trunk.paths.trunk.start.label.left.1'))
        ->toBe('strang.trunk.center.1.start.label.left.1')
        ->and(DevIdentifier::label('migration.right.1.ended-finding.paths.branch.step.label.right.1.text'))
        ->toBe('strang.branch.right.1.step-1.label.right.1');
});

it('normalizes legacy data driven strang ids into compact dev labels', function (): void {
    expect(DevIdentifier::label('strang.branch-left.7.main.path.branch.bridge1'))
        ->toBe('strang.branch.left.7.bridge-1')
        ->and(DevIdentifier::label('strang.rekey-target-right.1.main.path.rekey-target.stem2.anchorNode-end.label-2'))
        ->toBe('strang.rekey.right.target.1.stem-2.anchorNode-end.label-2');
});

it('uses a stable fallback label for blank ids', function (): void {
    expect(DevIdentifier::label(null))->toBe('tw-graph')
        ->and(DevIdentifier::label('   '))->toBe('tw-graph');
});

it('keeps hand authored branch extension and return labels compact', function (): void {
    expect(DevIdentifier::label('roadmap.left.2.risk.paths.branch-extension.stem2.label.right.1.text'))
        ->toBe('strang.branch.left.2.stem-2.label.right.1')
        ->and(DevIdentifier::label('orders.right.1.refund.paths.branch-return.2.bridge1'))
        ->toBe('strang.branch.right.1.return-2.bridge-1')
        ->and(DevIdentifier::label('orders.right.1.refund.paths.branch-return-extension.3.stem1.label.left.1.text'))
        ->toBe('strang.branch.right.1.return-3.stem-1.label.left.1')
        ->and(DevIdentifier::label('orders.right.1.refund.paths.branch-return.2.end.label.top.1.text'))
        ->toBe('strang.branch.right.1.return-2.end.label.top.1');
});

it('keeps hand authored rekey source and target arc labels compact', function (): void {
    expect(DevIdentifier::label('migration.left.1.old-key.paths.rekey-source.arc.out.end-label.text'))
        ->toBe('strang.rekey.left.source.1.arc-south-east-2.end-label')
        ->and(DevIdentifier::label('migration.right.1.new-key.paths.rekey-target.arc.in'))
        ->toBe('strang.rekey.right.target.1.arc-west-north-1')
        ->and(DevIdentifier::label('migration.right.1.new-key.paths.rekey-target.stem.2.label.right.1.text'))
        ->toBe('strang.rekey.right.target.1.stem-2.label.right.1');
});

it('keeps hand authored trunk stems and start/end labels compact', function (): void {
    expect(DevIdentifier::label('literature.center.1.paper.paths.trunk.stem12.label.left.1.text'))
        ->toBe('strang.trunk.center.1.stem-12.label.left.1')
        ->and(DevIdentifier::label('literature.center.1.paper.paths.trunk.end.label.top.1.text'))
        ->toBe('strang.trunk.center.1.end.label.top.1');
});

it('keeps handwritten branch step end and return bridge labels compact', function (): void {
    expect(DevIdentifier::label('roadmap.left.1.risk.paths.branch.step.label.right.1.text'))
        ->toBe('strang.branch.left.1.step-1.label.right.1')
        ->and(DevIdentifier::label('roadmap.left.1.risk.paths.branch.step.stem.after.node.end'))
        ->toBe('strang.branch.left.1.step-1.anchorNode-end')
        ->and(DevIdentifier::label('roadmap.left.1.risk.paths.branch.end.label.top.1.text'))
        ->toBe('strang.branch.left.1.end.label.top.1')
        ->and(DevIdentifier::label('orders.right.1.refund.extension.2.paths.branch-extension.start.label.end.1.text'))
        ->toBe('strang.branch.right.1.extension-2.start.anchorNode-end.label-1')
        ->and(DevIdentifier::label('orders.right.1.refund.extension.2.return-bridge.1.bridge.node.end'))
        ->toBe('strang.branch.right.1.extension-2.return-1.bridge.anchorNode-end');
});

it('keeps start end and side labels free of generic source path filler', function (): void {
    expect(DevIdentifier::label('migration.left.1.shared-origin.paths.merge.start.start-label'))
        ->toBe('strang.merge.left.1.start.label')
        ->and(DevIdentifier::label('migration.left.1.shared-origin.extension.1.paths.merge-extension.start.start-label'))
        ->toBe('strang.merge.left.1.extension-1.start.label')
        ->and(DevIdentifier::label('migration.left.1.shared-origin.extension.1.paths.merge-extension.start.label.end.1.text'))
        ->toBe('strang.merge.left.1.extension-1.start.anchorNode-end.label-1')
        ->and(DevIdentifier::label('migration.left.1.shared-origin.paths.merge.stem1.label.end.1.text'))
        ->toBe('strang.merge.left.1.stem-1.anchorNode-end.label-1')
        ->and(DevIdentifier::label('migration.right.1.ended-finding.paths.branch.end.end-label'))
        ->toBe('strang.branch.right.1.end.label')
        ->and(DevIdentifier::label('migration.right.1.ended-finding.paths.branch.end.label.top.1.text'))
        ->toBe('strang.branch.right.1.end.label.top.1');
});

it('keeps displayed dev labels concise for canonical correction style ids', function (): void {
    expect(DevIdentifier::label('strang.left.6.branch.bridge1.bounds'))
        ->toBe('strang.branch.left.6.bridge-1')
        ->and(DevIdentifier::label('strang.right.1.rekey.target.stem2.anchorNode-end.label-2'))
        ->toBe('strang.rekey.right.target.1.stem-2.anchorNode-end.label-2')
        ->and(DevIdentifier::label('strang.left.1.merge.extension.3.start.stem.bounds'))
        ->toBe('strang.merge.left.1.extension-3.start.stem');
});

it('keeps branch return and extension dev labels distinguishable by their chapter numbers', function (): void {
    expect(DevIdentifier::label('strang.branch-left.1.extension2.path.branch-extension.bridge1'))
        ->toBe('strang.branch.left.1.extension-2.bridge-1')
        ->and(DevIdentifier::label('strang.branch-left.1.branch-return.2.path.branch-return.bridge1'))
        ->toBe('strang.branch.left.1.return-2.bridge-1')
        ->and(DevIdentifier::label('strang.branch-left.1.branch-return.3.path.branch-return.bridge1'))
        ->toBe('strang.branch.left.1.return-3.bridge-1');
});
