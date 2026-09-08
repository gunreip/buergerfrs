<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

uses(TestCase::class);

it('renders the idea to paper documentation index with top level tabs and result tabs', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.index', [
        'dev' => true,
        'coordinates' => false,
    ])->render();

    expect($html)
        ->toContain('Authoring story: from thought draft to visible graph')
        ->toContain('name="idea-to-paper-canvas"')
        ->toContain('name="idea-to-paper-trunk"')
        ->toContain('name="idea-to-paper-merge"')
        ->toContain('name="idea-to-paper-branch"')
        ->toContain('name="idea-to-paper-rekey"')
        ->toContain('name="idea-to-paper-draft"')
        ->toContain('name="idea-to-paper-result"')
        ->toContain('tw-graph-sample-idea-to-paper-thought-draft')
        ->toContain('tw-graph-sample-idea-to-paper-current-result');
});

it('renders idea to paper split branch and rekey documentation fragments', function (): void {
    $branchHtml = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch.01-branch', [
        'dev' => true,
        'coordinates' => false,
    ])->render();
    $rekeyHtml = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.04-strang-rekey.01-rekey', [
        'dev' => true,
        'coordinates' => false,
    ])->render();

    expect($branchHtml)
        ->toContain('branchVariant')
        ->toContain('idea-to-paper-step-06-branch')
        ->toContain('literature.left.1.side-thought')
        ->toContain('literature.right.1.side-thought')
        ->and($rekeyHtml)
        ->toContain('rekeyVariant')
        ->toContain('idea-to-paper-step-07-rekey')
        ->toContain('literature.left.source.1.old-title')
        ->toContain('literature.right.target.1.new-paper');
});

it('keeps branch documentation code tabs matched to preview variants', function (): void {
    $codeTabs = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch._branch-code-tabs',
    ));
    $previews = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch._branch-preview-variants',
    ));

    expect($codeTabs)
        ->toContain('name="branch-default"')
        ->toContain('name="branch-offset"')
        ->toContain('name="branch-step"')
        ->toContain('name="branch-continuation"')
        ->toContain('name="branch-return"')
        ->toContain("'attachTo' => 'stem.1.end'")
        ->toContain("'fallback' => false")
        ->and($previews)
        ->toContain('x-show="branchVariant === \'default\'"')
        ->toContain('x-show="branchVariant === \'offset\'"')
        ->toContain('x-show="branchVariant === \'step\'"')
        ->toContain('x-show="branchVariant === \'continuation\'"')
        ->toContain('x-show="branchVariant === \'return\'"')
        ->toContain("'attachTo' => 'stem.1.end'")
        ->toContain("'fallback' => false");
});

it('keeps idea to paper branch subtabs scoped to one code panel and one preview panel per variant', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch.01-branch',
    ));
    $codeTabs = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch._branch-code-tabs',
    ));
    $previews = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch._branch-preview-variants',
    ));

    foreach (['default', 'offset', 'step', 'continuation', 'return'] as $variant) {
        expect(substr_count($source . $codeTabs . $previews, "branchVariant === '{$variant}'"))->toBe(2);
    }

    expect($source)
        ->toContain('x-data="{ branchVariant: \'default\' }"')
        ->not->toContain('subsub')
        ->not->toContain('subSub');
});

it('keeps rekey documentation code tabs matched to preview variants', function (): void {
    $codeTabs = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.04-strang-rekey._rekey-code-tabs',
    ));
    $previews = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.04-strang-rekey._rekey-preview-variants',
    ));

    expect($codeTabs)
        ->toContain('name="rekey-default"')
        ->toContain('name="rekey-source"')
        ->toContain('name="rekey-target"')
        ->toContain('name="rekey-compressed"')
        ->toContain('rekey source')
        ->toContain('rekey target')
        ->not->toContain('Merge')
        ->and($previews)
        ->toContain('x-show="rekeyVariant === \'default\'"')
        ->toContain('x-show="rekeyVariant === \'source\'"')
        ->toContain('x-show="rekeyVariant === \'target\'"')
        ->toContain('x-show="rekeyVariant === \'compressed\'"')
        ->toContain(':compressed-stem-parts')
        ->toContain("'compressed' => true")
        ->not->toContain('Merge');
});

it('keeps idea to paper rekey subtabs scoped to one code panel and one preview panel per variant', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.04-strang-rekey.01-rekey',
    ));
    $codeTabs = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.04-strang-rekey._rekey-code-tabs',
    ));
    $previews = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.04-strang-rekey._rekey-preview-variants',
    ));

    foreach (['default', 'source', 'target', 'compressed'] as $variant) {
        expect(substr_count($source . $codeTabs . $previews, "rekeyVariant === '{$variant}'"))->toBe(2);
    }

    expect($source)
        ->toContain('x-data="{ rekeyVariant: \'default\' }"')
        ->not->toContain('subsub')
        ->not->toContain('subSub');
});

it('renders the idea to paper trunk documentation with the expected subtabs and examples', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.01-strang-trunk.01-trunk', [
        'dev' => true,
        'coordinates' => false,
    ])->render();

    expect($html)
        ->toContain('name="trunk-default"')
        ->toContain('name="trunk-stem-count"')
        ->toContain('name="trunk-stem-lengths"')
        ->toContain('name="trunk-direction"')
        ->toContain('name="trunk-start"')
        ->toContain('name="trunk-end"')
        ->toContain('Default renders the trunk exactly as configured by the graph defaults.')
        ->toContain('literature.center.1.paper')
        ->toContain('idea-to-paper-step-02-trunk-stem-count')
        ->toContain('idea-to-paper-step-02-trunk-stem-lengths')
        ->toContain('idea-to-paper-step-02-trunk-direction')
        ->toContain('idea-to-paper-step-02-trunk-start-default')
        ->toContain('idea-to-paper-step-02-trunk-end-default');
});

it('keeps idea to paper trunk subtabs scoped to one code panel and one preview panel per variant', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.01-strang-trunk.01-trunk',
    ));

    foreach (['default', 'stemCount', 'stemLengths', 'direction', 'trunkStart', 'trunkEnd'] as $variant) {
        expect(substr_count($source, "x-show=\"trunkVariant === '{$variant}'\""))->toBe(2);
    }

    expect($source)
        ->toContain('x-data="{ trunkVariant: \'default\' }"')
        ->toContain('name="trunk-default"')
        ->toContain('name="trunk-stem-count"')
        ->toContain('name="trunk-stem-lengths"')
        ->toContain('name="trunk-direction"')
        ->toContain('name="trunk-start"')
        ->toContain('name="trunk-end"')
        ->not->toContain('subsub')
        ->not->toContain('subSub');
});

it('keeps idea to paper trunk default focused on an unmodified trunk component', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.01-strang-trunk.01-trunk',
    ));
    preg_match('/<flux:tab\.panel name="trunk-default">(.*?)<\/flux:tab\.panel>/s', $source, $defaultPanelMatches);

    expect($defaultPanelMatches[1] ?? '')
        ->toContain('&lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;')
        ->not->toContain('stem-length=')
        ->not->toContain(':stem-count')
        ->not->toContain(':stem-lengths')
        ->not->toContain('color=');
});

it('keeps idea to paper trunk examples explicit about dev mode and scoped props', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.01-strang-trunk.01-trunk',
    ));

    expect($source)
        ->toContain(':dev="true"')
        ->toContain('&lt;x-translation-workbench::ui.tw-graph')
        ->toContain('    ...')
        ->toContain('<span class="text-lime-300">:stem-count="4"</span>')
        ->toContain('<span class="text-lime-300">:stem-lengths="[')
        ->toContain('3 => \'8rem\',')
        ->toContain('<span class="text-amber-300">color="sky"</span>')
        ->toContain('<span class="text-amber-300">color="emerald"</span>')
        ->not->toContain('path-length=')
        ->not->toContain('pathLengths');
});

it('renders the idea to paper canvas documentation with default and prop comparison examples', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-tw-graph.01-canvas', [
        'dev' => true,
        'coordinates' => false,
    ])->render();

    expect($html)
        ->toContain('name="canvas-default"')
        ->toContain('name="canvas-default-trunk"')
        ->toContain('name="canvas-coordinates"')
        ->toContain('name="canvas-height"')
        ->toContain('name="canvas-props"')
        ->toContain('Default renders an empty graph canvas exactly as configured by the graph defaults.')
        ->toContain('This keeps the canvas at its defaults and adds a default trunk')
        ->toContain('literature.center.1.paper')
        ->toContain('idea-to-paper-step-01-coordinates')
        ->toContain('idea-to-paper-step-01-slot-height')
        ->toContain('idea-to-paper-step-01-props-line-custom')
        ->toContain('idea-to-paper-step-01-props-stem-custom');
});

it('keeps idea to paper canvas examples explicit and variant scoped', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-tw-graph.01-canvas',
    ));

    expect($source)
        ->toContain('x-data="{ canvasVariant:')
        ->toContain('x-show="canvasVariant === \'default\'"')
        ->toContain('x-show="canvasVariant === \'defaultTrunk\'"')
        ->toContain('x-show="canvasVariant === \'coordinates\'"')
        ->toContain('x-show="canvasVariant === \'height\'"')
        ->toContain('x-show="canvasVariant === \'props\'"')
        ->toContain('x-show="canvasPropsVariant === \'line\'"')
        ->toContain('x-show="canvasPropsVariant === \'stemLength\'"')
        ->toContain('x-show="canvasPropsVariant === \'nodeSize\'"')
        ->toContain('x-show="canvasPropsVariant === \'capLength\'"')
        ->toContain('x-show="canvasPropsVariant === \'minWidth\'"')
        ->toContain(':dev="true"')
        ->toContain(':coordinates="true"')
        ->toContain(':coordinates="false"')
        ->not->toContain(':coordinates="$coordinates"');
});

it('keeps idea to paper canvas default examples free of hidden visual overrides', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-tw-graph.01-canvas',
    ));
    preg_match('/<flux:tab\.panel name="canvas-default">(.*?)<\/flux:tab\.panel>/s', $source, $defaultPanelMatches);
    preg_match('/<flux:tab\.panel name="canvas-default-trunk">(.*?)<\/flux:tab\.panel>/s', $source, $defaultTrunkPanelMatches);

    expect($defaultPanelMatches[1] ?? '')
        ->toContain('&lt;x-translation-workbench::ui.tw-graph&gt;&lt;/x-translation-workbench::ui.tw-graph&gt;')
        ->not->toContain(':dev=')
        ->not->toContain('color=');

    expect($defaultTrunkPanelMatches[1] ?? '')
        ->toContain('&lt;x-translation-workbench::ui.tw-graph&gt;' . "\n"
            . '    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;' . "\n"
            . '&lt;/x-translation-workbench::ui.tw-graph&gt;')
        ->not->toContain('stem-length=')
        ->not->toContain('color=');
});

it('keeps idea to paper canvas prop examples visually scoped and explicitly colored', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-tw-graph.01-canvas',
    ));

    expect($source)
        ->toContain('name="canvas-props-line"')
        ->toContain('name="canvas-props-stem-length"')
        ->toContain('name="canvas-props-node-size"')
        ->toContain('name="canvas-props-cap-length"')
        ->toContain('name="canvas-props-min-width"')
        ->toContain('<span class="text-lime-300">line-width="0.5rem"</span>')
        ->toContain('<span class="text-lime-300">stem-length="8rem"</span>')
        ->toContain('<span class="text-lime-300">color="emerald"</span>')
        ->toContain('<span class="text-amber-300">min-height="88rem"</span>')
        ->toContain('<span class="text-amber-300">horizontal-padding="24rem"</span>')
        ->not->toContain(':dev="$dev"')
        ->not->toContain(':coordinates="$coordinates"');
});

it('keeps canvas height examples focused on slot and minimum height differences', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-tw-graph.01-canvas',
    ));

    expect($source)
        ->toContain('graph-id="idea-to-paper-step-01-slot-height"')
        ->toContain('<span class="text-lime-300">slot-min-height="30rem"</span>')
        ->toContain('slot-min-height is the fallback room for slotted handmade graph content.')
        ->toContain('graph-id="idea-to-paper-step-01-min-height"')
        ->toContain('<span class="text-lime-300">min-height="88rem"</span>')
        ->toContain('min-height overrides the visible minimum height of the graph canvas.')
        ->toContain('The first graph sets slot-min-height below the calculated graph bounds')
        ->toContain('The second graph sets min-height above the calculated bounds');
});

it('renders the idea to paper merge documentation fragments with default extension and aggregate examples', function (): void {
    $defaultHtml = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.01-merge', [
        'dev' => true,
        'coordinates' => false,
    ])->render();
    $startHtml = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.02-merge-start', [
        'dev' => true,
        'coordinates' => false,
    ])->render();
    $extensionHtml = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.03-merge-extension', [
        'dev' => true,
        'coordinates' => false,
    ])->render();
    $aggregateHtml = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.04-merge-aggregated', [
        'dev' => true,
        'coordinates' => false,
    ])->render();

    expect($defaultHtml)
        ->toContain('5. Merge')
        ->toContain('idea-to-paper-step-05-merge')
        ->toContain('literature.left.1.source-note')
        ->toContain('literature.right.1.source-note')
        ->and($startHtml)
        ->toContain('6. Merge start')
        ->toContain('idea-to-paper-step-06-merge-start')
        ->toContain('Archive finding')
        ->toContain('Finding ID #42')
        ->and($extensionHtml)
        ->toContain('7. Merge extension')
        ->toContain('idea-to-paper-step-07-merge-extension')
        ->toContain('Second source')
        ->toContain('Second review')
        ->and($aggregateHtml)
        ->toContain('8. Merge aggregated')
        ->toContain('idea-to-paper-step-08-merge-aggregated')
        ->toContain('Aggregated origins')
        ->toContain('extension-stem-continuations');
});

it('renders the project roadmap documentation index and embedded hardcopy references', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.project-roadmap.index')->render();

    expect($html)
        ->toContain('Project Roadmap: authoring notes')
        ->toContain('Project roadmap trunk hardcopy')
        ->toContain('Project roadmap merge-left hardcopy')
        ->toContain('Project roadmap branch-right hardcopy')
        ->not->toContain('Hardcopy notes');
});

it('keeps project roadmap documentation sections included in their authored order', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.project-roadmap.index',
    ));

    expect($source)
        ->toContain("project-roadmap.01-overview")
        ->toContain("project-roadmap.02-graph-wrapper")
        ->toContain("project-roadmap.02-trunk")
        ->toContain("project-roadmap.03-branches")
        ->toContain("project-roadmap.04-release-and-end")
        ->not->toContain("project-roadmap.05-hardcopys");
});

it('keeps idea to paper draft and current result as separate graph includes', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.index',
    ));

    expect($source)
        ->toContain("name=\"idea-to-paper-draft\"")
        ->toContain("name=\"idea-to-paper-result\"")
        ->toContain("idea-to-paper.00-graph-final")
        ->toContain("idea-to-paper._graph-current-result")
        ->toContain("'tw-graph-sample-idea-to-paper-thought-draft'")
        ->toContain("'tw-graph-sample-idea-to-paper-current-result'");
});

it('keeps idea to paper current result authored separately from the hidden thought draft', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper._graph-current-result',
    ));

    expect($source)
        ->toContain('tw-graph-sample-idea-to-paper-current-result')
        ->toContain('literature.center.1.paper')
        ->not->toContain('idea-to-paper.00-graph-final')
        ->not->toContain('tw-graph-sample-idea-to-paper-thought-draft');
});

it('keeps the idea to paper master graph assembled from documentation sections only', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.99-graph-master',
    ));

    expect($source)
        ->toContain('DocumentationViewResolver::latestOrdinalViewAcrossSections')
        ->toContain('00-tw-graph')
        ->toContain('01-strang-trunk')
        ->toContain('02-strang-merge')
        ->toContain('03-strang-branch')
        ->toContain('04-strang-rekey')
        ->not->toContain("idea-to-paper.00-graph-final")
        ->not->toContain("idea-to-paper._graph-current-result");
});

it('keeps idea to paper top level tabs delegated to one documentation section each', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.index',
    ));

    $sections = [
        '00-tw-graph.01-canvas',
        '01-strang-trunk.01-trunk',
        '02-strang-merge.01-merge',
        '02-strang-merge.02-merge-start',
        '02-strang-merge.03-merge-extension',
        '02-strang-merge.04-merge-aggregated',
        '03-strang-branch.01-branch',
        '04-strang-rekey.01-rekey',
    ];

    foreach ($sections as $section) {
        expect(substr_count($source, "idea-to-paper.{$section}"))->toBe(1);
    }

    expect($source)
        ->toContain('<flux:tab.panel name="idea-to-paper-canvas">')
        ->toContain('<flux:tab.panel name="idea-to-paper-trunk">')
        ->toContain('<flux:tab.panel name="idea-to-paper-merge">')
        ->toContain('<flux:tab.panel name="idea-to-paper-branch">')
        ->toContain('<flux:tab.panel name="idea-to-paper-rekey">')
        ->not->toContain('subsub')
        ->not->toContain('path/to/file');
});

it('documents trunk authoring with public stem props instead of internal path length names', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.01-strang-trunk.01-trunk',
    ));

    expect($source)
        ->toContain(':stem-count')
        ->toContain(':stem-lengths')
        ->toContain('Stem indexes name the rendered stem sections')
        ->toContain('DEV node counters')
        ->toContain('<span class="text-lime-300">:stem-count="4"</span>')
        ->toContain('<span class="text-lime-300">:stem-lengths="[')
        ->not->toContain(':path-count')
        ->not->toContain(':path-lengths')
        ->not->toContain('pathLengths')
        ->not->toContain('stemLength=');
});

it('renders the hand authored process sample pages with their graph ids and dev wrappers', function (): void {
    $samples = [
        'translation-workbench::pages.tw-graph.samples.bug-lifecycle' => [
            'Bug lifecycle graph canvas',
            'tw-graph-sample-bug-lifecycle',
            'bugLifecycleDev',
            'bug.center.1.trunk',
        ],
        'translation-workbench::pages.tw-graph.samples.order-lifecycle' => [
            'Order lifecycle graph canvas',
            'tw-graph-sample-order-lifecycle',
            'orderLifecycleDev',
            'order.center.1.lifecycle',
        ],
        'translation-workbench::pages.tw-graph.samples.translation-migration' => [
            'Translation migration graph canvas',
            'tw-graph-sample-translation-migration',
            'translationMigrationDev',
            'migration.center.1.trunk',
        ],
        'translation-workbench::pages.tw-graph.samples.project-roadmap' => [
            'Project roadmap graph canvas',
            'tw-graph-sample-project-roadmap',
            'projectRoadmapDev',
            'roadmap.center.1.timeline',
        ],
    ];

    foreach ($samples as $view => $markers) {
        $source = file_get_contents(View::getFinder()->find($view));

        foreach ($markers as $marker) {
            expect($source)->toContain($marker);
        }

        expect($source)
        ->toContain('tw-graph-protocol-dev-disabled')
        ->toContain('x-bind:checked')
        ->toContain('DEV');
    }
});

it('keeps all hand authored sample pages wired to their graph ids without leaking label options', function (): void {
    $samples = [
        'translation-workbench::pages.tw-graph.samples.resume-a-einstein' => [
            'Resume A. Einstein',
            'tw-graph-sample-resume-a-einstein',
        ],
        'translation-workbench::pages.tw-graph.samples.bug-lifecycle' => [
            'Bug Lifecycle',
            'tw-graph-sample-bug-lifecycle',
        ],
        'translation-workbench::pages.tw-graph.samples.order-lifecycle' => [
            'Order Lifecycle',
            'tw-graph-sample-order-lifecycle',
        ],
        'translation-workbench::pages.tw-graph.samples.project-roadmap' => [
            'Project Roadmap',
            'tw-graph-sample-project-roadmap',
        ],
        'translation-workbench::pages.tw-graph.samples.translation-migration' => [
            'Translation Migration',
            'tw-graph-sample-translation-migration',
        ],
        'translation-workbench::pages.tw-graph.samples.idea-to-paper' => [
            'Idea To Paper',
            'tw-graph-sample-idea-to-paper-final-draft',
        ],
    ];

    foreach ($samples as $view => $markers) {
        $source = file_get_contents(View::getFinder()->find($view));

        foreach ($markers as $marker) {
            expect($source)->toContain($marker);
        }

        expect($source)
            ->not->toContain('halfLong right')
            ->not->toContain('long center')
            ->not->toContain('default right');
    }
});

it('keeps all hand authored sample page sources free of raw blade and label leaks', function (): void {
    $samples = [
        'translation-workbench::pages.tw-graph.samples.resume-a-einstein' => 'tw-graph-sample-resume-a-einstein',
        'translation-workbench::pages.tw-graph.samples.bug-lifecycle' => 'tw-graph-sample-bug-lifecycle',
        'translation-workbench::pages.tw-graph.samples.order-lifecycle' => 'tw-graph-sample-order-lifecycle',
        'translation-workbench::pages.tw-graph.samples.project-roadmap' => 'tw-graph-sample-project-roadmap',
        'translation-workbench::pages.tw-graph.samples.translation-migration' => 'tw-graph-sample-translation-migration',
        'translation-workbench::pages.tw-graph.samples.idea-to-paper' => 'tw-graph-sample-idea-to-paper-final-draft',
    ];

    foreach ($samples as $view => $graphId) {
        $source = file_get_contents(View::getFinder()->find($view));

        expect($source)
            ->toContain($graphId)
            ->and(
                str_contains($source, 'x-translation-workbench::ui.tw-graph')
                || str_contains($source, 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.index')
            )->toBeTrue()
            ->and($source)
            ->not->toContain('Undefined variable')
            ->not->toContain('Undefined constant')
            ->not->toContain('htmlspecialchars(): Argument #1')
            ->not->toContain('halfLong right')
            ->not->toContain('long center')
            ->not->toContain('default right');
    }
});

it('renders hand authored graph documentation partials without blade runtime errors', function (): void {
    $partials = [
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-graph-final' => 'tw-graph-sample-idea-to-paper-final-draft',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper._graph-current-result' => 'tw-graph-sample-idea-to-paper-current-result',
        'translation-workbench::pages.tw-graph.samples.documentation.project-roadmap.index' => 'Project Roadmap: authoring notes',
    ];

    foreach ($partials as $view => $marker) {
        $html = view($view, [
            'dev' => true,
            'coordinates' => false,
        ])->render();

        expect($html)
            ->toContain($marker)
            ->not->toContain('Undefined variable')
            ->not->toContain('Undefined constant')
            ->not->toContain('htmlspecialchars(): Argument #1');
    }
});

it('renders idea to paper ordinal documentation steps without broken split partials', function (): void {
    $views = [
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-tw-graph.01-canvas',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.01-strang-trunk.01-trunk',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.01-strang-trunk.02-trunk-start',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.01-strang-trunk.03-trunk-end',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.01-merge',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.02-merge-start',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.03-merge-extension',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.04-merge-aggregated',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch.01-branch',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.04-strang-rekey.01-rekey',
    ];

    foreach ($views as $view) {
        $html = view($view, [
            'dev' => true,
            'coordinates' => false,
        ])->render();

        expect($html)
            ->toContain('tw-graph')
            ->not->toContain('Undefined variable')
            ->not->toContain('Undefined constant');
    }
});

it('keeps translation migration labels individually configurable with named options', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.translation-migration',
    ));

    expect($source)
        ->toMatch("/'left'\\s*=>\\s*\\[\\s*'text'\\s*=>\\s*\\['Lang values linked',\\s*'de\\/en active'\\]/")
        ->toMatch("/'right'\\s*=>\\s*\\[\\s*'text'\\s*=>\\s*\\['source lang value ID #801',\\s*'target lang value ID #802'\\]/")
        ->toMatch("/'text'\\s*=>\\s*'merged into\\|shared key ID #124',\\s*'width'\\s*=>\\s*'default',\\s*'align'\\s*=>\\s*'right'/")
        ->toMatch("/'text'\\s*=>\\s*'Target key ID #209\\|ui\\.button\\.save\\.primary',\\s*'width'\\s*=>\\s*'halfLong',\\s*'align'\\s*=>\\s*'right',\\s*'color'\\s*=>\\s*'sky'/")
        ->toMatch("/'text'\\s*=>\\s*'Rekey target\\|continues as key ID #209\\|2026-05-20 15:44',\\s*'width'\\s*=>\\s*'long',\\s*'align'\\s*=>\\s*'center',\\s*'color'\\s*=>\\s*'sky'/")
        ->toContain('color="amber"')
        ->toContain('color="rose"')
        ->toContain('color="sky"')
        ->not->toContain("'right' => 'Origin key|admin.buttons.save',\n                                        'width'")
        ->not->toContain('counter-start="5"');
});

it('keeps hand authored order lifecycle branch returns explicitly solid', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.order-lifecycle',
    ));

    expect($source)
        ->toContain(":branch-return=\"[")
        ->toContain("'attachTo' => 'stem.1'")
        ->toContain("'fallback' => false")
        ->toContain("'text' => ['Payment failed', 'issuer declined']")
        ->toContain("'text' => ['Fulfillment delay', 'stock mismatch']")
        ->not->toContain("'fallback' => true");
});

it('documents solid branch returns as the hand authored default', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch._branch-code-tabs',
    ));

    expect($source)
        ->toContain("'fallback' => false")
        ->toContain('name="branch-return"')
        ->toContain("'bridgeLength' => '22rem'")
        ->not->toContain("'fallback' => true");
});

it('renders translation migration label text without leaking formatting options', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="translation-migration-label-contract" color="green" :dev="true" :coordinates="false">
            <x-translation-workbench::ui.tw-graph.strang.merge-left
                id="migration.left.1.shared-origin"
                color="amber"
                bridge-length="30rem"
                stem-length="4rem"
                :node-labels="[
                    5 => [
                        'left' => [
                            'text' => 'merged into|shared key ID #124',
                            'width' => 'default',
                            'align' => 'right',
                        ],
                    ],
                ]"
            />

            <x-translation-workbench::ui.tw-graph.strang.rekey-target-left
                id="migration.left.1.rekey-target"
                attach-to="strang.merge-left.end"
                color="sky"
                bridge-length="16rem"
                stem-length="4rem"
                :stem-continuation="[
                    1 => [
                        '4rem',
                        'left' => [
                            'text' => 'Target key ID #209|ui.button.save.primary',
                            'width' => 'halfLong',
                            'align' => 'right',
                            'color' => 'sky',
                        ],
                    ],
                ]"
                :end-label="[
                    'text' => 'Rekey target|continues as key ID #209|2026-05-20 15:44',
                    'width' => 'long',
                    'align' => 'center',
                    'color' => 'sky',
                ]"
            />
        </x-translation-workbench::ui.tw-graph>
    BLADE);

    expect($html)
        ->toContain('merged into')
        ->toContain('shared key ID #124')
        ->toContain('Target key ID #209')
        ->toContain('ui.button.save.primary')
        ->toContain('Rekey target')
        ->toContain('continues as key ID #209')
        ->toContain('text-amber-700')
        ->toContain('text-sky-800')
        ->toContain('w-72')
        ->toContain('w-96')
        ->not->toContain('merged into|shared key ID #124')
        ->not->toContain('halfLong right')
        ->not->toContain('long center')
        ->not->toContain('default right');
});

it('renders the resume and idea to paper sample pages with their final graph canvases', function (): void {
    $resumeSource = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.resume-a-einstein',
    ));
    $ideaSource = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.idea-to-paper',
    ));

    expect($resumeSource)
        ->toContain('Resume A. Einstein')
        ->toContain('tw-graph-sample-resume-a-einstein')
        ->toContain('tw-graph-sample-resume-a-einstein-top-bottom')
        ->toContain('resume.center.1.start')
        ->toContain("'resume.top-bottom.'")
        ->and($ideaSource)
        ->toContain('Idea To Paper')
        ->toContain('tw-graph-sample-idea-to-paper-final-draft')
        ->toContain('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.index')
        ->toContain('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-graph-final');
});

it('keeps idea to paper canvas prop examples paired as default and custom previews', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-tw-graph.01-canvas',
    ));

    $propExamples = [
        'line' => [
            'defaultId' => 'idea-to-paper-step-01-props-line-default',
            'customId' => 'idea-to-paper-step-01-props-line-custom',
            'prop' => 'line-width="0.5rem"',
        ],
        'stem' => [
            'defaultId' => 'idea-to-paper-step-01-props-stem-default',
            'customId' => 'idea-to-paper-step-01-props-stem-custom',
            'prop' => 'stem-length="8rem"',
        ],
        'node' => [
            'defaultId' => 'idea-to-paper-step-01-props-node-default',
            'customId' => 'idea-to-paper-step-01-props-node-custom',
            'prop' => 'node-size="1.75rem"',
        ],
        'cap' => [
            'defaultId' => 'idea-to-paper-step-01-props-cap-default',
            'customId' => 'idea-to-paper-step-01-props-cap-custom',
            'prop' => 'cap-length="4rem"',
        ],
        'width' => [
            'defaultId' => 'idea-to-paper-step-01-props-width-default',
            'customId' => 'idea-to-paper-step-01-props-width-custom',
            'prop' => 'min-width="72rem"',
        ],
    ];

    foreach ($propExamples as $example) {
        expect($source)
            ->toContain($example['defaultId'])
            ->toContain($example['customId'])
            ->toContain('<span class="text-lime-300">' . $example['prop'] . '</span>');
    }

    expect($source)
        ->toContain('Default line-width comes from the graph defaults.')
        ->toContain('Default stem-length comes from the graph defaults.')
        ->toContain('Default node-size comes from the graph defaults.')
        ->toContain('Default cap-length comes from the graph defaults.')
        ->toContain('Default min-width follows the calculated graph bounds.')
        ->toContain('The trunk has no own props here; it inherits the canvas line width.')
        ->toContain('The trunk has no own props here; it inherits the canvas stem length.')
        ->toContain('The trunk has no own props here; it inherits the canvas node size.')
        ->toContain('The trunk has no own props here; it inherits the canvas cap length.')
        ->toContain('The trunk is unchanged; only the reserved canvas width grows.');
});

it('keeps idea to paper merge documentation split into the authored sub sections', function (): void {
    $indexSource = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.index',
    ));

    $sections = [
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.01-merge',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.02-merge-start',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.03-merge-extension',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.04-merge-aggregated',
    ];

    foreach ($sections as $section) {
        expect($indexSource)->toContain($section);
    }

    $mergeSource = file_get_contents(View::getFinder()->find($sections[0]));
    $startSource = file_get_contents(View::getFinder()->find($sections[1]));
    $extensionSource = file_get_contents(View::getFinder()->find($sections[2]));
    $aggregateSource = file_get_contents(View::getFinder()->find($sections[3]));

    expect($mergeSource)
        ->toContain('id="literature.left.1.source-note"')
        ->toContain('id="literature.right.1.source-note"')
        ->not->toContain('Archive finding')
        ->and($startSource)
        ->toContain('id="literature.left.1.archive-finding"')
        ->toContain("'text' => ['Archive finding', '1905-03-17']")
        ->not->toContain('extension-stem-continuations')
        ->and($extensionSource)
        ->toContain('id="literature.left.1.source-note"')
        ->toContain('id="literature.right.1.source-note"')
        ->toContain(':extension-count="1"')
        ->not->toContain('Aggregated origins')
        ->and($aggregateSource)
        ->toContain('id="literature.left.1.aggregated-sources"')
        ->toContain('id="literature.right.1.aggregated-sources"')
        ->toContain(':extension-stem-continuations')
        ->toContain("'Aggregated origins', '2 sources'");
});

it('keeps hand authored sample dev switches scoped to their own graph wrapper', function (): void {
    $samples = [
        'translation-workbench::pages.tw-graph.samples.bug-lifecycle' => [
            'graphId' => 'tw-graph-sample-bug-lifecycle',
            'devVariable' => 'bugLifecycleDev',
        ],
        'translation-workbench::pages.tw-graph.samples.order-lifecycle' => [
            'graphId' => 'tw-graph-sample-order-lifecycle',
            'devVariable' => 'orderLifecycleDev',
        ],
        'translation-workbench::pages.tw-graph.samples.project-roadmap' => [
            'graphId' => 'tw-graph-sample-project-roadmap',
            'devVariable' => 'projectRoadmapDev',
        ],
        'translation-workbench::pages.tw-graph.samples.translation-migration' => [
            'graphId' => 'tw-graph-sample-translation-migration',
            'devVariable' => 'translationMigrationDev',
        ],
    ];

    foreach ($samples as $view => $expected) {
        $source = file_get_contents(View::getFinder()->find($view));

        expect($source)
            ->toContain($expected['graphId'])
            ->toContain('x-data="{ ' . $expected['devVariable'] . ': @js($' . $expected['devVariable'] . ') }"')
            ->toContain("x-bind:class=\"{ 'tw-graph-protocol-dev-disabled': !" . $expected['devVariable'] . ' }"')
            ->toContain('x-bind:checked="' . $expected['devVariable'] . '"')
            ->toContain('x-on:click="' . $expected['devVariable'] . ' = !' . $expected['devVariable'] . '"')
            ->toContain(':dev="$' . $expected['devVariable'] . '"')
            ->not->toContain('halfLong right')
            ->not->toContain('long center')
            ->not->toContain('default right');
    }
});
