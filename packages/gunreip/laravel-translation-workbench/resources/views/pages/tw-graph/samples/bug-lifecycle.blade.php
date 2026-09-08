{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/bug-lifecycle.blade.php --}}

<x-layouts::app :title="__('TW-Graph Sample: Bug Lifecycle')">
    <flux:card class="translation-workbench">
        <x-ui.headers.page
            :title="__('Bug Lifecycle')"
            :description="__('Hand-authored tw-graph sample for issue states, branches, returns, and resolution paths.')"
        />

        @php
            $bugLifecycleGraphId = 'tw-graph-sample-bug-lifecycle';
            $bugLifecycleDev = true;
            $bugLifecycleCoordinates = false;
        @endphp

        <div x-data="{ bugLifecycleDev: @js($bugLifecycleDev) }">
            <flux:callout
                class="mt-6"
                color="zinc"
                icon="bug"
            >
                <flux:callout.heading>
                    <span class="flex w-full flex-wrap items-center justify-between gap-3">
                        <span class="inline-flex flex-wrap items-center gap-2">
                            <span>{{ __('Bug lifecycle graph canvas') }}</span>
                            <flux:badge
                                size="sm"
                                color="zinc"
                            >
                                {{ $bugLifecycleGraphId }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="amber"
                            >
                                {{ __('hand-authored') }}
                            </flux:badge>
                        </span>
                        @if ($bugLifecycleDev)
                            <flux:field
                                class="items-center gap-2"
                                variant="inline"
                                x-on:click.stop
                            >
                                <flux:switch
                                    class="switch-colored hover:cursor-pointer"
                                    x-bind:checked="bugLifecycleDev"
                                    x-on:click="bugLifecycleDev = !bugLifecycleDev"
                                />
                                <flux:label class="text-xs opacity-70 hover:cursor-pointer">
                                    {{ __('DEV') }}
                                </flux:label>
                            </flux:field>
                        @endif
                    </span>
                </flux:callout.heading>
                <flux:callout.text>
                    {{ __('Manual tw-graph canvas for demonstrating a bug lifecycle with explicit parts and later branch/merge examples.') }}
                </flux:callout.text>

                <div
                    class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    x-bind:class="{ 'tw-graph-protocol-dev-disabled': !bugLifecycleDev }"
                >
                    <x-translation-workbench::ui.tw-graph
                        class="px-24 py-12"
                        :graph-id="$bugLifecycleGraphId"
                        :dev="$bugLifecycleDev"
                        :coordinates="$bugLifecycleCoordinates"
                        color="rose"
                        line-length="4rem"
                        bridge-length="12rem"
                        stem-length="6.5rem"
                        slot-min-height="64rem"
                        horizontal-padding="24rem"
                    >
                        {{-- trunk --}}
                        <x-translation-workbench::ui.tw-graph.strang.trunk
                            id="bug.center.1.trunk"
                            :stem-count="8"
                            start-length="5rem"
                            {{-- Explicit stem-length values override the automatic trunk-start shift. Use null to keep defaults. --}}
                            :stem-lengths="['1' => '7rem']"
                            end-length="3rem"
                            start-label="BUG-1842"
                            :end-label="['text' => ['Closed', 'released and monitored'], 'width' => 'halfLong']"
                            :start-node-labels="[
                                'left' => [
                                    'text' => 'Reported',
                                    'width' => 'halfLong',
                                    'align' => 'right',
                                    'color' => 'rose',
                                ],
                                'right' => [
                                    'text' => 'Customer reports checkout timeout after payment confirmation.',
                                    'width' => 'long',
                                    'align' => 'left',
                                    'justify' => true,
                                    'color' => 'zinc',
                                ],
                            ]"
                            :node-labels="[
                                3 => [
                                    'left' => [
                                        'text' => 'Triaged',
                                        'width' => 'halfLong',
                                        'align' => 'right',
                                        'color' => 'rose',
                                    ],
                                    'right' => [
                                        'text' => 'Support confirms scope and adds reproduction steps.',
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                4 => [
                                    'left' => [
                                        'text' => 'Reproduced',
                                        'width' => 'halfLong',
                                        'align' => 'right',
                                        'color' => 'rose',
                                    ],
                                    'right' => [
                                        'text' => 'Failing checkout replay isolates stale payment-session cache.',
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                5 => [
                                    'left' => [
                                        'text' => 'Root cause confirmed',
                                        'width' => 'halfLong',
                                        'align' => 'center',
                                        'color' => 'amber',
                                    ],
                                    'right' => [
                                        'text' => 'Fix path chosen: invalidate cache on final webhook transition.',
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                6 => [
                                    'left' => [
                                        'text' => 'Fixed',
                                        'width' => 'halfLong',
                                        'align' => 'right',
                                        'color' => 'rose',
                                    ],
                                    'right' => [
                                        'text' => 'Patch and regression tests cover retry and duplicate webhook delivery.',
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                7 => [
                                    'left' => [
                                        'text' => 'Reviewed',
                                        'width' => 'halfLong',
                                        'align' => 'right',
                                        'color' => 'rose',
                                    ],
                                    'right' => [
                                        'text' => 'Reviewer requests clearer telemetry around payment-session expiry.',
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                8 => [
                                    'left' => [
                                        'text' => 'Released',
                                        'width' => 'halfLong',
                                        'align' => 'right',
                                        'color' => 'rose',
                                    ],
                                    'right' => [
                                        'text' => 'Deployed behind monitoring, no checkout timeout spike after 24 hours.',
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                            ]"
                            color="rose"
                        />
                        {{-- duplicate-merge / left --}}
                        <x-translation-workbench::ui.tw-graph.strang.merge-left
                            id="bug.left.1.duplicate-merge"
                            attach-to="strang.trunk.node.3"
                            color="amber"
                            bridge-length="28rem"
                            :stem-lengths="[1 => '3rem']"
                            start-label="Duplicate report|BUG-1843"
                            :node-labels="[
                                1 => [
                                    'right' => [
                                        'text' => ['same stack trace', 'mobile checkout'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                                5 => [
                                    'left' => [
                                        'text' => ['merged into', 'BUG-1842'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                            ]"
                            :extension-count="1"
                            extension-stem-length="3rem"
                            extension-bridge-length="18rem"
                            :extension-node-labels="[
                                1 => [
                                    1 => [
                                        'right' => [
                                            'text' => ['Duplicate report', 'BUG-1844'],
                                            'width' => 'default',
                                            'align' => 'left',
                                        ],
                                    ],
                                    4 => [
                                        'left' => [
                                            'text' => ['same root cause', 'webhook retry'],
                                            'width' => 'default',
                                            'align' => 'right',
                                        ],
                                    ],
                                ],
                            ]"
                        />
                        {{-- needs-info / right --}}
                        <x-translation-workbench::ui.tw-graph.strang.branch-right
                            id="bug.right.1.needs-info"
                            attach-to="strang.trunk.node.2"
                            color="sky"
                            entry-stem-length="0.5rem"
                            bridge-length="34rem"
                            stem-length="4rem"
                            :node-labels="[
                                3 => [
                                    'right' => [
                                        'text' => ['Needs info', 'missing logs'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                            ]"
                            :step="[
                                'beforeLength' => '5.35rem',
                                'afterLength' => '5.35rem',
                                'stepLabel' => [
                                    'text' => ['Reporter adds logs', 'session id included'],
                                    'width' => 'halfLong',
                                ],
                            ]"
                            :stem-continuation="[
                                1 => [
                                    '3rem',
                                    'right' => [
                                        'text' => ['User reply', 'logs attached'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                            ]"
                            :branch-return="[
                                1 => [
                                    'attachTo' => 'stem.1',
                                    'bridgeLength' => '34rem',
                                    'color' => 'fuchsia',
                                ],
                            ]"
                        />
                        {{-- cannot-reproduce / left --}}
                        <x-translation-workbench::ui.tw-graph.strang.branch-left
                            id="bug.left.1.cannot-reproduce"
                            attach-to="strang.trunk.node.4"
                            color="zinc"
                            entry-stem-length="0.5rem"
                            bridge-length="28rem"
                            stem-length="4rem"
                            :node-labels="[
                                3 => [
                                    'left' => [
                                        'text' => ['Cannot reproduce', 'local checkout passes'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                            ]"
                            :step="[
                                'beforeLength' => '3rem',
                                'afterLength' => '3rem',
                                'stepLabel' => [
                                    'text' => ['Decision point', 'keep investigation open'],
                                    'width' => 'halfLong',
                                ],
                            ]"
                            :stem-continuation="[
                                1 => [
                                    '5rem',
                                    'left' => [
                                        'text' => ['Not closed', 'evidence incomplete'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                                2 => [
                                    '6rem',
                                    'left' => [
                                        'text' => ['Returns to', 'root-cause work'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                            ]"
                        />
                        {{-- cannot-reproduce / left/end --}}
                        <x-translation-workbench::ui.tw-graph.strang.branch-end
                            id="bug.left.1.cannot-reproduce.end"
                            side="left"
                            attach-to="strang.branch-left.end"
                            color="zinc"
                            length="2rem"
                            :end-label="[
                                'text' => ['side path remains', 'attached to main lifecycle'],
                                'width' => 'halfLong',
                            ]"
                        />
                    </x-translation-workbench::ui.tw-graph>
                </div>
            </flux:callout>
        </div>
    </flux:card>
</x-layouts::app>
