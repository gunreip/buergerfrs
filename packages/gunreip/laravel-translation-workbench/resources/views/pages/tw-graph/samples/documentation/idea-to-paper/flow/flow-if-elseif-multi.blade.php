<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('IF ELSEIF multi') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('IF ELSEIF multi supports one or more individually defined ELSEIF branches. Each False output reaches the next condition; the first matching condition executes its action and skips all later tests. If none matches, the final plain bypass reaches Continue process. Both examples explicitly list three ELSEIF branches with stable keys, individual colors and different action widths.') }}
        </flux:callout.text>
        @php
            $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.flow-if-elseif-multi',
            );
            $docExample1Code = $docExampleSource->example('flow-if-elseif-multi-example-1');
            $docExample2Code = $docExampleSource->example('flow-if-elseif-multi-example-2');
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
        >{{ __('Props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">generated
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stable prefix;
                            .anchorNode-end is the shared continuation.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">component-counter
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">1</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Counter used to
                            generate the fallback ID.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">attach-to
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Registered incoming
                            anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchor-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt;
                            &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Fallback input
                            coordinates.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left or right: both
                            actions and the bypass travel in the same direction.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top or
                            top-bottom.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">condition-label
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">IF condition?; halfLong
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">First decision; text or
                            label configuration.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">if-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">IF action; half
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Action executed when
                            the first condition is true. Its resolved color also colors the complete shared return stem.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">elseifs
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">required, at least one
                            entry</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Ordered branch
                            definitions: key (unique; defaults to position), conditionLabel, actionLabel, color,
                            beforeLength and afterLength. conditionLabel.color sets the branch color; actionLabel.color
                            overrides the action route. The incoming question stem retains the preceding anchor color.
                            badgeColor remains independent. Branch IDs use .elseif.KEY.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">before-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Line before the first
                            condition.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">after-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Line after the first
                            condition.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">elseif-before-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">6rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Line from the first
                            False output to the second condition.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">elseif-after-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Default line after each
                            ELSEIF condition; branch afterLength overrides it.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">8rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Distance between the
                            second action and the final bypass; expands for multiline labels.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-radius
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">canvas arc-size /
                            2.75rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Radius of the route
                            arcs.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length around each
                            action label. The total route width follows the wider label; the bypass spans that same
                            width.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">node-labels
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional left/right
                            labels at the shared output.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">node-end
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">true</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Show the common
                            endpoint.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited / zinc
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Default route color.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dev-mode
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Override the canvas DEV
                            mode. Two counters per condition identify its decision and action output, followed by two
                            counters for the final bypass and common end.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">20</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stacking order.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">if-end
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
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
                class="mt-4"
                size="sm"
            >side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-if-elseif-multi-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-if-elseif-multi"
                    :dev="true"
                    :coordinates="true"
                    min-width="56rem"
                    min-height="68rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-elseif-multi"
                        :anchor-start="['x' => '0rem', 'y' => '3rem']"
                        side="left"
                        direction="bottom-top"
                        color="cyan"
                        arc-radius="2.75rem"
                        bridge-length="2rem"
                        before-length="2rem"
                        after-length="2rem"
                        {{-- elseif-before-length="4rem" --}}
                        {{-- elseif-after-length="2rem" --}}
                        stem-length="4rem"
                        :if-end="['color' => 'zinc']"
                        :condition-label="[
                            'text' => ['IF reviewApproved?'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                        :if-start="[
                            'color' => 'green',
                            'text' => ['Publish paper'],
                            'width' => 'default',
                            'align' => 'center',
                            'badgeColor' => 'green',
                        ]"
                        :elseifs="[
                            [
                                'key' => 'changes',
                                // 'beforeLength' => '4rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF changesRequested?'],
                                    'width' => 'halfLong',
                                    'color' => 'violet',
                                ],
                                'actionLabel' => [
                                    'text' => ['Revise draft'],
                                    'width' => 'half',
                                ],
                            ],
                            [
                                'key' => 'sources',
                                // 'beforeLength' => '3.5rem',
                                // 'afterLength' => '2rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF missingSources?'],
                                    'width' => 'halfLong',
                                    'color' => 'amber',
                                ],
                                'actionLabel' => [
                                    'text' => ['Add sources', 'Update citations'],
                                    'width' => 'halfLong',
                                ],
                            ],
                            [
                                'key' => 'review',
                                // 'beforeLength' => '4.0rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF awaitingReview?'],
                                    'width' => 'halfLong',
                                    'color' => 'blue',
                                ],
                                'actionLabel' => [
                                    'text' => ['Request review'],
                                    'width' => 'half',
                                ],
                            ],
                        ]"
                        :if-end="[
                            'color' => 'red',
                        ]"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.flow.1.if-elseif-multi.continue"
                        attach-to="literature.flow.1.if-elseif-multi.anchorNode-end"
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
                {{-- flow-if-elseif-multi-example-1:end --}}
            </div>
            <flux:heading
                class="mt-4"
                size="sm"
            >side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- flow-if-elseif-multi-example-2:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-flow-if-elseif-multi-right"
                    :dev="true"
                    :coordinates="true"
                    min-width="56rem"
                    min-height="68rem"
                    horizontal-padding="6rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif-multi
                        id="literature.flow.1.if-elseif-multi-right"
                        :anchor-start="['x' => '0rem', 'y' => '3rem']"
                        side="right"
                        direction="bottom-top"
                        color="cyan"
                        arc-radius="2.75rem"
                        bridge-length="2rem"
                        before-length="2rem"
                        after-length="2rem"
                        elseif-before-length="3rem"
                        elseif-after-length="2rem"
                        stem-length="4rem"
                        :if-end="['color' => 'red']"
                        :condition-label="[
                            'text' => ['IF reviewApproved?'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                        :if-start="[
                            'color' => 'green',
                            'text' => ['Publish paper'],
                            'width' => 'half',
                            'align' => 'center',
                            'badgeColor' => 'green',
                        ]"
                        :elseifs="[
                            [
                                'key' => 'changes',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF changesRequested?'],
                                    'width' => 'halfLong',
                                    'color' => 'violet',
                                ],
                                'actionLabel' => [
                                    'text' => ['Revise draft'],
                                    'width' => 'half',
                                ],
                            ],
                            [
                                'key' => 'sources',
                                'beforeLength' => '8rem',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF missingSources?'],
                                    'width' => 'halfLong',
                                    'color' => 'amber',
                                ],
                                'actionLabel' => [
                                    'text' => ['Add sources', 'Update citations'],
                                    'width' => 'halfLong',
                                ],
                            ],
                            [
                                'key' => 'review',
                                'conditionLabel' => [
                                    'text' => ['ELSEIF awaitingReview?'],
                                    'width' => 'halfLong',
                                    'color' => 'blue',
                                ],
                                'actionLabel' => [
                                    'text' => ['Request review'],
                                    'width' => 'half',
                                ],
                            ],
                        ]"
                    />
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.flow.1.if-elseif-multi-right.continue"
                        attach-to="literature.flow.1.if-elseif-multi-right.anchorNode-end"
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
                {{-- flow-if-elseif-multi-example-2:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../flow/flow-if-elseif-multi.blade.php
        </flux:field>
    </flux:callout>
</section>
