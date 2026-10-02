<x-translation-workbench::ui.common.heading-counter-group group="flow-return-void">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('RETURN without a value') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('notifyIfEnabled(enabled) is a procedure: it performs an action but supplies no result expression. When disabled, a bare RETURN immediately leaves the function. When enabled, it records the notification and then returns normally.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.return.flow-return-void" />

            @php
                $returnSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-void',
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
                            example="return-void-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $returnSource->example('return-void-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="return-void-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $returnSource->example('return-void-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="whitespace-normal align-top"><code>attach-to / anchor-start</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>null / component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Connects initialization, the condition and the independent terminal routes.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>side</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>left</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Places both alternatives on the same side of the condition.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start.return / if-end.return</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Both lanes explicitly remain open. An early RETURN must not rejoin the notification action.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>if-start.returnOffset / if-end.returnOffset</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>12rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Uses 12rem and 2rem respectively to separate the two terminal routes.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>bridge-length / stem-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Reserves room for the early RETURN and notification labels.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>before-length / after-length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Controls spacing around the normal bare RETURN step.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>parts.end.length</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>2rem</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('The early terminal stem uses 13rem; the normal exit uses the default.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="whitespace-normal align-top"><code>counter-start / counter-end</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="whitespace-normal align-top">{{ __('Numbers the visible markers consecutively from 1 to 8.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.return.flow-return-void"
                example="return-void"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('PHP declares void and permits bare return; a call still evaluates to null. JavaScript has no void return declaration here, and bare return yields undefined. C, C++, C# and Java use void functions or methods. Reaching the closing brace also ends these procedures; the final RETURN is explicit here to show both exits. Contexts, imports and enclosing classes are omitted where indicated.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('RETURN without a value — Preview') }}
            </flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('TRUE means disabled and ends the function immediately. FALSE records the notification before reaching the normal RETURN. Both exits return control to the caller; neither supplies a result expression. They do not join inside the function.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="return-void-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- return-void-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-return-void-left"
                            :dev="true"
                            :coordinates="true"
                            min-height="36rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.void.left.entry"
                                :anchor-start="['x' => '0rem', 'y' => '32rem']"
                                direction="top-bottom"
                                label-gap="4rem"
                                :step-label="['text' => ['PROCEDURE notifyIfEnabled(enabled)'], 'width' => 'default']"
                                after-length="4rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            {{-- Both lanes stay open: RETURN must not join the remaining function body. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.return.void.left.guard"
                                attach-to="literature.return.void.left.entry.anchorNode-end"
                                direction="top-bottom"
                                side="left"
                                bridge-length="2rem"
                                stem-length="3rem"
                                before-length="2rem"
                                after-length="2rem"
                                label-gap="2.8rem"
                                :condition-label="['text' => ['IF !enabled?'], 'width' => 'default', 'align' => 'center']"
                                :if-start="[
                                    'text' => ['TRUE: RETURN (no value)'],
                                    'return' => false,
                                    'returnOffset' => '12rem',
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: Record notification', 'events += notified'],
                                    'return' => false,
                                    'returnOffset' => '2rem',
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :counter-start="2"
                                :left-counter-end="3"
                                :false-stem-counter="4"
                                :right-counter-end="5"
                                color="sky"
                            />
                            {{-- Terminal cap: no processing follows the early RETURN. --}}
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.void.left.early-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-void-left',
                                    'literature.return.void.left.guard.true.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="13rem"
                                :dev-counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.void.left.result"
                                attach-to="literature.return.void.left.guard.false.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="2.8rem"
                                after-length="2rem"
                                :step-label="['text' => ['RETURN (no value)'], 'width' => 'default']"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                :counter-end="7"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.void.left.normal-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-void-left',
                                    'literature.return.void.left.result.anchorNode-end',
                                )"
                                direction="top-bottom"
                                :dev-counter-end="8"
                                color="green"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- return-void-left-example:end --}}
                    </div>
                    <x-translation-workbench::ui.common.heading-counter
                        example="return-void-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- return-void-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-return-void-right"
                            :dev="true"
                            :coordinates="true"
                            min-height="36rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.void.right.entry"
                                :anchor-start="['x' => '0rem', 'y' => '32rem']"
                                direction="top-bottom"
                                label-gap="4rem"
                                :step-label="['text' => ['PROCEDURE notifyIfEnabled(enabled)'], 'width' => 'default']"
                                after-length="4rem"
                                :counter-end="1"
                                color="zinc"
                            />
                            {{-- Both lanes stay open: RETURN must not join the remaining function body. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.return.void.right.guard"
                                attach-to="literature.return.void.right.entry.anchorNode-end"
                                direction="top-bottom"
                                side="right"
                                bridge-length="2rem"
                                stem-length="3rem"
                                before-length="2rem"
                                after-length="2rem"
                                label-gap="2.8rem"
                                :condition-label="['text' => ['IF !enabled?'], 'width' => 'default', 'align' => 'center']"
                                :if-start="[
                                    'text' => ['TRUE: RETURN (no value)'],
                                    'return' => false,
                                    'returnOffset' => '12rem',
                                    'width' => 'default',
                                    'color' => 'amber',
                                ]"
                                :if-end="[
                                    'text' => ['FALSE: Record notification', 'events += notified'],
                                    'return' => false,
                                    'returnOffset' => '2rem',
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :counter-start="2"
                                :left-counter-end="3"
                                :false-stem-counter="4"
                                :right-counter-end="5"
                                color="sky"
                            />
                            {{-- Terminal cap: no processing follows the early RETURN. --}}
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.void.right.early-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-void-right',
                                    'literature.return.void.right.guard.true.anchorNode-end',
                                )"
                                direction="top-bottom"
                                length="13rem"
                                :dev-counter-end="6"
                                color="amber"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.return.void.right.result"
                                attach-to="literature.return.void.right.guard.false.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="2.8rem"
                                after-length="2rem"
                                :step-label="['text' => ['RETURN (no value)'], 'width' => 'default']"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                :counter-end="7"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.parts.end
                                id="literature.return.void.right.normal-end"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-return-void-right',
                                    'literature.return.void.right.result.anchorNode-end',
                                )"
                                direction="top-bottom"
                                :dev-counter-end="8"
                                color="green"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- return-void-right-example:end --}}
                    </div>

                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/return/flow-return-void.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
