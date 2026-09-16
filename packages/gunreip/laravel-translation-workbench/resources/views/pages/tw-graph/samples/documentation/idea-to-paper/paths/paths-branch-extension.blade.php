<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Branch extension path') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Two individually authored branch extensions, shown one below the other. Each colored extension starts at the bridge endpoint of a zinc reference branch and continues outward through its own bridge, arc, stem, and labeled end. The reference branch also continues upwards, making the additional branch visible. side="left" runs right-left; side="right" runs left-right.') }}
        </flux:callout.text>
        @php
            $branchExtensionExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-branch-extension',
            );
            $branchExtensionLeftCode = $branchExtensionExampleSource->example('branch-extension-left-example');
            $branchExtensionRightCode = $branchExtensionExampleSource->example('branch-extension-right-example');
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >right-left · side="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $branchExtensionLeftCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >left-right · side="right"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $branchExtensionRightCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Branch extension path props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">path.branch-extension</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the extension and child elements.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left continues right-left; right continues left-right.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Starting coordinates; these examples match the reference branch bridge endpoint.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchorStart.sourceType</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">unset</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Set stem inside anchor-start when attaching to a vertical stem endpoint. This adds an incoming arc before the extension bridge.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-size</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph arc_size</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Size of the outgoing arc and optional incoming arc.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph bridge_length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the extension bridge.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph stem_length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the vertical section after the arc or optional step.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:step</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional status step before the stem. Accepts text or stepLabel with beforeLength, labelGap, and afterLength.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-labels</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Key 3 configures the stem endpoint labels with left/right entries. The key stays 3 when an introductory arc or step is added.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:end-label</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional text or label configuration for the final cap.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the final end section. The default 0rem still renders a cap at the stem endpoint; null or an empty string omits the section when no end-label is set.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">cap-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional cap size for the end section; explicitly 1.25rem in these examples.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited / zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Extension color and default label color.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional stacking-order override.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable path and segment diagnostic boxes and node counters.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout
        class="min-w-0"
        color="zinc"
        icon="eye"
    >
        <flux:callout.heading>{{ __('Branch extension path preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <flux:heading
                class="mt-4"
                size="sm"
            >right-left · side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- branch-extension-left-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-paths-branch-extension-right-left"
                    :dev="true"
                    :coordinates="true"
                    min-height="28rem"
                    min-width="44rem"
                    horizontal-padding="20rem"
                >
                    {{-- Reference bridge ends at x=3.25rem, y=9.75rem. --}}
                    <x-translation-workbench::ui.tw-graph.paths.branch
                        id="literature.paths.branch-extension.right-left.reference"
                        side="left"
                        :anchor-start="['x' => '10rem', 'y' => '5rem']"
                        entry-stem-length="2rem"
                        arc-size="2.75rem"
                        bridge-length="4rem"
                        :stem-continuation="[
                            1 => [
                                'length' => '4rem',
                                'right' => [
                                    'text' => ['Reference branch'],
                                    'width' => 'half',
                                    'align' => 'center',
                                ],
                            ],
                        ]"
                        color="zinc"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.paths.branch-extension
                        id="literature.paths.branch-extension.right-left"
                        side="left"
                        :anchor-start="['x' => '3.25rem', 'y' => '9.75rem']"
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        stem-length="4rem"
                        end-length="2rem"
                        cap-length="1.25rem"
                        :end-label="[
                            'text' => ['Extension end'],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                        color="cyan"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- branch-extension-left-example:end --}}
            </div>
            <flux:heading
                class="mt-4"
                size="sm"
            >left-right · side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- branch-extension-right-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-paths-branch-extension-left-right"
                    :dev="true"
                    :coordinates="true"
                    min-height="28rem"
                    min-width="44rem"
                    horizontal-padding="20rem"
                >
                    {{-- Reference bridge ends at x=-3.25rem, y=9.75rem. --}}
                    <x-translation-workbench::ui.tw-graph.paths.branch
                        id="literature.paths.branch-extension.left-right.reference"
                        side="right"
                        :anchor-start="['x' => '-10rem', 'y' => '5rem']"
                        entry-stem-length="2rem"
                        arc-size="2.75rem"
                        bridge-length="4rem"
                        :stem-continuation="[
                            1 => [
                                'length' => '4rem',
                                'left' => [
                                    'text' => ['Reference branch'],
                                    'width' => 'half',
                                    'align' => 'center',
                                ],
                            ],
                        ]"
                        color="zinc"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.paths.branch-extension
                        id="literature.paths.branch-extension.left-right"
                        side="right"
                        :anchor-start="['x' => '-3.25rem', 'y' => '9.75rem']"
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        stem-length="4rem"
                        end-length="2rem"
                        cap-length="1.25rem"
                        :end-label="[
                            'text' => ['Extension end'],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                        color="emerald"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- branch-extension-right-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/paths/paths-branch-extension.blade.php
        </flux:field>
    </flux:callout>
</section>
