<x-translation-workbench::ui.common.heading-counter-group group="strang-trunk-start-colors">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout color="indigo" icon="file-text" class="min-w-0">
            <flux:callout.heading>{{ __('Start colors') }}</flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.documentation-links example="strang.trunk.trunk-start-colors" />
            <flux:callout.text>{{ __('Handmade trunk example with individually adjustable props.') }}</flux:callout.text>
            @php
                $trunkExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-start-colors',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="trunk-start-colors-example-1"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Start colors') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $trunkExampleSource->example('trunk-start-colors-example-1') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator class="mt-4" :text="__('Props used in these examples')" />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Trunk props') }}</flux:callout.heading>
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
                                    <code>start-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Controls the first visible trunk segment from the canvas origin to the trunk-start nodeEnd anchor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:start-label</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Centered label for the start of the trunk. Use it for the general meaning of the chain, not for record-specific node facts.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:start-node-labels</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Left/right labels attached to the first trunk anchor. This is where the first concrete facts of the chain become visible.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>start-label-space</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>3rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Additional measured space for the centered start label so the canvas bounds can include the label area.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:start-shift-enabled</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>config trunk_start_shift_enabled</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Allows the start area to be compensated if the real start-label bounds collide with side content later.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>start-shift-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>config trunk_start_shift_length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Minimum compensation length used only when such a real start collision is detected.') }}
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
                <div class="mt-4 grid min-w-0 gap-4">
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="trunk-start-colors-example-1"
                            size="sm"
                        >{{ __('Start colors') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- trunk-start-colors-example-1:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-step-03-trunk-start-colors"
                                :dev="true"
                                :coordinates="true"
                                color="indigo"
                                line-length="4rem"
                                stem-length="5rem"
                                horizontal-padding="28rem"
                                min-width="48rem"
                                min-height="42rem"
                            >
                                <x-translation-workbench::ui.tw-graph.strang.trunk
                                    id="literature.center.1.paper"
                                    color="sky"
                                    :stem-count="4"
                                    start-length="4rem"
                                    :stem-lengths="[
                                        1 => '5rem',
                                        2 => '5rem',
                                        3 => '5rem',
                                        4 => '5rem',
                                    ]"
                                    end-length="3rem"
                                    :start-label="[
                                        'text' => ['Idea development', 'trunk overrides graph color'],
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
                                            'text' => ['Inherited from trunk', 'sky'],
                                            'width' => 'default',
                                            'align' => 'right',
                                        ],
                                        'right' => [
                                            'text' => ['Explicit label color', 'amber'],
                                            'width' => 'default',
                                            'align' => 'left',
                                            'color' => 'amber',
                                        ],
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- trunk-start-colors-example-1:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/strang/trunk/trunk-start-colors.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
