<x-translation-workbench::ui.common.heading-counter-group group="segments-start-end">
    <section class="mt-4 grid min-w-0 gap-4 lg:grid-cols-2">

        {{-- CodeBox And Props Table --}}
        <flux:callout
            class="min-w-0"
            color="indigo"
            icon="file-text"
        >
            <flux:callout.heading>{{ __('Start and end segments') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Start and end segments shown in both vertical directions. Start fades in and ends at a dot; end starts at a dot and finishes with a cap. Both delegate the line and nodes to segments.path. Their configuration is passed through the segment array; anchor coordinates must match direction and length. The labels mark the starting or ending anchor.') }}
            </flux:callout.text>
            @php
                $startEndExampleSource = \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ExampleSource::fromView(
                    'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.segments.segments-start-end',
                );
            @endphp
            <flux:separator
                class="mt-4"
                :text="__('Code examples')"
            />
            <flux:accordion
                transition
                exclusive
            >
                <flux:accordion.item expanded>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="start-bottom-top"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Start · bottom-top') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $startEndExampleSource->example('segment-start-bottom-top') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="start-top-bottom"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('Start · top-bottom') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $startEndExampleSource->example('segment-start-top-bottom') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="end-bottom-top"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('End · bottom-top') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $startEndExampleSource->example('segment-end-bottom-top') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:callout
                        icon="code"
                        color="indigo"
                    >
                        <x-translation-workbench::ui.common.heading-counter
                            example="end-top-bottom"
                            variant="accordion"
                            :prefix-text="__('Complete example')"
                        >{{ __('End · top-bottom') }}</x-translation-workbench::ui.common.heading-counter>
                    </flux:callout>
                    <flux:accordion.content>
                        <x-translation-workbench::ui.tw-graph.code-box
                            class="mt-3">{{ $startEndExampleSource->example('segment-end-top-bottom') }}</x-translation-workbench::ui.tw-graph.code-box>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>
            <flux:separator
                class="mt-4"
                :text="__('Props used in these examples')"
            />
            <flux:callout color="indigo">
                <flux:callout.heading icon="variable">{{ __('Start and end segment props') }}</flux:callout.heading>
                <div class="mt-3 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <flux:table container:class="max-h-80">
                        <flux:table.columns
                            class="bg-white dark:bg-zinc-900"
                            sticky
                        >
                            <flux:table.column>{{ __('Prop / segment key') }}</flux:table.column>
                            <flux:table.column>{{ __('Start default') }}</flux:table.column>
                            <flux:table.column>{{ __('End default') }}</flux:table.column>
                            <flux:table.column>{{ __('Purpose') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>:segment</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Component prop: configuration array. The following rows document keys inside this array.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}

                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>id</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>segment.start</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>segment.end</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Identifier for the segment and its child elements.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>direction</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bottom-top</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bottom-top</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Flow direction. These examples show bottom-top and top-bottom.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>length</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>4rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Line length; must agree with the distance between the explicit anchors.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchorStart</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;0rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Starting coordinates.') }}
                                </flux:table.cell>
                            </flux:table.row>

                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>anchorEnd</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;4rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>[&#x27;x&#x27; =&gt; &#x27;0rem&#x27;, &#x27;y&#x27; =&gt; &#x27;4rem&#x27;]</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Endpoint coordinates; not recalculated when direction or length changes.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>nodeStart</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Show a start dot; an array can supply up to two node labels.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>nodeEnd</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('false (enforced)') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Start can show an end dot or node labels. End always disables its end node.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>gradient</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>true</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Fade in the line from its starting anchor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>cap</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('true (enforced)') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('End enables the cap; capEnd can explicitly override its visibility.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>capStart</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional cap at the starting anchor, passed through to segments.path.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>capEnd</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Overrides the end-cap visibility; null follows cap.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>capLength</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>1.25rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>1.25rem</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Length of a visible cap.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>color</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>zinc</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Line and node color; also the default label badge color.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>startLabel</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>unset</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('not used') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Start-only label configuration at anchorStart: text, side, offset, width, align, badge, badgeColor, justify.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>startLabel.side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>bottom</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('not used') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Set top explicitly for a downward start to keep the label outside the line.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>endLabel</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('not used') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>unset</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('End-only label configuration at anchorEnd: text, side, offset, width, align, badge, badgeColor, justify, maxLines.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>endLabel.side</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('not used') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('from direction') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Defaults to top for bottom-top and bottom for top-bottom; can be overridden.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>startLabel.offset</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('inherited labelGap / graph label_offset') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('not used') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Distance between the start label and its anchor; fallback 0.75rem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>endLabel.offset</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('not used') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('inherited labelGap / graph label_offset') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Distance between the end label and its anchor; fallback 0.75rem.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>startLabel.width</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>default</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('not used') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Label widths: half, default, halfLong, long.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>endLabel.width</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('not used') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>default</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Label widths: half, default, halfLong, long.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>startLabel.align</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>center</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('not used') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Text alignment inside the label.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>endLabel.align</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('not used') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>center</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Text alignment inside the label.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>endLabel.maxLines</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>{{ __('not used') }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>3</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Maximum visible lines in the end label.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>dashed</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional dashed line, passed through to segments.path.') }}
                                </flux:table.cell>
                            </flux:table.row>

                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>toColor</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Target color when colorGradient is enabled.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>colorGradient</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>false</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Enable the gradient between color and toColor.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>tone</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>line</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>line</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Palette tone passed through to segments.path.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}
                            <flux:table.row>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>zIndex</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    <code>null</code>
                                </flux:table.cell>
                                <flux:table.cell class="wrap-break-word whitespace-normal align-top">
                                    {{ __('Optional stacking-order override.') }}
                                </flux:table.cell>
                            </flux:table.row>
                            {{-- Row --}}

                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:callout>
        </flux:callout>
        {{-- Preview --}}
        <flux:callout
            class="min-w-0"
            color="emerald"
        >
            <flux:callout.heading icon="eye">
                {{ __('Start and end segment preview') }}
            </flux:callout.heading>
            <x-translation-workbench::ui.tw-graph.preview-tools
                :dev="$dev ?? true"
                :coordinates="$coordinates ?? false"
            >
                <div class="mt-4 grid min-w-0 gap-4 xl:grid-cols-2">
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="start-bottom-top"
                            size="sm"
                        >
                            {{ __('Start · bottom-top') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-start-bottom-top:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-segments-start-bottom-top"
                                :dev="true"
                                :coordinates="true"
                                min-height="18rem"
                                min-width="24rem"
                                horizontal-padding="10rem"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.start
                                    :segment="[
                                        'id' => 'literature.segments.start.bottom-top',
                                        'direction' => 'bottom-top',
                                        'length' => '8rem',
                                        'anchorStart' => ['x' => '0rem', 'y' => '4rem'],
                                        'anchorEnd' => ['x' => '0rem', 'y' => '12rem'],
                                        'startLabel' => [
                                            'text' => ['Start'],
                                            'side' => 'bottom',
                                            'width' => 'half',
                                            'align' => 'center',
                                        ],
                                        'color' => 'cyan',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-start-bottom-top:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="start-top-bottom"
                            size="sm"
                        >
                            {{ __('Start · top-bottom') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-start-top-bottom:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-segments-start-top-bottom"
                                :dev="true"
                                :coordinates="true"
                                min-height="18rem"
                                min-width="24rem"
                                horizontal-padding="10rem"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.start
                                    :segment="[
                                        'id' => 'literature.segments.start.top-bottom',
                                        'direction' => 'top-bottom',
                                        'length' => '8rem',
                                        'anchorStart' => ['x' => '0rem', 'y' => '12rem'],
                                        'anchorEnd' => ['x' => '0rem', 'y' => '4rem'],
                                        'startLabel' => [
                                            'text' => ['Start'],
                                            'side' => 'top',
                                            'width' => 'half',
                                            'align' => 'center',
                                        ],
                                        'color' => 'emerald',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-start-top-bottom:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="end-bottom-top"
                            size="sm"
                        >
                            {{ __('End · bottom-top') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-end-bottom-top:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-segments-end-bottom-top"
                                :dev="true"
                                :coordinates="true"
                                min-height="18rem"
                                min-width="24rem"
                                horizontal-padding="10rem"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.end
                                    :segment="[
                                        'id' => 'literature.segments.end.bottom-top',
                                        'direction' => 'bottom-top',
                                        'length' => '8rem',
                                        'anchorStart' => ['x' => '0rem', 'y' => '4rem'],
                                        'anchorEnd' => ['x' => '0rem', 'y' => '12rem'],
                                        'endLabel' => [
                                            'text' => ['End'],
                                            'side' => 'top',
                                            'width' => 'half',
                                            'align' => 'center',
                                        ],
                                        'color' => 'fuchsia',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-end-bottom-top:end --}}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <x-translation-workbench::ui.common.heading-counter
                            example="end-top-bottom"
                            size="sm"
                        >
                            {{ __('End · top-bottom') }}
                        </x-translation-workbench::ui.common.heading-counter>
                        <div
                            class="mt-3 overflow-x-auto rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40">
                            {{-- segment-end-top-bottom:start --}}
                            <x-translation-workbench::ui.tw-graph
                                graph-id="idea-to-paper-segments-end-top-bottom"
                                :dev="true"
                                :coordinates="true"
                                min-height="18rem"
                                min-width="24rem"
                                horizontal-padding="10rem"
                            >
                                <x-translation-workbench::ui.tw-graph.segments.end
                                    :segment="[
                                        'id' => 'literature.segments.end.top-bottom',
                                        'direction' => 'top-bottom',
                                        'length' => '8rem',
                                        'anchorStart' => ['x' => '0rem', 'y' => '12rem'],
                                        'anchorEnd' => ['x' => '0rem', 'y' => '4rem'],
                                        'endLabel' => [
                                            'text' => ['End'],
                                            'side' => 'bottom',
                                            'width' => 'half',
                                            'align' => 'center',
                                        ],
                                        'color' => 'amber',
                                    ]"
                                />
                            </x-translation-workbench::ui.tw-graph>
                            {{-- segment-end-top-bottom:end --}}
                        </div>
                    </div>
                </div>
            </x-translation-workbench::ui.tw-graph.preview-tools>
            <flux:field class="m-3 flex justify-end font-mono text-xs text-zinc-400">
                .../idea-to-paper/segments/segments-start-end.blade.php
            </flux:field>
        </flux:callout>
    </section>
</x-translation-workbench::ui.common.heading-counter-group>
