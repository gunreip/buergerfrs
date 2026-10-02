<x-translation-workbench::ui.common.heading-counter-group group="flow-async-await-cancellation">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Cancellation') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Inside TRY, start an operation with a cancellation signal and AWAIT it. While this function is suspended, the caller requests cancellation. The operation observes the request, releases its timer and reports cancellation. CATCH handles that cancellation specifically; FINALLY cleans up before returning the cancelled outcome. No successful result is consumed.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.async-await.flow-async-await-cancellation" />

            @php
                $asyncSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-cancellation',
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
                            example="async-await-cancellation-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left" · cancelled operation') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-cancellation-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="async-await-cancellation-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right" · cancelled operation') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-cancellation-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('The side lane shows an external cancellation request followed by acknowledgement from the running operation. These are distinct events, not an immediate forced stop. Resumption at AWAIT carries cancellation into CATCH; FINALLY then runs. The request originates outside the suspended function.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Labels suspension at AWAIT and resumption with cancellation.') }}</flux:table.cell>
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
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Sky marks suspension, amber the cancellation request and CATCH, orange acknowledgement, violet FINALLY and zinc the function.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-end / dev-counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the nine diagnostic markers along the shown cancellation path.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-cancellation"
                example="async-await-cancellation"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('Cancellation is cooperative: the request must be observed by the operation. JavaScript uses an AbortSignal-aware promise; C# passes a CancellationToken to Task.Delay. The snippets keep unrelated failures distinct and also support normal completion. The drawn trace is cancellation while pending; a request after completion does not undo the completed result.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Cancellation — Preview') }}
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
                        example="async-await-cancellation-left-example"
                        size="sm"
                    >{{ __('side="left" · cancelled operation') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-cancellation-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-cancellation-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="78rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.cancellation.left.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['TRY', 'Start loadValueAsync(signal)', 'Operation is cancellable'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.cancellation.left.await"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-cancellation-left',
                                    'literature.async-await.cancellation.left.try.anchorNode-end',
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
                                id="literature.async-await.cancellation.left.request"
                                attach-to="literature.async-await.cancellation.left.await.anchorNode-end"
                                :step-label="[
                                    'text' => ['External caller', 'Request cancellation', 'abort / Cancel'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.cancellation.left.acknowledge"
                                attach-to="literature.async-await.cancellation.left.request.anchorNode-end"
                                :step-label="[
                                    'text' => ['Operation observes signal', 'Stop timer; release listener', 'Report cancellation'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="4"
                                color="orange"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.cancellation.left.cancelled"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-cancellation-left',
                                    'literature.async-await.cancellation.left.acknowledge.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Resume AWAIT with cancellation', 'No success value'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="5"
                                color="orange"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.cancellation.left.catch"
                                attach-to="literature.async-await.cancellation.left.cancelled.anchorNode-end"
                                :step-label="[
                                    'text' => ['CATCH cancellation', 'Record cancelled outcome', 'Other errors propagate'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.cancellation.left.finally"
                                attach-to="literature.async-await.cancellation.left.catch.anchorNode-end"
                                :step-label="[
                                    'text' => ['FINALLY', 'Perform cleanup'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.cancellation.left.continue"
                                attach-to="literature.async-await.cancellation.left.finally.anchorNode-end"
                                :step-label="[
                                    'text' => ['After cleanup', 'RETURN cancelled outcome'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.cancellation.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-cancellation-left',
                                    'literature.async-await.cancellation.left.continue.anchorNode-end',
                                )"
                                :dev-counter-end="9"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-cancellation-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-cancellation-right-example"
                        size="sm"
                    >{{ __('side="right" · cancelled operation') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-cancellation-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-cancellation-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="78rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.cancellation.right.try"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['TRY', 'Start loadValueAsync(signal)', 'Operation is cancellable'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.cancellation.right.await"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-cancellation-right',
                                    'literature.async-await.cancellation.right.try.anchorNode-end',
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
                                id="literature.async-await.cancellation.right.request"
                                attach-to="literature.async-await.cancellation.right.await.anchorNode-end"
                                :step-label="[
                                    'text' => ['External caller', 'Request cancellation', 'abort / Cancel'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.cancellation.right.acknowledge"
                                attach-to="literature.async-await.cancellation.right.request.anchorNode-end"
                                :step-label="[
                                    'text' => ['Operation observes signal', 'Stop timer; release listener', 'Report cancellation'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="4"
                                color="orange"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.cancellation.right.cancelled"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-cancellation-right',
                                    'literature.async-await.cancellation.right.acknowledge.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['Resume AWAIT with cancellation', 'No success value'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="5"
                                color="orange"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.cancellation.right.catch"
                                attach-to="literature.async-await.cancellation.right.cancelled.anchorNode-end"
                                :step-label="[
                                    'text' => ['CATCH cancellation', 'Record cancelled outcome', 'Other errors propagate'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.cancellation.right.finally"
                                attach-to="literature.async-await.cancellation.right.catch.anchorNode-end"
                                :step-label="[
                                    'text' => ['FINALLY', 'Perform cleanup'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.cancellation.right.continue"
                                attach-to="literature.async-await.cancellation.right.finally.anchorNode-end"
                                :step-label="[
                                    'text' => ['After cleanup', 'RETURN cancelled outcome'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.cancellation.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-cancellation-right',
                                    'literature.async-await.cancellation.right.continue.anchorNode-end',
                                )"
                                :dev-counter-end="9"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-cancellation-right-example:end --}}
                    </div>


                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/flow-async-await-cancellation.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
