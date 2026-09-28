<x-translation-workbench::ui.common.heading-counter-group group="flow-for-multiple-actions">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('FOR multiple actions') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Load the items once and initialize index to zero. Each FOR iteration first processes items[index], then records the result for that same item. Only after both actions does index increase by one and the condition run again. An empty list skips both actions and the increment.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.for.flow-for-multiple-actions" />

            @php
                $forSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-multiple-actions',
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
                            example="for-multiple-actions-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $forSource->example('for-multiple-actions-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="for-multiple-actions-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $forSource->example('for-multiple-actions-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>left</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Places the loop body and return on the selected side.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Connects each action to the preceding component endpoint.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>step-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component
                                        default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Shows preparation, FOR initialization, condition, increment and continuation as separate steps.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>before-length<br>after-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component
                                        default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Controls the stems around a step. The condition explicitly uses after-length="12rem" to reserve room for the additional eight-rem action.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>label-gap</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null
                                        (resolved by step)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Reserves the vertical space occupied by each step label.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchor-start<br>anchor-return</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>required
                                        (paths.loop)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Uses the condition end as the split and the condition start as the return target, without repeating initialization.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>return</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true
                                        (paths.loop)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('False leaves the body open for the separate increment action and explicit return.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>entry-bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Provides space between the TRUE arc and the body action.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length<br>bridge-out-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Sets the bridges before and after the body action label.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>exit-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Sets the FALSE exit stem leading to the summary.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-label<br>entry-label<br>exit-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Defines the body action and the TRUE/FALSE information labels, including their colors.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>return-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null
                                        (resolved anchor required)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Returns from the increment to the condition start.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>node-end-dot<br>joint-arrow-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null<br>false
                                        (flow-step)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Displays a joint arrow at the increment endpoint where no information label is attached.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>counter-end<br>counter-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component
                                        default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Continues the DEV numbering across the independently authored components.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-multiple-actions"
                example="for-multiple-actions"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('The FOR header contains initialization, condition and increment. These syntax excerpts use application-provided actions; enclosing functions, classes and imports are omitted.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('FOR multiple actions — Preview') }}</flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Both previews show the same action sequence with left and right body placement. Follow TRUE through processing and recording the result before advancing the index. The return starts after the increment and rejoins the condition. FALSE bypasses the entire action sequence. The explicitly longer condition stem reserves space for the additional action.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="for-multiple-actions-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- for-multiple-actions-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-for-multiple-actions-left"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.multiple.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'count = item count'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- FOR initialization runs once; the return skips this step. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.multiple.left.initialize-index"
                                attach-to="literature.for.multiple.left.initialize.anchorNode-end"
                                :step-label="['text' => ['index = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Check before every iteration, including the first. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.multiple.left.condition"
                                attach-to="literature.for.multiple.left.initialize-index.anchorNode-end"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="12rem"
                                :step-label="[
                                    'text' => ['FOR index < count?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="1"
                                color="cyan"
                            />
                            {{-- TRUE enters the body; FALSE leaves the FOR. --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.for.multiple.left.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-multiple-actions-left',
                                    'literature.for.multiple.left.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-multiple-actions-left',
                                    'literature.for.multiple.left.initialize-index.anchorNode-end',
                                )"
                                side="left"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="2rem"
                                exit-length="5.5rem"
                                :bridge-label="[
                                    'text' => ['Process items[index]'],
                                    'width' => 'default',
                                    'align' => 'center',
                                    'color' => 'green',
                                ]"
                                :entry-label="['text' => ['TRUE'], 'width' => 'half', 'side' => 'top', 'color' => 'green']"
                                :exit-label="[
                                    'text' => ['FALSE'],
                                    'width' => 'half',
                                    'side' => 'right',
                                    'color' => 'red',
                                ]"
                                color="cyan"
                            />
                            {{-- Record the result for the same item before the index changes. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.multiple.left.record"
                                attach-to="literature.for.multiple.left.loop.body.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['Record result for items[index]'], 'width' => 'default']"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                :counter-end="7"
                                color="green"
                            />
                            {{-- Increment only after both body actions have completed. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.multiple.left.advance"
                                attach-to="literature.for.multiple.left.record.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['index = index + 1'], 'width' => 'default']"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                :counter-end="8"
                                color="cyan"
                            />
                            {{-- Return after both actions and the increment, to the condition (not initialization). --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.for.multiple.left.body-return"
                                attach-to="literature.for.multiple.left.advance.anchorNode-end"
                                return-to="literature.for.multiple.left.loop.anchorNode-return"
                                side="left"
                                :counter-start="9"
                                color="cyan"
                            />
                            {{-- FALSE continues here, including when the item list is empty. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.multiple.left.continue"
                                attach-to="literature.for.multiple.left.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="['text' => ['Show summary'], 'width' => 'default']"
                                :counter-end="13"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- for-multiple-actions-left-example:end --}}
                    </div>

                    <x-translation-workbench::ui.common.heading-counter
                        example="for-multiple-actions-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- for-multiple-actions-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-for-multiple-actions-right"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.multiple.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'count = item count'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- FOR initialization runs once; the return skips this step. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.multiple.right.initialize-index"
                                attach-to="literature.for.multiple.right.initialize.anchorNode-end"
                                :step-label="['text' => ['index = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Check before every iteration, including the first. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.multiple.right.condition"
                                attach-to="literature.for.multiple.right.initialize-index.anchorNode-end"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="12rem"
                                :step-label="[
                                    'text' => ['FOR index < count?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :counter-end="1"
                                color="cyan"
                            />
                            {{-- TRUE enters the body; FALSE leaves the FOR. --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop
                                id="literature.for.multiple.right.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-multiple-actions-right',
                                    'literature.for.multiple.right.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-multiple-actions-right',
                                    'literature.for.multiple.right.initialize-index.anchorNode-end',
                                )"
                                side="right"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="2rem"
                                exit-length="5.5rem"
                                :bridge-label="[
                                    'text' => ['Process items[index]'],
                                    'width' => 'default',
                                    'align' => 'center',
                                    'color' => 'green',
                                ]"
                                :entry-label="['text' => ['TRUE'], 'width' => 'half', 'side' => 'top', 'color' => 'green']"
                                :exit-label="[
                                    'text' => ['FALSE'],
                                    'width' => 'half',
                                    'side' => 'left',
                                    'color' => 'red',
                                ]"
                                color="cyan"
                            />
                            {{-- Record the result for the same item before the index changes. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.multiple.right.record"
                                attach-to="literature.for.multiple.right.loop.body.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['Record result for items[index]'], 'width' => 'default']"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                :counter-end="7"
                                color="green"
                            />
                            {{-- Increment only after both body actions have completed. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.multiple.right.advance"
                                attach-to="literature.for.multiple.right.record.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['index = index + 1'], 'width' => 'default']"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                :counter-end="8"
                                color="cyan"
                            />
                            {{-- Return after both actions and the increment, to the condition (not initialization). --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.for.multiple.right.body-return"
                                attach-to="literature.for.multiple.right.advance.anchorNode-end"
                                return-to="literature.for.multiple.right.loop.anchorNode-return"
                                side="right"
                                :counter-start="9"
                                color="cyan"
                            />
                            {{-- FALSE continues here, including when the item list is empty. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.multiple.right.continue"
                                attach-to="literature.for.multiple.right.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="['text' => ['Show summary'], 'width' => 'default']"
                                :counter-end="13"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- for-multiple-actions-right-example:end --}}
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            {{-- Path/To/File --}}
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/for/flow-for-multiple-actions.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
