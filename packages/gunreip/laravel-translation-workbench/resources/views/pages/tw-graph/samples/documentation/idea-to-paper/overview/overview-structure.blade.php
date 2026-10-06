<flux:callout
    class="min-w-0"
    color="emerald"
>
    <flux:callout.heading icon="eye">{{ __('Main tabs') . ' · ' . __('Data-driven example') }}</flux:callout.heading>
    <flux:callout.text class="mb-3">
        {{ __('Idea to Paper starts at the top. Merge paths and their extensions connect the main tabs on the left and right. Inventory and Overview end here; other tabs remain open. Strang and Parts branch below Deep Reference as sibling sub-tabs.') }}
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
            <flux:callout color="amber" icon="exclamation-triangle" class="m-3" data-overview-layout-issues="true">
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
            style="max-height: 48rem;"
            data-overview-canvas-scroll
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
    {{-- Overview code boxes paused: retain source until an on-demand presentation is implemented.
    <flux:accordion
        class="mt-4"
        transition
        exclusive
    >
        <flux:accordion.item>
            <flux:callout color="indigo" icon="code">
                <flux:accordion.heading>{{ __('Shared layout defaults') }} · OverviewLayoutDefaults.php</flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromClass(\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewLayoutDefaults::class)->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item>
            <flux:callout color="indigo" icon="code">
                <flux:accordion.heading>{{ __('Shared subtree renderer') }} · documentation-tree.blade.php</flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::components.ui.tw-graph.documentation-tree')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <!-- Overview Structure Class -->
        <flux:accordion.item>
            <flux:callout
                color="indigo"
                icon="code"
            >
                <flux:accordion.heading>{{ __('Structure data') }} · Class · OverviewStructure.php
                </flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromClass(\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\OverviewStructure::class)->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        @foreach ($layoutOverrides['files'] as $overrideFile)
            <flux:accordion.item>
                <flux:callout color="indigo" icon="code">
                    <flux:accordion.heading>{{ __('Layout overrides') }} · data/{{ basename($overrideFile) }}</flux:accordion.heading>
                </flux:callout>
                <flux:accordion.content>
                    <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromFile($overrideFile)->source() }}</x-translation-workbench::ui.tw-graph.code-box>
                </flux:accordion.content>
            </flux:accordion.item>
        @endforeach
        <!-- Overview Structure View Main -->
        <flux:accordion.item>
            <flux:callout
                color="indigo"
                icon="code"
            >
                <flux:accordion.heading>{{ __('Rendering components') }} · Orchestrator · overview-structure.blade.php
                </flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.overview-structure')->example('overview-structure-example') }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <!-- Overview Root -->
        <flux:accordion.item>
            <flux:callout
                color="indigo"
                icon="code"
            >
                <flux:accordion.heading>{{ __('Rendering components') }} · Root · structure/root.blade.php
                </flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.root')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <!-- Overview Main Tabs -->
        <flux:accordion.item>
            <flux:callout
                color="indigo"
                icon="code"
            >
                <flux:accordion.heading>{{ __('Rendering components') }} · Main Tabs · structure/main-tabs.blade.php
                </flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.main-tabs')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <!-- Overview Inventory -->
        <flux:accordion.item>
            <flux:callout
                color="indigo"
                icon="code"
            >
                <flux:accordion.heading>{{ __('Rendering components') }} · Inventory · structure/inventory.blade.php
                </flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.inventory')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <!-- Overview Overview -->
        <flux:accordion.item>
            <flux:callout
                color="indigo"
                icon="code"
            >
                <flux:accordion.heading>{{ __('Rendering components') }} · Overview · structure/overview.blade.php
                </flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.overview')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <!-- Deep Reference -->
        <flux:accordion.item>
            <flux:callout
                color="indigo"
                icon="code"
            >
                <flux:accordion.heading>{{ __('Rendering components') }} · Deep Reference ·
                    structure/deep-reference.blade.php
                </flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.deep-reference')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item>
            <flux:callout color="indigo" icon="code">
                <flux:accordion.heading>{{ __('Rendering components') }} · structure/canvas.blade.php</flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.canvas')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item>
            <flux:callout color="indigo" icon="code">
                <flux:accordion.heading>{{ __('Rendering components') }} · structure/primitives.blade.php</flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.primitives')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item>
            <flux:callout color="indigo" icon="code">
                <flux:accordion.heading>{{ __('Rendering components') }} · structure/segments.blade.php</flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.segments')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item>
            <flux:callout color="indigo" icon="code">
                <flux:accordion.heading>{{ __('Rendering components') }} · structure/parts.blade.php</flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.parts')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item>
            <flux:callout color="indigo" icon="code">
                <flux:accordion.heading>{{ __('Rendering components') }} · structure/paths.blade.php</flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.paths')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item>
            <flux:callout color="indigo" icon="code">
                <flux:accordion.heading>{{ __('Rendering components') }} · structure/strang-trunk.blade.php</flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.strang-trunk')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item>
            <flux:callout color="indigo" icon="code">
                <flux:accordion.heading>{{ __('Rendering components') }} · structure/strang-merge.blade.php</flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.strang-merge')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item>
            <flux:callout color="indigo" icon="code">
                <flux:accordion.heading>{{ __('Rendering components') }} · structure/strang-branch.blade.php</flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.strang-branch')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item>
            <flux:callout color="indigo" icon="code">
                <flux:accordion.heading>{{ __('Rendering components') }} · structure/strang-rekey.blade.php</flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.strang-rekey')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item>
            <flux:callout color="indigo" icon="code">
                <flux:accordion.heading>{{ __('Rendering components') }} · structure/flow.blade.php</flux:accordion.heading>
            </flux:callout>
            <flux:accordion.content>
                <x-translation-workbench::ui.tw-graph.code-box>{{ \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.overview.structure.flow')->source() }}</x-translation-workbench::ui.tw-graph.code-box>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
    --}}
    <x-translation-workbench::ui.common.tw-graph-path-file
        path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/overview/overview-structure.blade.php"
        segments="3"
    />
</flux:callout>
