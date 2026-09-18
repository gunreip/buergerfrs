<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('IF nested 7') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('Three nested levels: the outer deferred action enters the amber review IF. Its sources action enters the sky reference-validation IF. The deepest return joins only the middle IF output; the middle return then joins the outer output. All other actions and fallbacks stay on their own level.') }}
        </flux:callout.text>
        @php
            $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-7',
            );
            $docExample1Code = $docExampleSource->example('flow-if-nested-7-example-1');
            $docExample2Code = $docExampleSource->example('flow-if-nested-7-example-2');
        @endphp
        <flux:heading class="mt-4" size="sm">side="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $docExample1Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">side="right"</flux:heading>
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
                    <flux:table.cell class="whitespace-normal">Each of the three independent IF components owns its
                        condition and branch choices.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">elseifs[].actionLabel.return</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">true</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Set false on outer.deferred and inner.sources to insert
                        the next nested level.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">elseifs[].actionLabel.returnOffset</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">12rem for open ELSEIF</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Separates each nested input from its parent return rail.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">elseifs[].actionLabel.lineJumps</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Bridge jumps: middle sources on the left; outer deferred on the right.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">elseifs[].actionLabel.stemLineJumps</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">[]</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Stem jumps: outer sources on the left; middle changes on the right.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">if-end.stemLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">stem-length / 8rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Outer: 76rem; middle: 40rem; deepest: 2rem. Each parent
                        reserves room for its nested content and return.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">outer.elseif.deferred.true.anchorNode-end
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">action output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Entry to inner, the middle review IF.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">inner.elseif.sources.true.anchorNode-end
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">action output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Entry to deep, the third-level reference-validation IF.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">inner.elseif.sources.true.anchorNode-return
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">middle output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Return target for deep-return.stem. The deepest block
                        does not bypass its parent.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">outer.elseif.deferred.true.anchorNode-return
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">outer output</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Return target for inner-return.stem, after the middle IF
                        completes.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">parts.start return-to</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Connects each return to its immediate parent and carries
                        the return color through the levels.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">continue.color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">graph color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">The independent continuation keeps its explicitly
                        configured fuchsia color.</flux:table.cell>
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
            <flux:text class="m-3">
                {{ __('Cyan: level 1 · Amber: level 2 · Sky: level 3 · Zinc: fallbacks') }}
            </flux:text>
            <flux:heading class="m-3" size="sm">side="left" · Outer: stem jump · Inner: bridge jump</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-if-nested-7-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-step-08-flow-if-nested-7"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="108rem"
                    min-height="135rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.flow.1.if-nested-7-origin"
                        :start-label="['text' => ['Review request'], 'width' => 'half']"
                    />
                    {{-- Outer IF --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-7.outer"
                        attach-to="literature.flow.1.if-nested-7-origin.anchorNode-end"
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
                                    'stemLineJumps' => [
                                        [
                                            'over' =>
                                                'literature.flow.1.if-nested-7.outer.elseif.deferred.true.bridge1.bridge-out',
                                            'radius' => '0.65rem',
                                            'side' => 'left',
                                        ],
                                    ],
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
                                    'text' => ['Evaluate deferred review'],
                                    'width' => 'halfLong',
                                    'return' => false,
                                    'returnOffset' => '12rem',
                                ],
                            ],
                        ]"
                        :if-end="[
                            'text' => ['Keep draft'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                            'stemLength' => '76rem',
                        ]"
                    />

                    {{-- The inner block is entered only when the last outer ELSEIF is true. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-7.inner"
                        attach-to="literature.flow.1.if-nested-7.outer.elseif.deferred.true.anchorNode-end"
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
                                'actionLabel' => [
                                    'text' => ['Validate sources'],
                                    'width' => 'halfLong',
                                    'return' => false,
                                    'returnOffset' => '12rem',
                                    'lineJumps' => [
                                        [
                                            'over' => 'literature.flow.1.if-nested-7.inner.elseif.changes.true.stem',
                                            'radius' => '0.65rem',
                                            'side' => 'top',
                                        ],
                                    ],
                                ],
                            ],
                        ]"
                        :if-end="[
                            'text' => ['Request another review'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                            'stemLength' => '40rem',
                        ]"
                    />

                    {{-- The inner block is entered only when the sources ELSEIF of the middle IF is true. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-7.deep"
                        attach-to="literature.flow.1.if-nested-7.inner.elseif.sources.true.anchorNode-end"
                        side="left"
                        color="sky"
                        before-length="2rem"
                        :condition-label="['text' => ['IF references verified?'], 'width' => 'halfLong', 'align' => 'left']"
                        :if-start="['text' => ['Accept references'], 'width' => 'halfLong', 'color' => 'blue']"
                        :elseifs="[
                            [
                                'key' => 'changes',
                                'beforeLength' => '4rem',
                                'afterLength' => '1rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF citations repairable?'],
                                    'width' => 'halfLong',
                                    'color' => 'amber',
                                ],
                                'actionLabel' => ['text' => ['Repair citations'], 'width' => 'halfLong'],
                            ],
                            [
                                'key' => 'sources',
                                'beforeLength' => '4rem',
                                'afterLength' => '1rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF expert check needed?', 'Review reference findings'],
                                    'width' => 'halfLong',
                                    'color' => 'blue',
                                ],
                                'actionLabel' => ['text' => ['Request manual check'], 'width' => 'halfLong'],
                            ],
                        ]"
                        :if-end="[
                            'text' => ['Reject references'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                            'stemLength' => '2rem',
                        ]"
                    />

                    {{-- Return above the inner block, then join the outer output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-7';
                        $deepEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7.deep.anchorNode-end',
                        );
                        $deepReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7.inner.elseif.sources.true.anchorNode-return',
                        );
                        $deepReturnBridge = 'calc(' . $deepReturn['x'] . ' - ' . $deepEnd['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.flow.1.if-nested-7.deep-return"
                        :anchor-start="$deepEnd"
                        side="left"
                        arc-radius="2.75rem"
                        :bridge-length="$deepReturnBridge"
                        :color="$deepEnd['returnColor']"
                        :node-end="false"
                        :joint-arrow-end="false"
                        :dev-counter-end="false"
                    />
                    @php
                        $deepReturnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7.deep-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.flow.1.if-nested-7.deep-return.stem"
                        return-to="literature.flow.1.if-nested-7.inner.elseif.sources.true.anchorNode-return"
                        :anchor-start="$deepReturnEnd"
                        :length="'calc(' . $deepReturn['y'] . ' - ' . $deepReturnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :joint-arrow-end="false"
                        :dev-counter-end="false"
                        :color="$deepReturnEnd['color']"
                    />
                    @php
                        $deepInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7.deep.anchorNode-start',
                        );
                        $deepBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                            [$deepInput, $deepEnd],
                            '10rem',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-7.deep.bounds"
                        label="Level 3: source validation"
                        :x="$deepBounds['left']"
                        :y="$deepBounds['bottom']"
                        :width="$deepBounds['width']"
                        :height="$deepBounds['height']"
                        color="sky"
                        :dev="true"
                    />

                    {{-- Return above the inner block, then join the outer output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-7';
                        $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7.inner.anchorNode-end',
                        );
                        $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7.outer.elseif.deferred.true.anchorNode-return',
                        );
                        $returnBridge = 'calc(' . $outerReturn['x'] . ' - ' . $innerEnd['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.flow.1.if-nested-7.inner-return"
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
                            'literature.flow.1.if-nested-7.inner-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.flow.1.if-nested-7.inner-return.stem"
                        return-to="literature.flow.1.if-nested-7.outer.elseif.deferred.true.anchorNode-return"
                        :anchor-start="$returnEnd"
                        :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :joint-arrow-end="false"
                        :dev-counter-end="false"
                        :color="$returnEnd['color']"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.flow.1.if-nested-7.continue"
                        attach-to="literature.flow.1.if-nested-7.outer.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                    />
                    @php
                        $outerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7.outer.anchorNode-start',
                        );
                        $outerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7.outer.anchorNode-end',
                        );
                        $innerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7.inner.anchorNode-start',
                        );
                        $innerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                            [$innerInput, $innerEnd, $deepInput, $deepEnd],
                            '10rem',
                        );
                        $outerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                            [$outerInput, $outerEnd, $innerEnd, $deepEnd],
                            '12rem',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-7.inner.bounds"
                        label="Inner IF / ELSEIF / ELSE"
                        :x="$innerBounds['left']"
                        :y="$innerBounds['bottom']"
                        :width="$innerBounds['width']"
                        :height="$innerBounds['height']"
                        color="amber"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-7.outer.bounds"
                        label="Outer IF including nested section"
                        :x="$outerBounds['left']"
                        :y="$outerBounds['bottom']"
                        :width="$outerBounds['width']"
                        :height="$outerBounds['height']"
                        color="cyan"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-if-nested-7-example-1:end --}}
            </div>
            <flux:heading class="m-3" size="sm">side="right" · Outer: bridge jump · Inner: stem jump</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-if-nested-7-example-2:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-step-08-flow-if-nested-7-right"
                    :dev="true"
                    :coordinates="true"
                    color="cyan"
                    min-width="108rem"
                    min-height="135rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-start
                        id="literature.flow.1.if-nested-7-right-origin"
                        :start-label="['text' => ['Review request'], 'width' => 'half']"
                    />
                    {{-- Outer IF --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-7-right.outer"
                        attach-to="literature.flow.1.if-nested-7-right-origin.anchorNode-end"
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
                                    'text' => ['Evaluate deferred review'],
                                    'width' => 'halfLong',
                                    'lineJumps' => [
                                        [
                                            'over' => 'literature.flow.1.if-nested-7-right.outer.elseif.sources.true.stem',
                                            'radius' => '0.65rem',
                                            'side' => 'top',
                                        ],
                                    ],
                                    'return' => false,
                                    'returnOffset' => '12rem',
                                ],
                            ],
                        ]"
                        :if-end="[
                            'text' => ['Keep draft'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                            'stemLength' => '76rem',
                        ]"
                    />

                    {{-- The inner block is entered only when the last outer ELSEIF is true. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-7-right.inner"
                        attach-to="literature.flow.1.if-nested-7-right.outer.elseif.deferred.true.anchorNode-end"
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
                                'actionLabel' => [
                                    'text' => ['Revise draft'],
                                    'width' => 'halfLong',
                                    'stemLineJumps' => [
                                        [
                                            'over' => 'literature.flow.1.if-nested-7-right.inner.elseif.sources.true.bridge1.bridge-out',
                                            'radius' => '0.65rem',
                                            'side' => 'right',
                                        ],
                                    ],
                                ],
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
                                'actionLabel' => [
                                    'text' => ['Validate sources'],
                                    'width' => 'halfLong',
                                    'return' => false,
                                    'returnOffset' => '12rem',
                                ],
                            ],
                        ]"
                        :if-end="[
                            'text' => ['Request another review'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                            'stemLength' => '40rem',
                        ]"
                    />

                    {{-- The inner block is entered only when the sources ELSEIF of the middle IF is true. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-nested-7-right.deep"
                        attach-to="literature.flow.1.if-nested-7-right.inner.elseif.sources.true.anchorNode-end"
                        side="right"
                        color="sky"
                        before-length="2rem"
                        :condition-label="['text' => ['IF references verified?'], 'width' => 'halfLong', 'align' => 'left']"
                        :if-start="['text' => ['Accept references'], 'width' => 'halfLong', 'color' => 'blue']"
                        :elseifs="[
                            [
                                'key' => 'changes',
                                'beforeLength' => '4rem',
                                'afterLength' => '1rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF citations repairable?'],
                                    'width' => 'halfLong',
                                    'color' => 'amber',
                                ],
                                'actionLabel' => ['text' => ['Repair citations'], 'width' => 'halfLong'],
                            ],
                            [
                                'key' => 'sources',
                                'beforeLength' => '4rem',
                                'afterLength' => '1rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF expert check needed?', 'Review reference findings'],
                                    'width' => 'halfLong',
                                    'color' => 'blue',
                                ],
                                'actionLabel' => ['text' => ['Request manual check'], 'width' => 'halfLong'],
                            ],
                        ]"
                        :if-end="[
                            'text' => ['Reject references'],
                            'width' => 'halfLong',
                            'color' => 'zinc',
                            'stemLength' => '2rem',
                        ]"
                    />

                    {{-- Return above the inner block, then join the outer output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-7-right';
                        $deepEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7-right.deep.anchorNode-end',
                        );
                        $deepReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7-right.inner.elseif.sources.true.anchorNode-return',
                        );
                        $deepReturnBridge = 'calc(' . $deepEnd['x'] . ' - ' . $deepReturn['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.flow.1.if-nested-7-right.deep-return"
                        :anchor-start="$deepEnd"
                        side="right"
                        arc-radius="2.75rem"
                        :bridge-length="$deepReturnBridge"
                        :color="$deepEnd['returnColor']"
                        :node-end="false"
                        :joint-arrow-end="false"
                        :dev-counter-end="false"
                    />
                    @php
                        $deepReturnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7-right.deep-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.flow.1.if-nested-7-right.deep-return.stem"
                        return-to="literature.flow.1.if-nested-7-right.inner.elseif.sources.true.anchorNode-return"
                        :anchor-start="$deepReturnEnd"
                        :length="'calc(' . $deepReturn['y'] . ' - ' . $deepReturnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :joint-arrow-end="false"
                        :dev-counter-end="false"
                        :color="$deepReturnEnd['color']"
                    />
                    @php
                        $deepInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7-right.deep.anchorNode-start',
                        );
                        $deepBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                            [$deepInput, $deepEnd],
                            '10rem',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-7-right.deep.bounds"
                        label="Level 3: source validation"
                        :x="$deepBounds['left']"
                        :y="$deepBounds['bottom']"
                        :width="$deepBounds['width']"
                        :height="$deepBounds['height']"
                        color="sky"
                        :dev="true"
                    />

                    {{-- Return above the inner block, then join the outer output rail. --}}
                    @php
                        $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-7-right';
                        $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7-right.inner.anchorNode-end',
                        );
                        $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7-right.outer.elseif.deferred.true.anchorNode-return',
                        );
                        $returnBridge = 'calc(' . $innerEnd['x'] . ' - ' . $outerReturn['x'] . ' - (2 * 2.75rem))';
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.sideways
                        id="literature.flow.1.if-nested-7-right.inner-return"
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
                            'literature.flow.1.if-nested-7-right.inner-return.anchorNode-end',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.parts.start
                        id="literature.flow.1.if-nested-7-right.inner-return.stem"
                        return-to="literature.flow.1.if-nested-7-right.outer.elseif.deferred.true.anchorNode-return"
                        :anchor-start="$returnEnd"
                        :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                        :gradient="false"
                        :node-end="false"
                        :joint-arrow-end="false"
                        :dev-counter-end="false"
                        :color="$returnEnd['color']"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.flow.1.if-nested-7-right.continue"
                        attach-to="literature.flow.1.if-nested-7-right.outer.anchorNode-end"
                        color="fuchsia"
                        :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                    />
                    @php
                        $outerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7-right.outer.anchorNode-start',
                        );
                        $outerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7-right.outer.anchorNode-end',
                        );
                        $innerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                            $nestedGraphId,
                            'literature.flow.1.if-nested-7-right.inner.anchorNode-start',
                        );
                        $innerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                            [$innerInput, $innerEnd, $deepInput, $deepEnd],
                            '10rem',
                        );
                        $outerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                            [$outerInput, $outerEnd, $innerEnd, $deepEnd],
                            '12rem',
                        );
                    @endphp
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-7-right.inner.bounds"
                        label="Inner IF / ELSEIF / ELSE"
                        :x="$innerBounds['left']"
                        :y="$innerBounds['bottom']"
                        :width="$innerBounds['width']"
                        :height="$innerBounds['height']"
                        color="amber"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.dev-box
                        id="literature.flow.1.if-nested-7-right.outer.bounds"
                        label="Outer IF including nested section"
                        :x="$outerBounds['left']"
                        :y="$outerBounds['bottom']"
                        :width="$outerBounds['width']"
                        :height="$outerBounds['height']"
                        color="cyan"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- flow-if-nested-7-example-2:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../flow/if/flow-if-nested-7.blade.php
        </flux:field>
    </flux:callout>
</section>
