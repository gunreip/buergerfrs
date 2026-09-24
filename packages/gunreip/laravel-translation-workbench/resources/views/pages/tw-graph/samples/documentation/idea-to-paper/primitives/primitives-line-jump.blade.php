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
            {{ __('Zinc lines provide context: one line is crossed, and two short bridges or stems meet the colored semicircle. These helpers are placed explicitly; this primitive example does not perform automatic cutting or crossing detection.') }}
        </flux:callout.text>
        @php
            $source = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-line-jump',
            );
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Complete example: top') }}</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $source->example('primitive-line-jump-top') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Changed props for the other examples') }}</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">side="bottom" color="blue"
            side="left" color="amber"
            side="right" color="green"</x-translation-workbench::ui.tw-graph.code-box>
        <flux:text class="mt-2">
            {{ __('Only changed line-jump props are listed above. For left/right, the helper lines rotate as well: the crossed line is horizontal and the two adjoining pieces are vertical stems.') }}
        </flux:text>
        <flux:table
            class="mt-3"
            container:class="max-h-80"
        >
            <flux:table.columns>
                <flux:table.column>Prop</flux:table.column>
                <flux:table.column>Default</flux:table.column>
                <flux:table.column>Purpose</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell>side</flux:table.cell>
                    <flux:table.cell>top</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">top / bottom for bridges; left / right for stems.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>radius</flux:table.cell>
                    <flux:table.cell>0.5rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Centerline radius of the semicircle.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>anchor-x</flux:table.cell>
                    <flux:table.cell>0rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Horizontal center of the cut; the baseline passes through
                        this point.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>anchor-y</flux:table.cell>
                    <flux:table.cell>0rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Vertical center of the cut; positive Y points up.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>line-width</flux:table.cell>
                    <flux:table.cell>graph line-width</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Stroke width and diameter of the round ends.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>color</flux:table.cell>
                    <flux:table.cell>zinc</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Drawing color.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>tone</flux:table.cell>
                    <flux:table.cell>line</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Line or surface tone, subject to graph path-tone.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>z-index</flux:table.cell>
                    <flux:table.cell>1</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Drawing layer. For configured crossings the browser
                        places it above both lines.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout
        class="min-w-0"
        color="zinc"
        icon="eye"
    >
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div class="mt-3 grid grid-cols-2 gap-3">
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-line-jump-example="top"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >side="top"</flux:heading>
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
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-line-jump-example="bottom"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >side="bottom"</flux:heading>
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
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-line-jump-example="left"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >side="left"</flux:heading>
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
                <div
                    class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    data-line-jump-example="right"
                >
                    <flux:heading
                        class="m-3"
                        size="sm"
                    >side="right"</flux:heading>
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
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../primitives/primitives-line-jump.blade.php</flux:field>
    </flux:callout>
</section>
