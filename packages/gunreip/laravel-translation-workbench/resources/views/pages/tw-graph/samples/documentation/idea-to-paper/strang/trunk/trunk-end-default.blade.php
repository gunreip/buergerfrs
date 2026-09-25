<x-translation-workbench::ui.common.heading-counter-group group="strang-trunk-end-default">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('End default') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.documentation-links example="strang.trunk.trunk-end-default" />
            <flux:callout.text>{{ __('Handmade trunk example with individually adjustable props.') }}
            </flux:callout.text>
            @php
                $trunkExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-end-default',
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
                            example="trunk-end-default-example-1"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('End default') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $trunkExampleSource->example('trunk-end-default-example-1') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Trunk props') }}</flux:callout.heading>
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
                                    <code>end-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Controls the final trunk stem before the cap closes the chain.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>end-cap-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cap-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Controls the horizontal cap width of the trunk end.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:end-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Centered label for the end of the trunk. Use it for the closing state or outcome of the visible chain.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inherited graph color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Sets the trunk line, cap, nodes, and labels unless a nested label defines its own color.') }}
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
                <div class="mt-4 grid min-w-0 gap-4">
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="trunk-end-default-example-1"
                            size="sm"
                        >{{ __('End default') }}</x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- trunk-end-default-example-1:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-step-04-trunk-end"
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
                                horizontal-padding="6rem"
                                min-width="48rem"
                                min-height="42rem"
                            >
                                <x-translation-workbench::ui.tw-graph.strang.trunk
                                    id="literature.center.1.paper"
                                    :stem-count="4"
                                    start-length="4rem"
                                    :stem-lengths="[
                                        1 => '5rem',
                                        2 => '5rem',
                                        3 => '5rem',
                                        4 => '5rem',
                                    ]"
                                    end-length="3rem"
                                    end-cap-length="1.75rem"
                                    :start-label="[
                                        'text' => ['Idea development', 'notes to paper'],
                                        'width' => 'halfLong',
                                        'align' => 'center',
                                    ]"
                                    :end-label="[
                                        'text' => ['draft complete', 'ready for review'],
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
                                    :node-labels="[
                                        2 => [
                                            'left' => [
                                                'text' => ['1888 outline', 'first structure'],
                                                'width' => 'default',
                                                'align' => 'right',
                                            ],
                                            'right' => [
                                                'text' => [
                                                    'The note receives a rough order and becomes a working outline.',
                                                ],
                                                'width' => 'long',
                                                'align' => 'left',
                                                'justify' => true,
                                            ],
                                        ],
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- trunk-end-default-example-1:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/strang/trunk/trunk-end-default.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
