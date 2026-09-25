<x-translation-workbench::ui.common.heading-counter-group group="primitives-line-jump">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Line jump') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('The primitive draws only a semicircle with round ends. It does not cut another line, create anchors or detect crossings. Parts and segments pass explicit lineJumps to the browser, which positions this same primitive after layout.') }}
            </flux:callout.text>
            <flux:callout.text class="mt-2">
                {{ __('A red crossing line and two short zinc bridges or stems provide context for the colored semicircle. These helpers are placed explicitly; this primitive example does not perform automatic cutting or crossing detection.') }}
            </flux:callout.text>
            @php
                $source = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-line-jump',
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
                            example="top"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >
                            side="top"
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->example('primitive-line-jump-top') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <flux:callout.heading
                class="mt-4"
                size="sm"
            >{{ __('Changed props for the other examples') }}</flux:callout.heading>
            <flux:callout.text class="mt-2">
                {{ __('Only changed line-jump props are listed below. For left/right, the helper lines rotate as well: the crossed line is horizontal and the two adjoining pieces are vertical stems.') }}
            </flux:callout.text>

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
                            example="bottom"
                            variant="accordion"
                        >
                            side="bottom"
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('primitive-line-jump-bottom', 'primitive-line-jump-top', 'x-translation-workbench::ui.tw-graph.primitives.line-jump') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left"
                            variant="accordion"
                        >
                            side="left"
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('primitive-line-jump-left', 'primitive-line-jump-top', 'x-translation-workbench::ui.tw-graph.primitives.line-jump') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right"
                            variant="accordion"
                        >
                            side="right"
                        </x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $source->changedProps('primitive-line-jump-right', 'primitive-line-jump-top', 'x-translation-workbench::ui.tw-graph.primitives.line-jump') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />

            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Line jump props') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns
                            class="bg-white dark:bg-zinc-900"
                            sticky
                        >
                            <flux:table.column>{{ __('Prop') }}</flux:table.column>
                            <flux:table.column>{{ __('Default') }}</flux:table.column>
                            <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>top</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('top / bottom for bridges; left / right for stems.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0.5rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Centerline radius of the semicircle.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchor-x</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Horizontal center of the cut; the baseline passes through this point.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchor-y</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Vertical center of the cut; positive Y points up.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>line-width</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('graph line-width') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Stroke width and diameter of the round ends.') }}
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
                                    {{ __('Drawing color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>tone</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>line</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Line or surface tone, subject to graph path-tone.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>z-index</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>1</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Drawing layer. For configured crossings the browser places it above both lines.') }}
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
            <flux:callout.heading icon="eye">{{ __('Line jump preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div
                    class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2"
                    data-line-jump-examples
                    style="min-width: 50rem;"
                >
                    <div
                        class="min-w-0"
                        data-line-jump-example="top"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="top"
                            size="sm"
                        >
                            side="top"
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- primitive-line-jump-top:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="primitives-line-jump-top"
                                :dev="true"
                                :coordinates="true"
                                min-width="12rem"
                                min-height="14rem"
                                horizontal-padding="5rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line-jump.top.crossing"
                                    direction="bottom-top"
                                    length="4rem"
                                    start-x="0rem"
                                    start-y="4.5rem"
                                    end-x="0rem"
                                    end-y="8.5rem"
                                    color="red"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line-jump.top.bridge-in"
                                    direction="left-right"
                                    length="2.5rem"
                                    start-x="-3.5rem"
                                    start-y="6rem"
                                    end-x="-2rem"
                                    end-y="6rem"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line-jump.top.bridge-out"
                                    direction="left-right"
                                    length="2.5rem"
                                    start-x="1rem"
                                    start-y="6rem"
                                    end-x="3.5rem"
                                    end-y="6rem"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.line-jump
                                    id="literature.primitives.line-jump.top"
                                    side="top"
                                    radius="1rem"
                                    anchor-y="6rem"
                                    color="violet"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- primitive-line-jump-top:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-jump-example="bottom"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="bottom"
                            size="sm"
                        >
                            side="bottom"
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- primitive-line-jump-bottom:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="primitives-line-jump-bottom"
                                :dev="true"
                                :coordinates="true"
                                min-width="12rem"
                                min-height="14rem"
                                horizontal-padding="5rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line-jump.bottom.crossing"
                                    direction="bottom-top"
                                    length="4.5rem"
                                    start-x="0rem"
                                    start-y="3.5rem"
                                    end-x="0rem"
                                    end-y="7.5rem"
                                    color="red"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line-jump.bottom.bridge-in"
                                    direction="left-right"
                                    length="3rem"
                                    start-x="-3.5rem"
                                    start-y="6rem"
                                    end-x="-2rem"
                                    end-y="6rem"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line-jump.bottom.bridge-out"
                                    direction="left-right"
                                    length="3rem"
                                    start-x="0.5rem"
                                    start-y="6rem"
                                    end-x="3.5rem"
                                    end-y="6rem"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.line-jump
                                    id="literature.primitives.line-jump.bottom"
                                    side="bottom"
                                    radius="0.5rem"
                                    anchor-y="6rem"
                                    color="blue"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- primitive-line-jump-bottom:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-jump-example="left"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="left"
                            size="sm"
                        >
                            side="left"
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- primitive-line-jump-left:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="primitives-line-jump-left"
                                :dev="true"
                                :coordinates="true"
                                min-width="12rem"
                                min-height="14rem"
                                horizontal-padding="5rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line-jump.left.crossing"
                                    direction="left-right"
                                    length="5rem"
                                    start-x="-3.5rem"
                                    start-y="6rem"
                                    end-x="1.5rem"
                                    end-y="6rem"
                                    color="red"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line-jump.left.stem-in"
                                    direction="bottom-top"
                                    length="1.5rem"
                                    start-x="0rem"
                                    start-y="2.5rem"
                                    end-x="0rem"
                                    end-y="4rem"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line-jump.left.stem-out"
                                    direction="bottom-top"
                                    length="1.5rem"
                                    start-x="0rem"
                                    start-y="8rem"
                                    end-x="0rem"
                                    end-y="9.5rem"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.line-jump
                                    id="literature.primitives.line-jump.left"
                                    side="left"
                                    radius="2rem"
                                    anchor-y="6rem"
                                    color="amber"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- primitive-line-jump-left:end --}}
                        </div>
                    </div>
                    <div
                        class="min-w-0"
                        data-line-jump-example="right"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="right"
                            size="sm"
                        >
                            side="right"
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-4 rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- primitive-line-jump-right:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="primitives-line-jump-right"
                                :dev="true"
                                :coordinates="true"
                                min-width="12rem"
                                min-height="14rem"
                                horizontal-padding="5rem"
                            >
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line-jump.right.crossing"
                                    direction="left-right"
                                    length="5rem"
                                    start-x="-1.5rem"
                                    start-y="6rem"
                                    end-x="1.5rem"
                                    end-y="6rem"
                                    color="red"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line-jump.right.stem-in"
                                    direction="bottom-top"
                                    length="1.5rem"
                                    start-x="0rem"
                                    start-y="2.5rem"
                                    end-x="0rem"
                                    end-y="4rem"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.line
                                    id="literature.primitives.line-jump.right.stem-out"
                                    direction="bottom-top"
                                    length="1.5rem"
                                    start-x="0rem"
                                    start-y="8rem"
                                    end-x="0rem"
                                    end-y="9.5rem"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.primitives.line-jump
                                    id="literature.primitives.line-jump.right"
                                    side="right"
                                    radius="2rem"
                                    anchor-y="6rem"
                                    color="green"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- primitive-line-jump-right:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/primitives/primitives-line-jump.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
