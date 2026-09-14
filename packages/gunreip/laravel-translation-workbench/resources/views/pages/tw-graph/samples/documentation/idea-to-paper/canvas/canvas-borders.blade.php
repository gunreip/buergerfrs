<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">
    <flux:callout color="indigo" icon="file-text" class="min-w-0">
        <flux:callout.heading>{{ __('Borders') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('An empty canvas illustrates its minimum dimensions and padding. The yellow frame marks the minimum area; the dashed box marks the area inside the padding. The annotations are ordinary HTML and do not contribute graph bounds.') }}
        </flux:callout.text>
        @php
            $bordersSource = file_get_contents(\Illuminate\Support\Facades\View::getFinder()->find(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-borders',
            ));
            if (preg_match('/^[ \t]*\{\{-- canvas-borders-example:start --\}\}\R(.*?)^[ \t]*\{\{-- canvas-borders-example:end --\}\}/ms', $bordersSource, $bordersMatch) !== 1) {
                throw new \LogicException('The canvas borders example requires start and end markers.');
            }
            $bordersLines = explode("\n", rtrim($bordersMatch[1]));
            $bordersIndent = min(array_map(
                fn(string $line): int => strlen($line) - strlen(ltrim($line)),
                array_filter($bordersLines, fn(string $line): bool => trim($line) !== ''),
            ));
            $bordersCode = implode("\n", array_map(fn(string $line): string => substr($line, $bordersIndent), $bordersLines));
        @endphp
        <div class="mt-4 overflow-x-auto rounded-lg bg-zinc-950 p-4 text-xs leading-5 text-zinc-100">
            <pre><code>{{ $bordersCode }}</code></pre>
        </div>
        <div class="mt-4 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <flux:table container:class="max-h-80">
                <flux:table.columns>
                    <flux:table.column>{{ __('Prop') }}</flux:table.column>
                    <flux:table.column>{{ __('Default') }}</flux:table.column>
                    <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell class="align-top"><code>min-width</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal">calculated; empty canvas: 40rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Minimum width of the yellow frame (40rem here). The canvas may grow to fit its container or graph content.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top"><code>min-height</code></flux:table.cell>
                        <flux:table.cell class="align-top whitespace-normal">slot-min-height for slot content</flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Explicit minimum height (24rem here). Takes precedence over slot-min-height; content may require more height.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top"><code>slot-min-height</code></flux:table.cell>
                        <flux:table.cell class="align-top">config / 52rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Fallback minimum height for a non-empty slot. Set to 20rem here; only takes effect when min-height is omitted.</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top"><code>horizontal-padding</code></flux:table.cell>
                        <flux:table.cell class="align-top">config / 12rem</flux:table.cell>
                        <flux:table.cell class="whitespace-normal">Reserved on each side of content bounds (8rem here). It contributes to calculated width, not an additional margin outside min-width.</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
        <flux:text class="mt-3">{{ __('The vertical 2rem padding is currently fixed internally; there is no vertical-padding prop. DEV and coordinates are enabled permanently for this illustration.') }}</flux:text>
    </flux:callout>
    <flux:callout color="zinc" icon="square-dashed-text" class="min-w-0">
        <flux:callout.heading>{{ __('Preview') }}</flux:callout.heading>
        <div class="mt-4" data-canvas-borders-example>
            {{-- canvas-borders-example:start --}}
            <x-translation-workbench::ui.tw-graph
                graph-id="idea-to-paper-canvas-borders"
                min-width="40rem"
                min-height="24rem"
                slot-min-height="20rem"
                horizontal-padding="8rem"
                :dev="true"
                :coordinates="true"
            >
                <div class="canvas-borders-annotations">
                    <span class="canvas-borders-width">min-width = 40rem</span>
                    <span class="canvas-borders-height">min-height = 24rem</span>
                    <span class="canvas-borders-padding-left">horizontal-padding<br>8rem</span>
                    <span class="canvas-borders-padding-right">horizontal-padding<br>8rem</span>
                    <span class="canvas-borders-padding-top">2rem · fixed vertical padding</span>
                    <span class="canvas-borders-padding-bottom">2rem · fixed vertical padding</span>
                    <div class="canvas-borders-interior">
                        <strong>Area inside the padding</strong>
                        <span>slot-min-height = 20rem</span>
                        <span>Fallback when min-height is omitted.<br>Here min-height = 24rem takes precedence.</span>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph>
            {{-- canvas-borders-example:end --}}
        </div>
        <flux:text class="mt-3">{{ __('Only the yellow configuration frame is shown. The dashed box and its labels describe dimensions; they are not graph components.') }}</flux:text>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/canvas/canvas-borders.blade.php
        </flux:field>
    </flux:callout>
    <style>
        [data-canvas-borders-example] .tw-graph-protocol-coordinate-only:not(.tw-graph-protocol-canvas-dimensions),
        [data-canvas-borders-example] .tw-graph-protocol-canvas-dimensions-label {
            display: none;
        }
        [data-canvas-borders-example] .canvas-borders-annotations {
            position: absolute;
            bottom: 0;
            left: 0;
            width: var(--tw-graph-protocol-min-width);
            height: var(--tw-graph-protocol-min-height);
            color: rgb(202 138 4);
            font: 0.75rem/1.5 ui-monospace, monospace;
            pointer-events: none;
        }
        .dark [data-canvas-borders-example] .canvas-borders-annotations { color: rgb(250 204 21); }
        [data-canvas-borders-example] .canvas-borders-annotations > span { position: absolute; text-align: center; }
        [data-canvas-borders-example] .canvas-borders-width { top: 0.25rem; left: 50%; transform: translateX(-50%); }
        [data-canvas-borders-example] .canvas-borders-height { right: 0.25rem; top: 50%; writing-mode: vertical-rl; transform: translateY(-50%); }
        [data-canvas-borders-example] .canvas-borders-padding-left,
        [data-canvas-borders-example] .canvas-borders-padding-right { top: 50%; width: 7rem; overflow-wrap: anywhere; transform: translateY(-50%); }
        [data-canvas-borders-example] .canvas-borders-padding-left { left: 0.25rem; }
        [data-canvas-borders-example] .canvas-borders-padding-right { right: 1rem; }
        [data-canvas-borders-example] .canvas-borders-padding-top { top: 2.25rem; left: 50%; transform: translateX(-50%); }
        [data-canvas-borders-example] .canvas-borders-padding-bottom { bottom: 0.5rem; left: 50%; transform: translateX(-50%); }
        [data-canvas-borders-example] .canvas-borders-interior { position: absolute; inset: 2rem 8rem; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 1rem; text-align: center; padding: 1rem; }
    </style>
</section>
