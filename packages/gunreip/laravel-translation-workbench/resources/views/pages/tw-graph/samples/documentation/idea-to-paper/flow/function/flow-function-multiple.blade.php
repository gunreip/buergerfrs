<x-translation-workbench::ui.common.heading-counter-group group="flow-function-multiple">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Multiple call sites') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The caller invokes the same addOne(value) function at two separate call sites: first with 10, then with 40. The first RETURN assigns first = 11 before the second call starts. The second RETURN assigns second = 41 and continues to the final output. There is one function definition and two sequential invocations.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.function.flow-function-multiple" />

            @php
                $functionSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-multiple',
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
                            example="function-multiple-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $functionSource->example('function-multiple-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="function-multiple-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $functionSource->example('function-multiple-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Both calls enter the same function lane; each return reaches its own call-site continuation. The second layout mirrors all side routes.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Shows both call sites and their distinct return values, 11 and 41.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Sky and cyan distinguish call sites. Both violet bodies execute the same function definition; green tones mark returns and zinc the caller.') }}</flux:table.cell>
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
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.function.flow-function-multiple"
                example="function-multiple"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('Each snippet defines addOne only once and calls it twice. Each call has its own parameter and local result. The two function-body drawings are execution instances of that one definition, not separate functions or concurrent work. Return values are assigned to first and second at their respective call sites.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Multiple call sites — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Follow call site 1 into invocation 1 and back to its first assignment. Only then does call site 2 enter invocation 2. Both calls use the same function lane, but their RETURN connections end at different points on the caller lane. Matching labels identify each invocation.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="function-multiple-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- function-multiple-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-function-multiple-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="72rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.multiple.left.prepare"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['Caller', 'Prepare two calls'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            {{-- Call site 1 invokes the single addOne definition with 10. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.multiple.left.first-call"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-multiple-left',
                                    'literature.function.multiple.left.prepare.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Call site 1: addOne(10)', 'Assign first after RETURN'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.multiple.left.first-body"
                                attach-to="literature.function.multiple.left.first-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['addOne: invocation 1', 'value = 10', 'result = value + 1 = 11'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.multiple.left.first-return"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-multiple-left',
                                    'literature.function.multiple.left.first-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 11', 'Resume call site 1'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.multiple.left.first-resume"
                                attach-to="literature.function.multiple.left.first-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller after call site 1', 'first = 11', 'Proceed to second call'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="zinc"
                            />
                            {{-- Call site 2 invokes the same definition with a new parameter value. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.multiple.left.second-call"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-multiple-left',
                                    'literature.function.multiple.left.first-resume.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Call site 2: addOne(40)', 'Assign second after RETURN'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.multiple.left.second-body"
                                attach-to="literature.function.multiple.left.second-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['addOne: invocation 2', 'value = 40', 'result = value + 1 = 41'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.multiple.left.second-return"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-multiple-left',
                                    'literature.function.multiple.left.second-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 41', 'Resume call site 2'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.multiple.left.second-resume"
                                attach-to="literature.function.multiple.left.second-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller after call site 2', 'second = 41', 'Display first and second'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.function.multiple.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-multiple-left',
                                    'literature.function.multiple.left.second-resume.anchorNode-end',
                                )"
                                :dev-counter-end="10"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- function-multiple-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="function-multiple-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- function-multiple-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-function-multiple-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="72rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.multiple.right.prepare"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['Caller', 'Prepare two calls'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            {{-- Call site 1 invokes the single addOne definition with 10. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.multiple.right.first-call"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-multiple-right',
                                    'literature.function.multiple.right.prepare.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Call site 1: addOne(10)', 'Assign first after RETURN'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.multiple.right.first-body"
                                attach-to="literature.function.multiple.right.first-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['addOne: invocation 1', 'value = 10', 'result = value + 1 = 11'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.multiple.right.first-return"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-multiple-right',
                                    'literature.function.multiple.right.first-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 11', 'Resume call site 1'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.multiple.right.first-resume"
                                attach-to="literature.function.multiple.right.first-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller after call site 1', 'first = 11', 'Proceed to second call'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="zinc"
                            />
                            {{-- Call site 2 invokes the same definition with a new parameter value. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.multiple.right.second-call"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-multiple-right',
                                    'literature.function.multiple.right.first-resume.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Call site 2: addOne(40)', 'Assign second after RETURN'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.multiple.right.second-body"
                                attach-to="literature.function.multiple.right.second-call.anchorNode-end"
                                :step-label="[
                                    'text' => ['addOne: invocation 2', 'value = 40', 'result = value + 1 = 41'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.function.multiple.right.second-return"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-multiple-right',
                                    'literature.function.multiple.right.second-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN 41', 'Resume call site 2'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.function.multiple.right.second-resume"
                                attach-to="literature.function.multiple.right.second-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Caller after call site 2', 'second = 41', 'Display first and second'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.function.multiple.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-function-multiple-right',
                                    'literature.function.multiple.right.second-resume.anchorNode-end',
                                )"
                                :dev-counter-end="10"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- function-multiple-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/function/flow-function-multiple.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
