<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout color="indigo" icon="file-text" class="min-w-0">
        <flux:callout.heading>{{ __('Line width') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.documentation-links example="canvas.canvas-props-line" />
        <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
            {{ __('line-width is the central/common thickness used as the fallback for stems, bridges, caps, connectors, and similar line-based pieces. In the more specific strang examples this same idea may appear with contextual names such as stem-width or bridge-width.') }}
        </p>
        <div class="mt-4 grid gap-4 xl:grid-cols-2">
            <x-translation-workbench::ui.tw-graph.code-box>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-props-line-default"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="false"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-width="40rem"</span>
    <span class="text-amber-300">min-height="88rem"</span>
&gt;
    &#123;&#123;-- Default line-width comes from the graph defaults. --&#125;&#125;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</x-translation-workbench::ui.tw-graph.code-box>
            <x-translation-workbench::ui.tw-graph.code-box>&lt;x-translation-workbench::ui.tw-graph
    <span class="text-amber-300">graph-id="idea-to-paper-step-01-props-line-custom"</span>
    <span class="text-amber-300">:dev="true"</span>
    <span class="text-amber-300">:coordinates="false"</span>
    <span class="text-amber-300">horizontal-padding="24rem"</span>
    <span class="text-amber-300">min-width="40rem"</span>
    <span class="text-amber-300">min-height="88rem"</span>
    <span class="text-lime-300">color="emerald"</span>
    <span class="text-lime-300">line-width="0.5rem"</span>
&gt;
    &#123;&#123;-- The trunk has no own props here; it inherits the canvas line width. --&#125;&#125;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</x-translation-workbench::ui.tw-graph.code-box>
        </div>
        <div class="mt-4 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns class="bg-white dark:bg-zinc-900" sticky>
                    <flux:table.column>{{ __('Prop') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>protocol</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>[]</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Data array for the older protocol renderer when no slot content is used. Handmade graphs normally leave this empty.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>graph-id</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>auto id</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Stable DOM id and registry scope for anchors, bounds, DEV counters, and canvas metrics.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>:dev</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>false</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Enables DEV rendering such as node counters, debug boxes, and reduced graph opacity.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>:coordinates</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>false</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Shows or hides coordinate badges. Calculations still run either way.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>color</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>config colors.graph / &#x27;zinc&#x27;</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Base semantic color inherited by child components unless they set their own color.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>line-length</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>4rem</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Internal fallback length for line-based pieces. Authoring usually uses the more specific stem, bridge, start, or end length props.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>line-width</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>0.25rem</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Central/common thickness for graph lines. Specific strangs may expose this as stem-width, bridge-width, cap-width, or connector-width in their own context.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>node-size</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>0.95rem</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Diameter of visible anchor dots and transverse size of joint arrows.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>arc-size</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>2.75rem</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Radius footprint used by arc segments and by geometry calculations around arcs.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>cap-length</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>1.75rem</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Length of end caps used by end-like segments.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>bridge-length</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>line-length</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Default length for bridge segments.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>stem-length</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>line-length</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Default length for stem segments.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>connector-length</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>2rem</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Default helper line length between an anchor node and a text label.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>connector-gap</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>0.25rem</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Gap between a connector and its related label/node edge.</flux:table.cell>
                    </flux:table.row>

                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>horizontal-padding</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>12rem</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Extra left/right canvas room for labels and side strangs.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>min-width</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>calculated</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Optional hard minimum width override for the graph viewport.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>min-height</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs"><code>config / 52rem; protocol: calculated</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal break-words text-xs">Minimum canvas height. Handmade graphs default to config / 52rem; protocol graphs use their geometry. Content bounds and padding can require more space.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:callout>
    <flux:callout color="zinc" icon="square-dashed-text" class="min-w-0">
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools :dev="$dev ?? true" :coordinates="$coordinates ?? false">
            <div class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                <div class="grid gap-4 xl:grid-cols-2">
                    <div class="min-w-0">
                        <flux:heading size="sm">{{ __('Default') }}</flux:heading>
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-step-01-props-line-default"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="24rem"
                            min-width="40rem"
                            min-height="88rem"
                        >
                            {{-- Default line-width comes from the graph defaults. --}}
                            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
                        </x-translation-workbench::ui.tw-graph>
                    </div>
                    <div class="min-w-0">
                        <flux:heading size="sm">{{ __('Custom') }}</flux:heading>
                        <x-translation-workbench::ui.tw-graph
                            graph-id="idea-to-paper-step-01-props-line-custom"
                            :dev="true"
                            :coordinates="true"
                            horizontal-padding="24rem"
                            min-width="40rem"
                            min-height="88rem"
                            color="emerald"
                            line-width="0.5rem"
                        >
                            {{-- The trunk has no own props here; it inherits the canvas line width. --}}
                            <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
                        </x-translation-workbench::ui.tw-graph>
                    </div>
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/canvas/canvas-props-line.blade.php
        </flux:field>
    </flux:callout>
</section>
