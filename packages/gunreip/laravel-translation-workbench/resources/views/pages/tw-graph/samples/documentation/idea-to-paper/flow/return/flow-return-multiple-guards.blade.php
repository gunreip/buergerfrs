<x-translation-workbench::ui.common.heading-counter-group group="flow-return-multiple-guards">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Multiple guards / early RETURN') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The function checks two guards in sequence. Values at or below 0 return 0 immediately. Only the first FALSE route reaches the second guard, where values at or above 100 return 200. Only two FALSE outcomes reach the calculation and final RETURN.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.return.flow-return-multiple-guards" />

            @php
                $returnSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-multiple-guards',
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
                            example="return-multiple-guards-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $returnSource->example('return-multiple-guards-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="return-multiple-guards-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $returnSource->example('return-multiple-guards-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>direction<br>side</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>bottom-top / left</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Both independent guards run top-bottom; the examples mirror their horizontal routes. The second guard attaches only to the first FALSE endpoint.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>if-start.return<br>if-end.return</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Both are explicitly false: the two paths remain separate and are not joined after the IF.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>if-start.returnOffset<br>if-end.returnOffset</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>0rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The first guard uses returnOffset = 26rem / -12rem; the second uses 11rem / 2rem for TRUE / FALSE.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>if-start.text<br>if-end.text</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('The first TRUE returns 0; the second TRUE returns 200. The first if-end omits text for a continuous bridge. Only the second FALSE calculates the result.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>attach-to<br>anchor-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null / component
                                        default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('upper-guard attaches to guard.false.anchorNode-end; the final RETURN attaches to upper-guard.false.anchorNode-end. Each early RETURN terminates separately.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>parts.end.length</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Explicit terminal lengths of 28.4rem and 13.1rem align the early exits; the final terminal retains its 2rem default.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top">
                                    <code>counter-start<br>counter-end<br>dev-counter-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">
                                    {{ __('Explicit counters number the function entry, decision, branches and terminal markers from 1 to 13.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-multiple-guards"
                example="return-multiple-guards"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('The snippets implement the same integer function in each language. RETURN exits the function, unlike BREAK or CONTINUE, which control a loop. Calling code and class wrappers are omitted where needed.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Multiple guards / early RETURN — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Each TRUE route ends the function at its own cap. The first FALSE route only connects to the second guard; it performs no action. The second FALSE route calculates the result. These are sequential IF statements, not a nested block or a merged return lane.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="return-multiple-guards-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- return-multiple-guards-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-return-multiple-guards-left"
                            :dev="true"
                            :coordinates="true"
                            min-height="36rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.multiple-guards.left.entry"
                                :anchor-start="['x' => '0rem', 'y' => '32rem']"
                                direction="top-bottom"
                                label-gap="4rem"
                                :step-label="['text' => ['FUNCTION boundedDouble(value)'], 'width' => 'default']"
                                after-length="4rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            {{-- Both lanes stay open: RETURN must not join the remaining function body. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.return.multiple-guards.left.guard"
                                attach-to="literature.return.multiple-guards.left.entry.anchorNode-end"
                                direction="top-bottom"
                                side="left"
                                bridge-length="2rem"
                                stem-length="3rem"
                                before-length="2rem"
                                after-length="2rem"
                                label-gap="2.8rem"
                                :condition-label="['text' => ['IF value <= 0?'], 'width' => 'default', 'align' => 'center']"
                                :if-start="[
                                    'text' => ['TRUE: RETURN 0'],
                                    'return' => false,
                                    'returnOffset' => '26rem',
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :if-end="[
                                    'return' => false,
                                    'returnOffset' => '-12rem',
                                    'width' => 'default',
                                    'color' => 'cyan',
                                ]"
                                :counter-start="2"
                                :left-counter-end="3"
                                :false-stem-counter="4"
                                :right-counter-end="5"
                                color="sky"
                            />
                            {{-- Terminal cap: no processing follows the early RETURN. --}}
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.multiple-guards.left.early-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-multiple-guards-left',
                                    'literature.return.multiple-guards.left.guard.true.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="28.4rem"
                                :dev-counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.return.multiple-guards.left.upper-guard"
                                attach-to="literature.return.multiple-guards.left.guard.false.anchorNode-end"
                                direction="top-bottom"
                                side="left"
                                bridge-length="2rem"
                                stem-length="3rem"
                                before-length="2rem"
                                after-length="2rem"
                                label-gap="2.8rem"
                                :condition-label="['text' => ['IF value >= 100?'], 'width' => 'default', 'align' => 'center']"
                                :if-start="[
                                    'text' => ['TRUE: RETURN 200'],
                                    'return' => false,
                                    'returnOffset' => '11rem',
                                    'width' => 'default',
                                    'color' => 'orange',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: Calculate result', 'result = value * 2'],
                                    'return' => false,
                                    'returnOffset' => '2rem',
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :counter-start="7"
                                :left-counter-end="8"
                                :false-stem-counter="9"
                                :right-counter-end="10"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.multiple-guards.left.upper-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-multiple-guards-left',
                                    'literature.return.multiple-guards.left.upper-guard.true.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="13.1rem"
                                :dev-counter-end="11"
                                color="orange"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.multiple-guards.left.result"
                                attach-to="literature.return.multiple-guards.left.upper-guard.false.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="2.8rem"
                                after-length="2rem"
                                :step-label="['text' => ['RETURN result'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="12"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.multiple-guards.left.normal-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-multiple-guards-left',
                                    'literature.return.multiple-guards.left.result.anchorNode-end',
                                )"
                                direction="top-bottom"
                                :dev-counter-end="13"
                                color="green"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- return-multiple-guards-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="return-multiple-guards-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- return-multiple-guards-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-return-multiple-guards-right"
                            :dev="true"
                            :coordinates="true"
                            min-height="36rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.multiple-guards.right.entry"
                                :anchor-start="['x' => '0rem', 'y' => '32rem']"
                                direction="top-bottom"
                                label-gap="4rem"
                                :step-label="['text' => ['FUNCTION boundedDouble(value)'], 'width' => 'default']"
                                after-length="4rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            {{-- Both lanes stay open: RETURN must not join the remaining function body. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.return.multiple-guards.right.guard"
                                attach-to="literature.return.multiple-guards.right.entry.anchorNode-end"
                                direction="top-bottom"
                                side="right"
                                bridge-length="2rem"
                                stem-length="3rem"
                                before-length="2rem"
                                after-length="2rem"
                                label-gap="2.8rem"
                                :condition-label="['text' => ['IF value <= 0?'], 'width' => 'default', 'align' => 'center']"
                                :if-start="[
                                    'text' => ['TRUE: RETURN 0'],
                                    'return' => false,
                                    'returnOffset' => '26rem',
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :if-end="[
                                    'return' => false,
                                    'returnOffset' => '-12rem',
                                    'width' => 'default',
                                    'color' => 'cyan',
                                ]"
                                :counter-start="2"
                                :left-counter-end="3"
                                :false-stem-counter="4"
                                :right-counter-end="5"
                                color="sky"
                            />
                            {{-- Terminal cap: no processing follows the early RETURN. --}}
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.multiple-guards.right.early-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-multiple-guards-right',
                                    'literature.return.multiple-guards.right.guard.true.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="28.4rem"
                                :dev-counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.return.multiple-guards.right.upper-guard"
                                attach-to="literature.return.multiple-guards.right.guard.false.anchorNode-end"
                                direction="top-bottom"
                                side="right"
                                bridge-length="2rem"
                                stem-length="3rem"
                                before-length="2rem"
                                after-length="2rem"
                                label-gap="2.8rem"
                                :condition-label="['text' => ['IF value >= 100?'], 'width' => 'default', 'align' => 'center']"
                                :if-start="[
                                    'text' => ['TRUE: RETURN 200'],
                                    'return' => false,
                                    'returnOffset' => '11rem',
                                    'width' => 'default',
                                    'color' => 'orange',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: Calculate result', 'result = value * 2'],
                                    'return' => false,
                                    'returnOffset' => '2rem',
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :counter-start="7"
                                :left-counter-end="8"
                                :false-stem-counter="9"
                                :right-counter-end="10"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.multiple-guards.right.upper-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-multiple-guards-right',
                                    'literature.return.multiple-guards.right.upper-guard.true.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="13.1rem"
                                :dev-counter-end="11"
                                color="orange"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.multiple-guards.right.result"
                                attach-to="literature.return.multiple-guards.right.upper-guard.false.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="2.8rem"
                                after-length="2rem"
                                :step-label="['text' => ['RETURN result'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="12"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.multiple-guards.right.normal-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-multiple-guards-right',
                                    'literature.return.multiple-guards.right.result.anchorNode-end',
                                )"
                                direction="top-bottom"
                                :dev-counter-end="13"
                                color="green"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- return-multiple-guards-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/return/flow-return-multiple-guards.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
