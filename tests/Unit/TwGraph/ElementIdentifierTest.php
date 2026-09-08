<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\ElementIdentifier;

it('normalizes legacy side suffixes into stable strang references', function (): void {
    expect(ElementIdentifier::normalize('strang.branch-left.1.main.path.branch.bridge1'))
        ->toBe('strang.left.1.branch.bridge1')
        ->and(ElementIdentifier::normalize('strang.merge-right.2.extension1.path.merge-extension.start.label.end.1.text'))
        ->toBe('strang.right.2.merge.extension.1.start.label.end.1.text');
});

it('strips graph prefixes before normalizing element ids', function (): void {
    expect(ElementIdentifier::normalize('timeline.demo.strang.branch-right.5.main.path.branch.bridge1.bounds'))
        ->toBe('strang.right.5.branch.bridge1.bounds');
});

it('normalizes path and bounds naming variants', function (): void {
    expect(ElementIdentifier::normalize('strang.trunk.center.main.path.trunk.path3.label-bounds'))
        ->toBe('strang.trunk.center.stem3.bounds.label')
        ->and(ElementIdentifier::normalize('strang.branch-left.1.main.path.branch.stem-labels.bounds'))
        ->toBe('strang.left.1.branch.stem.labels.bounds');
});

it('keeps concise canonical element ids unchanged', function (): void {
    expect(ElementIdentifier::normalize('strang.left.7.branch.bridge1'))
        ->toBe('strang.left.7.branch.bridge1')
        ->and(ElementIdentifier::normalize('strang.trunk.center.start.label.left.1'))
        ->toBe('strang.trunk.center.start.label.left.1')
        ->and(ElementIdentifier::normalize('strang.right.1.rekey.target.stem2.anchorNode-end.label-2'))
        ->toBe('strang.right.1.rekey.target.stem2.anchorNode-end.label-2');
});

it('normalizes branch and rekey terminal segment ids used by debug bounds', function (): void {
    expect(ElementIdentifier::normalize('strang.branch-left.4.end.path.branch-end.segment'))
        ->toBe('strang.left.4.branch.end.segment')
        ->and(ElementIdentifier::normalize('strang.rekey-target-right.1.end.path.rekey-target-end.segment.bounds'))
        ->toBe('strang.right.1.rekey.target.end.segment.bounds')
        ->and(ElementIdentifier::normalize('strang.rekey-source-left.1.main.path.rekey-source.bridge1.bounds'))
        ->toBe('strang.left.1.rekey.source.bridge1.bounds');
});

it('accepts compact display style rekey ids as correction lookup targets', function (): void {
    expect(ElementIdentifier::normalize('strang.rekey.right.target.1.stem-2'))
        ->toBe('strang.right.1.rekey.target.stem-2')
        ->and(ElementIdentifier::startsWith('strang.rekey.right.target.1.stem-2', 'strang.rekey-target-right.1'))
        ->toBeTrue();
});

it('compares normalized ids for equality and prefixes', function (): void {
    expect(ElementIdentifier::equals(
        'strang.branch-left.1.main.path.branch.bridge1',
        'strang.left.1.branch.bridge1',
    ))->toBeTrue()
        ->and(ElementIdentifier::startsWith(
            'strang.branch-left.1.main.path.branch.bridge1.bounds',
            'strang.left.1.branch',
        ))->toBeTrue();
});

it('normalizes branch return and extension chapter numbers consistently', function (): void {
    expect(ElementIdentifier::normalize('strang.branch-right.2.branch-return.3.path.branch-return.bridge1'))
        ->toBe('strang.right.2.branch.return.3.bridge1')
        ->and(ElementIdentifier::normalize('strang.branch-left.4.extension2.path.branch-extension.stem3.bounds'))
        ->toBe('strang.left.4.branch.extension.2.stem3.bounds')
        ->and(ElementIdentifier::normalize('strang.merge-left.1.extension3.path.merge-extension.bridge1.bounds'))
        ->toBe('strang.left.1.merge.extension.3.bridge1.bounds');
});

it('returns empty normalized ids for blank input', function (): void {
    expect(ElementIdentifier::normalize(null))->toBe('')
        ->and(ElementIdentifier::normalize('   '))->toBe('')
        ->and(ElementIdentifier::startsWith('', 'strang.trunk'))->toBeFalse();
});

it('does not treat sibling canonical ids as prefix matches', function (): void {
    expect(ElementIdentifier::startsWith(
        'strang.left.10.branch.bridge1',
        'strang.left.1.branch',
    ))->toBeFalse()
        ->and(ElementIdentifier::equals(
            'strang.left.1.branch.bridge1',
            'strang.left.1.branch.bridge2',
        ))->toBeFalse();
});

it('matches compact canonical ids against legacy long correction targets', function (): void {
    expect(ElementIdentifier::startsWith(
        'strang.branch-left.3.main.path.branch.bridge1.bounds',
        'strang.left.3.branch.bridge1',
    ))->toBeTrue()
        ->and(ElementIdentifier::startsWith(
            'strang.rekey-target-right.1.main.path.rekey-target.stem2.anchorNode-end.label-2',
            'strang.right.1.rekey.target',
        ))->toBeTrue()
        ->and(ElementIdentifier::startsWith(
            'strang.merge-left.1.extension3.path.merge-extension.stem12.label.end.1.text',
            'strang.left.1.merge.extension.3.stem12',
        ))->toBeTrue();
});

it('normalizes compact path number variants without changing unrelated text', function (): void {
    expect(ElementIdentifier::normalize('strang.trunk.1.path.12.bounds.label'))
        ->toBe('strang.trunk.1.stem12.bounds.label')
        ->and(ElementIdentifier::normalize('plain.element.id'))->toBe('plain.element.id');
});

it('normalizes handwritten branch step end and return debug ids consistently', function (): void {
    expect(ElementIdentifier::normalize('roadmap.left.1.risk.paths.branch.step.label.right.1.text'))
        ->toBe('roadmap.left.1.risk.paths.branch.step.label.right.1.text')
        ->and(ElementIdentifier::normalize('strang.branch-left.1.main.path.branch.step.bounds'))
        ->toBe('strang.left.1.branch.step.bounds')
        ->and(ElementIdentifier::normalize('strang.branch-right.4.end.path.branch-end.segment.bounds'))
        ->toBe('strang.right.4.branch.end.segment.bounds')
        ->and(ElementIdentifier::normalize('strang.branch-left.2.extension.3.path.branch-extension.start.stem.bounds'))
        ->toBe('strang.left.2.branch.extension.3.start.stem.bounds')
        ->and(ElementIdentifier::normalize('strang.branch-right.2.branch-return.1.path.branch-return.arc.out.node.end'))
        ->toBe('strang.right.2.branch.return.1.arc.out.node.end');
});

it('leaves hand authored sample ids intact while canonical strang ids stay compact', function (): void {
    expect(ElementIdentifier::normalize('migration.left.1.shared-origin.paths.merge.start.start-label'))
        ->toBe('migration.left.1.shared-origin.paths.merge.start.start-label')
        ->and(ElementIdentifier::normalize('migration.left.1.shared-origin.extension.1.stem1.label.end.1.text'))
        ->toBe('migration.left.1.shared-origin.extension.1.stem1.label.end.1.text')
        ->and(ElementIdentifier::normalize('strang.merge-left.1.main.path.merge.start.label.end.1.text'))
        ->toBe('strang.left.1.merge.start.label.end.1.text')
        ->and(ElementIdentifier::normalize('strang.merge-left.1.extension.1.path.merge-extension.stem1.label.end.1.text'))
        ->toBe('strang.left.1.merge.extension.1.stem1.label.end.1.text');
});

it('matches concise correction targets against verbose rendered label ids', function (): void {
    expect(ElementIdentifier::startsWith(
        'strang.branch-left.6.main.path.branch.bridge1.bounds',
        'strang.left.6.branch.bridge1',
    ))->toBeTrue()
        ->and(ElementIdentifier::startsWith(
            'strang.branch-left.4.end.path.branch-end.segment.label.top.1.text',
            'strang.left.4.branch.end.segment',
        ))->toBeTrue()
        ->and(ElementIdentifier::startsWith(
            'strang.rekey-target-right.1.main.path.rekey-target.stem2.anchorNode-end.label-2',
            'strang.right.1.rekey.target.stem2',
        ))->toBeTrue()
        ->and(ElementIdentifier::startsWith(
            'strang.merge-left.1.extension3.path.merge-extension.start.stem.labels.bounds',
            'strang.left.1.merge.extension.3.start.stem',
        ))->toBeTrue();
});

it('keeps canonical correction targets compact while matching verbose render ids', function (): void {
    expect(ElementIdentifier::normalize('strang.merge.left.1.bridge1'))
        ->toBe('strang.left.1.merge.bridge1')
        ->and(ElementIdentifier::normalize('strang.branch.right.2.end.segment'))
        ->toBe('strang.right.2.branch.end.segment')
        ->and(ElementIdentifier::normalize('strang.rekey.left.source.1.arc-south-east-2.end-label'))
        ->toBe('strang.left.1.rekey.source.arc-south-east-2.end-label')
        ->and(ElementIdentifier::startsWith(
            'strang.merge-left.1.extension3.path.merge-extension.bridge1.bounds',
            'strang.merge.left.1.extension.3.bridge1',
        ))->toBeTrue()
        ->and(ElementIdentifier::startsWith(
            'strang.branch-right.2.end.path.branch-end.segment.label.top.1.text',
            'strang.branch.right.2.end.segment',
        ))->toBeTrue()
        ->and(ElementIdentifier::startsWith(
            'strang.rekey-source-left.1.main.path.rekey-source.arc-south-east-2.end-label.bounds',
            'strang.rekey.left.source.1.arc-south-east-2.end-label',
        ))->toBeTrue();
});

it('treats compact and dotted extension correction targets as the same canonical id', function (): void {
    expect(ElementIdentifier::normalize('strang.merge-left.1.extension3'))
        ->toBe('strang.left.1.merge.extension.3')
        ->and(ElementIdentifier::normalize('strang.merge.left.1.extension.3'))
        ->toBe('strang.left.1.merge.extension.3')
        ->and(ElementIdentifier::normalize('strang.branch-right.2.extension4.stem2'))
        ->toBe('strang.right.2.branch.extension.4.stem2')
        ->and(ElementIdentifier::startsWith(
            'strang.merge-left.1.extension3.path.merge-extension.start.stem.bounds',
            'strang.merge.left.1.extension.3',
        ))->toBeTrue()
        ->and(ElementIdentifier::startsWith(
            'strang.branch-right.2.extension4.path.branch-extension.stem2.bounds',
            'strang.branch.right.2.extension.4',
        ))->toBeTrue();
});
