<x-translation-workbench::ui.common.heading-counter-group group="flow-function-nested">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Nested function calls') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The caller invokes doubleAdjusted(20). That function calls addOne(value), receives 21, multiplies it by two and returns 42. The inner RETURN resumes doubleAdjusted(), not the original caller. Only the outer RETURN reaches the caller, which then displays its result.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.function.flow-function-nested" />

            @php
                $functionSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-nested',
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
                            example="function-nested-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $functionSource->example('function-nested-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="function-nested-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $functionSource->example('function-nested-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Each right-sided parts.sideways enters a further left lane. Each left-sided return goes back one level.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Shows the two calls and their distinct return values, 21 and 42.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Sky and cyan identify the calls; violet and indigo distinguish the function bodies. Green tones show returns and zinc the original caller.') }}</flux:table.cell>
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
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-nested"
                example="function-nested"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('All six snippets use the same call order: caller → doubleAdjusted → addOne → doubleAdjusted → caller. These are nested synchronous calls to separate functions, not nested function declarations or recursion. Each caller waits until its callee returns before assigning the result and executing its next statement.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Nested function calls — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The first side route enters the outer function. The second enters addOne() on a further offset lane. Its RETURN goes back one level, where multiplication takes place. The outer RETURN then goes back to the original caller lane. Both levels have separate IDs and explicit connections.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="function-nested-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- function-nested-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-function-nested-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="64rem"
                            min-height="72rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.nested.left.prepare"
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
                                id="literature.function.nested.left.outer-call"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-nested-left',
                                    'literature.function.nested.left.prepare.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL doubleAdjusted(value)', 'Argument: 20'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.nested.left.outer-enter"
                                attach-to="literature.function.nested.left.outer-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside doubleAdjusted(value)', 'Prepare nested call'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            {{-- The inner call starts from the outer function, not from the original caller. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.nested.left.inner-call"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-nested-left',
                                    'literature.function.nested.left.outer-enter.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL addOne(value)', 'Argument: 20'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.nested.left.inner-body"
                                attach-to="literature.function.nested.left.inner-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside addOne(value)', 'result = value + 1', '20 + 1 = 21'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.nested.left.inner-return"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-nested-left',
                                    'literature.function.nested.left.inner-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN result = 21', 'Resume doubleAdjusted'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.nested.left.outer-resume"
                                attach-to="literature.function.nested.left.inner-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Back in doubleAdjusted', 'adjusted = 21', 'result = adjusted × 2 = 42'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            {{-- Only this RETURN resumes the original caller. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.nested.left.outer-return"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-nested-left',
                                    'literature.function.nested.left.outer-resume.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN result = 42', 'Resume original caller'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.nested.left.receive"
                                attach-to="literature.function.nested.left.outer-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller resumes', 'Assign result = 42', 'Display result'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.function.nested.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-nested-left',
                                    'literature.function.nested.left.receive.anchorNode-end',
                                )"
                                :dev-counter-end="10"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- function-nested-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="function-nested-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- function-nested-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-function-nested-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="64rem"
                            min-height="72rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.nested.right.prepare"
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
                                id="literature.function.nested.right.outer-call"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-nested-right',
                                    'literature.function.nested.right.prepare.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL doubleAdjusted(value)', 'Argument: 20'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.nested.right.outer-enter"
                                attach-to="literature.function.nested.right.outer-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside doubleAdjusted(value)', 'Prepare nested call'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            {{-- The inner call starts from the outer function, not from the original caller. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.nested.right.inner-call"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-nested-right',
                                    'literature.function.nested.right.outer-enter.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL addOne(value)', 'Argument: 20'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.nested.right.inner-body"
                                attach-to="literature.function.nested.right.inner-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside addOne(value)', 'result = value + 1', '20 + 1 = 21'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.nested.right.inner-return"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-nested-right',
                                    'literature.function.nested.right.inner-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN result = 21', 'Resume doubleAdjusted'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.nested.right.outer-resume"
                                attach-to="literature.function.nested.right.inner-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Back in doubleAdjusted', 'adjusted = 21', 'result = adjusted × 2 = 42'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            {{-- Only this RETURN resumes the original caller. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.nested.right.outer-return"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-nested-right',
                                    'literature.function.nested.right.outer-resume.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN result = 42', 'Resume original caller'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.nested.right.receive"
                                attach-to="literature.function.nested.right.outer-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller resumes', 'Assign result = 42', 'Display result'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.function.nested.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-nested-right',
                                    'literature.function.nested.right.receive.anchorNode-end',
                                )"
                                :dev-counter-end="10"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- function-nested-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/function/flow-function-nested.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
