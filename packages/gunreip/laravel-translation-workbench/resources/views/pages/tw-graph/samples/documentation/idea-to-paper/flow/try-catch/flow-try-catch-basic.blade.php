<x-translation-workbench::ui.common.heading-counter-group group="flow-try-catch-basic">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('TRY / CATCH') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('TRY performs the operation. Normal completion records success; an exception selects CATCH and handles the failure instead. Only one lane executes before continuation. The split represents runtime exception dispatch, not an additional IF statement.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.try-catch.flow-try-catch-basic" />

            @php
                $tryCatchSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-basic',
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
                            example="try-catch-basic-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $tryCatchSource->example('try-catch-basic-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="try-catch-basic-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $tryCatchSource->example('try-catch-basic-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>side</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>left</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Mirrors the exception-handling lanes without changing their execution order.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>anchor-start</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>x=0rem, y=0rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets the entry position of TRY.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>condition-label</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Labels the protected operation; the existing branch component draws the runtime outcome split.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>if-start</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Defines the normal-completion action and its color.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>if-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Defines the generic CATCH action and its color.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>before-length<br>after-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem (branch component)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets the stems around the TRY or handler-selection label.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>bridge-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem (multi) / inherited (if-else)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets space beside action labels; the component aligns the branch exits.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>stem-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>8rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets the requested separation between alternatives.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>attach-to</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Connects continuation to the common branch end.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>step-label</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Defines the continuation action.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>counter-start<br>left-counter-end<br>false-stem-counter<br>right-counter-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>D<br>1<br>2<br>3</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Explicitly numbers the branch nodes from 1 through 4.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>counter-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>1 (flow-step)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Continues numbering after the branch.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-basic"
                example="try-catch-basic"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('C has no native exceptions: its example uses equivalent status handling. The other examples use their native try/catch syntax. Application helpers, enclosing functions and imports are omitted.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('TRY / CATCH — Preview') }}</flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The two individually authored layouts have the same execution order. Green marks successful completion; the other lanes handle failures. The operation is the only action assumed to throw in this example. Handler and cleanup failures will be covered with propagation and early exits.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="try-catch-basic-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- try-catch-basic-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-try-catch-basic-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="44rem"
                            horizontal-padding="4rem"
                        >
                            {{-- The branch geometry represents exception dispatch; no extra IF runs in the program. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.try-catch.basic.left.dispatch"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                side="left"
                                bridge-length="2rem"
                                stem-length="8rem"
                                before-length="2rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['TRY', 'Perform operation'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['SUCCESS', 'Record success'],
                                    'width' => 'default',
                                    'align' => 'center',
                                    'color' => 'green',
                                ]"
                                :counter-start="1"
                                :left-counter-end="2"
                                :false-stem-counter="3"
                                :right-counter-end="4"
                                :if-end="[
                                    'text' => ['CATCH', 'Handle failure'],
                                    'width' => 'default',
                                    'align' => 'center',
                                    'color' => 'red',
                                ]"
                                color="cyan"
                            />
                            {{-- Reached after success or a handler that completes normally. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.try-catch.basic.left.continue"
                                attach-to="literature.try-catch.basic.left.dispatch.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Continue process'],
                                    'width' => 'default',
                                ]"
                                :counter-end="5"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- try-catch-basic-left-example:end --}}
                    </div>

                    <x-translation-workbench::ui.common.heading-counter
                        example="try-catch-basic-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- try-catch-basic-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-try-catch-basic-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="48rem"
                            min-height="44rem"
                            horizontal-padding="4rem"
                        >
                            {{-- The branch geometry represents exception dispatch; no extra IF runs in the program. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.try-catch.basic.right.dispatch"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                side="right"
                                bridge-length="2rem"
                                stem-length="8rem"
                                before-length="2rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['TRY', 'Perform operation'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['SUCCESS', 'Record success'],
                                    'width' => 'default',
                                    'align' => 'center',
                                    'color' => 'green',
                                ]"
                                :counter-start="1"
                                :left-counter-end="2"
                                :false-stem-counter="3"
                                :right-counter-end="4"
                                :if-end="[
                                    'text' => ['CATCH', 'Handle failure'],
                                    'width' => 'default',
                                    'align' => 'center',
                                    'color' => 'red',
                                ]"
                                color="cyan"
                            />
                            {{-- Reached after success or a handler that completes normally. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.try-catch.basic.right.continue"
                                attach-to="literature.try-catch.basic.right.dispatch.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Continue process'],
                                    'width' => 'default',
                                ]"
                                :counter-end="5"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- try-catch-basic-right-example:end --}}
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/try-catch/flow-try-catch-basic.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
