<x-translation-workbench::ui.common.heading-counter-group group="flow-throw-return-finally">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('RETURN inside FINALLY') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('failAndReturn() throws inside TRY, but FINALLY returns 7 before the exception reaches the caller. That return suppresses the pending exception: the caller assigns 7 and skips CATCH. This illustrates a control-flow hazard, not a recommended error-handling pattern.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.throw.flow-throw-return-finally" />

            @php
                $throwSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-return-finally',
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
                            example="throw-return-finally-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-return-finally-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="throw-return-finally-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-return-finally-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connects the pending exception to FINALLY, RETURN and normal caller continuation.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Sets both bridge sections around the RETURN action label to 2rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-label</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Shows the actual RETURN action inside the bridge.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>before-length / after-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Provides spacing around the original error, cleanup and caller.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>inherited</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Red marks throwing, violet cleanup, green the replacement return and zinc normal continuation.') }}</flux:table.cell>
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
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-return-finally"
                example="throw-return-finally"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('PHP 8.5, JavaScript and Java demonstrate RETURN inside FINALLY suppressing a pending exception. A pending return would likewise be replaced. C# forbids leaving FINALLY with RETURN (CS0157); its snippet shows an explicitly different catch-and-return alternative. C and C++ have no FINALLY and show explicit fallback handling instead.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('RETURN inside FINALLY — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The red TRY step raises an exception. Violet FINALLY runs next; its green RETURN 7 replaces the pending exception. The caller receives the value and continues normally. CATCH has no incoming route because it is not executed.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-return-finally-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-return-finally-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-return-finally-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="56rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.return-finally.left.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['Outer TRY', 'Call failAndReturn()'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- The original exception is pending while FINALLY runs. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.return-finally.left.original-error"
                                attach-to="literature.throw.return-finally.left.try.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['TRY: THROW', 'InvalidArgumentException', 'Original failure'], 'width' => 'default']"
                                :counter-end="2"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.return-finally.left.finally"
                                attach-to="literature.throw.return-finally.left.original-error.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['FINALLY', 'Start cleanup'], 'width' => 'default']"
                                :counter-end="3"
                                color="violet"
                            />
                            {{-- RETURN in FINALLY suppresses the pending exception; no outer CATCH is entered. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.return-finally.left.return"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-return-finally-left',
                                    'literature.throw.return-finally.left.finally.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 7', 'Replace pending exception'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.return-finally.left.caller"
                                attach-to="literature.throw.return-finally.left.return.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="['text' => ['Caller receives 7', 'CATCH is skipped'], 'width' => 'default']"
                                :counter-end="5"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.return-finally.left.continue"
                                attach-to="literature.throw.return-finally.left.caller.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['After outer TRY/CATCH', 'Continue with result 7'], 'width' => 'default']"
                                :counter-end="6"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.return-finally.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-return-finally-left',
                                    'literature.throw.return-finally.left.continue.anchorNode-end',
                                )"
                                :dev-counter-end="7"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-return-finally-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-return-finally-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-return-finally-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-return-finally-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="56rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.return-finally.right.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['Outer TRY', 'Call failAndReturn()'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- The original exception is pending while FINALLY runs. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.return-finally.right.original-error"
                                attach-to="literature.throw.return-finally.right.try.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['TRY: THROW', 'InvalidArgumentException', 'Original failure'], 'width' => 'default']"
                                :counter-end="2"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.return-finally.right.finally"
                                attach-to="literature.throw.return-finally.right.original-error.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['FINALLY', 'Start cleanup'], 'width' => 'default']"
                                :counter-end="3"
                                color="violet"
                            />
                            {{-- RETURN in FINALLY suppresses the pending exception; no outer CATCH is entered. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.return-finally.right.return"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-return-finally-right',
                                    'literature.throw.return-finally.right.finally.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 7', 'Replace pending exception'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.return-finally.right.caller"
                                attach-to="literature.throw.return-finally.right.return.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="['text' => ['Caller receives 7', 'CATCH is skipped'], 'width' => 'default']"
                                :counter-end="5"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.return-finally.right.continue"
                                attach-to="literature.throw.return-finally.right.caller.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="['text' => ['After outer TRY/CATCH', 'Continue with result 7'], 'width' => 'default']"
                                :counter-end="6"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.return-finally.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-return-finally-right',
                                    'literature.throw.return-finally.right.continue.anchorNode-end',
                                )"
                                :dev-counter-end="7"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-return-finally-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/flow-throw-return-finally.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
