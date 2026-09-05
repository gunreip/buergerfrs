{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/project-roadmap/index.blade.php --}}

<section class="mt-8 space-y-4">
    <div class="space-y-1">
        <h2 class="text-base font-semibold text-zinc-950 dark:text-zinc-50">
            {{ __('Project Roadmap: authoring notes') }}
        </h2>
        <p class="max-w-4xl text-sm leading-6 text-zinc-600 dark:text-zinc-300">
            {{ __('These notes document the hand-authored graph above in small sections. Each section maps visible roadmap meaning back to the concrete tw-graph component chain used in the sample.') }}
        </p>
    </div>

    @include('translation-workbench::pages.tw-graph.samples.documentation.project-roadmap.01-overview')
    @include('translation-workbench::pages.tw-graph.samples.documentation.project-roadmap.02-graph-wrapper')
    @include('translation-workbench::pages.tw-graph.samples.documentation.project-roadmap.02-trunk')
    @include('translation-workbench::pages.tw-graph.samples.documentation.project-roadmap.03-branches')
    @include('translation-workbench::pages.tw-graph.samples.documentation.project-roadmap.04-release-and-end')
</section>
