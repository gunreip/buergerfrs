{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/graph-v2/segments-catalog-card.blade.php --}}

<flux:card class="dark:bg-zinc-800">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <span class="font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-100">
            {{ $row['name'] }}
        </span>
        <flux:badge
            size="sm"
            color="amber"
        >
            {{ __('segment') }}
        </flux:badge>
    </div>

    {{-- Component Canvas Rendering --}}
    <div class="my-24 flex justify-center">
        <div
            class="tw-graph-protocol tw-graph-protocol-catalog-preview"
            style="
                --tw-graph-protocol-color-rgb: 6 182 212;
                --tw-graph-protocol-color-alpha: 0.5;
                --tw-graph-protocol-path-width: 0.25rem;
                --tw-graph-protocol-path-half: calc(var(--tw-graph-protocol-path-width) / 2);
                --tw-graph-protocol-text-connector-width: calc(var(--tw-graph-protocol-path-width) / 2);
                --tw-graph-protocol-text-connector-anchor-gap: 0.25rem;
                --tw-graph-protocol-node-size: 1rem;
                --tw-graph-protocol-node-half: calc(var(--tw-graph-protocol-node-size) / 2);
                --tw-graph-protocol-arc-size: 2.75rem;
                --tw-graph-protocol-arc-radius: var(--tw-graph-protocol-arc-size);
                --tw-graph-protocol-trunk-x: 50%;
            "
        >
            @switch($row['view'])
                @case('path')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.path :segment="$pathSegment" />
                @break

                @case('path-top-bottom')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.path :segment="$pathSegmentTopBottom" />
                @break

                @case('start')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.start :segment="$startSegment" />
                @break

                @case('start-right-left')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.start :segment="$startSegmentRightLeft" />
                @break

                @case('end')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.end :segment="$endSegment" />
                @break

                @case('end-left-right')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.end :segment="$endSegmentLeftRight" />
                @break

                @case('arc-north-west')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.arc :segment="$arcNorthWestSegment" />
                @break

                @case('arc-west-north')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.arc :segment="$arcWestNorthSegment" />
                @break

                @case('arc-north-east')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.arc :segment="$arcNorthEastSegment" />
                @break

                @case('arc-east-north')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.arc :segment="$arcEastNorthSegment" />
                @break

                @case('arc-west-south')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.arc :segment="$arcWestSouthSegment" />
                @break

                @case('arc-south-west')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.arc :segment="$arcSouthWestSegment" />
                @break

                @case('arc-east-south')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.arc :segment="$arcEastSouthSegment" />
                @break

                @case('arc-south-east')
                    <x-translation-workbench::ui.tw-graph-protocol.segments.arc :segment="$arcSouthEastSegment" />
                @break

                @case('label')
                    @foreach ($packageLabelDirections as $directionLabel)
                        <x-translation-workbench::ui.tw-graph-protocol.segments.label
                            :id="$directionLabel['id']"
                            :label="$directionLabel"
                            anchor-x="0rem"
                            anchor-y="3rem"
                            :side="$directionLabel['side']"
                            :color="$directionLabel['color']"
                        />
                    @endforeach
                    <x-translation-workbench::ui.tw-graph-protocol.primitives.line
                        id="catalog.segment.label.anchor"
                        direction="bottom-top"
                        length="0.1rem"
                        start-x="0rem"
                        start-y="3rem"
                        end-x="0rem"
                        end-y="3rem"
                        :node-start="true"
                        :node-end="false"
                        color="zinc"
                    />
                @break

                {{--
                    TODO TW-GRAPH cleanup:
                    Old segment fallback cases kept inactive until the package-local
                    tw-graph-protocol segment catalogue has been validated.

                    @case('trunk-start')
                        <x-ui.tw-graph-protocol.segments.trunk-start :segment="$previewSegment" />
                    @break

                    @case('trunk-path')
                        <x-ui.tw-graph-protocol.segments.trunk-path :segment="array_replace($previewSegment, ['nodeStart' => true])" />
                    @break

                    @case('trunk-end')
                        <x-ui.tw-graph-protocol.segments.trunk-end :segment="array_replace($previewSegment, ['nodeStart' => true, 'nodeEnd' => false])" />
                    @break

                    @case('text-label')
                        <x-ui.tw-graph-protocol.segments.text-label
                            :label="$label"
                            :segment="$labelSegment"
                        />
                    @break

                    @case('merge-end-path')
                        <x-ui.tw-graph-protocol.segments.merge-end-path :segment="$mergeEndSegment" />
                    @break

                    @case('merge-arc-se')
                        <x-ui.tw-graph-protocol.segments.merge-arc-se :segment="$mergeArcSegment" />
                    @break

                    @case('merge-arc-sw')
                        <x-ui.tw-graph-protocol.segments.merge-arc-sw :segment="array_replace($mergeArcSegment, [
                            'direction' => 'sw',
                            'anchorEnd' => ['x' => '2.5rem', 'y' => '2rem'],
                        ])" />
                    @break

                    @case('merge-arc-nw')
                        <x-ui.tw-graph-protocol.segments.merge-arc-nw :segment="array_replace($mergeArcSegment, [
                            'direction' => 'nw',
                            'anchorStart' => ['x' => '-2.5rem', 'y' => '4.5rem'],
                            'anchorEnd' => ['x' => '0rem', 'y' => '2rem'],
                        ])" />
                    @break

                    @case('merge-arc-ne')
                        <x-ui.tw-graph-protocol.segments.merge-arc-ne :segment="array_replace($mergeArcSegment, [
                            'direction' => 'ne',
                            'anchorStart' => ['x' => '2.5rem', 'y' => '4.5rem'],
                            'anchorEnd' => ['x' => '0rem', 'y' => '2rem'],
                        ])" />
                    @break

                    @case('merge-path')
                        <x-ui.tw-graph-protocol.segments.merge-path :segment="$mergePathSegment" />
                    @break

                    @case('merge-end-text')
                        <x-ui.tw-graph-protocol.segments.merge-end-text
                            :path="$mergeEndPath"
                            :segment="$mergeEndSegment"
                            side="left"
                        />
                    @break
                --}}

            @endswitch
        </div>
    </div>

    {{-- Component Call --}}
    <div class="mt-3 grid gap-3 border-t border-zinc-200 pt-2 text-[0.7rem] dark:border-zinc-700">
        <div>
            <flux:callout
                color="green"
                icon="code-xml"
            >
                <flux:callout.heading>
                    {{ __('Component Call') }}
                </flux:callout.heading>
                <flux:callout.text>
                    <flux:text
                        class="mt-2 font-mono"
                        color="sky"
                    >
                        {{ $row['component'] }}
                    </flux:text>
                </flux:callout.text>
            </flux:callout>
        </div>

        {{-- Component Structure --}}
        <div>
            <flux:callout
                color="red"
                icon="list-tree"
            >
                <flux:callout.heading>
                    {{ __('Structure plan') }}
                </flux:callout.heading>
                <flux:callout.text>
                    <div class="mt-2 space-y-1 font-mono text-xs">
                        @foreach ($row['structure'] ?? [__('Structure plan missing')] as $structureItem)
                            @php
                                $isTreeStructureItem =
                                    is_string($structureItem) && str_starts_with($structureItem, '|');
                                $isPartStructureItem =
                                    is_string($structureItem) && str_starts_with($structureItem, $part);
                                $isIndentedStructureItem =
                                    is_string($structureItem) && str_starts_with($structureItem, $tab);
                                $structureText = $isIndentedStructureItem
                                    ? substr($structureItem, strlen($tab))
                                    : $structureItem;
                                $indentLevel = 0;
                                if ($isTreeStructureItem) {
                                    $structureText = $structureItem;
                                } elseif ($isPartStructureItem) {
                                    $structureText = substr($structureItem, strlen($part));
                                } elseif ($isIndentedStructureItem) {
                                    $remainingStructureItem = $structureItem;
                                    while (str_starts_with($remainingStructureItem, $tab)) {
                                        $indentLevel++;
                                        $remainingStructureItem = substr($remainingStructureItem, strlen($tab));
                                    }
                                    $structureText = $remainingStructureItem;
                                }
                                $structureTreePrefix = '';
                                $structureQualifier = '';
                                $structureBody = $structureText;
                                foreach (['dev-mode ', 'optional '] as $qualifier) {
                                    $qualifierPosition = is_string($structureText)
                                        ? strpos($structureText, $qualifier)
                                        : false;
                                    if ($qualifierPosition !== false) {
                                        $structureTreePrefix = substr($structureText, 0, $qualifierPosition);
                                        $structureQualifier = $qualifier;
                                        $structureBody = substr(
                                            $structureText,
                                            $qualifierPosition + strlen($qualifier),
                                        );
                                        break;
                                    }
                                }
                            @endphp
                            <div class="flex leading-tight">
                                @if ($isPartStructureItem)
                                    <span class="inline-block w-4 shrink-0"></span>
                                @elseif (!$isTreeStructureItem && $indentLevel > 0)
                                    <span
                                        class="inline-block shrink-0"
                                        style="width: {{ $indentLevel }}rem;"
                                    ></span>
                                @endif
                                <span @class([
                                    'whitespace-pre',
                                    'text-emerald-700 dark:text-emerald-300' => $isPartStructureItem,
                                ])>@if (filled($structureQualifier)){{ $structureTreePrefix }}<span class="text-amber-700 dark:text-amber-300">{{ $structureQualifier }}</span>{{ $structureBody }}@else{{ $structureText }}@endif</span>
                            </div>
                        @endforeach
                    </div>
                </flux:callout.text>
            </flux:callout>
        </div>

        {{-- Component Purpose --}}
        <div>
            <flux:callout
                color="blue"
                icon="info"
            >
                <flux:callout.heading>
                    {{ __('Purpose') }}
                </flux:callout.heading>
                <flux:callout.text>
                    <div class="mt-2 space-y-1">
                        @foreach ($row['composition'] as $composition)
                            <flux:text>
                                {{ $composition }}
                            </flux:text>
                        @endforeach
                    </div>
                </flux:callout.text>
            </flux:callout>
        </div>

        {{-- Component Props --}}
        <div>
            <flux:callout
                color="fuchsia"
                icon="sliders-horizontal"
            >
                <flux:callout.heading>
                    {{ __('Props') }}
                </flux:callout.heading>
                <flux:callout.text>
                    <div class="mt-2 space-y-1">
                        @foreach ($row['props'] as $prop)
                            @php
                                $propText = str_starts_with($prop, $tab) ? substr($prop, strlen($tab)) : $prop;
                            @endphp
                            <flux:text class="font-mono leading-tight">
                                {{ $propText }}
                            </flux:text>
                        @endforeach
                    </div>
                </flux:callout.text>
            </flux:callout>
        </div>
    </div>
</flux:card>
