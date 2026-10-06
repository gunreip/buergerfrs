<x-translation-workbench::ui.common.heading-counter-group group="flow-async-await-group-cleanup">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Failure → Cancel remaining → Cleanup') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Start A and B as one task group. A fails after releasing its own resources. Request cancellation of B, wait for B to acknowledge cancellation and finish asynchronous cleanup, then propagate the original A error. Cancellation is cooperative: requesting it does not mean that B has already stopped.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.async-await.flow-async-await-group-cleanup" />

            @php
                $asyncSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-group-cleanup',
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
                            example="async-await-group-cleanup-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Example · left') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-group-cleanup-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="async-await-group-cleanup-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Example · right') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-group-cleanup-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-group-cleanup"
                example="async-await-group-cleanup"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('The snippets attach error observation to every task. The first failure requests cancellation of the remaining work, but the group still waits for all tasks and their FINALLY blocks. The original failure is preserved; cancellation of a sibling does not replace it. An operation that ignores cancellation can delay group completion.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Failure → Cancel remaining → Cleanup — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('This concrete failure trace follows A failing while B is pending. Red is the original error, amber the cancellation request, sky the wait and violet cleanup. The caller receives the error only after both operations have settled. This is an explicit group policy, not an automatic property of an ALL combinator.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-group-cleanup-left-example"
                        size="sm"
                    >{{ __('Example · left') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-group-cleanup-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-group-cleanup-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="70rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.group-cleanup.left.start"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => [
                                        'Start A and B',
                                        'One task group',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.group-cleanup.left.await"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-group-cleanup-left',
                                    'literature.async-await.group-cleanup.left.start.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => [
                                        'AWAIT group',
                                        'Both tasks observed',
                                    ],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.group-cleanup.left.failure"
                                attach-to="literature.async-await.group-cleanup.left.await.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'A fails',
                                        'A cleanup already finished',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.group-cleanup.left.cancel"
                                attach-to="literature.async-await.group-cleanup.left.failure.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Request cancellation of B',
                                        'Preserve original A error',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="4"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.group-cleanup.left.acknowledge"
                                attach-to="literature.async-await.group-cleanup.left.cancel.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'B acknowledges cancellation',
                                        'Stops pending operation',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.group-cleanup.left.cleanup"
                                attach-to="literature.async-await.group-cleanup.left.acknowledge.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'AWAIT B cleanup',
                                        'Release B resources',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="6"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.group-cleanup.left.join"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-group-cleanup-left',
                                    'literature.async-await.group-cleanup.left.cleanup.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => [
                                        'AWAIT all tasks settled',
                                        'Cleanup must finish first',
                                    ],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="7"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.group-cleanup.left.receive"
                                attach-to="literature.async-await.group-cleanup.left.join.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'THROW original A error',
                                        'Caller CATCH handles it',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.group-cleanup.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-group-cleanup-left',
                                    'literature.async-await.group-cleanup.left.receive.anchorNode-end',
                                )"
                                :dev-counter-end="9"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-group-cleanup-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-group-cleanup-right-example"
                        size="sm"
                    >{{ __('Example · right') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-group-cleanup-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-group-cleanup-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="70rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.group-cleanup.right.start"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => [
                                        'Start A and B',
                                        'One task group',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.group-cleanup.right.await"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-group-cleanup-right',
                                    'literature.async-await.group-cleanup.right.start.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => [
                                        'AWAIT group',
                                        'Both tasks observed',
                                    ],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="2"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.group-cleanup.right.failure"
                                attach-to="literature.async-await.group-cleanup.right.await.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'A fails',
                                        'A cleanup already finished',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="3"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.group-cleanup.right.cancel"
                                attach-to="literature.async-await.group-cleanup.right.failure.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Request cancellation of B',
                                        'Preserve original A error',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="4"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.group-cleanup.right.acknowledge"
                                attach-to="literature.async-await.group-cleanup.right.cancel.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'B acknowledges cancellation',
                                        'Stops pending operation',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.group-cleanup.right.cleanup"
                                attach-to="literature.async-await.group-cleanup.right.acknowledge.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'AWAIT B cleanup',
                                        'Release B resources',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="6"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.group-cleanup.right.join"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-group-cleanup-right',
                                    'literature.async-await.group-cleanup.right.cleanup.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => [
                                        'AWAIT all tasks settled',
                                        'Cleanup must finish first',
                                    ],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="7"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.group-cleanup.right.receive"
                                attach-to="literature.async-await.group-cleanup.right.join.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'THROW original A error',
                                        'Caller CATCH handles it',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.group-cleanup.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-group-cleanup-right',
                                    'literature.async-await.group-cleanup.right.receive.anchorNode-end',
                                )"
                                :dev-counter-end="9"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-group-cleanup-right-example:end --}}
                    </div>


                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/flow-async-await-group-cleanup.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
