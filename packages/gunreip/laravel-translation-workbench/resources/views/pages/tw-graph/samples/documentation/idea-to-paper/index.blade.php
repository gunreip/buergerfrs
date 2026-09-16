{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/index.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
@endphp

<section class="mt-6 space-y-6">
    @isset($documentationTabs)
        <div wire:loading.delay role="status" class="text-sm text-zinc-500">
            {{ __('Loading example…') }}
        </div>
    @endisset
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
        <flux:tabs wire:model.live="tabs.main"
            scrollable
            scrollable:fade
            scrollable:scrollbar="hide"
        >
            <flux:tab name="idea-to-paper-canvas">
                {{ __('Canvas') }}
            </flux:tab>
            <flux:tab name="idea-to-paper-primitives">
                {{ __('Primitives') }}
            </flux:tab>
            <flux:tab name="idea-to-paper-segments">
                {{ __('Segments') }}
            </flux:tab>
            <flux:tab name="idea-to-paper-parts">
                {{ __('Parts') }}
            </flux:tab>
            <flux:tab name="idea-to-paper-paths">
                {{ __('Paths') }}
            </flux:tab>
            <flux:tab name="idea-to-paper-trunk">
                {{ __('Strang Trunk') }}
            </flux:tab>
            <flux:tab name="idea-to-paper-merge">
                {{ __('Strang Merge') }}
            </flux:tab>
            <flux:tab name="idea-to-paper-branch">
                {{ __('Strang Branch') }}
            </flux:tab>
            <flux:tab name="idea-to-paper-rekey">
                {{ __('Strang Rekey') }}
            </flux:tab>
            <flux:tab name="idea-to-paper-flow">
                {{ __('Flow') }}
            </flux:tab>
        </flux:tabs>

        <flux:tab.panel name="idea-to-paper-canvas">
            @if (!isset($documentationTabs) || $documentationTabs['main'] === 'idea-to-paper-canvas')
                <div wire:key="documentation-idea-to-paper-canvas">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.index', [
                            'dev' => $dev,
                            'coordinates' => $coordinates,
                        ])
                    </div>
                </div>
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="idea-to-paper-primitives">
            @if (!isset($documentationTabs) || $documentationTabs['main'] === 'idea-to-paper-primitives')
                <div wire:key="documentation-idea-to-paper-primitives">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.index', [
                            'dev' => $dev,
                            'coordinates' => $coordinates,
                        ])
                    </div>
                </div>
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="idea-to-paper-segments">
            @if (!isset($documentationTabs) || $documentationTabs['main'] === 'idea-to-paper-segments')
                <div wire:key="documentation-idea-to-paper-segments">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.index', [
                            'dev' => $dev,
                            'coordinates' => $coordinates,
                        ])
                    </div>
                </div>
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="idea-to-paper-parts">
            @if (!isset($documentationTabs) || $documentationTabs['main'] === 'idea-to-paper-parts')
                <div wire:key="documentation-idea-to-paper-parts">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.parts.index')
                    </div>
                </div>
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="idea-to-paper-paths">
            @if (!isset($documentationTabs) || $documentationTabs['main'] === 'idea-to-paper-paths')
                <div wire:key="documentation-idea-to-paper-paths">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.index', [
                            'dev' => $dev,
                            'coordinates' => $coordinates,
                        ])
                    </div>
                </div>
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="idea-to-paper-trunk">
            @if (!isset($documentationTabs) || $documentationTabs['main'] === 'idea-to-paper-trunk')
                <div wire:key="documentation-idea-to-paper-trunk">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.index', [
                            'dev' => $dev,
                            'coordinates' => $coordinates,
                        ])
                    </div>
                </div>
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="idea-to-paper-merge">
            @if (!isset($documentationTabs) || $documentationTabs['main'] === 'idea-to-paper-merge')
                <div wire:key="documentation-idea-to-paper-merge">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.index', [
                            'dev' => $dev,
                            'coordinates' => $coordinates,
                        ])
                    </div>
                </div>
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="idea-to-paper-branch">
            @if (!isset($documentationTabs) || $documentationTabs['main'] === 'idea-to-paper-branch')
                <div wire:key="documentation-idea-to-paper-branch">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.index', [
                            'dev' => $dev,
                            'coordinates' => $coordinates,
                        ])
                    </div>
                </div>
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="idea-to-paper-rekey">
            @if (!isset($documentationTabs) || $documentationTabs['main'] === 'idea-to-paper-rekey')
                <div wire:key="documentation-idea-to-paper-rekey">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.rekey.index', [
                            'dev' => $dev,
                            'coordinates' => $coordinates,
                        ])
                    </div>
                </div>
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="idea-to-paper-flow">
            @if (!isset($documentationTabs) || $documentationTabs['main'] === 'idea-to-paper-flow')
                <div wire:key="documentation-idea-to-paper-flow">
                    <div class="mt-4">
                        @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.index', [
                            'dev' => $dev,
                            'coordinates' => $coordinates,
                        ])
                    </div>
                </div>
            @endif
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
            {{ __('The draft tab preserves the original thought sketch. The result tab uses a separate copy that can be adjusted into the final graph without changing the draft. The flow diagram tab assembles the flow components as they are introduced above.') }}
        </flux:callout.text>

        <flux:tab.group class="mt-4 min-w-0 max-w-full">
            <flux:tabs wire:model.live="tabs.results"
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
                <flux:tab name="idea-to-paper-flow-result">
                    {{ __('Flow diagram') }}
                </flux:tab>
            </flux:tabs>

            <flux:tab.panel name="idea-to-paper-draft">
                @if (!isset($documentationTabs) || $documentationTabs['results'] === 'idea-to-paper-draft')
                    <div wire:key="documentation-idea-to-paper-draft">
                        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev" :coordinates="$coordinates">
                            <div
                                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                            >
                                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.00-graph-final', [
                                    'ideaToPaperGraphId' => 'tw-graph-sample-idea-to-paper-thought-draft',
                                    'ideaToPaperDev' => true,
                                    'ideaToPaperCoordinates' => true,
                                ])
                            </div>
                        </x-translation-workbench::ui.tw-graph.preview-tools>
                    </div>
                @endif
            </flux:tab.panel>

            <flux:tab.panel name="idea-to-paper-result">
                @if (!isset($documentationTabs) || $documentationTabs['results'] === 'idea-to-paper-result')
                    <div wire:key="documentation-idea-to-paper-result">
                        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev" :coordinates="$coordinates">
                            <div
                                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                            >
                                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper._graph-current-result', [
                                    'ideaToPaperGraphId' => 'tw-graph-sample-idea-to-paper-current-result',
                                    'ideaToPaperDev' => true,
                                    'ideaToPaperCoordinates' => true,
                                ])
                            </div>
                        </x-translation-workbench::ui.tw-graph.preview-tools>
                    </div>
                @endif
            </flux:tab.panel>

            <flux:tab.panel name="idea-to-paper-flow-result">
                @if (!isset($documentationTabs) || $documentationTabs['results'] === 'idea-to-paper-flow-result')
                    <div wire:key="documentation-idea-to-paper-flow-result">
                        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev" :coordinates="$coordinates">
                            <div
                                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                            >
                                @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper._graph-flow-diagram', [
                                    'ideaToPaperGraphId' => 'tw-graph-sample-idea-to-paper-flow-diagram',
                                    'ideaToPaperDev' => true,
                                    'ideaToPaperCoordinates' => true,
                                ])
                            </div>
                        </x-translation-workbench::ui.tw-graph.preview-tools>
                    </div>
                @endif
            </flux:tab.panel>
        </flux:tab.group>
    </flux:callout>
</section>
