{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/translation-migration.blade.php --}}

<x-layouts::app :title="__('TW-Graph Sample: Translation Migration')">
    <flux:card class="translation-workbench">
        <x-ui.headers.page
            :title="__('Translation Migration')"
            :description="__(
                'Hand-authored tw-graph sample for a translation key becoming shared, rekeyed, and leaving ended findings behind.',
            )"
        />

        @php
            $translationMigrationGraphId = 'tw-graph-sample-translation-migration';
            $translationMigrationDev = true;
            $translationMigrationCoordinates = false;
        @endphp

        <div x-data="{ translationMigrationDev: @js($translationMigrationDev) }">
            <flux:callout
                class="mt-6"
                color="zinc"
                icon="git-branch"
            >
                <flux:callout.heading>
                    <span class="flex w-full flex-wrap items-center justify-between gap-3">
                        <span class="inline-flex flex-wrap items-center gap-2">
                            <span>{{ __('Translation migration graph canvas') }}</span>
                            <flux:badge
                                size="sm"
                                color="zinc"
                            >
                                {{ $translationMigrationGraphId }}
                            </flux:badge>
                            <flux:badge
                                size="sm"
                                color="amber"
                            >
                                {{ __('hand-authored') }}
                            </flux:badge>
                        </span>
                        @if ($translationMigrationDev)
                            <flux:field
                                class="items-center gap-2"
                                variant="inline"
                                x-on:click.stop
                            >
                                <flux:switch
                                    class="switch-colored hover:cursor-pointer"
                                    x-bind:checked="translationMigrationDev"
                                    x-on:click="translationMigrationDev = !translationMigrationDev"
                                />
                                <flux:label class="text-xs opacity-70 hover:cursor-pointer">
                                    {{ __('DEV') }}
                                </flux:label>
                            </flux:field>
                        @endif
                    </span>
                </flux:callout.heading>
                <flux:callout.text>
                    {{ __('Manual domain sample showing a single translation key, shared origins, one ended finding, and a later rekey target.') }}
                </flux:callout.text>

                <div
                    class="mt-4 overflow-x-auto overflow-y-clip rounded-lg border border-zinc-200 bg-white/70 dark:border-zinc-700 dark:bg-zinc-900/40"
                    x-bind:class="{ 'tw-graph-protocol-dev-disabled': !translationMigrationDev }"
                >
                    <x-translation-workbench::ui.tw-graph
                        class="px-28 py-14"
                        :graph-id="$translationMigrationGraphId"
                        :dev="$translationMigrationDev"
                        :coordinates="$translationMigrationCoordinates"
                        color="green"
                        line-length="4rem"
                        bridge-length="16rem"
                        stem-length="5rem"
                        slot-min-height="74rem"
                        horizontal-padding="34rem"
                    >
                        {{-- central translation key lifecycle --}}
                        <x-translation-workbench::ui.tw-graph.strang.trunk
                            id="migration.center.1.trunk"
                            {{-- color="" --}}
                            :stem-count="5"
                            start-length="4rem"
                            :stem-lengths="['1' => '9rem', '4' => '5.5rem', '5' => '5.5rem']"
                            end-length="5rem"
                            :start-label="[
                                'text' => 'Key ID #124|ui.button.save',
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                            :end-label="[
                                'text' => 'Key ID #124|migrated to shared key|- MIGRATION END -',
                                'width' => 'default',
                                'align' => 'center',
                            ]"
                            :start-node-labels="[
                                'left' => [
                                    'text' => ['Single key', '2026-03-02 09:18'],
                                    'width' => 'default',
                                    'align' => 'left',
                                ],
                                'right' => [
                                    'text' => ['Origin literal', 'Save'],
                                    'width' => 'default',
                                    'align' => 'right',
                                    // 'justify' => true,
                                ],
                            ]"
                            :node-labels="[
                                2 => [
                                    'left' => [
                                        'text' => ['Lang values linked', 'de/en active'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                    'right' => [
                                        'text' => ['source lang value ID #801', 'target lang value ID #802'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                    ],
                                ],
                                3 => [
                                    'left' => [
                                        'text' => ['Shared key created', '2026-04-14 11:36'],
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                    'right' => [
                                        'text' => ['shared translation key', 'ui.actions.save'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                    ],
                                ],
                                4 => [
                                    'left' => [
                                        'text' => ['Rekey decision', '2026-05-20 15:44'],
                                        'width' => 'default',
                                        'align' => 'right',
                                        'color' => 'sky',
                                    ],
                                    'right' => [
                                        'text' => ['ui.actions.save', 'becomes ui.button.save.primary'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                    ],
                                ],
                                5 => [
                                    'left' => [
                                        'text' => ['Active target', '2026-06-01 08:10'],
                                        'width' => 'default',
                                        'align' => 'right',
                                        'color' => 'emerald',
                                    ],
                                    'right' => [
                                        'text' => ['current app usage', 'toolbar and form submit buttons'],
                                        'width' => 'default',
                                        'align' => 'left',
                                        'justify' => true,
                                    ],
                                ],
                            ]"
                        />

                        {{-- origins merge into the shared key --}}
                        <x-translation-workbench::ui.tw-graph.strang.merge-left
                            id="migration.left.1.shared-origin"
                            attach-to="strang.trunk.node.2"
                            color="amber"
                            bridge-length="30rem"
                            :stem-lengths="[1 => '4rem']"
                            :start-label="[
                                'text' => 'finding ID #441|2026-04-10 10:12',
                                'width' => 'halfLong',
                                'align' => 'center',
                                'color' => 'amber',
                            ]"
                            :node-labels="[
                                1 => [
                                    'right' => [
                                        'text' => 'Origin key|admin.buttons.save',
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                                2 => [
                                    'right' => [
                                        'text' => 'Literal|Save',
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                                3 => [
                                    'left' => [
                                        'text' => 'merged into|shared key ID #124',
                                        'width' => 'default',
                                        'align' => 'right',
                                    ],
                                ],
                            ]"
                            :extension-count="1"
                            extension-stem-length="4rem"
                            extension-bridge-length="21rem"
                            :extension-node-labels="[
                                1 => [
                                    1 => [
                                        'right' => [
                                            'text' => 'finding ID #442|2026-04-12 14:03',
                                            'width' => 'default',
                                            'align' => 'left',
                                        ],
                                    ],
                                    2 => [
                                        'right' => [
                                            'text' => 'Origin key|ui.forms.submit',
                                            'width' => 'default',
                                            'align' => 'left',
                                        ],
                                    ],
                                    5 => [
                                        'left' => [
                                            'text' => 'same literal|enters shared key',
                                            'width' => 'default',
                                            'align' => 'right',
                                        ],
                                    ],
                                ],
                            ]"
                        />

                        <x-translation-workbench::ui.tw-graph.strang.merge-right
                            id="migration.right.1.shared-origin"
                            attach-to="strang.trunk.node.2"
                            color="amber"
                            bridge-length="16rem"
                            :stem-lengths="[1 => '4rem']"
                            :start-label="[
                                'text' => 'finding ID #443|2026-04-13 16:21',
                                'width' => 'halfLong',
                                'align' => 'center',
                            ]"
                            :node-labels="[
                                1 => [
                                    'center' => [
                                        'text' => 'Origin key|admin.actions.store',
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                                2 => [
                                    'left' => [
                                        'text' => 'Literal|Save',
                                        'width' => 'default',
                                        'align' => 'left',
                                    ],
                                ],
                                3 => [
                                    'right' => [
                                        'text' => 'merged into|shared key ID #124',
                                        'width' => 'default',
                                        'align' => 'left',
                                        'color' => 'amber',
                                    ],
                                ],
                            ]"
                        />

                        {{-- one finding leaves the shared key after it became obsolete --}}
                        <x-translation-workbench::ui.tw-graph.strang.branch-right
                            id="migration.right.1.ended-finding"
                            attach-to="strang.trunk.node.4"
                            color="rose"
                            {{-- entry-stem-length="0.5rem" --}}
                            bridge-length="23rem"
                            stem-length="4rem"
                            :node-labels="[
                                3 => [
                                    'right' => [
                                        'text' => 'finding ID #442|ui.forms.submit',
                                        'width' => 'default',
                                        'align' => 'left',
                                        'color' => 'rose',
                                    ],
                                ],
                            ]"
                            :step="[
                                'beforeLength' => '3rem',
                                'afterLength' => '4rem',
                                'stepLabel' => [
                                    'text' => 'Source inactive|origin obsolete 1 row',
                                    'width' => 'halfLong',
                                ],
                            ]"
                            :stem-continuation="[
                                1 => [
                                    // '4rem',
                                    'right' => [
                                        'text' => 'ended|2026-05-03 09:40',
                                        'width' => 'default',
                                        'align' => 'left',
                                        'color' => 'rose',
                                    ],
                                ],
                            ]"
                        />

                        <x-translation-workbench::ui.tw-graph.strang.branch-end
                            id="migration.right.1.ended-finding.end"
                            side="right"
                            attach-to="strang.branch-right.end"
                            color="rose"
                            length="3rem"
                            :end-label="[
                                'text' => 'Finding ended|not part of target key',
                                'width' => 'halfLong',
                                'align' => 'center',
                                'color' => 'rose',
                            ]"
                        />

                        {{-- current key continues as a new canonical translation key --}}
                        <x-translation-workbench::ui.tw-graph.strang.rekey-target-left
                            id="migration.left.1.rekey-target"
                            attach-to="strang.trunk.node.5"
                            color="sky"
                            bridge-length="16rem"
                            stem-length="4rem"
                            :stem-continuation="[
                                1 => [
                                    '4rem',
                                    'left' => [
                                        'text' => 'Target key ID #209|ui.button.save.primary',
                                        'width' => 'halfLong',
                                        'align' => 'right',
                                        'color' => 'sky',
                                    ],
                                ],
                            ]"
                            :node-labels="[
                                3 => [
                                    'left' => [
                                        'text' => 'rekeyed to|new canonical key',
                                        'width' => 'halfLong',
                                        'align' => 'right',
                                        'color' => 'sky',
                                    ],
                                ],
                            ]"
                            :end-label="[
                                'text' => 'Rekey target|continues as key ID #209|2026-05-20 15:44',
                                'width' => 'long',
                                'align' => 'center',
                                'color' => 'sky',
                            ]"
                        />
                    </x-translation-workbench::ui.tw-graph>
                </div>
            </flux:callout>
        </div>
    </flux:card>
</x-layouts::app>
