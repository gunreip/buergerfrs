<x-translation-workbench::ui.common.heading-counter-group group="flow-continue-for">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('FOR with CONTINUE') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('FOR initializes the index once. TRUE at the IF executes CONTINUE and skips Process item; FALSE processes the item. Both routes reach the FOR update before checking the condition again. The update is outside the body, so CONTINUE must not bypass it. An empty collection skips both the body and update.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.break-continue.flow-continue-for" />

            @php
                $continueSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-for',
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
                            example="continue-for-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $continueSource->example('continue-for-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="continue-for-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $continueSource->example('continue-for-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>attach-to<br>anchor-start</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null / component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Connects initialization, condition, body, IF, update and return as independent components.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>condition.after-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>4rem (flow-step)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Explicitly 35rem reserves room for the IF, update and return.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>loop.return</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('False leaves the loop body open for the IF and shared update.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>entry-bridge-length<br>bridge-length<br>bridge-out-length<br>exit-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>2rem / 4rem / 4rem / 4rem</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Explicitly 4rem, 2rem, 2rem and 5.5rem for the loop route.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>bridge-label<br>entry-label<br>exit-label</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Labels Read current item and the TRUE/FALSE loop exits.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>if-start<br>if-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('TRUE is CONTINUE; FALSE processes the item. Both outcomes reach the update.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>direction<br>side</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>bottom-top / left</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('The IF and update follow the body downward with top-bottom.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>bridge-length<br>stem-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>inherited / 8rem (IF)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Explicitly 2rem and 4rem for the IF lanes.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>advance.attach-to</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Attaches the FOR update to the common IF endpoint so CONTINUE cannot bypass it.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>advance.step-label</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Shows the update from the FOR header as a separate action.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>return-to</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Returns from advance.anchorNode-end to loop.anchorNode-return after initialization and before the condition.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>node-end-dot<br>joint-arrow-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true / false (flow-step)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('The update ends with a joint arrow at the technical connection to the return.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>loop.anchorNode-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>connection</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('The FALSE exit reaches the summary without executing the body or update.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>counter-start<br>counter-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Keeps DEV numbering continuous from the condition through the summary.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.break-continue.flow-continue-for"
                example="continue-for"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('The FOR header owns initialization, condition and update. CONTINUE skips the remaining body but still executes the update. Unlike the preceding WHILE example, no manual increment is placed before the IF. Application helpers, enclosing functions and imports are omitted.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('FOR with CONTINUE — Preview') }}</flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Follow CONTINUE past Process item to the shared index update, then back to the FOR condition. The normal processing route reaches the same update. Only the FOR FALSE exit reaches the summary; initialization runs once.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="continue-for-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- continue-for-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-continue-for-left"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.for.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'count = item count'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- FOR initialization runs once; the return skips this step. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.for.left.initialize-index"
                                attach-to="literature.continue.for.left.initialize.anchorNode-end"
                                :step-label="['text' => ['index = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Check before every iteration, including the first. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.for.left.condition"
                                attach-to="literature.continue.for.left.initialize-index.anchorNode-end"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="35rem"
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
                                id="literature.continue.for.left.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-continue-for-left',
                                    'literature.continue.for.left.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-continue-for-left',
                                    'literature.continue.for.left.initialize-index.anchorNode-end',
                                )"
                                side="left"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="2rem"
                                exit-length="5.5rem"
                                :bridge-label="[
                                    'text' => ['Read current item'],
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
                            {{-- TRUE skips Process item; both lanes must execute the FOR update before returning. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.continue.for.left.decision"
                                attach-to="literature.continue.for.left.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="left"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                label-gap="4rem"
                                after-length="1rem"
                                :condition-label="[
                                    'text' => ['IF shouldSkip(item)?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE: CONTINUE'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: Process item'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :counter-start="7"
                                :left-counter-end="8"
                                :false-stem-counter="9"
                                :right-counter-end="10"
                                color="sky"
                            />
                            {{-- FOR update runs after normal body completion AND after CONTINUE. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.for.left.advance"
                                attach-to="literature.continue.for.left.decision.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['FOR update', 'index = index + 1'], 'width' => 'default']"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                :counter-end="11"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.continue.for.left.body-return"
                                attach-to="literature.continue.for.left.advance.anchorNode-end"
                                return-to="literature.continue.for.left.loop.anchorNode-return"
                                side="left"
                                :counter-start="12"
                                color="green"
                            />
                            {{-- Only FOR exhaustion reaches the following action; CONTINUE returns above. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.for.left.continue"
                                attach-to="literature.continue.for.left.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Show summary after FOR'],
                                    'width' => 'default',
                                ]"
                                :counter-end="16"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- continue-for-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="continue-for-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- continue-for-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-continue-for-right"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.for.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'count = item count'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- FOR initialization runs once; the return skips this step. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.for.right.initialize-index"
                                attach-to="literature.continue.for.right.initialize.anchorNode-end"
                                :step-label="['text' => ['index = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Check before every iteration, including the first. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.for.right.condition"
                                attach-to="literature.continue.for.right.initialize-index.anchorNode-end"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="35rem"
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
                                id="literature.continue.for.right.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-continue-for-right',
                                    'literature.continue.for.right.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-continue-for-right',
                                    'literature.continue.for.right.initialize-index.anchorNode-end',
                                )"
                                side="right"
                                :return="false"
                                entry-bridge-length="4rem"
                                bridge-length="2rem"
                                bridge-out-length="2rem"
                                exit-length="5.5rem"
                                :bridge-label="[
                                    'text' => ['Read current item'],
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
                            {{-- TRUE skips Process item; both lanes must execute the FOR update before returning. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.continue.for.right.decision"
                                attach-to="literature.continue.for.right.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="right"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                label-gap="4rem"
                                after-length="1rem"
                                :condition-label="[
                                    'text' => ['IF shouldSkip(item)?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE: CONTINUE'],
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: Process item'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :counter-start="7"
                                :left-counter-end="8"
                                :false-stem-counter="9"
                                :right-counter-end="10"
                                color="sky"
                            />
                            {{-- FOR update runs after normal body completion AND after CONTINUE. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.for.right.advance"
                                attach-to="literature.continue.for.right.decision.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['FOR update', 'index = index + 1'], 'width' => 'default']"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                :counter-end="11"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.continue.for.right.body-return"
                                attach-to="literature.continue.for.right.advance.anchorNode-end"
                                return-to="literature.continue.for.right.loop.anchorNode-return"
                                side="right"
                                :counter-start="12"
                                color="green"
                            />
                            {{-- Only FOR exhaustion reaches the following action; CONTINUE returns above. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.continue.for.right.continue"
                                attach-to="literature.continue.for.right.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Show summary after FOR'],
                                    'width' => 'default',
                                ]"
                                :counter-end="16"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- continue-for-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/break-continue/flow-continue-for.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
