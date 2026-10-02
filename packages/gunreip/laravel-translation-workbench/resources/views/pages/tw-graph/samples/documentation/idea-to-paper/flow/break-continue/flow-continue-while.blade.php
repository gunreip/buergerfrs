<x-translation-workbench::ui.common.heading-counter-group group="flow-continue-while">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('WHILE with CONTINUE') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('WHILE checks for another item before each iteration. The body reads the item and advances the index before the IF. TRUE executes CONTINUE and skips Process item; FALSE processes the item. Both routes return to the WHILE condition. Only an exhausted collection reaches the action after WHILE.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.break-continue.flow-continue-while" />

            @php
                $continueSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-while',
                );
            @endphp
            <x-translation-workbench::ui.common.separator-code-example-tw-graph />
            <flux:accordion
                transition
                exclusive
            >
                <flux:accordion.item expanded>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="continue-while-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $continueSource->example('continue-while-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="continue-while-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $continueSource->example('continue-while-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <x-translation-workbench::ui.common.separator-props-used-tw-graph />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Props and connections') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns
                            class="bg-white dark:bg-zinc-900"
                            sticky
                        >
                            <flux:table.column>{{ __('Prop / anchor') }}</flux:table.column>
                            <flux:table.column>{{ __('Default') }}</flux:table.column>
                            <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>attach-to<br>anchor-start</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null / component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Connects independent actions and the IF to the open WHILE body.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>condition-label.afterLength</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Explicitly 27rem reserves room for the IF and the return.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>action-label.return</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('False leaves the WHILE body open for the IF. The index advances before the decision so CONTINUE cannot repeat the same item.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>action-label.beforeLength<br>afterLength</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>4rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Explicitly 2rem for the bridges around the read-and-advance action.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true-bridge-length<br>stem-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem / 4rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Explicitly 4rem and 5.5rem for the WHILE entry and exhausted exit.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>direction<br>side</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>bottom-top / left</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('The IF uses top-bottom to follow the downward body; the example extends left.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>condition-label<br>if-start<br>if-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('TRUE bypasses Process item with CONTINUE. FALSE executes Process item before returning.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>bridge-length<br>stem-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>inherited / 8rem (IF)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Explicitly 2rem and 4rem for the IF lanes.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>before-length<br>label-gap<br>after-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem / 4rem / 2rem (IF)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Explicitly 1rem, 4rem and 1rem around the IF question.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>return-to</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Both IF outcomes join the loop return to loop.anchorNode-return, before the next WHILE condition check.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>loop.anchorNode-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>connection</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Only normal WHILE exhaustion reaches Show summary after WHILE.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>counter-start<br>counter-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Keeps DEV numbering continuous across the independent components.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-while"
                example="continue-while"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('CONTINUE does not exit WHILE. Advancing the index before the IF ensures progress even when every item is skipped. Process item is the remaining body action and is bypassed by CONTINUE. Application helpers, enclosing functions and imports are omitted.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('WHILE with CONTINUE — Preview') }}</flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Follow the amber CONTINUE route past Process item to the shared return. The index advances before the IF, so skipped items still make progress. Only the WHILE FALSE exit reaches the summary.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="continue-while-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- continue-while-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-continue-while-left"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.while.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'index = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- WHILE owns the condition; the open body continues into the independent IF. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.continue.while.left.loop"
                                attach-to="literature.continue.while.left.initialize.anchorNode-end"
                                side="left"
                                true-bridge-length="4rem"
                                stem-length="5.5rem"
                                :condition-label="[
                                    'text' => ['WHILE index < count?'],
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '27rem',
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :action-label="[
                                    'text' => ['Read current item', 'index = index + 1'],
                                    'beforeLength' => '2rem',
                                    'afterLength' => '2rem',
                                    'return' => false,
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :true-label="['text' => ['TRUE'], 'width' => 'half', 'color' => 'green']"
                                :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red']"
                                color="cyan"
                            />
                            {{-- TRUE skips Process item; both lanes join only the return to the WHILE condition. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.continue.while.left.decision"
                                attach-to="literature.continue.while.left.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="left"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                label-gap="4rem"
                                after-length="1rem"
                                :condition-label="[
                                    'text' => ['IF shouldSkip(item)?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE: CONTINUE'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: Process item'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :counter-start="7"
                                :left-counter-end="8"
                                :false-stem-counter="9"
                                :right-counter-end="10"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.continue.while.left.body-return"
                                attach-to="literature.continue.while.left.decision.anchorNode-end"
                                return-to="literature.continue.while.left.loop.anchorNode-return"
                                side="left"
                                :counter-start="11"
                                color="green"
                            />
                            {{-- Only WHILE exhaustion reaches the following action; CONTINUE returns above. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.while.left.continue"
                                attach-to="literature.continue.while.left.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Show summary after WHILE'],
                                    'width' => 'default',
                                ]"
                                :counter-end="15"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- continue-while-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="continue-while-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- continue-while-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-continue-while-right"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.while.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'index = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- WHILE owns the condition; the open body continues into the independent IF. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.continue.while.right.loop"
                                attach-to="literature.continue.while.right.initialize.anchorNode-end"
                                side="right"
                                true-bridge-length="4rem"
                                stem-length="5.5rem"
                                :condition-label="[
                                    'text' => ['WHILE index < count?'],
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '27rem',
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :action-label="[
                                    'text' => ['Read current item', 'index = index + 1'],
                                    'beforeLength' => '2rem',
                                    'afterLength' => '2rem',
                                    'return' => false,
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :true-label="['text' => ['TRUE'], 'width' => 'half', 'color' => 'green']"
                                :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red']"
                                color="cyan"
                            />
                            {{-- TRUE skips Process item; both lanes join only the return to the WHILE condition. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.continue.while.right.decision"
                                attach-to="literature.continue.while.right.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="right"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                label-gap="4rem"
                                after-length="1rem"
                                :condition-label="[
                                    'text' => ['IF shouldSkip(item)?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE: CONTINUE'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: Process item'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :counter-start="7"
                                :left-counter-end="8"
                                :false-stem-counter="9"
                                :right-counter-end="10"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.continue.while.right.body-return"
                                attach-to="literature.continue.while.right.decision.anchorNode-end"
                                return-to="literature.continue.while.right.loop.anchorNode-return"
                                side="right"
                                :counter-start="11"
                                color="green"
                            />
                            {{-- Only WHILE exhaustion reaches the following action; CONTINUE returns above. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.while.right.continue"
                                attach-to="literature.continue.while.right.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Show summary after WHILE'],
                                    'width' => 'default',
                                ]"
                                :counter-end="15"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- continue-while-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/break-continue/flow-continue-while.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
