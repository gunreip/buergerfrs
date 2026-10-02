<x-translation-workbench::ui.common.heading-counter-group group="flow-return-loop">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('RETURN from WHILE') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('findFirstPositive(values) scans the input in order. A positive item immediately returns its index from the entire function. Only non-positive items advance the index and repeat WHILE. If no match exists, including an empty input, normal exhaustion returns -1.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.return.flow-return-loop" />

            @php
                $returnSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-loop',
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
                            example="return-loop-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $returnSource->example('return-loop-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="return-loop-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $returnSource->example('return-loop-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>action-label.return</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Explicitly false leaves the WHILE body open for the independent decision.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>condition-label.afterLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Explicitly 13rem leaves room for the decision and the loop return.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>if-start.return<br>if-end.return</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Both branches are open. TRUE returns the index and terminates; FALSE advances index and returns to the WHILE condition.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start.returnOffset</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>0rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Explicitly 8rem separates the successful RETURN endpoint from the normal loop-return stem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>return-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The loop return attaches only to decision.false.anchorNode-end and targets loop.anchorNode-return.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>fallback.attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The fallback RETURN -1 attaches only to loop.anchorNode-end, reached by exhaustion.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>parts.end.direction<br>length</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bottom-top / 2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The successful RETURN closes downward; the exhausted route closes upward. The successful terminal explicitly uses 10.3rem; the fallback terminal retains its 2rem default.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>counter-start<br>counter-end<br>dev-counter-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The composed components use continuous counters from 1 to 18.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-loop"
                example="return-loop"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('All snippets return the first positive index, or -1 if none exists. RETURN exits the function directly from inside WHILE. Class wrappers and imports are omitted where needed; C receives the array length explicitly.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('RETURN from WHILE — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('TRUE ends at RETURN index and its own terminal cap. FALSE increments index and returns to the WHILE condition. RETURN -1 is reached only through the exhausted WHILE exit; the successful RETURN never joins that route. Unlike BREAK, RETURN also skips the remaining function code.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="return-loop-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- return-loop-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-return-loop-left"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.loop.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="[
                                    'text' => ['FUNCTION findFirstPositive(values)', 'index = 0'],
                                    'width' => 'default',
                                ]"
                                after-length="5rem"
                                color="zinc"
                                :counter-end="1"
                            />
                            {{-- WHILE owns the condition; the open body continues into the independent IF. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.return.loop.left.loop"
                                attach-to="literature.return.loop.left.initialize.anchorNode-end"
                                side="left"
                                :counter-start="2"
                                true-bridge-length="4rem"
                                stem-length="5.5rem"
                                :condition-label="[
                                    'text' => ['WHILE index < count(values)?'],
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '13rem',
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :action-label="[
                                    'text' => ['item = values[index]'],
                                    'beforeLength' => '2rem',
                                    'afterLength' => '2rem',
                                    'return' => false,
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :true-label="['text' => ['TRUE'], 'width' => 'half', 'color' => 'green']"
                                :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red']"
                                color="cyan"
                            />
                            {{-- TRUE leaves the entire function; only FALSE advances and returns to WHILE. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.return.loop.left.decision"
                                attach-to="literature.return.loop.left.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="left"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                label-gap="4rem"
                                after-length="1rem"
                                :condition-label="[
                                    'text' => ['IF item > 0?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE: RETURN index'],
                                    'return' => false,
                                    'returnOffset' => '6rem',
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: index = index + 1'],
                                    'return' => false,
                                    'returnOffset' => '0rem',
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :counter-start="8"
                                :left-counter-end="9"
                                :false-stem-counter="10"
                                :right-counter-end="11"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.loop.left.found-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-loop-left',
                                    'literature.return.loop.left.decision.true.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="10.3rem"
                                :dev-counter-end="12"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.return.loop.left.body-return"
                                attach-to="literature.return.loop.left.decision.false.anchorNode-end"
                                return-to="literature.return.loop.left.loop.anchorNode-return"
                                side="left"
                                :counter-start="13"
                                color="green"
                            />
                            {{-- Only exhaustion reaches the fallback; RETURN index never rejoins this route. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.loop.left.fallback"
                                attach-to="literature.return.loop.left.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['RETURN -1'],
                                    'width' => 'default',
                                ]"
                                :counter-end="17"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.loop.left.not-found-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-loop-left',
                                    'literature.return.loop.left.fallback.anchorNode-end',
                                )"
                                :dev-counter-end="18"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- return-loop-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="return-loop-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- return-loop-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-return-loop-right"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.loop.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="[
                                    'text' => ['FUNCTION findFirstPositive(values)', 'index = 0'],
                                    'width' => 'default',
                                ]"
                                after-length="5rem"
                                color="zinc"
                                :counter-end="1"
                            />
                            {{-- WHILE owns the condition; the open body continues into the independent IF. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-while
                                id="literature.return.loop.right.loop"
                                attach-to="literature.return.loop.right.initialize.anchorNode-end"
                                side="right"
                                :counter-start="2"
                                true-bridge-length="4rem"
                                stem-length="5.5rem"
                                :condition-label="[
                                    'text' => ['WHILE index < count(values)?'],
                                    'beforeLength' => '2rem',
                                    'labelGap' => '4rem',
                                    'afterLength' => '13rem',
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :action-label="[
                                    'text' => ['item = values[index]'],
                                    'beforeLength' => '2rem',
                                    'afterLength' => '2rem',
                                    'return' => false,
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :true-label="['text' => ['TRUE'], 'width' => 'half', 'color' => 'green']"
                                :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red']"
                                color="cyan"
                            />
                            {{-- TRUE leaves the entire function; only FALSE advances and returns to WHILE. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.return.loop.right.decision"
                                attach-to="literature.return.loop.right.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="right"
                                bridge-length="2rem"
                                stem-length="4rem"
                                before-length="1rem"
                                label-gap="4rem"
                                after-length="1rem"
                                :condition-label="[
                                    'text' => ['IF item > 0?'],
                                    'width' => 'default',
                                    'align' => 'center',
                                ]"
                                :if-start="[
                                    'text' => ['TRUE: RETURN index'],
                                    'return' => false,
                                    'returnOffset' => '6rem',
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: index = index + 1'],
                                    'return' => false,
                                    'returnOffset' => '0rem',
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :counter-start="8"
                                :left-counter-end="9"
                                :false-stem-counter="10"
                                :right-counter-end="11"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.loop.right.found-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-loop-right',
                                    'literature.return.loop.right.decision.true.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="10.3rem"
                                :dev-counter-end="12"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.return.loop.right.body-return"
                                attach-to="literature.return.loop.right.decision.false.anchorNode-end"
                                return-to="literature.return.loop.right.loop.anchorNode-return"
                                side="right"
                                :counter-start="13"
                                color="green"
                            />
                            {{-- Only exhaustion reaches the fallback; RETURN index never rejoins this route. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.loop.right.fallback"
                                attach-to="literature.return.loop.right.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['RETURN -1'],
                                    'width' => 'default',
                                ]"
                                :counter-end="17"
                                color="zinc"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.loop.right.not-found-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-loop-right',
                                    'literature.return.loop.right.fallback.anchorNode-end',
                                )"
                                :dev-counter-end="18"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- return-loop-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/return/flow-return-loop.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
