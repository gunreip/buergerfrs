<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\DocumentationViewResolver;
use Tests\TestCase;

uses(TestCase::class);

it('returns null for missing documentation view directories', function (): void {
    expect(DocumentationViewResolver::latestOrdinalView(
        'translation-workbench::pages.tw-graph.samples.documentation.missing-section',
    ))->toBeNull();
});

it('resolves an explicit final view for a reorganized section', function (): void {
    expect(DocumentationViewResolver::latestOrdinalViewAcrossSections([
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-final',
    ]))->toBe(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-final',
    );
});

it('resolves the latest documentation view across ordered sections', function (): void {
    $latest = DocumentationViewResolver::latestOrdinalViewAcrossSections([
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-final',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-final',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.branch-final',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.rekey.rekey-final',
    ]);

    expect($latest)->toBe(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.rekey.rekey-final',
    );
});

it('skips empty or invalid documentation sections while resolving the latest authored step', function (): void {
    $latest = DocumentationViewResolver::latestOrdinalViewAcrossSections([
        null,
        '',
        'translation-workbench::pages.tw-graph.samples.documentation.missing-section',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-final',
    ]);

    expect($latest)->toBe(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-final',
    );
});

it('does not select documentation examples by filename after section reorganization', function (): void {
    expect(DocumentationViewResolver::latestOrdinalView(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch',
    ))->toBeNull();
});

it('keeps ordinal resolution scoped to public documentation steps only', function (): void {
    $sections = [
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-final',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-final',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.branch-final',
    ];

    expect(DocumentationViewResolver::latestOrdinalViewAcrossSections($sections))->toBe(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.branch-final',
    );
});

it('keeps the idea to paper master graph wired to the latest authored graph step', function (): void {
    $source = file_get_contents(view()->getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.99-graph-master',
    ));

    expect($source)
        ->toContain('latestOrdinalViewAcrossSections($sections)')
        ->toContain("'renderMode' => 'graph'")
        ->toContain("'ideaToPaperGraphId' => \$ideaToPaperGraphId")
        ->toContain("'ideaToPaperDev' => \$ideaToPaperDev")
        ->toContain("'ideaToPaperCoordinates' => \$ideaToPaperCoordinates")
        ->not->toContain("idea-to-paper.00-graph-final")
        ->not->toContain("idea-to-paper._graph-current-result");
});
