<?php

use Gunreip\TranslationWorkbench\Livewire\TwGraphOverview;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides;
use Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Symfony\Component\Process\Process;
use Tests\TestCase;

uses(TestCase::class);

it('lists the active Flow tabs and examples in their visible order with short keys', function () {
    $directory = dirname(OverviewLayoutOverrides::directory(), 2).'/flow';
    $tabs = function (string $file): array {
        $source = preg_replace('/\{\{--.*?--\}\}/s', '', file_get_contents($file));
        preg_match_all('/<flux:tab name="([^"]+)"[^>]*>\s*\{\{\s*__\(\x27(.*?)\x27\)\s*\}\}/s', $source, $matches, PREG_SET_ORDER);

        return array_column($matches, 2, 1);
    };
    $tree = OverviewStructure::data()['flowTabs'];
    $actualTabs = $tabs($directory.'/index.blade.php');
    expect(array_keys($tree['children']))->toBe(array_map(fn ($key) => substr($key, 5), array_keys($actualTabs)));
    foreach ($actualTabs as $name => $title) {
        $key = substr($name, 5);
        $group = $tree['children'][$key];
        expect($group['text'])->toBe(__($title));
        expect($group['id'])->toBe('literature.overview.flow.tabs.'.$key);
        expect(array_keys($group))->toBe(['id', 'text', 'children']);
        $file = is_file($directory.'/'.$key.'/index.blade.php')
            ? $directory.'/'.$key.'/index.blade.php' : $directory.'/'.$name.'.blade.php';
        $expected = [];
        foreach ($tabs($file) as $child => $label) {
            $short = str_starts_with($child, $name.'-') ? substr($child, strlen($name) + 1) : substr($child, 5);
            $expected[$short] = ['text' => __($label)];
        }
        expect($group['children'])->toBe($expected);
    }
});

it('accepts optional global blocks and validates overrides for every Flow topic and active example', function () {
    $authored = require OverviewLayoutOverrides::directory().'/flow.php';
    $children = $authored['flow']['children'];
    if (array_key_exists('global', $children)) {
        expect($children['global'])->toBeArray();
    }
    $tree = OverviewStructure::data()['flowTabs'];
    expect(array_keys(array_diff_key($children, ['global' => true])))->toBe(array_keys($tree['children']));
    foreach ($tree['children'] as $key => $group) {
        if (array_key_exists('global', $children[$key])) {
            expect($children[$key]['global'])->toBeArray();
        }
        if ($group['children'] !== []) {
            if (array_key_exists('global', $children[$key]['children'])) {
                expect($children[$key]['children']['global'])->toBeArray();
            }
            expect(array_keys(array_diff_key($children[$key]['children'], ['global' => true])))->toBe(array_keys($group['children']));
            foreach ($group['children'] as $leaf => $item) {
                if (array_key_exists('global', $children[$key]['children'][$leaf])) {
                    expect($children[$key]['children'][$leaf]['global'])->toBeArray();
                }
            }
        }
    }
    expect(OverviewLayoutOverrides::scope('flow.php', $authored)['issues'])->toBe([]);
});

it('mounts then lazily loads the complete overview within 128 MiB including request memory', function () {
    $script = <<<'PHP_SCRIPT'
        require 'vendor/autoload.php';
        $app = require 'bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        // Reserve room for the surrounding page, middleware and diagnostic tooling.
        // The former full-markup replacement exhausted 128 MiB under this load.
        $requestOverhead = str_repeat('x', 28 * 1024 * 1024);
        config(['app.debug' => true]);
        $parent = Livewire\Livewire::test(Gunreip\TranslationWorkbench\Livewire\TwGraphDocumentation::class, [
            'tabs' => ['main' => 'idea-to-paper-overview'],
        ]);
        if (str_contains($parent->html(), 'data-tw-graph-bounds-records') && str_contains($parent->html(), 'literature.overview.flow')) {
            throw new RuntimeException('Overview rendered during parent mount.');
        }
        unset($parent);
        $component = Livewire\Livewire::test(Gunreip\TranslationWorkbench\Livewire\TwGraphOverview::class);
        preg_match('/__lazyLoad\(\x27([^\x27]+)\x27\)/', html_entity_decode($component->html(), ENT_QUOTES), $lazy);
        if (! isset($lazy[1])) throw new RuntimeException('Missing native lazy-load request.');
        $component->call('__lazyLoad', $lazy[1]);
        $html = $component->html();
        echo json_encode([
            'flow' => str_contains($html, 'literature.overview.flow.tabs.async-await.retry-deadline'),
            'bounds' => str_contains($html, 'data-tw-graph-bounds-records'),
            'files' => str_contains($html, 'structure/flow.blade.php'),
            'calculated' => str_contains($html, 'data-tw-graph-calculated-marker'),
            'peak' => memory_get_peak_usage(true),
        ], JSON_THROW_ON_ERROR);
    PHP_SCRIPT;
    $process = new Process([PHP_BINARY, '-d', 'memory_limit=128M', '-r', $script], base_path(), [
        'SESSION_DRIVER' => 'array', 'CACHE_STORE' => 'array',
        'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => ':memory:', 'DB_URL' => '',
    ]);
    $process->setTimeout(90)->run();
    expect($process->isSuccessful())->toBeTrue($process->getErrorOutput().$process->getOutput());
    $result = json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR);
    expect($result)->toMatchArray(['flow' => true, 'bounds' => true, 'files' => true, 'calculated' => true]);
    expect($result['peak'])->toBeLessThan(128 * 1024 * 1024);
});

it('refreshes the overview independently while retaining its native lazy-load boundary', function () {
    $parentRenders = 0;
    $codeBoxRenders = 0;
    View::composer('translation-workbench::components.ui.tw-graph.code-box', function () use (&$codeBoxRenders) {
        $codeBoxRenders++;
    });
    $graphRenders = 0;
    View::composer('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.index', function () use (&$parentRenders) {
        $parentRenders++;
    });
    View::composer('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.overview-structure', function () use (&$graphRenders) {
        $graphRenders++;
    });
    $component = Livewire::test(TwGraphOverview::class);
    expect($graphRenders)->toBe(0);
    preg_match('/__lazyLoad\(\x27([^\x27]+)\x27\)/', html_entity_decode($component->html(), ENT_QUOTES), $lazy);
    expect($lazy)->toHaveKey(1);
    $component->call('__lazyLoad', $lazy[1])->assertSee('literature.overview.flow.tabs.async-await.retry-deadline', false);
    expect($graphRenders)->toBe(1);
    $dom = new DOMDocument;
    @$dom->loadHTML($component->html());
    $xpath = new DOMXPath($dom);
    $overlays = $xpath->query('//*[@data-tw-graph-overview]/*[@role="status"]');
    expect($overlays)->toHaveCount(1);
    expect($overlays->item(0)->getAttribute('style'))->toContain('display: none');
    expect($overlays->item(0)->hasAttribute('wire:loading.flex.delay'))->toBeTrue();
    expect($overlays->item(0)->getAttribute('wire:target'))->toBe('$refresh');
    $component->call('$refresh')->assertSee('literature.overview.flow.tabs.async-await.retry-deadline', false);
    expect($graphRenders)->toBe(2);
    expect($parentRenders)->toBe(0);
    expect($codeBoxRenders)->toBe(0);
    $component->assertDontSee('data-tw-graph-overview-loading', false);
});
