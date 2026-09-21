<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('IF nested 3') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.documentation-links example="flow.if.flow-if-nested-3" />
        <flux:callout.text>
            {{ __('The amber inner IF belongs to the outer ELSE fallback. It is entered only when every outer condition is false. Successful outer actions bypass it and continue at the common output. Every inner result rejoins that same output.') }}
        </flux:callout.text>
        @php
            $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-3',
            );
            $docExample1Code = $docExampleSource->example('flow-if-nested-3-example-1');
            $docExample2Code = $docExampleSource->example('flow-if-nested-3-example-2');
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
                    <flux:table.cell class="whitespace-normal">IF condition?</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">First decision of each independent IF block.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">if-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">IF action</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Action taken when the first condition is true.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">elseifs</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Subsequent conditions and their actions. None of their
                        successful routes enters the fallback.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">if-end.return</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">true</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Set false to open the fallback for a separately authored
                        nested graph.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">if-end.returnOffset</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">12rem when open</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Extends the outgoing fallback bridge beyond the common
                        return rail.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">if-end.returnLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">8rem when open</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Distance from the fallback output level to the common
                        continuation. This example reserves 40rem for the inner block and its return.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">if-end.stemLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">stem-length / 8rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Incoming stem before the fallback bridge. Independent of
                        returnLength.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">if-end.lineJumps</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Explicit crossing on the outgoing fallback bridge, over
                        the final True return stem.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">.false.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">fallback output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Entry for the inner IF after all outer conditions are
                        false.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">.false.anchorNode-return</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">common output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Target for the handmade nested return. Earlier True
                        actions meet here without entering the nested graph.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">.anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">common continuation</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Continue after successful outer actions or completion of
                        the fallback nested IF.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">parts.start return-to</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Declares the return connection and propagates returnColor
                        to the outer continuation.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">elseifs[].actionLabel.stemLineJumps</flux:table.cell>
                    <flux:table.cell>[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">The right example puts the jump on the final True return
                        stem, over the continuous fallback bridge. side="right" controls the bow direction.
                    </flux:table.cell>
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
                class="m-3"
                size="sm"
            >side="left" · Line jump on bridge</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-if-nested-3-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-step-08-flow-if-nested-3"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="72rem"
                    min-height="103rem"
                    horizontal-padding="12rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.flow.1.if-nested-3-origin"
                        :start-label="['text' => ['Review request'], 'width' => 'half']"
                    />
                    {{-- Outer IF --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-3.outer"
                        attach-to="literature.flow.1.if-nested-3-origin.anchorNode-end"
                        side="left"
                        color="cyan"
                        :condition-label="['text' => ['IF review required?'], 'width' => 'halfLong']"
                        :if-start="[
                            'text' => ['Schedule review'],
                            'width' => 'halfLong',
                            'color' => 'cyan',
                        ]"
                        :elseifs="[
                            [
                                'key' => 'automatic',
                                'beforeLength' => '4rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF automatic approval?'],
                                    'width' => 'halfLong',
                                    'color' => 'blue',
                                ],
                                'actionLabel' => ['text' => ['Approve automatically'], 'width' => 'halfLong'],
                            ],
                            [
                                'key' => 'sources',
                                'beforeLength' => '4rem',
                                'afterLength' => '1rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF sources missing?'],
                                    'width' => 'halfLong',
                                    'color' => 'violet',
                                ],
                                'actionLabel' => [
                                    'text' => ['Evaluate source review'],
                                    'width' => 'halfLong',
                                ],
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
                            'text' => ['Evaluate fallback review'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                            'stemLength' => '5rem',
                            'return' => false,
                            'returnOffset' => '12rem',
                            'returnLength' => '40rem',
                            'lineJumps' => [
                                [
                                    'over' => 'literature.flow.1.if-nested-3.outer.elseif.deferred.true.stem',
                                    'radius' => '0.65rem',
                                    'side' => 'top',
                                ],
                            ],
                        ]"
                    />

                    {{-- The inner block is entered only when all outer conditions are false. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-3.inner"
                        attach-to="literature.flow.1.if-nested-3.outer.false.anchorNode-end"
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
                        $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-3';
                        $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-3.inner.anchorNode-end',
                        );
                        $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-3.outer.false.anchorNode-return',
                        );
                        $returnBridge = 'calc(' . $outerReturn['x'] . ' - ' . $innerEnd['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.flow.1.if-nested-3.inner-return"
                        :anchor-start="$innerEnd"
                        side="left"
                        arc-radius="2.75rem"
                        :bridge-length="$returnBridge"
                        :color="$innerEnd['returnColor']"
                        :node-end="false"
                        :joint-arrow-end="false"
                        :dev-counter-end="false"
                    />
                    @php
                        $returnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-3.inner-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.flow.1.if-nested-3.inner-return.stem"
                        return-to="literature.flow.1.if-nested-3.outer.false.anchorNode-return"
                        :anchor-start="$returnEnd"
                        :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :joint-arrow-end="false"
                        :dev-counter-end="false"
                        :color="$returnEnd['color']"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.flow.1.if-nested-3.continue"
                        attach-to="literature.flow.1.if-nested-3.outer.anchorNode-end"
                        color="yellow"
                        :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                    />
                    @php
                        $outerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-3.outer.anchorNode-start',
                        );
                        $outerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-3.outer.anchorNode-end',
                        );
                        $innerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-3.inner.anchorNode-start',
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
                        id="literature.flow.1.if-nested-3.inner.bounds"
                        label="Inner IF / ELSEIF / ELSE"
                        :x="$innerBounds['left']"
                        :y="$innerBounds['bottom']"
                        :width="$innerBounds['width']"
                        :height="$innerBounds['height']"
                        color="amber"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-3.outer.bounds"
                        label="Outer IF including nested section"
                        :x="$outerBounds['left']"
                        :y="$outerBounds['bottom']"
                        :width="$outerBounds['width']"
                        :height="$outerBounds['height']"
                        color="cyan"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-if-nested-3-example-1:end --}}
            </div>
            <flux:heading
                class="m-3"
                size="sm"
            >side="right" · Line jump on stem</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-if-nested-3-example-2:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-step-08-flow-if-nested-3-right"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="72rem"
                    min-height="103rem"
                    horizontal-padding="12rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.flow.1.if-nested-3-right-origin"
                        :start-label="['text' => ['Review request'], 'width' => 'half']"
                    />
                    {{-- Outer IF --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-3-right.outer"
                        attach-to="literature.flow.1.if-nested-3-right-origin.anchorNode-end"
                        side="right"
                        color="cyan"
                        :condition-label="['text' => ['IF review required?'], 'width' => 'halfLong']"
                        :if-start="[
                            'text' => ['Schedule review'],
                            'width' => 'halfLong',
                            'color' => 'cyan',
                        ]"
                        :elseifs="[
                            [
                                'key' => 'automatic',
                                'beforeLength' => '4rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF automatic approval?'],
                                    'width' => 'halfLong',
                                    'color' => 'blue',
                                ],
                                'actionLabel' => ['text' => ['Approve automatically'], 'width' => 'halfLong'],
                            ],
                            [
                                'key' => 'sources',
                                'beforeLength' => '4rem',
                                'afterLength' => '1rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF sources missing?'],
                                    'width' => 'halfLong',
                                    'color' => 'violet',
                                ],
                                'actionLabel' => [
                                    'text' => ['Evaluate source review'],
                                    'width' => 'halfLong',
                                ],
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
                                'actionLabel' => [
                                    'text' => ['Schedule review'],
                                    'width' => 'halfLong',
                                    'stemLineJumps' => [
                                        [
                                            'over' =>
                                                'literature.flow.1.if-nested-3-right.outer.elseif.deferred.false.bridge1.bridge-out',
                                            'radius' => '0.65rem',
                                            'side' => 'right',
                                        ],
                                    ],
                                ],
                            ],
                        ]"
                        :if-end="[
                            'text' => ['Evaluate fallback review'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                            'stemLength' => '5rem',
                            'return' => false,
                            'returnOffset' => '12rem',
                            'returnLength' => '40rem',
                        ]"
                    />

                    {{-- The inner block is entered only when all outer conditions are false. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-3-right.inner"
                        attach-to="literature.flow.1.if-nested-3-right.outer.false.anchorNode-end"
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
                        $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-3-right';
                        $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-3-right.inner.anchorNode-end',
                        );
                        $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-3-right.outer.false.anchorNode-return',
                        );
                        $returnBridge = 'calc(' . $innerEnd['x'] . ' - ' . $outerReturn['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.flow.1.if-nested-3-right.inner-return"
                        :anchor-start="$innerEnd"
                        side="right"
                        arc-radius="2.75rem"
                        :bridge-length="$returnBridge"
                        :color="$innerEnd['returnColor']"
                        :node-end="false"
                        :joint-arrow-end="false"
                        :dev-counter-end="false"
                    />
                    @php
                        $returnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-3-right.inner-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.flow.1.if-nested-3-right.inner-return.stem"
                        return-to="literature.flow.1.if-nested-3-right.outer.false.anchorNode-return"
                        :anchor-start="$returnEnd"
                        :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :joint-arrow-end="false"
                        :dev-counter-end="false"
                        :color="$returnEnd['color']"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.flow.1.if-nested-3-right.continue"
                        attach-to="literature.flow.1.if-nested-3-right.outer.anchorNode-end"
                        color="rose"
                        :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                    />
                    @php
                        $outerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-3-right.outer.anchorNode-start',
                        );
                        $outerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-3-right.outer.anchorNode-end',
                        );
                        $innerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-3-right.inner.anchorNode-start',
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
                        id="literature.flow.1.if-nested-3-right.inner.bounds"
                        label="Inner IF / ELSEIF / ELSE"
                        :x="$innerBounds['left']"
                        :y="$innerBounds['bottom']"
                        :width="$innerBounds['width']"
                        :height="$innerBounds['height']"
                        color="amber"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-3-right.outer.bounds"
                        label="Outer IF including nested section"
                        :x="$outerBounds['left']"
                        :y="$outerBounds['bottom']"
                        :width="$outerBounds['width']"
                        :height="$outerBounds['height']"
                        color="cyan"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-if-nested-3-example-2:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../flow/if/flow-if-nested-3.blade.php
        </flux:field>
    </flux:callout>
</section>
