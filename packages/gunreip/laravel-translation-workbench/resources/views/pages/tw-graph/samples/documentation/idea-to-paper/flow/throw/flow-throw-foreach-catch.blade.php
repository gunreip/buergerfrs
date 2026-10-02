<x-translation-workbench::ui.common.heading-counter-group group="flow-throw-foreach-catch">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('CATCH inside FOREACH') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Each FOREACH iteration has its own TRY/CATCH. A valid item is processed; an invalid item throws InvalidArgumentException and is handled inside that iteration. Both paths reach the next-item check. For valid, invalid, valid, the first and third items are processed and the second is reported.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.throw.flow-throw-foreach-catch" />

            @php
                $throwSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-foreach-catch',
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
                            example="throw-foreach-catch-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-foreach-catch-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="throw-foreach-catch-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-foreach-catch-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>side</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Uses the same side for the loop, decision and common return.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>after-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('The condition explicitly reserves 19rem for the decision lanes and loop return.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>condition-label</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Shows validation inside the per-item TRY.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start / if-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Valid items are processed. Invalid items THROW and are handled by the CATCH inside that iteration.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>return</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('The loop body remains open for the explicit decision. The decision joins both branches using its default returns.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>attach-to / return-to</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('The shared return starts at the decision output and targets the next-item check, not iteration initialization.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>before-length / after-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Visible authored lengths control the surrounding steps.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-start / counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the loop, both decision branches, the common return and summary.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-foreach-catch"
                example="throw-foreach-catch"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('The CATCH sits inside FOREACH, unlike the previous example. The expected result for [valid, invalid, valid] is processed:0, caught:1, processed:2, completed, after. C uses an explicit per-item failure branch because it has no exceptions. Other unexpected exception types are not handled by the typed CATCH.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('CATCH inside FOREACH — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The green branch processes a valid item. The amber branch shows THROW followed by the matching CATCH for that item. Both branches join before the shared loop return. Only normal exhaustion reaches the summary; handling one invalid item does not skip later items.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-foreach-catch-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-foreach-catch-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-foreach-catch-left"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach-catch.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items:', 'valid, invalid, valid'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Iteration setup runs once; the return skips this step. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach-catch.left.iterator"
                                attach-to="literature.throw.foreach-catch.left.initialize.anchorNode-end"
                                :step-label="['text' => ['Open item iteration'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Check before every iteration, including the first. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach-catch.left.condition"
                                attach-to="literature.throw.foreach-catch.left.iterator.anchorNode-end"
                                before-length="2rem"
                                after-length="19rem"
                                :step-label="[
                                    'text' => ['FOREACH next item?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="1"
                                color="cyan"
                            />
                            {{-- TRUE selects an item; FALSE leaves the loop. --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.throw.foreach-catch.left.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-catch-left',
                                    'literature.throw.foreach-catch.left.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-catch-left',
                                    'literature.throw.foreach-catch.left.iterator.anchorNode-end',
                                )"
                                side="left"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="2rem"
                                exit-length="5.5rem"
                                :bridge-label="[
                                    'text' => ['Select current item'],
                                    'width' => 'default',
                                    'align' => 'center',
                                    'color' => 'green',
                                ]"
                                :entry-label="['text' => ['TRUE'], 'width' => 'half', 'side' => 'top', 'color' => 'green']"
                                :exit-label="[
                                    'text' => ['FALSE'],
                                    'width' => 'half',
                                    'side' => 'right',
                                    'color' => 'red',
                                ]"
                                color="cyan"
                            />
                            {{-- Each iteration has its own TRY/CATCH. Both outcomes return to the next-item check. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.throw.foreach-catch.left.dispatch"
                                attach-to="literature.throw.foreach-catch.left.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="left"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                after-length="1rem"
                                :condition-label="[
                                    'text' => ['TRY inside FOREACH', 'IF current item is valid?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE', 'Process valid item'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: THROW', 'InvalidArgumentException', 'CATCH: report item failure'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :counter-start="7"
                                :left-counter-end="8"
                                :false-stem-counter="9"
                                :right-counter-end="10"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.throw.foreach-catch.left.body-return"
                                attach-to="literature.throw.foreach-catch.left.dispatch.anchorNode-end"
                                return-to="literature.throw.foreach-catch.left.loop.anchorNode-return"
                                side="left"
                                :counter-start="11"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach-catch.left.continue"
                                attach-to="literature.throw.foreach-catch.left.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Show summary'],
                                    'width' => 'default',
                                ]"
                                :counter-end="15"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-foreach-catch-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-foreach-catch-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-foreach-catch-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-foreach-catch-right"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach-catch.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items:', 'valid, invalid, valid'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Iteration setup runs once; the return skips this step. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach-catch.right.iterator"
                                attach-to="literature.throw.foreach-catch.right.initialize.anchorNode-end"
                                :step-label="['text' => ['Open item iteration'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Check before every iteration, including the first. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach-catch.right.condition"
                                attach-to="literature.throw.foreach-catch.right.iterator.anchorNode-end"
                                before-length="2rem"
                                after-length="19rem"
                                :step-label="[
                                    'text' => ['FOREACH next item?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="1"
                                color="cyan"
                            />
                            {{-- TRUE selects an item; FALSE leaves the loop. --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.throw.foreach-catch.right.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-catch-right',
                                    'literature.throw.foreach-catch.right.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-foreach-catch-right',
                                    'literature.throw.foreach-catch.right.iterator.anchorNode-end',
                                )"
                                side="right"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="2rem"
                                exit-length="5.5rem"
                                :bridge-label="[
                                    'text' => ['Select current item'],
                                    'width' => 'default',
                                    'align' => 'center',
                                    'color' => 'green',
                                ]"
                                :entry-label="['text' => ['TRUE'], 'width' => 'half', 'side' => 'top', 'color' => 'green']"
                                :exit-label="[
                                    'text' => ['FALSE'],
                                    'width' => 'half',
                                    'side' => 'left',
                                    'color' => 'red',
                                ]"
                                color="cyan"
                            />
                            {{-- Each iteration has its own TRY/CATCH. Both outcomes return to the next-item check. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.throw.foreach-catch.right.dispatch"
                                attach-to="literature.throw.foreach-catch.right.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="right"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                after-length="1rem"
                                :condition-label="[
                                    'text' => ['TRY inside FOREACH', 'IF current item is valid?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE', 'Process valid item'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: THROW', 'InvalidArgumentException', 'CATCH: report item failure'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :counter-start="7"
                                :left-counter-end="8"
                                :false-stem-counter="9"
                                :right-counter-end="10"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.throw.foreach-catch.right.body-return"
                                attach-to="literature.throw.foreach-catch.right.dispatch.anchorNode-end"
                                return-to="literature.throw.foreach-catch.right.loop.anchorNode-return"
                                side="right"
                                :counter-start="11"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.foreach-catch.right.continue"
                                attach-to="literature.throw.foreach-catch.right.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Show summary'],
                                    'width' => 'default',
                                ]"
                                :counter-end="15"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-foreach-catch-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/flow-throw-foreach-catch.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
