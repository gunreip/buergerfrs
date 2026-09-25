<x-translation-workbench::ui.common.heading-counter-group group="parts-fusion">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>parts.fusion</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.documentation-links example="parts.parts-fusion" />
            <flux:callout.text>
                {{ __('Two or more independent inputs converge on one shared output dot. All entry arcs share one radius. Outer inputs join the next inner arc end through a plain stem without an extra dot or arrow. Only the innermost input on each side continues through segments.fusion to the output. Inputs at the output height continue straight. The labeled straight input lines are helper segments. The examples show two, three and four inputs in both horizontal directions, with individual colors and unequal spacing.') }}
            </flux:callout.text>
            <flux:callout.text class="mt-3">
                {{ __('The configured arc-radius is a starting value. Closely spaced inputs can reduce it. The nearest off-center input determines the shared radius for all lanes. When a positive compensating stem would be shorter than min-stem-length, the radius increases to half the input-to-output height difference and the stem disappears. The arcs then share one joint. Longer stems retain the configured radius. A segment with fixed endpoints must provide enough horizontal space; parts.fusion calculates the required output position automatically. In SWITCH/CASE the starting fusion radius is half the switch arc-radius, and entryStemLength must be at least 3rem. Two symmetric inputs 3rem apart have a 1.5rem offset to their shared output and use 0.75rem bends.') }}
            </flux:callout.text>
            @php
                $exampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.parts.parts-fusion',
                );
            @endphp
            <flux:separator
                class="mt-4"
                :text="__('Code examples')"
            />
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
                            example="left-2"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('2 inputs / left') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $exampleSource->example('part-fusion-left-2') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-3"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('3 inputs / left') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $exampleSource->example('part-fusion-left-3') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-4"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('4 inputs / left / unequal spacing') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $exampleSource->example('part-fusion-left-4') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-2"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('2 inputs / right') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $exampleSource->example('part-fusion-right-2') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-3"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('3 inputs / right') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $exampleSource->example('part-fusion-right-3') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-4"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('4 inputs / right / unequal spacing') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $exampleSource->example('part-fusion-right-4') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Props and anchors') }}</flux:callout.heading>
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
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>part.fusion</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier of the fusion and its inputs.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inputs</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('[]; at least two') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Explicit entries with unique key, anchor (x/y), and optional color. Input array order does not determine the output height.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>right-left</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Horizontal flow: left-right or right-left.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>arc-radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>1.375rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Starting radius; may grow to eliminate a short compensator, or shrink for closely spaced inputs.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>min-stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>1rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Minimum positive compensator length. Zero-length stems remain allowed. Short positive residual stems enlarge the arcs and become zero. Set 0rem to disable the minimum.') }}
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
                                    {{ __('Drawing layer.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('inherited / zinc') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Shared output color; fallback color for inputs.') }}
                                </flux:table.cell>
                            </flux:table.row>

                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>dev-counter-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>1</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('DEV counter at the shared output dot.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>.inputs.&lt;key&gt;</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('registered input') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Anchor of each individually identified input.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>.anchorNode-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('calculated') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Shared output, halfway between the lowest and highest inputs; can connect to a following component.') }}
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
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2">
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-2"
                            size="sm"
                        >{{ __('2 inputs / left') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- part-fusion-left-2:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="parts-fusion-left-2"
                                min-width="20rem"
                                min-height="20rem"
                                horizontal-padding="3rem"
                                :dev="true"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.left-2.helper.a"
                                    direction="right-left"
                                    length="3rem"
                                    color="amber"
                                    :anchor-start="['x' => '13rem', 'y' => '2rem']"
                                    :anchor-end="['x' => '10rem', 'y' => '2rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.left-2.helper.a.label"
                                    side="right"
                                    color="amber"
                                    anchor-x="13rem"
                                    anchor-y="2rem"
                                    :label="['text' => ['Input A'], 'width' => 'half']"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.left-2.helper.b"
                                    direction="right-left"
                                    length="3rem"
                                    color="orange"
                                    :anchor-start="['x' => '13rem', 'y' => '4rem']"
                                    :anchor-end="['x' => '10rem', 'y' => '4rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.left-2.helper.b.label"
                                    side="right"
                                    color="orange"
                                    anchor-x="13rem"
                                    anchor-y="4rem"
                                    :label="['text' => ['Input B'], 'width' => 'half']"
                                />
                                {{-- Fusion 2 Inputs / Left --}}
                                <x-translation-workbench::ui.tw-graph.parts.fusion
                                    id="literature.parts.fusion.left-2"
                                    direction="right-left"
                                    arc-radius="1.375rem"
                                    color="green"
                                    :inputs="[
                                        ['key' => 'a', 'anchor' => ['x' => '10rem', 'y' => '2rem'], 'color' => 'amber'],
                                        [
                                            'key' => 'b',
                                            'anchor' => ['x' => '10rem', 'y' => '4rem'],
                                            'color' => 'orange',
                                        ],
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- part-fusion-left-2:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-3"
                            size="sm"
                        >{{ __('3 inputs / left') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- part-fusion-left-3:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="parts-fusion-left-3"
                                min-width="20rem"
                                min-height="20rem"
                                horizontal-padding="3rem"
                                :dev="true"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.left-3.helper.a"
                                    direction="right-left"
                                    length="3rem"
                                    color="amber"
                                    :anchor-start="['x' => '13rem', 'y' => '2rem']"
                                    :anchor-end="['x' => '10rem', 'y' => '2rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.left-3.helper.a.label"
                                    side="right"
                                    color="amber"
                                    anchor-x="13rem"
                                    anchor-y="2rem"
                                    :label="['text' => ['Input A'], 'width' => 'half']"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.left-3.helper.b"
                                    direction="right-left"
                                    length="3rem"
                                    color="orange"
                                    :anchor-start="['x' => '13rem', 'y' => '6rem']"
                                    :anchor-end="['x' => '10rem', 'y' => '6rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.left-3.helper.b.label"
                                    side="right"
                                    color="orange"
                                    anchor-x="13rem"
                                    anchor-y="6rem"
                                    :label="['text' => ['Input B'], 'width' => 'half']"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.left-3.helper.c"
                                    direction="right-left"
                                    length="3rem"
                                    color="cyan"
                                    :anchor-start="['x' => '13rem', 'y' => '10rem']"
                                    :anchor-end="['x' => '10rem', 'y' => '10rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.left-3.helper.c.label"
                                    side="right"
                                    color="cyan"
                                    anchor-x="13rem"
                                    anchor-y="10rem"
                                    :label="['text' => ['Input C'], 'width' => 'half']"
                                />
                                {{-- Fusion 3 Inputs / Left --}}
                                <x-translation-workbench::ui.tw-graph.parts.fusion
                                    id="literature.parts.fusion.left-3"
                                    direction="right-left"
                                    arc-radius="1.375rem"
                                    color="green"
                                    :inputs="[
                                        ['key' => 'a', 'anchor' => ['x' => '10rem', 'y' => '2rem'], 'color' => 'amber'],
                                        [
                                            'key' => 'b',
                                            'anchor' => ['x' => '10rem', 'y' => '6rem'],
                                            'color' => 'orange',
                                        ],
                                        ['key' => 'c', 'anchor' => ['x' => '10rem', 'y' => '10rem'], 'color' => 'cyan'],
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- part-fusion-left-3:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-4"
                            size="sm"
                        >{{ __('4 inputs / left / unequal spacing') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- part-fusion-left-4:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="parts-fusion-left-4"
                                min-width="20rem"
                                min-height="20rem"
                                horizontal-padding="3rem"
                                :dev="true"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.left-4.helper.a"
                                    direction="right-left"
                                    length="3rem"
                                    color="amber"
                                    :anchor-start="['x' => '13rem', 'y' => '2rem']"
                                    :anchor-end="['x' => '10rem', 'y' => '2rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.left-4.helper.a.label"
                                    side="right"
                                    color="amber"
                                    anchor-x="13rem"
                                    anchor-y="2rem"
                                    :label="['text' => ['Input A'], 'width' => 'half']"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.left-4.helper.b"
                                    direction="right-left"
                                    length="3rem"
                                    color="orange"
                                    :anchor-start="['x' => '13rem', 'y' => '4rem']"
                                    :anchor-end="['x' => '10rem', 'y' => '4rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.left-4.helper.b.label"
                                    side="right"
                                    color="orange"
                                    anchor-x="13rem"
                                    anchor-y="4rem"
                                    :label="['text' => ['Input B'], 'width' => 'half']"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.left-4.helper.c"
                                    direction="right-left"
                                    length="3rem"
                                    color="cyan"
                                    :anchor-start="['x' => '13rem', 'y' => '8rem']"
                                    :anchor-end="['x' => '10rem', 'y' => '8rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.left-4.helper.c.label"
                                    side="right"
                                    color="cyan"
                                    anchor-x="13rem"
                                    anchor-y="8rem"
                                    :label="['text' => ['Input C'], 'width' => 'half']"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.left-4.helper.d"
                                    direction="right-left"
                                    length="3rem"
                                    color="violet"
                                    :anchor-start="['x' => '13rem', 'y' => '14rem']"
                                    :anchor-end="['x' => '10rem', 'y' => '14rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.left-4.helper.d.label"
                                    side="right"
                                    color="violet"
                                    anchor-x="13rem"
                                    anchor-y="14rem"
                                    :label="['text' => ['Input D'], 'width' => 'half']"
                                />
                                {{-- Fusion 4 Inputs / Left --}}
                                <x-translation-workbench::ui.tw-graph.parts.fusion
                                    id="literature.parts.fusion.left-4"
                                    direction="right-left"
                                    arc-radius="1.375rem"
                                    color="green"
                                    :inputs="[
                                        ['key' => 'a', 'anchor' => ['x' => '10rem', 'y' => '2rem'], 'color' => 'amber'],
                                        [
                                            'key' => 'b',
                                            'anchor' => ['x' => '10rem', 'y' => '4rem'],
                                            'color' => 'orange',
                                        ],
                                        ['key' => 'c', 'anchor' => ['x' => '10rem', 'y' => '8rem'], 'color' => 'cyan'],
                                        [
                                            'key' => 'd',
                                            'anchor' => ['x' => '10rem', 'y' => '14rem'],
                                            'color' => 'violet',
                                        ],
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- part-fusion-left-4:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-2"
                            size="sm"
                        >{{ __('2 inputs / right') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- part-fusion-right-2:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="parts-fusion-right-2"
                                min-width="25rem"
                                min-height="20rem"
                                horizontal-padding="3rem"
                                :dev="true"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.right-2.helper.a"
                                    direction="left-right"
                                    length="3rem"
                                    color="amber"
                                    :anchor-start="['x' => '-1rem', 'y' => '2rem']"
                                    :anchor-end="['x' => '2rem', 'y' => '2rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.right-2.helper.a.label"
                                    side="left"
                                    color="amber"
                                    anchor-x="-1rem"
                                    anchor-y="2rem"
                                    :label="['text' => ['Input A'], 'width' => 'half']"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.right-2.helper.b"
                                    direction="left-right"
                                    length="3rem"
                                    color="orange"
                                    :anchor-start="['x' => '-1rem', 'y' => '4rem']"
                                    :anchor-end="['x' => '2rem', 'y' => '4rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.right-2.helper.b.label"
                                    side="left"
                                    color="orange"
                                    anchor-x="-1rem"
                                    anchor-y="4rem"
                                    :label="['text' => ['Input B'], 'width' => 'half']"
                                />
                                {{-- Fusion 2 Inputs / Right --}}
                                <x-translation-workbench::ui.tw-graph.parts.fusion
                                    id="literature.parts.fusion.right-2"
                                    direction="left-right"
                                    arc-radius="1.375rem"
                                    color="green"
                                    :inputs="[
                                        ['key' => 'a', 'anchor' => ['x' => '2rem', 'y' => '2rem'], 'color' => 'amber'],
                                        ['key' => 'b', 'anchor' => ['x' => '2rem', 'y' => '4rem'], 'color' => 'orange'],
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- part-fusion-right-2:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-3"
                            size="sm"
                        >{{ __('3 inputs / right') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- part-fusion-right-3:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="parts-fusion-right-3"
                                min-width="25rem"
                                min-height="20rem"
                                horizontal-padding="3rem"
                                :dev="true"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.right-3.helper.a"
                                    direction="left-right"
                                    length="3rem"
                                    color="amber"
                                    :anchor-start="['x' => '-1rem', 'y' => '2rem']"
                                    :anchor-end="['x' => '2rem', 'y' => '2rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.right-3.helper.a.label"
                                    side="left"
                                    color="amber"
                                    anchor-x="-1rem"
                                    anchor-y="2rem"
                                    :label="['text' => ['Input A'], 'width' => 'half']"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.right-3.helper.b"
                                    direction="left-right"
                                    length="3rem"
                                    color="orange"
                                    :anchor-start="['x' => '-1rem', 'y' => '6rem']"
                                    :anchor-end="['x' => '2rem', 'y' => '6rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.right-3.helper.b.label"
                                    side="left"
                                    color="orange"
                                    anchor-x="-1rem"
                                    anchor-y="6rem"
                                    :label="['text' => ['Input B'], 'width' => 'half']"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.right-3.helper.c"
                                    direction="left-right"
                                    length="3rem"
                                    color="cyan"
                                    :anchor-start="['x' => '-1rem', 'y' => '10rem']"
                                    :anchor-end="['x' => '2rem', 'y' => '10rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.right-3.helper.c.label"
                                    side="left"
                                    color="cyan"
                                    anchor-x="-1rem"
                                    anchor-y="10rem"
                                    :label="['text' => ['Input C'], 'width' => 'half']"
                                />
                                {{-- Fusion 3 Inputs / Right --}}
                                <x-translation-workbench::ui.tw-graph.parts.fusion
                                    id="literature.parts.fusion.right-3"
                                    direction="left-right"
                                    arc-radius="1.375rem"
                                    color="green"
                                    :inputs="[
                                        ['key' => 'a', 'anchor' => ['x' => '2rem', 'y' => '2rem'], 'color' => 'amber'],
                                        ['key' => 'b', 'anchor' => ['x' => '2rem', 'y' => '6rem'], 'color' => 'orange'],
                                        ['key' => 'c', 'anchor' => ['x' => '2rem', 'y' => '10rem'], 'color' => 'cyan'],
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- part-fusion-right-3:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-4"
                            size="sm"
                        >{{ __('4 inputs / right / unequal spacing') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- part-fusion-right-4:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="parts-fusion-right-4"
                                min-width="25rem"
                                min-height="20rem"
                                horizontal-padding="3rem"
                                :dev="true"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.right-4.helper.a"
                                    direction="left-right"
                                    length="3rem"
                                    color="amber"
                                    :anchor-start="['x' => '-1rem', 'y' => '2rem']"
                                    :anchor-end="['x' => '2rem', 'y' => '2rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.right-4.helper.a.label"
                                    side="left"
                                    color="amber"
                                    anchor-x="-1rem"
                                    anchor-y="2rem"
                                    :label="['text' => ['Input A'], 'width' => 'half']"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.right-4.helper.b"
                                    direction="left-right"
                                    length="3rem"
                                    color="orange"
                                    :anchor-start="['x' => '-1rem', 'y' => '4rem']"
                                    :anchor-end="['x' => '2rem', 'y' => '4rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.right-4.helper.b.label"
                                    side="left"
                                    color="orange"
                                    anchor-x="-1rem"
                                    anchor-y="4rem"
                                    :label="['text' => ['Input B'], 'width' => 'half']"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.right-4.helper.c"
                                    direction="left-right"
                                    length="3rem"
                                    color="cyan"
                                    :anchor-start="['x' => '-1rem', 'y' => '8rem']"
                                    :anchor-end="['x' => '2rem', 'y' => '8rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.right-4.helper.c.label"
                                    side="left"
                                    color="cyan"
                                    anchor-x="-1rem"
                                    anchor-y="8rem"
                                    :label="['text' => ['Input C'], 'width' => 'half']"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.parts.fusion.right-4.helper.d"
                                    direction="left-right"
                                    length="3rem"
                                    color="violet"
                                    :anchor-start="['x' => '-1rem', 'y' => '14rem']"
                                    :anchor-end="['x' => '2rem', 'y' => '14rem']"
                                    :node-start="true"
                                />
                                <x-translation-workbench::ui.tw-graph.segments.label
                                    id="literature.parts.fusion.right-4.helper.d.label"
                                    side="left"
                                    color="violet"
                                    anchor-x="-1rem"
                                    anchor-y="14rem"
                                    :label="['text' => ['Input D'], 'width' => 'half']"
                                />
                                {{-- Fusion 4 Inputs / Right --}}
                                <x-translation-workbench::ui.tw-graph.parts.fusion
                                    id="literature.parts.fusion.right-4"
                                    direction="left-right"
                                    arc-radius="1.375rem"
                                    color="green"
                                    :inputs="[
                                        ['key' => 'a', 'anchor' => ['x' => '2rem', 'y' => '2rem'], 'color' => 'amber'],
                                        ['key' => 'b', 'anchor' => ['x' => '2rem', 'y' => '4rem'], 'color' => 'orange'],
                                        ['key' => 'c', 'anchor' => ['x' => '2rem', 'y' => '8rem'], 'color' => 'cyan'],
                                        [
                                            'key' => 'd',
                                            'anchor' => ['x' => '2rem', 'y' => '14rem'],
                                            'color' => 'violet',
                                        ],
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- part-fusion-right-4:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/parts/parts-fusion.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
