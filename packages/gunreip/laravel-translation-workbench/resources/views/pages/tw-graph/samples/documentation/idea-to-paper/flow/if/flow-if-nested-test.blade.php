<x-translation-workbench::ui.common.heading-counter-group group="flow-if-nested-test">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Flow IF nested Test') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Action sequence inside one outer branch: prepare review data, run the nested IF, then save the result. Every inner result reaches the final action before the branch returns to the outer IF. Other outer actions and the fallback bypass the entire sequence. Both mixed-side orientations are shown.') }}
            </flux:callout.text>
            @php
                $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-test',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="flow-if-nested-test-example-1"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Outer left · Inner right') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $docExampleSource->example('flow-if-nested-test-example-1') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="flow-if-nested-test-example-2"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Outer right · Inner left') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $docExampleSource->example('flow-if-nested-test-example-2') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                    <code>elseifs</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('The last entry (deferred) opens its successful action for the inner IF. Earlier successful actions skip this entry.') }}
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
                                    {{ __('false omits the direct True return stem. The author connects the nested output to anchorNode-return.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>elseifs[].actionLabel.returnOffset</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>12rem for open ELSEIF</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('48rem in these examples places the opposite-facing inner block outside the outer return rail. The return bridge is calculated from the actual output anchors.') }}
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
                                    {{ __('Marks the explicit crossing over the previous successful action return stem.') }}
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
                                    {{ __('The right example places the jump on the preceding successful return stem, over the outgoing bridge of the last ELSEIF.') }}
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
                                    {{ __('Independent outer fallback: Keep draft. It reaches the shared end without entering the inner IF.') }}
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
                                    {{ __('The last False route reserves 60rem for both actions, the inner IF and the return. No following ELSEIF exists to provide this space.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>.elseif.deferred.true.anchorNode-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>last action output</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Entry to Prepare review data when the last outer ELSEIF is true.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>.elseif.deferred.true.anchorNode-return</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>common output</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('The sequence returns here only after Save review result has completed.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>.anchorNode-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>common continuation</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Continue after another successful outer action, the complete nested action sequence, or the outer fallback.') }}
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
                                    {{ __('Declares the nested return connection and propagates returnColor to the common continuation.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>prepare.anchorNode-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>action output</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Entry to the nested IF, after preparing review data.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inner.anchorNode-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>shared inner output</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Every inner result reaches the Save review result action.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>finish.anchorNode-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>action output</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Starts the return to the outer IF. Its rose color is carried onto the return.') }}
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
                    example="flow-if-nested-test-example-1"
                    size="sm"
                >{{ __('Outer left · Inner right · Line jump on bridge') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- flow-if-nested-test-example-1:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-step-08-flow-if-nested-test"
                        :dev="true"
                        :coordinates="true"
                        color="cyan"
                        min-width="108rem"
                        min-height="118rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.flow-start
                            id="literature.flow.1.if-nested-test-origin"
                            :start-label="['text' => ['Review request'], 'width' => 'half']"
                        />
                        {{-- Outer IF --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                            id="literature.flow.1.if-nested-test.outer"
                            attach-to="literature.flow.1.if-nested-test-origin.anchorNode-end"
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
                                    'actionLabel' => [
                                        'text' => ['Evaluate deferred review'],
                                        'width' => 'halfLong',
                                        'return' => false,
                                        'returnOffset' => '48rem',
                                        'lineJumps' => [
                                            [
                                                'over' => 'literature.flow.1.if-nested-test.outer.elseif.sources.true.stem',
                                                'radius' => '0.65rem',
                                                'side' => 'top',
                                            ],
                                        ],
                                    ],
                                ],
                            ]"
                            :if-end="[
                                'text' => ['Keep draft'],
                                'width' => 'halfLong',
                                'color' => 'zinc',
                                'stemLength' => '60rem',
                            ]"
                        />

                        {{-- This action starts the selected branch sequence. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.if-nested-test.prepare"
                            attach-to="literature.flow.1.if-nested-test.outer.elseif.deferred.true.anchorNode-end"
                            color="violet"
                            :step-label="['text' => ['Prepare review data'], 'width' => 'halfLong']"
                        />
                        {{-- The inner block is entered only when the last outer ELSEIF is true. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                            id="literature.flow.1.if-nested-test.inner"
                            attach-to="literature.flow.1.if-nested-test.prepare.anchorNode-end"
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

                        {{-- Every inner result proceeds to this action before returning to the outer IF. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.if-nested-test.finish"
                            attach-to="literature.flow.1.if-nested-test.inner.anchorNode-end"
                            color="rose"
                            :step-label="['text' => ['Save review result'], 'width' => 'halfLong']"
                        />

                        {{-- Return above the inner block, then join the outer output rail. --}}
                        @php
                            $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-test';
                            $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test.inner.anchorNode-end',
                            );
                            $sequenceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test.finish.anchorNode-end',
                            );
                            $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test.outer.elseif.deferred.true.anchorNode-return',
                            );
                            $returnBridge = 'calc(' . $outerReturn['x'] . ' - ' . $sequenceEnd['x'] . ' - (2 * 2.75rem))';
                        @endphp
                        <x-translation-workbench::ui.tw-graph.parts.sideways
                            id="literature.flow.1.if-nested-test.inner-return"
                            :anchor-start="$sequenceEnd"
                            side="left"
                            arc-radius="2.75rem"
                            :bridge-length="$returnBridge"
                            :color="$sequenceEnd['color']"
                            :node-end="false"
                            :joint-arrow-end="false"
                            :dev-counter-end="false"
                        />
                        @php
                            $returnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test.inner-return.anchorNode-end',
                            );
                        @endphp
                        <x-translation-workbench::ui.tw-graph.parts.start
                            id="literature.flow.1.if-nested-test.inner-return.stem"
                            return-to="literature.flow.1.if-nested-test.outer.elseif.deferred.true.anchorNode-return"
                            :anchor-start="$returnEnd"
                            :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                            :gradient="false"
                            :node-end="false"
                            :joint-arrow-end="false"
                            :dev-counter-end="false"
                            :color="$returnEnd['color']"
                        />
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.if-nested-test.continue"
                            attach-to="literature.flow.1.if-nested-test.outer.anchorNode-end"
                            color="fuchsia"
                            :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                        />
                        @php
                            $outerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test.outer.anchorNode-start',
                            );
                            $outerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test.outer.anchorNode-end',
                            );
                            $innerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test.inner.anchorNode-start',
                            );
                            $innerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                                [$innerInput, $innerEnd],
                                '10rem',
                            );
                            $outerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                                [$outerInput, $outerEnd, $innerEnd, $sequenceEnd],
                                '12rem',
                            );
                        @endphp
                        <x-translation-workbench::ui.tw-graph.dev-box
                            id="literature.flow.1.if-nested-test.inner.bounds"
                            label="Inner IF / ELSEIF / ELSE"
                            :x="$innerBounds['left']"
                            :y="$innerBounds['bottom']"
                            :width="$innerBounds['width']"
                            :height="$innerBounds['height']"
                            color="amber"
                        />
                        <x-translation-workbench::ui.tw-graph.dev-box
                            id="literature.flow.1.if-nested-test.outer.bounds"
                            label="Outer IF including nested section"
                            :x="$outerBounds['left']"
                            :y="$outerBounds['bottom']"
                            :width="$outerBounds['width']"
                            :height="$outerBounds['height']"
                            color="cyan"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- flow-if-nested-test-example-1:end --}}
                </div>
                <x-translation-workbench::ui.common.heading-counter
                    example="flow-if-nested-test-example-2"
                    size="sm"
                >{{ __('Outer right · Inner left · Line jump on stem') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- flow-if-nested-test-example-2:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-step-08-flow-if-nested-test-right"
                        :dev="true"
                        :coordinates="true"
                        color="cyan"
                        min-width="108rem"
                        min-height="118rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.flow-start
                            id="literature.flow.1.if-nested-test-right-origin"
                            :start-label="['text' => ['Review request'], 'width' => 'half']"
                        />
                        {{-- Outer IF --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                            id="literature.flow.1.if-nested-test-right.outer"
                            attach-to="literature.flow.1.if-nested-test-right-origin.anchorNode-end"
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
                                        'stemLineJumps' => [
                                            [
                                                'over' =>
                                                    'literature.flow.1.if-nested-test-right.outer.elseif.deferred.true.bridge1.bridge-out',
                                                'radius' => '0.65rem',
                                                'side' => 'right',
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
                                        'returnOffset' => '48rem',
                                    ],
                                ],
                            ]"
                            :if-end="[
                                'text' => ['Keep draft'],
                                'width' => 'halfLong',
                                'color' => 'zinc',
                                'stemLength' => '60rem',
                            ]"
                        />

                        {{-- This action starts the selected branch sequence. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.if-nested-test-right.prepare"
                            attach-to="literature.flow.1.if-nested-test-right.outer.elseif.deferred.true.anchorNode-end"
                            color="violet"
                            :step-label="['text' => ['Prepare review data'], 'width' => 'halfLong']"
                        />
                        {{-- The inner block is entered only when the last outer ELSEIF is true. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                            id="literature.flow.1.if-nested-test-right.inner"
                            attach-to="literature.flow.1.if-nested-test-right.prepare.anchorNode-end"
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

                        {{-- Every inner result proceeds to this action before returning to the outer IF. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.if-nested-test-right.finish"
                            attach-to="literature.flow.1.if-nested-test-right.inner.anchorNode-end"
                            color="rose"
                            :step-label="['text' => ['Save review result'], 'width' => 'halfLong']"
                        />

                        {{-- Return above the inner block, then join the outer output rail. --}}
                        @php
                            $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-test-right';
                            $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test-right.inner.anchorNode-end',
                            );
                            $sequenceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test-right.finish.anchorNode-end',
                            );
                            $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test-right.outer.elseif.deferred.true.anchorNode-return',
                            );
                            $returnBridge = 'calc(' . $sequenceEnd['x'] . ' - ' . $outerReturn['x'] . ' - (2 * 2.75rem))';
                        @endphp
                        <x-translation-workbench::ui.tw-graph.parts.sideways
                            id="literature.flow.1.if-nested-test-right.inner-return"
                            :anchor-start="$sequenceEnd"
                            side="right"
                            arc-radius="2.75rem"
                            :bridge-length="$returnBridge"
                            :color="$sequenceEnd['color']"
                            :node-end="false"
                            :joint-arrow-end="false"
                            :dev-counter-end="false"
                        />
                        @php
                            $returnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test-right.inner-return.anchorNode-end',
                            );
                        @endphp
                        <x-translation-workbench::ui.tw-graph.parts.start
                            id="literature.flow.1.if-nested-test-right.inner-return.stem"
                            return-to="literature.flow.1.if-nested-test-right.outer.elseif.deferred.true.anchorNode-return"
                            :anchor-start="$returnEnd"
                            :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                            :gradient="false"
                            :node-end="false"
                            :joint-arrow-end="false"
                            :dev-counter-end="false"
                            :color="$returnEnd['color']"
                        />
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.if-nested-test-right.continue"
                            attach-to="literature.flow.1.if-nested-test-right.outer.anchorNode-end"
                            color="fuchsia"
                            :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                        />
                        @php
                            $outerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test-right.outer.anchorNode-start',
                            );
                            $outerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test-right.outer.anchorNode-end',
                            );
                            $innerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-test-right.inner.anchorNode-start',
                            );
                            $innerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                                [$innerInput, $innerEnd],
                                '10rem',
                            );
                            $outerBounds = \Gunreip\TranslationWorkbench\Support\TwGraphProtocol\GeometryBounds::fromPoints(
                                [$outerInput, $outerEnd, $innerEnd, $sequenceEnd],
                                '12rem',
                            );
                        @endphp
                        <x-translation-workbench::ui.tw-graph.dev-box
                            id="literature.flow.1.if-nested-test-right.inner.bounds"
                            label="Inner IF / ELSEIF / ELSE"
                            :x="$innerBounds['left']"
                            :y="$innerBounds['bottom']"
                            :width="$innerBounds['width']"
                            :height="$innerBounds['height']"
                            color="amber"
                        />
                        <x-translation-workbench::ui.tw-graph.dev-box
                            id="literature.flow.1.if-nested-test-right.outer.bounds"
                            label="Outer IF including nested section"
                            :x="$outerBounds['left']"
                            :y="$outerBounds['bottom']"
                            :width="$outerBounds['width']"
                            :height="$outerBounds['height']"
                            color="cyan"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- flow-if-nested-test-example-2:end --}}
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../flow/if/flow-if-nested-test.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
