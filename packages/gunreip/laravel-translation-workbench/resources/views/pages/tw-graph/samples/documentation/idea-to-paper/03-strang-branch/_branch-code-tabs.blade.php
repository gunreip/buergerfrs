            <flux:tab.group class="mt-4 min-w-0 max-w-full">
                <flux:tabs
                    scrollable
                    scrollable:fade
                    scrollable:scrollbar="hide"
                >
                    <flux:tab
                        name="branch-default"
                        x-on:click="branchVariant = 'default'"
                    >
                        {{ __('Default') }}
                    </flux:tab>
                    <flux:tab
                        name="branch-offset"
                        x-on:click="branchVariant = 'offset'"
                    >
                        {{ __('Different anchors') }}
                    </flux:tab>
                    <flux:tab
                        name="branch-step"
                        x-on:click="branchVariant = 'step'"
                    >
                        {{ __('Step') }}
                    </flux:tab>
                    <flux:tab
                        name="branch-continuation"
                        x-on:click="branchVariant = 'continuation'"
                    >
                        {{ __('Continuation') }}
                    </flux:tab>
                    <flux:tab
                        name="branch-return"
                        x-on:click="branchVariant = 'return'"
                    >
                        {{ __('Return') }}
                    </flux:tab>
                    <flux:tab
                        name="branch-mismatch"
                        x-on:click="branchVariant = 'mismatch'"
                    >
                        {{ __('Mismatch') }}
                    </flux:tab>
                </flux:tabs>

                <flux:tab.panel name="branch-default">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
<pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.branch-left
    id="literature.left.1.side-thought"
    attach-to="strang.trunk.node.2"
    <span class="text-lime-300">bridge-length="18rem"
    stem-length="5rem"
    :node-labels="[
        3 => [
            'left' => [
                'text' => ['side thought', 'kept separate'],
                'width' => 'default',
                'align' => 'right',
            ],
        ],
    ]"</span>
/&gt;

&lt;x-translation-workbench::ui.tw-graph.strang.branch-right
    id="literature.right.1.side-thought"
    attach-to="strang.trunk.node.2"
    <span class="text-lime-300">bridge-length="18rem"
    stem-length="5rem"
    :node-labels="[
        3 => [
            'right' => [
                'text' => ['parallel thought', 'kept separate'],
                'width' => 'default',
                'align' => 'left',
            ],
        ],
    ]"</span>
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="branch-offset">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
<pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.branch-left
    id="literature.left.1.question"
    <span class="text-lime-300">attach-to="strang.trunk.node.2"</span>
    <span class="text-amber-300">bridge-length="20rem"</span>
/&gt;

&lt;x-translation-workbench::ui.tw-graph.strang.branch-right
    id="literature.right.1.review"
    <span class="text-lime-300">attach-to="strang.trunk.node.4"</span>
    <span class="text-amber-300">bridge-length="24rem"</span>
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="branch-step">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
<pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.branch-right
    id="literature.right.1.decision"
    attach-to="strang.trunk.node.3"
    <span class="text-amber-300">bridge-length="22rem"</span>
    <span class="text-lime-300">:step="[
        'beforeLength' => '1.5rem',
        'afterLength' => '2.5rem',
        'stepLabel' => [
            'text' => ['Decision', 'keep as appendix'],
            'width' => 'halfLong',
            'align' => 'center',
        ],
    ]"</span>
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="branch-continuation">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
<pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.branch-left
    id="literature.left.1.archive-path"
    attach-to="strang.trunk.node.2"
    <span class="text-amber-300">bridge-length="20rem"</span>
    <span class="text-lime-300">:stem-continuation="[
        1 => [
            'length' => '5rem',
            'left' => [
                'text' => ['Archive copy', 'kept for traceability'],
                'width' => 'halfLong',
                'align' => 'right',
            ],
        ],
        2 => [
            'length' => '5rem',
            'right' => [
                'text' => ['Later cited', 'by reviewer'],
                'width' => 'default',
                'align' => 'left',
            ],
        ],
    ]"</span>
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="branch-return">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
<pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.branch-right
    id="literature.right.1.review-loop"
    attach-to="strang.trunk.node.2"
    <span class="text-amber-300">bridge-length="22rem"</span>
    <span class="text-lime-300">:stem-continuation="[
        1 => [
            'length' => '4.1rem',
            'right' => [
                'text' => ['Review loop', 'comment resolved'],
                'width' => 'default',
                'align' => 'left',
            ],
        ],
    ]"
    :branch-return="[
        1 => [
            'attachTo' => 'stem.1.end',
            'closeTo' => '+3',
            'bridgeLength' => '22rem',
            'fallback' => false,
        ],
    ]"</span>
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>

                <flux:tab.panel name="branch-mismatch">
                    <div
                        class="mt-4 overflow-x-auto rounded-lg border border-zinc-200 bg-zinc-950 p-4 text-xs leading-5 text-zinc-100 dark:border-zinc-700">
                        <pre><code>&lt;x-translation-workbench::ui.tw-graph.strang.branch-left
    id="literature.left.1.label-mismatch"
    attach-to="strang.trunk.node.2"
    <span class="text-amber-300">bridge-length="18rem"
    stem-length="5rem"</span>
    :node-labels="[
        3 => [
            'left' => ['text' => ['Valid label', 'branch arc end']],
        ],
        ...
        <span class="text-lime-300">4 => [
            'right' => ['text' => ['Ignored label', 'anchor 4 missing']],
        ],</span>
    ]"
/&gt;</code></pre>
                    </div>
                </flux:tab.panel>
            </flux:tab.group>
