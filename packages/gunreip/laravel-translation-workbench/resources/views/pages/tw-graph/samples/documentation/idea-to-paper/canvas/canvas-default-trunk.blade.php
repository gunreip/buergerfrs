<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>{{ __('Default + trunk') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.documentation-links example="canvas.canvas-default-trunk" />
        <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
            {{ __('This keeps the canvas at its defaults and adds a default trunk. It shows why canvas sizing props become necessary once real graph content is added (missing coordinates, padding, margin, and related frame space).') }}
        </p>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-4">&lt;x-translation-workbench::ui.tw-graph&gt;
    &lt;x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" /&gt;
&lt;/x-translation-workbench::ui.tw-graph&gt;</x-translation-workbench::ui.tw-graph.code-box>
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
        <flux:heading class="mt-6" size="lg">{{ __('Using the preview tools') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('The preview-tools component wraps one or more graph previews and provides the shared control bar. Try the controls on the default trunk alongside this explanation. They change diagnostic visibility immediately in the browser; they do not change lengths, anchors, collision calculations or the graph source.') }}
        </flux:text>
        <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Control') }}</flux:table.column>
                    <flux:table.column>{{ __('Effect') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal text-xs">DEV mode</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal text-xs">
                            {{ __('Master switch for diagnostic overlays: node counters, DEV hints and mismatch badges, bounding boxes and coordinate overlays. Turning it off hides these overlays and restores normal graph opacity. The Bounding boxes and Coordinates selections are retained and take effect again when DEV mode is enabled.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal text-xs">Bounding boxes</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal text-xs">
                            {{ __('Shows the dashed diagnostic rectangles around components and labels. These help inspect occupied space, multiline labels and overlaps. Requires DEV mode. Hiding the rectangles does not change the bounds used for layout, and does not hide the separate canvas dimension guides.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal text-xs">Coordinates</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal text-xs">
                            {{ __('Shows the available coordinate guides and measurement badges for the rendered graph bounds. The yellow guides additionally explain canvas dimensions, minimum sizes and padding; they are not the bounding box of an individual component. Requires DEV mode and coordinate diagnostics rendered by the graph.') }}
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top whitespace-normal text-xs">Refresh preview ↻</flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal text-xs">
                            {{ __('Re-renders the containing Livewire documentation component on the server. The active example, its source code box and its graph are rendered again without switching tabs or reloading the whole browser page. The active tab and saved visibility settings remain selected. The button is disabled while its refresh request is running; outside a Livewire component it has no refresh action.') }}
                        </flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
        <figure class="mt-4">
            <figcaption class="mb-2 text-sm text-zinc-600 dark:text-zinc-300">
                {{ __('Static example — no function. DEV mode and Bounding boxes are selected; Coordinates is off. The refresh icon sits at the far right.') }}
            </figcaption>
            <div inert class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                <flux:fieldset class="rounded-lg bg-white p-3 dark:bg-zinc-900">
                    <div class="flex items-center gap-4 *:gap-x-2">
                        <flux:checkbox.group variant="pills">
                            <flux:checkbox checked label="{{ __('DEV mode') }}" />
                            <flux:checkbox checked label="{{ __('Bounding boxes') }}" />
                            <flux:checkbox label="{{ __('Coordinates') }}" />
                        </flux:checkbox.group>
                        <div class="ml-auto shrink-0">
                            <flux:button type="button" variant="ghost" size="sm" square
                                icon="arrow-path" :aria-label="__('Refresh preview')" />
                        </div>
                    </div>
                </flux:fieldset>
            </div>
        </figure>
        <flux:heading class="mt-4" size="sm">{{ __('Start values and saved settings') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('On preview-tools, :dev defaults to true and :coordinates defaults to false; Bounding boxes starts enabled. These are initial visibility values only. Alpine Persist stores the three selections in localStorage under tw-graph-preview-dev, tw-graph-preview-boxes and tw-graph-preview-coordinates. Previously saved values take precedence, including after a reload. All previews using these keys share the preferences in this browser for this site; they are not saved in the database or synchronized to other devices.') }}
        </flux:text>
        <flux:heading class="mt-4" size="sm">{{ __('Render diagnostics first, then control their visibility') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('The graph itself must render the diagnostics before the toolbar can reveal them. Documentation previews therefore set :dev="true" and :coordinates="true" on tw-graph. The wrapper can initially hide them. If the graph was rendered with these props disabled, switching the toolbar on cannot create the missing diagnostic elements. The short graph example above omits this documentation wrapper to focus on canvas defaults.') }}
        </flux:text>
        <x-translation-workbench::ui.tw-graph.code-box class="mt-3">&lt;x-translation-workbench::ui.tw-graph.preview-tools
    :dev="true"
    :coordinates="false"
&gt;
    &lt;x-translation-workbench::ui.tw-graph
        graph-id="preview-tools-example"
        :dev="true"
        :coordinates="true"
    &gt;
        &lt;x-translation-workbench::ui.tw-graph.strang.trunk
            id="literature.center.1.paper"
        /&gt;
    &lt;/x-translation-workbench::ui.tw-graph&gt;
&lt;/x-translation-workbench::ui.tw-graph.preview-tools&gt;</x-translation-workbench::ui.tw-graph.code-box>
        <flux:callout class="mt-4" color="indigo" icon="information-circle">
            <flux:callout.heading>{{ __('Try it here') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Enable DEV mode, then toggle Bounding boxes and Coordinates separately. Leave Coordinates enabled and turn DEV mode off and on to see the master switch in action. Switch to another example to check the saved settings. After editing and saving an example, use the refresh icon at the far right to render the current documentation again.') }}
            </flux:callout.text>
        </flux:callout>
    </flux:callout>
    <flux:callout
        class="min-w-0"
        color="zinc"
        icon="square-dashed-text"
    >
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                <div class="min-w-0">
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-canvas-default-trunk"
                        :dev="true"
                        :coordinates="true"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
                    </x-translation-workbench::ui.tw-graph>
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/canvas/canvas-default-trunk.blade.php
        </flux:field>
    </flux:callout>
</section>
