<x-translation-workbench::ui.common.heading-counter-group group="flow-if-elseif">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('IF ELSEIF') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.documentation-links example="flow.if.flow-if-elseif" />
            <flux:callout.text>
                {{ __('The first matching condition selects one action. IF reviewApproved executes Publish paper. Only its False route reaches ELSEIF changesRequested, which may execute Revise draft. When both conditions are false, a plain bypass reaches the common output without an action. Continue process runs after any of these three routes. Each side is authored as a separate component example.') }}
            </flux:callout.text>
            @php
                $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.if.flow-if-elseif',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="flow-if-elseif-example-1"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $docExampleSource->example('flow-if-elseif-example-1') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="flow-if-elseif-example-2"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $docExampleSource->example('flow-if-elseif-example-2') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator class="mt-4" :text="__('Props used in these examples')" />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Props') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns class="bg-white dark:bg-zinc-900" sticky>
                            <flux:table.column>{{ __('Prop') }}</flux:table.column>
                            <flux:table.column>{{ __('Default') }}</flux:table.column>
                            <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>generated</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Stable prefix; .anchorNode-end is the shared continuation.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>component-counter</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>1</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Counter used to generate the fallback ID.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>attach-to</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Registered incoming anchor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchor-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Fallback input coordinates.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>left</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('left or right: both actions and the bypass travel in the same direction.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bottom-top</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('bottom-top or top-bottom.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>condition-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>IF condition?; halfLong</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('First decision; text or label configuration.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>if-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>IF action; half</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Action executed when the first condition is true. Its resolved color also colors the complete shared return stem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>elseifs</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]; one entry required</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('List with exactly one ELSEIF. Each entry supports conditionLabel, actionLabel, color, beforeLength and afterLength. Use flow-if-elseif-multi for multiple entries.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>elseifs[].conditionLabel</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>ELSEIF condition?; halfLong</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Second decision, reached only when the first condition is false.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>elseifs[].actionLabel</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>ELSEIF action; half</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Action executed only when the first condition is false and the second is true.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>before-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Line before the first condition.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>after-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Line after the first condition.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>elseif-before-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>6rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Line from the first False output to the second condition.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>elseif-after-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Line after the second condition.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>8rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Distance between the second action and the final bypass; expands for multiline labels.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>arc-radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>canvas arc-radius / 2.75rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Radius of the route arcs.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length around each action label. The total route width follows the wider label; the bypass spans that same width.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>node-labels</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional left/right labels at the shared output.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>node-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Show the common endpoint.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inherited / zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Default route color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>z-index</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>20</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Stacking order.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>if-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('color sets the entire False lane; without text the bridge stays continuous. Adding text inserts a label; badgeColor overrides its color. The canvas controls path-tone.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>if-end.stemLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length / 8rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Overrides the incoming stem of the final False route, including a textless bypass. The opposite return stem adjusts with it; label clearance remains a minimum.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
        </flux:callout>
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">{{ __('Preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <x-translation-workbench::ui.common.heading-counter
                    example="flow-if-elseif-example-1"
                    size="sm"
                >{{ __('side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- flow-if-elseif-example-1:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-flow-if-elseif"
                        :dev="true"
                        :coordinates="true"
                        min-width="56rem"
                        min-height="44rem"
                        horizontal-padding="6rem"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif
                            id="literature.flow.1.if-elseif"
                            :anchor-start="['x' => '0rem', 'y' => '3rem']"
                            side="left"
                            direction="bottom-top"
                            color="cyan"
                            arc-radius="2.75rem"
                            bridge-length="2rem"
                            before-length="2rem"
                            after-length="2rem"
                            elseif-before-length="4rem"
                            elseif-after-length="2rem"
                            stem-length="4rem"
                            :if-end="['color' => 'zinc']"
                            :condition-label="[
                                'text' => ['IF reviewApproved?'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                            :if-start="[
                                'color' => 'green',
                                'text' => ['Publish paper'],
                                'width' => 'default',
                                'align' => 'center',
                                'badgeColor' => 'green',
                            ]"
                            :elseifs="[
                                [
                                    'conditionLabel' => [
                                        'color' => 'violet',
                                        'text' => ['ELSEIF changesRequested?'],
                                        'width' => 'halfLong',
                                        'align' => 'center',
                                    ],
                                    'actionLabel' => [
                                        'text' => ['Revise draft'],
                                        'width' => 'halfLong',
                                        'align' => 'center',
                                        'badgeColor' => 'amber',
                                    ],
                                ],
                            ]"
                        />
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.if-elseif.continue"
                            attach-to="literature.flow.1.if-elseif.anchorNode-end"
                            before-length="2rem"
                            after-length="2rem"
                            color="zinc"
                            :step-label="[
                                'text' => ['Continue process'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- flow-if-elseif-example-1:end --}}
                </div>
                <x-translation-workbench::ui.common.heading-counter
                    example="flow-if-elseif-example-2"
                    size="sm"
                >{{ __('side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                <div
                    class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                    {{-- flow-if-elseif-example-2:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-flow-if-elseif-right"
                        :dev="true"
                        :coordinates="true"
                        min-width="56rem"
                        min-height="54rem"
                        horizontal-padding="12rem"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.flow-if-elseif
                            id="literature.flow.1.if-elseif-right"
                            :anchor-start="['x' => '0rem', 'y' => '3rem']"
                            side="right"
                            direction="bottom-top"
                            color="cyan"
                            arc-radius="2.75rem"
                            bridge-length="2rem"
                            before-length="2rem"
                            after-length="2rem"
                            elseif-before-length="3rem"
                            elseif-after-length="2rem"
                            stem-length="4rem"
                            :condition-label="[
                                'text' => ['IF reviewApproved?'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                            :if-start="[
                                'color' => 'green',
                                'text' => ['Publish paper'],
                                'width' => 'half',
                                'align' => 'center',
                                'badgeColor' => 'green',
                            ]"
                            :elseifs="[
                                [
                                    'conditionLabel' => [
                                        'color' => 'amber',
                                        'text' => ['ELSEIF changesRequested?'],
                                        'width' => 'halfLong',
                                        'align' => 'center',
                                    ],
                                    'actionLabel' => [
                                        'text' => ['Revise draft'],
                                        'width' => 'halfLong',
                                        'align' => 'center',
                                        'badgeColor' => 'amber',
                                    ],
                                ],
                            ]"
                            :if-end="[
                                'color' => 'red',
                            ]"
                        />
                        <x-translation-workbench::ui.tw-graph.strang.flow-step
                            id="literature.flow.1.if-elseif-right.continue"
                            attach-to="literature.flow.1.if-elseif-right.anchorNode-end"
                            before-length="2rem"
                            after-length="2rem"
                            color="zinc"
                            :step-label="[
                                'text' => ['Continue process'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                        />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- flow-if-elseif-example-2:end --}}
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../flow/if/flow-if-elseif.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
