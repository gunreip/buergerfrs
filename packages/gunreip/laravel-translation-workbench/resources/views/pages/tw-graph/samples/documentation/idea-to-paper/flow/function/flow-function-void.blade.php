<x-translation-workbench::ui.common.heading-counter-group group="flow-function-void">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Function without return value') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The caller displays before and calls logMessage(message). The function displays Processed item and reaches the end of its body. Control returns to the caller, which displays after. No result assignment is made: the useful effect is the output performed inside the function.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.function.flow-function-void" />

            @php
                $functionSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-void',
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
                            example="function-void-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $functionSource->example('function-void-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="function-void-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $functionSource->example('function-void-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('For parts.sideways, right routes left and left routes right. The second layout reverses both routes.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>anchor-start / attach-to</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default / null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connects preparation, call, function body, return and caller continuation in execution order.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Both sideways components explicitly use 2rem around equally wide labels so the control return reaches the caller lane.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-label</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Shows the call and the return of control without a result value.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>step-label.text</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Each authored entry is one line; the step gap is automatic.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>before-length / after-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Provides space before and after each step label.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>color</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>inherited</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Sky identifies the call, violet the function body, green the return and zinc the caller.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-end / dev-counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the six visible markers in execution order.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-void"
                example="function-void"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('PHP, C, C++ and Java use void functions or methods; C# uses a void method. JavaScript has no void return declaration here and completes with undefined, which the caller ignores. Each function reaches its closing brace without an explicit RETURN statement. All snippets output before, Processed item, after in that order.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Function without return value — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The sky lane calls the function; the violet action displays the message. The green lane represents returning control at the end of the function body, not a returned data value. The caller then executes its next statement.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="function-void-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- function-void-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-function-void-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="44rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.void.left.prepare"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="['text' => ['Caller', 'Display before', 'message = Processed item'], 'width' => 'default']"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            {{-- The caller waits here until logMessage finishes. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.void.left.call"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-void-left',
                                    'literature.function.void.left.prepare.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL logMessage(message)', 'No result assignment'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.void.left.body"
                                attach-to="literature.function.void.left.call.anchorNode-end"
                                :step-label="['text' => ['Inside logMessage(message)', 'Display Processed item'], 'width' => 'default']"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            {{-- Same bridge widths bring the return back onto the caller lane. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.void.left.return"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-void-left',
                                    'literature.function.void.left.body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['End of function body', 'Return control only'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.void.left.continue"
                                attach-to="literature.function.void.left.return.anchorNode-end"
                                :step-label="['text' => ['Caller resumes', 'Display after'], 'width' => 'default']"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.function.void.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-void-left',
                                    'literature.function.void.left.continue.anchorNode-end',
                                )"
                                :dev-counter-end="6"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- function-void-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="function-void-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- function-void-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-function-void-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="44rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.void.right.prepare"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="['text' => ['Caller', 'Display before', 'message = Processed item'], 'width' => 'default']"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            {{-- The caller waits here until logMessage finishes. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.void.right.call"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-void-right',
                                    'literature.function.void.right.prepare.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL logMessage(message)', 'No result assignment'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.void.right.body"
                                attach-to="literature.function.void.right.call.anchorNode-end"
                                :step-label="['text' => ['Inside logMessage(message)', 'Display Processed item'], 'width' => 'default']"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            {{-- Same bridge widths bring the return back onto the caller lane. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.void.right.return"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-void-right',
                                    'literature.function.void.right.body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['End of function body', 'Return control only'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.void.right.continue"
                                attach-to="literature.function.void.right.return.anchorNode-end"
                                :step-label="['text' => ['Caller resumes', 'Display after'], 'width' => 'default']"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.function.void.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-void-right',
                                    'literature.function.void.right.continue.anchorNode-end',
                                )"
                                :dev-counter-end="6"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- function-void-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/function/flow-function-void.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
