<x-translation-workbench::ui.common.heading-counter-group group="flow-async-await-timeout">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Timeout') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Arm a 20 ms timeout and start a cancellation-aware operation that normally completes after 100 ms. AWAIT suspends this function. When the timeout callback runs, it requests cancellation; the operation stops its timer and reports that timeout reason. CATCH records a timeout outcome, and FINALLY clears the deadline timer. The shown path is timeout before completion.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.async-await.flow-async-await-timeout" />

            @php
                $asyncSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-timeout',
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
                            example="async-await-timeout-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left" · timeout requests cancellation') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-timeout-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="async-await-timeout-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right" · timeout requests cancellation') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-timeout-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Follow timeout expiry, cancellation acknowledgement, CATCH timeout and FINALLY in that order. The timeout callback is external to the suspended function. Cleanup clears both the operation timer and the deadline timer on this path. If the operation finishes first, its value is kept and the pending deadline is removed.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>anchor-start / attach-to</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default / null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connects operation start, suspension, completion and continuation in logical order.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Both sideways components use 2rem bridges and equal label widths to return to the original function lane.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-label</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Labels suspension at AWAIT and resumption with the timeout reason.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Sky marks suspension, amber the timeout request and CATCH, orange acknowledgement, violet FINALLY and zinc the function.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-end / dev-counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the nine diagnostic markers along the shown timeout path.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-timeout"
                example="async-await-timeout"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('This example cancels the underlying cooperative operation, rather than merely abandoning the wait. The timeout is a cancellation request, not a guaranteed maximum duration: scheduling and cancellation acknowledgement may take time. Normal completion clears the deadline; unrelated errors still propagate. JavaScript and C# provide runnable examples; the other tabs describe runtime-specific requirements.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Timeout — Preview') }}
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
                        example="async-await-timeout-left-example"
                        size="sm"
                    >{{ __('side="left" · timeout requests cancellation') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-timeout-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-timeout-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="78rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.timeout.left.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['TRY: arm timeout = 20 ms', 'Start cancellable operation', 'Normal duration = 100 ms'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.timeout.left.await"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-timeout-left',
                                    'literature.async-await.timeout.left.try.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['AWAIT pending', 'Suspend this function'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.timeout.left.request"
                                attach-to="literature.async-await.timeout.left.await.anchorNode-end"
                                :step-label="[
                                    'text' => ['Timeout callback runs', '20 ms deadline reached', 'Request cancellation'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.timeout.left.acknowledge"
                                attach-to="literature.async-await.timeout.left.request.anchorNode-end"
                                :step-label="[
                                    'text' => ['Operation observes signal', 'Stop timer; release listener', 'Report timeout reason'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="4"
                                color="orange"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.timeout.left.cancelled"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-timeout-left',
                                    'literature.async-await.timeout.left.acknowledge.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['AWAIT reports timeout', 'No success value'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="5"
                                color="orange"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.timeout.left.catch"
                                attach-to="literature.async-await.timeout.left.cancelled.anchorNode-end"
                                :step-label="[
                                    'text' => ['CATCH timeout', 'Record timeout outcome', 'Other errors propagate'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.timeout.left.finally"
                                attach-to="literature.async-await.timeout.left.catch.anchorNode-end"
                                :step-label="[
                                    'text' => ['FINALLY', 'Clear deadline timer'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.timeout.left.continue"
                                attach-to="literature.async-await.timeout.left.finally.anchorNode-end"
                                :step-label="[
                                    'text' => ['After cleanup', 'RETURN timeout outcome'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.timeout.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-timeout-left',
                                    'literature.async-await.timeout.left.continue.anchorNode-end',
                                )"
                                :dev-counter-end="9"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-timeout-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-timeout-right-example"
                        size="sm"
                    >{{ __('side="right" · timeout requests cancellation') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-timeout-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-timeout-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="78rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.timeout.right.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['TRY: arm timeout = 20 ms', 'Start cancellable operation', 'Normal duration = 100 ms'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.timeout.right.await"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-timeout-right',
                                    'literature.async-await.timeout.right.try.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['AWAIT pending', 'Suspend this function'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.timeout.right.request"
                                attach-to="literature.async-await.timeout.right.await.anchorNode-end"
                                :step-label="[
                                    'text' => ['Timeout callback runs', '20 ms deadline reached', 'Request cancellation'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.timeout.right.acknowledge"
                                attach-to="literature.async-await.timeout.right.request.anchorNode-end"
                                :step-label="[
                                    'text' => ['Operation observes signal', 'Stop timer; release listener', 'Report timeout reason'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="4"
                                color="orange"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.timeout.right.cancelled"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-timeout-right',
                                    'literature.async-await.timeout.right.acknowledge.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['AWAIT reports timeout', 'No success value'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="5"
                                color="orange"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.timeout.right.catch"
                                attach-to="literature.async-await.timeout.right.cancelled.anchorNode-end"
                                :step-label="[
                                    'text' => ['CATCH timeout', 'Record timeout outcome', 'Other errors propagate'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.timeout.right.finally"
                                attach-to="literature.async-await.timeout.right.catch.anchorNode-end"
                                :step-label="[
                                    'text' => ['FINALLY', 'Clear deadline timer'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.timeout.right.continue"
                                attach-to="literature.async-await.timeout.right.finally.anchorNode-end"
                                :step-label="[
                                    'text' => ['After cleanup', 'RETURN timeout outcome'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.timeout.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-timeout-right',
                                    'literature.async-await.timeout.right.continue.anchorNode-end',
                                )"
                                :dev-counter-end="9"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-timeout-right-example:end --}}
                    </div>


                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/flow-async-await-timeout.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
