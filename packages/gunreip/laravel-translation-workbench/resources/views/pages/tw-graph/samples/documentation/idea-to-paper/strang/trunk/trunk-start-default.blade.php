<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout color="indigo" icon="file-text" class="min-w-0">
        <flux:callout.heading>{{ __('Start default') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.documentation-links example="strang.trunk.trunk-start-default" />
        <flux:callout.text>{{ __('Handmade trunk example with individually adjustable props.') }}</flux:callout.text>
        @php
            $trunkExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-start-default',
            );
            $trunkExample1Code = $trunkExampleSource->example('trunk-start-default-example-1');
        @endphp
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $trunkExample1Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">{{ __('Trunk props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Controls the first visible trunk segment from the canvas origin to the trunk-start nodeEnd anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:start-label</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Centered label for the start of the trunk. Use it for the general meaning of the chain, not for record-specific node facts.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:start-node-labels</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Left/right labels attached to the first trunk anchor. This is where the first concrete facts of the chain become visible.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-label-space</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">3rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Additional measured space for the centered start label so the canvas bounds can include the label area.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:start-shift-enabled</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">config trunk_start_shift_enabled</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Allows the start area to be compensated if the real start-label bounds collide with side content later.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">start-shift-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">config trunk_start_shift_length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Minimum compensation length used only when such a real start collision is detected.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout color="zinc" icon="eye" class="min-w-0">
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
                    <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- trunk-start-default-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    class="px-20 py-12"
                    graph-id="idea-to-paper-step-03-trunk-start"
                    :dev="true"
                    :coordinates="true"
                    color="indigo"
                    line-length="4rem"
                    line-width="0.25rem"
                    node-size="0.95rem"
                    arc-radius="2.75rem"
                    cap-length="1.75rem"
                    bridge-length="18rem"
                    stem-length="5rem"
                    connector-length="2rem"
                    connector-gap="0.25rem"
                    horizontal-padding="28rem"
                    min-width="48rem"
                    min-height="42rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.trunk
                        id="literature.center.1.paper"
                        :stem-count="4"
                        start-length="4rem"
                        start-label-space="3rem"
                        :stem-lengths="[
                            1 => '5rem',
                            2 => '5rem',
                            3 => '5rem',
                            4 => '5rem',
                        ]"
                        end-length="3rem"
                        :start-label="[
                            'text' => ['Idea development', 'notes to paper'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                        :end-label="[
                            'text' => ['next', 'structure the thought'],
                            'width' => 'default',
                            'align' => 'center',
                        ]"
                        :start-node-labels="[
                            'left' => [
                                'text' => ['1879 notebook', 'raw observation'],
                                'width' => 'default',
                                'align' => 'right',
                                'color' => 'indigo',
                            ],
                            'right' => [
                                'text' => ['A loose idea is captured as a short note.'],
                                'width' => 'long',
                                'align' => 'left',
                                'justify' => true,
                                'color' => 'zinc',
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- trunk-start-default-example-1:end --}}
                    </div>
                </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../strang/trunk/trunk-start-default.blade.php</flux:field>
    </flux:callout>
</section>
