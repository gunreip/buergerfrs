<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Rekey compressed') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.documentation-links example="strang.rekey.rekey-compressed" />
        <flux:callout.text>{{ __('Handmade example with individually configurable components.') }}</flux:callout.text>
        @php
            $docExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.rekey.rekey-compressed',
            );
            $docExample1Code = $docExampleSource->example('rekey-compressed-example-1');
        @endphp
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $docExample1Code }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Rekey compressed props') }}</flux:heading>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns sticky>
                    <flux:table.column>{{ __('Prop') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">attach-to
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchor-start at 0/0
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Places the rekey
                            strand. Source strands end at attach-to; target strands start at attach-to.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchor-start at 0/0
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Places the rekey
                            strand. Source strands end at attach-to; target strands start at attach-to.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Controls the horizontal
                            distance between the outer path and the trunk reference.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Controls the visible
                            vertical stem used for the rekey continuation.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:stem-continuation
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Adds explicit rekey
                            source/target continuation stems. Entries can set length and left/right labels; labels are
                            only rendered for anchors that actually exist.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:compressed-stem-parts
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">source only
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Marks omitted history
                            inside a rekey-source path with the compressed stem convention.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">end-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">target only
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Closes a rekey-target
                            with an end segment and centered end label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">cap-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">target only
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Closes a rekey-target
                            with an end segment and centered end label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:start-label
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Source uses start-label
                            at the old key side; target uses end-label at the new continuing side.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:end-label
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Source uses start-label
                            at the old key side; target uses end-label at the new continuing side.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-labels
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Labels concrete facts
                            at source/target anchors, using named arrays with text, width, align, color, and justify.
                            The explicit end key targets the end anchor where supported.</flux:table.cell>
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
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- rekey-compressed-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-16"
                    graph-id="idea-to-paper-step-07-rekey-compressed"
                    :dev="true"
                    :coordinates="true"
                    color="violet"
                    arc-radius="2.75rem"
                    bridge-length="18rem"
                    stem-length="5rem"
                    horizontal-padding="8rem"
                    min-width="52rem"
                    min-height="42rem"
                >
                    <div class="pointer-events-none opacity-25">
                        <x-translation-workbench::ui.tw-graph.strang.trunk
                            id="literature.center.1.rekey-reference"
                            color="zinc"
                            :stem-count="5"
                            start-length="4rem"
                            :stem-lengths="[1 => '5rem', 2 => '5rem', 3 => '5rem', 4 => '5rem', 5 => '5rem']"
                            end-length="3rem"
                            :dev-mode="false"
                            :start-label="[
                                'text' => ['reference trunk'],
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                        />
                    </div>

                    <x-translation-workbench::ui.tw-graph.strang.rekey-source-right
                        id="literature.right.source.1.archive-key"
                        attach-to="strang.trunk.node.4"
                        bridge-length="22rem"
                        stem-length="6rem"
                        :start-label="[
                            'text' => ['rekey source', 'history gap'],
                            'width' => 'halfLong',
                            'align' => 'center',
                        ]"
                        :compressed-stem-parts="[
                            'beforeLength' => '1rem',
                            'gapLength' => '2rem',
                            'afterLength' => '1rem',
                            'capLength' => '1.25rem',
                        ]"
                        :stem-continuation="[
                            1 => [
                                'length' => '5rem',
                                'compressed' => true,
                                'left' => [
                                    'text' => ['omitted history', 'several draft steps'],
                                    'width' => 'halfLong',
                                    'align' => 'right',
                                ],
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- rekey-compressed-example-1:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../strang/rekey/rekey-compressed.blade.php</flux:field>
    </flux:callout>
</section>
