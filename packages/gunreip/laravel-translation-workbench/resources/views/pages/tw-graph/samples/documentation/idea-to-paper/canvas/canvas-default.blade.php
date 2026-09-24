@php
    $canvasSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
        'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.canvas.canvas-default',
    );
@endphp
<section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">

    {{-- CodeBox And Pros Table --}}
    <flux:callout
        class="min-w-0"
        color="indigo"
        icon="file-text"
    >
        <flux:callout.heading>
            {{ __('Default') }}
        </flux:callout.heading>
        <flux:callout.text>
            {{ __('Default renders an empty graph canvas exactly as configured by the graph defaults. No canvas prop changes the visual output here.') }}
        </flux:callout.text>

        <flux:separator
            class="mt-4"
            :text="__('Code examples')"
        />

        <x-translation-workbench::ui.tw-graph.code-box class="mt-4">{{ $canvasSource->example('canvas-default-1') }}</x-translation-workbench::ui.tw-graph.code-box>

        <flux:separator
            class="mt-4"
            :text="__('Props used in this example')"
        />

        {{-- Canvas Props --}}
        <flux:callout color="indigo">
            <flux:callout.heading icon="variable">
                {{ __('Canvas props') }}
            </flux:callout.heading>
            <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                {{-- Table --}}
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
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:callout>
    </flux:callout>

    {{-- Preview --}}
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
                    {{-- canvas-default-1:start --}}
                    <x-translation-workbench::ui.tw-graph
                        graph-id="idea-to-paper-canvas-default"
                        :dev="true"
                        :coordinates="true"
                    ></x-translation-workbench::ui.tw-graph>
                    {{-- canvas-default-1:end --}}
                </div>
            </div>
        </x-translation-workbench::ui.tw-graph.preview-tools>
        <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
            .../idea-to-paper/canvas/canvas-default.blade.php
        </flux:field>
    </flux:callout>
</section>
