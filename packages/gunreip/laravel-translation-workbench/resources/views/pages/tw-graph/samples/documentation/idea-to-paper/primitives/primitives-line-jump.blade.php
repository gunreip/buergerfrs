<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout class="min-w-0" color="indigo" icon="file-text">
        <flux:callout.heading>{{ __('Line jump') }}</flux:callout.heading>
        <flux:callout.text>{{ __('The primitive draws only a semicircle with round ends. It does not cut another line, create anchors or detect crossings. Parts and segments pass explicit lineJumps to the browser, which positions this same primitive after layout.') }}</flux:callout.text>
        @php
            $source = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.primitives.primitives-line-jump');
        @endphp
        <flux:heading class="mt-4" size="sm">{{ __('Complete example: top') }}</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $source->example('primitive-line-jump-top') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">{{ __('Changed props for the other examples') }}</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">side="bottom" color="blue"
side="left" color="amber"
side="right" color="green"</x-translation-workbench::ui.tw-graph.code-box>
        <flux:table class="mt-3" container:class="max-h-80">
            <flux:table.columns><flux:table.column>Prop</flux:table.column><flux:table.column>Default</flux:table.column><flux:table.column>Purpose</flux:table.column></flux:table.columns>
            <flux:table.rows>
                <flux:table.row><flux:table.cell>side</flux:table.cell><flux:table.cell>top</flux:table.cell><flux:table.cell class="whitespace-normal">top / bottom for bridges; left / right for stems.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell>radius</flux:table.cell><flux:table.cell>0.5rem</flux:table.cell><flux:table.cell class="whitespace-normal">Centerline radius of the semicircle.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell>anchor-x</flux:table.cell><flux:table.cell>0rem</flux:table.cell><flux:table.cell class="whitespace-normal">Horizontal center of the cut; the baseline passes through this point.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell>anchor-y</flux:table.cell><flux:table.cell>0rem</flux:table.cell><flux:table.cell class="whitespace-normal">Vertical center of the cut; positive Y points up.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell>line-width</flux:table.cell><flux:table.cell>graph line-width</flux:table.cell><flux:table.cell class="whitespace-normal">Stroke width and diameter of the round ends.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell>color</flux:table.cell><flux:table.cell>zinc</flux:table.cell><flux:table.cell class="whitespace-normal">Drawing color.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell>tone</flux:table.cell><flux:table.cell>line</flux:table.cell><flux:table.cell class="whitespace-normal">Line or surface tone, subject to graph path-tone.</flux:table.cell></flux:table.row>
                <flux:table.row><flux:table.cell>z-index</flux:table.cell><flux:table.cell>1</flux:table.cell><flux:table.cell class="whitespace-normal">Drawing layer. For configured crossings the browser places it above both lines.</flux:table.cell></flux:table.row>
            </flux:table.rows>
        </flux:table>
    </flux:callout>
    <flux:callout class="min-w-0" color="zinc" icon="eye">
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
            <div class="mt-3 grid grid-cols-2 gap-3">
                <div data-line-jump-example="top" class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:heading class="m-3" size="sm">side="top"</flux:heading>
                    {{-- primitive-line-jump-top:start --}}
                    <x-translation-workbench::ui.tw-graph graph-id="primitives-line-jump-top"
                        :dev="true" :coordinates="true" min-width="12rem" min-height="12rem" horizontal-padding="5rem">
                        <x-translation-workbench::ui.tw-graph.primitives.line-jump
                            id="literature.primitives.line-jump.top"
                            side="top" radius="2rem" anchor-y="4rem" color="violet" />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- primitive-line-jump-top:end --}}
                </div>
                <div data-line-jump-example="bottom" class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:heading class="m-3" size="sm">side="bottom"</flux:heading>
                    {{-- primitive-line-jump-bottom:start --}}
                    <x-translation-workbench::ui.tw-graph graph-id="primitives-line-jump-bottom"
                        :dev="true" :coordinates="true" min-width="12rem" min-height="12rem" horizontal-padding="5rem">
                        <x-translation-workbench::ui.tw-graph.primitives.line-jump
                            id="literature.primitives.line-jump.bottom"
                            side="bottom" radius="2rem" anchor-y="4rem" color="blue" />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- primitive-line-jump-bottom:end --}}
                </div>
                <div data-line-jump-example="left" class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:heading class="m-3" size="sm">side="left"</flux:heading>
                    {{-- primitive-line-jump-left:start --}}
                    <x-translation-workbench::ui.tw-graph graph-id="primitives-line-jump-left"
                        :dev="true" :coordinates="true" min-width="12rem" min-height="12rem" horizontal-padding="5rem">
                        <x-translation-workbench::ui.tw-graph.primitives.line-jump
                            id="literature.primitives.line-jump.left"
                            side="left" radius="2rem" anchor-y="4rem" color="amber" />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- primitive-line-jump-left:end --}}
                </div>
                <div data-line-jump-example="right" class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:heading class="m-3" size="sm">side="right"</flux:heading>
                    {{-- primitive-line-jump-right:start --}}
                    <x-translation-workbench::ui.tw-graph graph-id="primitives-line-jump-right"
                        :dev="true" :coordinates="true" min-width="12rem" min-height="12rem" horizontal-padding="5rem">
                        <x-translation-workbench::ui.tw-graph.primitives.line-jump
                            id="literature.primitives.line-jump.right"
                            side="right" radius="2rem" anchor-y="4rem" color="green" />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- primitive-line-jump-right:end --}}
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../primitives/primitives-line-jump.blade.php</flux:field>
    </flux:callout>
</section>
