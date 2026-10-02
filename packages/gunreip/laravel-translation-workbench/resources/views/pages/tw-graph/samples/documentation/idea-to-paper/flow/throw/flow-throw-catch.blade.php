<x-translation-workbench::ui.common.heading-counter-group group="flow-throw-catch">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('THROW to matching CATCH') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('An explicit THROW interrupts the TRY body and transfers control to a matching CATCH. The remaining TRY statements are skipped. When the handler completes normally, execution continues after TRY/CATCH in the same function.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.throw.flow-throw-catch" />

            @php
                $throwSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-catch',
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
                            example="throw-catch-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-catch-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="throw-catch-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $throwSource->example('throw-catch-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connects TRY to THROW, then the matching CATCH and the continuation.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Provides spacing around TRY, CATCH and the continuation.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>inherited</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Red marks the throw route, amber the handler and zinc normal continuation.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>dev-counter-end / counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the visible markers from 1 to 5.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.throw.flow-throw-catch"
                example="throw-catch"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('The examples record try, caught and after in that order. JavaScript checks the error type inside CATCH because it has no typed CATCH clause. C has no language-level exceptions; its example uses an explicit status code and is marked as an equivalent error-handling flow. Java shows the skipped statement as a comment because an unconditional THROW makes following statements unreachable at compile time.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('THROW to matching CATCH — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The red THROW route enters the amber CATCH step. The handler records the error, then the neutral route continues after TRY/CATCH. There is no route to the statement following THROW inside TRY. This first example throws unconditionally; selection among handlers and propagation follow in later examples.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-catch-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-catch-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-catch-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="40rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.caught.left.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['TRY', 'Record try'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- THROW changes control flow: no continuation of the TRY body follows this action. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.caught.left.raise"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-catch-left',
                                    'literature.throw.caught.left.try.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['THROW', 'InvalidArgumentException', 'Invalid input'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.caught.left.catch"
                                attach-to="literature.throw.caught.left.raise.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['CATCH', 'InvalidArgumentException', 'Record caught'],
                                    'width' => 'default',
                                ]"
                                :counter-end="3"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.caught.left.continue"
                                attach-to="literature.throw.caught.left.catch.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['After TRY/CATCH', 'Record after'],
                                    'width' => 'default',
                                ]"
                                :counter-end="4"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.caught.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-catch-left',
                                    'literature.throw.caught.left.continue.anchorNode-end',
                                )"
                                :dev-counter-end="5"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-catch-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="throw-catch-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- throw-catch-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-throw-catch-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="40rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.caught.right.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                before-length="2rem"
                                after-length="3rem"
                                :step-label="['text' => ['TRY', 'Record try'], 'width' => 'default']"
                                :counter-end="1"
                                color="sky"
                            />
                            {{-- THROW changes control flow: no continuation of the TRY body follows this action. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.throw.caught.right.raise"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-catch-right',
                                    'literature.throw.caught.right.try.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['THROW', 'InvalidArgumentException', 'Invalid input'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.caught.right.catch"
                                attach-to="literature.throw.caught.right.raise.anchorNode-end"
                                before-length="3rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['CATCH', 'InvalidArgumentException', 'Record caught'],
                                    'width' => 'default',
                                ]"
                                :counter-end="3"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.throw.caught.right.continue"
                                attach-to="literature.throw.caught.right.catch.anchorNode-end"
                                before-length="2rem"
                                after-length="2rem"
                                :step-label="[
                                    'text' => ['After TRY/CATCH', 'Record after'],
                                    'width' => 'default',
                                ]"
                                :counter-end="4"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.throw.caught.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-throw-catch-right',
                                    'literature.throw.caught.right.continue.anchorNode-end',
                                )"
                                :dev-counter-end="5"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- throw-catch-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/throw/flow-throw-catch.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
