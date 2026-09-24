<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Flow IF True/False') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('A binary IF decision first evaluates its question inside a step. The shared output splits into two stacked action bridges pointing in the same direction. True routes sideways first and then follows a stem; False follows its stem first and then routes sideways. Both paths meet at one end anchor. Per execution only one branch is taken. Node labels remain optional explanations; the question and results are embedded in the path.') }}
        </flux:callout.text>
        @php
            $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-decision',
            );
            $docExample1Code = $docExampleSource->example('flow-decision-example-1');
        @endphp
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $docExample1Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Flow IF True/False props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">auto id
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stable prefix. The
                            question uses .question, the two routes use .true and .false, and .anchorNode-end is the
                            common continuation.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">attach-to
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Incoming anchor before
                            the question step.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt;
                            &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">x, y</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback incoming
                            coordinate when no attach-to anchor is available.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top, top-bottom
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Vertical direction for
                            the question, both arcs and branch continuations.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:condition-label
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">IF condition?
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, width, align,
                            badgeColor, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Question embedded in
                            the incoming step, not attached as a separate node label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">before-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stem before the
                            question.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">label-gap
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">auto from question
                            lines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Space occupied by the
                            question in the stem.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">after-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stem between the
                            question and the shared split anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">step-caps
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Draws caps bordering
                            the question gap.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">cap-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph cap-length /
                            1.75rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Size of the
                            question-step caps.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-radius
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph arc-radius /
                            2.75rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Size of the incoming
                            and final arcs.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0.75rem
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Minimum straight length
                            before and after each outcome label. Both route spans align to the larger required total;
                            the shorter route receives longer straight sections.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left-bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Legacy alias of
                            true-bridge-length.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">right-bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Legacy alias of
                            false-bridge-length.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:if-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">True; width=half
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, width, align,
                            badgeColor, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Action text on the
                            route that crosses sideways first, then follows its stem to the common end.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:if-end
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">False; width=half
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">text, width, align,
                            badgeColor, maxLines</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Action text on the
                            route that follows its stem first, then crosses sideways to the common end.
                        </flux:table.cell>
                    </flux:table.row>

                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-labels
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left, right → text,
                            width, align, connectorLength, connectorGap</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Additional information
                            at the single shared end anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">node-end
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Visibility of the
                            shared end anchor, rendered once by the final False arc.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited graph color /
                            zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Base color for the
                            question and paths. Label badgeColor can distinguish outcomes.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dev-mode
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited :dev
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional local
                            override for diagnostic output.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">20</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Rendering layer.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left, right
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Side toward which both
                            bridges run; default left uses east-north and south-west arcs.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">8rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Vertical offset
                            between the True and False bridges. The same length follows True and precedes False; enough
                            space for the label line heights plus 2rem is enforced.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true-bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Requested straight
                            length before and after the True label. May grow to match the False route span.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false-bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs"></flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Requested straight
                            length before and after the False label. May grow to match the True route span.
                        </flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
        <flux:text class="mt-3">
            {{ __('Continue after both routes with attach-to="…decision-1.anchorNode-end". The older true/false and left/right output aliases now refer to this same shared continuation; branch-specific actions belong in the corresponding action bridge.') }}
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
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-decision-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-step-08-flow-decision"
                    :dev="true"
                    :coordinates="true"
                    color="zinc"
                    horizontal-padding="12rem"
                    min-width="56rem"
                    min-height="44rem"
                >
                    <div class="pointer-events-none opacity-25">
                        <x-translation-workbench::ui.tw-graph.strang.flow-start
                            id="literature.flow.1.paper-process"
                            start-length="7rem"
                            :node-end-dot="false"
                            :start-label="[
                                'text' => ['Paper process', 'start'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                        />
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.paper-process.step-1"
                            attach-to="literature.flow.1.paper-process.anchorNode-end"
                            before-length="2rem"
                            after-length="3rem"
                            :step-label="[
                                'text' => ['Draft prepared'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                        />
                    </div>

                    <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                        id="literature.flow.1.paper-process.decision-1"
                        attach-to="literature.flow.1.paper-process.step-1.anchorNode-end"
                        color="cyan"
                        before-length="2rem"
                        after-length="2rem"
                        arc-radius="2.75rem"
                        side="left"
                        stem-length="4rem"
                        bridge-length="2rem"
                        true-bridge-length="5rem"
                        false-bridge-length="3rem"
                        :condition-label="[
                            'text' => ['IF reviewApproved?'],
                            'width' => 'default',
                            'align' => 'center',
                        ]"
                        :if-start="[
                            'text' => ['True'],
                            'width' => 'half',
                            'align' => 'center',
                            'badgeColor' => 'green',
                        ]"
                        :if-end="[
                            'text' => ['False'],
                            'width' => 'half',
                            'align' => 'center',
                            'badgeColor' => 'rose',
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-decision-example-1:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../flow/if/flow-decision.blade.php
        </flux:field>
    </flux:callout>
</section>
