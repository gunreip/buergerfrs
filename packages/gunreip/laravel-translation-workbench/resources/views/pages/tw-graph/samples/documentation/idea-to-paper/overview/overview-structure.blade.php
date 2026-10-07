<flux:callout
    class="min-w-0"
    color="emerald"
>
    <flux:callout.heading icon="eye">
        {{ __('Main tabs') . ' · ' . __('Data-driven example') }}
    </flux:callout.heading>
    <flux:callout.text class="mb-3">
        {{ __('Idea to Paper starts at the top. Merge paths and their extensions connect the main tabs on the left and right. Inventory and Overview end here; other tabs remain open. Strang and Parts branch below Deep Reference as sibling sub-tabs.') }}
    </flux:callout.text>
    <flux:callout.text class="mb-3">
        {{ __('The layout of the individual branches of this tw-graph is not necessarily optimal; rather, in addition to providing a visual structure for “Ideas To Paper,” it serves as a demonstration example to show how individual branches could be structured in different ways.') }}
    </flux:callout.text>
    <flux:callout.text class="mb-3">
        {{ __('This overview is a data-driven example: structure lives in OverviewStructure, common layout defaults in OverviewLayoutDefaults and authored adjustments in overview/data/*.php; the tab views render them through the existing TW-Graph components. Other documentation examples remain individually authored.') }}
    </flux:callout.text>
    <flux:callout.text class="mb-3">
        {{ __('Tab-specific layout overrides live in overview/data/*.php and are applied before rendering. Existing values and supported inherited layout fields may be overridden. Individual entries take precedence over their level defaults. Invalid or conflicting entries are reported below; conflicting entries retain the base value.') }}
    </flux:callout.text>
    <flux:callout.text class="mb-3">
        {{ __('This canvas explicitly enables dev-counter-auto. Visible DEV node counters are numbered consecutively in render order across all included files and restart for each canvas. Other canvases retain their authored counter values by default.') }}
    </flux:callout.text>
    <x-translation-workbench::ui.tw-graph.preview-tools
        :dev="$dev ?? true"
        :coordinates="$coordinates ?? false"
    >
        {{-- overview-structure-example:start --}}
        @php
            $layoutOverrides = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides::load();
            $structure = $layoutOverrides['data'];
        @endphp
        @if ($layoutOverrides['issues'] !== [])
            <flux:callout
                class="m-3"
                data-overview-layout-issues="true"
                color="amber"
                icon="exclamation-triangle"
            >
                <flux:callout.heading>{{ __('Layout override mismatch') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('Invalid or conflicting overrides were not applied. Review the files and key paths below.') }}
                </flux:callout.text>
                @foreach ($layoutOverrides['issues'] as $issue)
                    <div class="mt-2 text-sm">
                        <code class="break-all">{{ $issue['file'] }} · {{ $issue['path'] }}</code>
                        <p>{{ $issue['message'] }}</p>
                    </div>
                @endforeach
            </flux:callout>
        @endif
        <div
            class="mt-3 overflow-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
            data-overview-canvas-scroll
            style="max-height: 48rem;"
        >
            <div class="w-full min-w-0">
                <x-translation-workbench::ui.tw-graph
                    :graph-id="$structure['canvas']['graphId']"
                    :dev="true"
                    :dev-counter-auto="true"
                    :coordinates="true"
                    :min-width="$structure['canvas']['minWidth']"
                    :min-height="$structure['canvas']['minHeight']"
                    :horizontal-padding="$structure['canvas']['horizontalPadding']"
                >
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.inventory')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.overview')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.deep-reference')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.canvas')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.primitives')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.segments')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.parts')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.paths')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.strang-trunk')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.strang-merge')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.strang-branch')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.strang-rekey')
                    @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.flow')
                </x-translation-workbench::ui.tw-graph>
            </div>
        </div>
        {{-- overview-structure-example:end --}}
    </x-translation-workbench::ui.tw-graph.preview-tools>
    @php
        $sectionDescriptions = require dirname(
            \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutOverrides::directory(),
        ) . '/section-descriptions.php';
        $overviewOverrideFiles = collect($layoutOverrides['files'])->keyBy(fn($file) => basename($file, '.php'));
    @endphp
    <flux:accordion
        class="mt-4"
        transition
        exclusive
    >
        <flux:accordion.item>
            <flux:callout
                color="red"
                icon="file-code-corner"
            >
                {{-- Overview: Shared layout defaults --}}
                <flux:accordion.heading>
                    <flux:callout.heading class="grid grid-cols-3">
                        <span class="col-span-2">{{ __('Shared layout defaults') }}</span>
                        <code>OverviewLayoutDefaults.php</code>
                    </flux:callout.heading>
                    @if (filled($sectionDescriptions['layout-defaults'] ?? null))
                        <flux:callout.text class="col-span-3">
                            {{ $sectionDescriptions['layout-defaults'] }}
                        </flux:callout.text>
                    @endif
                </flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromClass(\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutDefaults::class)->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>

        {{-- Shared subtree renderer --}}
        <flux:accordion.item>
            <flux:callout
                color="pink"
                icon="share"
            >
                <flux:accordion.heading>
                    <flux:callout.heading class="grid grid-cols-3">
                        <span class="col-span-2">{{ __('Shared subtree renderer') }}</span>
                        <code>documentation-tree.blade.php</code>
                    </flux:callout.heading>
                    @if (filled($sectionDescriptions['subtree-renderer'] ?? null))
                        <flux:callout.text class="col-span-3">
                            {{ $sectionDescriptions['subtree-renderer'] }}
                        </flux:callout.text>
                    @endif
                </flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::components.ui.tw-graph.documentation-tree')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>

        <!-- Overview: Structure Data Class -->
        <flux:accordion.item>
            <flux:callout
                color="amber"
                icon="layout-grid-circles"
            >
                <flux:accordion.heading>
                    <flux:callout.heading class="grid grid-cols-3">
                        <span class="col-span-2">{{ __('Structure data') }}</span>
                        <code>OverviewStructure.php</code>
                    </flux:callout.heading>
                    @if (filled($sectionDescriptions['structure-data'] ?? null))
                        <flux:callout.text class="col-span-3">
                            {{ $sectionDescriptions['structure-data'] }}
                        </flux:callout.text>
                    @endif
                </flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromClass(\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure::class)->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>

        <!-- Overview Structure View Main -->
        <flux:accordion.item>
            <flux:callout
                color="violet"
                icon="git-fork"
            >
                <flux:accordion.heading>
                    <flux:callout.heading class="grid grid-cols-3">
                        <span>{{ __('Rendering components') }}</span>
                        <span>Orchestrator</span>
                        <code>overview-structure.blade.php</code>
                    </flux:callout.heading>
                    @if (filled($sectionDescriptions['structure']['orchestrator'] ?? null))
                        <flux:callout.text class="col-span-3">
                            {{ $sectionDescriptions['structure']['orchestrator'] }}
                        </flux:callout.text>
                    @endif
                </flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.overview-structure')->example('overview-structure-example') }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>

        <!-- Overview Root -->
        <flux:accordion.item>
            <flux:callout
                color="teal"
                icon="folder-root"
            >
                <flux:accordion.heading>
                    <flux:callout.heading class="grid grid-cols-3">
                        <span>{{ __('Rendering components') }}</span>
                        <span>Root</span>
                        <code>structure/root.blade.php</code>
                    </flux:callout.heading>
                    @if (filled($sectionDescriptions['structure']['root'] ?? null))
                        <flux:callout.text class="col-span-3">
                            {{ $sectionDescriptions['structure']['root'] }}
                        </flux:callout.text>
                    @endif
                </flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>

        <!-- Overview Main Tabs -->
        <x-translation-workbench::ui.common.overview-structure-layout
            section="main-tabs"
            :heading="__('Main Tabs')"
            :override-file="$overviewOverrideFiles['main-tabs'] ?? null"
            :structure-text="$sectionDescriptions['structure']['main-tabs'] ?? null"
            :data-text="$sectionDescriptions['data']['main-tabs'] ?? null"
        />

        <!-- Overview Inventory -->
        <x-translation-workbench::ui.common.overview-structure-layout
            section="inventory"
            :heading="__('Inventory')"
            :override-file="$overviewOverrideFiles['inventory'] ?? null"
            :structure-text="$sectionDescriptions['structure']['inventory'] ?? null"
            :data-text="$sectionDescriptions['data']['inventory'] ?? null"
        />

        <!-- Overview Overview -->
        <x-translation-workbench::ui.common.overview-structure-layout
            section="overview"
            :heading="__('Overview')"
            :override-file="$overviewOverrideFiles['overview'] ?? null"
            :structure-text="$sectionDescriptions['structure']['overview'] ?? null"
            :data-text="$sectionDescriptions['data']['overview'] ?? null"
        />

        <!-- Overview Deep Reference -->
        <x-translation-workbench::ui.common.overview-structure-layout
            section="deep-reference"
            :heading="__('Deep Reference')"
            :override-file="$overviewOverrideFiles['deep-reference'] ?? null"
            :structure-text="$sectionDescriptions['structure']['deep-reference'] ?? null"
            :data-text="$sectionDescriptions['data']['deep-reference'] ?? null"
        />

        {{-- Overview Canvas --}}
        <x-translation-workbench::ui.common.overview-structure-layout
            section="canvas"
            :heading="__('Canvas')"
            :override-file="$overviewOverrideFiles['canvas'] ?? null"
            :structure-text="$sectionDescriptions['structure']['canvas'] ?? null"
            :data-text="$sectionDescriptions['data']['canvas'] ?? null"
        />

        {{-- Overview Primitives --}}
        <x-translation-workbench::ui.common.overview-structure-layout
            section="primitives"
            :heading="__('Primitives')"
            :override-file="$overviewOverrideFiles['primitives'] ?? null"
            :structure-text="$sectionDescriptions['structure']['primitives'] ?? null"
            :data-text="$sectionDescriptions['data']['primitives'] ?? null"
        />

        {{-- Overview Segments --}}
        <x-translation-workbench::ui.common.overview-structure-layout
            section="segments"
            :heading="__('Segments')"
            :override-file="$overviewOverrideFiles['segments'] ?? null"
            :structure-text="$sectionDescriptions['structure']['segments'] ?? null"
            :data-text="$sectionDescriptions['data']['segments'] ?? null"
        />

        {{-- OverviewParts --}}
        <x-translation-workbench::ui.common.overview-structure-layout
            section="parts"
            :heading="__('Parts')"
            :override-file="$overviewOverrideFiles['parts'] ?? null"
            :structure-text="$sectionDescriptions['structure']['parts'] ?? null"
            :data-text="$sectionDescriptions['data']['parts'] ?? null"
        />

        {{-- Overview Paths --}}
        <x-translation-workbench::ui.common.overview-structure-layout
            section="paths"
            :heading="__('Paths')"
            :override-file="$overviewOverrideFiles['paths'] ?? null"
            :structure-text="$sectionDescriptions['structure']['paths'] ?? null"
            :data-text="$sectionDescriptions['data']['paths'] ?? null"
        />

        {{-- Overview Strang-trunk --}}
        <x-translation-workbench::ui.common.overview-structure-layout
            section="strang-trunk"
            :heading="__('Strang Trunk')"
            :override-file="$overviewOverrideFiles['strang-trunk'] ?? null"
            :structure-text="$sectionDescriptions['structure']['strang-trunk'] ?? null"
            :data-text="$sectionDescriptions['data']['strang-trunk'] ?? null"
        />

        {{-- Overview Strang-merge --}}
        <x-translation-workbench::ui.common.overview-structure-layout
            section="strang-merge"
            :heading="__('Strang Merge')"
            :override-file="$overviewOverrideFiles['strang-merge'] ?? null"
            :structure-text="$sectionDescriptions['structure']['strang-merge'] ?? null"
            :data-text="$sectionDescriptions['data']['strang-merge'] ?? null"
        />

        {{-- Overview Strang-branch --}}
        <x-translation-workbench::ui.common.overview-structure-layout
            section="strang-branch"
            :heading="__('Strang Branch')"
            :override-file="$overviewOverrideFiles['strang-branch'] ?? null"
            :structure-text="$sectionDescriptions['structure']['strang-branch'] ?? null"
            :data-text="$sectionDescriptions['data']['strang-branch'] ?? null"
        />

        {{-- Overview Strang-rekey --}}
        <x-translation-workbench::ui.common.overview-structure-layout
            section="strang-rekey"
            :heading="__('Strang Rekey')"
            :override-file="$overviewOverrideFiles['strang-rekey'] ?? null"
            :structure-text="$sectionDescriptions['structure']['strang-rekey'] ?? null"
            :data-text="$sectionDescriptions['data']['strang-rekey'] ?? null"
        />

        {{-- Overview Flow --}}
        <x-translation-workbench::ui.common.overview-structure-layout
            section="flow"
            :heading="__('Flow')"
            :override-file="$overviewOverrideFiles['flow'] ?? null"
            :structure-text="$sectionDescriptions['structure']['flow'] ?? null"
            :data-text="$sectionDescriptions['data']['flow'] ?? null"
        />
    </flux:accordion>
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/overview/overview-structure.blade.php"
        segments="3"
    />
</flux:callout>
