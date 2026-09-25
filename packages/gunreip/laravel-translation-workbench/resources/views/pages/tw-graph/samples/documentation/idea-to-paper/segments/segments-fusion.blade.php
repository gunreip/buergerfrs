<x-translation-workbench::ui.common.heading-counter-group group="segments-fusion">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">

        {{-- CodeBox and props table --}}
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('segments.fusion') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('One connection between parallel horizontal lanes at different heights. Two opposed arcs and a compensating stem perform the change of level. A straight input bridge supplies any remaining horizontal distance; no bridge follows the last arc. Joint arrows mark the transitions.') }}
            </flux:callout.text>
            <flux:callout.text class="mt-3">
                {{ __('The configured arc-radius is a starting value. Closely spaced inputs can reduce it.
                                                                                                        When a positive compensating stem would be shorter than min-stem-length, the radius increases
                                                                                                        to half the input-to-output height difference and the stem disappears. The arcs then share one joint.
                                                                                                        Longer stems retain the configured radius. A segment with fixed endpoints must provide enough horizontal
                                                                                                        space;
                                                                                                        parts.fusion calculates the required output position automatically.
                                                                                                        In SWITCH/CASE the starting fusion radius is half the switch arc-radius, and entryStemLength must be at
                                                                                                        least 3rem.
                                                                                                        Two symmetric inputs 3rem apart have a 1.5rem offset to their shared output and use 0.75rem bends.') }}
            </flux:callout.text>
            @php
                $exampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-fusion',
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
                            example="right-up"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Right / upward') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $exampleSource->example('segment-fusion-right-up') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-down"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Right / downward') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $exampleSource->example('segment-fusion-right-down') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-up"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Left / upward') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $exampleSource->example('segment-fusion-left-up') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-down"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Left / downward') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $exampleSource->example('segment-fusion-left-down') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="close"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Close offset: radius limited to 0.5rem') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $exampleSource->example('segment-fusion-close') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="wide"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Large offset: radius 1.375rem, longer stem') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $exampleSource->example('segment-fusion-wide') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                    <code>segment.fusion</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier of this connection.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchor-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('required') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Input coordinates x/y as rem or resolvable calc expressions.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchor-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('required') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Output coordinates; must lie ahead in the selected direction.') }}
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
                                    {{ __('Minimum positive compensator length. Zero-length stems remain allowed.') }}
                                    {{ __('Short positive residual stems enlarge the arcs and become zero. Set 0rem to disable the minimum.') }}
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
                                    <code>zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Color of the arcs, compensating stem and arrows.') }}
                                </flux:table.cell>
                            </flux:table.row>

                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
        </flux:callout>

        {{-- Preview --}}
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
                            example="right-up"
                            size="sm"
                        >{{ __('Right / upward') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-fusion-right-up:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="segments-fusion-right-up"
                                min-width="18rem"
                                min-height="16rem"
                                :dev="true"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.fusion
                                    id="literature.segments.fusion.right-up"
                                    direction="left-right"
                                    :anchor-start="['x' => '2rem', 'y' => '2rem']"
                                    :anchor-end="['x' => '8rem', 'y' => '6rem']"
                                    arc-radius="1.375rem"
                                    color="amber"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-fusion-right-up:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="right-down"
                            size="sm"
                        >{{ __('Right / downward') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-fusion-right-down:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="segments-fusion-right-down"
                                min-width="18rem"
                                min-height="16rem"
                                :dev="true"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.fusion
                                    id="literature.segments.fusion.right-down"
                                    direction="left-right"
                                    :anchor-start="['x' => '2rem', 'y' => '6rem']"
                                    :anchor-end="['x' => '8rem', 'y' => '2rem']"
                                    arc-radius="1.375rem"
                                    color="orange"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-fusion-right-down:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-up"
                            size="sm"
                        >{{ __('Left / upward') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-fusion-left-up:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="segments-fusion-left-up"
                                min-width="18rem"
                                min-height="16rem"
                                :dev="true"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.fusion
                                    id="literature.segments.fusion.left-up"
                                    direction="right-left"
                                    :anchor-start="['x' => '8rem', 'y' => '2rem']"
                                    :anchor-end="['x' => '2rem', 'y' => '6rem']"
                                    arc-radius="1.375rem"
                                    color="cyan"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-fusion-left-up:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="left-down"
                            size="sm"
                        >{{ __('Left / downward') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-fusion-left-down:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="segments-fusion-left-down"
                                min-width="18rem"
                                min-height="16rem"
                                :dev="true"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.fusion
                                    id="literature.segments.fusion.left-down"
                                    direction="right-left"
                                    :anchor-start="['x' => '8rem', 'y' => '6rem']"
                                    :anchor-end="['x' => '2rem', 'y' => '2rem']"
                                    arc-radius="1.375rem"
                                    color="green"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-fusion-left-down:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="close"
                            size="sm"
                        >{{ __('Close offset: radius limited to 0.5rem') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-fusion-close:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="segments-fusion-close"
                                min-width="18rem"
                                min-height="16rem"
                                :dev="true"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.fusion
                                    id="literature.segments.fusion.close"
                                    direction="left-right"
                                    :anchor-start="['x' => '2rem', 'y' => '2rem']"
                                    :anchor-end="['x' => '8rem', 'y' => '3rem']"
                                    arc-radius="1.375rem"
                                    color="violet"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-fusion-close:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="wide"
                            size="sm"
                        >{{ __('Large offset: radius 1.375rem, longer stem') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-fusion-wide:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="segments-fusion-wide"
                                min-width="18rem"
                                min-height="16rem"
                                :dev="true"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.fusion
                                    id="literature.segments.fusion.wide"
                                    direction="left-right"
                                    :anchor-start="['x' => '2rem', 'y' => '2rem']"
                                    :anchor-end="['x' => '8rem', 'y' => '10rem']"
                                    arc-radius="1.375rem"
                                    color="rose"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-fusion-wide:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/segments/segments-fusion.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
