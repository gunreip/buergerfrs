<x-translation-workbench::ui.common.heading-counter-group group="flow-if-nested-8">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('IF nested 8') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.if.flow-if-nested-8" />
            <flux:callout.text>
                {{ __('Mixed sides: an outer left IF contains an inner right IF, and the second example reverses both sides. The last outer ELSEIF opens the inner block. Its wider outgoing bridge reserves space for the opposite-facing actions. Each inner return rejoins its own outer output; explicit line-jumps distinguish crossings from connections.') }}
            </flux:callout.text>
            @php
                $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-nested-8',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="flow-if-nested-8-example-1"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Outer left · Inner right') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $docExampleSource->example('flow-if-nested-8-example-1') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="flow-if-nested-8-example-2"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Outer right · Inner left') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $docExampleSource->example('flow-if-nested-8-example-2') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                    {{ __('The last False route reserves 40rem here for the inner IF and its return. No following ELSEIF exists to provide this space.') }}
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
                                    {{ __('Entry for the inner IF when the last outer ELSEIF is true.') }}
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
                                    {{ __('Direct return target at the shared outer end, not at a following ELSEIF action.') }}
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
                                    {{ __('Continue after any successful outer action, completion of the inner IF, or the outer fallback.') }}
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
                    example="flow-if-nested-8-example-1"
                    size="sm"
                >{{ __('Outer left · Inner right · Line jump on bridge') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- flow-if-nested-8-example-1:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-step-08-flow-if-nested-8"
                        :dev="true"
                        :coordinates="true"
                        color="cyan"
                        min-width="108rem"
                        min-height="98rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.flow-start
                            id="literature.flow.1.if-nested-8-origin"
                            :start-label="['text' => ['Review request'], 'width' => 'half']"
                        />
                        {{-- Outer IF --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                            id="literature.flow.1.if-nested-8.outer"
                            attach-to="literature.flow.1.if-nested-8-origin.anchorNode-end"
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
                                                'over' => 'literature.flow.1.if-nested-8.outer.elseif.sources.true.stem',
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
                                'stemLength' => '40rem',
                            ]"
                        />

                        {{-- The inner block is entered only when the last outer ELSEIF is true. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                            id="literature.flow.1.if-nested-8.inner"
                            attach-to="literature.flow.1.if-nested-8.outer.elseif.deferred.true.anchorNode-end"
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
                            $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-8';
                            $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-8.inner.anchorNode-end',
                            );
                            $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-8.outer.elseif.deferred.true.anchorNode-return',
                            );
                            $returnBridge = 'calc(' . $outerReturn['x'] . ' - ' . $innerEnd['x'] . ' - (2 * 2.75rem))';
                        @endphp
                        <x-translation-workbench::ui.tw-graph.parts.sideways
                            id="literature.flow.1.if-nested-8.inner-return"
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
                                'literature.flow.1.if-nested-8.inner-return.anchorNode-end',
                            );
                        @endphp
                        <x-translation-workbench::ui.tw-graph.parts.start
                            id="literature.flow.1.if-nested-8.inner-return.stem"
                            return-to="literature.flow.1.if-nested-8.outer.elseif.deferred.true.anchorNode-return"
                            :anchor-start="$returnEnd"
                            :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                            :gradient="false"
                            :node-end="false"
                            :joint-arrow-end="false"
                            :dev-counter-end="false"
                            :color="$returnEnd['color']"
                        />
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.if-nested-8.continue"
                            attach-to="literature.flow.1.if-nested-8.outer.anchorNode-end"
                            color="fuchsia"
                            :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                        />
                        @php
                            $outerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-8.outer.anchorNode-start',
                            );
                            $outerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-8.outer.anchorNode-end',
                            );
                            $innerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-8.inner.anchorNode-start',
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
                            id="literature.flow.1.if-nested-8.inner.bounds"
                            label="Inner IF / ELSEIF / ELSE"
                            :x="$innerBounds['left']"
                            :y="$innerBounds['bottom']"
                            :width="$innerBounds['width']"
                            :height="$innerBounds['height']"
                            color="amber"
                        />
                        <x-translation-workbench::ui.tw-graph.dev-box
                            id="literature.flow.1.if-nested-8.outer.bounds"
                            label="Outer IF including nested section"
                            :x="$outerBounds['left']"
                            :y="$outerBounds['bottom']"
                            :width="$outerBounds['width']"
                            :height="$outerBounds['height']"
                            color="cyan"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- flow-if-nested-8-example-1:end --}}
                </div>
                <x-translation-workbench::ui.common.heading-counter
                    example="flow-if-nested-8-example-2"
                    size="sm"
                >{{ __('Outer right · Inner left · Line jump on stem') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- flow-if-nested-8-example-2:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-step-08-flow-if-nested-8-right"
                        :dev="true"
                        :coordinates="true"
                        color="cyan"
                        min-width="108rem"
                        min-height="98rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.flow-start
                            id="literature.flow.1.if-nested-8-right-origin"
                            :start-label="['text' => ['Review request'], 'width' => 'half']"
                        />
                        {{-- Outer IF --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                            id="literature.flow.1.if-nested-8-right.outer"
                            attach-to="literature.flow.1.if-nested-8-right-origin.anchorNode-end"
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
                                                    'literature.flow.1.if-nested-8-right.outer.elseif.deferred.true.bridge1.bridge-out',
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
                                'stemLength' => '40rem',
                            ]"
                        />

                        {{-- The inner block is entered only when the last outer ELSEIF is true. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                            id="literature.flow.1.if-nested-8-right.inner"
                            attach-to="literature.flow.1.if-nested-8-right.outer.elseif.deferred.true.anchorNode-end"
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
                            $nestedGraphId = 'idea-to-paper-step-08-flow-if-nested-8-right';
                            $innerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-8-right.inner.anchorNode-end',
                            );
                            $outerReturn = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-8-right.outer.elseif.deferred.true.anchorNode-return',
                            );
                            $returnBridge = 'calc(' . $innerEnd['x'] . ' - ' . $outerReturn['x'] . ' - (2 * 2.75rem))';
                        @endphp
                        <x-translation-workbench::ui.tw-graph.parts.sideways
                            id="literature.flow.1.if-nested-8-right.inner-return"
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
                                'literature.flow.1.if-nested-8-right.inner-return.anchorNode-end',
                            );
                        @endphp
                        <x-translation-workbench::ui.tw-graph.parts.start
                            id="literature.flow.1.if-nested-8-right.inner-return.stem"
                            return-to="literature.flow.1.if-nested-8-right.outer.elseif.deferred.true.anchorNode-return"
                            :anchor-start="$returnEnd"
                            :length="'calc(' . $outerReturn['y'] . ' - ' . $returnEnd['y'] . ')'"
                            :gradient="false"
                            :node-end="false"
                            :joint-arrow-end="false"
                            :dev-counter-end="false"
                            :color="$returnEnd['color']"
                        />
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.if-nested-8-right.continue"
                            attach-to="literature.flow.1.if-nested-8-right.outer.anchorNode-end"
                            color="fuchsia"
                            :step-label="['text' => ['Continue process'], 'width' => 'halfLong']"
                        />
                        @php
                            $outerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-8-right.outer.anchorNode-start',
                            );
                            $outerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-8-right.outer.anchorNode-end',
                            );
                            $innerInput = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                $nestedGraphId,
                                'literature.flow.1.if-nested-8-right.inner.anchorNode-start',
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
                            id="literature.flow.1.if-nested-8-right.inner.bounds"
                            label="Inner IF / ELSEIF / ELSE"
                            :x="$innerBounds['left']"
                            :y="$innerBounds['bottom']"
                            :width="$innerBounds['width']"
                            :height="$innerBounds['height']"
                            color="amber"
                        />
                        <x-translation-workbench::ui.tw-graph.dev-box
                            id="literature.flow.1.if-nested-8-right.outer.bounds"
                            label="Outer IF including nested section"
                            :x="$outerBounds['left']"
                            :y="$outerBounds['bottom']"
                            :width="$outerBounds['width']"
                            :height="$outerBounds['height']"
                            color="cyan"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- flow-if-nested-8-example-2:end --}}
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../flow/if/flow-if-nested-8.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
