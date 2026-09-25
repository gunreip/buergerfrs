<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

uses(TestCase::class);

it('uses only canvas diagnostics throughout every segment', function (string $component, string $configuration, bool $enabled): void {
    $opposite = $enabled ? 'false' : 'true';
    $html = Blade::render(<<<BLADE
        <x-translation-workbench::ui.tw-graph graph-id="diagnostics-contract" :dev="\$enabled" :coordinates="\$enabled">
            <x-translation-workbench::ui.tw-graph.segments.{$component}
                {$configuration} :dev="{$opposite}" :coordinates="{$opposite}"
            />
        </x-translation-workbench::ui.tw-graph>
        BLADE, compact('enabled'));
    expect(str_contains($html, 'data-tw-graph-dev-box='))->toBe($enabled);
    expect($html)->toContain('data-tw-graph-dev="'.($enabled ? 'true' : 'false').'"');
    expect(str_contains($html, 'tw-graph-protocol-coordinates-disabled'))->toBe(! $enabled);
})->with([
    'path' => ['path', 'id="diagnostic.path" :node-start="true"'],
    'arc' => ['arc', ':segment="[\'id\' => \'diagnostic.arc\', \'dev\' => true]"'],
    'start' => ['start', ':segment="[\'id\' => \'diagnostic.start\', \'startLabel\' => [\'text\' => \'Start\'], \'dev\' => true]"'],
    'end' => ['end', ':segment="[\'id\' => \'diagnostic.end\', \'endLabel\' => [\'text\' => \'End\'], \'dev\' => true]"'],
    'step' => ['step', ':segment="[\'id\' => \'diagnostic.step\', \'stepLabel\' => [\'text\' => \'Step\'], \'dev\' => true]"'],
    'compressed' => ['stem-compressed', ':segment="[\'id\' => \'diagnostic.compressed\', \'dev\' => true]"'],
    'label' => ['label', 'id="diagnostic.label" :label="[\'text\' => \'Label\']"'],
    'label bridge' => ['label-bridge', 'id="diagnostic.label-bridge" :label="[\'text\' => \'Label\']"'],
    'fusion' => ['fusion', 'id="diagnostic.fusion" direction="left-right" :anchor-start="[\'x\' => \'0rem\', \'y\' => \'0rem\']" :anchor-end="[\'x\' => \'8rem\', \'y\' => \'6rem\']"'],
])->with([true, false]);

it('keeps diagnostics isolated between nested and successive canvases', function (): void {
    $template = <<<'BLADE'
        <x-translation-workbench::ui.tw-graph graph-id="outer" :dev="true" :coordinates="true">
            <x-translation-workbench::ui.tw-graph.segments.path id="outer.before" />
            <x-translation-workbench::ui.tw-graph graph-id="inner" :dev="false" :coordinates="true">
                <x-translation-workbench::ui.tw-graph.segments.path id="inner.path" />
            </x-translation-workbench::ui.tw-graph>
            <x-translation-workbench::ui.tw-graph.segments.path id="outer.after" />
        </x-translation-workbench::ui.tw-graph>
        <x-translation-workbench::ui.tw-graph graph-id="next">
            <x-translation-workbench::ui.tw-graph.segments.path id="next.path" />
        </x-translation-workbench::ui.tw-graph>
        BLADE;
    foreach ([1, 2] as $render) {
        $html = Blade::render($template);
        expect($html)->toContain('data-tw-graph-dev-box="outer.before.dev-box"', 'data-tw-graph-dev-box="outer.after.dev-box"')
            ->not->toContain('data-tw-graph-dev-box="inner.path.dev-box"', 'data-tw-graph-dev-box="next.path.dev-box"');
    }
});

it('keeps diagnostic props out of child APIs and authored documentation calls', function (): void {
    $views = base_path('packages/gunreip/laravel-translation-workbench/resources/views');
    $violations = [];
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($views));
    foreach ($files as $file) {
        $path = $file->getPathname();
        if (! $file->isFile() || ! str_ends_with($path, '.blade.php') || str_contains($path, '/_old/')) {
            continue;
        }
        $source = file_get_contents($path);
        if (str_contains($path, '/components/ui/tw-graph/') && ! str_ends_with($path, '/preview-tools.blade.php')) {
            preg_match_all('/@props\(\[(.*?)\]\)/s', $source, $props);
            foreach ($props[1] as $definition) {
                if (preg_match('/[\'"](?:dev|devMode|coordinates)[\'"]\s*=>/', $definition)) {
                    $violations[] = $path.': local diagnostic prop';
                }
            }
        }
        if (! str_contains($path, '/pages/tw-graph/samples/documentation/idea-to-paper/')) {
            continue;
        }
        preg_match_all('/<x-translation-workbench::ui\.tw-graph\.[\w.-]+\b(?:[^\'">]|"[^"]*"|\'[^\']*\')*>/s', $source, $tags);
        foreach ($tags[0] as $tag) {
            if (! str_starts_with($tag, '<x-translation-workbench::ui.tw-graph.preview-tools') && preg_match('/\s:?(?:dev|dev-mode|coordinates)=/', $tag)) {
                $violations[] = $path.': local diagnostic setting in preview';
            }
        }
    }
    expect($violations)->toBe([]);
});
