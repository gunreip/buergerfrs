<x-translation-workbench::ui.common.heading-counter-group group="flow-async-await-limited">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Limited concurrency') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Run A, B and C with two worker slots. Start A and B immediately; C stays queued. When B finishes first, its worker starts C while A is still pending. Collect all outcomes in input order. This concrete success trace demonstrates a limit of two active operations, not batches that wait for their slowest member.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.async-await.flow-async-await-limited" />

            @php
                $asyncSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-limited',
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
                            example="async-await-limited-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Limit 2 · left') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-limited-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="async-await-limited-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Limit 2 · right') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $asyncSource->example('async-await-limited-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                    {{ __('Routes the scheduler trace away from the start and back to the result. The active operations are listed explicitly in the labels; the drawing is not a complete worker-pool implementation.') }}
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
                                    {{ __('Sky marks waiting, violet completed operations, amber queue scheduling and green the final collection.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-end /
                                        dev-counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Numbers the authored scheduler trace in execution order.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.async-await.flow-async-await-limited"
                example="async-await-limited"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('JavaScript uses a fixed worker pool. Each worker takes the next item only after its current operation settles. Rejections become outcome records so the queue continues. C# follows the same worker pattern. Java uses two asynchronous worker chains. PHP, C and C++ require a selected async runtime. No operation is started merely by placing its item in the queue.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Limited concurrency — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Follow the scheduler trace: active A+B, then A+C, then only A, then none. The configured delays make B and C finish before A in this example; other completion orders are valid. Results retain the input order A, B, C. An empty queue starts no operations. The limit is about in-flight work, not the number of operating-system threads.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-limited-left-example"
                        size="sm"
                    >{{ __('Limit 2 · left') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-limited-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-limited-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="78rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.left.start"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['Queue: A, B, C', 'Limit: 2 active operations'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.left.launch"
                                attach-to="literature.async-await.limited.left.start.anchorNode-end"
                                :step-label="[
                                    'text' => ['Start A and B', 'Active: 2 / queued: C'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="2"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.limited.left.await"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-limited-left',
                                    'literature.async-await.limited.left.launch.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['AWAIT a worker slot', 'C has not started'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="3"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.left.b-complete"
                                attach-to="literature.async-await.limited.left.await.anchorNode-end"
                                :step-label="[
                                    'text' => ['B completes: 20', 'A remains pending'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="4"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.left.refill"
                                attach-to="literature.async-await.limited.left.b-complete.anchorNode-end"
                                :step-label="[
                                    'text' => ['Freed worker starts C', 'Active: A + C / queued: none'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.left.c-complete"
                                attach-to="literature.async-await.limited.left.refill.anchorNode-end"
                                :step-label="[
                                    'text' => ['C completes: 30', 'Active: A only'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="6"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.left.a-complete"
                                attach-to="literature.async-await.limited.left.c-complete.anchorNode-end"
                                :step-label="[
                                    'text' => ['A completes: 10', 'Active: 0 / queued: none'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.limited.left.resume"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-limited-left',
                                    'literature.async-await.limited.left.a-complete.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['ALL workers finished', 'Collect outcomes'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.left.receive"
                                attach-to="literature.async-await.limited.left.resume.anchorNode-end"
                                :step-label="[
                                    'text' => ['Input order: A, B, C', 'Values: 10, 20, 30'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.limited.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-limited-left',
                                    'literature.async-await.limited.left.receive.anchorNode-end',
                                )"
                                :dev-counter-end="10"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-limited-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="async-await-limited-right-example"
                        size="sm"
                    >{{ __('Limit 2 · right') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- async-await-limited-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-async-await-limited-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="44rem"
                            min-height="78rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.right.start"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => ['Queue: A, B, C', 'Limit: 2 active operations'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.right.launch"
                                attach-to="literature.async-await.limited.right.start.anchorNode-end"
                                :step-label="[
                                    'text' => ['Start A and B', 'Active: 2 / queued: C'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="2"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.limited.right.await"
                                side="left"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-limited-right',
                                    'literature.async-await.limited.right.launch.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['AWAIT a worker slot', 'C has not started'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="3"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.right.b-complete"
                                attach-to="literature.async-await.limited.right.await.anchorNode-end"
                                :step-label="[
                                    'text' => ['B completes: 20', 'A remains pending'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="4"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.right.refill"
                                attach-to="literature.async-await.limited.right.b-complete.anchorNode-end"
                                :step-label="[
                                    'text' => ['Freed worker starts C', 'Active: A + C / queued: none'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="5"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.right.c-complete"
                                attach-to="literature.async-await.limited.right.refill.anchorNode-end"
                                :step-label="[
                                    'text' => ['C completes: 30', 'Active: A only'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="6"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.right.a-complete"
                                attach-to="literature.async-await.limited.right.c-complete.anchorNode-end"
                                :step-label="[
                                    'text' => ['A completes: 10', 'Active: 0 / queued: none'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="7"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.async-await.limited.right.resume"
                                side="right"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-limited-right',
                                    'literature.async-await.limited.right.a-complete.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['ALL workers finished', 'Collect outcomes'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.async-await.limited.right.receive"
                                attach-to="literature.async-await.limited.right.resume.anchorNode-end"
                                :step-label="[
                                    'text' => ['Input order: A, B, C', 'Values: 10, 20, 30'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.async-await.limited.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-async-await-limited-right',
                                    'literature.async-await.limited.right.receive.anchorNode-end',
                                )"
                                :dev-counter-end="10"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- async-await-limited-right-example:end --}}
                    </div>


                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/async-await/flow-async-await-limited.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
