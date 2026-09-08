{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/index.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
@endphp

<section class="mt-6 space-y-6">
    <flux:callout
        color="zinc"
        icon="file-text"
    >
        <flux:callout.heading>
            {{ __('Authoring story: from thought draft to visible graph') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('The finished graph is kept as a hidden reference. The visible documentation rebuilds it in smaller steps, so each visual decision can be explained and adjusted in place.') }}
        </flux:callout.text>
    </flux:callout>

    <flux:tab.group class="min-w-0 max-w-full">
        <flux:tabs
            scrollable
            scrollable:fade
            scrollable:scrollbar="hide"
        >
            <flux:tab name="idea-to-paper-canvas">
                {{ __('Canvas') }}
            </flux:tab>
            <flux:tab name="idea-to-paper-trunk">
                {{ __('Trunk') }}
            </flux:tab>
            <flux:tab name="idea-to-paper-merge">
                {{ __('Merge') }}
            </flux:tab>
            <flux:tab name="idea-to-paper-branch">
                {{ __('Branch') }}
            </flux:tab>
            <flux:tab name="idea-to-paper-rekey">
                {{ __('Rekey') }}
            </flux:tab>
        </flux:tabs>

        <flux:tab.panel name="idea-to-paper-canvas">
            <div class="mt-4">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-tw-graph.01-canvas', [
                    'dev' => $dev,
                    'coordinates' => $coordinates,
                ])
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="idea-to-paper-trunk">
            <div class="mt-4">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.01-strang-trunk.01-trunk', [
                    'dev' => $dev,
                    'coordinates' => $coordinates,
                ])
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="idea-to-paper-merge">
            <flux:tab.group class="mt-4 min-w-0 max-w-full">
                <flux:tabs
                    scrollable
                    scrollable:fade
                    scrollable:scrollbar="hide"
                >
                    <flux:tab name="idea-to-paper-merge-base">
                        {{ __('Default') }}
                    </flux:tab>
                    <flux:tab name="idea-to-paper-merge-start">
                        {{ __('Merge start') }}
                    </flux:tab>
                    <flux:tab name="idea-to-paper-merge-extension">
                        {{ __('Extension') }}
                    </flux:tab>
                    <flux:tab name="idea-to-paper-merge-aggregated">
                        {{ __('Aggregated') }}
                    </flux:tab>
                </flux:tabs>

                <flux:tab.panel name="idea-to-paper-merge-base">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.01-merge', [
                            'dev' => $dev,
                            'coordinates' => $coordinates,
                        ])
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="idea-to-paper-merge-start">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.02-merge-start', [
                            'dev' => $dev,
                            'coordinates' => $coordinates,
                        ])
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="idea-to-paper-merge-extension">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.03-merge-extension', [
                            'dev' => $dev,
                            'coordinates' => $coordinates,
                        ])
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="idea-to-paper-merge-aggregated">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.02-strang-merge.04-merge-aggregated', [
                            'dev' => $dev,
                            'coordinates' => $coordinates,
                        ])
                    </div>
                </flux:tab.panel>
            </flux:tab.group>
        </flux:tab.panel>

        <flux:tab.panel name="idea-to-paper-branch">
            <div class="mt-4">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch.01-branch', [
                    'dev' => $dev,
                    'coordinates' => $coordinates,
                ])
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="idea-to-paper-rekey">
            <div class="mt-4">
                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.04-strang-rekey.01-rekey', [
                    'dev' => $dev,
                    'coordinates' => $coordinates,
                ])
            </div>
        </flux:tab.panel>
    </flux:tab.group>

    <flux:callout
        color="zinc"
        icon="waypoints"
    >
        <flux:callout.heading>
            {{ __('Draft and current result') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('The draft tab preserves the original thought sketch. The result tab uses a separate copy that can be adjusted into the final graph without changing the draft.') }}
        </flux:callout.text>

        <flux:tab.group class="mt-4 min-w-0 max-w-full">
            <flux:tabs
                scrollable
                scrollable:fade
                scrollable:scrollbar="hide"
            >
                <flux:tab name="idea-to-paper-draft">
                    {{ __('Thought draft') }}
                </flux:tab>
                <flux:tab name="idea-to-paper-result">
                    {{ __('Current result') }}
                </flux:tab>
            </flux:tabs>

            <flux:tab.panel name="idea-to-paper-draft">
                <div
                    class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                >
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-graph-final', [
                        'ideaToPaperGraphId' => 'tw-graph-sample-idea-to-paper-thought-draft',
                        'ideaToPaperDev' => false,
                        'ideaToPaperCoordinates' => false,
                    ])
                </div>
            </flux:tab.panel>

            <flux:tab.panel name="idea-to-paper-result">
                <div
                    class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                >
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper._graph-current-result', [
                        'ideaToPaperGraphId' => 'tw-graph-sample-idea-to-paper-current-result',
                        'ideaToPaperDev' => false,
                        'ideaToPaperCoordinates' => false,
                    ])
                </div>
            </flux:tab.panel>
        </flux:tab.group>
    </flux:callout>
</section>
