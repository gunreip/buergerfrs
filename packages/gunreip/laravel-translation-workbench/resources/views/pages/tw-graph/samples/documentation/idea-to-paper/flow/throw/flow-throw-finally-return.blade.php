<x-translation-workbench::ui.common.heading-counter-group group="flow-throw-finally-return">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('THROW in FINALLY replaces RETURN') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('computeAndCleanup() evaluates RETURN 42 inside TRY. Before the value reaches its caller, FINALLY starts cleanup and throws a new RuntimeException. This cancels the pending return: the caller receives the exception instead of 42, and its result assignment never completes.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.throw.flow-throw-finally-return" />

            @php
                $throwSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally-return',
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
                            example="throw-finally-return-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-finally-return-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="throw-finally-return-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-finally-return-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>parts.sideways.side</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Destination side: left extends the route left; right extends it right.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>anchor-start / attach-to</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default / null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connects the pending RETURN to FINALLY, its THROW and the outer handler.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Sets both bridge sections around the THROW action label to 2rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-label</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Shows the actual THROW action inside the bridge.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>before-length / after-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Provides spacing around the pending RETURN, cleanup and outer handler.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>inherited</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Red marks throwing, amber handling, violet cleanup and zinc normal continuation.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>dev-counter-end / counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the visible markers from 1 to 7.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally-return"
                example="throw-finally-return"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('PHP, JavaScript, C# and Java show THROW in FINALLY cancelling a pending return. C++ has no FINALLY: the equivalent explicitly stores the result and calls throwing cleanup before returning, rather than throwing from a destructor. C uses a cleanup-error status and writes the output only on success. In every example the caller keeps its original result value; the intended 42 is not assigned.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('THROW in FINALLY replaces RETURN — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The amber step saves the intended result 42. The violet FINALLY step starts cleanup, but the red THROW interrupts it. Only the outer CATCH is reached; there is no successful return route carrying 42 back to the caller. This demonstrates abrupt completion, not a recommended cleanup strategy.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-finally-return-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-finally-return-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-finally-return-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="56rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-return.left.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['Outer TRY', 'Call computeAndCleanup()'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- RETURN is evaluated here, but the value has not reached the caller yet. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-return.left.pending-return"
                                attach-to="literature.throw.finally-return.left.try.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['Inner TRY: RETURN 42', 'Save pending result'], 'width' => 'default']"
                                :counter-end="2"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-return.left.finally"
                                attach-to="literature.throw.finally-return.left.pending-return.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['FINALLY', 'Start cleanup'], 'width' => 'default']"
                                :counter-end="3"
                                color="violet"
                            />
                            {{-- This new exception cancels the pending RETURN. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.finally-return.left.raise"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-finally-return-left',
                                    'literature.throw.finally-return.left.finally.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['THROW RuntimeException', 'Cleanup failed', 'Cancel pending RETURN'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-return.left.catch"
                                attach-to="literature.throw.finally-return.left.raise.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="['text' => ['Outer CATCH', 'RuntimeException', 'Handle cleanup failure'], 'width' => 'default']"
                                :counter-end="5"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-return.left.continue"
                                attach-to="literature.throw.finally-return.left.catch.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['After outer TRY/CATCH', 'No result 42 received'], 'width' => 'default']"
                                :counter-end="6"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.finally-return.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-finally-return-left',
                                    'literature.throw.finally-return.left.continue.anchorNode-end',
                                )"
                                :dev-counter-end="7"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-finally-return-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-finally-return-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-finally-return-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-finally-return-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="56rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-return.right.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['Outer TRY', 'Call computeAndCleanup()'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- RETURN is evaluated here, but the value has not reached the caller yet. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-return.right.pending-return"
                                attach-to="literature.throw.finally-return.right.try.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['Inner TRY: RETURN 42', 'Save pending result'], 'width' => 'default']"
                                :counter-end="2"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-return.right.finally"
                                attach-to="literature.throw.finally-return.right.pending-return.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['FINALLY', 'Start cleanup'], 'width' => 'default']"
                                :counter-end="3"
                                color="violet"
                            />
                            {{-- This new exception cancels the pending RETURN. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.finally-return.right.raise"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-finally-return-right',
                                    'literature.throw.finally-return.right.finally.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['THROW RuntimeException', 'Cleanup failed', 'Cancel pending RETURN'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-return.right.catch"
                                attach-to="literature.throw.finally-return.right.raise.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="['text' => ['Outer CATCH', 'RuntimeException', 'Handle cleanup failure'], 'width' => 'default']"
                                :counter-end="5"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-return.right.continue"
                                attach-to="literature.throw.finally-return.right.catch.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['After outer TRY/CATCH', 'No result 42 received'], 'width' => 'default']"
                                :counter-end="6"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.finally-return.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-finally-return-right',
                                    'literature.throw.finally-return.right.continue.anchorNode-end',
                                )"
                                :dev-counter-end="7"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-finally-return-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/flow-throw-finally-return.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
