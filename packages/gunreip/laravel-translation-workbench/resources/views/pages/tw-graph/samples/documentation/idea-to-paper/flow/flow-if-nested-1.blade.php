<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('IF nested 1') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('The outer IF enters the amber inner IF only when review is required. Every inner result returns to the outer output rail. Outer ELSEIF conditions are reached exclusively through the outer False route.') }}
        </flux:callout.text>
        @php
            $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.flow-if-nested-1',
            );
            $docExample1Code = $docExampleSource->example('flow-if-nested-1-example-1');
            $docExample2Code = $docExampleSource->example('flow-if-nested-1-example-2');
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >side="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $docExample1Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >side="right"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $docExample2Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Props and connections') }}</flux:heading>
        <flux:table
            class="mt-3"
            container:class="max-h-80"
        >
            <flux:table.columns sticky>
                <flux:table.column>{{ __('Prop / anchor') }}</flux:table.column>
                <flux:table.column>{{ __('Default') }}</flux:table.column>
                <flux:table.column>{{ __('Purpose') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">condition-label</flux:table.cell>
                    <flux:table.cell>IF condition?</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">First decision of each independent IF block.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>if-start</flux:table.cell>
                    <flux:table.cell>IF action</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">First action and color of the shared return rail.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>if-start.return</flux:table.cell>
                    <flux:table.cell>true</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">false leaves the action output open for a nested graph.
                        The author must connect its output to anchorNode-return. No direct stem bypasses the nested
                        graph.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>elseifs</flux:table.cell>
                    <flux:table.cell>[]; one or more entries</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Explicit conditionLabel/actionLabel entries.
                        actionLabel.return can also open an ELSEIF action for nesting.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>if-end</flux:table.cell>
                    <flux:table.cell>[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Final fallback action, or a bypass when no text is
                        supplied.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">elseifs[].beforeLength</flux:table.cell>
                    <flux:table.cell>elseif-before-length / 6rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">The first outer ELSEIF reserves 84rem for the nested
                        block. Adjust this explicit clearance when enlarging the inner graph.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">.if.true.anchorNode-end</flux:table.cell>
                    <flux:table.cell>action output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Entry anchor for the inner IF, after the outer True
                        action.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">.if.true.anchorNode-return</flux:table.cell>
                    <flux:table.cell>next output junction</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Rejoins the common outer output rail, never the next
                        condition input.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>.anchorNode-end</flux:table.cell>
                    <flux:table.cell>common output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Continuation after all alternatives of the corresponding
                        IF. returnColor carries its shared True rail color; color retains the final node color.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">if-end.stemLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">stem-length / 8rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Overrides the incoming stem of the final False route,
                        including a textless bypass. The opposite return stem adjusts with it; label clearance remains a
                        minimum.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>parts.start return-to</flux:table.cell>
                    <flux:table.cell>null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Declares the IF return target. The incoming color
                        continues along the outer return stems after this junction; branch arcs and dots keep their own
                        colors.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
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
            <flux:text class="m-3">{{ __('Cyan: outer IF · Amber: inner IF · Zinc: fallback routes') }}</flux:text>
            <flux:heading
                class="mt-3"
                size="sm"
            >side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-if-nested-1-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-step-08-flow-if-nested-1"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="72rem"
                    min-height="86rem"
                    horizontal-padding="12rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.flow.1.if-nested-1-origin"
                        :start-label="['text' => ['Review request'], 'width' => 'half']"
                    />
                    {{-- Outer IF --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-1.outer"
                        attach-to="literature.flow.1.if-nested-1-origin.anchorNode-end"
                        side="left"
                        color="cyan"
                        :condition-label="['text' => ['IF review required?'], 'width' => 'halfLong']"
                        :if-start="[
                            'text' => ['Evaluate review result'],
                            'width' => 'halfLong',
                            'color' => 'cyan',
                            'return' => false,
                        ]"
                        :elseifs="[
                            [
                                'key' => 'automatic',
                                'beforeLength' => '36rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF automatic approval?'],
                                    'width' => 'halfLong',
                                    'color' => 'blue',
                                ],
                                'actionLabel' => ['text' => ['Approve automatically'], 'width' => 'halfLong'],
                            ],
                            [
                                'key' => 'deferred',
                                'beforeLength' => '4rem',
                                'afterLength' => '1rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF review deferred?'],
                                    'width' => 'halfLong',
                                    'color' => 'violet',
                                ],
                                'actionLabel' => ['text' => ['Schedule review'], 'width' => 'halfLong'],
                            ],
                        ]"
                        :if-end="[
                            'text' => ['Keep draft'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                            'stemLength' => '2rem',
                        ]"
                    />

                    {{-- The inner block belongs only to the first outer True action. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-1.inner"
                        attach-to="literature.flow.1.if-nested-1.outer.if.true.anchorNode-end"
                        side="left"
                        color="amber"
                        before-length="2rem"
                        :condition-label="['text' => ['IF result is green?'], 'width' => 'halfLong', 'align' => 'left']"
                        :if-start="['text' => ['Publish paper'], 'width' => 'halfLong', 'color' => 'green']"
                        :elseifs="[
                            [
                                'key' => 'changes',
                                'beforeLength' => '4rem',
                                'afterLength' => '1rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF changes requested?'],
                                    'width' => 'halfLong',
                                    'color' => 'amber',
                                ],
                                'actionLabel' => ['text' => ['Revise draft'], 'width' => 'halfLong'],
                            ],
                            [
                                'key' => 'sources',
                                'beforeLength' => '4rem',
                                'afterLength' => '1rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF sources missing?', 'Check references'],
                                    'width' => 'halfLong',
                                    'color' => 'blue',
                                ],
                                'actionLabel' => ['text' => ['Add sources'], 'width' => 'halfLong'],
                            ],
                        ]"
                        :if-end="[
                            'text' => ['Request another review'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                            'stemLength' => '2rem',
                        ]"
                    />

                    {{-- Return above the inner block, then join the outer output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-1';
                        $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-1.inner.anchorNode-end',
                        );
                        $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-1.outer.if.true.anchorNode-return',
                        );
                        $returnBridge = 'calc(' . $outerReturn['x'] . ' - ' . $innerEnd['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.flow.1.if-nested-1.inner-return"
                        :anchor-start="$innerEnd"
                        side="left"
                        arc-radius="2.75rem"
                        :bridge-length="$returnBridge"
                        :color="$innerEnd['returnColor']"
                        :joint-arrow-end="true"
                        :dev-counter-end="'R'"
                    />
                    @php
                        $returnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-1.inner-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.flow.1.if-nested-1.inner-return.stem"
                        return-to="literature.flow.1.if-nested-1.outer.if.true.anchorNode-return"
                        :anchor-start="$returnEnd"
                        :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :dev-counter-end="false"
                        :color="$returnEnd['color']"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.flow.1.if-nested-1.continue"
                        attach-to="literature.flow.1.if-nested-1.outer.anchorNode-end"
                        color="cyan"
                        :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                    />
                    @php
                        $outerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-1.outer.anchorNode-start',
                        );
                        $outerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-1.outer.anchorNode-end',
                        );
                        $innerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-1.inner.anchorNode-start',
                        );
                        $innerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                            [$innerInput, $innerEnd],
                            '10rem',
                        );
                        $outerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                            [$outerInput, $outerEnd, $innerEnd],
                            '12rem',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-1.inner.bounds"
                        label="Inner IF / ELSEIF / ELSE"
                        :x="$innerBounds['left']"
                        :y="$innerBounds['bottom']"
                        :width="$innerBounds['width']"
                        :height="$innerBounds['height']"
                        color="amber"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-1.outer.bounds"
                        label="Outer IF including nested section"
                        :x="$outerBounds['left']"
                        :y="$outerBounds['bottom']"
                        :width="$outerBounds['width']"
                        :height="$outerBounds['height']"
                        color="cyan"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-if-nested-1-example-1:end --}}
            </div>
            <flux:heading
                class="mt-6"
                size="sm"
            >side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-if-nested-1-example-2:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-step-08-flow-if-nested-1-right"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="72rem"
                    min-height="86rem"
                    horizontal-padding="12rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.flow.1.if-nested-1-right-origin"
                        :start-label="['text' => ['Review request'], 'width' => 'half']"
                    />
                    {{-- Outer IF --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-1-right.outer"
                        attach-to="literature.flow.1.if-nested-1-right-origin.anchorNode-end"
                        side="right"
                        color="cyan"
                        :condition-label="['text' => ['IF review required?'], 'width' => 'halfLong']"
                        :if-start="[
                            'text' => ['Evaluate review result'],
                            'width' => 'halfLong',
                            'color' => 'cyan',
                            'return' => false,
                        ]"
                        :elseifs="[
                            [
                                'key' => 'automatic',
                                'beforeLength' => '36rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF automatic approval?'],
                                    'width' => 'halfLong',
                                    'color' => 'blue',
                                ],
                                'actionLabel' => ['text' => ['Approve automatically'], 'width' => 'halfLong'],
                            ],
                            [
                                'key' => 'deferred',
                                'beforeLength' => '4rem',
                                'afterLength' => '1rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF review deferred?'],
                                    'width' => 'halfLong',
                                    'color' => 'violet',
                                ],
                                'actionLabel' => ['text' => ['Schedule review'], 'width' => 'halfLong'],
                            ],
                        ]"
                        :if-end="[
                            'text' => ['Keep draft'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                            'stemLength' => '2rem',
                        ]"
                    />

                    {{-- The inner block belongs only to the first outer True action. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-1-right.inner"
                        attach-to="literature.flow.1.if-nested-1-right.outer.if.true.anchorNode-end"
                        side="right"
                        color="amber"
                        before-length="2rem"
                        :condition-label="['text' => ['IF result is green?'], 'width' => 'halfLong', 'align' => 'left']"
                        :if-start="['text' => ['Publish paper'], 'width' => 'halfLong', 'color' => 'green']"
                        :elseifs="[
                            [
                                'key' => 'changes',
                                'beforeLength' => '4rem',
                                'afterLength' => '1rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF changes requested?'],
                                    'width' => 'halfLong',
                                    'color' => 'amber',
                                ],
                                'actionLabel' => ['text' => ['Revise draft'], 'width' => 'halfLong'],
                            ],
                            [
                                'key' => 'sources',
                                'beforeLength' => '4rem',
                                'afterLength' => '1rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF sources missing?', 'Check references'],
                                    'width' => 'halfLong',
                                    'color' => 'blue',
                                ],
                                'actionLabel' => ['text' => ['Add sources'], 'width' => 'halfLong'],
                            ],
                        ]"
                        :if-end="[
                            'text' => ['Request another review'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                            'stemLength' => '2rem',
                        ]"
                    />

                    {{-- Return above the inner block, then join the outer output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-1-right';
                        $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-1-right.inner.anchorNode-end',
                        );
                        $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-1-right.outer.if.true.anchorNode-return',
                        );
                        $returnBridge = 'calc(' . $innerEnd['x'] . ' - ' . $outerReturn['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.flow.1.if-nested-1-right.inner-return"
                        :anchor-start="$innerEnd"
                        side="right"
                        arc-radius="2.75rem"
                        :bridge-length="$returnBridge"
                        :color="$innerEnd['returnColor']"
                        :joint-arrow-end="true"
                        :dev-counter-end="'R'"
                    />
                    @php
                        $returnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-1-right.inner-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.flow.1.if-nested-1-right.inner-return.stem"
                        return-to="literature.flow.1.if-nested-1-right.outer.if.true.anchorNode-return"
                        :anchor-start="$returnEnd"
                        :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :dev-counter-end="false"
                        :color="$returnEnd['color']"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.flow.1.if-nested-1-right.continue"
                        attach-to="literature.flow.1.if-nested-1-right.outer.anchorNode-end"
                        color="cyan"
                        :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                    />
                    @php
                        $outerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-1-right.outer.anchorNode-start',
                        );
                        $outerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-1-right.outer.anchorNode-end',
                        );
                        $innerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-1-right.inner.anchorNode-start',
                        );
                        $innerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                            [$innerInput, $innerEnd],
                            '10rem',
                        );
                        $outerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                            [$outerInput, $outerEnd, $innerEnd],
                            '12rem',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-1-right.inner.bounds"
                        label="Inner IF / ELSEIF / ELSE"
                        :x="$innerBounds['left']"
                        :y="$innerBounds['bottom']"
                        :width="$innerBounds['width']"
                        :height="$innerBounds['height']"
                        color="amber"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-1-right.outer.bounds"
                        label="Outer IF including nested section"
                        :x="$outerBounds['left']"
                        :y="$outerBounds['bottom']"
                        :width="$outerBounds['width']"
                        :height="$outerBounds['height']"
                        color="cyan"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-if-nested-1-example-2:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../flow/flow-if-nested-1.blade.php
        </flux:field>
    </flux:callout>
</section>
