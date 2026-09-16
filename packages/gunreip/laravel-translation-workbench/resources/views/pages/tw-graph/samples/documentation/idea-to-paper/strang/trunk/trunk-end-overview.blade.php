<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout color="indigo" icon="file-text" class="min-w-0">
        <flux:callout.heading>{{ __('End') }}</flux:callout.heading>
        <flux:callout.text>{{ __('Trunk end compares the default closing segment with explicit end props. The highlighted props control the final stem, cap width, and centered end label.') }}</flux:callout.text>
        @php
            $trunkExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-end-overview',
            );
            $trunkExample1Code = $trunkExampleSource->example('trunk-end-overview-example-1');
            $trunkExample2Code = $trunkExampleSource->example('trunk-end-overview-example-2');
        @endphp
        <flux:heading class="mt-4" size="sm">Example 1</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $trunkExample1Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">Example 2</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $trunkExample2Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">{{ __('Trunk props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">auto id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stable element prefix for labels, DEV identifiers, anchors, and later attach-to targets.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">component-counter</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">1</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback counter when no explicit id is supplied. Handmade graphs should usually set id explicitly.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Direction of the trunk timeline. The usual vertical authoring flow grows from bottom to top.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Manual start coordinate. Most handmade graphs can keep the default and let tw-graph provide the canvas origin.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited graph color / zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Trunk color inherited from tw-graph unless the trunk sets its own color.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the trunk-start segment before the first nodeEnd anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">tw-graph stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Default length for every trunk stem unless stem-lengths overrides a specific one.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:stem-count</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">default-path-segments</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">How many trunk stems are rendered after the start segment.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:stem-lengths</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Per-stem length overrides. Missing or null entries keep the default stem length.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-labels</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Text labels attached to numbered trunk stem anchor nodes.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">default-path-segments</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">10</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback stem count when stem-count is not set.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the final trunk-end segment.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end-cap-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">cap-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Cap size used by the trunk end segment.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:start-label</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Centered label at the trunk start. Accepts text, width, align, color, and related text-label options.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:end-label</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Centered label at the trunk end.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:start-node-labels</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Left/right labels attached to the trunk-start nodeEnd anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-label-space</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">3rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Extra space considered for start-label placement and bounds.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:start-shift-enabled</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">config trunk_start_shift_enabled</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Allows collision compensation to extend the start area when needed.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-shift-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">config trunk_start_shift_length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Minimum visible delta used when trunk-start collision compensation is applied.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">20</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Layer position of the trunk relative to side strangs and DEV overlays.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev-mode</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional local DEV override. Usually inherited from tw-graph.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout color="zinc" icon="eye" class="min-w-0">
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
                    <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- trunk-end-overview-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    class="px-20 py-12"
                    graph-id="idea-to-paper-step-02-trunk-end-default"
                    :dev="true"
                    :coordinates="true"
                    horizontal-padding="24rem"
                    min-width="40rem"
                    min-height="42rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
                </x-translation-workbench::ui.tw-graph>
                {{-- trunk-end-overview-example-1:end --}}
                    </div>
                            <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- trunk-end-overview-example-2:start --}}
                <x-translation-workbench::ui.tw-graph
                    class="px-20 py-12"
                    graph-id="idea-to-paper-step-02-trunk-end-props"
                    :dev="true"
                    :coordinates="true"
                    horizontal-padding="28rem"
                    min-width="48rem"
                    min-height="50rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.trunk
                        id="literature.center.1.paper"
                        color="emerald"
                        end-length="7rem"
                        end-cap-length="3rem"
                        :end-label="[
                            'text' => ['published paper', 'stable reference'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- trunk-end-overview-example-2:end --}}
                    </div>
                </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../strang/trunk/trunk-end-overview.blade.php</flux:field>
    </flux:callout>
</section>
