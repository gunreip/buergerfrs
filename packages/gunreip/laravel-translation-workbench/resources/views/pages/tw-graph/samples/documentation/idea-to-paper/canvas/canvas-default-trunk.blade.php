@php
    $canvasSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-default-trunk',
    );
@endphp
<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">

    {{-- CodeBox And Props Table --}}
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>
            {{ __('Default + trunk') }}
        </flux:callout.heading>
        <flux:callout.text class="mt-4 hyphens-auto text-justify text-sm leading-6 text-zinc-600 dark:text-zinc-300">
            {{ __('This adds a default trunk. Each drawing primitive declares its bounds. The canvas and coordinate display use their combined geometry, independently of DEV mode. Text boxes are provisional on the server and updated after font layout. Browser checks report geometry mismatches instead of overriding the declared geometry. Horizontal padding positions the coordinate origin; min-width may leave extra space on the right. The dashed cyan frame shows the resulting bounds; yellow borders show the configured canvas dimensions.') }}
        </flux:callout.text>

        <flux:separator
            class="mt-4"
            :text="__('Deep reference links')"
        />

        <x-translation-workbench::ui.tw-graph.documentation-links example="canvas.canvas-default-trunk" />

        <flux:separator :text="__('Code examples')" />

        <x-translation-workbench::ui.tw-graph.code-box
            class="">{{ $canvasSource->example('canvas-default-trunk-1') }}</x-translation-workbench::ui.tw-graph.code-box>

        <flux:separator
            class="mt-4"
            :text="__('Props used in this example')"
        />

        <flux:callout color="indigo">
            <flux:callout.heading icon="variable">
                {{ __('Canvas Default + Trunk Props') }}
            </flux:callout.heading>
            <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">

                {{-- Props-Table --}}
                <flux:table container:class="max-h-80">
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-900"
                        sticky
                    >
                        <flux:table.column>{{ __('Prop') }}</flux:table.column>
                        <flux:table.column>{{ __('Default') }}</flux:table.column>
                        <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>graph-id</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>{{ __('auto id') }}</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Stable DOM id and registry scope for anchors, bounds, DEV counters, and canvas metrics.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>:dev</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>false</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Enables DEV rendering such as node counters, debug boxes, and reduced graph opacity.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>:coordinates</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                <code>false</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Renders coordinate guides and measurement badges when DEV mode is enabled. Preview tools control their visibility; geometry calculations run independently.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>id
                                    (strang.trunk)</code></flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top"><code>null</code>
                            </flux:table.cell>
                            <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                {{ __('Explicit root ID of the trunk; used to derive its child component and anchor IDs.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:callout>

        <flux:callout.heading
            class="mt-6"
            icon="eye"
        >
            {{ __('Using the preview tools') }}
        </flux:callout.heading>
        <flux:callout.text class="mt-2 hyphens-auto text-justify">
            {{ __('The preview-tools component wraps one or more graph previews and provides the shared control bar. Try the controls on the default trunk alongside this explanation. They change diagnostic visibility immediately in the browser; they do not change lengths, anchors, collision calculations or the graph source.') }}
        </flux:callout.text>

        {{-- Table Using Preview Tools --}}
        <flux:callout
            class="mt-4"
            color="indigo"
        >
            <flux:callout.heading icon="pickaxe">
                {{ __('Preview tools controls') }}
            </flux:callout.heading>
            <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                {{-- Table --}}
                <flux:table container:class="max-h-80">
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-900"
                        sticky
                    >
                        <flux:table.column>{{ __('Control') }}</flux:table.column>
                        <flux:table.column>{{ __('Effect') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        <flux:table.row>
                            <flux:table.cell class="whitespace-normal align-top">{{ __('DEV mode') }}</flux:table.cell>
                            <flux:table.cell class="whitespace-normal align-top">
                                {{ __('Master switch for all diagnostic overlays, including node counters, Bounding boxes, Coordinates and Grid X/Y. Turning it off hides all diagnostics and restores normal graph opacity without changing the individual selections. Turning it back on restores the selected overlays.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="whitespace-normal align-top">{{ __('Bounding boxes') }}
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-normal align-top">
                                {{ __('Shows the dashed diagnostic rectangles around components and labels. These help inspect occupied space, multiline labels and overlaps. Requires DEV mode, but is independent of Coordinates. Includes the calculated content bounds of the entire graph. Hiding the rectangles does not change the bounds used for layout, and does not hide the separate canvas dimension guides.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="whitespace-normal align-top">{{ __('Coordinates') }}
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-normal align-top">
                                {{ __('Shows the available coordinate guides and measurement badges for the rendered graph bounds. The yellow guides additionally explain canvas dimensions, minimum sizes and padding; they are not the bounding box of an individual component. Requires DEV mode and coordinate diagnostics rendered by the graph.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="whitespace-normal align-top">{{ __('Grid X/Y') }}</flux:table.cell>
                            <flux:table.cell class="whitespace-normal align-top">
                                {{ __('Shows a 1rem grid with stronger lines and coordinate values every 5rem. X points right, Y points up. The origin (0|0) follows the graph origin, including padding and negative coordinates. Requires DEV mode; independent of Coordinates; does not affect bounds or intercept clicks.') }}
                            </flux:table.cell>
                        </flux:table.row>
                        <flux:table.row>
                            <flux:table.cell class="whitespace-normal align-top">{{ __('Refresh preview ↻') }}
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-normal align-top">
                                {{ __('Re-renders the containing Livewire documentation component on the server. The active example, its source code box and its graph are rendered again without switching tabs or reloading the whole browser page. The active tab and saved visibility settings remain selected. The button is disabled while its refresh request is running; outside a Livewire component it has no refresh action.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:callout>

        <flux:callout.text class="mt-2">
            {{ __('The DEV legend below the controls explains diagnostic line colors and dash patterns. It only shows entries for enabled overlays and disappears when DEV mode is off. Graph path colors are not part of this legend.') }}
        </flux:callout.text>
        <figure class="mt-4">
            <figcaption class="mb-3 text-sm text-zinc-600 dark:text-zinc-300">
                <flux:callout.text>
                    {{ __('Static example — no function. DEV mode and Bounding boxes are selected; Coordinates and Grid X/Y are off. The refresh icon sits at the far right.') }}
                </flux:callout.text>
            </figcaption>
            <div
                class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700"
                inert
            >
                <flux:fieldset class="rounded-lg bg-white p-3 dark:bg-zinc-900">
                    <div class="flex items-center gap-4 *:gap-x-2">
                        <flux:toggle
                            size="sm"
                            color="cyan"
                            icon="code-bracket"
                            checked
                            label="{{ __('DEV mode') }}"
                        />
                        <flux:toggle
                            size="sm"
                            color="cyan"
                            icon="square-2-stack"
                            checked
                            label="{{ __('Bounding boxes') }}"
                        />
                        <flux:toggle
                            size="sm"
                            color="cyan"
                            icon="arrows-pointing-out"
                            label="{{ __('Coordinates') }}"
                        />
                        <flux:toggle
                            size="sm"
                            color="cyan"
                            icon="squares-2x2"
                            label="{{ __('Grid X/Y') }}"
                        />
                        <div class="ml-auto shrink-0">
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                square
                                icon="arrow-path"
                                :aria-label="__('Refresh preview')"
                            />
                        </div>
                    </div>
                </flux:fieldset>
            </div>
        </figure>
        <flux:callout.heading
            class="mt-4"
            icon="library"
        >
            {{ __('Start values and saved settings') }}
        </flux:callout.heading>
        <flux:callout.text class="mt-2 hyphens-auto text-justify">
            {{ __('On preview-tools, :dev defaults to true and :coordinates defaults to false; Bounding boxes starts enabled; :grid defaults to false. Grid X/Y requires DEV mode and works independently of Coordinates. These are initial visibility values only. Alpine Persist stores the four selections in localStorage under tw-graph-preview-dev, tw-graph-preview-boxes, tw-graph-preview-coordinates and tw-graph-preview-grid. Previously saved values take precedence, including after a reload. All previews using these keys share the preferences in this browser for this site; they are not saved in the database or synchronized to other devices.') }}
        </flux:callout.text>

        <flux:callout.heading
            class="mt-4"
            icon="chart-network"
        >{{ __('Render diagnostics first, then control their visibility') }}</flux:callout.heading>
        <flux:callout.text class="mt-2">
            {{ __('The graph itself must render the diagnostics before the toolbar can reveal them. Documentation previews therefore set :dev="true" and :coordinates="true" on tw-graph. The wrapper can initially hide them. If the graph was rendered with these props disabled, switching the toolbar on cannot create the missing diagnostic elements. Both code examples are extracted from the actual preview below: the first shows the graph, the second includes its preview-tools wrapper.') }}
        </flux:callout.text>

        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $canvasSource->example('canvas-preview-tools') }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:callout
            class="mt-4"
            color="indigo"
            icon="information-circle"
        >
            <flux:callout.heading>{{ __('Try it here') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Enable DEV mode, then toggle Bounding boxes and Coordinates separately. Leave Coordinates enabled and turn DEV mode off and on to see the master switch in action. Switch to another example to check the saved settings. After editing and saving an example, use the refresh icon at the far right to render the current documentation again.') }}
            </flux:callout.text>
        </flux:callout>
    </flux:callout>

    <flux:callout
        class="min-w-0"
        color="emerald"
    >
        <flux:callout.heading icon="square-dashed-text">
            {{ __('Preview') }}
        </flux:callout.heading>
        {{-- canvas-preview-tools:start --}}
        <x-translation-workbench::ui.tw-graph.preview-tools
            :dev="$dev ?? true"
            :coordinates="$coordinates ?? false"
        >
            <div
                class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                <div class="min-w-0">
                    {{-- canvas-default-trunk-1:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-canvas-default-trunk"
                        :dev="true"
                        :coordinates="true"
                    >
                        <x-translation-workbench::ui.tw-graph.strang.trunk id="literature.center.1.paper" />
                    </x-translation-workbench::ui.tw-graph>
                    {{-- canvas-default-trunk-1:end --}}
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        {{-- canvas-preview-tools:end --}}
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/canvas/canvas-default-trunk.blade.php
        </flux:field>
    </flux:callout>
</section>
