<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

uses(TestCase::class);

it('registers tw graph documentation data driven and sample routes against existing views', function (): void {
    $routes = [
        'admin.tw-graph.documentation' => [
            'uri' => 'admin/tw-graph/documentation',
            'view' => 'translation-workbench::pages.tw-graph.documentation',
        ],
        'admin.tw-graph.diagnostics' => [
            'uri' => 'admin/tw-graph/diagnostics',
            'view' => 'translation-workbench::pages.tw-graph.diagnostics',
        ],
        'admin.tw-graph.data-driven.datasets' => [
            'uri' => 'admin/tw-graph/data-driven/datasets',
            'view' => 'translation-workbench::pages.tw-graph.data-driven.datasets',
        ],
        'admin.tw-graph.samples.resume-a-einstein' => [
            'uri' => 'admin/tw-graph/samples/resume-a-einstein',
            'view' => 'translation-workbench::pages.tw-graph.samples.resume-a-einstein',
        ],
        'admin.tw-graph.samples.bug-lifecycle' => [
            'uri' => 'admin/tw-graph/samples/bug-lifecycle',
            'view' => 'translation-workbench::pages.tw-graph.samples.bug-lifecycle',
        ],
        'admin.tw-graph.samples.order-lifecycle' => [
            'uri' => 'admin/tw-graph/samples/order-lifecycle',
            'view' => 'translation-workbench::pages.tw-graph.samples.order-lifecycle',
        ],
        'admin.tw-graph.samples.project-roadmap' => [
            'uri' => 'admin/tw-graph/samples/project-roadmap',
            'view' => 'translation-workbench::pages.tw-graph.samples.project-roadmap',
        ],
        'admin.tw-graph.samples.translation-migration' => [
            'uri' => 'admin/tw-graph/samples/translation-migration',
            'view' => 'translation-workbench::pages.tw-graph.samples.translation-migration',
        ],
        'admin.tw-graph.samples.idea-to-paper' => [
            'uri' => 'admin/tw-graph/samples/idea-to-paper',
            'view' => 'translation-workbench::pages.tw-graph.samples.idea-to-paper',
        ],
    ];

    foreach ($routes as $routeName => $expected) {
        $route = Route::getRoutes()->getByName($routeName);

        expect($route)->not->toBeNull()
            ->and($route->uri())->toBe($expected['uri'])
            ->and($route->methods())->toContain('GET')
            ->and(View::exists($expected['view']))->toBeTrue();
    }
});

it('lists the tw graph navigation entries in the administration sidebar source', function (): void {
    $source = file_get_contents(resource_path('views/layouts/app/sidebar/⚡administration.blade.php'));

    expect($source)
        ->toContain(":heading=\"__('Data Driven')\"")
        ->toContain(":heading=\"__('Samples')\"")
        ->toContain("route('admin.tw-graph.documentation')")
        ->toContain("route('admin.tw-graph.diagnostics')")
        ->toContain("route('admin.tw-graph.data-driven.datasets')")
        ->toContain("route('admin.tw-graph.samples.resume-a-einstein')")
        ->toContain("route('admin.tw-graph.samples.bug-lifecycle')")
        ->toContain("route('admin.tw-graph.samples.order-lifecycle')")
        ->toContain("route('admin.tw-graph.samples.project-roadmap')")
        ->toContain("route('admin.tw-graph.samples.translation-migration')")
        ->toContain("route('admin.tw-graph.samples.idea-to-paper')")
        ->toContain("request()->routeIs('admin.tw-graph.*')")
        ->toContain("request()->routeIs('admin.tw-graph.data-driven.*')")
        ->toContain("request()->routeIs('admin.tw-graph.samples.*')")
        ->toContain("icon=\"table-cells\"")
        ->toContain("icon=\"clipboard-check\"")
        ->toContain("icon=\"bug\"")
        ->toContain("icon=\"package-check\"")
        ->toContain("icon=\"map\"")
        ->toContain("icon=\"git-branch\"")
        ->toContain("{{ __('Datasets') }}")
        ->toContain("{{ __('Diagnostics') }}")
        ->toContain("{{ __('Resume A. Einstein') }}")
        ->toContain("{{ __('Bug Lifecycle') }}")
        ->toContain("{{ __('Order Lifecycle') }}")
        ->toContain("{{ __('Project Roadmap') }}")
        ->toContain("{{ __('Translation Migration') }}")
        ->toContain("{{ __('Idea To Paper') }}");
});
