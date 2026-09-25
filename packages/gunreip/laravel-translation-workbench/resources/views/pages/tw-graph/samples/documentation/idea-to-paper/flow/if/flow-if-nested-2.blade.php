<x-translation-workbench::ui.common.heading-counter-group group="flow-if-nested-2">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('IF nested 2') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.if.flow-if-nested-2" />
            <flux:callout.text>
                {{ __('The amber inner IF belongs to the second outer ELSEIF (sources). Earlier successful actions bypass this nested block. Every inner result returns to the outer output rail. Outer ELSEIF conditions are reached exclusively through the outer False route.') }}
            </flux:callout.text>
            @php
                $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-2',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="flow-if-nested-2-example-1"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $docExampleSource->example('flow-if-nested-2-example-1') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="flow-if-nested-2-example-2"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $docExampleSource->example('flow-if-nested-2-example-2') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator class="mt-4" :text="__('Props used in these examples')" />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Props and connections') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns class="bg-white dark:bg-zinc-900" sticky>
                            <flux:table.column>{{ __('Prop / anchor') }}</flux:table.column>
                            <flux:table.column>{{ __('Default') }}</flux:table.column>
                            <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>condition-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>IF condition?</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('First decision of each independent IF block.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>if-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>IF action</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('First action and color of the shared return rail.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>elseifs[].actionLabel.return</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('false leaves the action output open for a nested graph. The author must connect its output to anchorNode-return. No direct stem bypasses the nested graph.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>elseifs</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]; one or more entries</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicit conditionLabel/actionLabel entries. actionLabel.return can also open an ELSEIF action for nesting.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>if-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Final fallback action, or a bypass when no text is supplied.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>elseifs[].beforeLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>elseif-before-length / 6rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('The following outer ELSEIF (deferred) reserves 36rem for the nested block. Adjust this explicit clearance when enlarging the inner graph.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>.elseif.sources.true.anchorNode-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>action output</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Entry anchor for the inner IF, after the second outer ELSEIF action.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>.elseif.sources.true.anchorNode-return</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>next output junction</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Rejoins the common outer output rail, never the next condition input.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>.anchorNode-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>common output</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Continuation after all alternatives of the corresponding IF. returnColor carries its shared True rail color; color retains the final node color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>if-end.stemLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length / 8rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Overrides the incoming stem of the final False route, including a textless bypass. The opposite return stem adjusts with it; label clearance remains a minimum.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>parts.start return-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Declares the IF return target. The incoming color continues along the outer return stems after this junction; branch arcs and dots keep their own colors.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>elseifs[].actionLabel.lineJumps</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicit jumps on the outgoing bridge: over references a line, radius sets the semicircle size, side selects top/bottom (bridge) or left/right (stem). Multiple entries are supported. Invalid entries keep the line intact and show a DEV mismatch badge.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>elseifs[].actionLabel.returnOffset</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>12rem for an open ELSEIF</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Extends only the outgoing action bridge to separate the nested entry from the earlier alternatives\' return rail. Crossings without a dot are not junctions.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>elseifs[].actionLabel.stemLineJumps</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicit jumps on the action return stem. The right example crosses the continuous source-review bridge with side="right". The left example uses lineJumps on the bridge.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
        </flux:callout>
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <flux:text class="m-3">{{ __('Cyan: outer IF · Amber: inner IF · Zinc: fallback routes') }}</flux:text>
                <x-translation-workbench::ui.common.heading-counter
                    example="flow-if-nested-2-example-1"
                    size="sm"
                >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- flow-if-nested-2-example-1:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-step-08-flow-if-nested-2"
                        :dev="true"
                        :coordinates="true"
                        color="cyan"
                        min-width="72rem"
                        min-height="93rem"
                        horizontal-padding="12rem"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.flow-start
                            id="literature.flow.1.if-nested-2-origin"
                            :start-label="['text' => ['Review request'], 'width' => 'half']"
                        />
                        {{-- Outer IF --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                            id="literature.flow.1.if-nested-2.outer"
                            attach-to="literature.flow.1.if-nested-2-origin.anchorNode-end"
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
                                        'return' => false,
                                        'returnOffset' => '12rem',
                                        'lineJumps' => [
                                            [
                                                'over' => 'literature.flow.1.if-nested-2.outer.elseif.automatic.true.stem',
                                                'radius' => '0.65rem',
                                                'side' => 'top',
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'key' => 'deferred',
                                    'beforeLength' => '36rem',
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

                        {{-- The inner block is entered only when the second outer ELSEIF is true. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                            id="literature.flow.1.if-nested-2.inner"
                            attach-to="literature.flow.1.if-nested-2.outer.elseif.sources.true.anchorNode-end"
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
                            $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-2';
                            $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-2.inner.anchorNode-end',
                            );
                            $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-2.outer.elseif.sources.true.anchorNode-return',
                            );
                            $returnBridge = 'calc(' . $outerReturn['x'] . ' - ' . $innerEnd['x'] . ' - (2 * 2.75rem))';
                        @endphp
                        <x-translation-workbench::ui.tw-graph.parts.sideways
                            id="literature.flow.1.if-nested-2.inner-return"
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
                                'literature.flow.1.if-nested-2.inner-return.anchorNode-end',
                            );
                        @endphp
                        <x-translation-workbench::ui.tw-graph.parts.start
                            id="literature.flow.1.if-nested-2.inner-return.stem"
                            return-to="literature.flow.1.if-nested-2.outer.elseif.sources.true.anchorNode-return"
                            :anchor-start="$returnEnd"
                            :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                            :gradient="false"
                            :node-end="false"
                            :dev-counter-end="false"
                            :color="$returnEnd['color']"
                        />
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.if-nested-2.continue"
                            attach-to="literature.flow.1.if-nested-2.outer.anchorNode-end"
                            color="cyan"
                            :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                        />
                        @php
                            $outerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-2.outer.anchorNode-start',
                            );
                            $outerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-2.outer.anchorNode-end',
                            );
                            $innerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-2.inner.anchorNode-start',
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
                            id="literature.flow.1.if-nested-2.inner.bounds"
                            label="Inner IF / ELSEIF / ELSE"
                            :x="$innerBounds['left']"
                            :y="$innerBounds['bottom']"
                            :width="$innerBounds['width']"
                            :height="$innerBounds['height']"
                            color="amber"
                        />
                        <x-translation-workbench::ui.tw-graph.dev-box
                            id="literature.flow.1.if-nested-2.outer.bounds"
                            label="Outer IF including nested section"
                            :x="$outerBounds['left']"
                            :y="$outerBounds['bottom']"
                            :width="$outerBounds['width']"
                            :height="$outerBounds['height']"
                            color="cyan"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- flow-if-nested-2-example-1:end --}}
                </div>
                <x-translation-workbench::ui.common.heading-counter
                    example="flow-if-nested-2-example-2"
                    size="sm"
                >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- flow-if-nested-2-example-2:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-step-08-flow-if-nested-2-right"
                        :dev="true"
                        :coordinates="true"
                        color="cyan"
                        min-width="72rem"
                        min-height="93rem"
                        horizontal-padding="12rem"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.flow-start
                            id="literature.flow.1.if-nested-2-right-origin"
                            :start-label="['text' => ['Review request'], 'width' => 'half']"
                        />
                        {{-- Outer IF --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                            id="literature.flow.1.if-nested-2-right.outer"
                            attach-to="literature.flow.1.if-nested-2-right-origin.anchorNode-end"
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
                                    'actionLabel' => [
                                        'text' => ['Approve automatically'],
                                        'width' => 'halfLong',
                                        'stemLineJumps' => [
                                            [
                                                'over' =>
                                                    'literature.flow.1.if-nested-2-right.outer.elseif.sources.true.bridge1.bridge-out',
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
                                        'text' => ['ELSEIF sources missing?'],
                                        'width' => 'halfLong',
                                        'color' => 'violet',
                                    ],
                                    'actionLabel' => [
                                        'text' => ['Evaluate source review'],
                                        'width' => 'halfLong',
                                        'return' => false,
                                        'returnOffset' => '12rem',
                                    ],
                                ],
                                [
                                    'key' => 'deferred',
                                    'beforeLength' => '36rem',
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

                        {{-- The inner block is entered only when the second outer ELSEIF is true. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                            id="literature.flow.1.if-nested-2-right.inner"
                            attach-to="literature.flow.1.if-nested-2-right.outer.elseif.sources.true.anchorNode-end"
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
                            $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-2-right';
                            $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-2-right.inner.anchorNode-end',
                            );
                            $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-2-right.outer.elseif.sources.true.anchorNode-return',
                            );
                            $returnBridge = 'calc(' . $innerEnd['x'] . ' - ' . $outerReturn['x'] . ' - (2 * 2.75rem))';
                        @endphp
                        <x-translation-workbench::ui.tw-graph.parts.sideways
                            id="literature.flow.1.if-nested-2-right.inner-return"
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
                                'literature.flow.1.if-nested-2-right.inner-return.anchorNode-end',
                            );
                        @endphp
                        <x-translation-workbench::ui.tw-graph.parts.start
                            id="literature.flow.1.if-nested-2-right.inner-return.stem"
                            return-to="literature.flow.1.if-nested-2-right.outer.elseif.sources.true.anchorNode-return"
                            :anchor-start="$returnEnd"
                            :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                            :gradient="false"
                            :node-end="false"
                            :dev-counter-end="false"
                            :color="$returnEnd['color']"
                        />
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.if-nested-2-right.continue"
                            attach-to="literature.flow.1.if-nested-2-right.outer.anchorNode-end"
                            color="cyan"
                            :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                        />
                        @php
                            $outerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-2-right.outer.anchorNode-start',
                            );
                            $outerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-2-right.outer.anchorNode-end',
                            );
                            $innerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-2-right.inner.anchorNode-start',
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
                            id="literature.flow.1.if-nested-2-right.inner.bounds"
                            label="Inner IF / ELSEIF / ELSE"
                            :x="$innerBounds['left']"
                            :y="$innerBounds['bottom']"
                            :width="$innerBounds['width']"
                            :height="$innerBounds['height']"
                            color="amber"
                        />
                        <x-translation-workbench::ui.tw-graph.dev-box
                            id="literature.flow.1.if-nested-2-right.outer.bounds"
                            label="Outer IF including nested section"
                            :x="$outerBounds['left']"
                            :y="$outerBounds['bottom']"
                            :width="$outerBounds['width']"
                            :height="$outerBounds['height']"
                            color="cyan"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- flow-if-nested-2-example-2:end --}}
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../flow/if/flow-if-nested-2.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
