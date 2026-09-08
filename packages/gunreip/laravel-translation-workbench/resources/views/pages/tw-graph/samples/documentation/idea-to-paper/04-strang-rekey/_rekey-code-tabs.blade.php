            <flux:tab.group class="mt-4 min-w-0 max-w-full">
                <flux:tabs
                    scrollable
                    scrollable:fade
                    scrollable:scrollbar="hide"
                >
                    <flux:tab
                        name="rekey-default"
                        x-on:click="rekeyVariant = 'default'"
                    >
                        {{ __('Default') }}
                    </flux:tab>
                    <flux:tab
                        name="rekey-source"
                        x-on:click="rekeyVariant = 'source'"
                    >
                        {{ __('Source') }}
                    </flux:tab>
                    <flux:tab
                        name="rekey-target"
                        x-on:click="rekeyVariant = 'target'"
                    >
                        {{ __('Target') }}
                    </flux:tab>
                    <flux:tab
                        name="rekey-compressed"
                        x-on:click="rekeyVariant = 'compressed'"
                    >
                        {{ __('Source gap') }}
                    </flux:tab>
                </flux:tabs>

                <flux:tab.panel name="rekey-default">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.rekey-source-left
    id="literature.left.source.1.old-title"
    attach-to="strang.trunk.node.3"
    bridge-length="18rem"
    stem-length="5rem"
    :start-label="[
        'text' => ['rekey source', 'from note ID #12'],
        'width' => 'halfLong',
        'align' => 'center',
    ]"
/&gt;

&lt;x-translation-workbench::ui.tw-graph.strang.rekey-target-right
    id="literature.right.target.1.new-paper"
    attach-to="strang.trunk.node.4"
    bridge-length="18rem"
    stem-length="5rem"
    :end-label="[
        'text' => ['rekey target', 'continues as paper ID #42'],
        'width' => 'halfLong',
        'align' => 'center',
    ]"
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="rekey-source">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.rekey-source-left
    id="literature.left.source.1.old-title"
    attach-to="strang.trunk.node.3"
    bridge-length="22rem"
    stem-length="6rem"
    :start-label="[
        'text' => ['rekey source', 'from notebook ID #12'],
        'width' => 'halfLong',
        'align' => 'center',
    ]"
    :node-labels="[
        1 => [
            'right' => [
                'text' => ['Origin key', 'notes.gravity.first'],
                'width' => 'halfLong',
                'align' => 'left',
            ],
        ],
        'end' => [
            'left' => [
                'text' => ['rekeyed into', 'current paper key'],
                'width' => 'default',
                'align' => 'right',
            ],
        ],
    ]"
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="rekey-target">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.rekey-target-right
    id="literature.right.target.1.new-paper"
    attach-to="strang.trunk.node.4"
    bridge-length="24rem"
    stem-length="6rem"
    end-length="4rem"
    cap-length="2rem"
    :end-label="[
        'text' => ['rekey target to ID #42', '1905-06-30'],
        'width' => 'halfLong',
        'align' => 'center',
    ]"
    :node-labels="[
        3 => [
            'right' => [
                'text' => ['New key', 'papers.relativity.special'],
                'width' => 'halfLong',
                'align' => 'left',
            ],
        ],
    ]"
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="rekey-compressed">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.rekey-source-right
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
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>
            </flux:tab.group>
