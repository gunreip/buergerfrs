<x-translation-workbench::ui.common.heading-counter-group group="flow-throw-unhandled">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Unhandled exception') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('failWithCleanup() throws inside TRY and has no CATCH. FINALLY performs cleanup without replacing the exception. The same error then leaves the shown call; the statement after that call is not reached. The red endpoint marks propagation beyond this example, not a successful return.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.throw.flow-throw-unhandled" />

            @php
                $throwSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-unhandled',
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
                            example="throw-unhandled-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-unhandled-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="throw-unhandled-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-unhandled-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connects THROW to cleanup and the exception propagation boundary.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Sets both bridge sections around the propagation label label to 2rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-label</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Shows the actual propagation label inside the bridge.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>before-length / after-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Provides spacing around the original error, cleanup and propagation boundary.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>inherited</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Red marks exception propagation; violet marks cleanup and sky the call entry.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>dev-counter-end / counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the visible markers from 1 to 6.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-unhandled"
                example="throw-unhandled"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('PHP, JavaScript, Java and C# show a call without a local CATCH. The caller or host determines how an escaping exception is reported; this graph does not promise process-wide termination or cleanup on every runtime termination path. C++ uses explicit cleanup-and-rethrow because it has no FINALLY; unwinding for a completely uncaught exception is implementation-defined. C demonstrates an explicit failure status instead of exceptions.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Unhandled exception — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Follow the original exception through FINALLY to the red propagation boundary. There is no handler, fallback value or normal continuation in this example. Any handler supplied by a caller or framework lies outside the shown graph.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-unhandled-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-unhandled-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-unhandled-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="56rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.unhandled.left.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['Caller', 'Call failWithCleanup()'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- The original exception is pending while FINALLY runs. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.unhandled.left.original-error"
                                attach-to="literature.throw.unhandled.left.try.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['TRY: THROW', 'InvalidArgumentException', 'Original failure'], 'width' => 'default']"
                                :counter-end="2"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.unhandled.left.finally"
                                attach-to="literature.throw.unhandled.left.original-error.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['FINALLY', 'Run cleanup'], 'width' => 'default']"
                                :counter-end="3"
                                color="violet"
                            />
                            {{-- FINALLY completes normally: the same exception continues beyond this call. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.unhandled.left.propagate"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-unhandled-left',
                                    'literature.throw.unhandled.left.finally.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Propagate original exception', 'No local CATCH'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.unhandled.left.boundary"
                                attach-to="literature.throw.unhandled.left.propagate.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="['text' => ['Exception leaves this call', 'No normal continuation'], 'width' => 'default']"
                                :counter-end="5"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.unhandled.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-unhandled-left',
                                    'literature.throw.unhandled.left.boundary.anchorNode-end',
                                )"
                                :dev-counter-end="6"
                                color="red"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-unhandled-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-unhandled-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-unhandled-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-unhandled-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="56rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.unhandled.right.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['Caller', 'Call failWithCleanup()'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- The original exception is pending while FINALLY runs. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.unhandled.right.original-error"
                                attach-to="literature.throw.unhandled.right.try.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['TRY: THROW', 'InvalidArgumentException', 'Original failure'], 'width' => 'default']"
                                :counter-end="2"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.unhandled.right.finally"
                                attach-to="literature.throw.unhandled.right.original-error.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['FINALLY', 'Run cleanup'], 'width' => 'default']"
                                :counter-end="3"
                                color="violet"
                            />
                            {{-- FINALLY completes normally: the same exception continues beyond this call. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.unhandled.right.propagate"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-unhandled-right',
                                    'literature.throw.unhandled.right.finally.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Propagate original exception', 'No local CATCH'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.unhandled.right.boundary"
                                attach-to="literature.throw.unhandled.right.propagate.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="['text' => ['Exception leaves this call', 'No normal continuation'], 'width' => 'default']"
                                :counter-end="5"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.unhandled.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-unhandled-right',
                                    'literature.throw.unhandled.right.boundary.anchorNode-end',
                                )"
                                :dev-counter-end="6"
                                color="red"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-unhandled-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/flow-throw-unhandled.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
