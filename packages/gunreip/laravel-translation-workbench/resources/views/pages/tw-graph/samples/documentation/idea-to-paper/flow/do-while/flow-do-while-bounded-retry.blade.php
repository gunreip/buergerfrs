<x-translation-workbench::ui.common.heading-counter-group group="flow-do-while-bounded-retry">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Bounded retry') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Start with attempt = 0 and maxAttempts = 3. Each body execution increments attempt and performs one operation. Repeat only while the operation failed AND attempt is below the limit. Success or an exhausted limit leaves the loop. DO WHILE always performs at least one attempt.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.do-while.flow-do-while-bounded-retry" />

            @php
                $doWhileSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.do-while.flow-do-while-bounded-retry',
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
                            example="do-while-bounded-retry-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $doWhileSource->example('do-while-bounded-retry-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="do-while-bounded-retry-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $doWhileSource->example('do-while-bounded-retry-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>attach-to</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Connects each action and the condition in execution order.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>step-label</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Names initialization, body actions, the post-condition and continuation.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>before-length<br>after-length<br>label-gap</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Sets explicit stem lengths and space for each step label.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>node-end<br>node-end-dot<br>joint-arrow-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>true<br>null<br>false</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Uses joint arrows on body steps and dots where information or a return joins the path.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>segment</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Defines each arc or straight segment with explicit anchors, radius or length, color and endpoint markers.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>label<br>side</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[] / right (segments.label)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Places TRUE and FALSE information beside their anchor dots.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>anchor-start<br>return-to</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null (paths.loop-return)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Returns from the upper turn to the body entry, after the one-time initialization.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>side</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>left (paths.loop-return)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Positions the repeat lane to the left or right of the body.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>counter-end<br>counter-start</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>1</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Continues DEV numbering across the separate steps and return path.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.do-while.flow-do-while-bounded-retry"
                example="do-while-bounded-retry"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('All six languages support do { ... } while (condition); with a trailing semicolon. The condition is evaluated after the body. Application functions, enclosing functions and imports are omitted.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Bounded retry — Preview') }}</flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Both previews execute the body before the condition. Green TRUE returns to the first body action, skipping preparation; red FALSE continues after the loop. The side changes only the position of the repeat lane.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="do-while-bounded-retry-left-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- do-while-bounded-retry-left-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-do-while-bounded-retry-left"
                            :dev="true"
                            :coordinates="true"
                            min-width="40rem"
                            min-height="40rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Preparation runs once, outside the DO WHILE return. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.do-while.bounded-retry.left.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                after-length="5rem"
                                :step-label="['text' => ['attempt = 0', 'maxAttempts = 3'], 'width' => 'default']"
                                :node-end="false"
                                color="zinc"
                            />
                            {{-- Enter the body before any condition check. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.do-while.bounded-retry.left.attempt"
                                attach-to="literature.do-while.bounded-retry.left.initialize.anchorNode-end"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="4rem"
                                :step-label="['text' => ['attempt = attempt + 1'], 'width' => 'default']"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                :counter-end="1"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.do-while.bounded-retry.left.action"
                                attach-to="literature.do-while.bounded-retry.left.attempt.anchorNode-end"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="4rem"
                                :step-label="['text' => ['success = Try operation'], 'width' => 'default']"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                :counter-end="2"
                                color="green"
                            />
                            {{-- Check only after the complete body. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.do-while.bounded-retry.left.condition"
                                attach-to="literature.do-while.bounded-retry.left.action.anchorNode-end"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="4rem"
                                :step-label="['text' => ['WHILE !success', 'AND attempt < maxAttempts?'], 'width' => 'default']"
                                :counter-end="3"
                                color="cyan"
                            />
                            @php
                                $leftConditionEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-do-while-bounded-retry-left',
                                    'literature.do-while.bounded-retry.left.condition.anchorNode-end',
                                );
                                $leftArcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('arc_radius', '2.75rem');
                                // These explicit lengths control the clearance beside the body and the exit.
                                $leftBridgeLength = '10rem';
                                $leftExitLength = '6rem';
                                $leftArcInEnd = [
                                    'x' => 'calc(' . $leftConditionEnd['x'] . ' - ' . $leftArcRadius . ')',
                                    'y' => 'calc(' . $leftConditionEnd['y'] . ' + ' . $leftArcRadius . ')',
                                ];
                                $leftBridgeEnd = [
                                    'x' => 'calc(' . $leftArcInEnd['x'] . ' - ' . $leftBridgeLength . ')',
                                    'y' => $leftArcInEnd['y'],
                                ];
                                $leftReturnStart = [
                                    'x' => 'calc(' . $leftBridgeEnd['x'] . ' - ' . $leftArcRadius . ')',
                                    'y' => $leftConditionEnd['y'],
                                ];
                                $leftExitEnd = [
                                    'x' => $leftConditionEnd['x'],
                                    'y' => 'calc(' . $leftConditionEnd['y'] . ' + ' . $leftExitLength . ')',
                                ];
                            @endphp
                            {{-- TRUE is a plain connection back to the first action. --}}
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.do-while.bounded-retry.left.repeat.arc-in',
                                'anchorStart' => $leftConditionEnd,
                                'anchorEnd' => $leftArcInEnd,
                                'startAnchor' => 'e',
                                'endAnchor' => 'n',
                                'arcRadius' => $leftArcRadius,
                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'left',
                                'devCounterEnd' => 4,
                                'devCounterColor' => 'green',
                                'color' => 'green',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                                'id' => 'literature.do-while.bounded-retry.left.repeat.bridge',
                                'anchorStart' => $leftArcInEnd,
                                'anchorEnd' => $leftBridgeEnd,
                                'direction' => 'right-left',
                                'length' => $leftBridgeLength,
                                'nodeEnd' => true,
                                'nodeEndDot' => true,
                                'devCounterEnd' => 5,
                                'devCounterColor' => 'green',
                                'color' => 'green',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.segments.label
                                id="literature.do-while.bounded-retry.left.repeat.label"
                                :anchor-x="$leftBridgeEnd['x']"
                                :anchor-y="$leftBridgeEnd['y']"
                                :label="['text' => ['TRUE'], 'width' => 'half', 'align' => 'center']"
                                side="top"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.do-while.bounded-retry.left.repeat.arc-out',
                                'anchorStart' => $leftBridgeEnd,
                                'anchorEnd' => $leftReturnStart,
                                'startAnchor' => 'n',
                                'endAnchor' => 'w',
                                'arcRadius' => $leftArcRadius,
                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'bottom',
                                'devCounterEnd' => 6,
                                'devCounterColor' => 'green',
                                'color' => 'green',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.do-while.bounded-retry.left.repeat.return"
                                :anchor-start="$leftReturnStart"
                                return-to="literature.do-while.bounded-retry.left.initialize.anchorNode-end"
                                side="left"
                                :counter-start="7"
                                color="green"
                            />
                            {{-- FALSE leaves the loop after the completed body. --}}
                            <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                                'id' => 'literature.do-while.bounded-retry.left.exit.stem',
                                'anchorStart' => $leftConditionEnd,
                                'anchorEnd' => $leftExitEnd,
                                'direction' => 'bottom-top',
                                'length' => $leftExitLength,
                                'nodeEnd' => true,
                                'nodeEndDot' => true,
                                'devCounterEnd' => 11,
                                'devCounterColor' => 'red',
                                'color' => 'red',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.segments.label
                                id="literature.do-while.bounded-retry.left.exit.label"
                                :anchor-x="$leftExitEnd['x']"
                                :anchor-y="$leftExitEnd['y']"
                                :label="['text' => ['FALSE'], 'width' => 'half', 'align' => 'center']"
                                side="right"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.do-while.bounded-retry.left.continue"
                                :anchor-start="$leftExitEnd"
                                before-length="4rem"
                                :step-label="['text' => ['Report outcome'], 'width' => 'default']"
                                :counter-end="12"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- do-while-bounded-retry-left-example:end --}}
                    </div>

                    <x-translation-workbench::ui.common.heading-counter
                        example="do-while-bounded-retry-right-example"
                        size="sm"
                    >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- do-while-bounded-retry-right-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-do-while-bounded-retry-right"
                            :dev="true"
                            :coordinates="true"
                            min-width="40rem"
                            min-height="40rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Preparation runs once, outside the DO WHILE return. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.do-while.bounded-retry.right.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                after-length="5rem"
                                :step-label="['text' => ['attempt = 0', 'maxAttempts = 3'], 'width' => 'default']"
                                :node-end="false"
                                color="zinc"
                            />
                            {{-- Enter the body before any condition check. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.do-while.bounded-retry.right.attempt"
                                attach-to="literature.do-while.bounded-retry.right.initialize.anchorNode-end"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="4rem"
                                :step-label="['text' => ['attempt = attempt + 1'], 'width' => 'default']"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                :counter-end="1"
                                color="sky"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.do-while.bounded-retry.right.action"
                                attach-to="literature.do-while.bounded-retry.right.attempt.anchorNode-end"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="4rem"
                                :step-label="['text' => ['success = Try operation'], 'width' => 'default']"
                                :node-end-dot="false"
                                :joint-arrow-end="true"
                                :counter-end="2"
                                color="green"
                            />
                            {{-- Check only after the complete body. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.do-while.bounded-retry.right.condition"
                                attach-to="literature.do-while.bounded-retry.right.action.anchorNode-end"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="4rem"
                                :step-label="['text' => ['WHILE !success', 'AND attempt < maxAttempts?'], 'width' => 'default']"
                                :counter-end="3"
                                color="cyan"
                            />
                            @php
                                $rightConditionEnd = \Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-do-while-bounded-retry-right',
                                    'literature.do-while.bounded-retry.right.condition.anchorNode-end',
                                );
                                $rightArcRadius = \Gunreip\TranslationWorkbench\Support\TwGraph\Defaults::graphString('arc_radius', '2.75rem');
                                // These explicit lengths control the clearance beside the body and the exit.
                                $rightBridgeLength = '10rem';
                                $rightExitLength = '6rem';
                                $rightArcInEnd = [
                                    'x' => 'calc(' . $rightConditionEnd['x'] . ' + ' . $rightArcRadius . ')',
                                    'y' => 'calc(' . $rightConditionEnd['y'] . ' + ' . $rightArcRadius . ')',
                                ];
                                $rightBridgeEnd = [
                                    'x' => 'calc(' . $rightArcInEnd['x'] . ' + ' . $rightBridgeLength . ')',
                                    'y' => $rightArcInEnd['y'],
                                ];
                                $rightReturnStart = [
                                    'x' => 'calc(' . $rightBridgeEnd['x'] . ' + ' . $rightArcRadius . ')',
                                    'y' => $rightConditionEnd['y'],
                                ];
                                $rightExitEnd = [
                                    'x' => $rightConditionEnd['x'],
                                    'y' => 'calc(' . $rightConditionEnd['y'] . ' + ' . $rightExitLength . ')',
                                ];
                            @endphp
                            {{-- TRUE is a plain connection back to the first action. --}}
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.do-while.bounded-retry.right.repeat.arc-in',
                                'anchorStart' => $rightConditionEnd,
                                'anchorEnd' => $rightArcInEnd,
                                'startAnchor' => 'w',
                                'endAnchor' => 'n',
                                'arcRadius' => $rightArcRadius,
                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'right',
                                'devCounterEnd' => 4,
                                'devCounterColor' => 'green',
                                'color' => 'green',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                                'id' => 'literature.do-while.bounded-retry.right.repeat.bridge',
                                'anchorStart' => $rightArcInEnd,
                                'anchorEnd' => $rightBridgeEnd,
                                'direction' => 'left-right',
                                'length' => $rightBridgeLength,
                                'nodeEnd' => true,
                                'nodeEndDot' => true,
                                'devCounterEnd' => 5,
                                'devCounterColor' => 'green',
                                'color' => 'green',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.segments.label
                                id="literature.do-while.bounded-retry.right.repeat.label"
                                :anchor-x="$rightBridgeEnd['x']"
                                :anchor-y="$rightBridgeEnd['y']"
                                :label="['text' => ['TRUE'], 'width' => 'half', 'align' => 'center']"
                                side="top"
                                color="green"
                            />
                            <x-translation-workbench::ui.tw-graph.segments.arc :segment="[
                                'id' => 'literature.do-while.bounded-retry.right.repeat.arc-out',
                                'anchorStart' => $rightBridgeEnd,
                                'anchorEnd' => $rightReturnStart,
                                'startAnchor' => 'n',
                                'endAnchor' => 'e',
                                'arcRadius' => $rightArcRadius,
                                'nodeEnd' => true,
                                'nodeEndDot' => false,
                                'jointArrowEnd' => true,
                                'jointArrowEndDirection' => 'bottom',
                                'devCounterEnd' => 6,
                                'devCounterColor' => 'green',
                                'color' => 'green',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.do-while.bounded-retry.right.repeat.return"
                                :anchor-start="$rightReturnStart"
                                return-to="literature.do-while.bounded-retry.right.initialize.anchorNode-end"
                                side="right"
                                :counter-start="7"
                                color="green"
                            />
                            {{-- FALSE leaves the loop after the completed body. --}}
                            <x-translation-workbench::ui.tw-graph.segments.path :segment="[
                                'id' => 'literature.do-while.bounded-retry.right.exit.stem',
                                'anchorStart' => $rightConditionEnd,
                                'anchorEnd' => $rightExitEnd,
                                'direction' => 'bottom-top',
                                'length' => $rightExitLength,
                                'nodeEnd' => true,
                                'nodeEndDot' => true,
                                'devCounterEnd' => 11,
                                'devCounterColor' => 'red',
                                'color' => 'red',
                            ]" />
                            <x-translation-workbench::ui.tw-graph.segments.label
                                id="literature.do-while.bounded-retry.right.exit.label"
                                :anchor-x="$rightExitEnd['x']"
                                :anchor-y="$rightExitEnd['y']"
                                :label="['text' => ['FALSE'], 'width' => 'half', 'align' => 'center']"
                                side="left"
                                color="red"
                            />
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.do-while.bounded-retry.right.continue"
                                :anchor-start="$rightExitEnd"
                                before-length="4rem"
                                :step-label="['text' => ['Report outcome'], 'width' => 'default']"
                                :counter-end="12"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- do-while-bounded-retry-right-example:end --}}
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/do-while/flow-do-while-bounded-retry.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
