<x-translation-workbench::ui.common.heading-counter-group group="flow-for-test">
    <flux:callout
        class="mt-4"
        color="indigo"
        icon="information-circle"
    >
        <flux:callout.heading>{{ __('FOR variants — progress and next steps') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('All planned FOR variants are saved as independent left/right examples. FOR Test retains the SWITCH example for future experiments. Early exits and loop control will be documented separately.') }}
        </flux:callout.text>
    </flux:callout>
    <flux:callout
        class="mt-4"
        color="zinc"
        icon="list-bullet"
    >
        <flux:callout.heading>{{ __('Suggested sequence') }}</flux:callout.heading>
        <flux:table class="mt-3">
            <flux:table.columns>
                <flux:table.column>#</flux:table.column>
                <flux:table.column>{{ __('Example') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('What it demonstrates') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell>1</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('FOR basic') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            size="sm"
                            color="green"
                        >{{ __('Saved · left / right') }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">
                        {{ __('Initialization, condition, body, increment and zero iterations.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>2</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Descending counter / step size') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            size="sm"
                            color="green"
                        >{{ __('Saved · left / right') }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">
                        {{ __('Count down or advance by more than one; check the matching exit condition.') }}
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>3</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Multiple body actions') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            size="sm"
                            color="green"
                        >{{ __('Saved · left / right') }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Run all body actions before the increment.') }}
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>4</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('FOR with IF / SWITCH') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            size="sm"
                            color="green"
                        >{{ __('Saved · left / right') }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">
                        {{ __('Rejoin conditional branches before the common increment.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>5</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Nested FOR') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            size="sm"
                            color="green"
                        >{{ __('Saved · left / right') }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">
                        {{ __('Initialize the inner counter for each outer iteration and return to the correct condition.') }}
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>6</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Two independent inner loops') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            size="sm"
                            color="green"
                        >{{ __('Saved · left / right') }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Keep counters and return targets separate.') }}
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>7</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Mixed sides and crossings') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            size="sm"
                            color="green"
                        >{{ __('Saved · left / right') }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">
                        {{ __('Mirror nested loops and show crossings with explicit line-jumps.') }}</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>8</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">{{ __('Action → Nested FOR → Action') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            size="sm"
                            color="green"
                        >{{ __('Saved · left / right') }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-normal">
                        {{ __('Keep preparation and finalization outside the inner return.') }}</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('FOR with SWITCH — Test') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Load the items once and initialize index to zero. Each FOR iteration reads the current item and selects an action with SWITCH: edit a draft, display a published item, or record an unknown status. Every CASE and DEFAULT rejoins before the shared increment. BREAK leaves only the SWITCH; the FOR continues with the next item. An empty list skips the entire body.') }}
            </flux:callout.text>

            <x-translation-workbench::ui.common.separator-deep-reference-links />
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.for.flow-for-test" />

            @php
                $forSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-test',
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
                            example="for-switch-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $forSource->example('for-switch-example') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                    {{ __('Controls the stems around a step. The condition explicitly uses after-length="30rem" to reserve room for the nested SWITCH and shared increment.') }}
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






                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>case-expression</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>SWITCH expression; halfLong</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Displays the current item status. Its stemLength is explicitly 3rem.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>cases</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>[]</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Defines the draft and published CASE labels and their actions, widths and colors.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>case-default</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>Default action; halfLong</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('Records unknown statuses and rejoins the same shared increment.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>stem-length</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>10rem (flow-switch-case)</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('The example explicitly uses 4rem between CASE lanes.') }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>inner-switch.anchorNode-end</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>common output</code></flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">{{ __('All CASE and DEFAULT routes converge here before the increment.') }}</flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
            <x-translation-workbench::ui.tw-graph.language-examples
                source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.for.flow-for-test"
                example="for-switch"
            >
                <flux:callout.text class="mt-2 text-sm">
                    {{ __('The FOR header contains initialization, condition and increment. These syntax excerpts use application-provided actions; enclosing functions, classes and imports are omitted. C and C++ use status enums instead of string cases.') }}
                </flux:callout.text>
            </x-translation-workbench::ui.tw-graph.language-examples>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('FOR with SWITCH — Preview') }}</flux:callout.heading>
            <flux:callout.text class="mb-3">
                {{ __('Follow the FOR TRUE route into the SWITCH. Each CASE or DEFAULT action reaches the common output before index increases. The return then leads to the FOR condition, while its FALSE exit reaches the summary. CASE BREAK does not terminate the FOR. The longer condition stem explicitly reserves room for all SWITCH lanes.') }}
            </flux:callout.text>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-1">
                    <x-translation-workbench::ui.common.heading-counter
                        example="for-switch-example"
                        size="sm"
                    >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    <div
                        class="overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- for-switch-example:start --}}
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-for-test"
                            :dev="true"
                            :coordinates="true"
                            min-height="44rem"
                            min-width="36rem"
                            horizontal-padding="2rem"
                        >
                            {{-- Initialization runs once. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.1.initialize"
                                :anchor-start="['x' => '0rem', 'y' => '2rem']"
                                :step-label="['text' => ['Load items', 'count = item count'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- FOR initialization runs once; the return skips this step. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.1.initialize-index"
                                attach-to="literature.for.1.initialize.anchorNode-end"
                                :step-label="['text' => ['index = 0'], 'width' => 'default']"
                                after-length="5rem"
                                color="zinc"
                                :node-end="false"
                            />
                            {{-- Check before every iteration, including the first. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.1.condition"
                                attach-to="literature.for.1.initialize-index.anchorNode-end"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="30rem"
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
                                id="literature.for.1.loop"
                                :anchor-start="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-test',
                                    'literature.for.1.condition.anchorNode-end',
                                )"
                                :anchor-return="\Gunreip\TranslationWorkbench\Support\TwGraph\AnchorRegistry::get(
                                    'idea-to-paper-for-test',
                                    'literature.for.1.initialize-index.anchorNode-end',
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
                            {{-- All CASE exits and DEFAULT rejoin before the shared index increment. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-switch-case
                                id="literature.for.1.inner-switch"
                                :counter-start="7"
                                attach-to="literature.for.1.loop.body.anchorNode-end"
                                direction="top-bottom"
                                side="left"
                                stem-length="4rem"
                                color="sky"
                                :case-expression="[
                                    'text' => ['SWITCH item.status'],
                                    'width' => 'default',
                                    'stemLength' => '3rem',
                                ]"
                                :cases="[
                                    [
                                        'key' => 'draft',
                                        'label' => 'CASE draft',
                                        'actionLabel' => [
                                            'text' => ['Edit draft'],
                                            'width' => 'default',
                                            'color' => 'amber',
                                        ],
                                    ],
                                    [
                                        'key' => 'published',
                                        'label' => 'CASE published',
                                        'actionLabel' => [
                                            'text' => ['Display item'],
                                            'width' => 'default',
                                            'color' => 'green',
                                        ],
                                    ],
                                ]"
                                :case-default="[
                                    'text' => ['Record unknown status'],
                                    'width' => 'default',
                                    'color' => 'zinc',
                                ]"
                            />
                            {{-- Increment once after the selected CASE or DEFAULT action. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.1.advance"
                                attach-to="literature.for.1.inner-switch.anchorNode-end"
                                direction="top-bottom"
                                before-length="2rem"
                                label-gap="4rem"
                                after-length="2rem"
                                :step-label="['text' => ['index = index + 1'], 'width' => 'default']"
                                :node-end-dot="false"
                                :counter-end="14"
                                color="cyan"
                            />
                            {{-- Return after the selected SWITCH action and shared increment, to the FOR condition. --}}
                            <x-translation-workbench::ui.tw-graph.paths.loop-return
                                id="literature.for.1.body-return"
                                attach-to="literature.for.1.advance.anchorNode-end"
                                return-to="literature.for.1.loop.anchorNode-return"
                                side="left"
                                :counter-start="15"
                                color="cyan"
                            />
                            {{-- FALSE continues here, including when the item list is empty. --}}
                            <x-translation-workbench::ui.tw-graph.strang.flow-step
                                id="literature.for.1.continue"
                                attach-to="literature.for.1.loop.anchorNode-end"
                                before-length="4rem"
                                :step-label="['text' => ['Show summary'], 'width' => 'default']"
                                :counter-end="19"
                                color="violet"
                            />
                        </x-translation-workbench::ui.tw-graph>
                        {{-- for-switch-example:end --}}
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            {{-- Path/To/File --}}
            <x-translation-workbench::ui.common.tw-graph-path-file
                path="packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/flow/for/flow-for-test.blade.php"
                segments="3"
            />
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
