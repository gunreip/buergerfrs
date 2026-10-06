<x-translation-workbench::ui.common.heading-counter-group group="flow-try-catch-if-try">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('IF → TRY/CATCH') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Only the enabled IF branch prepares and performs the protected operation. Success and a handled failure both rejoin the outer IF continuation. The disabled branch skips TRY and records the skipped operation.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.try-catch.flow-try-catch-if-try" />

            @php
                $tryCatchSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-if-try',
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
                            example="try-catch-if-try-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $tryCatchSource->example('try-catch-if-try-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="try-catch-if-try-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $tryCatchSource->example('try-catch-if-try-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>if-start.return</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('False leaves the successful branch open for its explicitly authored continuation.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>return-to</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Identifies the actual destination of an authored return.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>gradient<br>node-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true (parts.start)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Disables gradients and duplicate markers on connecting stems.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.try-catch.flow-try-catch-if-try"
                example="try-catch-if-try"
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
            <flux:callout.heading icon="eye">{{ __('IF → TRY/CATCH — Preview') }}</flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Both layouts show the same scope and execution order. Follow green success and red handled-failure routes to their actual continuation. Protected operations may throw; selection, bookkeeping, handlers and cleanup complete normally in these examples. Unhandled propagation is outside the illustrated paths.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="try-catch-if-try-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- try-catch-if-try-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-try-catch-if-try-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="64rem"
                            min-height="56rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.try-catch.if-try.left.outer"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                side="left"
                                direction="bottom-top"
                                bridge-length="2rem"
                                stem-length="40rem"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['IF enabled?'],
                                    'width' => 'default',
                                ]"
                                :if-start="[
                                    'text' => ['Prepare operation'],
                                    'width' => 'default',
                                    'color' => 'green',
                                    'return' => false,
                                ]"
                                :if-end="[
                                    'text' => ['Record skipped operation'],
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
                                id="literature.try-catch.if-try.left.dispatch"
                                attach-to="literature.try-catch.if-try.left.outer.true.anchorNode-end"
                                side="left"
                                direction="bottom-top"
                                bridge-length="2rem"
                                stem-length="8rem"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['TRY', 'Perform operation'],
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
                            {{-- Return from the protected branch to the outer IF output. --}}
                            @php
                                $leftInnerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-try-catch-if-try-left',
                                    'literature.try-catch.if-try.left.dispatch.anchorNode-end',
                                );
                                $leftOuterEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-try-catch-if-try-left',
                                    'literature.try-catch.if-try.left.outer.true.anchorNode-return',
                                );
                                $leftRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('arc_radius', '2.75rem');
                                $leftBridge = 'calc((' . $leftOuterEnd['x'] . ' - ' . $leftInnerEnd['x'] . ') * 1 - (2 * ' . $leftRadius . '))';
                            @endphp
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.try-catch.if-try.left.inner-return"
                                :anchor-start="$leftInnerEnd"
                                side="right"
                                :bridge-length="$leftBridge"
                                :joint-arrow-end="true"
                                :dev-counter-end="9"
                                color="green"
                            />
                            @php
                                $leftReturnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-try-catch-if-try-left',
                                    'literature.try-catch.if-try.left.inner-return.anchorNode-end',
                                );
                            @endphp
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.try-catch.if-try.left.inner-return.stem"
                                :anchor-start="$leftReturnEnd"
                                :length="'calc(' . $leftOuterEnd['y'] . ' - ' . $leftReturnEnd['y'] . ')'"
                                return-to="literature.try-catch.if-try.left.outer.true.anchorNode-return"
                                :gradient="false"
                                :node-end="false"
                                :dev-counter-end="false"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.try-catch.if-try.left.continue"
                                attach-to="literature.try-catch.if-try.left.outer.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Continue process'],
                                    'width' => 'default',
                                ]"
                                :counter-end="10"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- try-catch-if-try-left-example:end --}}
                    </div>

                    <x-translation-workbench::ui.common.heading-counter
                        example="try-catch-if-try-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- try-catch-if-try-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-try-catch-if-try-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="64rem"
                            min-height="56rem"
                            horizontal-padding="4rem"
                        >
                            <x-translation-workbench::ui.tw-graph.strang.flow-if-else
                                id="literature.try-catch.if-try.right.outer"
                                :anchor-start="['x' => '0rem', 'y' => '3rem']"
                                side="right"
                                direction="bottom-top"
                                bridge-length="2rem"
                                stem-length="40rem"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['IF enabled?'],
                                    'width' => 'default',
                                ]"
                                :if-start="[
                                    'text' => ['Prepare operation'],
                                    'width' => 'default',
                                    'color' => 'green',
                                    'return' => false,
                                ]"
                                :if-end="[
                                    'text' => ['Record skipped operation'],
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
                                id="literature.try-catch.if-try.right.dispatch"
                                attach-to="literature.try-catch.if-try.right.outer.true.anchorNode-end"
                                side="right"
                                direction="bottom-top"
                                bridge-length="2rem"
                                stem-length="8rem"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :condition-label="[
                                    'text' => ['TRY', 'Perform operation'],
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
                            {{-- Return from the protected branch to the outer IF output. --}}
                            @php
                                $rightInnerEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-try-catch-if-try-right',
                                    'literature.try-catch.if-try.right.dispatch.anchorNode-end',
                                );
                                $rightOuterEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-try-catch-if-try-right',
                                    'literature.try-catch.if-try.right.outer.true.anchorNode-return',
                                );
                                $rightRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('arc_radius', '2.75rem');
                                $rightBridge = 'calc((' . $rightOuterEnd['x'] . ' - ' . $rightInnerEnd['x'] . ') * -1 - (2 * ' . $rightRadius . '))';
                            @endphp
                            <x-translation-workbench::ui.tw-graph.parts.sideways
                                id="literature.try-catch.if-try.right.inner-return"
                                :anchor-start="$rightInnerEnd"
                                side="left"
                                :bridge-length="$rightBridge"
                                :joint-arrow-end="true"
                                :dev-counter-end="9"
                                color="green"
                            />
                            @php
                                $rightReturnEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-try-catch-if-try-right',
                                    'literature.try-catch.if-try.right.inner-return.anchorNode-end',
                                );
                            @endphp
                            <x-translation-workbench::ui.tw-graph.parts.start
                                id="literature.try-catch.if-try.right.inner-return.stem"
                                :anchor-start="$rightReturnEnd"
                                :length="'calc(' . $rightOuterEnd['y'] . ' - ' . $rightReturnEnd['y'] . ')'"
                                return-to="literature.try-catch.if-try.right.outer.true.anchorNode-return"
                                :gradient="false"
                                :node-end="false"
                                :dev-counter-end="false"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.try-catch.if-try.right.continue"
                                attach-to="literature.try-catch.if-try.right.outer.anchorNode-end"
                                before-length="4rem"
                                :step-label="[
                                    'text' => ['Continue process'],
                                    'width' => 'default',
                                ]"
                                :counter-end="10"
                                color="zinc"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- try-catch-if-try-right-example:end --}}
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/try-catch/flow-try-catch-if-try.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
