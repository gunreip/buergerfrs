<x-translation-workbench::ui.common.heading-counter-group group="flow-function-recursion">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Recursion with a base case') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('A concrete execution of factorial(2) creates three separate call frames for n = 2, n = 1 and n = 0. Each invocation checks n == 0. The first two checks are FALSE and recurse with n - 1; the third is TRUE and returns 1 without another call. Returns unwind one frame at a time until the caller receives 2.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.function.flow-function-recursion" />

            @php
                $functionSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-recursion',
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
                            example="function-recursion-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $functionSource->example('function-recursion-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="function-recursion-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $functionSource->example('function-recursion-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Each call enters a further frame. Each return goes back one level to its immediate caller. The second layout mirrors all side routes.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>anchor-start / attach-to</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default / null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connects preparation, call, function body, return and caller continuation in execution order.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('All six sideways components use 2rem around equally wide labels, so each RETURN reaches the lane of its own caller.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-label</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Identifies the recursive call frames and their return values.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Blue tones identify calls, violet and indigo distinguish suspended frames, amber marks the base case and green tones mark returns.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-end / dev-counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the fourteen visible markers in execution order.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-recursion"
                example="function-recursion"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('The graph unfolds one execution for n = 2, rather than displaying every possible branch. Each snippet defines factorial once. The base case stops recursion; each other frame waits for factorial(n - 1), multiplies the returned value by its own n and returns the result. This teaching example uses small non-negative integers; validation and numeric overflow are outside its scope.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Recursion with a base case — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Follow the calls into three separate frames of the same function. The base case returns to frame n = 1, which then returns to frame n = 2. Only the final RETURN resumes the original caller. Local n values remain distinct, and the recursive calls finish in reverse order.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="function-recursion-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- function-recursion-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-function-recursion-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="88rem"
                            min-height="96rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.left.prepare"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['Caller', 'Calculate factorial(2)'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.recursion.left.call-2"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-left',
                                    'literature.function.recursion.left.prepare.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL factorial(2)', 'Enter frame n = 2'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.left.frame-2"
                                attach-to="literature.function.recursion.left.call-2.anchorNode-end"
                                :step-label="[
                                    'text' => ['Frame n = 2', 'IF n == 0? FALSE', 'Wait for factorial(1)'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.recursion.left.call-1"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-left',
                                    'literature.function.recursion.left.frame-2.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL factorial(1)', 'Enter frame n = 1'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.left.frame-1"
                                attach-to="literature.function.recursion.left.call-1.anchorNode-end"
                                :step-label="[
                                    'text' => ['Frame n = 1', 'IF n == 0? FALSE', 'Wait for factorial(0)'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.recursion.left.call-0"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-left',
                                    'literature.function.recursion.left.frame-1.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL factorial(0)', 'Enter frame n = 0'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="blue"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.left.base-case"
                                attach-to="literature.function.recursion.left.call-0.anchorNode-end"
                                :step-label="[
                                    'text' => ['Frame n = 0', 'IF n == 0? TRUE', 'Base case: RETURN 1'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.recursion.left.return-0"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-left',
                                    'literature.function.recursion.left.base-case.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 1', 'Resume frame n = 1'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.left.resume-1"
                                attach-to="literature.function.recursion.left.return-0.anchorNode-end"
                                :step-label="[
                                    'text' => ['Frame n = 1 resumes', 'result = n × 1', 'result = 1'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.recursion.left.return-1"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-left',
                                    'literature.function.recursion.left.resume-1.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 1', 'Resume frame n = 2'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="10"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.left.resume-2"
                                attach-to="literature.function.recursion.left.return-1.anchorNode-end"
                                :step-label="[
                                    'text' => ['Frame n = 2 resumes', 'result = n × 1', 'result = 2'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="11"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.recursion.left.return-2"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-left',
                                    'literature.function.recursion.left.resume-2.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 2', 'Resume original caller'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="12"
                                color="lime"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.left.receive"
                                attach-to="literature.function.recursion.left.return-2.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller resumes', 'Assign result = 2', 'Display result'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="13"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.function.recursion.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-left',
                                    'literature.function.recursion.left.receive.anchorNode-end',
                                )"
                                :dev-counter-end="14"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- function-recursion-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="function-recursion-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- function-recursion-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-function-recursion-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="88rem"
                            min-height="96rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.right.prepare"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['Caller', 'Calculate factorial(2)'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.recursion.right.call-2"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-right',
                                    'literature.function.recursion.right.prepare.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL factorial(2)', 'Enter frame n = 2'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.right.frame-2"
                                attach-to="literature.function.recursion.right.call-2.anchorNode-end"
                                :step-label="[
                                    'text' => ['Frame n = 2', 'IF n == 0? FALSE', 'Wait for factorial(1)'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.recursion.right.call-1"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-right',
                                    'literature.function.recursion.right.frame-2.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL factorial(1)', 'Enter frame n = 1'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.right.frame-1"
                                attach-to="literature.function.recursion.right.call-1.anchorNode-end"
                                :step-label="[
                                    'text' => ['Frame n = 1', 'IF n == 0? FALSE', 'Wait for factorial(0)'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.recursion.right.call-0"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-right',
                                    'literature.function.recursion.right.frame-1.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL factorial(0)', 'Enter frame n = 0'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="blue"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.right.base-case"
                                attach-to="literature.function.recursion.right.call-0.anchorNode-end"
                                :step-label="[
                                    'text' => ['Frame n = 0', 'IF n == 0? TRUE', 'Base case: RETURN 1'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.recursion.right.return-0"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-right',
                                    'literature.function.recursion.right.base-case.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 1', 'Resume frame n = 1'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.right.resume-1"
                                attach-to="literature.function.recursion.right.return-0.anchorNode-end"
                                :step-label="[
                                    'text' => ['Frame n = 1 resumes', 'result = n × 1', 'result = 1'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.recursion.right.return-1"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-right',
                                    'literature.function.recursion.right.resume-1.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 1', 'Resume frame n = 2'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="10"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.right.resume-2"
                                attach-to="literature.function.recursion.right.return-1.anchorNode-end"
                                :step-label="[
                                    'text' => ['Frame n = 2 resumes', 'result = n × 1', 'result = 2'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="11"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.recursion.right.return-2"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-right',
                                    'literature.function.recursion.right.resume-2.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 2', 'Resume original caller'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="12"
                                color="lime"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.recursion.right.receive"
                                attach-to="literature.function.recursion.right.return-2.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller resumes', 'Assign result = 2', 'Display result'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="13"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.function.recursion.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-recursion-right',
                                    'literature.function.recursion.right.receive.anchorNode-end',
                                )"
                                :dev-counter-end="14"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- function-recursion-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/function/flow-function-recursion.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
