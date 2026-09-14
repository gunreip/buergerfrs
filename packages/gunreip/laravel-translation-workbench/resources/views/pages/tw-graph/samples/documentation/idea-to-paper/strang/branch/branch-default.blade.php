<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Branch default') }}</flux:callout.heading>
        <flux:callout.text>{{ __('Handmade example with individually configurable components.') }}</flux:callout.text>
        @php
            $docExampleSource = file_get_contents(
                \Illuminate\Support\Facades\View::getFinder()->find(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.strang.branch.branch-default',
                ),
            );
            if (
                preg_match(
                    '/^[ \t]*\{\{-- branch-default-example-1:start --\}\}\R(.*?)^[ \t]*\{\{-- branch-default-example-1:end --\}\}/ms',
                    $docExampleSource,
                    $docExample1Match,
                ) !== 1
            ) {
                throw new \LogicException('Missing branch-default-example-1 source markers.');
            }
            $docExample1Lines = explode("\n", rtrim($docExample1Match[1]));
            $docExample1Indent = min(
                array_map(
                    fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                    array_filter($docExample1Lines, fn(string $line): bool => trim($line) !== ''),
                ),
            );
            $docExample1Code = implode(
                "\n",
                array_map(fn(string $line): string => substr($line, $docExample1Indent), $docExample1Lines),
            );
        @endphp
        <div
            class="mt-3 min-w-0 max-w-full overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
            <pre style="white-space: pre-wrap; overflow-wrap: anywhere;"><code>{{ $docExample1Code }}</code></pre>
        </div>
        <flux:heading
            class="mt-4"
            size="sm"
        >{{ __('Branch default props') }}</flux:heading>
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
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Places the branch.
                            Prefer attach-to when the branch belongs to a registered trunk, merge, or rekey anchor.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:anchor-start
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">anchor-start at 0/0
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Places the branch.
                            Prefer attach-to when the branch belongs to a registered trunk, merge, or rekey anchor.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">entry-stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">0rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Adds a short vertical
                            entry before the first branch arc. Useful when the branch should clear the trunk node.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">bridge-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Controls the horizontal
                            distance from the trunk before the branch turns into its vertical stem.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">stem-length
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Controls the first
                            branch stem after the arcs and optional step.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:step</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">null</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Places a centered step
                            label between short stems, useful for status changes or decisions.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:stem-continuation
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Adds more branch stems.
                            Each continuation can carry left/right labels and a custom length.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:branch-extension
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Adds secondary branch
                            paths from a branch anchor without rebuilding the whole strand by hand.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:branch-return
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Adds a return path back
                            toward another anchor. Default fallback=true keeps unresolved returns visible;
                            fallback=false means the requested anchor is required and DEV warns when it is missing.
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">:node-labels
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">[]</flux:table.cell>
                        <flux:table.cell class="whitespace-normal break-words align-top text-xs">Labels on supported
                            branch anchors. Numeric labels map to existing branch anchors only; unsupported numeric keys
                            are ignored and reported as nodeLabel-Mismatch in DEV mode.</flux:table.cell>
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
                {{-- branch-default-example-1:start --}}
                <x-translation-workbench::ui.tw-graph
                    class="px-24 py-16"
                    graph-id="idea-to-paper-step-06-branch"
                    :dev="true"
                    :coordinates="true"
                    color="rose"
                    line-length="4rem"
                    line-width="0.25rem"
                    node-size="0.95rem"
                    arc-size="2.75rem"
                    bridge-length="18rem"
                    stem-length="5rem"
                    connector-length="2rem"
                    connector-gap="0.25rem"
                    slot-min-height="46rem"
                    horizontal-padding="8rem"
                    min-width="32rem"
                    min-height="46rem"
                >
                    <div class="pointer-events-none opacity-25">
                        <x-translation-workbench::ui.tw-graph.strang.trunk
                            id="literature.center.1.branch-reference"
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

                    <x-translation-workbench::ui.tw-graph.strang.branch-left
                        id="literature.left.1.side-thought"
                        attach-to="strang.trunk.node.2"
                        bridge-length="18rem"
                        stem-length="5rem"
                        :node-labels="[
                            3 => [
                                'left' => [
                                    'text' => ['side thought', 'kept separate'],
                                    'width' => 'default',
                                    'align' => 'right',
                                ],
                            ],
                        ]"
                    />

                    <x-translation-workbench::ui.tw-graph.strang.branch-right
                        id="literature.right.1.side-thought"
                        attach-to="strang.trunk.node.2"
                        bridge-length="18rem"
                        stem-length="5rem"
                        :node-labels="[
                            3 => [
                                'right' => [
                                    'text' => ['parallel thought', 'kept separate'],
                                    'width' => 'default',
                                    'align' => 'left',
                                ],
                            ],
                        ]"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- branch-default-example-1:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../strang/branch/branch-default.blade.php</flux:field>
    </flux:callout>
</section>
