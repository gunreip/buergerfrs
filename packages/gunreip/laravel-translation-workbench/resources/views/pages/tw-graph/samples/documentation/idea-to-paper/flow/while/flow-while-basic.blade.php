<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>WHILE basic</flux:callout.heading>
        <flux:callout.text>Check the condition before every iteration. TRUE removes and processes the next item, then
            returns to the condition. FALSE continues with the summary. An empty queue skips the body entirely. Loading
            the queue happens once, outside the loop.</flux:callout.text>
        @php
            $whileSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-basic',
            );
        @endphp
        <flux:heading class="mt-4" size="sm">side="left"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $whileSource->example('while-basic-left-example') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:heading class="mt-4" size="sm">side="right"</flux:heading>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">{{ $whileSource->example('while-basic-right-example') }}</x-translation-workbench::ui.tw-graph.code-box>
        <x-translation-workbench::ui.tw-graph.documentation-links example="flow.while.flow-while-basic" />
        <flux:heading
            class="mt-4"
            size="sm"
        >Props and connections</flux:heading>
        <flux:table
            class="mt-3"
            container:class="max-h-80"
        >
            <flux:table.columns sticky>
                <flux:table.column>Prop / anchor</flux:table.column>
                <flux:table.column>Default</flux:table.column>
                <flux:table.column>Purpose</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">side</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">left</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Physical side of the body and return: left or right. The
                        FALSE exit stays on the main axis.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">attach-to</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">null</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Connect the loop after initialization. The return rejoins
                        here without repeating initialization.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">condition-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">WHILE pending items?</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Condition text, width, align and badge color.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">action-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Process next item</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Action inside the horizontal body bridge. This example
                        explicitly removes an item to make progress.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">condition-label.beforeLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Stem before the condition.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">condition-label.labelGap</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">4rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Space reserved for the condition text.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">condition-label.afterLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Stem between the condition and the TRUE/FALSE split.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">arc-radius</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">inherited / 2.75rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Common radius of the four loop corners.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">true-bridge-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">2rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Separate bridge after the TRUE arc. Its end owns the TRUE
                        label; the return bridge grows by the same length.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">action-label.beforeLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">4rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Bridge before the body action.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">action-label.afterLength</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">4rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Bridge after the body action.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">stem-length</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">4rem</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">FALSE stem to the public exit. The return stem follows
                        the condition height automatically.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">true-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">TRUE</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Information at the body entry; text, width, align, side
                        and connector settings.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">false-label</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">FALSE</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Information at the loop exit; text, width, align, side
                        and connector settings.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">color</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">inherited / zinc</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Loop paths and labels; explicit label colors remain
                        configurable.</flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">anchorNode-start</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">connection</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">Initialization and return meet before the condition.
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell class="whitespace-normal">anchorNode-end</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">connection</flux:table.cell>
                    <flux:table.cell class="whitespace-normal">FALSE exit. Attach the next independent action here.
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
        <x-translation-workbench::ui.tw-graph.language-examples
            source-view="translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.flow.while.flow-while-basic"
            example="while-basic"
        />
    </flux:callout>
    <flux:callout
        class="min-w-0"
        color="zinc"
        icon="eye"
    >
        <flux:callout.heading>WHILE basic · left / right</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <flux:heading class="mt-4" size="sm">side="left"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- while-basic-left-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-while-basic-left"
                    :dev="true"
                    :coordinates="true"
                    min-height="44rem"
                    min-width="36rem"
                >
                    {{-- Initialization runs once. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.basic.left.initialize"
                        :anchor-start="['x' => '0rem', 'y' => '2rem']"
                        :step-label="['text' => ['Load pending items'], 'width' => 'default']"
                        afterLength="5rem"
                        color="zinc"
                        :node-end="false"
                    />
                    {{-- TRUE processes one item and returns to the condition. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-while
                        id="literature.while.basic.left.loop"
                        attach-to="literature.while.basic.left.initialize.anchorNode-end"
                        side="left"
                        color="cyan"
                        arc-radius="2.75rem"
                        true-bridge-length="4rem"
                        stem-length="5.5rem"
                        :condition-label="[
                            'beforeLength' => '2rem',
                            'labelGap' => '4rem',
                            'afterLength' => '2rem',
                            'text' => ['WHILE pending items?'],
                            'width' => 'default',
                            'align' => 'center',
                        ]"
                        :action-label="[
                            'beforeLength' => '2rem',
                            'afterLength' => '2rem',
                            'text' => ['Remove next item', 'Process item'],
                            'width' => 'default',
                            'align' => 'center',
                        ]"
                        :true-label="['text' => ['TRUE'], 'width' => 'half', 'side' => 'top', 'color' => 'green']"
                        :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'right', 'color' => 'red']"
                    />
                    {{-- FALSE continues here, including when the queue starts empty. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.basic.left.continue"
                        attach-to="literature.while.basic.left.loop.anchorNode-end"
                        beforeLength="4rem"
                        :step-label="['text' => ['Show summary'], 'width' => 'default']"
                        color="violet"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- while-basic-left-example:end --}}
            </div>
            <flux:heading class="mt-6" size="sm">side="right"</flux:heading>
            <div
                class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                {{-- while-basic-right-example:start --}}
                <x-translation-workbench::ui.tw-graph
                    graph-id="idea-to-paper-while-basic-right"
                    :dev="true"
                    :coordinates="true"
                    min-height="44rem"
                    min-width="36rem"
                >
                    {{-- Initialization runs once. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.basic.right.initialize"
                        :anchor-start="['x' => '0rem', 'y' => '2rem']"
                        :step-label="['text' => ['Load pending items'], 'width' => 'default']"
                        afterLength="5rem"
                        color="zinc"
                        :node-end="false"
                    />
                    {{-- TRUE processes one item and returns to the condition. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-while
                        id="literature.while.basic.right.loop"
                        attach-to="literature.while.basic.right.initialize.anchorNode-end"
                        side="right"
                        color="cyan"
                        arc-radius="2.75rem"
                        true-bridge-length="4rem"
                        stem-length="5.5rem"
                        :condition-label="[
                            'beforeLength' => '2rem',
                            'labelGap' => '4rem',
                            'afterLength' => '2rem',
                            'text' => ['WHILE pending items?'],
                            'width' => 'default',
                            'align' => 'center',
                        ]"
                        :action-label="[
                            'beforeLength' => '2rem',
                            'afterLength' => '2rem',
                            'text' => ['Remove next item', 'Process item'],
                            'width' => 'default',
                            'align' => 'center',
                        ]"
                        :true-label="['text' => ['TRUE'], 'width' => 'half', 'side' => 'top', 'color' => 'green']"
                        :false-label="['text' => ['FALSE'], 'width' => 'half', 'side' => 'left', 'color' => 'red']"
                    />
                    {{-- FALSE continues here, including when the queue starts empty. --}}
                    <x-translation-workbench::ui.tw-graph.strang.flow-step
                        id="literature.while.basic.right.continue"
                        attach-to="literature.while.basic.right.loop.anchorNode-end"
                        beforeLength="4rem"
                        :step-label="['text' => ['Show summary'], 'width' => 'default']"
                        color="violet"
                    />
                </x-translation-workbench::ui.tw-graph>
                {{-- while-basic-right-example:end --}}
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="mt-3 flex justify-end font-mono text-xs text-zinc-400">
            .../flow/while/flow-while-basic.blade.php
        </flux:field>
    </flux:callout>
</section>
