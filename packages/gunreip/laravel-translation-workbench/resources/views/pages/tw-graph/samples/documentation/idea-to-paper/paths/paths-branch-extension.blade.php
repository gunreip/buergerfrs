<x-translation-workbench::ui.common.heading-counter-group group="paths-branch-extension">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Branch extension path') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Two individually authored branch extensions, shown one below the other. Each colored extension starts at the bridge endpoint of a zinc reference branch and continues outward through its own bridge, arc, stem, and labeled end. The reference branch also continues upwards, making the additional branch visible. side="left" runs right-left; side="right" runs left-right.') }}
            </flux:callout.text>
            @php
                $branchExtensionExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-branch-extension',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-extension-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('right-left · side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $branchExtensionExampleSource->example('branch-extension-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-extension-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('left-right · side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $branchExtensionExampleSource->example('branch-extension-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator class="mt-4" :text="__('Props used in these examples')" />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Branch extension path props') }}</flux:callout.heading>
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
                                    <code>id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>path.branch-extension</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the extension and child elements.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>left</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('left continues right-left; right continues left-right.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:anchor-start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Starting coordinates; these examples match the reference branch bridge endpoint.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchorStart.sourceType</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>unset</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Set stem inside anchor-start when attaching to a vertical stem endpoint. This adds an incoming arc before the extension bridge.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>arc-radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph arc_radius</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Size of the outgoing arc and optional incoming arc.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bridge-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph bridge_length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length of the extension bridge.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>stem-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>graph stem_length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length of the vertical section after the arc or optional step.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:step</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional status step before the stem. Accepts text or stepLabel with beforeLength, labelGap, and afterLength.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:node-labels</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Key 3 configures the stem endpoint labels with left/right entries. The key stays 3 when an introductory arc or step is added.') }}
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
                                    {{ __('Optional text or label configuration for the final cap.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>end-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>0rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length of the final end section. The default 0rem still renders a cap at the stem endpoint; null or an empty string omits the section when no end-label is set.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cap-length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional cap size for the end section; explicitly 1.25rem in these examples.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>inherited / zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Extension color and default label color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>z-index</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional stacking-order override.') }}
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
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4">
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-extension-left-example"
                            size="sm"
                        >{{ __('right-left · side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- branch-extension-left-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-branch-extension-right-left"
                                :dev="true"
                                :coordinates="true"
                                min-height="28rem"
                                min-width="44rem"
                                horizontal-padding="20rem"
                            >
                                {{-- Reference bridge ends at x=3.25rem, y=9.75rem. --}}
                                <x-translation-workbench::ui.tw-graph.paths.branch
                                    id="literature.paths.branch-extension.right-left.reference"
                                    side="left"
                                    :anchor-start="['x' => '10rem', 'y' => '5rem']"
                                    entry-stem-length="2rem"
                                    arc-radius="2.75rem"
                                    bridge-length="4rem"
                                    :stem-continuation="[
                                        1 => [
                                            'length' => '4rem',
                                            'right' => [
                                                'text' => ['Reference branch'],
                                                'width' => 'half',
                                                'align' => 'center',
                                            ],
                                        ],
                                    ]"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.paths.branch-extension
                                    id="literature.paths.branch-extension.right-left"
                                    side="left"
                                    :anchor-start="['x' => '3.25rem', 'y' => '9.75rem']"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    stem-length="4rem"
                                    end-length="2rem"
                                    cap-length="1.25rem"
                                    :end-label="[
                                        'text' => ['Extension end'],
                                        'width' => 'half',
                                        'align' => 'center',
                                    ]"
                                    color="cyan"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- branch-extension-left-example:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-extension-right-example"
                            size="sm"
                        >{{ __('left-right · side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- branch-extension-right-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-branch-extension-left-right"
                                :dev="true"
                                :coordinates="true"
                                min-height="28rem"
                                min-width="44rem"
                                horizontal-padding="20rem"
                            >
                                {{-- Reference bridge ends at x=-3.25rem, y=9.75rem. --}}
                                <x-translation-workbench::ui.tw-graph.paths.branch
                                    id="literature.paths.branch-extension.left-right.reference"
                                    side="right"
                                    :anchor-start="['x' => '-10rem', 'y' => '5rem']"
                                    entry-stem-length="2rem"
                                    arc-radius="2.75rem"
                                    bridge-length="4rem"
                                    :stem-continuation="[
                                        1 => [
                                            'length' => '4rem',
                                            'left' => [
                                                'text' => ['Reference branch'],
                                                'width' => 'half',
                                                'align' => 'center',
                                            ],
                                        ],
                                    ]"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.paths.branch-extension
                                    id="literature.paths.branch-extension.left-right"
                                    side="right"
                                    :anchor-start="['x' => '-3.25rem', 'y' => '9.75rem']"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    stem-length="4rem"
                                    end-length="2rem"
                                    cap-length="1.25rem"
                                    :end-label="[
                                        'text' => ['Extension end'],
                                        'width' => 'half',
                                        'align' => 'center',
                                    ]"
                                    color="emerald"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- branch-extension-right-example:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/paths/paths-branch-extension.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
