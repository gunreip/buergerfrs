<x-translation-workbench::ui.common.heading-counter-group group="flow-callback-interchangeable">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Interchangeable callbacks') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The caller invokes apply twice with the same value 20. The first call receives addOne and returns 21. The second receives doubleValue and returns 40. Only the supplied callback changes; apply keeps the same implementation and invokes callback(value) once per call.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.callback.flow-callback-interchangeable" />

            @php
                $callbackSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-interchangeable',
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
                            example="callback-interchangeable-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $callbackSource->example('callback-interchangeable-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="callback-interchangeable-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $callbackSource->example('callback-interchangeable-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('All eight sideways components use 2rem around equally wide labels, so each RETURN reaches the lane of its own caller.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Sky marks calls to apply, cyan callback invocations, violet apply, indigo addOne, amber doubleValue and green tones the returns.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-end / dev-counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the eighteen visible markers in execution order.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-interchangeable"
                example="callback-interchangeable"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('Each snippet defines apply once and passes two different named callbacks. Both callbacks accept one integer and return one integer. The calls execute sequentially, and each result returns through its own invocation of apply before the caller continues.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Interchangeable callbacks — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The first sequence passes addOne, while the second passes doubleValue. Both use the same receiving-function lane and callback lane at different times. The labels identify the selected callback and the returned value at every call site.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="callback-interchangeable-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- callback-interchangeable-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-callback-interchangeable-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="64rem"
                            min-height="132rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.left.prepare"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['Caller', 'value = 20'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.left.first-call"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-left',
                                    'literature.callback.interchangeable.left.prepare.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL apply(20, addOne)', 'First callback: addOne'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.left.first-enter"
                                attach-to="literature.callback.interchangeable.left.first-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside apply: call 1', 'callback refers to addOne'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.left.first-invoke"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-left',
                                    'literature.callback.interchangeable.left.first-enter.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL callback(20)', 'Invokes addOne(20)'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.left.first-body"
                                attach-to="literature.callback.interchangeable.left.first-invoke.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside addOne', '20 + 1 = 21'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.left.first-return"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-left',
                                    'literature.callback.interchangeable.left.first-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 21', 'Resume apply: call 1'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.left.first-result"
                                attach-to="literature.callback.interchangeable.left.first-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside apply: call 1', 'result = 21'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.left.first-exit"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-left',
                                    'literature.callback.interchangeable.left.first-result.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 21', 'Resume original caller'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.left.between"
                                attach-to="literature.callback.interchangeable.left.first-exit.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller resumes', 'first = 21', 'Select doubleValue next'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.left.second-call"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-left',
                                    'literature.callback.interchangeable.left.between.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL apply(20, doubleValue)', 'Second callback: doubleValue'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="10"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.left.second-enter"
                                attach-to="literature.callback.interchangeable.left.second-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside apply: call 2', 'callback refers to doubleValue'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="11"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.left.second-invoke"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-left',
                                    'literature.callback.interchangeable.left.second-enter.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL callback(20)', 'Invokes doubleValue(20)'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="12"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.left.second-body"
                                attach-to="literature.callback.interchangeable.left.second-invoke.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside doubleValue', '20 × 2 = 40'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="13"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.left.second-return"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-left',
                                    'literature.callback.interchangeable.left.second-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 40', 'Resume apply: call 2'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="14"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.left.second-result"
                                attach-to="literature.callback.interchangeable.left.second-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside apply: call 2', 'result = 40'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="15"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.left.second-exit"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-left',
                                    'literature.callback.interchangeable.left.second-result.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 40', 'Resume original caller'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="16"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.left.receive"
                                attach-to="literature.callback.interchangeable.left.second-exit.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller resumes', 'second = 40', 'Display first and second'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="17"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.callback.interchangeable.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-left',
                                    'literature.callback.interchangeable.left.receive.anchorNode-end',
                                )"
                                :dev-counter-end="18"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- callback-interchangeable-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="callback-interchangeable-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- callback-interchangeable-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-callback-interchangeable-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="64rem"
                            min-height="132rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.right.prepare"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['Caller', 'value = 20'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.right.first-call"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-right',
                                    'literature.callback.interchangeable.right.prepare.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL apply(20, addOne)', 'First callback: addOne'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.right.first-enter"
                                attach-to="literature.callback.interchangeable.right.first-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside apply: call 1', 'callback refers to addOne'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.right.first-invoke"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-right',
                                    'literature.callback.interchangeable.right.first-enter.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL callback(20)', 'Invokes addOne(20)'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.right.first-body"
                                attach-to="literature.callback.interchangeable.right.first-invoke.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside addOne', '20 + 1 = 21'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.right.first-return"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-right',
                                    'literature.callback.interchangeable.right.first-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 21', 'Resume apply: call 1'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.right.first-result"
                                attach-to="literature.callback.interchangeable.right.first-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside apply: call 1', 'result = 21'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.right.first-exit"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-right',
                                    'literature.callback.interchangeable.right.first-result.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 21', 'Resume original caller'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.right.between"
                                attach-to="literature.callback.interchangeable.right.first-exit.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller resumes', 'first = 21', 'Select doubleValue next'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.right.second-call"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-right',
                                    'literature.callback.interchangeable.right.between.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL apply(20, doubleValue)', 'Second callback: doubleValue'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="10"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.right.second-enter"
                                attach-to="literature.callback.interchangeable.right.second-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside apply: call 2', 'callback refers to doubleValue'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="11"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.right.second-invoke"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-right',
                                    'literature.callback.interchangeable.right.second-enter.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL callback(20)', 'Invokes doubleValue(20)'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="12"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.right.second-body"
                                attach-to="literature.callback.interchangeable.right.second-invoke.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside doubleValue', '20 × 2 = 40'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="13"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.right.second-return"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-right',
                                    'literature.callback.interchangeable.right.second-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 40', 'Resume apply: call 2'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="14"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.right.second-result"
                                attach-to="literature.callback.interchangeable.right.second-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside apply: call 2', 'result = 40'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="15"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.interchangeable.right.second-exit"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-right',
                                    'literature.callback.interchangeable.right.second-result.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 40', 'Resume original caller'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="16"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.interchangeable.right.receive"
                                attach-to="literature.callback.interchangeable.right.second-exit.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller resumes', 'second = 40', 'Display first and second'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="17"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.callback.interchangeable.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-interchangeable-right',
                                    'literature.callback.interchangeable.right.receive.anchorNode-end',
                                )"
                                :dev-counter-end="18"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- callback-interchangeable-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/callback/flow-callback-interchangeable.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
