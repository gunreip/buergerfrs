{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/project-roadmap.blade.php --}}

<x-layouts::app :title="__('TW-Graph Sample: Project Roadmap')">
    <flux:card class="translation-workbench">
        <x-ui.headers.page
            :title="__('Project Roadmap')"
            :description="__(
                'Hand-authored tw-graph sample for milestones, feature branches, decisions, release merges, and terminal roadmap states.',
            )"
        />

        @php
            $projectRoadmapGraphId = 'tw-graph-sample-project-roadmap';
            $projectRoadmapDev = true;
            $projectRoadmapCoordinates = false;
        @endphp

        <div x-data="{ projectRoadmapDev: @js($projectRoadmapDev) }">
            <flux:callout
                class="mt-6"
                color="zinc"
                icon="map"
            >
                <flux:callout.heading>
                    <span class="flex w-full flex-wrap items-center justify-between gap-3">
                        <span class="inline-flex flex-wrap items-center gap-2">
                            <span>{{ __('Project roadmap graph canvas') }}</span>
                            <flux:badge
                                size="sm"
                                color="zinc"
                            >
                                {{ $projectRoadmapGraphId }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="amber"
                            >
                                {{ __('hand-authored') }}
                            </flux:badge>
                        </span>
                        @if ($projectRoadmapDev)
                            <flux:field
                                class="items-center gap-2"
                                variant="inline"
                                x-on:click.stop
                            >
                                <flux:switch
                                    class="switch-colored hover:cursor-pointer"
                                    x-bind:checked="projectRoadmapDev"
                                    x-on:click="projectRoadmapDev = !projectRoadmapDev"
                                />
                                <flux:label class="text-xs opacity-70 hover:cursor-pointer">
                                    {{ __('DEV') }}
                                </flux:label>
                            </flux:field>
                        @endif
                    </span>
                </flux:callout.heading>
                <flux:callout.text>
                    {{ __('Manual roadmap sample showing a central milestone axis with side tracks for features, decisions, release consolidation, and postponed scope.') }}
                </flux:callout.text>

                <div
                    class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    x-bind:class="{ 'tw-graph-protocol-dev-disabled': !projectRoadmapDev }"
                >
                    <x-translation-workbench::ui.tw-graph
                        class="px-28 py-14"
                        :graph-id="$projectRoadmapGraphId"
                        :dev="$projectRoadmapDev"
                        :coordinates="$projectRoadmapCoordinates"
                        color="emerald"
                        line-length="4rem"
                        bridge-length="16rem"
                        stem-length="5rem"
                        slot-min-height="78rem"
                        horizontal-padding="36rem"
                    >
                        {{-- central milestone timeline --}}
                        <x-translation-workbench::ui.tw-graph.strang.trunk
                            id="roadmap.center.1.timeline"
                            :stem-count="8"
                            start-length="4rem"
                            :stem-lengths="[
                                1 => '2rem',
                                2 => '8rem',
                                3 => '5rem',
                                4 => '19.5rem',
                                5 => '7.5rem',
                                6 => '5rem',
                                7 => '5rem',
                                8 => '5rem',
                            ]"
                            end-length="4rem"
                            :start-label="[
                                'text' => ['Project roadmap', '2026 initiative'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                            :end-label="[
                                'text' => ['Roadmap closed', 'v1.0 shipped and monitored'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                            :start-node-labels="[
                                'left' => [
                                    'text' => ['Discovery', 'problem framing'],
                                    'width' => 'default',
                                    'align' => 'left',
                                    'color' => 'emerald',
                                ],
                                'right' => [
                                    'text' => [
                                        'Stakeholders agree on goals, constraints, and measurable release outcomes.',
                                    ],
                                    'width' => 'long',
                                    'align' => 'left',
                                    'justify' => true,
                                    'color' => 'zinc',
                                ],
                            ]"
                            :node-labels="[
                                2 => [
                                    'left' => [
                                        'text' => ['M1', 'Architecture baseline'],
                                        'width' => 'default',
                                        'align' => 'right',
                                        'color' => 'emerald',
                                    ],
                                    'right' => [
                                        'text' => [
                                            'Data model, auth boundary, and delivery scope are stable enough for parallel work.',
                                        ],
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                3 => [
                                    'left' => [
                                        'text' => ['M2', 'Alpha cut'],
                                        'width' => 'default',
                                        'align' => 'right',
                                        'color' => 'emerald',
                                    ],
                                    'right' => [
                                        'text' => [
                                            'First integrated build with feature flags and internal feedback enabled.',
                                        ],
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                5 => [
                                    'left' => [
                                        'text' => ['M3', 'Beta readiness'],
                                        'width' => 'default',
                                        'align' => 'right',
                                        'color' => 'emerald',
                                    ],
                                    'right' => [
                                        'text' => [
                                            'Performance budget, onboarding path, and support runbook are verified.',
                                        ],
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                6 => [
                                    'left' => [
                                        'text' => ['Release candidate', 'scope lock'],
                                        'width' => 'halfLong',
                                        'align' => 'right',
                                        'color' => 'amber',
                                    ],
                                    'right' => [
                                        'text' => [
                                            'Only blockers and launch-critical defects remain eligible for v1.0.',
                                        ],
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                7 => [
                                    'left' => [
                                        'text' => ['v1.0 release', 'production rollout'],
                                        'width' => 'halfLong',
                                        'align' => 'right',
                                        'color' => 'emerald',
                                    ],
                                    'right' => [
                                        'text' => [
                                            'Public release with support watch, telemetry review, and rollback plan.',
                                        ],
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                                8 => [
                                    'left' => [
                                        'text' => ['Stabilization', 'post-launch'],
                                        'width' => 'default',
                                        'align' => 'right',
                                        'color' => 'emerald',
                                    ],
                                    'right' => [
                                        'text' => ['Roadmap transitions into maintenance and v1.1 discovery.'],
                                        'width' => 'long',
                                        'align' => 'left',
                                        'justify' => true,
                                        'color' => 'zinc',
                                    ],
                                ],
                            ]"
                        />

                        {{-- feature track / design system --}}
                        <x-translation-workbench::ui.tw-graph.strang.branch-left
                            id="roadmap.left.1.design-system"
                            attach-to="strang.trunk.node.2"
                            color="sky"
                            entry-stem-length="0.5rem"
                            bridge-length="28rem"
                            stem-length="4rem"
                            :node-labels="[
                                3 => [
                                    'left' => [
                                        'text' => ['Feature branch', 'design system'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                            ]"
                            :step="[
                                'beforeLength' => '14.1rem',
                                'afterLength' => '4.1rem',
                                'stepLabel' => [
                                    'text' => ['Decision', 'tokens before components'],
                                    'width' => 'halfLong',
                                ],
                            ]"
                            :stem-continuation="[
                                1 => [
                                    '4rem',
                                    'left' => [
                                        'text' => ['Foundations', 'colors and spacing'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                                2 => [
                                    '5rem',
                                    'left' => [
                                        'text' => ['Components', 'forms and navigation'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                            ]"
                            :branch-return="[
                                1 => [
                                    'attachTo' => 'stem.2',
                                    'bridgeLength' => '28rem',
                                    'color' => 'sky',
                                ],
                            ]"
                        />

                        {{-- feature track / API platform --}}
                        <x-translation-workbench::ui.tw-graph.strang.branch-right
                            id="roadmap.right.1.api-platform"
                            attach-to="strang.trunk.node.2"
                            color="violet"
                            entry-stem-length="0.5rem"
                            bridge-length="35rem"
                            stem-length="4rem"
                            :node-labels="[
                                3 => [
                                    'right' => [
                                        'text' => ['Feature branch', 'API platform'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                            ]"
                            :step="[
                                'beforeLength' => '3rem',
                                'afterLength' => '3rem',
                                'stepLabel' => [
                                    'text' => ['Status change', 'contract freeze'],
                                    'width' => 'halfLong',
                                ],
                            ]"
                            :stem-continuation="[
                                1 => [
                                    '4rem',
                                    'right' => [
                                        'text' => ['Endpoints', 'bulk import ready'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                                2 => [
                                    '5rem',
                                    'right' => [
                                        'text' => ['Telemetry', 'request traces linked'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                            ]"
                        />

                        {{-- release merge into the candidate --}}
                        <x-translation-workbench::ui.tw-graph.strang.merge-left
                            id="roadmap.left.1.release-train"
                            attach-to="strang.trunk.node.5"
                            color="amber"
                            bridge-length="14rem"
                            :stem-lengths="[1 => '4rem']"
                            :start-label="[
                                'text' => ['Release train', 'feature bundle A'],
                                'width' => 'halfLong',
                                'align' => 'center',
                                'color' => 'amber',
                            ]"
                            :node-labels="[
                                1 => [
                                    'right' => [
                                        'text' => ['Design system', 'merged'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                                2 => [
                                    'right' => [
                                        'text' => ['API platform', 'merged'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                                5 => [
                                    'left' => [
                                        'text' => ['v1.0 scope', 'accepted'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                            ]"
                        />

                        {{-- postponed scope remains visible as terminal side path --}}
                        <x-translation-workbench::ui.tw-graph.strang.branch-right
                            id="roadmap.right.2.mobile-companion"
                            attach-to="strang.trunk.node.4"
                            color="rose"
                            entry-stem-length="0.5rem"
                            bridge-length="15rem"
                            stem-length="4rem"
                            :node-labels="[
                                3 => [
                                    'right' => [
                                        'text' => ['Feature branch', 'mobile companion'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                            ]"
                            :step="[
                                'beforeLength' => '3rem',
                                'afterLength' => '3rem',
                                'stepLabel' => [
                                    'text' => ['Decision', 'defer after beta'],
                                    'width' => 'halfLong',
                                ],
                            ]"
                            :stem-continuation="[
                                1 => [
                                    '5rem',
                                    'right' => [
                                        'text' => ['Postponed', 'moves to v1.1 discovery'],
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                            ]"
                        />

                        <x-translation-workbench::ui.tw-graph.strang.branch-end
                            id="roadmap.right.2.mobile-companion.end"
                            side="right"
                            attach-to="strang.branch-right.end"
                            color="rose"
                            length="3rem"
                            :end-label="[
                                'text' => ['Deferred scope', 'tracked outside v1.0'],
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                        />
                    </x-translation-workbench::ui.tw-graph>
                </div>
            </flux:callout>

            @include('translation-workbench::pages.tw-graph.samples.documentation.project-roadmap.index')
        </div>
    </flux:card>
</x-layouts::app>
