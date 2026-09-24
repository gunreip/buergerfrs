<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">

    {{-- CodeBox And Props Table --}}
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Path segment') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Four individually authored path segments with the same length in all four directions. Each combines a line with anchor dots, an end label and connector, and DEV diagnostics. Set both anchors to match direction and length: unlike a complete path, this segment expects its endpoint coordinates explicitly. Canvas y coordinates increase upwards.') }}
        </flux:callout.text>
        @php
            $pathExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-path',
            );
            $pathBottomTopCode = $pathExampleSource->example('segment-path-bottom-top');
            $pathTopBottomCode = $pathExampleSource->example('segment-path-top-bottom');
            $pathLeftRightCode = $pathExampleSource->example('segment-path-left-right');
            $pathRightLeftCode = $pathExampleSource->example('segment-path-right-left');
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >bottom-top</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $pathBottomTopCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >top-bottom</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $pathTopBottomCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >left-right</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $pathLeftRightCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >right-left</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $pathRightLeftCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Path segment props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            {{-- Table --}}
            <flux:table container:class="max-h-80">
                <flux:table.columns
                    class="dark:bg-zinc-900"
                    sticky
                >
                    <flux:table.column>{{ __('Prop') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">id
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            segment.path
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Identifier for the path segment and its child elements.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">direction
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">bottom-top
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('bottom-top, top-bottom, left-right, or right-left.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">length
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">4rem
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Length of the line; must match the distance between the anchors.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">:anchor-start
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">[&#x27;x&#x27;
                            =&gt;
                            &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Explicit starting coordinates.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">:anchor-end
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">[&#x27;x&#x27;
                            =&gt;
                            &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;4rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Explicit endpoint; changing direction or length does not recalculate it.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">:node-start
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">false
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Show the start node with true, or provide up to two label entries in an array.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">:node-end
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">false
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Show the end node with true, or provide up to two label entries in an array. Label entries accept text, width, align, and side; horizontal lines use top/bottom, vertical lines use left/right.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">color
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">zinc
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Line, node, and label color.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">:dashed
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">false
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Draw a dashed line.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">:gradient
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">false
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Fade the line along its direction.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">to-color
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">null
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Target color for a color gradient.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">:color-gradient
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">false
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Enable the gradient between color and to-color.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">:cap
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">false
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Enable the end cap unless cap-end explicitly overrides it.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">:cap-start
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">false
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Show a cap at the starting anchor.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">:cap-end
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">null
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Override the end-cap setting.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">cap-length
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">1.25rem
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Length of the cap.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">tone
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">line
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Palette tone used for the line and nodes.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">z-index
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">null
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Optional stacking-order override.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">:dev
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">null
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Enable diagnostic boxes and node counters. Falls back to segment.dev, then false.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    {{-- Row --}}
                    <flux:table.row>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">:segment
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">[]
                        </flux:table.cell>
                        <flux:table.cell class="wrap-break-words whitespace-normal align-top text-sm">
                            {{ __('Alternative complete segment configuration. A non-empty array replaces the individual geometry and styling props; dev can still override segment.dev.') }}
                        </flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    {{-- Preview --}}
    <flux:callout
        class="min-w-0"
        color="emerald"
        icon="eye"
    >
        <flux:callout.heading>{{ __('Path segment preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2">
                <div class="min-w-0">
                    <flux:heading size="sm">bottom-top</flux:heading>
                    <div
                        class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-path-bottom-top:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-path-bottom-top"
                            :dev="true"
                            :coordinates="true"
                            min-height="18rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.path
                                id="literature.segments.path.bottom-top"
                                direction="bottom-top"
                                length="8rem"
                                :anchor-start="['x' => '0rem', 'y' => '4rem']"
                                :anchor-end="['x' => '0rem', 'y' => '12rem']"
                                :node-start="true"
                                :node-end="[['text' => ['End'], 'width' => 'half', 'align' => 'center']]"
                                color="cyan"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-path-bottom-top:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">top-bottom</flux:heading>
                    <div
                        class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-path-top-bottom:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-path-top-bottom"
                            :dev="true"
                            :coordinates="true"
                            min-height="18rem"
                            min-width="24rem"
                            horizontal-padding="10rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.path
                                id="literature.segments.path.top-bottom"
                                direction="top-bottom"
                                length="8rem"
                                :anchor-start="['x' => '0rem', 'y' => '12rem']"
                                :anchor-end="['x' => '0rem', 'y' => '4rem']"
                                :node-start="true"
                                :node-end="[['text' => ['End'], 'width' => 'half', 'align' => 'center']]"
                                color="emerald"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-path-top-bottom:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">left-right</flux:heading>
                    <div
                        class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-path-left-right:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-path-left-right"
                            :dev="true"
                            :coordinates="true"
                            min-height="18rem"
                            min-width="24rem"
                            horizontal-padding="5rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.path
                                id="literature.segments.path.left-right"
                                direction="left-right"
                                length="8rem"
                                :anchor-start="['x' => '-4rem', 'y' => '8rem']"
                                :anchor-end="['x' => '4rem', 'y' => '8rem']"
                                :node-start="true"
                                :node-end="[['text' => ['End'], 'width' => 'half', 'align' => 'center']]"
                                color="fuchsia"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-path-left-right:end --}}
                    </div>
                </div>
                <div class="min-w-0">
                    <flux:heading size="sm">right-left</flux:heading>
                    <div
                        class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- segment-path-right-left:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-segments-path-right-left"
                            :dev="true"
                            :coordinates="true"
                            min-height="18rem"
                            min-width="24rem"
                            horizontal-padding="5rem"
                        >
                            <x-translation-workbench::ui.tw-graph.segments.path
                                id="literature.segments.path.right-left"
                                direction="right-left"
                                length="8rem"
                                :anchor-start="['x' => '4rem', 'y' => '8rem']"
                                :anchor-end="['x' => '-4rem', 'y' => '8rem']"
                                :node-start="true"
                                :node-end="[['text' => ['End'], 'width' => 'half', 'align' => 'center']]"
                                color="amber"
                                :dev="true"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- segment-path-right-left:end --}}
                    </div>
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/segments/segments-path.blade.php
        </flux:field>
    </flux:callout>
</section>
