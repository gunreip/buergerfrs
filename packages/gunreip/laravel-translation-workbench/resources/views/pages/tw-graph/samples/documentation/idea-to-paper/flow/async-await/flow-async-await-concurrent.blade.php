<x-translation-workbench::ui.common.heading-counter-group group="flow-async-await-concurrent">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Concurrent operations') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Start independent operations A and B before the shared AWAIT ALL. Both are in progress when this function suspends. The success continuation requires both results: A = 20 and B = 40. Either operation may finish first; the result tuple remains ordered [A, B]. This example shows successful completion only.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.async-await.flow-async-await-concurrent" />

            @php
                $asyncSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-concurrent',
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
                            example="async-await-concurrent-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('A left / B right') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-concurrent-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="async-await-concurrent-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('A right / B left') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-concurrent-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Both lanes belong to the same execution: they are not TRUE/FALSE alternatives. The shared end represents an ALL-completed join. One completed lane alone must not resume the function. Equal drawing heights make the independent operations comparable and do not imply equal duration or completion order.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>anchor-start / attach-to</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default / null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connects operation start, suspension, completion and continuation in logical order.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('All four sideways components use 2rem bridges and equal label widths, so both result routes meet at the same anchor.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-label</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Labels the suspension and resumption at AWAIT.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Sky and violet identify operation A, cyan and indigo operation B, green tones ready results and zinc the awaiting function.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-end / dev-counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the authored diagnostic markers in execution order.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-concurrent"
                example="async-await-concurrent"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('JavaScript uses Promise.all, C# uses Task.WhenAll and Java combines both futures with allOf. Both operations start before the combined wait. Concurrency does not require parallel threads. Failure and cancellation behavior differs between these APIs and is outside this success-only example.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Concurrent operations — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('The outward route marks suspension at AWAIT. The completion step supplies the value, and the inward route resumes the same function after AWAIT. The side lane represents the waiting and completion relationship, not a required worker thread. Other runnable work is omitted from this first example.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-concurrent-left-example"
                        size="sm"
                    >{{ __('A left / B right') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-concurrent-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-concurrent-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="72rem"
                            min-height="70rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.concurrent.left.start-a"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['Start operation A', 'first = loadAAsync()'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.concurrent.left.start-b"
                                attach-to="literature.async-await.concurrent.left.start-a.anchorNode-end"
                                :step-label="[
                                    'text' => ['Start operation B', 'second = loadBAsync()', 'A may still be pending'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="2"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.concurrent.left.await-all"
                                attach-to="literature.async-await.concurrent.left.start-b.anchorNode-end"
                                :step-label="[
                                    'text' => ['AWAIT ALL [first, second]', 'Suspend this function', 'Both lanes participate'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="zinc"
                            />
                            {{-- Independent operation A: both lanes must complete successfully. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.concurrent.left.a-pending"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-concurrent-left',
                                    'literature.async-await.concurrent.left.await-all.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Operation A pending', 'Part of AWAIT ALL'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.concurrent.left.a-complete"
                                attach-to="literature.async-await.concurrent.left.a-pending.anchorNode-end"
                                :step-label="[
                                    'text' => ['A completes', 'value A = 20'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.concurrent.left.a-ready"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-concurrent-left',
                                    'literature.async-await.concurrent.left.a-complete.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['A ready: 20', 'Join when BOTH are ready'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="green"
                            />
                            {{-- Independent operation B may finish before A; positions are not a timeline. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.concurrent.left.b-pending"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-concurrent-left',
                                    'literature.async-await.concurrent.left.await-all.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Operation B pending', 'Part of AWAIT ALL'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="7"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.concurrent.left.b-complete"
                                attach-to="literature.async-await.concurrent.left.b-pending.anchorNode-end"
                                :step-label="[
                                    'text' => ['B completes', 'value B = 40'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.concurrent.left.b-ready"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-concurrent-left',
                                    'literature.async-await.concurrent.left.b-complete.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['B ready: 40', 'Join when BOTH are ready'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="9"
                                color="emerald"
                            />
                            {{-- Both equal-width routes meet here: ALL, not an alternative merge. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.concurrent.left.receive"
                                attach-to="literature.async-await.concurrent.left.a-ready.anchorNode-end"
                                :step-label="[
                                    'text' => ['BOTH operations completed', 'Resume after AWAIT ALL', 'results = [20, 40]'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="10"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.concurrent.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-concurrent-left',
                                    'literature.async-await.concurrent.left.receive.anchorNode-end',
                                )"
                                :dev-counter-end="11"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-concurrent-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-concurrent-right-example"
                        size="sm"
                    >{{ __('A right / B left') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-concurrent-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-concurrent-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="72rem"
                            min-height="70rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.concurrent.right.start-a"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['Start operation A', 'first = loadAAsync()'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.concurrent.right.start-b"
                                attach-to="literature.async-await.concurrent.right.start-a.anchorNode-end"
                                :step-label="[
                                    'text' => ['Start operation B', 'second = loadBAsync()', 'A may still be pending'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="2"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.concurrent.right.await-all"
                                attach-to="literature.async-await.concurrent.right.start-b.anchorNode-end"
                                :step-label="[
                                    'text' => ['AWAIT ALL [first, second]', 'Suspend this function', 'Both lanes participate'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="zinc"
                            />
                            {{-- Independent operation A: both lanes must complete successfully. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.concurrent.right.a-pending"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-concurrent-right',
                                    'literature.async-await.concurrent.right.await-all.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Operation A pending', 'Part of AWAIT ALL'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.concurrent.right.a-complete"
                                attach-to="literature.async-await.concurrent.right.a-pending.anchorNode-end"
                                :step-label="[
                                    'text' => ['A completes', 'value A = 20'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.concurrent.right.a-ready"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-concurrent-right',
                                    'literature.async-await.concurrent.right.a-complete.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['A ready: 20', 'Join when BOTH are ready'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="green"
                            />
                            {{-- Independent operation B may finish before A; positions are not a timeline. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.concurrent.right.b-pending"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-concurrent-right',
                                    'literature.async-await.concurrent.right.await-all.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Operation B pending', 'Part of AWAIT ALL'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="7"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.concurrent.right.b-complete"
                                attach-to="literature.async-await.concurrent.right.b-pending.anchorNode-end"
                                :step-label="[
                                    'text' => ['B completes', 'value B = 40'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.concurrent.right.b-ready"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-concurrent-right',
                                    'literature.async-await.concurrent.right.b-complete.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['B ready: 40', 'Join when BOTH are ready'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="9"
                                color="emerald"
                            />
                            {{-- Both equal-width routes meet here: ALL, not an alternative merge. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.concurrent.right.receive"
                                attach-to="literature.async-await.concurrent.right.a-ready.anchorNode-end"
                                :step-label="[
                                    'text' => ['BOTH operations completed', 'Resume after AWAIT ALL', 'results = [20, 40]'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="10"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.concurrent.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-concurrent-right',
                                    'literature.async-await.concurrent.right.receive.anchorNode-end',
                                )"
                                :dev-counter-end="11"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-concurrent-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/flow-async-await-concurrent.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
