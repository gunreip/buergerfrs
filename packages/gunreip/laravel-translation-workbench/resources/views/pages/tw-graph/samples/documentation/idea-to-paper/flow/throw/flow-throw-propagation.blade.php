<x-translation-workbench::ui.common.heading-counter-group group="flow-throw-propagation">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Propagation to outer CATCH') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The outer TRY calls validateInput(). That function throws InvalidArgumentException and has no local handler. The exception leaves the function and reaches the matching CATCH of its caller. Neither the remaining helper statements nor the statement after the call executes.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.throw.flow-throw-propagation" />

            @php
                $throwSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-propagation',
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
                            example="throw-propagation-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-propagation-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="throw-propagation-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-propagation-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connects the outer TRY to the called function, then THROW to the outer CATCH and continuation.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Provides spacing around the outer TRY, called function, outer CATCH and continuation.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>inherited</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Red marks the throw route, amber the handler and zinc normal continuation.') }}</flux:table.cell>
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
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-propagation"
                example="throw-propagation"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('The examples record outer-try, inner, outer-caught and after. Both unreachable actions are skipped. JavaScript checks the type inside its untyped CATCH. C explicitly returns and forwards a status code; it has no automatic exception propagation. Java comments out the statement after unconditional THROW because it is a compile-time error.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Propagation to outer CATCH — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The sky route enters validateInput(). The red route represents propagation out of that function into the outer CATCH; it is not a normal function return and needs no second THROW. Once the outer handler completes, the zinc route continues after the outer TRY/CATCH.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-propagation-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-propagation-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-propagation-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="48rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.propagation.left.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['Outer TRY', 'Call validateInput()'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- There is no handler in validateInput(); THROW leaves this function. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.propagation.left.inner"
                                attach-to="literature.throw.propagation.left.try.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="[
                                    'text' => ['validateInput()', 'Record inner'],
                                    'width' => 'default',
                                ]"
                                :counter-end="2"
                                color="sky"
                            />
                            {{-- THROW changes control flow: no continuation of the TRY body follows this action. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.propagation.left.raise"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-propagation-left',
                                    'literature.throw.propagation.left.inner.anchorNode-end',
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
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.propagation.left.catch"
                                attach-to="literature.throw.propagation.left.raise.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['Outer CATCH', 'InvalidArgumentException', 'Record outer-caught'],
                                    'width' => 'default',
                                ]"
                                :counter-end="4"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.propagation.left.continue"
                                attach-to="literature.throw.propagation.left.catch.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['After outer TRY/CATCH', 'Record after'],
                                    'width' => 'default',
                                ]"
                                :counter-end="5"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.propagation.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-propagation-left',
                                    'literature.throw.propagation.left.continue.anchorNode-end',
                                )"
                                :dev-counter-end="6"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-propagation-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-propagation-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-propagation-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-propagation-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="48rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.propagation.right.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['Outer TRY', 'Call validateInput()'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- There is no handler in validateInput(); THROW leaves this function. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.propagation.right.inner"
                                attach-to="literature.throw.propagation.right.try.anchorNode-end"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="[
                                    'text' => ['validateInput()', 'Record inner'],
                                    'width' => 'default',
                                ]"
                                :counter-end="2"
                                color="sky"
                            />
                            {{-- THROW changes control flow: no continuation of the TRY body follows this action. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.propagation.right.raise"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-propagation-right',
                                    'literature.throw.propagation.right.inner.anchorNode-end',
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
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.propagation.right.catch"
                                attach-to="literature.throw.propagation.right.raise.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['Outer CATCH', 'InvalidArgumentException', 'Record outer-caught'],
                                    'width' => 'default',
                                ]"
                                :counter-end="4"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.propagation.right.continue"
                                attach-to="literature.throw.propagation.right.catch.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['After outer TRY/CATCH', 'Record after'],
                                    'width' => 'default',
                                ]"
                                :counter-end="5"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.propagation.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-propagation-right',
                                    'literature.throw.propagation.right.continue.anchorNode-end',
                                )"
                                :dev-counter-end="6"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-propagation-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/flow-throw-propagation.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
