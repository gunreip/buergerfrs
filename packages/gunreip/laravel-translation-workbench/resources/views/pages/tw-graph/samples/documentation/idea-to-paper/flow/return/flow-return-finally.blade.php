<x-translation-workbench::ui.common.heading-counter-group group="flow-return-finally">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('RETURN with FINALLY') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('doublePositive(value) evaluates its return expression inside TRY. Both branches pass through FINALLY before the function exits. FINALLY records cleanup and resets the local value to 0; the already evaluated integer return value remains unchanged.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.return.flow-return-finally" />

            @php
                $returnSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-finally',
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
                            example="return-finally-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $returnSource->example('return-finally-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="return-finally-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $returnSource->example('return-finally-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Connects the condition, shared FINALLY step and terminal function exit through their named anchors.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>side</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Mirrors the two return-value branches to the left or right of the condition.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start / if-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The branch labels show evaluation of the two return expressions. Both routes lead to the same FINALLY block.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-length /
                                        stem-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Explicit lengths reserve room for the two return labels and their common exit.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>before-length /
                                        after-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Sets the visible spacing before and after the FINALLY and return-completion steps.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>parts.end.length</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Ends the function only after FINALLY and delivery of the saved return value.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-start /
                                        counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Numbers the visible markers consecutively from 1 to 7.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-finally"
                example="return-finally"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('PHP, JavaScript, C# and Java use TRY/FINALLY. C explicitly stores the result and performs cleanup before returning; C++ uses a scope guard. The pending result is explanatory state in the graph, not an extra variable required by native FINALLY syntax. This example assumes cleanup completes normally; exceptions and RETURN inside FINALLY are separate cases.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('RETURN with FINALLY — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('TRUE evaluates value * 2; FALSE evaluates 0. The joined lanes carry a pending RETURN into FINALLY, not a continuation of the TRY body. Cleanup runs once, then the function returns the saved result to its caller. For value = 3 the result is 6, even though FINALLY resets the local value to 0.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="return-finally-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- return-finally-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-return-finally-left"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="4rem"
                            min-height="42rem"
                            min-width="48rem"
                        >
                            {{-- TRY evaluates one return expression; control still has to pass through FINALLY. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.return.finally.left.selection"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                side="left"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['TRY: doublePositive(value)', 'IF value > 0?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE: RETURN value * 2', 'Evaluate and save result'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: RETURN 0', 'Evaluate and save result'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :counter-start="1"
                                :left-counter-end="2"
                                :false-stem-counter="3"
                                :right-counter-end="4"
                                color="sky"
                            />
                            {{-- Both pending RETURN routes run the same FINALLY exactly once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.finally.left.finally"
                                attach-to="literature.return.finally.left.selection.anchorNode-end"
                                before-length="4rem"
                                after-length="2rem"
                                label-gap="4rem"
                                :step-label="[
                                    'text' => ['FINALLY: record cleanup', 'value = 0'],
                                    'width' => 'default',
                                ]"
                                :counter-end="5"
                                color="violet"
                            />
                            {{-- This completes the pending RETURN; it is not another statement in the TRY body. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.finally.left.deliver"
                                attach-to="literature.return.finally.left.finally.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                label-gap="4rem"
                                :step-label="[
                                    'text' => ['Complete pending RETURN', 'Deliver saved result to caller'],
                                    'width' => 'default',
                                ]"
                                :counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.finally.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-finally-left',
                                    'literature.return.finally.left.deliver.anchorNode-end',
                                )"
                                length="3rem"
                                :dev-counter-end="7"
                                color="amber"
                            />
                        </x-translation-workbench::ui.tw-graph>

                        {{-- return-finally-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="return-finally-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- return-finally-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-return-finally-right"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="4rem"
                            min-height="42rem"
                            min-width="48rem"
                        >
                            {{-- TRY evaluates one return expression; control still has to pass through FINALLY. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.return.finally.right.selection"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                side="right"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['TRY: doublePositive(value)', 'IF value > 0?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE: RETURN value * 2', 'Evaluate and save result'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: RETURN 0', 'Evaluate and save result'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :counter-start="1"
                                :left-counter-end="2"
                                :false-stem-counter="3"
                                :right-counter-end="4"
                                color="sky"
                            />
                            {{-- Both pending RETURN routes run the same FINALLY exactly once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.finally.right.finally"
                                attach-to="literature.return.finally.right.selection.anchorNode-end"
                                before-length="4rem"
                                after-length="2rem"
                                label-gap="4rem"
                                :step-label="[
                                    'text' => ['FINALLY: record cleanup', 'value = 0'],
                                    'width' => 'default',
                                ]"
                                :counter-end="5"
                                color="violet"
                            />
                            {{-- This completes the pending RETURN; it is not another statement in the TRY body. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.finally.right.deliver"
                                attach-to="literature.return.finally.right.finally.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                label-gap="4rem"
                                :step-label="[
                                    'text' => ['Complete pending RETURN', 'Deliver saved result to caller'],
                                    'width' => 'default',
                                ]"
                                :counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.finally.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-finally-right',
                                    'literature.return.finally.right.deliver.anchorNode-end',
                                )"
                                length="3rem"
                                :dev-counter-end="7"
                                color="amber"
                            />
                        </x-translation-workbench::ui.tw-graph>

                        {{-- return-finally-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/return/flow-return-finally.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
