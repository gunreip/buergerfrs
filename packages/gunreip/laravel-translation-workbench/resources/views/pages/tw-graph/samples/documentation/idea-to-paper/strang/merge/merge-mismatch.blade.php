<x-translation-workbench::ui.common.heading-counter-group group="strang-merge-mismatch">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Merge mismatch') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.documentation-links example="strang.merge.merge-mismatch" />
            <flux:callout.text>
                {{ __('A merge can only render node labels for existing label anchors. If numeric node-label keys exceed the available merge anchors, DEV mode reports nodeLabel-Mismatch. If a numeric end label and the explicit end alias are both set, end wins and DEV mode reports nodeLabel-EndOverride.') }}
            </flux:callout.text>
            @php
                $mergeExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.merge.merge-mismatch',
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
                            example="merge-mismatch-example-1"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Merge mismatch') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $mergeExampleSource->example('merge-mismatch-example-1') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Merge props') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
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
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>auto id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Stable identifier for the strand, its nodes and registered anchors.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Registered target anchor. Resolves the merge position from the target and route dimensions.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:anchor-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt;
                                        &#x27;0rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Manual starting coordinates when no attach-to target is used.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inherited / zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Strand color inherited from the graph unless explicitly set.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Horizontal distance between the two arcs.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Fallback length of the main vertical stem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:node-labels</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Labels for concrete merge anchors. Use named text, width, align and color options; end labels the final anchor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:stem-lengths</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Defines the available stem sections and their lengths.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>start-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>resolved arc size</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length of the initial section.') }}
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
            <flux:callout.heading icon="eye">{{ __('Preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4">
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="merge-mismatch-example-1"
                            size="sm"
                        >{{ __('Merge mismatch') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- merge-mismatch-example-1:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-step-07-merge-mismatch"
                                :dev="true"
                                :coordinates="true"
                                color="amber"
                                arc-radius="2.75rem"
                                bridge-length="18rem"
                                stem-length="5rem"
                                horizontal-padding="6rem"
                                min-width="42rem"
                                min-height="24rem"
                            >
                                <div class="pointer-events-none opacity-25">
                                    <x-translation-workbench::ui.tw-graph.strang.trunk
                                        id="literature.center.1.merge-mismatch-reference"
                                        color="zinc"
                                        :stem-count="3"
                                        start-length="7rem"
                                        :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem']"
                                        end-length="3rem"
                                        :start-label="[
                                            'text' => ['reference trunk'],
                                            'width' => 'default',
                                            'align' => 'center',
                                        ]"
                                    />
                                </div>

                                <x-translation-workbench::ui.tw-graph.strang.merge-left
                                    id="literature.left.1.label-mismatch"
                                    attach-to="strang.trunk.node.2"
                                    bridge-length="12rem"
                                    start-length="3rem"
                                    :stem-lengths="[1 => '4rem']"
                                    :node-labels="[
                                        1 => [
                                            'right' => [
                                                'text' => ['Valid label', 'anchor 1'],
                                                'width' => 'default',
                                                'align' => 'left',
                                            ],
                                        ],
                                        'end' => [
                                            'left' => [
                                                'text' => ['Valid end label', 'explicit end'],
                                                'width' => 'halfLong',
                                                'align' => 'right',
                                            ],
                                        ],
                                        4 => [
                                            'right' => [
                                                'text' => ['Ignored label', 'anchor 4 missing'],
                                                'width' => 'default',
                                                'align' => 'left',
                                            ],
                                        ],
                                        5 => [
                                            'left' => [
                                                'text' => ['Ignored label', 'anchor 5 missing'],
                                                'width' => 'default',
                                                'align' => 'right',
                                            ],
                                        ],
                                    ]"
                                />

                                <x-translation-workbench::ui.tw-graph.strang.merge-right
                                    id="literature.right.1.end-override"
                                    attach-to="strang.trunk.node.3"
                                    bridge-length="12rem"
                                    start-length="3rem"
                                    :stem-lengths="[1 => '4rem']"
                                    :node-labels="[
                                        1 => [
                                            'left' => [
                                                'text' => ['Valid label', 'anchor 1'],
                                                'width' => 'default',
                                                'align' => 'right',
                                            ],
                                        ],
                                        3 => [
                                            'right' => [
                                                'text' => ['Numeric end label', 'overridden'],
                                                'width' => 'halfLong',
                                                'align' => 'left',
                                            ],
                                        ],
                                        'end' => [
                                            'right' => [
                                                'text' => ['Explicit end label', 'end wins'],
                                                'width' => 'halfLong',
                                                'align' => 'left',
                                            ],
                                        ],
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- merge-mismatch-example-1:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/strang/merge/merge-mismatch.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
