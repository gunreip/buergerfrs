<x-translation-workbench::ui.common.heading-counter-group group="paths-branch-return">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Branch return path') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Two individually authored return paths, shown one below the other. A branch return leads back toward the trunk: side="left" runs left-right, while side="right" runs right-left. Each path consists of an incoming arc, a bridge, and an outgoing arc. Each zinc reference branch leads outward and upwards. The colored return starts at its stem endpoint and leads back to the x coordinate of the branch origin. Both components calculate their own route from explicitly configured anchors and lengths.') }}
            </flux:callout.text>
            @php
                $branchReturnExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-branch-return',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-return-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('left-right · side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $branchReturnExampleSource->example('branch-return-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-return-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('right-left · side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $branchReturnExampleSource->example('branch-return-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator class="mt-4" :text="__('Props used in these examples')" />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Branch return path props') }}</flux:callout.heading>
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
                                    <code>path.branch-return</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the return and its child elements.') }}
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
                                    {{ __('left returns left-right; right returns right-left.') }}
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
                                    {{ __('Starting coordinates for the incoming arc; subsequent anchors are calculated.') }}
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
                                    {{ __('Size of both arcs. The total vertical rise is twice this size.') }}
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
                                    {{ __('Horizontal bridge length. Total horizontal displacement includes both arcs.') }}
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
                                    {{ __('Color of the arcs, bridge, and joint arrows.') }}
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
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:fallback</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Allow dashed fallback styling when the owning component reports that a fallback anchor was used. Does not select or calculate a fallback anchor itself.') }}
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
                            example="branch-return-left-example"
                            size="sm"
                        >{{ __('left-right · side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- branch-return-left-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-branch-return-left-right"
                                :dev="true"
                                :coordinates="true"
                                color="cyan"
                                min-height="30rem"
                                min-width="36rem"
                                horizontal-padding="14rem"
                            >
                                {{-- Reference stem ends at x=-6.75rem, y=16.5rem, where the return starts. --}}
                                <x-translation-workbench::ui.tw-graph.paths.branch
                                    id="literature.paths.branch-return.left-right.reference"
                                    side="left"
                                    :anchor-start="['x' => '6.75rem', 'y' => '5rem']"
                                    entry-stem-length="2rem"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    :stem-continuation="[
                                        1 => [
                                            'length' => '4rem',
                                            'render' => true,
                                        ],
                                    ]"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.paths.branch-return
                                    id="literature.paths.branch-return.left-right"
                                    side="left"
                                    :anchor-start="['x' => '-6.75rem', 'y' => '16.5rem']"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    color="cyan"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- branch-return-left-example:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-return-right-example"
                            size="sm"
                        >{{ __('right-left · side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- branch-return-right-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-branch-return-right-left"
                                :dev="true"
                                :coordinates="true"
                                color="emerald"
                                min-height="30rem"
                                min-width="36rem"
                                horizontal-padding="14rem"
                            >
                                {{-- Reference stem ends at x=6.75rem, y=16.5rem, where the return starts. --}}
                                <x-translation-workbench::ui.tw-graph.paths.branch
                                    id="literature.paths.branch-return.right-left.reference"
                                    side="right"
                                    :anchor-start="['x' => '-6.75rem', 'y' => '5rem']"
                                    entry-stem-length="2rem"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    :stem-continuation="[
                                        1 => [
                                            'length' => '4rem',
                                            'render' => true,
                                        ],
                                    ]"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.paths.branch-return
                                    id="literature.paths.branch-return.right-left"
                                    side="right"
                                    :anchor-start="['x' => '6.75rem', 'y' => '16.5rem']"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    color="emerald"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- branch-return-right-example:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/paths/paths-branch-return.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
