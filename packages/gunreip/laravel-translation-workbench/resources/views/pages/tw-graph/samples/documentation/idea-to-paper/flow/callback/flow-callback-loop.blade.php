<x-translation-workbench::ui.common.heading-counter-group group="flow-callback-loop">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Callback inside a loop') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The caller supplies items [10, 20, 30] and the addOne callback. The loop selects one item, invokes callback(item), appends the returned value and then requests the next item. Exhaustion continues to the final result [11, 21, 31]; an empty collection skips the callback entirely.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.callback.flow-callback-loop" />

            @php
                $callbackSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-loop',
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
                            example="callback-loop-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $callbackSource->example('callback-loop-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="callback-loop-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $callbackSource->example('callback-loop-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                    {{ __('The callback occupies a further outer lane and returns to the current iteration. The second layout mirrors the loop and both callback routes.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>anchor-start /
                                        attach-to</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default /
                                        null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Connects preparation, call, function body, return and caller continuation in execution order.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The two callback side routes use 2rem bridges and equal label widths to return to the loop body lane.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Distinguishes passing a callable, invoking it and returning its result.') }}
                                </flux:table.cell>
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
                                    {{ __('Cyan marks iteration, sky the callback call, indigo the callback body, violet result collection and green the returns.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-end /
                                        dev-counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Sets explicit diagnostic counters for the authored components.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bottom-top</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The callback excursion and append step explicitly run top-bottom inside the loop.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>after-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The condition uses 44rem to make room for the callback and append sequence.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>return / return-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true / null</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The loop stays open until paths.loop-return reconnects append to the iteration entry.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>entry-bridge-length /
                                        bridge-out-length / exit-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem / 4rem / 4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Sets the visible loop entry, body exit bridge and exhaustion stem lengths.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.callback.flow-callback-loop"
                example="callback-loop"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('All six snippets pass addOne to mapEach and invoke it synchronously once per item. The graph expands the loop inside mapEach: callback RETURN resumes the current iteration, not the caller of mapEach. After exhaustion, mapEach returns the collected results. C uses an indexed traversal and an output array.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Callback inside a loop — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Follow TRUE through item selection into the callback. Its RETURN rejoins the iteration before the result is appended. The loop return requests another item without reopening iteration. FALSE means exhaustion and leads to the collected result. One drawn callback body represents repeated execution.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="callback-loop-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- callback-loop-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-callback-loop-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="68rem"
                            min-height="68rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.loop.left.prepare"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => [
                                        'Inside mapEach',
                                        'items = [10, 20, 30]',
                                        'callback = addOne',
                                        'Open iteration; results = []',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="5rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.loop.left.condition"
                                attach-to="literature.callback.loop.left.prepare.anchorNode-end"
                                :step-label="[
                                    'text' => ['FOREACH next item?'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="25rem"
                                :counter-end="2"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.callback.loop.left.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-loop-left',
                                    'literature.callback.loop.left.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-loop-left',
                                    'literature.callback.loop.left.prepare.anchorNode-end',
                                )"
                                side="left"
                                :counter-start="3"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="2rem"
                                exit-length="4rem"
                                :bridge-label="[
                                    'text' => ['Select current item'],
                                    'width' => 'default',
                                    'align' => 'center',
                                    'color' => 'green',
                                ]"
                                :entry-label="[
                                    'text' => ['TRUE'],
                                    'width' => 'half',
                                    'side' => 'right',
                                    'color' => 'green',
                                ]"
                                :entry-label-anchor="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-loop-left',
                                    'literature.callback.loop.left.condition.anchorNode-end',
                                )"
                                :exit-label="[
                                    'text' => ['FALSE'],
                                    'width' => 'half',
                                    'side' => 'right',
                                    'color' => 'red',
                                ]"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.loop.left.invoke"
                                side="right"
                                direction="top-bottom"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-loop-left',
                                    'literature.callback.loop.left.loop.body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL callback(item)', 'Invokes addOne(item)'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.loop.left.callback-body"
                                attach-to="literature.callback.loop.left.invoke.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside addOne', 'RETURN item + 1'],
                                    'width' => 'default',
                                ]"
                                direction="top-bottom"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.loop.left.callback-return"
                                side="left"
                                direction="top-bottom"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-loop-left',
                                    'literature.callback.loop.left.callback-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN transformed item', 'Resume current iteration'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="10"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.loop.left.append"
                                attach-to="literature.callback.loop.left.callback-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Append returned value', 'results: 11, then 21, then 31'],
                                    'width' => 'default',
                                ]"
                                direction="top-bottom"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="11"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.callback.loop.left.repeat"
                                attach-to="literature.callback.loop.left.append.anchorNode-end"
                                return-to="literature.callback.loop.left.prepare.anchorNode-end"
                                side="left"
                                :counter-start="12"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.loop.left.receive"
                                attach-to="literature.callback.loop.left.loop.anchorNode-end"
                                :step-label="[
                                    'text' => ['Iteration exhausted', 'RETURN results', '[11, 21, 31]'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="16"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.callback.loop.left.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-loop-left',
                                    'literature.callback.loop.left.receive.anchorNode-end',
                                )"
                                :dev-counter-end="17"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- callback-loop-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="callback-loop-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- callback-loop-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-callback-loop-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="68rem"
                            min-height="68rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.loop.right.prepare"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                :step-label="[
                                    'text' => [
                                        'Inside mapEach',
                                        'items = [10, 20, 30]',
                                        'callback = addOne',
                                        'Open iteration; results = []',
                                    ],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="5rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.loop.right.condition"
                                attach-to="literature.callback.loop.right.prepare.anchorNode-end"
                                :step-label="[
                                    'text' => ['FOREACH next item?'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="25rem"
                                :counter-end="2"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.callback.loop.right.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-loop-right',
                                    'literature.callback.loop.right.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-loop-right',
                                    'literature.callback.loop.right.prepare.anchorNode-end',
                                )"
                                side="right"
                                :counter-start="3"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="2rem"
                                exit-length="4rem"
                                :bridge-label="[
                                    'text' => ['Select current item'],
                                    'width' => 'default',
                                    'align' => 'center',
                                    'color' => 'green',
                                ]"
                                :entry-label="[
                                    'text' => ['TRUE'],
                                    'width' => 'half',
                                    'side' => 'right',
                                    'color' => 'green',
                                ]"
                                :entry-label-anchor="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-loop-right',
                                    'literature.callback.loop.right.condition.anchorNode-end',
                                )"
                                :exit-label="[
                                    'text' => ['FALSE'],
                                    'width' => 'half',
                                    'side' => 'right',
                                    'color' => 'red',
                                ]"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.loop.right.invoke"
                                side="left"
                                direction="top-bottom"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-loop-right',
                                    'literature.callback.loop.right.loop.body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['CALL callback(item)', 'Invokes addOne(item)'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="8"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.loop.right.callback-body"
                                attach-to="literature.callback.loop.right.invoke.anchorNode-end"
                                :step-label="[
                                    'text' => ['Inside addOne', 'RETURN item + 1'],
                                    'width' => 'default',
                                ]"
                                direction="top-bottom"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="9"
                                color="indigo"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.callback.loop.right.callback-return"
                                side="right"
                                direction="top-bottom"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-loop-right',
                                    'literature.callback.loop.right.callback-body.anchorNode-end',
                                )"
                                bridge-length="2rem"
                                :bridge-label="[
                                    'text' => ['RETURN transformed item', 'Resume current iteration'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :dev-counter-end="10"
                                color="emerald"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.loop.right.append"
                                attach-to="literature.callback.loop.right.callback-return.anchorNode-end"
                                :step-label="[
                                    'text' => ['Append returned value', 'results: 11, then 21, then 31'],
                                    'width' => 'default',
                                ]"
                                direction="top-bottom"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="11"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.callback.loop.right.repeat"
                                attach-to="literature.callback.loop.right.append.anchorNode-end"
                                return-to="literature.callback.loop.right.prepare.anchorNode-end"
                                side="right"
                                :counter-start="12"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.callback.loop.right.receive"
                                attach-to="literature.callback.loop.right.loop.anchorNode-end"
                                :step-label="[
                                    'text' => ['Iteration exhausted', 'RETURN results', '[11, 21, 31]'],
                                    'width' => 'default',
                                ]"
                                before-length="2rem"
                                after-length="2rem"
                                :counter-end="16"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.callback.loop.right.end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-callback-loop-right',
                                    'literature.callback.loop.right.receive.anchorNode-end',
                                )"
                                :dev-counter-end="17"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- callback-loop-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/callback/flow-callback-loop.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
