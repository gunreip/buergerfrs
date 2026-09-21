<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout color="indigo" icon="file-text" class="min-w-0">
        <flux:callout.heading>{{ __('End part') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.documentation-links example="parts.parts-end" />
        <flux:callout.text>{{ __('Two individually authored endings demonstrate both vertical directions and different cap lengths. Each part calculates its final anchor and places the end label there.') }}</flux:callout.text>
        @php
            $exampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.parts.parts-end',
            );
            $exampleCode = $exampleSource->example('parts-end-example');
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">id</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">generated</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stable identifier for the part and its child elements.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">component-counter</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">1</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Used when generating an ID.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchor-start</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Starting coordinates; the part calculates its end anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">direction</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bottom-top</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Vertical flow direction; start and end also support left-right and right-left.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited / zinc</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Color of the connection and default label color.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">z-index</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">20</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Stacking order.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dev-mode</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">inherited</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Overrides the canvas DEV mode.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">canvas part_end_length / 2rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Length of the ending line.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">cap-length</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">canvas cap-length / 1.75rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Width of the final cap.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">node-start</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">false</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Show a node at the entry.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end-label</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Label configuration at the final anchor.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dev-counter-end</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">E</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Counter at the end; false hides it.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">dev-counter-color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">resolved color</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Counter color.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout color="cyan" icon="eye" class="min-w-0">
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
            <div class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- parts-end-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-parts-end-preview"
                    :dev="true"
                    :coordinates="true"
                    min-width="34rem"
                    min-height="18rem"
                    horizontal-padding="8rem"
                >
                        <x-translation-workbench::ui.tw-graph.parts.end
                            id="literature.parts.end.up"
                            :anchor-start="['x' => '0rem', 'y' => '3rem']"
                            direction="bottom-top"
                            length="8rem"
                            cap-length="2rem"
                            color="cyan"
                            :node-start="true"
                            :end-label="['text' => ['End upward'], 'width' => 'half']"
                        />
                        <x-translation-workbench::ui.tw-graph.parts.end
                            id="literature.parts.end.down"
                            :anchor-start="['x' => '14rem', 'y' => '11rem']"
                            direction="top-bottom"
                            length="8rem"
                            cap-length="3rem"
                            color="violet"
                            :node-start="true"
                            :end-label="['text' => ['End downward'], 'width' => 'half']"
                        />
                </x-translation-workbench::ui.tw-graph>
                {{-- parts-end-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/parts/parts-end.blade.php
        </flux:field>
    </flux:callout>
</section>
