{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/05-strang-flow/_flow-preview-variants.blade.php --}}

@if ($renderMode === 'documentation')
    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="flowVariant === 'start'"
    >
@endif
@include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-start', ['sectionContent' => 'preview'])

@if ($renderMode === 'documentation')
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="flowVariant === 'step'"
    >
        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-step', ['sectionContent' => 'preview'])
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="flowVariant === 'decision'"
    >
        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-decision', ['sectionContent' => 'preview'])
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="flowVariant === 'branchSteps'"
    >
        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-branch-steps', ['sectionContent' => 'preview'])
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="flowVariant === 'if' && flowIfVariant === 'if'"
    >
        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-if-if', ['sectionContent' => 'preview'])
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="flowVariant === 'if' && flowIfVariant === 'ifEndif'"
    >
        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-if-endif', ['sectionContent' => 'preview'])
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="flowVariant === 'if' && flowIfVariant === 'ifElseEndif'"
    >
        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-if-else-endif', ['sectionContent' => 'preview'])
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="flowVariant === 'if' && flowIfVariant === 'ifElseifEndif'"
    >
        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-if-elseif-endif', ['sectionContent' => 'preview'])
    </div>

    <div
        class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
        x-show="flowVariant === 'if' && flowIfVariant === 'test'"
    >
        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.05-strang-flow.sections.flow-if-test', ['sectionContent' => 'preview'])
    </div>
@endif
