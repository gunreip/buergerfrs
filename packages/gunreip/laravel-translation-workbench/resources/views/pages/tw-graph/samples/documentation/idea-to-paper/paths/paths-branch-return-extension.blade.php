<x-translation-workbench::ui.common.heading-counter-group group="paths-branch-return-extension">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Branch return extension path') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Two individually authored return extensions, shown one below the other. The zinc branch and its return provide context. Each colored extension connects a stem, an arc, and a bridge, joining the reference return where its incoming arc meets its bridge. side="left" runs left-right; side="right" runs right-left. The endpoint coordinates are calculated from the starting anchor and lengths.') }}
            </flux:callout.text>
            @php
                $branchReturnExtensionExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.paths.paths-branch-return-extension',
                );
            @endphp
            <flux:separator class="mt-4" :text="__('Code examples')" />
            <flux:accordion transition exclusive>
                <flux:accordion.item expanded>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-return-extension-left-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('left-right · side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $branchReturnExtensionExampleSource->example('branch-return-extension-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout icon="code" color="indigo">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-return-extension-right-example"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('right-left · side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $branchReturnExtensionExampleSource->example('branch-return-extension-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator class="mt-4" :text="__('Props used in these examples')" />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Branch return extension path props') }}</flux:callout.heading>
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
                                    <code>path.branch-return-extension</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the extension and its child elements.') }}
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
                                    {{ __('left returns left-right; right returns right-left.') }}
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
                                    {{ __('Starting coordinates of the vertical stem. Choose these so the final bridge endpoint meets the intended return anchor.') }}
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
                                    {{ __('Size of the arc between stem and bridge.') }}
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
                                    {{ __('Length of the vertical section before the arc.') }}
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
                                    {{ __('Length of the horizontal section after the arc.') }}
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
                                    {{ __('Color of all sections and joint arrows.') }}
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
                            example="branch-return-extension-left-example"
                            size="sm"
                        >{{ __('left-right · side="left"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- branch-return-extension-left-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-branch-return-extension-left-right"
                                :dev="true"
                                :coordinates="true"
                                min-height="30rem"
                                min-width="44rem"
                                horizontal-padding="20rem"
                            >
                                <x-translation-workbench::ui.tw-graph.paths.branch
                                    id="literature.paths.branch-return-extension.left-right.reference-branch"
                                    side="left"
                                    :anchor-start="['x' => '6.75rem', 'y' => '5rem']"
                                    entry-stem-length="2rem"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    :stem-continuation="[
                                        1 => [
                                            'length' => '4rem',
                                            'render' => true,
                                        ],
                                    ]"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.paths.branch-return
                                    id="literature.paths.branch-return-extension.left-right.reference-return"
                                    side="left"
                                    :anchor-start="['x' => '-6.75rem', 'y' => '16.5rem']"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    color="zinc"
                                />
                                {{-- Extension bridge joins the reference return at x=-4rem, y=19.25rem. --}}
                                <x-translation-workbench::ui.tw-graph.paths.branch-return-extension
                                    id="literature.paths.branch-return-extension.left-right"
                                    side="left"
                                    :anchor-start="['x' => '-14.75rem', 'y' => '12.5rem']"
                                    stem-length="4rem"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    color="cyan"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- branch-return-extension-left-example:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="branch-return-extension-right-example"
                            size="sm"
                        >{{ __('right-left · side="right"') }}</x-translation-workbench::ui.common.heading-counter>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- branch-return-extension-right-example:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-paths-branch-return-extension-right-left"
                                :dev="true"
                                :coordinates="true"
                                min-height="30rem"
                                min-width="44rem"
                                horizontal-padding="20rem"
                            >
                                <x-translation-workbench::ui.tw-graph.paths.branch
                                    id="literature.paths.branch-return-extension.right-left.reference-branch"
                                    side="right"
                                    :anchor-start="['x' => '-6.75rem', 'y' => '5rem']"
                                    entry-stem-length="2rem"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    :stem-continuation="[
                                        1 => [
                                            'length' => '4rem',
                                            'render' => true,
                                        ],
                                    ]"
                                    color="zinc"
                                />
                                <x-translation-workbench::ui.tw-graph.paths.branch-return
                                    id="literature.paths.branch-return-extension.right-left.reference-return"
                                    side="right"
                                    :anchor-start="['x' => '6.75rem', 'y' => '16.5rem']"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    color="zinc"
                                />
                                {{-- Extension bridge joins the reference return at x=4rem, y=19.25rem. --}}
                                <x-translation-workbench::ui.tw-graph.paths.branch-return-extension
                                    id="literature.paths.branch-return-extension.right-left"
                                    side="right"
                                    :anchor-start="['x' => '14.75rem', 'y' => '12.5rem']"
                                    stem-length="4rem"
                                    arc-radius="2.75rem"
                                    bridge-length="8rem"
                                    color="emerald"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- branch-return-extension-right-example:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/paths/paths-branch-return-extension.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
