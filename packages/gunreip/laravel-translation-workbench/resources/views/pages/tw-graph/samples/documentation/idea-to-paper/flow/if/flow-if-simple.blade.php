<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('IF') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('A simple IF executes its action only when the condition is true. False follows a plain bypass with no action label in the bridge. Both routes join at the same output and continue with the next step. True and False are informational node labels. These two examples are authored separately for side=left and side=right.') }}
        </flux:callout.text>
        @php
            $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-simple',
            );
            $docExample1Code = $docExampleSource->example('flow-if-simple-example-1');
            $docExample2Code = $docExampleSource->example('flow-if-simple-example-2');
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >side="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $docExample1Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >side="right"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $docExample2Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('IF props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Array keys') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">generated
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stable prefix;
                            .anchorNode-end is the common continuation.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">component-counter
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">1</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Used when generating
                            the component ID.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">condition-label
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">condition?;
                            width=halfLong</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, width, align,
                            badgeColor, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Condition displayed in
                            the question step. Text or label configuration; no PHP expression is evaluated by this
                            diagram component.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">if-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Action; width=half
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, width, align,
                            badgeColor, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Action displayed in the
                            True bridge. The plain False bypass follows its total width automatically.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left, right
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Destination side for
                            the action and bypass routes. True/False information labels follow the layout.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">attach-to
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Incoming registered
                            anchor before the condition step.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchor-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt;
                            &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">x, y</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback input
                            coordinates.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top, top-bottom
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Vertical direction.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">before-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Line length before the
                            condition.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">after-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Line length after the
                            condition.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label-gap
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">automatic
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Condition height;
                            follows its content when omitted.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">step-caps
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Show caps at the
                            condition step.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">cap-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">canvas default
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Cap width.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-radius
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">canvas arc-size /
                            2.75rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Radius of the route
                            arcs.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-size
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Alias for arc-radius.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0.75rem (rendered at
                            least 1.15rem)</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Base length on each
                            side of a action label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true-bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">True route bridge
                            length; total spans are aligned automatically.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">8rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Distance between
                            alternatives; grows when needed for multiline action labels.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">node-labels
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left, right
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional information at
                            the common output.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">node-end
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Show the common output
                            node.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited / zinc
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Color of the
                            IF route.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dev-mode
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Override canvas
                            diagnostics.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">20</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stacking order.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left-counter-end
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">1</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">DEV counter at the
                            True arc output. Set false to hide this counter.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false-stem-counter
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">DEV counter at the
                            False stem output. Set false to hide this counter.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">right-counter-end
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">3</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">DEV counter at the
                            common output. Set false to hide this counter.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">if-end
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, color,
                            badgeColor, width, align</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color sets the entire
                            False lane; without text the bridge stays continuous. Adding text inserts a label;
                            badgeColor overrides its color. The canvas controls path-tone.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                    <flux:table.cell class="whitespace-normal">if-end.stemLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">stem-length / 8rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Overrides the incoming stem of the final False route, including a textless bypass. The opposite return stem adjusts with it; label clearance remains a minimum.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
            </flux:table>
        </div>
        <flux:text class="mt-3">
            {{ __('Continue with attach-to="…decision-1.anchorNode-end" after either executing the action or taking the bypass. The bypass has no action and its bridge spans the same width as the True route.') }}
        </flux:text>
    </flux:callout>
    <flux:callout
        class="min-w-0"
        color="zinc"
        icon="eye"
    >
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <flux:heading
                class="mt-3"
                size="sm"
            >side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-if-simple-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-step-08-flow-if-simple"
                    :dev="true"
                    :coordinates="true"
                    color="zinc"
                    horizontal-padding="6rem"
                    min-width="56rem"
                    min-height="34rem"
                >

                    <x-translation-workbench::ui.tw-graph.strang.flow-if
                        id="literature.flow.1.simple-if-process.decision-1"
                        :anchor-start="['x' => '0rem', 'y' => '3rem']"
                        color="cyan"
                        before-length="2rem"
                        after-length="2rem"
                        arc-size="2.75rem"
                        side="left"
                        stem-length="3rem"
                        :if-end="['color' => 'zinc']"
                        bridge-length="2rem"
                        true-bridge-length="5rem"
                        :condition-label="['text' => ['IF reviewApproved?'], 'width' => 'default', 'align' => 'center']"
                        :if-start="[
                            'text' => ['Publish paper'],
                            'width' => 'default',
                            'align' => 'center',
                            'badgeColor' => 'green',
                            'color' => 'green',
                        ]"
                        :if-end="[
                            // 'text' => ['Do not publish'],
                            // 'width' => 'default',
                            // 'align' => 'center',
                            // 'badgeColor' => 'red',
                            'color' => 'red',
                        ]"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.flow.1.simple-if-process.continue"
                        attach-to="literature.flow.1.simple-if-process.decision-1.anchorNode-end"
                        before-length="3rem"
                        after-length="3rem"
                        color="zinc"
                        :step-label="[
                            'text' => ['Continue process'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-if-simple-example-1:end --}}
            </div>
            <flux:heading
                class="mt-4"
                size="sm"
            >side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-if-simple-example-2:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-step-08-flow-if-simple-right"
                    :dev="true"
                    :coordinates="true"
                    color="zinc"
                    horizontal-padding="6rem"
                    min-width="56rem"
                    min-height="34rem"
                >

                    <x-translation-workbench::ui.tw-graph.strang.flow-if
                        id="literature.flow.1.simple-if-process-right.decision-1"
                        :anchor-start="['x' => '0rem', 'y' => '3rem']"
                        color="cyan"
                        before-length="2rem"
                        after-length="2rem"
                        arc-size="2.75rem"
                        side="right"
                        stem-length="4rem"
                        :if-end="['color' => 'zinc']"
                        bridge-length="2rem"
                        true-bridge-length="5rem"
                        :condition-label="[
                            'text' => ['IF reviewApproved?'],
                            'width' => 'default',
                            'align' => 'center',
                        ]"
                        :if-start="[
                            'text' => ['Publish paper'],
                            'width' => 'half',
                            'align' => 'center',
                            'badgeColor' => 'green',
                        ]"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.flow.1.simple-if-process-right.continue"
                        attach-to="literature.flow.1.simple-if-process-right.decision-1.anchorNode-end"
                        before-length="2rem"
                        after-length="2rem"
                        color="zinc"
                        :step-label="[
                            'text' => ['Continue process'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-if-simple-example-2:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../flow/if/flow-if-simple.blade.php
        </flux:field>
    </flux:callout>
</section>
