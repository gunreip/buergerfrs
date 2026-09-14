<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout color="indigo" icon="file-text" class="min-w-0">
        <flux:callout.heading>{{ __('End cap') }}</flux:callout.heading>
        <flux:callout.text>{{ __('Handmade trunk example with individually adjustable props.') }}</flux:callout.text>
        @php
            $trunkExampleSource = file_get_contents(\Illuminate\Support\Facades\View::getFinder()->find(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.trunk.trunk-end-cap',
            ));
            if (preg_match('/^[ \t]*\{\{-- trunk-end-cap-example-1:start --\}\}\R(.*?)^[ \t]*\{\{-- trunk-end-cap-example-1:end --\}\}/ms', $trunkExampleSource, $trunkExample1Match) !== 1) {
                throw new \LogicException('Missing trunk-end-cap-example-1 source markers.');
            }
            $trunkExample1Lines = explode("\n", rtrim($trunkExample1Match[1]));
            $trunkExample1Indent = min(array_map(
                fn (string $line): int => strlen($line) - strlen(ltrim($line)),
                array_filter($trunkExample1Lines, fn (string $line): bool => trim($line) !== ''),
            ));
            $trunkExample1Code = implode("\n", array_map(
                fn (string $line): string => substr($line, $trunkExample1Indent),
                $trunkExample1Lines,
            ));
        @endphp
        <div class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $trunkExample1Code }}</code></pre>
        </div>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Controls the final trunk stem before the cap closes the chain.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end-cap-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">cap-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Controls the horizontal cap width of the trunk end.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:end-label</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Centered label for the end of the trunk. Use it for the closing state or outcome of the visible chain.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited graph color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Sets the trunk line, cap, nodes, and labels unless a nested label defines its own color.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout color="zinc" icon="eye" class="min-w-0">
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
                    <div class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                        {{-- trunk-end-cap-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    class="px-20 py-12"
                    graph-id="idea-to-paper-step-04-trunk-end-cap"
                    :dev="true"
                    :coordinates="true"
                    color="indigo"
                    line-length="4rem"
                    stem-length="5rem"
                    cap-length="1.75rem"
                    slot-min-height="42rem"
                    horizontal-padding="28rem"
                    min-width="48rem"
                    min-height="42rem"
                >
                    <x-translation-workbench::ui.tw-graph.strang.trunk
                        id="literature.center.1.paper"
                        :stem-count="4"
                        start-length="4rem"
                        :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem', 4 => '5rem']"
                        end-length="3rem"
                        end-cap-length="3rem"
                        :start-label="[
                            'text' => ['Idea development', 'notes to paper'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                        :end-label="[
                            'text' => ['draft complete', 'wider end cap'],
                            'width' => 'default',
                            'align' => 'center',
                        ]"
                        :start-node-labels="[
                            'left' => [
                                'text' => ['1879 notebook', 'raw observation'],
                                'width' => 'default',
                                'align' => 'right',
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- trunk-end-cap-example-1:end --}}
                    </div>
                </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">.../strang/trunk/trunk-end-cap.blade.php</flux:field>
    </flux:callout>
</section>
