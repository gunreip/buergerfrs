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
            $branchReturnExtensionLeftCode = $branchReturnExtensionExampleSource->example('branch-return-extension-left-example');
            $branchReturnExtensionRightCode = $branchReturnExtensionExampleSource->example('branch-return-extension-right-example');
        @endphp
        <flux:heading
            class="mt-4"
            size="sm"
        >left-right · side="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $branchReturnExtensionLeftCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >right-left · side="right"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $branchReturnExtensionRightCode }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Branch return extension path props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">path.branch-return-extension</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Identifier for the extension and its child elements.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">side</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">left returns left-right; right returns right-left.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Starting coordinates of the vertical stem. Choose these so the final bridge endpoint meets the intended return anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">arc-size</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph arc_size</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Size of the arc between stem and bridge.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph stem_length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the vertical section before the arc.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">graph bridge_length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the horizontal section after the arc.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited / zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Color of all sections and joint arrows.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Optional stacking-order override.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:dev</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Enable path and segment diagnostic boxes and node counters.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout
        class="min-w-0"
        color="zinc"
        icon="eye"
    >
        <flux:callout.heading>{{ __('Branch return extension path preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <flux:heading
                class="mt-4"
                size="sm"
            >left-right · side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
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
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        :stem-continuation="[
                            1 => [
                                'length' => '4rem',
                                'render' => true,
                            ],
                        ]"
                        color="zinc"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.paths.branch-return
                        id="literature.paths.branch-return-extension.left-right.reference-return"
                        side="left"
                        :anchor-start="['x' => '-6.75rem', 'y' => '16.5rem']"
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        color="zinc"
                        :dev="true"
                    />
                    {{-- Extension bridge joins the reference return at x=-4rem, y=19.25rem. --}}
                    <x-translation-workbench::ui.tw-graph.paths.branch-return-extension
                        id="literature.paths.branch-return-extension.left-right"
                        side="left"
                        :anchor-start="['x' => '-14.75rem', 'y' => '12.5rem']"
                        stem-length="4rem"
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        color="cyan"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- branch-return-extension-left-example:end --}}
            </div>
            <flux:heading
                class="mt-4"
                size="sm"
            >right-left · side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
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
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        :stem-continuation="[
                            1 => [
                                'length' => '4rem',
                                'render' => true,
                            ],
                        ]"
                        color="zinc"
                        :dev="true"
                    />
                    <x-translation-workbench::ui.tw-graph.paths.branch-return
                        id="literature.paths.branch-return-extension.right-left.reference-return"
                        side="right"
                        :anchor-start="['x' => '6.75rem', 'y' => '16.5rem']"
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        color="zinc"
                        :dev="true"
                    />
                    {{-- Extension bridge joins the reference return at x=4rem, y=19.25rem. --}}
                    <x-translation-workbench::ui.tw-graph.paths.branch-return-extension
                        id="literature.paths.branch-return-extension.right-left"
                        side="right"
                        :anchor-start="['x' => '14.75rem', 'y' => '12.5rem']"
                        stem-length="4rem"
                        arc-size="2.75rem"
                        bridge-length="8rem"
                        color="emerald"
                        :dev="true"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- branch-return-extension-right-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/paths/paths-branch-return-extension.blade.php
        </flux:field>
    </flux:callout>
</section>
