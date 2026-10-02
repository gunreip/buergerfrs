<x-translation-workbench::ui.common.heading-counter-group group="flow-callback-basic">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Pass and invoke a callback') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The caller passes addOne as a callable together with value 41 to apply(value, callback). Passing the function does not execute it. Inside apply, callback(value) invokes addOne(41), receives 42 and returns that result to the original caller.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.callback.flow-callback-basic" />

            @php
                $callbackSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-basic',
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
                            example="callback-basic-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $callbackSource->example('callback-basic-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="callback-basic-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $callbackSource->example('callback-basic-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Calls enter the receiving function and then the callback on separate lanes. Returns go back to their immediate callers.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>anchor-start / attach-to</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default / null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connects preparation, call, function body, return and caller continuation in execution order.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('All four sideways components use 2rem around equally wide labels, so each RETURN reaches the lane of its own caller.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-label</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Distinguishes passing a callable, invoking it and returning its result.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Sky marks the call to apply, cyan the callback invocation, violet and indigo the function bodies and green tones the returns.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-end / dev-counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the ten visible markers in execution order.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-basic"
                example="callback-basic"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('All six snippets pass a named function without calling it first. PHP uses callable, JavaScript a function value, C and C++ a function pointer, C# a delegate and Java an IntUnaryOperator method reference. This callback executes synchronously: apply waits for its return before continuing.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Pass and invoke a callback — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The first lane enters apply with the callback reference. The second enters addOne only when apply invokes callback(value). The inner RETURN resumes apply; the outer RETURN resumes the original caller. Passing and invoking the callback are separate steps.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="callback-basic-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- callback-basic-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-callback-basic-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="64rem"
                            min-height="72rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.basic.left.prepare"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['Caller', 'value = 41', 'callback = addOne (not called)'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.basic.left.apply-call"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-basic-left',
                                    'literature.callback.basic.left.prepare.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL apply(41, addOne)', 'Pass value and callable'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.basic.left.apply-enter"
                                attach-to="literature.callback.basic.left.apply-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside apply', 'value = 41', 'callback refers to addOne'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.basic.left.invoke"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-basic-left',
                                    'literature.callback.basic.left.apply-enter.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL callback(value)', 'Invokes addOne(41)'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.basic.left.callback-body"
                                attach-to="literature.callback.basic.left.invoke.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside addOne', 'result = value + 1', '41 + 1 = 42'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.basic.left.callback-return"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-basic-left',
                                    'literature.callback.basic.left.callback-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 42', 'Resume apply'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.basic.left.apply-resume"
                                attach-to="literature.callback.basic.left.callback-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside apply again', 'result = 42', 'Return callback result'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.basic.left.apply-return"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-basic-left',
                                    'literature.callback.basic.left.apply-resume.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 42', 'Resume original caller'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.basic.left.receive"
                                attach-to="literature.callback.basic.left.apply-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller resumes', 'result = 42', 'Display result'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.callback.basic.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-basic-left',
                                    'literature.callback.basic.left.receive.anchorNode-end',
                                )"
                                :dev-counter-end="10"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- callback-basic-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="callback-basic-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- callback-basic-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-callback-basic-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="64rem"
                            min-height="72rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.basic.right.prepare"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['Caller', 'value = 41', 'callback = addOne (not called)'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.basic.right.apply-call"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-basic-right',
                                    'literature.callback.basic.right.prepare.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL apply(41, addOne)', 'Pass value and callable'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.basic.right.apply-enter"
                                attach-to="literature.callback.basic.right.apply-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside apply', 'value = 41', 'callback refers to addOne'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.basic.right.invoke"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-basic-right',
                                    'literature.callback.basic.right.apply-enter.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL callback(value)', 'Invokes addOne(41)'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.basic.right.callback-body"
                                attach-to="literature.callback.basic.right.invoke.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside addOne', 'result = value + 1', '41 + 1 = 42'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.basic.right.callback-return"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-basic-right',
                                    'literature.callback.basic.right.callback-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 42', 'Resume apply'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.basic.right.apply-resume"
                                attach-to="literature.callback.basic.right.callback-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside apply again', 'result = 42', 'Return callback result'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.basic.right.apply-return"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-basic-right',
                                    'literature.callback.basic.right.apply-resume.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 42', 'Resume original caller'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.basic.right.receive"
                                attach-to="literature.callback.basic.right.apply-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller resumes', 'result = 42', 'Display result'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.callback.basic.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-basic-right',
                                    'literature.callback.basic.right.receive.anchorNode-end',
                                )"
                                :dev-counter-end="10"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- callback-basic-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/callback/flow-callback-basic.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
