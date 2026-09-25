<x-translation-workbench::ui.common.heading-counter-group group="paths-branch">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Branch path') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Two individually authored branch paths, shown one below the other. side="left" routes the bridge right-left; side="right" routes it left-right. Each connects an entry stem, an incoming arc, a bridge, an outgoing arc, and a labeled stem. The path calculates subsequent anchors from the starting coordinates and lengths.') }}
            </flux:callout.text>
            @php
                $branchExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-branch',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('right-left · side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $branchExampleSource->example('branch-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('left-right · side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $branchExampleSource->example('branch-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator class="mt-4" :text="__('Props used in these examples')" />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Branch path props') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns class="bg-white dark:bg-zinc-900" sticky>
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
                                    <code>path.branch</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the branch and its child elements.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>left</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('left branches right-left; right branches left-right. Both continue upwards after the outgoing arc.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:anchor-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Starting coordinates. All subsequent anchors are calculated.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>entry-stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional vertical section before the incoming arc.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>line-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph line_length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Fallback length used when resolving path dimensions.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>arc-radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph arc_radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Size of both arcs.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph bridge_length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length of the bridge when no bridge-continuation is supplied.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:bridge-continuation</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Replaces the default bridge with individually configured entries. Each accepts a length string or an array with length and top/bottom labels.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Fallback length for stem entries. A length alone does not produce a visible stem; use stem-continuation with a label or an explicit render flag.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:stem-continuation</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Vertical entries after the outgoing arc or step. Use length and left/right labels, or force/render/spacer=true to render an unlabeled stem. compressed=true uses segments.stem-compressed.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:step</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional status step after the outgoing arc. Supply text or a configuration with stepLabel, beforeLength, labelGap, and afterLength. A first plain labeled stem entry may supply the step endpoint labels instead of an extra stem.') }}
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
                                    {{ __('Arc-node configuration: keys 1 and 3. Other numeric keys are reported as mismatches in DEV mode. Bridge and stem labels belong in their continuation entries.') }}
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
                                    {{ __('Color of the path and default labels.') }}
                                </flux:table.cell>
                            </flux:table.row>
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
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
        </flux:callout>

        {{-- Preview --}}
        <flux:callout class="min-w-0" color="emerald">
            <flux:callout.heading icon="eye">{{ __('Preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4">
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-left-example"
                            size="sm"
                        >{{ __('right-left · side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- branch-left-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-branch-right-left"
                                :dev="true"
                                :coordinates="true"
                                color="cyan"
                                min-height="24rem"
                                min-width="36rem"
                                horizontal-padding="14rem"
                            >
                                <x-translation-workbench::ui.tw-graph.paths.branch
                                    id="literature.paths.branch.right-left"
                                    side="left"
                                    :anchor-start="['x' => '6.75rem', 'y' => '5rem']"
                                    entry-stem-length="2rem"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    :stem-continuation="[
                                        1 => [
                                            'length' => '4rem',
                                            'right' => [
                                                'text' => ['Branch continues'],
                                                'width' => 'half',
                                                'align' => 'center',
                                            ],
                                        ],
                                    ]"
                                    color="cyan"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- branch-left-example:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-right-example"
                            size="sm"
                        >{{ __('left-right · side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- branch-right-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-branch-left-right"
                                :dev="true"
                                :coordinates="true"
                                color="emerald"
                                min-height="24rem"
                                min-width="36rem"
                                horizontal-padding="14rem"
                            >
                                <x-translation-workbench::ui.tw-graph.paths.branch
                                    id="literature.paths.branch.left-right"
                                    side="right"
                                    :anchor-start="['x' => '-6.75rem', 'y' => '5rem']"
                                    entry-stem-length="4rem"
                                    arc-radius="2.75rem"
                                    bridge-length="12rem"
                                    :stem-continuation="[
                                        1 => [
                                            'length' => '4rem',
                                            'left' => [
                                                'text' => ['Branch continues'],
                                                'width' => 'half',
                                                'align' => 'center',
                                            ],
                                        ],
                                    ]"
                                    color="emerald"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- branch-right-example:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/paths/paths-branch.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
