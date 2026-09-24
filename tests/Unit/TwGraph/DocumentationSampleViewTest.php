<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

uses(TestCase::class);

it('renders the idea to paper documentation index with top level tabs and result tabs', function (): void {
    $component = \Livewire\Livewire::test(\Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation::class);
    $html = $component->html();

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
        ->not->toContain('tw-graph-sample-idea-to-paper-current-result');

    $component->set('tabs.results', 'idea-to-paper-result')
        ->assertSee('tw-graph-sample-idea-to-paper-current-result', false)
        ->assertDontSee('tw-graph-sample-idea-to-paper-thought-draft', false);
});

it('renders idea to paper split branch and rekey documentation fragments', function (): void {
    $branchHtml = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.index', [
        'dev' => true,
        'coordinates' => false,
    ])->render();
    $rekeyHtml = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.rekey.index', [
        'dev' => true,
        'coordinates' => false,
    ])->render();

    expect($branchHtml)
        ->toContain('name="branch-default"')
        ->toContain('idea-to-paper-step-06-branch')
        ->toContain('literature.left.1.side-thought')
        ->toContain('literature.right.1.side-thought')
        ->and($rekeyHtml)
        ->toContain('name="rekey-default"')
        ->toContain('idea-to-paper-step-07-rekey')
        ->toContain('literature.left.source.1.old-title')
        ->toContain('literature.right.target.1.new-paper');
});

it('renders the idea to paper trunk documentation with the expected subtabs and examples', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.index', [
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

it('keeps trunk examples in independent files with matching source code boxes', function (): void {
    $prefix = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.';
    $index = file_get_contents(View::getFinder()->find($prefix . 'index'));
    expect($index)->not->toContain('trunkVariant')->not->toContain('<x-translation-workbench::ui.tw-graph.strang.trunk');
    $files = glob(dirname(View::getFinder()->find($prefix . 'index')) . '/trunk-*.blade.php');
    $examples = 0;
    foreach ($files as $file) {
        $source = file_get_contents($file);
        if (!str_contains($source, 'example-1:start')) {
            continue;
        }
        expect($source)->not->toContain('@foreach')->not->toContain('trunkVariant');
        $view = $prefix . basename($file, '.blade.php');
        $html = view($view, ['dev' => true, 'coordinates' => false])->render();
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $xpath = new DOMXPath($dom);
        preg_match_all('/^[ \t]*\{\{-- [a-z0-9-]+:start --\}\}\R(.*?)^[ \t]*\{\{-- [a-z0-9-]+:end --\}\}/ms', $source, $matches);
        expect($xpath->query('//pre/code')->length)->toBe(count($matches[1]));
        foreach ($matches[1] as $i => $code) {
            $lines = explode("\n", rtrim($code));
            $indent = min(array_map(fn ($line) => strlen($line) - strlen(ltrim($line)), array_filter($lines, fn ($line) => trim($line) !== '')));
            $expected = implode("\n", array_map(fn ($line) => substr($line, $indent), $lines));
            expect($xpath->query('//pre/code')->item($i)->textContent)->toBe($expected);
            $examples++;
        }
        expect($html)->toContain('x-model="previewDev"')->toContain('x-model="previewCoordinates"')->toContain('x-model="previewBoxes"');
    }
    expect($examples)->toBe(22);
    $default = file_get_contents(View::getFinder()->find($prefix . 'trunk-default'));
    preg_match('/<x-translation-workbench::ui.tw-graph.strang.trunk\b.*?\/>/s', $default, $component);
    expect($component[0])->toBe('<x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />');
});

it('renders the relocated trunk final without documentation wrappers in graph mode', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-final', [
        'renderMode' => 'graph', 'ideaToPaperGraphId' => 'relocated-trunk-final', 'dev' => false, 'coordinates' => false,
    ])->render();
    expect($html)->toContain('id="relocated-trunk-final"')->not->toContain('<pre')->not->toContain('previewDev');
});

it('renders the idea to paper canvas documentation with default and prop comparison examples', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.index', [
        'dev' => true,
        'coordinates' => false,
    ])->render();

    expect($html)
        ->toContain('data-tw-graph-preview-tools')
        ->toContain('x-model="previewDev"')
        ->toContain('x-model="previewBoxes"')
        ->toContain('x-model="previewCoordinates"')
        ->toContain('name="canvas-default"')
        ->toContain('name="canvas-default-trunk"')
        ->toContain('name="canvas-coordinates"')
        ->toContain('name="canvas-height"')
        ->toContain('name="canvas-props"')
        ->toContain('Default renders an empty graph canvas exactly as configured by the graph defaults.')
        ->toContain('Each drawing primitive declares its bounds')
        ->toContain('literature.center.1.paper')
        ->toContain('idea-to-paper-step-01-coordinates')
        ->toContain('idea-to-paper-step-01-content-height')
        ->toContain('idea-to-paper-step-01-props-line-custom')
        ->toContain('idea-to-paper-step-01-props-stem-custom');
});

it('keeps each canvas subtab handmade in its own file', function (): void {
    $directory = dirname(View::getFinder()->find('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.index'));
    foreach (['canvas-default', 'canvas-default-trunk', 'canvas-coordinates', 'canvas-height', 'canvas-props-line', 'canvas-props-stem-length', 'canvas-props-node-size', 'canvas-props-cap-length', 'canvas-props-min-width'] as $name) {
        $source = file_get_contents($directory . '/' . $name . '.blade.php');
        expect($source)
            ->toContain('<x-translation-workbench::ui.tw-graph.code-box')
            ->toContain('<x-translation-workbench::ui.tw-graph')
            ->toContain('<x-translation-workbench::ui.tw-graph.preview-tools')
            ->not->toContain('canvasVariant')
            ->not->toContain('canvasPropsVariant')
            ->not->toContain('@foreach')
            ->not->toContain('@include');
        expect($source)->toContain("{{ __('Prop') }}")->toContain("{{ __('Default') }}")->toContain("{{ __('Purpose') }}");
        $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.' . $name, ['dev' => false, 'coordinates' => false])->render();
        expect($html)->toContain('data-tw-graph-preview-tools')->toContain('tw-graph-protocol');
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        $xpath = new DOMXPath($dom);
        $callout = $xpath->query('//section/*[@data-flux-callout]')->item(0);
        expect($callout)->not->toBeNull($name);
        $code = $xpath->query('.//pre/code', $callout)->item(0);
        $table = $xpath->query('.//table', $callout)->item(0);
        expect($code)->not->toBeNull($name);
        expect($table)->not->toBeNull($name);
        expect($xpath->query('following::table', $code)->item(0)?->isSameNode($table))->toBeTrue($name);

    }
});

it('keeps idea to paper canvas default examples free of hidden visual overrides', function (): void {
    foreach (['canvas-default', 'canvas-default-trunk'] as $name) {
        $code = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
            'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.'.$name,
        )->example($name.'-1');
        expect($code)->not->toContain('stem-length=')->not->toContain('color=')
            ->not->toContain('min-height=')->not->toContain('min-width=')
            ->toContain(':dev="true"')->toContain(':coordinates="true"');
    }
});

it('keeps idea to paper canvas prop examples visually scoped and explicitly colored', function (): void {
    $directory = dirname(View::getFinder()->find('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.index'));
    $source = implode("\n", array_map('file_get_contents', glob($directory . '/canvas-props*.blade.php')));

    // Toolbar initial visibility is independent of the explicit canvas examples.
    $source = preg_replace('/<x-translation-workbench::ui\.tw-graph\.preview-tools\b[^>]*>/s', '', $source);

    expect($source)
        ->toContain('name="canvas-props-line"')
        ->toContain('name="canvas-props-stem-length"')
        ->toContain('name="canvas-props-node-size"')
        ->toContain('name="canvas-props-cap-length"')
        ->toContain('name="canvas-props-min-width"')
        ->toContain('line-width="0.5rem"')
        ->toContain('stem-length="8rem"')
        ->toContain('color="emerald"')
        ->toContain('min-height="88rem"')
        ->toContain('horizontal-padding="24rem"')
        ->not->toContain(':dev="$dev"')
        ->not->toContain(':coordinates="$coordinates"');
});

it('keeps canvas height examples focused on content and minimum height differences', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-height',
    ));

    expect($source)
        ->toContain('graph-id="idea-to-paper-step-01-content-height"')
        ->toContain('min-height="30rem"')
        ->toContain('canvas-height-1:start')
        ->toContain('graph-id="idea-to-paper-step-01-min-height"')
        ->toContain('min-height="88rem"')
        ->toContain('canvas-height-2:start')
        ->toContain('The first graph sets min-height below the calculated graph bounds')
        ->toContain('The second graph sets min-height above the calculated bounds');
});

it('renders the idea to paper merge documentation fragments with default extension and aggregate examples', function (): void {
    $defaultHtml = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-default', [
        'dev' => true,
        'coordinates' => false,
    ])->render();
    $startHtml = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-start', [
        'dev' => true,
        'coordinates' => false,
    ])->render();
    $extensionHtml = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-extension', [
        'dev' => true,
        'coordinates' => false,
    ])->render();
    $aggregateHtml = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-aggregated', [
        'dev' => true,
        'coordinates' => false,
    ])->render();

    expect($defaultHtml)
        ->toContain('Merge')
        ->toContain('idea-to-paper-step-05-merge')
        ->toContain('literature.left.1.source-note')
        ->toContain('literature.right.1.source-note')
        ->and($startHtml)
        ->toContain('Merge start')
        ->toContain('idea-to-paper-step-06-merge-start')
        ->toContain('Archive finding')
        ->toContain('Finding ID #42')
        ->and($extensionHtml)
        ->toContain('Merge extension')
        ->toContain('idea-to-paper-step-08-merge-extension')
        ->toContain('Second source')
        ->toContain('Second review')
        ->and($aggregateHtml)
        ->toContain('Merge aggregated')
        ->toContain('idea-to-paper-step-09-merge-aggregated')
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
        ->toContain("name=\"idea-to-paper-flow-result\"")
        ->toContain("idea-to-paper.00-graph-final")
        ->toContain("idea-to-paper._graph-current-result")
        ->toContain("idea-to-paper._graph-flow-diagram")
        ->toContain("'tw-graph-sample-idea-to-paper-thought-draft'")
        ->toContain("'tw-graph-sample-idea-to-paper-current-result'")
        ->toContain("'tw-graph-sample-idea-to-paper-flow-diagram'");
});

it('keeps the idea to paper flow diagram assembled separately from draft and current result', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper._graph-flow-diagram',
    ));

    expect($source)
        ->toContain('tw-graph-sample-idea-to-paper-flow-diagram')
        ->toContain('strang.flow-start')
        ->toContain('strang.flow-if-else')
        ->toContain('literature.flow.1.paper-process')
        ->toContain('literature.flow.1.paper-process.decision-1')
        ->toContain('literature.flow.1.paper-process.continue-step')
        ->toContain('Accepted path')
        ->toContain('Revision loop')
        ->not->toContain('tw-graph-sample-idea-to-paper-current-result')
        ->not->toContain('tw-graph-sample-idea-to-paper-thought-draft');
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
        ->toContain('idea-to-paper.canvas')
        ->toContain('strang.trunk.trunk-final')
        ->toContain('strang.merge.merge-final')
        ->toContain('strang.branch.branch-final')
        ->toContain('strang.rekey.rekey-final')
        ->not->toContain("idea-to-paper.00-graph-final")
        ->not->toContain("idea-to-paper._graph-current-result");
});

it('keeps idea to paper top level tabs delegated to one documentation section each', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.index',
    ));

    $sections = [
        'canvas.index',
        'strang.trunk.index',
        'strang.merge.index',
        'strang.branch.index',
        'strang.rekey.index',
        'flow.index',
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
        ->toContain('<flux:tab.panel name="idea-to-paper-flow">')
        ->not->toContain('subsub')
        ->not->toContain('path/to/file');
});

it('documents the current IF variants including a handmade nested block', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.index', [
        'dev' => true, 'coordinates' => false,
    ])->render();
    expect($html)->toContain('IF nested 9')
        ->toContain('literature.flow.1.if-nested-9.inner.elseif.sources.question')
        ->toContain('literature.flow.1.if-nested-9.outer.elseif.automatic.question')
        ->toContain('if-start.return')->toContain('anchorNode-return')
        ->toContain('x-model="previewDev"');
});

it('documents trunk stem lengths in its own handmade example', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-stem-lengths',
    ));
    expect($source)->toContain(':stem-count="4"')->toContain(':stem-lengths="[')
        ->toContain('Stem indexes name the rendered stem sections')->toContain('DEV node counters')
        ->not->toContain(':path-count')->not->toContain(':path-lengths');
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
                || str_contains($source, 'livewire:translation-workbench.tw-graph.documentation')
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
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.index',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.index',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-start',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-end',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-default',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-start',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-extension',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-aggregated',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.index',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.rekey.index',
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
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.branch-return',
    ));

    expect($source)
        ->toContain("'fallback' => false")
        ->toContain('branch-return-example-1:start')
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
                :stem-lengths="[1 => '4rem']"
                :node-labels="[
                    3 => [
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
        ->toContain('livewire:translation-workbench.tw-graph.documentation')
        ->toContain('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-graph-final');
});

it('keeps idea to paper canvas prop examples paired as default and custom previews', function (): void {
    $directory = dirname(View::getFinder()->find('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.index'));
    $source = implode("\n", array_map('file_get_contents', glob($directory . '/canvas-props*.blade.php')));

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
            ->toContain('' . $example['prop'] . '');
    }

    expect($source)
        ->toContain('Default line-width comes from the graph defaults.')
        ->toContain('Default stem-length comes from the graph defaults.')
        ->toContain('Default node-size comes from the graph defaults.')
        ->toContain('Default cap-length comes from the graph defaults.')
        ->toContain('Default min-width follows the calculated graph bounds.')
        ->toContain('The trunk has no own props here; it inherits the canvas line width.')
        ->toContain('The trunk has no own props here; it inherits the canvas stem length.')
        ->toContain('Both labeled anchors render dots; their size is inherited from the canvas.')
        ->toContain('The trunk has no own props here; it inherits the canvas cap length.')
        ->toContain('The trunk is unchanged; only the reserved canvas width grows.');
});

it('keeps idea to paper merge documentation split into the authored sub sections', function (): void {
    $indexSource = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.index',
    ));

    $sections = [
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-default',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-start',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-extension',
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-aggregated',
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

it('keeps left and right flow examples individually authored and editable', function (): void {
    $source = file_get_contents(View::getFinder()->find(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-elseif-multi',
    ));
    expect($source)->not->toContain('@foreach');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi'))->toBe(2);
    expect(substr_count($source, ':if-start='))->toBe(2);
    expect(substr_count($source, ':elseifs='))->toBe(2);
    expect($source)->toContain('side="left"')->toContain('side="right"');
});

it('renders nested IF code examples with intact PHP array arrows', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-test', [
    ])->render();
    $document = new DOMDocument;
    @$document->loadHTML($html);
    $code = (new DOMXPath($document))->query('//pre/code')->item(0)->textContent;

    expect($code)
        ->toContain('color="amber"')
        ->toContain("'width' => 'halfLong',")
        ->toContain("'align' => 'left'")
        ->not->toContain('= & gt;');
    expect($code)->toContain('side="left"');
});

it('joins opposite-facing nested IFs without overlapping their parent return rails', function (string $sample): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.' . $sample;
    $html = view($view)->render();
    $source = file_get_contents(View::getFinder()->find($view));
    expect($source)->not->toContain('@foreach')->not->toContain('@include');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi'))->toBe(4);
    $evaluate = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn ($value) => $evaluate->invoke(null, $value);
    foreach (['' => 1, '-right' => -1] as $suffix => $sign) {
        $get = fn ($id) => \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
            'idea-to-paper-step-08-' . $sample . $suffix, 'literature.flow.1.' . substr($sample, 5) . $suffix . '.' . $id,
        );
        foreach (['x', 'y'] as $axis) {
            expect($number($get('inner.anchorNode-start')[$axis]))->toBe($number($get('outer.elseif.deferred.true.anchorNode-end')[$axis]));
            expect($number($get('inner-return.stem.anchorNode-end')[$axis]))->toBe($number($get('outer.anchorNode-end')[$axis]));
        }
        // The child grows toward its parent, but remains beyond the return rail.
        expect($sign * ($number($get('inner.anchorNode-end')['x']) - $number($get('inner.anchorNode-start')['x'])))->toBeGreaterThan(0);
        expect($sign * ($number($get('outer.anchorNode-end')['x']) - $number($get('inner.anchorNode-end')['x'])))->toBeGreaterThan(5.5);
        expect($number($get('inner-return.anchorNode-end')['y']))->toBeLessThan($number($get('outer.anchorNode-end')['y']));
        expect($get('outer.anchorNode-end')['returnColor'])->toBe($get('inner.anchorNode-end')['returnColor']);
    }
})->with(['flow-if-nested-8']);

it('renders eight handmade primitive arcs with connected lines and reversed joint arrows', function (): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-arc';
    $source = file_get_contents(View::getFinder()->find($view));
    expect($source)->not->toContain('@foreach')->not->toContain('@include');
    expect($source)->not->toContain('&lt;x-translation-workbench::ui.tw-graph.primitives.arc');
    $html = view($view, ['dev' => false, 'coordinates' => false])->render();
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    $xpath = new DOMXPath($dom);
    $cells = $xpath->query('//*[@data-arc-example]');
    expect($cells->length)->toBe(8);
    $expected = [
        'west-north' => ['nw', 'top', 'right'],
        'east-north' => ['ne', 'top', 'left'],
        'south-west' => ['sw', 'left', 'top'],
        'south-east' => ['se', 'right', 'top'],
        'north-west' => ['nw', 'left', 'bottom'],
        'north-east' => ['ne', 'right', 'bottom'],
        'west-south' => ['sw', 'bottom', 'right'],
        'east-south' => ['se', 'bottom', 'left'],
    ];
    $property = static function (DOMElement $element, string $name): string {
        preg_match('/--tw-graph-protocol-' . preg_quote($name, '/') . ':\s*([^;]+)/', $element->getAttribute('style'), $matches);
        return trim($matches[1] ?? '');
    };
    foreach (array_keys($expected) as $index => $name) {
        $cell = $cells->item($index);
        expect($cell->getAttribute('data-arc-example'))->toBe($name);
        $prefix = 'literature.primitives.arc.' . $name;
        $arc = $xpath->query('.//*[@data-tw-graph-path="' . $prefix . '"]', $cell)->item(0);
        expect($arc->getAttribute('class'))->toContain('primitive-arc-' . $expected[$name][0]);
        $stem = $xpath->query('.//*[@data-tw-graph-path="' . $prefix . '.stem"]', $cell)->item(0);
        $bridge = $xpath->query('.//*[@data-tw-graph-path="' . $prefix . '.bridge"]', $cell)->item(0);
        expect($stem)->not->toBeNull();
        expect($bridge)->not->toBeNull();
        $lineEndpoints = [];
        foreach ([$stem, $bridge] as $line) {
            foreach (['start', 'end'] as $endpoint) {
                $lineEndpoints[] = [$property($line, $endpoint . '-x'), $property($line, $endpoint . '-y')];
            }
        }
        foreach (['start' => 1, 'end' => 2] as $endpoint => $directionIndex) {
            $arrow = $xpath->query('.//*[@data-tw-graph-path="' . $prefix . '.' . $endpoint . '.joint-arrow"]', $cell)->item(0);
            expect($arrow->getAttribute('class'))->toContain('primitive-joint-arrow-' . $expected[$name][$directionIndex]);
            expect($lineEndpoints)->toContain([$property($arrow, 'anchor-x'), $property($arrow, 'anchor-y')]);
        }
    }
});

it('renders handmade line examples with matching lengths endpoints and directions', function (): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-line';
    $source = file_get_contents(View::getFinder()->find($view));
    expect($source)->not->toContain('@foreach')->not->toContain('@include');
    expect($source)->toContain("\$lineSource->example('line-complete-example')");
    $html = view($view, ['dev' => false, 'coordinates' => false])->render();
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    $xpath = new DOMXPath($dom);
    $cells = $xpath->query('//*[@data-line-example]');
    expect($cells->length)->toBe(8);
    $property = static function (DOMElement $element, string $name): float {
        preg_match('/--tw-graph-protocol-' . preg_quote($name, '/') . ':\s*(-?[\d.]+)rem/', $element->getAttribute('style'), $matches);
        expect($matches)->toHaveCount(2);
        return (float) $matches[1];
    };
    $index = 0;
    foreach ([4, 8] as $length) {
        foreach (['bottom-top' => [0, 1, 'top'], 'top-bottom' => [0, -1, 'bottom'], 'left-right' => [1, 0, 'right'], 'right-left' => [-1, 0, 'left']] as $direction => [$dx, $dy, $arrowDirection]) {
            $name = $direction . '-' . $length;
            $cell = $cells->item($index++);
            expect($cell->getAttribute('data-line-example'))->toBe($name);
            $prefix = 'literature.primitives.line.' . $name;
            $line = $xpath->query('.//*[@data-tw-graph-path="' . $prefix . '"]', $cell)->item(0);
            $arrow = $xpath->query('.//*[@data-tw-graph-path="' . $prefix . '.end.joint-arrow"]', $cell)->item(0);
            expect($line->getAttribute('class'))->toContain('primitive-line-' . $direction);
            expect($arrow->getAttribute('class'))->toContain('primitive-joint-arrow-' . $arrowDirection);
            expect($property($line, 'local-length'))->toBe((float) $length);
            expect($property($line, 'end-x') - $property($line, 'start-x'))->toBe((float) ($dx * $length));
            expect($property($line, 'end-y') - $property($line, 'start-y'))->toBe((float) ($dy * $length));
            expect($property($arrow, 'anchor-x'))->toBe($property($line, 'end-x'));
            expect($property($arrow, 'anchor-y'))->toBe($property($line, 'end-y'));
        }
    }
});

it('renders all four text primitive widths with the same content and optional diagnostic boxes', function (): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-text-label';
    $source = file_get_contents(View::getFinder()->find($view));
    expect($source)->not->toContain('@foreach')->not->toContain('@include');
    expect($source)->not->toContain('&lt;x-translation-workbench::ui.tw-graph.primitives.text');
    $html = view($view, ['dev' => false, 'coordinates' => false])->render();
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//*[@data-tw-graph-path and contains(@class, "tw-graph-protocol-primitive-text-label")]')->length)->toBe(7);
    foreach (['half' => 'w-24', 'default' => 'w-48', 'half-long' => 'w-72', 'long' => 'w-96'] as $name => $widthClass) {
        $id = 'literature.primitives.text.' . $name;
        $label = $xpath->query('//*[@data-tw-graph-path="' . $id . '"]')->item(0);
        expect($label)->not->toBeNull();
        expect($label->getAttribute('style'))->toContain('--tw-graph-protocol-anchor-x: 0rem');
        expect($label->textContent)->toContain('Text label')->toContain('Two lines of information');
        expect($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " ' . $widthClass . ' ")]', $label)->length)->toBe(1);
        expect($xpath->query('.//*[@data-tw-graph-dev-box="' . $id . '.dev-box"]', $label)->length)->toBe(1);
    }
    expect($html)->toContain('x-model="previewDev"')->toContain('x-model="previewBoxes"')->toContain('x-model="previewCoordinates"');
});

it('renders handmade marker and connector primitive examples', function (string $kind, int $count): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-' . $kind;
    $source = file_get_contents(View::getFinder()->find($view));
    expect($source)->not->toContain('@foreach')->not->toContain('@include');
    $html = view($view, ['dev' => false, 'coordinates' => false])->render();
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//*[@data-primitive-example]')->length)->toBe($count);
    $code = $xpath->query('//pre/code')->item(0)->textContent;
    expect($code)->toStartWith('<x-translation-workbench::ui.tw-graph' . "\n    graph-id=")
        ->toContain("\n    <x-translation-workbench::ui.tw-graph.primitives." . $kind)
        ->toContain("\n        id=");
    expect($html)->toContain('x-model="previewDev"')->toContain('x-model="previewBoxes"')->toContain('x-model="previewCoordinates"');
    if ($kind === 'node') {
        foreach (['small' => '0.5rem', 'medium' => '1.5rem', 'large' => '2rem'] as $name => $size) {
            $node = $xpath->query('//*[@data-tw-graph-path="literature.primitives.node.' . $name . '"]')->item(0);
            expect($node->getAttribute('style'))->toContain('--tw-graph-protocol-local-node-size: ' . $size);
        }
    } elseif ($kind === 'joint-arrow') {
        foreach (['right', 'left', 'top', 'bottom'] as $direction) {
            foreach (['default', 'large'] as $size) {
                $arrow = $xpath->query('//*[@data-tw-graph-path="literature.primitives.joint-arrow.' . $direction . '-' . $size . '"]')->item(0);
                expect($arrow->getAttribute('class'))->toContain('primitive-joint-arrow-' . $direction);
            }
            $canvas = $dom->getElementById('idea-to-paper-primitives-joint-arrow-' . $direction . '-large');
            expect($canvas->getAttribute('style'))->toContain('--tw-graph-protocol-node-size: 1.75rem');
        }
    } else {
        foreach (['right', 'left', 'top', 'bottom'] as $placement) {
            foreach ([2 => '0.25rem', 4 => '0.75rem'] as $length => $gap) {
                $prefix = 'literature.primitives.connector.' . $placement . '-' . $length;
                $connector = $xpath->query('//*[@data-tw-graph-path="' . $prefix . '"]')->item(0);
                expect($connector->getAttribute('class'))->toContain('primitive-connector-' . $placement);
                expect($connector->getAttribute('style'))->toContain('--tw-graph-protocol-connector-length: ' . $length . 'rem')->toContain('--tw-graph-protocol-connector-anchor-gap: ' . $gap);
                expect($xpath->query('//*[@data-tw-graph-path="' . $prefix . '.anchor"]')->length)->toBe(1);
            }
        }
    }
})->with([['node', 4], ['joint-arrow', 8], ['connector', 8]]);

it('renders the handmade trunk path example through its segment chain', function (): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-trunk';
    $source = file_get_contents(View::getFinder()->find($view));
    $previewSource = preg_replace('/<pre[^>]*>.*?<\/pre>/s', '', $source);
    expect(substr_count($previewSource, '<x-translation-workbench::ui.tw-graph.paths.trunk'))->toBe(2);
    expect($source)->not->toContain('ui.tw-graph.primitives.')->not->toContain('@foreach')->not->toContain('@include');
    $html = view($view, ['dev' => false, 'coordinates' => false])->render();
    expect($html)->toContain('Path start')->toContain('Path end')->toContain('Checkpoint')
        ->toContain('data-tw-graph-dev-box="literature.paths.trunk.dev-box"')
        ->toContain('x-model="previewDev"')->toContain('x-model="previewBoxes"')->toContain('x-model="previewCoordinates"');
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    $xpath = new DOMXPath($dom);
    $code = $xpath->query('//pre/code')->item(0)->textContent;
    expect($code)->toContain("['x' => '0rem', 'y' => '5rem']")
        ->toContain("'length' => '4rem'")
        ->not->toContain('= & gt;');

    preg_match('/^[ \t]*\{\{-- trunk-example:start --\}\}\R(.*?)^[ \t]*\{\{-- trunk-example:end --\}\}/ms', $source, $example);
    $expectedCode = preg_replace('/^ {24}/m', '', rtrim($example[1]));
    expect($code)->toBe($expectedCode);
    expect($xpath->query('//pre/code/*[not(self::span[@class="tw-graph-code-comment" or @class="tw-graph-code-component"])]')->length)->toBe(0);

    foreach ([1 => '3rem', 2 => '4rem', 3 => '5rem'] as $index => $length) {
        $line = $xpath->query('//*[@data-tw-graph-path="literature.paths.trunk.stem' . $index . '"]')->item(0);
        expect($line)->not->toBeNull();
        expect($line->getAttribute('style'))->toContain('--tw-graph-protocol-local-length: ' . $length);
    }
    expect($xpath->query('//*[@data-tw-graph-path="literature.paths.trunk.stem4"]')->length)->toBe(0);
    $downCode = $xpath->query('//pre/code')->item(1)->textContent;
    expect($downCode)->toContain('direction="top-bottom"')->toContain("'y' => '22rem'");
    preg_match('/^[ \t]*\{\{-- trunk-top-bottom-example:start --\}\}\R(.*?)^[ \t]*\{\{-- trunk-top-bottom-example:end --\}\}/ms', $source, $downExample);
    expect($downCode)->toBe(preg_replace('/^ {24}/m', '', rtrim($downExample[1])));
    foreach ([1 => '3rem', 2 => '4rem', 3 => '5rem'] as $index => $length) {
        $line = $xpath->query('//*[@data-tw-graph-path="literature.paths.trunk-top-bottom.stem' . $index . '"]')->item(0);
        expect($line)->not->toBeNull();
        expect($line->getAttribute('class'))->toContain('primitive-line-top-bottom');
        expect($line->getAttribute('style'))->toContain('--tw-graph-protocol-local-length: ' . $length);
    }

});

it('renders relocated merge examples from their own source and retains the graph-only final', function (): void {
    $prefix = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.';
    foreach (['merge-default', 'merge-start', 'merge-mismatch', 'merge-extension', 'merge-aggregated'] as $name) {
        $source = file_get_contents(View::getFinder()->find($prefix . $name));
        expect($source)->not->toContain('@foreach')->not->toContain('@include');
        preg_match('/^[ \t]*\{\{-- ' . $name . '-example-1:start --\}\}\R(.*?)^[ \t]*\{\{-- ' . $name . '-example-1:end --\}\}/ms', $source, $match);
        $lines = explode("\n", rtrim($match[1]));
        $indent = min(array_map(fn ($line) => strlen($line) - strlen(ltrim($line)), array_filter($lines, fn ($line) => trim($line) !== '')));
        $expected = implode("\n", array_map(fn ($line) => substr($line, $indent), $lines));
        $html = view($prefix . $name, ['dev' => true, 'coordinates' => false])->render();
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $xpath = new DOMXPath($dom);
        expect($xpath->query('//pre/code')->length)->toBe(1);
        expect($xpath->query('//pre/code')->item(0)->textContent)->toBe($expected);
        expect($html)->toContain('x-model="previewDev"')->toContain('x-model="previewBoxes"')->toContain('x-model="previewCoordinates"');
    }
    $final = view($prefix . 'merge-final', ['ideaToPaperGraphId' => 'relocated-merge-final', 'dev' => false, 'coordinates' => false])->render();
    expect($final)->toContain('id="relocated-merge-final"')->not->toContain('<pre')->not->toContain('previewDev');
});

it('keeps reorganized branch rekey and flow examples self contained with matching source code', function (string $section, array $names): void {
    $prefix = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.' . $section . '.';
    $navigation = file_get_contents(View::getFinder()->find($prefix . 'index'));
    expect($navigation)->not->toContain('x-show')->not->toContain('x-on:click');
    foreach ($names as $name) {
        $source = file_get_contents(View::getFinder()->find($prefix . $name));
        expect($source)->not->toContain('@foreach')->not->toContain('@include')->not->toContain('sectionContent');
        preg_match('/^[ \t]*\{\{-- ' . $name . '-example-1:start --\}\}\R(.*?)^[ \t]*\{\{-- ' . $name . '-example-1:end --\}\}/ms', $source, $match);
        $lines = explode("\n", rtrim($match[1]));
        $indent = min(array_map(fn ($line) => strlen($line) - strlen(ltrim($line)), array_filter($lines, fn ($line) => trim($line) !== '')));
        $expected = implode("\n", array_map(fn ($line) => substr($line, $indent), $lines));
        $html = view($prefix . $name, ['dev' => true, 'coordinates' => false])->render();
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $xpath = new DOMXPath($dom);
        expect($xpath->query('//pre/code')->length)->toBe(in_array($name, ['flow-if-else', 'flow-if-nested-1', 'flow-if-nested-2', 'flow-if-nested-3', 'flow-if-nested-test'], true) ? 2 : 1);
        expect($xpath->query('//pre/code')->item(0)->textContent)->toBe($expected);
        if (in_array($name, ['flow-if-else', 'flow-if-nested-1', 'flow-if-nested-2', 'flow-if-nested-3', 'flow-if-nested-test'], true)) {
            preg_match('/^[ \t]*\{\{-- ' . $name . '-example-2:start --\}\}\R(.*?)^[ \t]*\{\{-- ' . $name . '-example-2:end --\}\}/ms', $source, $secondMatch);
            $secondLines = explode("\n", rtrim($secondMatch[1]));
            $secondIndent = min(array_map(fn ($line) => strlen($line) - strlen(ltrim($line)), array_filter($secondLines, fn ($line) => trim($line) !== '')));
            $secondExpected = implode("\n", array_map(fn ($line) => substr($line, $secondIndent), $secondLines));
            expect($xpath->query('//pre/code')->item(1)->textContent)->toBe($secondExpected);
            expect($html)->toContain('id="idea-to-paper-step-08-' . $name . '-right"');
        }
        expect($html)->toContain('x-model="previewDev"')->toContain('x-model="previewBoxes"')->toContain('x-model="previewCoordinates"');
        foreach ($xpath->query('//pre') as $pre) {
            $pre->parentNode->removeChild($pre);
        }
        expect($dom->textContent)->not->toContain('{{--')->not->toContain('{--');
    }
})->with([
    ['strang.branch', ['branch-default', 'branch-offset', 'branch-step', 'branch-continuation', 'branch-return', 'branch-mismatch']],
    ['strang.rekey', ['rekey-default', 'rekey-source', 'rekey-target', 'rekey-compressed']],
    ['flow', ['flow-start', 'flow-step', 'flow-branch-steps']],
    ['flow.if', ['flow-if-else', 'flow-if-nested-1', 'flow-if-nested-2', 'flow-if-nested-3', 'flow-if-nested-test']],
]);

it('preserves graph-only branch and rekey finals for the master', function (string $kind): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.' . $kind . '.' . $kind . '-final', [
        'ideaToPaperGraphId' => 'relocated-' . $kind . '-final', 'dev' => false, 'coordinates' => false,
    ])->render();
    expect($html)->toContain('id="relocated-' . $kind . '-final"')->not->toContain('<pre')->not->toContain('previewDev');
})->with(['branch', 'rekey']);

it('renders both ternary previews as graphs rather than literal component source', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-ternary', [
        'dev' => true, 'coordinates' => false,
    ])->render();
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    $xpath = new DOMXPath($dom);
    foreach (['', '-right'] as $suffix) {
        $preview = $xpath->query('//*[@id="idea-to-paper-step-08-flow-if-ternary' . $suffix . '"]')->item(0);
        expect($preview)->not->toBeNull();
        $prefix = 'literature.flow.1.ternary-process' . $suffix . '.decision-1';
        foreach (['.question.label', '.true.bridge1.label.center.1', '.false.bridge1.label.center.1'] as $label) {
            expect($xpath->query('.//*[@data-tw-graph-path="' . $prefix . $label . '"]', $preview)->length)->toBe(1);
        }
        expect($preview->textContent)->toContain("'Unknown'", '$name')
            ->not->toContain('badgeColor', ':if-end', '=>');
        expect($xpath->query('.//*[starts-with(name(), "x-translation-workbench")]', $preview)->length)->toBe(0);
    }
});

it('connects both independently authored nested examples to their own outer return rails', function (): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-1';
    $source = file_get_contents(View::getFinder()->find($view));
    expect($source)->not->toContain('@foreach');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi'))->toBe(4);
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $evaluate = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn (string $value): float => $evaluate->invoke(null, $value);
    foreach (['' => -1, '-right' => 1] as $suffix => $sign) {
        $graph = 'idea-to-paper-step-08-flow-if-nested-1' . $suffix;
        $prefix = 'literature.flow.1.if-nested-1' . $suffix;
        $get = fn (string $id) => \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($graph, $prefix . '.' . $id);
        foreach (['x', 'y'] as $axis) {
            expect($number($get('inner.anchorNode-start')[$axis]))->toBe($number($get('outer.if.true.anchorNode-end')[$axis]));
            expect($number($get('inner-return.stem.anchorNode-end')[$axis]))->toBe($number($get('outer.if.true.anchorNode-return')[$axis]));
        }
        expect(($number($get('inner.anchorNode-end')['x']) - $number($get('inner.anchorNode-start')['x'])) * $sign)->toBeGreaterThan(0.0);
        expect(($number($get('inner.anchorNode-end')['x']) - $number($get('inner-return.anchorNode-end')['x'])) * $sign)->toBeGreaterThan(0.0);
        expect($number($get('outer.if.true.anchorNode-return')['y']) - $number($get('inner-return.anchorNode-end')['y']))->toBeGreaterThanOrEqual(0.0);
        expect($get('outer.anchorNode-end')['returnColor'])->toBe($get('inner.anchorNode-end')['returnColor']);
        expect($xpath->query('//*[@data-tw-graph-path="' . $prefix . '.outer.if.true.stem"]')->length)->toBe(0);
    }
});

it('connects both saved ELSEIF nested examples to their own outer return rails', function (): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-2';
    $source = file_get_contents(View::getFinder()->find($view));
    expect($source)->not->toContain('@foreach');
    expect(substr_count($source, '<x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi'))->toBe(4);
    $html = view($view)->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $evaluate = new ReflectionMethod(\Gunreip\TranslationWorkbench\Support\TwGraph\BoundsRegistry::class, 'evaluateRemExpression');
    $number = fn (string $value): float => $evaluate->invoke(null, $value);
    foreach (['' => -1, '-right' => 1] as $suffix => $sign) {
        $graph = 'idea-to-paper-step-08-flow-if-nested-2' . $suffix;
        $prefix = 'literature.flow.1.if-nested-2' . $suffix;
        $get = fn (string $id) => \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get($graph, $prefix . '.' . $id);
        foreach (['x', 'y'] as $axis) {
            expect($number($get('inner.anchorNode-start')[$axis]))->toBe($number($get('outer.elseif.sources.true.anchorNode-end')[$axis]));
            expect($number($get('inner-return.stem.anchorNode-end')[$axis]))->toBe($number($get('outer.elseif.sources.true.anchorNode-return')[$axis]));
        }
        expect(($number($get('inner.anchorNode-end')['x']) - $number($get('inner.anchorNode-start')['x'])) * $sign)->toBeGreaterThan(0.0);
        expect(($number($get('inner.anchorNode-end')['x']) - $number($get('inner-return.anchorNode-end')['x'])) * $sign)->toBeGreaterThan(0.0);
        expect($number($get('outer.elseif.sources.true.anchorNode-return')['y']) - $number($get('inner-return.anchorNode-end')['y']))->toBeGreaterThanOrEqual(0.0);
        expect($get('outer.anchorNode-end')['returnColor'])->toBe($get('inner.anchorNode-end')['returnColor']);
        $owner = $suffix === '' ? '.outer.elseif.sources.true.bridge1.bridge-out' : '.outer.elseif.automatic.true.stem';
        $target = $suffix === '' ? '.outer.elseif.automatic.true.stem' : '.outer.elseif.sources.true.bridge1.bridge-out';
        $jump = $xpath->query('//*[@data-tw-graph-path="' . $prefix . $owner . '"]')->item(0);
        $config = json_decode($jump->getAttribute('data-tw-graph-line-jumps'), true, flags: JSON_THROW_ON_ERROR);
        expect($config[0]['over'])->toBe($prefix . $target);
        expect($config[0]['side'])->toBe($suffix === '' ? 'top' : 'right');
        expect($xpath->query('//*[@data-tw-graph-path="' . $prefix . '.outer.elseif.sources.true.stem"]')->length)->toBe(0);
    }
});

it('demonstrates line endpoint combinations without stacking a Dot and arrow at the same endpoint', function (): void {
    $html = view('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-line')->render();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    expect($xpath->query('//*[@data-line-endpoints]')->length)->toBe(8);
    foreach ([
        'plain' => [false, false, false, false],
        'arrow-start' => [false, false, true, false],
        'arrows-both' => [false, false, true, true],
        'dot-start' => [true, false, false, false],
        'dot-end' => [false, true, false, false],
        'dots-both' => [true, true, false, false],
        'dot-arrow' => [true, false, false, true],
        'arrow-dot' => [false, true, true, false],
    ] as $key => [$dotStart, $dotEnd, $arrowStart, $arrowEnd]) {
        $cell = $xpath->query('//*[@data-line-endpoints="'.$key.'"]')->item(0);
        $id = 'literature.primitives.line.endpoints.'.$key;
        $line = $xpath->query('.//*[@data-tw-graph-path="'.$id.'"]', $cell)->item(0);
        expect($line)->not->toBeNull();
        expect(str_contains($line->getAttribute('class'), 'primitive-line-node-start'))->toBe($dotStart);
        expect(str_contains($line->getAttribute('class'), 'primitive-line-node-end'))->toBe($dotEnd);
        foreach (['start' => [$arrowStart, '2rem'], 'end' => [$arrowEnd, '6rem']] as $end => [$visible, $y]) {
            $arrow = $xpath->query('.//*[@data-tw-graph-path="'.$id.'.'.$end.'.joint-arrow"]', $cell)->item(0);
            expect($arrow !== null)->toBe($visible);
            if ($visible) {
                expect($arrow->getAttribute('class'))->toContain('primitive-joint-arrow-top');
                expect($arrow->getAttribute('style'))->toContain('--tw-graph-protocol-anchor-y: '.$y.';');
            }
        }
        expect($cell->textContent)->not->toContain('{--', ':node-start=');
    }
});

it('shows the actual authored Canvas preview source in every code box', function (string $name): void {
    $view = 'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.'.$name;
    $source = file_get_contents(View::getFinder()->find($view));
    preg_match_all("/\\\$canvasSource->example\\('([^']+)'\\)/", $source, $markers);
    $examples = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView($view);
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">'.view($view)->render());
    $boxes = (new DOMXPath($dom))->query('//pre/code');
    expect($boxes->length)->toBe(count($markers[1]));
    foreach ($markers[1] as $index => $marker) {
        expect($boxes->item($index)->textContent)->toBe($examples->example($marker));
    }
})->with([
    'canvas-default', 'canvas-default-trunk', 'canvas-borders', 'canvas-coordinates', 'canvas-height',
    'canvas-props-cap-length', 'canvas-props-line', 'canvas-props-min-width',
    'canvas-props-node-size', 'canvas-props-stem-length',
]);
