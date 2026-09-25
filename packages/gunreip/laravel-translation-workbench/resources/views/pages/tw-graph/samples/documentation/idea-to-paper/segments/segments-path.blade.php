<x-translation-workbench::ui.common.heading-counter-group group="segments-path">
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
            @endphp
            <flux:separator
                class="mt-4"
                :text="__('Code examples')"
            />
            <flux:accordion
                transition
                exclusive
            >
                <flux:accordion.item expanded>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-top"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >bottom-top</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $pathExampleSource->example('segment-path-bottom-top') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-bottom"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >top-bottom</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $pathExampleSource->example('segment-path-top-bottom') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-right"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >left-right</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $pathExampleSource->example('segment-path-left-right') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-left"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >right-left</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $pathExampleSource->example('segment-path-right-left') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Path segment props') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    {{-- Table --}}
                    <flux:table container:class="max-h-80">
                        <flux:table.columns
                            class="bg-white dark:bg-zinc-900"
                            sticky
                        >
                            <flux:table.column>{{ __('Prop') }}</flux:table.column>
                            <flux:table.column>{{ __('Default') }}</flux:table.column>
                            <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>segment.path</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the path segment and its child elements.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bottom-top</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('bottom-top, top-bottom, left-right, or right-left.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length of the line; must match the distance between the anchors.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:anchor-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt;
                                        &#x27;0rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicit starting coordinates.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:anchor-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt;
                                        &#x27;4rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicit endpoint; changing direction or length does not recalculate it.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:node-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Show the start node with true, or provide up to two label entries in an array.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:node-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Show the end node with true, or provide up to two label entries in an array. Label entries accept text, width, align, and side; horizontal lines use top/bottom, vertical lines use left/right.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Line, node, and label color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:dashed</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Draw a dashed line.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:gradient</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Fade the line along its direction.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>to-color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Target color for a color gradient.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:color-gradient</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Enable the gradient between color and to-color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:cap</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Enable the end cap unless cap-end explicitly overrides it.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:cap-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Show a cap at the starting anchor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:cap-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Override the end-cap setting.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cap-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>1.25rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length of the cap.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>tone</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>line</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Palette tone used for the line and nodes.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>z-index</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional stacking-order override.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}

                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:segment</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Alternative complete segment configuration. A non-empty array replaces the individual geometry and styling props. DEV and coordinates are controlled by the canvas.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Path segment preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2">
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom-top"
                            size="sm"
                        >
                            {{ __('bottom-top') }}
                        </x-translation-workbench::ui.common.heading-counter>
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
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-path-bottom-top:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="top-bottom"
                            size="sm"
                        >
                            {{ __('top-bottom') }}
                        </x-translation-workbench::ui.common.heading-counter>
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
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-path-top-bottom:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-right"
                            size="sm"
                        >
                            {{ __('left-right') }}
                        </x-translation-workbench::ui.common.heading-counter>
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
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-path-left-right:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-left"
                            size="sm"
                        >
                            {{ __('right-left') }}
                        </x-translation-workbench::ui.common.heading-counter>
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
</x-translation-workbench::ui.common.heading-counter-group>
