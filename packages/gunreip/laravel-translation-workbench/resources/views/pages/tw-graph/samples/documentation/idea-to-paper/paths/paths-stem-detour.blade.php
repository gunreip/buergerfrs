<x-translation-workbench::ui.common.heading-counter-group group="paths-stem-detour">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout class="min-w-0" color="indigo" icon="file-text">
            <flux:callout.heading>{{ __('Stem detour path') }}</flux:callout.heading>
            <flux:callout.text>{{ __('An explicit detour around a reserved area, composed from parts.sideways and parts.start. Both turns cancel the horizontal offset: the endpoint remains on the original vertical axis. The dashed zinc line marks the direct route for comparison; it is a helper, not another flow connection. Four individually authored examples cover both sides and both directions.') }}</flux:callout.text>
            @php
                $detourSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-stem-detour',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="stem-detour-left-up-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left" · direction="bottom-top"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $detourSource->example('stem-detour-left-up-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="stem-detour-right-up-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right" · direction="bottom-top"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $detourSource->example('stem-detour-right-up-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="stem-detour-left-down-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="left" · direction="top-bottom"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $detourSource->example('stem-detour-left-down-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="stem-detour-right-down-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('side="right" · direction="top-bottom"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $detourSource->example('stem-detour-right-down-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator class="mt-4" :text="__('Props used in these examples')" />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Props and connections') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns class="bg-white dark:bg-zinc-900" sticky>
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
                                    <code>required</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Root identifier; outward, stem, inward and after identify the route sections.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchor-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>required</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Starting coordinates. Both examples pointing up start at (0rem, 5rem); both pointing down start at (0rem, 25rem).') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>required</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Total vertical displacement, not the sum of all route lengths. Explicitly 20rem here.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>right</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Physical side of the detour: left or right, independent of direction.') }}
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
                                    {{ __('bottom-top increases graph Y; top-bottom decreases it.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Horizontal bridge in each turn; 4rem in these examples.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>arc-radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>2rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Radius of all four arcs; 2rem here.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>before-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Straight section before the first turn; explicitly 2rem here.') }}
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
                                    {{ __('Straight section after returning to the original axis; 2rem here.') }}
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
                                    {{ __('Color of the detour and its default labels.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>node-label-left</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Endpoint annotation used by right-side detours.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>node-label-right</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Endpoint annotation used by left-side detours.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchorNode-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>generated</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Published start coordinates under the root ID.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchorNode-end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>generated</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Published endpoint on the original vertical axis, including devCounterNext.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
        </flux:callout>

        {{-- Preview --}}
        <flux:callout class="min-w-0" color="emerald">
            <flux:callout.heading icon="eye">{{ __('Preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2">
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="stem-detour-left-up-example"
                            size="sm"
                        >{{ __('side="left" · direction="bottom-top"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- stem-detour-left-up-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-stem-detour-left-up"
                                :dev="true" :coordinates="true"
                                min-height="32rem" min-width="34rem" horizontal-padding="12rem"
                            >
                                {{-- Dashed helper: original vertical route, not a second flow connection. --}}
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.paths.stem-detour.left-up.reference"
                                    :anchor-start="['x' => '0rem', 'y' => '5rem']"
                                    :anchor-end="['x' => '0rem', 'y' => '25rem']"
                                    direction="bottom-top" length="20rem"
                                    color="zinc" :dashed="true" :z-index="5"
                                />
                                <x-translation-workbench::ui.tw-graph.paths.stem-detour
                                    id="literature.paths.stem-detour.left-up"
                                    :anchor-start="['x' => '0rem', 'y' => '5rem']"
                                    side="left" direction="bottom-top"
                                    length="20rem" before-length="2rem" after-length="2rem"
                                    bridge-length="4rem" arc-radius="2rem"
                                    color="cyan"
                                    :node-label-right="[
                                        'text' => ['Same axis', 'x = 0rem'],
                                        'width' => 'half',
                                        'align' => 'center',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- stem-detour-left-up-example:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="stem-detour-right-up-example"
                            size="sm"
                        >{{ __('side="right" · direction="bottom-top"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- stem-detour-right-up-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-stem-detour-right-up"
                                :dev="true" :coordinates="true"
                                min-height="32rem" min-width="34rem" horizontal-padding="12rem"
                            >
                                {{-- Dashed helper: original vertical route, not a second flow connection. --}}
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.paths.stem-detour.right-up.reference"
                                    :anchor-start="['x' => '0rem', 'y' => '5rem']"
                                    :anchor-end="['x' => '0rem', 'y' => '25rem']"
                                    direction="bottom-top" length="20rem"
                                    color="zinc" :dashed="true" :z-index="5"
                                />
                                <x-translation-workbench::ui.tw-graph.paths.stem-detour
                                    id="literature.paths.stem-detour.right-up"
                                    :anchor-start="['x' => '0rem', 'y' => '5rem']"
                                    side="right" direction="bottom-top"
                                    length="20rem" before-length="2rem" after-length="2rem"
                                    bridge-length="4rem" arc-radius="2rem"
                                    color="amber"
                                    :node-label-left="[
                                        'text' => ['Same axis', 'x = 0rem'],
                                        'width' => 'half',
                                        'align' => 'center',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- stem-detour-right-up-example:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="stem-detour-left-down-example"
                            size="sm"
                        >{{ __('side="left" · direction="top-bottom"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- stem-detour-left-down-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-stem-detour-left-down"
                                :dev="true" :coordinates="true"
                                min-height="32rem" min-width="34rem" horizontal-padding="12rem"
                            >
                                {{-- Dashed helper: original vertical route, not a second flow connection. --}}
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.paths.stem-detour.left-down.reference"
                                    :anchor-start="['x' => '0rem', 'y' => '25rem']"
                                    :anchor-end="['x' => '0rem', 'y' => '5rem']"
                                    direction="top-bottom" length="20rem"
                                    color="zinc" :dashed="true" :z-index="5"
                                />
                                <x-translation-workbench::ui.tw-graph.paths.stem-detour
                                    id="literature.paths.stem-detour.left-down"
                                    :anchor-start="['x' => '0rem', 'y' => '25rem']"
                                    side="left" direction="top-bottom"
                                    length="20rem" before-length="2rem" after-length="2rem"
                                    bridge-length="4rem" arc-radius="2rem"
                                    color="violet"
                                    :node-label-right="[
                                        'text' => ['Same axis', 'x = 0rem'],
                                        'width' => 'half',
                                        'align' => 'center',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- stem-detour-left-down-example:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="stem-detour-right-down-example"
                            size="sm"
                        >{{ __('side="right" · direction="top-bottom"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- stem-detour-right-down-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-stem-detour-right-down"
                                :dev="true" :coordinates="true"
                                min-height="32rem" min-width="34rem" horizontal-padding="12rem"
                            >
                                {{-- Dashed helper: original vertical route, not a second flow connection. --}}
                                <x-translation-workbench::ui.tw-graph.segments.path
                                    id="literature.paths.stem-detour.right-down.reference"
                                    :anchor-start="['x' => '0rem', 'y' => '25rem']"
                                    :anchor-end="['x' => '0rem', 'y' => '5rem']"
                                    direction="top-bottom" length="20rem"
                                    color="zinc" :dashed="true" :z-index="5"
                                />
                                <x-translation-workbench::ui.tw-graph.paths.stem-detour
                                    id="literature.paths.stem-detour.right-down"
                                    :anchor-start="['x' => '0rem', 'y' => '25rem']"
                                    side="right" direction="top-bottom"
                                    length="20rem" before-length="2rem" after-length="2rem"
                                    bridge-length="4rem" arc-radius="2rem"
                                    color="emerald"
                                    :node-label-left="[
                                        'text' => ['Same axis', 'x = 0rem'],
                                        'width' => 'half',
                                        'align' => 'center',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- stem-detour-right-down-example:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/paths/paths-stem-detour.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
