<x-translation-workbench::ui.common.heading-counter-group group="flow-async-await-stream">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Async iteration / Stream') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Consume an asynchronous source containing 10, 20 and 30. Each request for the next item can suspend. This trace processes two items, then BREAK stops consumption and awaits source cleanup. The third item is never requested. This differs from awaiting work over an already available collection.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.async-await.flow-async-await-stream" />

            @php
                $asyncSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-stream',
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
                            example="async-await-stream-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Example · left') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-stream-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="async-await-stream-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Example · right') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-stream-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The sideways pair leaves the caller lane and returns to it. Both orientations show the same concrete execution with mirrored geometry.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>anchor-start /
                                        attach-to</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default /
                                        null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Connects operation start, suspension, completion and continuation in logical order.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Each pair of 2rem sideways routes uses equal label widths and returns to the original lane.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Labels the suspension and resumption at AWAIT.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>step-label.text</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Each authored entry is one line; the step gap is automatic.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>before-length /
                                        after-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Provides space before and after each step label.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>inherited</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Colors distinguish waiting, operations, cancellation or stopping, cleanup and the final outcome as described in the preview.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-end /
                                        dev-counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Numbers the authored trace in execution order.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-stream"
                example="async-await-stream"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('JavaScript uses an async generator and for await; C# uses an async iterator and await foreach. Their early-exit paths dispose the iterator and await its asynchronous cleanup. Java shows an explicit asynchronous cursor with next and close operations. Normal exhaustion also closes the source. Cancellation is passed to pending reads; cleanup is still awaited.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Async iteration / Stream — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Follow OPEN → AWAIT next → process 10 → AWAIT next → process 20 → BREAK → AWAIT close. The source is pull-based and does not prefetch. On normal exhaustion the consumer also exits through cleanup; a read failure or cancellation must not skip resource release.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-stream-left-example"
                        size="sm"
                    >{{ __('Example · left') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-stream-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-stream-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="70rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.stream.left.start"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => [
                                        'OPEN async source',
                                        'Items: 10, 20, 30',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.stream.left.next-1"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-stream-left',
                                    'literature.async-await.stream.left.start.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => [
                                        'AWAIT next item',
                                        'Producer resumes on request',
                                    ],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.stream.left.item-1"
                                attach-to="literature.async-await.stream.left.next-1.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Receive 10',
                                        'Process item 10',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.stream.left.next-2"
                                attach-to="literature.async-await.stream.left.item-1.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'AWAIT next item',
                                        'Wait for producer',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="4"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.stream.left.item-2"
                                attach-to="literature.async-await.stream.left.next-2.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Receive 20',
                                        'Process item 20',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.stream.left.stop"
                                attach-to="literature.async-await.stream.left.item-2.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'BREAK after 2 items',
                                        'Do not request item 30',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.stream.left.close"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-stream-left',
                                    'literature.async-await.stream.left.stop.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => [
                                        'AWAIT source cleanup',
                                        'Close asynchronous iterator',
                                    ],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="7"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.stream.left.receive"
                                attach-to="literature.async-await.stream.left.close.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Continue with [10, 20]',
                                        'Source resources released',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.stream.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-stream-left',
                                    'literature.async-await.stream.left.receive.anchorNode-end',
                                )"
                                :dev-counter-end="9"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-stream-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-stream-right-example"
                        size="sm"
                    >{{ __('Example · right') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-stream-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-stream-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="70rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.stream.right.start"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => [
                                        'OPEN async source',
                                        'Items: 10, 20, 30',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.stream.right.next-1"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-stream-right',
                                    'literature.async-await.stream.right.start.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => [
                                        'AWAIT next item',
                                        'Producer resumes on request',
                                    ],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.stream.right.item-1"
                                attach-to="literature.async-await.stream.right.next-1.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Receive 10',
                                        'Process item 10',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.stream.right.next-2"
                                attach-to="literature.async-await.stream.right.item-1.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'AWAIT next item',
                                        'Wait for producer',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="4"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.stream.right.item-2"
                                attach-to="literature.async-await.stream.right.next-2.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Receive 20',
                                        'Process item 20',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.stream.right.stop"
                                attach-to="literature.async-await.stream.right.item-2.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'BREAK after 2 items',
                                        'Do not request item 30',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.stream.right.close"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-stream-right',
                                    'literature.async-await.stream.right.stop.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => [
                                        'AWAIT source cleanup',
                                        'Close asynchronous iterator',
                                    ],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="7"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.stream.right.receive"
                                attach-to="literature.async-await.stream.right.close.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Continue with [10, 20]',
                                        'Source resources released',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.stream.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-stream-right',
                                    'literature.async-await.stream.right.receive.anchorNode-end',
                                )"
                                :dev-counter-end="9"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-stream-right-example:end --}}
                    </div>


                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/flow-async-await-stream.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
