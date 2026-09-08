<?php

use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\DocumentationViewResolver;
use Tests\TestCase;

uses(TestCase::class);

it('returns null for missing documentation view directories', function (): void {
    expect(DocumentationViewResolver::latestOrdinalView(
        'translation-workbench::pages.tw-graph.samples.documentation.missing-section',
    ))->toBeNull();
});

it('resolves the highest ordinal documentation view inside one section', function (): void {
    expect(DocumentationViewResolver::latestOrdinalView(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge',
    ))->toBe(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.04-merge-aggregated',
    );
});

it('resolves the latest documentation view across ordered sections', function (): void {
    $latest = DocumentationViewResolver::latestOrdinalViewAcrossSections([
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-tw-graph',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.01-strang-trunk',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.04-strang-rekey',
    ]);

    expect($latest)->toBe(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.04-strang-rekey.01-rekey',
    );
});

it('skips empty or invalid documentation sections while resolving the latest authored step', function (): void {
    $latest = DocumentationViewResolver::latestOrdinalViewAcrossSections([
        null,
        '',
        'translation-workbench::pages.tw-graph.samples.documentation.missing-section',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-tw-graph',
    ]);

    expect($latest)->toBe(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-tw-graph.01-canvas',
    );
});

it('ignores underscore partials and non blade assets while resolving latest documentation steps', function (): void {
    expect(DocumentationViewResolver::latestOrdinalView(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch',
    ))->toBe(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch.01-branch',
    );
});

it('keeps ordinal resolution scoped to public documentation steps only', function (): void {
    $sections = [
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-tw-graph',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.01-strang-trunk',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch',
    ];

    expect(DocumentationViewResolver::latestOrdinalViewAcrossSections($sections))->toBe(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch.01-branch',
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
