<x-translation-workbench::ui.common.heading-counter-group group="parts-chain">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout color="indigo" icon="file-text" class="min-w-0">
            <flux:callout.heading>{{ __('Part chain') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.documentation-links example="parts.parts-chain" />
            <flux:callout.text>{{ __('One explicitly authored start → sideways → end sequence demonstrates automatic continuation anchors. The parts array is the API of parts.chain; each entry is written here individually and can be edited independently. Unlike separate part calls, only the first starting anchor is required.') }}</flux:callout.text>
            @php
                $exampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.parts.parts-chain',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="sequence"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Start → Sideways → End') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $exampleSource->example('parts-chain-sequence') }}</x-translation-workbench::ui.tw-graph.code-box>
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
                                    <code>parts</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Ordered, explicitly authored array of start, sideways and end parts. Each entry can override its ID, direction, color and dimensions.') }}
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
                                    {{ __('Initial cursor. Subsequent entries start at the preceding continuation point.') }}
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
                                    {{ __('Default direction for the sequence.') }}
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
                                    {{ __('Default color; entries can override it.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('canvas stem-length / 4rem') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Default length for start and end entries.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>arc-radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('canvas arc-radius / 2.75rem') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Default arc radius.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('canvas bridge-length / 4rem') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Default bridge length.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cap-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('canvas cap-length / 1.75rem') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Default end-cap length.') }}
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
        </flux:callout>
        <flux:callout color="emerald" class="min-w-0">
            <flux:callout.heading icon="eye">{{ __('Preview') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
                <div class="mt-4 grid min-w-0 gap-4">
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="sequence"
                            size="sm"
                        >{{ __('Start → Sideways → End') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- parts-chain-sequence:start --}}
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
                            {{-- parts-chain-sequence:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/parts/parts-chain.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
