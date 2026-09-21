<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout class="min-w-0" color="indigo" icon="file-text">
        <flux:callout.heading>{{ __('Stem detour path') }}</flux:callout.heading>
        <flux:callout.text>{{ __('An explicit detour around a reserved area, composed from parts.sideways and parts.start. Both turns cancel the horizontal offset: the endpoint remains on the original vertical axis. The dashed zinc line marks the direct route for comparison; it is a helper, not another flow connection. Four individually authored examples cover both sides and both directions.') }}</flux:callout.text>
        @php
            $detourSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-stem-detour',
            );
        @endphp
        <flux:heading class="mt-4" size="sm">side="left" · direction="bottom-top"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $detourSource->example('stem-detour-left-up-example') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">side="right" · direction="bottom-top"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $detourSource->example('stem-detour-right-up-example') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">side="left" · direction="top-bottom"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $detourSource->example('stem-detour-left-down-example') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">side="right" · direction="top-bottom"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $detourSource->example('stem-detour-right-down-example') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">{{ __('Props and connections') }}</flux:heading>
        <flux:table class="mt-3" container:class="max-h-80">
            <flux:table.columns sticky>
                <flux:table.column>Prop / anchor</flux:table.column>
                <flux:table.column>Default</flux:table.column>
                <flux:table.column>Purpose</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">id</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">required</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Root identifier; outward, stem, inward and after identify the route sections.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">anchor-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">required</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Starting coordinates. Both examples pointing up start at (0rem, 5rem); both pointing down start at (0rem, 25rem).</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">required</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Total vertical displacement, not the sum of all route lengths. Explicitly 20rem here.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">side</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">right</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Physical side of the detour: left or right, independent of direction.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">direction</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">bottom-top</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">bottom-top increases graph Y; top-bottom decreases it.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">bridge-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">4rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Horizontal bridge in each turn; 4rem in these examples.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">arc-radius</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Radius of all four arcs; 2rem here.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">before-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">0rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Straight section before the first turn; explicitly 2rem here.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">after-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Straight section after returning to the original axis; 2rem here.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">zinc</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Color of the detour and its default labels.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">node-label-left</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Endpoint annotation used by right-side detours.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">node-label-right</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Endpoint annotation used by left-side detours.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">dev-mode</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">false</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Enable component diagnostics and counters; true here for the preview tools.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">anchorNode-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">generated</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Published start coordinates under the root ID.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">generated</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Published endpoint on the original vertical axis, including devCounterNext.</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
        <flux:text class="mt-4">Middle stem = length − beforeLength − afterLength − 4 × arcRadius. Here: 20 − 2 − 2 − 8 = 8rem. Horizontal offset = bridgeLength + 2 × arcRadius = 8rem. The endpoints remain (0rem, 25rem) upwards and (0rem, 5rem) downwards.</flux:text>
        <flux:text class="mt-2">Lengths must resolve to nonnegative rem values; length and arcRadius must be positive. Insufficient total height is rejected. Positioning is explicit: the path does not detect collisions or find a route automatically.</flux:text>
        <flux:text class="mt-2">In a SWITCH, use cases[].entryDetour or case-default.entryDetour with stemLength as the total height. Grouped CASE entries do not support entryDetour.</flux:text>
        <flux:button class="mt-3" size="sm" icon="book-open"
            :href="\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\DocumentationLinks::url(\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\DocumentationLinks::reference('strang.flow-switch-case'))"
            wire:click.prevent="openReference('strang.flow-switch-case')"
        >SWITCH entryDetour · Deep Reference</flux:button>
    </flux:callout>
    <flux:callout class="min-w-0" color="zinc" icon="eye">
        <flux:callout.heading>{{ __('Stem detour preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
            <flux:heading class="m-3" size="sm">side="left" · direction="bottom-top"</flux:heading>
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
                        color="cyan" :dev-mode="true"
                        :node-label-right="[
                            'text' => ['Same axis', 'x = 0rem'],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- stem-detour-left-up-example:end --}}
            </div>
            <flux:heading class="m-3" size="sm">side="right" · direction="bottom-top"</flux:heading>
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
                        color="amber" :dev-mode="true"
                        :node-label-left="[
                            'text' => ['Same axis', 'x = 0rem'],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- stem-detour-right-up-example:end --}}
            </div>
            <flux:heading class="m-3" size="sm">side="left" · direction="top-bottom"</flux:heading>
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
                        color="violet" :dev-mode="true"
                        :node-label-right="[
                            'text' => ['Same axis', 'x = 0rem'],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- stem-detour-left-down-example:end --}}
            </div>
            <flux:heading class="m-3" size="sm">side="right" · direction="top-bottom"</flux:heading>
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
                        color="emerald" :dev-mode="true"
                        :node-label-left="[
                            'text' => ['Same axis', 'x = 0rem'],
                            'width' => 'half',
                            'align' => 'center',
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- stem-detour-right-down-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/paths/paths-stem-detour.blade.php
        </flux:field>
    </flux:callout>
</section>
