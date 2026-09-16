<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout color="indigo" icon="file-text" class="min-w-0">
        <flux:callout.heading>{{ __('Part chain') }}</flux:callout.heading>
        <flux:callout.text>{{ __('One explicitly authored start → sideways → end sequence demonstrates automatic continuation anchors. The parts array is the API of parts.chain; each entry is written here individually and can be edited independently. Unlike separate part calls, only the first starting anchor is required.') }}</flux:callout.text>
        @php
            $exampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.parts.parts-chain',
            );
            $exampleCode = $exampleSource->example('parts-chain-example');
        @endphp
        <x-translation-workbench::ui.tw-graph.code-box class="mt-4">{{ $exampleCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">{{ __('Props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">parts</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Ordered, explicitly authored array of start, sideways and end parts. Each entry can override its ID, direction, color and dimensions.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchor-start</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Initial cursor. Subsequent entries start at the preceding continuation point.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Default direction for the sequence.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited / zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Default color; entries can override it.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">canvas stem-length / 4rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Default length for start and end entries.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-radius</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">canvas arc-size / 2.75rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Default arc radius.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">canvas bridge-length / 4rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Default bridge length.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">cap-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">canvas cap-length / 1.75rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Default end-cap length.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout color="cyan" icon="eye" class="min-w-0">
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
            <div class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- parts-chain-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-parts-chain-preview"
                    :dev="true"
                    :coordinates="true"
                    min-width="34rem"
                    min-height="22rem"
                    horizontal-padding="8rem"
                >
                        <x-translation-workbench::ui.tw-graph.parts.chain
                            :anchor-start="['x' => '0rem', 'y' => '3rem']"
                            direction="bottom-top"
                            arc-radius="2rem"
                            bridge-length="6rem"
                            :parts="[
                                [
                                    'type' => 'start',
                                    'id' => 'literature.parts.chain.start',
                                    'length' => '4rem',
                                    'color' => 'cyan',
                                    'jointArrowEnd' => true,
                                ],
                                [
                                    'type' => 'sideways',
                                    'id' => 'literature.parts.chain.sideways',
                                    'side' => 'left',
                                    'color' => 'violet',
                                    'jointArrowEnd' => true,
                                ],
                                [
                                    'type' => 'end',
                                    'id' => 'literature.parts.chain.end',
                                    'length' => '4rem',
                                    'color' => 'amber',
                                    'endLabel' => [
                                        'text' => ['Sequence end'],
                                        'width' => 'half',
                                    ],
                                ],
                            ]"
                        />
                </x-translation-workbench::ui.tw-graph>
                {{-- parts-chain-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/parts/parts-chain.blade.php
        </flux:field>
    </flux:callout>
</section>
