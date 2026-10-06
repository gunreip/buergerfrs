<x-translation-workbench::ui.common.heading-counter-group group="flow-async-await-partial">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Partial success') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Start operations A and B before AWAIT ALL SETTLED. A succeeds with 20; B fails with Load B failed. Wait until both have settled, then collect one fulfilled record and one rejected record in input order. The rejected operation does not discard the successful result or end this combined wait early.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.async-await.flow-async-await-partial" />

            @php
                $asyncSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-partial',
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
                            example="async-await-partial-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('A left / B right') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-partial-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="async-await-partial-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('A right / B left') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-partial-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Sky and violet identify operation A, cyan starts B, green carries success, red carries its error record and zinc collects both outcomes.') }}</flux:table.cell>
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
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-partial"
                example="async-await-partial"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('JavaScript uses Promise.allSettled. C# wraps each operation in an outcome-producing observer before Task.WhenAll; Java converts each completion with handle before allOf. These wrappers preserve success values and error details. Cancellation is not part of this example. Unlike the earlier all-success example, the combined result explicitly contains a failure.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Partial success — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Both lanes run independently. Green carries the successful value, red the failed outcome; both meet at ALL SETTLED. A red outcome at this join is recorded data, not a second unhandled throw. Completion order may differ from the drawing; the records remain ordered A then B.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-partial-left-example"
                        size="sm"
                    >{{ __('A left / B right') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-partial-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-partial-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="72rem"
                            min-height="70rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.partial.left.start-a"
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
                                id="literature.async-await.partial.left.start-b"
                                attach-to="literature.async-await.partial.left.start-a.anchorNode-end"
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
                                id="literature.async-await.partial.left.await-all"
                                attach-to="literature.async-await.partial.left.start-b.anchorNode-end"
                                :step-label="[
                                    'text' => ['AWAIT ALL SETTLED', 'Suspend this function', 'Both lanes participate'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="zinc"
                            />
                            {{-- Independent operation A: both lanes must complete successfully. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.partial.left.a-pending"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-partial-left',
                                    'literature.async-await.partial.left.await-all.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Operation A pending', 'Part of ALL SETTLED'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.partial.left.a-complete"
                                attach-to="literature.async-await.partial.left.a-pending.anchorNode-end"
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
                                id="literature.async-await.partial.left.a-ready"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-partial-left',
                                    'literature.async-await.partial.left.a-complete.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['A fulfilled: 20', 'Wait for all outcomes'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="green"
                            />
                            {{-- Independent operation B may finish before A; positions are not a timeline. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.partial.left.b-pending"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-partial-left',
                                    'literature.async-await.partial.left.await-all.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Operation B pending', 'Part of ALL SETTLED'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="7"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.partial.left.b-complete"
                                attach-to="literature.async-await.partial.left.b-pending.anchorNode-end"
                                :step-label="[
                                    'text' => ['B fails', 'Error: Load B failed'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.partial.left.b-ready"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-partial-left',
                                    'literature.async-await.partial.left.b-complete.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['B rejected: Load B failed', 'Wait for all outcomes'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="9"
                                color="red"
                            />
                            {{-- Both equal-width routes meet here: ALL SETTLED: collect both outcomes. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.partial.left.receive"
                                attach-to="literature.async-await.partial.left.a-ready.anchorNode-end"
                                :step-label="[
                                    'text' => ['BOTH operations settled', 'A: fulfilled, value 20', 'B: rejected, Load B failed'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="10"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.partial.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-partial-left',
                                    'literature.async-await.partial.left.receive.anchorNode-end',
                                )"
                                :dev-counter-end="11"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-partial-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-partial-right-example"
                        size="sm"
                    >{{ __('A right / B left') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-partial-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-partial-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="72rem"
                            min-height="70rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.partial.right.start-a"
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
                                id="literature.async-await.partial.right.start-b"
                                attach-to="literature.async-await.partial.right.start-a.anchorNode-end"
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
                                id="literature.async-await.partial.right.await-all"
                                attach-to="literature.async-await.partial.right.start-b.anchorNode-end"
                                :step-label="[
                                    'text' => ['AWAIT ALL SETTLED', 'Suspend this function', 'Both lanes participate'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="zinc"
                            />
                            {{-- Independent operation A: both lanes must complete successfully. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.partial.right.a-pending"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-partial-right',
                                    'literature.async-await.partial.right.await-all.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Operation A pending', 'Part of ALL SETTLED'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="4"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.partial.right.a-complete"
                                attach-to="literature.async-await.partial.right.a-pending.anchorNode-end"
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
                                id="literature.async-await.partial.right.a-ready"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-partial-right',
                                    'literature.async-await.partial.right.a-complete.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['A fulfilled: 20', 'Wait for all outcomes'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="6"
                                color="green"
                            />
                            {{-- Independent operation B may finish before A; positions are not a timeline. --}}
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.partial.right.b-pending"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-partial-right',
                                    'literature.async-await.partial.right.await-all.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Operation B pending', 'Part of ALL SETTLED'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="7"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.partial.right.b-complete"
                                attach-to="literature.async-await.partial.right.b-pending.anchorNode-end"
                                :step-label="[
                                    'text' => ['B fails', 'Error: Load B failed'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.partial.right.b-ready"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-partial-right',
                                    'literature.async-await.partial.right.b-complete.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['B rejected: Load B failed', 'Wait for all outcomes'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="9"
                                color="red"
                            />
                            {{-- Both equal-width routes meet here: ALL SETTLED: collect both outcomes. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.partial.right.receive"
                                attach-to="literature.async-await.partial.right.a-ready.anchorNode-end"
                                :step-label="[
                                    'text' => ['BOTH operations settled', 'A: fulfilled, value 20', 'B: rejected, Load B failed'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="10"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.partial.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-partial-right',
                                    'literature.async-await.partial.right.receive.anchorNode-end',
                                )"
                                :dev-counter-end="11"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-partial-right-example:end --}}
                    </div>


                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/flow-async-await-partial.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
