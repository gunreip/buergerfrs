<x-translation-workbench::ui.common.heading-counter-group group="flow-throw-finally-exception">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('THROW in FINALLY replaces an exception') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('failAndCleanup() throws InvalidArgumentException inside TRY. During propagation, FINALLY throws a new RuntimeException. The outer handler receives the cleanup failure as the active exception. The original failure remains available as diagnostic context where the language or explicit chaining preserves it.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.throw.flow-throw-finally-exception" />

            @php
                $throwSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally-exception',
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
                            example="throw-finally-exception-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-finally-exception-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="throw-finally-exception-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-finally-exception-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connects the original THROW, FINALLY, the replacement THROW and the outer handler.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Provides spacing around the original error, cleanup and outer handler.') }}</flux:table.cell>
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
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-finally-exception"
                example="throw-finally-exception"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('PHP automatically links the pending exception through getPrevious(). JavaScript cause, Java getCause() and C# InnerException are explicitly populated in these examples; without that chaining the original error can be lost. C++ uses throw_with_nested in an explicit handler, not a throwing destructor. C carries both error codes explicitly. The extra capture/rethrow in some snippets only preserves context; the graph shows the resulting control flow.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('THROW in FINALLY replaces an exception — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The first red step throws the original input error. The violet FINALLY starts cleanup; its second red THROW replaces the active exception. The outer CATCH handles the cleanup error and inspects the original cause. The cause is diagnostic context, not a second control-flow route.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-finally-exception-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-finally-exception-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-finally-exception-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="56rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-exception.left.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['Outer TRY', 'Call failAndCleanup()'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- The original exception is pending while FINALLY runs. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-exception.left.original-error"
                                attach-to="literature.throw.finally-exception.left.try.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['TRY: THROW', 'InvalidArgumentException', 'Original failure'], 'width' => 'default']"
                                :counter-end="2"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-exception.left.finally"
                                attach-to="literature.throw.finally-exception.left.original-error.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['FINALLY', 'Start cleanup'], 'width' => 'default']"
                                :counter-end="3"
                                color="violet"
                            />
                            {{-- This new exception replaces the original; preserve its cause for diagnostics. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.finally-exception.left.raise"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-finally-exception-left',
                                    'literature.throw.finally-exception.left.finally.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['THROW RuntimeException', 'Cleanup failed', 'Replace original exception'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-exception.left.catch"
                                attach-to="literature.throw.finally-exception.left.raise.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="['text' => ['Outer CATCH', 'RuntimeException', 'Inspect original cause'], 'width' => 'default']"
                                :counter-end="5"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-exception.left.continue"
                                attach-to="literature.throw.finally-exception.left.catch.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['After outer TRY/CATCH', 'Cleanup error handled'], 'width' => 'default']"
                                :counter-end="6"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.finally-exception.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-finally-exception-left',
                                    'literature.throw.finally-exception.left.continue.anchorNode-end',
                                )"
                                :dev-counter-end="7"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-finally-exception-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-finally-exception-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-finally-exception-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-finally-exception-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="56rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-exception.right.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['Outer TRY', 'Call failAndCleanup()'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- The original exception is pending while FINALLY runs. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-exception.right.original-error"
                                attach-to="literature.throw.finally-exception.right.try.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['TRY: THROW', 'InvalidArgumentException', 'Original failure'], 'width' => 'default']"
                                :counter-end="2"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-exception.right.finally"
                                attach-to="literature.throw.finally-exception.right.original-error.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['FINALLY', 'Start cleanup'], 'width' => 'default']"
                                :counter-end="3"
                                color="violet"
                            />
                            {{-- This new exception replaces the original; preserve its cause for diagnostics. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.finally-exception.right.raise"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-finally-exception-right',
                                    'literature.throw.finally-exception.right.finally.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['THROW RuntimeException', 'Cleanup failed', 'Replace original exception'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-exception.right.catch"
                                attach-to="literature.throw.finally-exception.right.raise.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="['text' => ['Outer CATCH', 'RuntimeException', 'Inspect original cause'], 'width' => 'default']"
                                :counter-end="5"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.finally-exception.right.continue"
                                attach-to="literature.throw.finally-exception.right.catch.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['After outer TRY/CATCH', 'Cleanup error handled'], 'width' => 'default']"
                                :counter-end="6"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.finally-exception.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-finally-exception-right',
                                    'literature.throw.finally-exception.right.continue.anchorNode-end',
                                )"
                                :dev-counter-end="7"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-finally-exception-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/flow-throw-finally-exception.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
