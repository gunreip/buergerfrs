<x-translation-workbench::ui.common.heading-counter-group group="flow-throw-finally">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('FINALLY during propagation') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The outer TRY calls validateAndCleanup(). The inner handler logs and rethrows the exception. Before it reaches the caller, FINALLY runs cleanup exactly once. The outer CATCH then handles the same exception and execution continues after the outer TRY/CATCH.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.throw.flow-throw-finally" />

            @php
                $throwSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally',
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
                            example="throw-finally-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-finally-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="throw-finally-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-finally-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Uses right for a route extending left and left for a route extending right; this prop names the incoming arc side.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>anchor-start / attach-to</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default / null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connects THROW, logging, RETHROW, FINALLY, the outer CATCH and continuation.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Provides spacing around both handlers, RETHROW and the FINALLY cleanup.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>inherited</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Red marks throwing, amber handling, violet cleanup and zinc normal continuation.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>dev-counter-end / counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the visible markers from 1 to 9.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally"
                example="throw-finally"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('The examples record outer-try, inner, inner-log, cleanup, outer-caught and after. PHP, JavaScript, C# and Java use FINALLY. C++ uses a scope guard destroyed during stack unwinding; C explicitly cleans up before forwarding its status code. Cleanup is assumed to complete normally. Unreachable statements are commented out where the compiler rejects or warns about them.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('FINALLY during propagation — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Follow THROW to the inner CATCH, then RETHROW to the violet FINALLY step. Cleanup does not cancel the pending exception: only after FINALLY completes does the outer CATCH run. This example assumes cleanup completes normally; replacing the exception or return inside FINALLY is the next topic.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-finally-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-finally-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-finally-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="74rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.left.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['Outer TRY', 'Call validateAndCleanup()'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- The inner TRY has a matching handler that logs and rethrows. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.left.inner"
                                attach-to="literature.throw.finally.left.try.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="[
                                    'text' => ['validateAndCleanup()', 'Record inner'],
                                    'width' => 'default',
                                ]"
                                :counter-end="2"
                                color="sky"
                            />
                            {{-- THROW changes control flow: no continuation of the TRY body follows this action. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.finally.left.raise"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-finally-left',
                                    'literature.throw.finally.left.inner.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['THROW', 'InvalidArgumentException', 'Invalid input'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="3"
                                color="red"
                            />
                            {{-- The inner handler logs, but does not recover from this exception. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.left.inner-catch"
                                attach-to="literature.throw.finally.left.raise.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['Inner CATCH', 'Record inner-log'],
                                    'width' => 'default',
                                ]"
                                :counter-end="4"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.left.rethrow"
                                attach-to="literature.throw.finally.left.inner-catch.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['RETHROW', 'Same exception'],
                                    'width' => 'default',
                                ]"
                                :counter-end="5"
                                color="red"
                            />
                            {{-- FINALLY runs while the exception is pending, before the outer handler. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.left.finally"
                                attach-to="literature.throw.finally.left.rethrow.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['FINALLY', 'Record cleanup'],
                                    'width' => 'default',
                                ]"
                                :counter-end="6"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.left.catch"
                                attach-to="literature.throw.finally.left.finally.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['Outer CATCH', 'InvalidArgumentException', 'Record outer-caught'],
                                    'width' => 'default',
                                ]"
                                :counter-end="7"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.left.continue"
                                attach-to="literature.throw.finally.left.catch.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['After outer TRY/CATCH', 'Record after'],
                                    'width' => 'default',
                                ]"
                                :counter-end="8"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.finally.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-finally-left',
                                    'literature.throw.finally.left.continue.anchorNode-end',
                                )"
                                :dev-counter-end="9"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-finally-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-finally-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-finally-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-finally-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="74rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.right.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['Outer TRY', 'Call validateAndCleanup()'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- The inner TRY has a matching handler that logs and rethrows. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.right.inner"
                                attach-to="literature.throw.finally.right.try.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="[
                                    'text' => ['validateAndCleanup()', 'Record inner'],
                                    'width' => 'default',
                                ]"
                                :counter-end="2"
                                color="sky"
                            />
                            {{-- THROW changes control flow: no continuation of the TRY body follows this action. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.finally.right.raise"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-finally-right',
                                    'literature.throw.finally.right.inner.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['THROW', 'InvalidArgumentException', 'Invalid input'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="3"
                                color="red"
                            />
                            {{-- The inner handler logs, but does not recover from this exception. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.right.inner-catch"
                                attach-to="literature.throw.finally.right.raise.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['Inner CATCH', 'Record inner-log'],
                                    'width' => 'default',
                                ]"
                                :counter-end="4"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.right.rethrow"
                                attach-to="literature.throw.finally.right.inner-catch.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['RETHROW', 'Same exception'],
                                    'width' => 'default',
                                ]"
                                :counter-end="5"
                                color="red"
                            />
                            {{-- FINALLY runs while the exception is pending, before the outer handler. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.right.finally"
                                attach-to="literature.throw.finally.right.rethrow.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['FINALLY', 'Record cleanup'],
                                    'width' => 'default',
                                ]"
                                :counter-end="6"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.right.catch"
                                attach-to="literature.throw.finally.right.finally.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['Outer CATCH', 'InvalidArgumentException', 'Record outer-caught'],
                                    'width' => 'default',
                                ]"
                                :counter-end="7"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally.right.continue"
                                attach-to="literature.throw.finally.right.catch.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['After outer TRY/CATCH', 'Record after'],
                                    'width' => 'default',
                                ]"
                                :counter-end="8"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.finally.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-finally-right',
                                    'literature.throw.finally.right.continue.anchorNode-end',
                                )"
                                :dev-counter-end="9"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-finally-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/flow-throw-finally.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
