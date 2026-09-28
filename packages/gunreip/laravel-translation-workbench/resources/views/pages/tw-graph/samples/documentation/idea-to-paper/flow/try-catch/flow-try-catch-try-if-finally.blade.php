<x-translation-workbench::ui.common.heading-counter-group group="flow-try-catch-try-if-finally">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('TRY → IF/ELSE → FINALLY') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The boolean IF and its selected action belong to TRY. Only one action is attempted. The following outcome split summarizes normal completion or an exception from that protected action; it is runtime dispatch, not an extra IF statement. FINALLY runs after success or a handled failure.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.try-catch.flow-try-catch-try-if-finally" />

            @php
                $tryCatchSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-try-if-finally',
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
                            example="try-catch-try-if-finally-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $tryCatchSource->example('try-catch-try-if-finally-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="try-catch-try-if-finally-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $tryCatchSource->example('try-catch-try-if-finally-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>side</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>left</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Mirrors the authored branches and returns.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>attach-to<br>anchor-start</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null / component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Connects each component to the intended control-flow entry.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>condition-label<br>if-start<br>if-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Names the condition, protected operation, successful action and CATCH action.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>before-length<br>after-length<br>label-gap</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Reserves explicit space for nested content and labels.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>bridge-length<br>stem-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Controls the branch width and vertical separation.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>step-label</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Names setup, cleanup and continuation actions.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>counter-start<br>counter-end<br>left-counter-end<br>false-stem-counter<br>right-counter-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>component default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Keeps DEV numbering continuous across the authored components.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-try-if-finally"
                example="try-catch-try-if-finally"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('C uses explicit status handling instead of exceptions and indexed traversal instead of FOREACH. C++ uses a scope guard for FINALLY-style cleanup. Application helpers, types, enclosing functions and imports are omitted.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('TRY → IF/ELSE → FINALLY — Preview') }}</flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Both layouts show the same scope and execution order. Follow green success and red handled-failure routes to their actual continuation. Protected operations may throw; selection, bookkeeping, handlers and cleanup complete normally in these examples. Unhandled propagation is outside the illustrated paths.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="try-catch-try-if-finally-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- try-catch-try-if-finally-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-try-catch-try-if-finally-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="64rem"
                            min-height="56rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.try-catch.try-if-finally.left.selection"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                side="left"
                                direction="bottom-top"
                                bridge-length="2rem"
                                stem-length="8rem"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['TRY', 'IF usePrimary?'],
                                    'width' => 'default',
                                ]"
                                :if-start="[
                                    'text' => ['Perform primary action'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :if-end="[
                                    'text' => ['Perform secondary action'],
                                    'width' => 'default',
                                    'color' => 'red',
                                ]"
                                :counter-start="1"
                                :left-counter-end="2"
                                :false-stem-counter="3"
                                :right-counter-end="4"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.try-catch.try-if-finally.left.dispatch"
                                attach-to="literature.try-catch.try-if-finally.left.selection.anchorNode-end"
                                side="left"
                                direction="bottom-top"
                                bridge-length="2rem"
                                stem-length="8rem"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['TRY outcome', 'Normal or exception?'],
                                    'width' => 'default',
                                ]"
                                :if-start="[
                                    'text' => ['SUCCESS', 'Record success'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :if-end="[
                                    'text' => ['CATCH', 'Handle failure'],
                                    'width' => 'default',
                                    'color' => 'red',
                                ]"
                                :counter-start="5"
                                :left-counter-end="6"
                                :false-stem-counter="7"
                                :right-counter-end="8"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.try-catch.try-if-finally.left.finally"
                                attach-to="literature.try-catch.try-if-finally.left.dispatch.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['FINALLY', 'Cleanup'],
                                    'width' => 'default',
                                ]"
                                :counter-end="9"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.try-catch.try-if-finally.left.continue"
                                attach-to="literature.try-catch.try-if-finally.left.finally.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Continue process'],
                                    'width' => 'default',
                                ]"
                                :counter-end="10"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- try-catch-try-if-finally-left-example:end --}}
                    </div>

                    <x-translation-workbench::ui.common.heading-counter
                        example="try-catch-try-if-finally-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- try-catch-try-if-finally-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-try-catch-try-if-finally-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="64rem"
                            min-height="56rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.try-catch.try-if-finally.right.selection"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                side="right"
                                direction="bottom-top"
                                bridge-length="2rem"
                                stem-length="8rem"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['TRY', 'IF usePrimary?'],
                                    'width' => 'default',
                                ]"
                                :if-start="[
                                    'text' => ['Perform primary action'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :if-end="[
                                    'text' => ['Perform secondary action'],
                                    'width' => 'default',
                                    'color' => 'red',
                                ]"
                                :counter-start="1"
                                :left-counter-end="2"
                                :false-stem-counter="3"
                                :right-counter-end="4"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.try-catch.try-if-finally.right.dispatch"
                                attach-to="literature.try-catch.try-if-finally.right.selection.anchorNode-end"
                                side="right"
                                direction="bottom-top"
                                bridge-length="2rem"
                                stem-length="8rem"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['TRY outcome', 'Normal or exception?'],
                                    'width' => 'default',
                                ]"
                                :if-start="[
                                    'text' => ['SUCCESS', 'Record success'],
                                    'width' => 'default',
                                    'color' => 'green',
                                ]"
                                :if-end="[
                                    'text' => ['CATCH', 'Handle failure'],
                                    'width' => 'default',
                                    'color' => 'red',
                                ]"
                                :counter-start="5"
                                :left-counter-end="6"
                                :false-stem-counter="7"
                                :right-counter-end="8"
                                color="cyan"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.try-catch.try-if-finally.right.finally"
                                attach-to="literature.try-catch.try-if-finally.right.dispatch.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['FINALLY', 'Cleanup'],
                                    'width' => 'default',
                                ]"
                                :counter-end="9"
                                color="violet"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.try-catch.try-if-finally.right.continue"
                                attach-to="literature.try-catch.try-if-finally.right.finally.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Continue process'],
                                    'width' => 'default',
                                ]"
                                :counter-end="10"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- try-catch-try-if-finally-right-example:end --}}
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/try-catch/flow-try-catch-try-if-finally.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
