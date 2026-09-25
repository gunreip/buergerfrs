<x-translation-workbench::ui.common.heading-counter-group group="flow-while-action-sequence">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Action → Nested WHILE → Action') }}</flux:callout.heading>
            <flux:callout.text>{{ __('Prepare each group before entering its item loop. Process its items, then finalize the group after the inner FALSE exit. Finalization also runs for an empty group. Only after finalization does the group index advance and the outer WHILE check its condition again. Preparation and finalization each run once per outer iteration; the inner return repeats neither action.') }}</flux:callout.text>
            @php
                $whileSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-action-sequence',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="while-action-sequence-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $whileSource->example('while-action-sequence-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="while-action-sequence-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $whileSource->example('while-action-sequence-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.while.flow-while-action-sequence" />
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
                                    <code>side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>left</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Physical side of the body and return: left or right. The FALSE exit stays on the main axis.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Connect the loop after initialization. The return rejoins here without repeating initialization.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>condition-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>WHILE pending items?</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Condition text, width, align and badge color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>action-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>Process next item</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('First action inside the horizontal body bridge. The separate advance step makes progress.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>condition-label.beforeLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Stem before the condition.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>condition-label.labelGap</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Space reserved for the condition text.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>condition-label.afterLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Stem between the condition and the TRUE/FALSE split: 6rem outside, 8rem inside.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>arc-radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inherited / 2.75rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Common radius of the four loop corners.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true-bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Separate bridge after the TRUE arc. The TRUE label is attached to the condition here; the return bridge grows by the same length.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>action-label.beforeLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Bridge before the body action.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>action-label.afterLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Bridge after the body action: 16rem outside reserves the separate return lane and its crossing; 2rem inside.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('FALSE stem to the public exit. The independent return uses the remaining height after its body. Each loop has its own exit and return.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>TRUE</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Information at the body entry; text, width, align, side and connector settings.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>FALSE</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Information at the loop exit; text, width, align, side and connector settings.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inherited / zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Loop paths and labels; explicit label colors remain configurable.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchorNode-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>connection</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Initialization and return meet before the condition.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchorNode-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>connection</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('FALSE exit. Attach the next independent action here.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>action-label.return</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicitly false here: leave the body open for the independent nested loop or advance action. Both automatic returns are omitted. inner-entry uses the same rounded path to reverse the incoming direction; its 12rem horizontal and 8rem vertical offsets are explicit.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>body.anchorNode-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>connection</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Outer body: inner-entry turns down-left-up toward the inner WHILE. Inner body: attach the item increment with direction top-bottom.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchorNode-return</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>connection</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Target before the condition. paths.loop-return connects the final body action to this target; its remaining stem length comes from both anchors. Insufficient room produces an error instead of moving the condition.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>node-end-dot</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true (flow-step)</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicitly false for the advance action: no Dot at this technical connection.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>joint-arrow-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicitly true for the advance action: a downward joint-arrow connects to the return stem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>action-label.color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inherited</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Color of the complete TRUE action route, including both arcs and the entry bridge. Explicitly green here.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true-label.anchor</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('condition attaches TRUE to the decision Dot; bridge keeps its original position at the entry bridge end.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true-label.side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>top</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Right in the left example, left in the right example.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false-label.color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inherited</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Colors the FALSE stem, its end Dot and DEV counter. Explicitly red here.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>action-label.lineJumps</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('The outer action\'s outgoing bridge jumps over body-return.stem. The crossing is not a connection.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>outer-resume arcs / advance</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>explicit composition</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Two segments.arc using the canvas radius (default 2.75rem) turn the finalization exit into a horizontal flow-step, then downward into the outer return. Its label-gap of 16rem sets the horizontal separation; both arc endpoint calculations are visible in the example.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>counter-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>1 (flow-while)</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('First DEV counter of this WHILE. The open loop uses six numbers, a closed loop ten. Steps, manual arcs and independent returns specify their following counter values explicitly in this example.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>finalize.attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('The inner FALSE exit starts finalization even for zero inner iterations. Its before-length and after-length are explicitly 4rem. The outer index advance follows finalize.anchorNode-end; the inner return bypasses finalization.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-action-sequence"
                example="while-action-sequence"
            />
        </flux:callout>
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Action → Nested WHILE → Action') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <x-translation-workbench::ui.common.heading-counter
                    example="while-action-sequence-left-example"
                    size="sm"
                >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- while-action-sequence-left-example:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-while-action-sequence-left"
                        :dev="true"
                        :coordinates="true"
                        horizontalPadding="6rem"
                        min-height="60rem"
                        min-width="88rem"
                    >
                        {{-- Initialization runs once. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.while.action-sequence.left.initialize"
                            :anchor-start="['x' => '0rem', 'y' => '2rem']"
                            :step-label="['text' => ['Load groups', 'groupIndex = 0'], 'width' => 'default']"
                            afterLength="5rem"
                            color="zinc"
                            :node-end="false"
                        />
                        {{-- Each outer iteration selects a group and resets its inner index. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-while
                            id="literature.while.action-sequence.left.loop"
                            :counter-start="1"
                            attach-to="literature.while.action-sequence.left.initialize.anchorNode-end"
                            side="left"
                            color="cyan"
                            true-bridge-length="4rem"
                            stem-length="3.5rem"
                            :condition-label="[
                                'beforeLength' => '2rem',
                                'labelGap' => '4rem',
                                'afterLength' => '6rem',
                                'text' => ['WHILE groupIndex < groupCount?'],
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                            :action-label="[
                                'beforeLength' => '2rem',
                                'afterLength' => '16rem',
                                'lineJumps' => [
                                    ['over' => 'literature.while.action-sequence.left.body-return.stem', 'radius' => '0.65rem', 'side' =>
                                        'top'
                                    ],
                                ],
                                'text' => ['Prepare group', 'itemIndex = 0'],
                                'color' => 'green',
                                'return' => false,
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                            :true-label="[
                                'text' => ['TRUE'],
                                'width' => 'half',
                                'anchor' => 'condition',
                                'side' => 'left',
                                'color' => 'green',
                            ]"
                            :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red']"
                        />
                        {{-- Turn the downward body exit into the upward inner condition. --}}
                        @php
                            $leftOuterBodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                'idea-to-paper-while-action-sequence-left',
                                'literature.while.action-sequence.left.loop.body.anchorNode-end',
                            );
                            $leftInnerStart = [
                                'x' => 'calc(' . $leftOuterBodyEnd['x'] . ' - 12rem)',
                                'y' => 'calc(' . $leftOuterBodyEnd['y'] . ' - 8rem)',
                            ];
                        @endphp
                        <x-translation-workbench::ui.tw-graph.paths.loop-return
                            id="literature.while.action-sequence.left.inner-entry"
                            :counter-start="7"
                            attach-to="literature.while.action-sequence.left.loop.body.anchorNode-end"
                            :anchor-return="$leftInnerStart"
                            side="right"
                            color="green"
                        />
                        {{-- The inner return skips initialization, so itemIndex is not reset on every item. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-while
                            id="literature.while.action-sequence.left.inner-loop"
                            :counter-start="11"
                            :anchor-start="$leftInnerStart"
                            side="left"
                            color="sky"
                            true-bridge-length="4rem"
                            stem-length="4rem"
                            :condition-label="[
                                'beforeLength' => '2rem',
                                'labelGap' => '4rem',
                                'afterLength' => '8rem',
                                'text' => ['WHILE itemIndex < itemCount?'],
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                            :action-label="[
                                'beforeLength' => '2rem',
                                'afterLength' => '2rem',
                                'text' => ['Process current item'],
                                'color' => 'green',
                                'return' => false,
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                            :true-label="[
                                'text' => ['TRUE'], 'width' => 'half',
                                'anchor' => 'condition', 'side' => 'left', 'color' => 'green',
                            ]"
                            :false-label="[
                                'text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red',
                            ]"
                        />
                        {{-- Advance only the inner index, then recheck the inner condition. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.while.action-sequence.left.inner-advance"
                            :counter-end="17"
                            attach-to="literature.while.action-sequence.left.inner-loop.body.anchorNode-end"
                            direction="top-bottom"
                            before-length="2rem"
                            label-gap="4rem"
                            after-length="2rem"
                            :step-label="['text' => ['itemIndex = itemIndex + 1'], 'width' => 'default']"
                            :node-end-dot="false"
                            :joint-arrow-end="true"
                            color="sky"
                        />
                        <x-translation-workbench::ui.tw-graph.paths.loop-return
                            id="literature.while.action-sequence.left.inner-return"
                            :counter-start="18"
                            attach-to="literature.while.action-sequence.left.inner-advance.anchorNode-end"
                            return-to="literature.while.action-sequence.left.inner-loop.anchorNode-return"
                            side="left"
                            color="sky"
                        />
                        {{-- Inner FALSE always finalizes this group, including when it had no items. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.while.action-sequence.left.finalize"
                            attach-to="literature.while.action-sequence.left.inner-loop.anchorNode-end"
                            before-length="4rem"
                            label-gap="4rem"
                            after-length="4rem"
                            :step-label="['text' => ['Finalize group'], 'width' => 'default']"
                            :counter-end="22"
                            :node-end-dot="false"
                            :joint-arrow-end="true"
                            color="amber"
                        />
                        {{-- Turn the finalized group toward the separate outer return lane. --}}
                        @php
                            $leftInnerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                'idea-to-paper-while-action-sequence-left',
                                'literature.while.action-sequence.left.finalize.anchorNode-end',
                            );
                            $leftResumeArcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString(
                                'arc_radius',
                                '2.75rem',
                            );
                            $leftAdvanceStart = [
                                'x' => 'calc(' . $leftInnerExit['x'] . ' + ' . $leftResumeArcRadius . ')',
                                'y' => 'calc(' . $leftInnerExit['y'] . ' + ' . $leftResumeArcRadius . ')',
                            ];
                        @endphp
                        <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                            'id' => 'literature.while.action-sequence.left.outer-resume.arc-in',
                            'devCounterEnd' => 23,
                            'anchorStart' => $leftInnerExit,
                            'anchorEnd' => $leftAdvanceStart,
                            'startAnchor' => 'w',
                            'endAnchor' => 'n',
                            'arcRadius' => $leftResumeArcRadius,
                            'color' => 'cyan',

                            'nodeEnd' => true,
                            'nodeEndDot' => false,
                            'jointArrowEnd' => true,
                            'jointArrowEndDirection' => 'right',
                        ]" />
                        {{-- Advance the outer index only after finalization. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.while.action-sequence.left.advance"
                            :counter-end="24"
                            :anchor-start="$leftAdvanceStart"
                            direction="left-right"
                            before-length="2rem"
                            label-gap="16rem"
                            after-length="2rem"
                            :step-label="['text' => ['groupIndex = groupIndex + 1'], 'width' => 'default']"
                            :node-end-dot="false"
                            :joint-arrow-end="true"
                            color="cyan"
                        />
                        @php
                            $leftAdvanceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                'idea-to-paper-while-action-sequence-left',
                                'literature.while.action-sequence.left.advance.anchorNode-end',
                            );
                            $leftOuterReturnStart = [
                                'x' => 'calc(' . $leftAdvanceEnd['x'] . ' + ' . $leftResumeArcRadius . ')',
                                'y' => 'calc(' . $leftAdvanceEnd['y'] . ' - ' . $leftResumeArcRadius . ')',
                            ];
                        @endphp
                        <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                            'id' => 'literature.while.action-sequence.left.outer-resume.arc-out',
                            'devCounterEnd' => 25,
                            'anchorStart' => $leftAdvanceEnd,
                            'anchorEnd' => $leftOuterReturnStart,
                            'startAnchor' => 'n',
                            'endAnchor' => 'e',
                            'arcRadius' => $leftResumeArcRadius,
                            'color' => 'cyan',

                            'nodeEnd' => true,
                            'nodeEndDot' => false,
                            'jointArrowEnd' => true,
                            'jointArrowEndDirection' => 'bottom',
                        ]" />
                        {{-- Return only after advancing, to the same condition (not initialization). --}}
                        <x-translation-workbench::ui.tw-graph.paths.loop-return
                            id="literature.while.action-sequence.left.body-return"
                            :counter-start="26"
                            :anchor-start="$leftOuterReturnStart"
                            return-to="literature.while.action-sequence.left.loop.anchorNode-return"
                            side="left"
                            color="cyan"
                        />
                        {{-- FALSE continues here, including when the group list starts empty. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.while.action-sequence.left.continue"
                            :counter-end="30"
                            attach-to="literature.while.action-sequence.left.loop.anchorNode-end"
                            beforeLength="4rem"
                            :step-label="['text' => ['Show summary'], 'width' => 'default']"
                            color="violet"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- while-action-sequence-left-example:end --}}
                </div>
                <x-translation-workbench::ui.common.heading-counter
                    example="while-action-sequence-right-example"
                    size="sm"
                >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- while-action-sequence-right-example:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-while-action-sequence-right"
                        :dev="true"
                        :coordinates="true"
                        horizontalPadding="6rem"
                        min-height="60rem"
                        min-width="88rem"
                    >
                        {{-- Initialization runs once. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.while.action-sequence.right.initialize"
                            :anchor-start="['x' => '0rem', 'y' => '2rem']"
                            :step-label="['text' => ['Load groups', 'groupIndex = 0'], 'width' => 'default']"
                            afterLength="5rem"
                            color="zinc"
                            :node-end="false"
                        />
                        {{-- Each outer iteration selects a group and resets its inner index. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-while
                            id="literature.while.action-sequence.right.loop"
                            :counter-start="1"
                            attach-to="literature.while.action-sequence.right.initialize.anchorNode-end"
                            side="right"
                            color="cyan"
                            true-bridge-length="4rem"
                            stem-length="3.5rem"
                            :condition-label="[
                                'beforeLength' => '2rem',
                                'labelGap' => '4rem',
                                'afterLength' => '6rem',
                                'text' => ['WHILE groupIndex < groupCount?'],
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                            :action-label="[
                                'beforeLength' => '2rem',
                                'afterLength' => '16rem',
                                'lineJumps' => [
                                    ['over' => 'literature.while.action-sequence.right.body-return.stem', 'radius' => '0.65rem', 'side' =>
                                        'top'
                                    ],
                                ],
                                'text' => ['Prepare group', 'itemIndex = 0'],
                                'color' => 'green',
                                'return' => false,
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                            :true-label="[
                                'text' => ['TRUE'],
                                'width' => 'half',
                                'anchor' => 'condition',
                                'side' => 'left',
                                'color' => 'green',
                            ]"
                            :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red']"
                        />
                        {{-- Turn the downward body exit into the upward inner condition. --}}
                        @php
                            $rightOuterBodyEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                'idea-to-paper-while-action-sequence-right',
                                'literature.while.action-sequence.right.loop.body.anchorNode-end',
                            );
                            $rightInnerStart = [
                                'x' => 'calc(' . $rightOuterBodyEnd['x'] . ' + 12rem)',
                                'y' => 'calc(' . $rightOuterBodyEnd['y'] . ' - 8rem)',
                            ];
                        @endphp
                        <x-translation-workbench::ui.tw-graph.paths.loop-return
                            id="literature.while.action-sequence.right.inner-entry"
                            :counter-start="7"
                            attach-to="literature.while.action-sequence.right.loop.body.anchorNode-end"
                            :anchor-return="$rightInnerStart"
                            side="left"
                            color="green"
                        />
                        {{-- The inner return skips initialization, so itemIndex is not reset on every item. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-while
                            id="literature.while.action-sequence.right.inner-loop"
                            :counter-start="11"
                            :anchor-start="$rightInnerStart"
                            side="right"
                            color="sky"
                            true-bridge-length="4rem"
                            stem-length="4rem"
                            :condition-label="[
                                'beforeLength' => '2rem',
                                'labelGap' => '4rem',
                                'afterLength' => '8rem',
                                'text' => ['WHILE itemIndex < itemCount?'],
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                            :action-label="[
                                'beforeLength' => '2rem',
                                'afterLength' => '2rem',
                                'text' => ['Process current item'],
                                'color' => 'green',
                                'return' => false,
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                            :true-label="[
                                'text' => ['TRUE'], 'width' => 'half',
                                'anchor' => 'condition', 'side' => 'left', 'color' => 'green',
                            ]"
                            :false-label="[
                                'text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red',
                            ]"
                        />
                        {{-- Advance only the inner index, then recheck the inner condition. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.while.action-sequence.right.inner-advance"
                            :counter-end="17"
                            attach-to="literature.while.action-sequence.right.inner-loop.body.anchorNode-end"
                            direction="top-bottom"
                            before-length="2rem"
                            label-gap="4rem"
                            after-length="2rem"
                            :step-label="['text' => ['itemIndex = itemIndex + 1'], 'width' => 'default']"
                            :node-end-dot="false"
                            :joint-arrow-end="true"
                            color="sky"
                        />
                        <x-translation-workbench::ui.tw-graph.paths.loop-return
                            id="literature.while.action-sequence.right.inner-return"
                            :counter-start="18"
                            attach-to="literature.while.action-sequence.right.inner-advance.anchorNode-end"
                            return-to="literature.while.action-sequence.right.inner-loop.anchorNode-return"
                            side="right"
                            color="sky"
                        />
                        {{-- Inner FALSE always finalizes this group, including when it had no items. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.while.action-sequence.right.finalize"
                            attach-to="literature.while.action-sequence.right.inner-loop.anchorNode-end"
                            before-length="4rem"
                            label-gap="4rem"
                            after-length="4rem"
                            :step-label="['text' => ['Finalize group'], 'width' => 'default']"
                            :counter-end="22"
                            :node-end-dot="false"
                            :joint-arrow-end="true"
                            color="amber"
                        />
                        {{-- Turn the finalized group toward the separate outer return lane. --}}
                        @php
                            $rightInnerExit = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                'idea-to-paper-while-action-sequence-right',
                                'literature.while.action-sequence.right.finalize.anchorNode-end',
                            );
                            $rightResumeArcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString(
                                'arc_radius',
                                '2.75rem',
                            );
                            $rightAdvanceStart = [
                                'x' => 'calc(' . $rightInnerExit['x'] . ' - ' . $rightResumeArcRadius . ')',
                                'y' => 'calc(' . $rightInnerExit['y'] . ' + ' . $rightResumeArcRadius . ')',
                            ];
                        @endphp
                        <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                            'id' => 'literature.while.action-sequence.right.outer-resume.arc-in',
                            'devCounterEnd' => 23,
                            'anchorStart' => $rightInnerExit,
                            'anchorEnd' => $rightAdvanceStart,
                            'startAnchor' => 'e',
                            'endAnchor' => 'n',
                            'arcRadius' => $rightResumeArcRadius,
                            'color' => 'cyan',

                            'nodeEnd' => true,
                            'nodeEndDot' => false,
                            'jointArrowEnd' => true,
                            'jointArrowEndDirection' => 'left',
                        ]" />
                        {{-- Advance the outer index only after finalization. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.while.action-sequence.right.advance"
                            :counter-end="24"
                            :anchor-start="$rightAdvanceStart"
                            direction="right-left"
                            before-length="2rem"
                            label-gap="16rem"
                            after-length="2rem"
                            :step-label="['text' => ['groupIndex = groupIndex + 1'], 'width' => 'default']"
                            :node-end-dot="false"
                            :joint-arrow-end="true"
                            color="cyan"
                        />
                        @php
                            $rightAdvanceEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                'idea-to-paper-while-action-sequence-right',
                                'literature.while.action-sequence.right.advance.anchorNode-end',
                            );
                            $rightOuterReturnStart = [
                                'x' => 'calc(' . $rightAdvanceEnd['x'] . ' - ' . $rightResumeArcRadius . ')',
                                'y' => 'calc(' . $rightAdvanceEnd['y'] . ' - ' . $rightResumeArcRadius . ')',
                            ];
                        @endphp
                        <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                            'id' => 'literature.while.action-sequence.right.outer-resume.arc-out',
                            'devCounterEnd' => 25,
                            'anchorStart' => $rightAdvanceEnd,
                            'anchorEnd' => $rightOuterReturnStart,
                            'startAnchor' => 'n',
                            'endAnchor' => 'w',
                            'arcRadius' => $rightResumeArcRadius,
                            'color' => 'cyan',

                            'nodeEnd' => true,
                            'nodeEndDot' => false,
                            'jointArrowEnd' => true,
                            'jointArrowEndDirection' => 'bottom',
                        ]" />
                        {{-- Return only after advancing, to the same condition (not initialization). --}}
                        <x-translation-workbench::ui.tw-graph.paths.loop-return
                            id="literature.while.action-sequence.right.body-return"
                            :counter-start="26"
                            :anchor-start="$rightOuterReturnStart"
                            return-to="literature.while.action-sequence.right.loop.anchorNode-return"
                            side="right"
                            color="cyan"
                        />
                        {{-- FALSE continues here, including when the group list starts empty. --}}
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.while.action-sequence.right.continue"
                            :counter-end="30"
                            attach-to="literature.while.action-sequence.right.loop.anchorNode-end"
                            beforeLength="4rem"
                            :step-label="['text' => ['Show summary'], 'width' => 'default']"
                            color="violet"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- while-action-sequence-right-example:end --}}
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="mt-3 flex justify-end font-mono text-xs text-zinc-400">
                .../flow/while/flow-while-action-sequence.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
