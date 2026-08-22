{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/graph-v2/path-catalog-card.blade.php --}}

<flux:card class="dark:bg-zinc-800">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <span class="font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-100">
            {{ $row['name'] }}
        </span>
        <flux:badge
            size="sm"
            color="sky"
        >
            {{ __('path') }}
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
                width: 18rem;
                min-width: 18rem;
                height: 13rem;
                min-height: 13rem;
            "
        >
            {{-- Component Calls / Props --}}
            @switch($row['view'])
                @case('path-trunk-bottom-top')
                    <x-translation-workbench::ui.tw-graph.paths.trunk
                        id="catalog.path.trunk.bottom-top"
                        direction="bottom-top"
                        :anchor-start="['x' => '0rem', 'y' => '0.75rem']"
                        start-length="2.5rem"
                        :path-lengths="[['3rem', ['Section A|left', null]], ['3rem', [null, 'Section B|right']]]"
                        end-length="3rem"
                        color="emerald"
                        :dev="true"
                    />
                @break

                @case('path-trunk-left-right')
                    <x-translation-workbench::ui.tw-graph.paths.trunk
                        id="catalog.path.trunk.left-right"
                        direction="left-right"
                        :anchor-start="['x' => '-7rem', 'y' => '4.75rem']"
                        start-length="3rem"
                        :path-lengths="[
                            ['3rem', ['Step A|top', null]],
                            ['3rem', [null, 'Step B|bottom']],
                            ['5rem', ['Step C|top', 'Step D|bottom']],
                        ]"
                        end-length="2.5rem"
                        color="cyan"
                        :dev="true"
                    />
                @break

                @case('path-merge-left')
                    <x-translation-workbench::ui.tw-graph.paths.merge
                        id="catalog.path.merge.left"
                        side="left"
                        :anchor-start="['x' => '-8.5rem', 'y' => '0.75rem']"
                        start-length="2rem"
                        stem-length="2rem"
                        bridge-length="3rem"
                        color="amber"
                        :dev="true"
                    />
                @break

                @case('path-merge-right')
                    <x-translation-workbench::ui.tw-graph.paths.merge
                        id="catalog.path.merge.right"
                        side="right"
                        :anchor-start="['x' => '8.5rem', 'y' => '0.75rem']"
                        start-length="2rem"
                        stem-length="2rem"
                        bridge-length="3rem"
                        color="green"
                        :dev="true"
                    />
                @break

                @case('path-merge-extension-left')
                    <x-translation-workbench::ui.tw-graph.paths.merge-extension
                        id="catalog.path.merge-extension.left"
                        side="left"
                        :anchor-start="['x' => '-7rem', 'y' => '0.75rem']"
                        start-length="2rem"
                        stem-length="2rem"
                        bridge-length="4rem"
                        color="sky"
                        :dev="true"
                    />
                @break

                @case('path-merge-extension-right')
                    <x-translation-workbench::ui.tw-graph.paths.merge-extension
                        id="catalog.path.merge-extension.right"
                        side="right"
                        :anchor-start="['x' => '7rem', 'y' => '0.75rem']"
                        start-length="2rem"
                        stem-length="2rem"
                        bridge-length="4rem"
                        color="rose"
                        :dev="true"
                    />
                @break

                @case('path-branch-left')
                    <x-translation-workbench::ui.tw-graph.paths.branch
                        id="catalog.path.branch.left"
                        side="left"
                        :anchor-start="['x' => '7rem', 'y' => '0.75rem']"
                        bridge-length="3rem"
                        stem-length="4rem"
                        color="pink"
                        :dev="true"
                    />
                @break

                @case('path-branch-right')
                    <x-translation-workbench::ui.tw-graph.paths.branch
                        id="catalog.path.branch.right"
                        side="right"
                        :anchor-start="['x' => '-7rem', 'y' => '0.75rem']"
                        bridge-length="3rem"
                        stem-length="4rem"
                        color="violet"
                        :dev="true"
                    />
                @break

                @case('path-branch-extension-left')
                    <x-translation-workbench::ui.tw-graph.paths.branch-extension
                        id="catalog.path.branch-extension.left"
                        side="left"
                        :anchor-start="['x' => '6rem', 'y' => '2rem']"
                        bridge-length="3rem"
                        stem-length="4rem"
                        color="rose"
                        :dev="true"
                    />
                @break

                @case('path-branch-extension-right')
                    <x-translation-workbench::ui.tw-graph.paths.branch-extension
                        id="catalog.path.branch-extension.right"
                        side="right"
                        :anchor-start="['x' => '-6rem', 'y' => '2rem']"
                        bridge-length="3rem"
                        stem-length="4rem"
                        color="indigo"
                        :dev="true"
                    />
                @break

                @case('path-branch-return-left')
                    <x-translation-workbench::ui.tw-graph.paths.branch-return
                        id="catalog.path.branch-return.left"
                        side="left"
                        :anchor-start="['x' => '-7rem', 'y' => '0.75rem']"
                        bridge-length="3rem"
                        color="orange"
                        :dev="true"
                    />
                @break

                @case('path-branch-return-right')
                    <x-translation-workbench::ui.tw-graph.paths.branch-return
                        id="catalog.path.branch-return.right"
                        side="right"
                        :anchor-start="['x' => '7rem', 'y' => '0.75rem']"
                        bridge-length="3rem"
                        color="teal"
                        :dev="true"
                    />
                @break

                @case('path-branch-return-bridge-left')
                    <x-translation-workbench::ui.tw-graph.paths.branch-return-bridge
                        id="catalog.path.branch-return-bridge.left"
                        side="left"
                        :anchor-start="['x' => '-6rem', 'y' => '1rem']"
                        bridge-length="4rem"
                        :node-labels="[1 => ['top' => 'Arc'], 2 => ['bottom' => 'Bridge']]"
                        color="amber"
                        :dev="true"
                    />
                @break

                @case('path-branch-return-bridge-right')
                    <x-translation-workbench::ui.tw-graph.paths.branch-return-bridge
                        id="catalog.path.branch-return-bridge.right"
                        side="right"
                        :anchor-start="['x' => '6rem', 'y' => '1rem']"
                        bridge-length="4rem"
                        :node-labels="[1 => ['top' => 'Arc'], 2 => ['bottom' => 'Bridge']]"
                        color="emerald"
                        :dev="true"
                    />
                @break

                @case('path-branch-return-extension-left')
                    <x-translation-workbench::ui.tw-graph.paths.branch-return-extension
                        id="catalog.path.branch-return-extension.left"
                        side="left"
                        :anchor-start="['x' => '-6rem', 'y' => '0.75rem']"
                        stem-length="2rem"
                        bridge-length="3rem"
                        color="yellow"
                        :dev="true"
                    />
                @break

                @case('path-branch-return-extension-right')
                    <x-translation-workbench::ui.tw-graph.paths.branch-return-extension
                        id="catalog.path.branch-return-extension.right"
                        side="right"
                        :anchor-start="['x' => '6rem', 'y' => '0.75rem']"
                        stem-length="2rem"
                        bridge-length="3rem"
                        color="lime"
                        :dev="true"
                    />
                @break
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
                                $isIndentedStructureItem =
                                    is_string($structureItem) && str_starts_with($structureItem, $tab);
                                $structureText = $isIndentedStructureItem
                                    ? substr($structureItem, strlen($tab))
                                    : $structureItem;
                                $indentLevel = 0;
                                if ($isIndentedStructureItem) {
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
                                @if (!$isTreeStructureItem && $indentLevel > 0)
                                    <span
                                        class="inline-block shrink-0"
                                        style="width: {{ $indentLevel }}rem;"
                                    ></span>
                                @endif
                                <span class="whitespace-pre">@if (filled($structureQualifier)){{ $structureTreePrefix }}<span class="text-amber-700 dark:text-amber-300">{{ $structureQualifier }}</span>{{ $structureBody }}@else{{ $structureText }}@endif</span>
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
