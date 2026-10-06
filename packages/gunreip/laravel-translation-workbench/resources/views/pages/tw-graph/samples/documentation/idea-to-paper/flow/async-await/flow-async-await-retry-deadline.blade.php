<x-translation-workbench::ui.common.heading-counter-group group="flow-async-await-retry-deadline">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Retry with total deadline') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Use one 50ms deadline for the whole retry operation, with at most three attempts and a 20ms delay. Attempt 1 fails after 10ms. Attempt 2 starts at about 30ms but needs 100ms, so the original deadline cancels it at about 50ms. The deadline is never restarted for a new attempt or delay.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.async-await.flow-async-await-retry-deadline" />

            @php
                $asyncSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-retry-deadline',
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
                            example="async-await-retry-deadline-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Example · left') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-retry-deadline-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="async-await-retry-deadline-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Example · right') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-retry-deadline-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-retry-deadline"
                example="async-await-retry-deadline"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('The snippets combine a single deadline timer with caller cancellation. The same cancellation signal reaches every attempt and every delay. Only transient failures are retried. Success clears the deadline; timeout or external cancellation prevents another attempt. The budget starts at entry and covers all attempts and delays. Cancellation is cooperative, so cleanup may finish after the deadline.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Retry with total deadline — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Read this as a timeout trace: 10ms failed attempt + 20ms delay + 20ms of attempt 2 exhaust the 50ms budget. The operation acknowledges cancellation, timer and listeners are cleaned up, then the caller handles the timeout. No third attempt starts. Labels describe nominal durations, not guaranteed scheduler timing.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-retry-deadline-left-example"
                        size="sm"
                    >{{ __('Example · left') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-retry-deadline-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-retry-deadline-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="70rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.retry-deadline.left.start"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => [
                                        'Start ONE deadline: 50ms',
                                        'Max attempts: 3',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.retry-deadline.left.attempt-1"
                                attach-to="literature.async-await.retry-deadline.left.start.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'AWAIT attempt 1: 10ms',
                                        'Transient failure',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="2"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.retry-deadline.left.delay"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-retry-deadline-left',
                                    'literature.async-await.retry-deadline.left.attempt-1.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => [
                                        'AWAIT retry delay: 20ms',
                                        'Same deadline keeps running',
                                    ],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="3"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.retry-deadline.left.attempt-2"
                                attach-to="literature.async-await.retry-deadline.left.delay.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Start attempt 2 at ~30ms',
                                        'Needs 100ms',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="4"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.retry-deadline.left.deadline"
                                attach-to="literature.async-await.retry-deadline.left.attempt-2.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Deadline expires at ~50ms',
                                        'Cancel pending attempt',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.retry-deadline.left.acknowledge"
                                attach-to="literature.async-await.retry-deadline.left.deadline.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Operation stops',
                                        'No third attempt',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.retry-deadline.left.cleanup"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-retry-deadline-left',
                                    'literature.async-await.retry-deadline.left.acknowledge.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => [
                                        'FINALLY: clear deadline',
                                        'Remove cancellation listener',
                                    ],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="7"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.retry-deadline.left.receive"
                                attach-to="literature.async-await.retry-deadline.left.cleanup.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'THROW timeout error',
                                        'Caller handles timeout',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.retry-deadline.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-retry-deadline-left',
                                    'literature.async-await.retry-deadline.left.receive.anchorNode-end',
                                )"
                                :dev-counter-end="9"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-retry-deadline-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-retry-deadline-right-example"
                        size="sm"
                    >{{ __('Example · right') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-retry-deadline-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-retry-deadline-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="70rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.retry-deadline.right.start"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => [
                                        'Start ONE deadline: 50ms',
                                        'Max attempts: 3',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.retry-deadline.right.attempt-1"
                                attach-to="literature.async-await.retry-deadline.right.start.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'AWAIT attempt 1: 10ms',
                                        'Transient failure',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="2"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.retry-deadline.right.delay"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-retry-deadline-right',
                                    'literature.async-await.retry-deadline.right.attempt-1.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => [
                                        'AWAIT retry delay: 20ms',
                                        'Same deadline keeps running',
                                    ],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="3"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.retry-deadline.right.attempt-2"
                                attach-to="literature.async-await.retry-deadline.right.delay.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Start attempt 2 at ~30ms',
                                        'Needs 100ms',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="4"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.retry-deadline.right.deadline"
                                attach-to="literature.async-await.retry-deadline.right.attempt-2.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Deadline expires at ~50ms',
                                        'Cancel pending attempt',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.retry-deadline.right.acknowledge"
                                attach-to="literature.async-await.retry-deadline.right.deadline.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'Operation stops',
                                        'No third attempt',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.retry-deadline.right.cleanup"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-retry-deadline-right',
                                    'literature.async-await.retry-deadline.right.acknowledge.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => [
                                        'FINALLY: clear deadline',
                                        'Remove cancellation listener',
                                    ],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="7"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.retry-deadline.right.receive"
                                attach-to="literature.async-await.retry-deadline.right.cleanup.anchorNode-end"
                                :step-label="[
                                    'text' => [
                                        'THROW timeout error',
                                        'Caller handles timeout',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="8"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.retry-deadline.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-retry-deadline-right',
                                    'literature.async-await.retry-deadline.right.receive.anchorNode-end',
                                )"
                                :dev-counter-end="9"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-retry-deadline-right-example:end --}}
                    </div>


                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/flow-async-await-retry-deadline.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
