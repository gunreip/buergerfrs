{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/raw-data/timeline-chains/graph-preview/graph-v2/strang-catalog.blade.php --}}

@php
    $componentChain = ['primitives', 'segments', 'paths', 'strangs'];
@endphp

<div
    class="w-full"
    x-data="{ twGraphDev: true }"
>
    <flux:accordion>
        <flux:accordion.item>
            <flux:accordion.heading class="rounded rounded-b-md bg-amber-800 p-2">
                <span class="inline-flex flex-wrap items-center gap-2">
                    <span>{{ __('Strang catalog') }}</span>
                    <flux:field
                        class="items-center gap-2"
                        variant="inline"
                        x-on:click.stop
                    >
                        <flux:switch
                            class="switch-colored hover:cursor-pointer"
                            x-model="twGraphDev"
                        />
                        <flux:label class="text-xs opacity-70 hover:cursor-pointer">
                            {{ __('DEV') }}
                        </flux:label>
                    </flux:field>
                    <flux:badge color="amber">
                        {{ __('Concept layer') }}
                    </flux:badge>
                    <flux:badge color="sky">
                        {{ implode('->', $componentChain) }}
                    </flux:badge>
                </span>
            </flux:accordion.heading>
            <flux:accordion.content>
                <div
                    class="mt-3 w-full"
                    x-bind:class="{ 'tw-graph-protocol-dev-disabled': !twGraphDev }"
                >
                    <flux:tab.group>
                        <flux:tabs>
                            <flux:tab name="strang-trunk-bottom-top">
                                {{ __('trunk bottom-top') }}
                            </flux:tab>
                            <flux:tab name="strang-merge-left">
                                {{ __('merge-left') }}
                            </flux:tab>
                            <flux:tab name="strang-merge-right">
                                {{ __('merge-right') }}
                            </flux:tab>
                            <flux:tab name="strang-branch-left">
                                {{ __('branch-left') }}
                            </flux:tab>
                            <flux:tab name="strang-branch-right">
                                {{ __('branch-right') }}
                            </flux:tab>
                        </flux:tabs>

                        <flux:tab.panel name="strang-trunk-bottom-top">
                            <flux:card class="mt-3 dark:bg-zinc-800">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-100">
                                        {{ __('strang.trunk bottom-top') }}
                                    </span>
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('strang') }}
                                    </flux:badge>
                                </div>

                                {{-- Component Canvas Rendering strang.trunk bottom-top --}}
                                <div class="my-24 overflow-x-auto">
                                    <div
                                        class="tw-graph-protocol tw-graph-protocol-catalog-preview mx-auto"
                                        style="
                                    --tw-graph-protocol-color-rgb: 16 185 129;
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
                                    width: 32rem;
                                    min-width: 32rem;
                                    height: 2rem;
                                    min-height: 22rem;
                                "
                                    >
                                        {{-- Component strang.trunk bottom-top --}}
                                        <x-translation-workbench::ui.tw-graph-protocol.strangs.trunk
                                            id="catalog.strang.trunk.bottom-top"
                                            direction="bottom-top"
                                            :anchor-start="['x' => '0rem', 'y' => '0.75rem']"
                                            start-length="4.5rem"
                                            end-length="3.5rem"
                                            :path-lengths="[
                                                ['4rem', ['Root|left', null]],
                                                ['3rem', ['Shared key|left', 'Shared key|right']],
                                            ]"
                                            color="emerald"
                                            :dev="true"
                                        />
                                    </div>
                                </div>

                                <div class="mt-3 grid gap-3 border-t border-zinc-200 pt-2 text-xs dark:border-zinc-700">
                                    <flux:callout
                                        color="green"
                                        icon="code-xml"
                                    >
                                        {{-- Component Call strang.trunk bottom-top --}}
                                        <flux:callout.heading>
                                            {{ __('Component Call') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <flux:text
                                                class="mt-2 font-mono"
                                                color="sky"
                                            >
                                                {{ __('<x-translation-workbench::ui.tw-graph-protocol.strangs.trunk') }}
                                            </flux:text>
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="red"
                                        icon="list-tree"
                                    >
                                        {{-- Component Structure Plan strang.trunk bottom-top --}}
                                        <flux:callout.heading>
                                            {{ __('Structure plan') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <div class="mt-2 space-y-1 font-mono text-xs">
                                                <div>{{ __('strangs.trunk') }}</div>
                                                <div>{{ __('|-- paths.trunk bottom-top') }}</div>
                                            </div>
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="blue"
                                        icon="info"
                                    >
                                        {{-- Component Purpose strang.trunk bottom-top --}}
                                        <flux:callout.heading>
                                            {{ __('Purpose') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            {{ __('Passes trunk geometry intent through the explicit strang layer to paths.trunk.') }}
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="fuchsia"
                                        icon="sliders-horizontal"
                                    >
                                        {{-- Component Props strang.trunk bottom-top --}}
                                        <flux:callout.heading>
                                            {{ __('Props') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <div class="mt-2 space-y-1 font-mono text-xs">
                                                <div>{{ __('direction=bottom-top') }}</div>
                                                <div>{{ __('anchorStart{x,y}') }}</div>
                                                <div>{{ __('startLength') }}</div>
                                                <div>{{ __('pathLengths[]') }}</div>
                                                <div>{{ __('endLength') }}</div>
                                                <div>{{ __('color') }}</div>
                                                <div>{{ __('dev') }}</div>
                                            </div>
                                        </flux:callout.text>
                                    </flux:callout>
                                </div>
                            </flux:card>
                        </flux:tab.panel>

                        <flux:tab.panel name="strang-merge-left">
                            <flux:card class="mt-3 dark:bg-zinc-800">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-100">
                                        {{ __('strang.merge-left') }}
                                    </span>
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('strang') }}
                                    </flux:badge>
                                </div>

                                {{-- Component Canvas Rendering strang.merge-left --}}
                                <div class="my-24 overflow-x-auto">
                                    <div
                                        class="tw-graph-protocol tw-graph-protocol-catalog-preview mx-auto"
                                        style="
                                    --tw-graph-protocol-color-rgb: 245 158 11;
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
                                    width: 58rem;
                                    min-width: 58rem;
                                    height: 22rem;
                                    min-height: 22rem;
                                "
                                    >
                                        {{-- component strang.merge-left --}}
                                        <x-translation-workbench::ui.tw-graph-protocol.strangs.merge-left
                                            id="catalog.strang.merge-left"
                                            :dev="true"
                                            :anchor-start="['x' => '-2rem', 'y' => '6.5rem']"
                                            color="amber"
                                            start-length="2.8rem"
                                            vertical-length="2rem"
                                            connector-length="6.5rem"
                                            extension-count="7"
                                            :extension-vertical-lengths="[
                                                1 => '6rem',
                                                2 => '2rem',
                                                3 => '7rem',
                                                4 => '3rem',
                                                5 => '7.5rem',
                                                6 => '12rem',
                                                7 => '5rem',
                                            ]"
                                            :extension-connector-lengths="[
                                                1 => '5rem',
                                                2 => null,
                                                3 => '7rem',
                                                4 => '6rem',
                                                5 => null,
                                                6 => null,
                                                7 => null,
                                            ]"
                                            :extension-labels="[
                                                1 => ['connectorEnd' => [['text' => 'Root #1', 'side' => 'top'], null]],
                                                2 => null,
                                                3 => ['arcEnd' => ['text' => 'Arc #3', 'side' => 'left']],
                                                4 => null,
                                                5 => null,
                                                6 => [
                                                    'verticalEnd' => [null, ['text' => 'Long root', 'side' => 'left']],
                                                ],
                                                7 => null,
                                            ]"
                                        />
                                    </div>
                                </div>

                                <div class="mt-3 grid gap-3 border-t border-zinc-200 pt-2 text-xs dark:border-zinc-700">
                                    <flux:callout
                                        color="green"
                                        icon="code-xml"
                                    >
                                        {{-- Component Call strang.merge-left --}}
                                        <flux:callout.heading>
                                            {{ __('Component Call') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <flux:text
                                                class="mt-2 font-mono"
                                                color="sky"
                                            >
                                                {{ __('<x-translation-workbench::ui.tw-graph-protocol.strangs.merge-left') }}
                                            </flux:text>
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="red"
                                        icon="list-tree"
                                    >
                                        {{-- Component Structure Plan strang.merge-left --}}
                                        <flux:callout.heading>
                                            {{ __('Structure plan') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <div class="mt-2 space-y-1 font-mono text-xs">
                                                <div>{{ __('strangs.merge-left') }}</div>
                                                <div>{{ __('|-- paths.merge-extension left') }}</div>
                                                <div>{{ __('|-- paths.merge-extension left') }}</div>
                                                <div>{{ __('|-- paths.merge left') }}</div>
                                            </div>
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="blue"
                                        icon="info"
                                    >
                                        {{-- Component Purpose strang.merge-left --}}
                                        <flux:callout.heading>
                                            {{ __('Purpose') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            {{ __('Groups a left merge path with two outward merge extensions into one connected merge strand.') }}
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="fuchsia"
                                        icon="sliders-horizontal"
                                    >
                                        {{-- Component Props strang.merge-left --}}
                                        <flux:callout.heading>
                                            {{ __('Props') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <div class="mt-2 space-y-1 font-mono text-xs">
                                                <div>{{ __('anchorStart{x,y}') }}</div>
                                                <div>{{ __('arcSize=2.75rem') }}</div>
                                                <div>{{ __('color=amber') }}</div>
                                                <div>{{ __('startLength') }}</div>
                                                <div>{{ __('verticalLength') }}</div>
                                                <div>{{ __('connectorLength') }}</div>
                                                <div>{{ __('extensionCount') }}</div>
                                                <div>{{ __('extensionStartLength=null') }}</div>
                                                <div>{{ __('extensionVerticalLength=null') }}</div>
                                                <div>{{ __('extensionVerticalLengths[]') }}</div>
                                                <div>{{ __('extensionConnectorLength=null') }}</div>
                                                <div>{{ __('extensionConnectorLengths[]') }}</div>
                                                <div>{{ __('extensionLabels[]') }}</div>
                                                <div>{{ __('dev') }}</div>
                                            </div>
                                        </flux:callout.text>
                                    </flux:callout>
                                </div>
                            </flux:card>
                        </flux:tab.panel>

                        <flux:tab.panel name="strang-merge-right">
                            <flux:card class="mt-3 dark:bg-zinc-800">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-100">
                                        {{ __('strang.merge-right') }}
                                    </span>
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('strang') }}
                                    </flux:badge>
                                </div>

                                {{-- Component Canvas Rendering strang.merge-right --}}
                                <div class="my-24 overflow-x-auto">
                                    <div
                                        class="tw-graph-protocol tw-graph-protocol-catalog-preview mx-auto"
                                        style="
                                    --tw-graph-protocol-color-rgb: 34 197 94;
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
                                    width: 58rem;
                                    min-width: 58rem;
                                    height: 22rem;
                                    min-height: 22rem;
                                "
                                    >
                                        {{-- Component strang.merge-right --}}
                                        <x-translation-workbench::ui.tw-graph-protocol.strangs.merge-right
                                            id="catalog.strang.merge-right"
                                            :dev="true"
                                            :anchor-start="['x' => '2rem', 'y' => '6.5rem']"
                                            color="green"
                                            start-length="2.8rem"
                                            vertical-length="2rem"
                                            connector-length="4.5rem"
                                            extension-count="4"
                                            :extension-vertical-lengths="[1 => '6rem', 2 => '2rem', 3 => '7rem', 4 => '3rem']"
                                            :extension-connector-lengths="[1 => '4rem', 2 => '7.5rem', 3 => '6rem', 4 => null]"
                                            :extension-labels="[
                                                1 => ['connectorEnd' => [['text' => 'Root #1', 'side' => 'top'], null]],
                                                2 => null,
                                                3 => ['arcEnd' => ['text' => 'Arc #3', 'side' => 'right']],
                                                4 => null,
                                            ]"
                                        />
                                    </div>
                                </div>

                                <div class="mt-3 grid gap-3 border-t border-zinc-200 pt-2 text-xs dark:border-zinc-700">
                                    <flux:callout
                                        color="green"
                                        icon="code-xml"
                                    >
                                        {{-- Component Call strang.merge-right --}}
                                        <flux:callout.heading>
                                            {{ __('Component Call') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <flux:text
                                                class="mt-2 font-mono"
                                                color="sky"
                                            >
                                                {{ __('<x-translation-workbench::ui.tw-graph-protocol.strangs.merge-right') }}
                                            </flux:text>
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="red"
                                        icon="list-tree"
                                    >
                                        {{-- Component Structure Plan strang.merge-right --}}
                                        <flux:callout.heading>
                                            {{ __('Structure plan') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <div class="mt-2 space-y-1 font-mono text-xs">
                                                <div>{{ __('strangs.merge-right') }}</div>
                                                <div>{{ __('|-- paths.merge-extension right') }}</div>
                                                <div>{{ __('|-- paths.merge-extension right') }}</div>
                                                <div>{{ __('|-- paths.merge right') }}</div>
                                            </div>
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="blue"
                                        icon="info"
                                    >
                                        {{-- Component Purpose strang.merge-right --}}
                                        <flux:callout.heading>
                                            {{ __('Purpose') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            {{ __('Groups a right merge path with outward merge extensions into one connected merge strand.') }}
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="fuchsia"
                                        icon="sliders-horizontal"
                                    >
                                        {{-- Component Props strang.merge-right --}}
                                        <flux:callout.heading>
                                            {{ __('Props') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <div class="mt-2 space-y-1 font-mono text-xs">
                                                <div>{{ __('anchorStart{x,y}') }}</div>
                                                <div>{{ __('arcSize=2.75rem') }}</div>
                                                <div>{{ __('color=green') }}</div>
                                                <div>{{ __('startLength') }}</div>
                                                <div>{{ __('verticalLength') }}</div>
                                                <div>{{ __('connectorLength') }}</div>
                                                <div>{{ __('extensionCount') }}</div>
                                                <div>{{ __('extensionStartLength=null') }}</div>
                                                <div>{{ __('extensionVerticalLength=null') }}</div>
                                                <div>{{ __('extensionVerticalLengths[]') }}</div>
                                                <div>{{ __('extensionConnectorLength=null') }}</div>
                                                <div>{{ __('extensionConnectorLengths[]') }}</div>
                                                <div>{{ __('extensionLabels[]') }}</div>
                                                <div>{{ __('dev') }}</div>
                                            </div>
                                        </flux:callout.text>
                                    </flux:callout>
                                </div>
                            </flux:card>
                        </flux:tab.panel>

                        <flux:tab.panel name="strang-branch-left">
                            <flux:card class="mt-3 dark:bg-zinc-800">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-100">
                                        {{ __('strang.branch-left') }}
                                    </span>
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('strang') }}
                                    </flux:badge>
                                </div>

                                {{-- Component Canvas Rendering strang.branch-left --}}
                                <div class="my-24 overflow-x-auto">
                                    <div
                                        class="tw-graph-protocol tw-graph-protocol-catalog-preview mx-auto"
                                        style="
                                    --tw-graph-protocol-color-rgb: 236 72 153;
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
                                    width: 58rem;
                                    min-width: 58rem;
                                    height: 34rem;
                                    min-height: 34rem;
                                "
                                    >
                                        {{-- Component strang.branch-left --}}
                                        <x-translation-workbench::ui.tw-graph-protocol.strangs.branch-left
                                            id="catalog.strang.branch-left"
                                            {{-- Identity / rendering state --}}
                                            :dev="true"
                                            {{-- Base anchor / shared geometry --}}
                                            :anchor-start="['x' => '8rem', 'y' => '0.75rem']"
                                            color="pink"
                                            {{-- Main branch: paths.branch left --}}
                                            connector-length="5rem"
                                            vertical-length="5rem"
                                            branch-end-path-length="2rem"
                                            :branch-continuation-node-labels="[
                                                ['text' => 'Main return', 'side' => 'right', 'badgeColor' => 'pink'],
                                                null,
                                            ]"
                                            {{-- Main branch return: paths.branch-return left from branch.vertical.continuation --}}
                                            :branch-return="true"
                                            branch-return-vertical-length="3rem"
                                            branch-return-connector-length="5rem"
                                            branch-return-color="pink"
                                            {{-- Branch extensions: paths.branch-extension left[] --}}
                                            extension-count="2"
                                            :extension-connector-lengths="[1 => '10rem', 2 => '10rem']"
                                            :extension-vertical-lengths="[1 => '5rem', 2 => '5rem']"
                                            :extension-end-path-lengths="[1 => '4rem', 2 => '5.425rem']"
                                            :extension-continuation-node-labels="[
                                                1 => [
                                                    [
                                                        'text' => 'Ext 1 return',
                                                        'side' => 'right',
                                                        'badgeColor' => 'blue',
                                                    ],
                                                    null,
                                                ],
                                                2 => [
                                                    [
                                                        'text' => 'Ext 2 return',
                                                        'side' => 'right',
                                                        'badgeColor' => 'teal',
                                                    ],
                                                    null,
                                                ],
                                            ]"
                                            :extension-colors="[1 => 'blue', 2 => 'teal']"
                                            {{-- Branches from extension vertical endpoints: paths.branch left[] --}}
                                            :extension-branch-indexes="[2]"
                                            :extension-branch-connector-lengths="[1 => null, 2 => '4rem']"
                                            :extension-branch-vertical-lengths="[1 => null, 2 => '5.4rem']"
                                            :extension-branch-colors="[1 => null, 2 => 'red']"
                                            {{-- Returns from extension branch endpoints: paths.branch-return-extension left[] --}}
                                            :extension-branch-return-indexes="[2]"
                                            :extension-branch-return-vertical-lengths="[1 => null, 2 => '5rem']"
                                            :extension-branch-return-connector-lengths="[1 => null, 2 => '19.5rem']"
                                            :extension-branch-return-colors="[1 => null, 2 => 'red']"
                                            {{-- Returns from extension continuations: paths.branch-return left[] --}}
                                            :extension-return-indexes="[1, 2]"
                                            :extension-return-vertical-lengths="[1 => '11.9rem', 2 => '5.0rem']"
                                            :extension-return-connector-lengths="[1 => '13rem', 2 => '4.5rem']"
                                            :extension-return-colors="[1 => 'blue', 2 => 'teal']"
                                        />
                                    </div>
                                </div>

                                <div
                                    class="mt-3 grid gap-3 border-t border-zinc-200 pt-2 text-xs dark:border-zinc-700">
                                    <flux:callout
                                        color="green"
                                        icon="code-xml"
                                    >
                                        {{-- Component Call strang.branch-left --}}
                                        <flux:callout.heading>
                                            {{ __('Component Call') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <flux:text
                                                class="mt-2 font-mono"
                                                color="sky"
                                            >
                                                {{ __('<x-translation-workbench::ui.tw-graph-protocol.strangs.branch-left') }}
                                            </flux:text>
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="red"
                                        icon="list-tree"
                                    >
                                        {{-- Component Structure Plan strang.branch-left --}}
                                        <flux:callout.heading>
                                            {{ __('Structure plan') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <div class="mt-2 space-y-1 font-mono text-xs">
                                                <div>{{ __('strangs.branch-left') }}</div>
                                                <div>{{ __('|-- paths.branch left') }}</div>
                                                <div>{{ __('|-- paths.branch-return left') }}</div>
                                                <div>{{ __('|-- paths.branch-extension left[]') }}</div>
                                                <div>{{ __('|-- paths.branch-return left[]') }}</div>
                                                <div>{{ __('|-- paths.branch-return-extension left[]') }}</div>
                                            </div>
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="blue"
                                        icon="info"
                                    >
                                        {{-- Component Purpose strang.branch-left --}}
                                        <flux:callout.heading>
                                            {{ __('Purpose') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            {{ __('Passes left branch geometry intent through the explicit strang layer to paths.branch.') }}
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="fuchsia"
                                        icon="sliders-horizontal"
                                    >
                                        {{-- Component Props strang.branch-left --}}
                                        <flux:callout.heading>
                                            {{ __('Props') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <div class="mt-2 space-y-1 font-mono text-xs">
                                                <div>{{ __('anchorStart{x,y}') }}</div>
                                                <div>{{ __('arcSize') }}</div>
                                                <div>{{ __('connectorLength') }}</div>
                                                <div>{{ __('verticalLength') }}</div>
                                                <div>{{ __('branchEndPathLength') }}</div>
                                                <div>{{ __('branchContinuationNodeLabels') }}</div>
                                                <div>{{ __('branchReturn') }}</div>
                                                <div>{{ __('branchReturnVerticalLength') }}</div>
                                                <div>{{ __('branchReturnConnectorLength') }}</div>
                                                <div>{{ __('branchReturnColor') }}</div>
                                                <div>{{ __('extensionCount') }}</div>
                                                <div>{{ __('extensionConnectorLengths[]') }}</div>
                                                <div>{{ __('extensionVerticalLengths[]') }}</div>
                                                <div>{{ __('extensionEndPathLengths[]') }}</div>
                                                <div>{{ __('extensionContinuationNodeLabels[]') }}</div>
                                                <div>{{ __('extensionColors[]') }}</div>
                                                <div>{{ __('extensionBranchIndexes[]') }}</div>
                                                <div>{{ __('extensionBranchConnectorLengths[]') }}</div>
                                                <div>{{ __('extensionBranchVerticalLengths[]') }}</div>
                                                <div>{{ __('extensionBranchColors[]') }}</div>
                                                <div>{{ __('extensionBranchReturnIndexes[]') }}</div>
                                                <div>{{ __('extensionBranchReturnVerticalLengths[]') }}</div>
                                                <div>{{ __('extensionBranchReturnConnectorLengths[]') }}</div>
                                                <div>{{ __('extensionBranchReturnColors[]') }}</div>
                                                <div>{{ __('extensionReturnIndexes[]') }}</div>
                                                <div>{{ __('extensionReturnVerticalLengths[]') }}</div>
                                                <div>{{ __('extensionReturnConnectorLengths[]') }}</div>
                                                <div>{{ __('extensionReturnColors[]') }}</div>
                                                <div>{{ __('color') }}</div>
                                                <div>{{ __('dev') }}</div>
                                            </div>
                                        </flux:callout.text>
                                    </flux:callout>
                                </div>
                            </flux:card>
                        </flux:tab.panel>

                        <flux:tab.panel name="strang-branch-right">
                            <flux:card class="mt-3 dark:bg-zinc-800">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-100">
                                        {{ __('strang.branch-right') }}
                                    </span>
                                    <flux:badge
                                        size="sm"
                                        color="amber"
                                    >
                                        {{ __('strang') }}
                                    </flux:badge>
                                </div>

                                {{-- Component Canvas Rendering strang.branch-right --}}
                                <div class="my-24 overflow-x-auto">
                                    <div
                                        class="tw-graph-protocol tw-graph-protocol-catalog-preview mx-auto"
                                        style="
                                    --tw-graph-protocol-color-rgb: 139 92 246;
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
                                    width: 58rem;
                                    min-width: 58rem;
                                    height: 34rem;
                                    min-height: 34rem;
                                "
                                    >
                                        {{-- Component strang.branch-right --}}
                                        <x-translation-workbench::ui.tw-graph-protocol.strangs.branch-right
                                            id="catalog.strang.branch-right"
                                            :dev="true"
                                            :anchor-start="['x' => '-8rem', 'y' => '0.75rem']"
                                            color="violet"
                                            connector-length="5rem"
                                            vertical-length="2rem"
                                            branch-end-path-length="3rem"
                                            :branch-continuation-node-labels="true"
                                            :branch-return="true"
                                            branch-return-vertical-length="3rem"
                                            branch-return-connector-length="5rem"
                                            branch-return-color="violet"
                                            extension-count="2"
                                            :extension-connector-lengths="[1 => '4rem', 2 => '6rem']"
                                            :extension-vertical-lengths="[1 => '3rem', 2 => '5rem']"
                                            :extension-end-path-lengths="[1 => '3rem', 2 => '3rem']"
                                            :extension-continuation-node-labels="[1 => true, 2 => true]"
                                            :extension-colors="[1 => 'sky', 2 => 'cyan']"
                                            :extension-branch-indexes="[2]"
                                            :extension-branch-connector-lengths="[1 => null, 2 => '4rem']"
                                            :extension-branch-vertical-lengths="[1 => null, 2 => '5.4rem']"
                                            :extension-branch-colors="[1 => null, 2 => 'red']"
                                            :extension-branch-return-indexes="[2]"
                                            :extension-branch-return-vertical-lengths="[1 => null, 2 => '5rem']"
                                            :extension-branch-return-connector-lengths="[1 => null, 2 => '12rem']"
                                            :extension-branch-return-colors="[1 => null, 2 => 'red']"
                                            :extension-return-indexes="[1, 2]"
                                            :extension-return-vertical-lengths="[1 => '7rem', 2 => '5rem']"
                                            :extension-return-connector-lengths="[1 => '9.0rem', 2 => '4rem']"
                                            :extension-return-colors="[1 => 'sky', 2 => 'cyan']"
                                        />
                                    </div>
                                </div>

                                <div
                                    class="mt-3 grid gap-3 border-t border-zinc-200 pt-2 text-xs dark:border-zinc-700">
                                    <flux:callout
                                        color="green"
                                        icon="code-xml"
                                    >
                                        {{-- Component Call strang.branch-right --}}
                                        <flux:callout.heading>
                                            {{ __('Component Call') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <flux:text
                                                class="mt-2 font-mono"
                                                color="sky"
                                            >
                                                {{ __('<x-translation-workbench::ui.tw-graph-protocol.strangs.branch-right') }}
                                            </flux:text>
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="red"
                                        icon="list-tree"
                                    >
                                        {{-- Component Structure Plan strang.branch-right --}}
                                        <flux:callout.heading>
                                            {{ __('Structure plan') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <div class="mt-2 space-y-1 font-mono text-xs">
                                                <div>{{ __('strangs.branch-right') }}</div>
                                                <div>{{ __('|-- paths.branch right') }}</div>
                                                <div>{{ __('|-- paths.branch-return right') }}</div>
                                                <div>{{ __('|-- paths.branch-extension right[]') }}</div>
                                                <div>{{ __('|-- paths.branch-return right[]') }}</div>
                                                <div>{{ __('|-- paths.branch-return-extension right[]') }}</div>
                                            </div>
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="blue"
                                        icon="info"
                                    >
                                        {{-- Component Purpose strang.branch-right --}}
                                        <flux:callout.heading>
                                            {{ __('Purpose') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            {{ __('Passes right branch geometry intent through the explicit strang layer to paths.branch.') }}
                                        </flux:callout.text>
                                    </flux:callout>

                                    <flux:callout
                                        color="fuchsia"
                                        icon="sliders-horizontal"
                                    >
                                        {{-- Component Props strang.branch-right --}}
                                        <flux:callout.heading>
                                            {{ __('Props') }}
                                        </flux:callout.heading>
                                        <flux:callout.text>
                                            <div class="mt-2 space-y-1 font-mono text-xs">
                                                <div>{{ __('anchorStart{x,y}') }}</div>
                                                <div>{{ __('arcSize') }}</div>
                                                <div>{{ __('connectorLength') }}</div>
                                                <div>{{ __('verticalLength') }}</div>
                                                <div>{{ __('branchEndPathLength') }}</div>
                                                <div>{{ __('branchContinuationNodeLabels') }}</div>
                                                <div>{{ __('branchReturn') }}</div>
                                                <div>{{ __('branchReturnVerticalLength') }}</div>
                                                <div>{{ __('branchReturnConnectorLength') }}</div>
                                                <div>{{ __('branchReturnColor') }}</div>
                                                <div>{{ __('extensionCount') }}</div>
                                                <div>{{ __('extensionConnectorLengths[]') }}</div>
                                                <div>{{ __('extensionVerticalLengths[]') }}</div>
                                                <div>{{ __('extensionEndPathLengths[]') }}</div>
                                                <div>{{ __('extensionContinuationNodeLabels[]') }}</div>
                                                <div>{{ __('extensionColors[]') }}</div>
                                                <div>{{ __('extensionBranchIndexes[]') }}</div>
                                                <div>{{ __('extensionBranchConnectorLengths[]') }}</div>
                                                <div>{{ __('extensionBranchVerticalLengths[]') }}</div>
                                                <div>{{ __('extensionBranchColors[]') }}</div>
                                                <div>{{ __('extensionBranchReturnIndexes[]') }}</div>
                                                <div>{{ __('extensionBranchReturnVerticalLengths[]') }}</div>
                                                <div>{{ __('extensionBranchReturnConnectorLengths[]') }}</div>
                                                <div>{{ __('extensionBranchReturnColors[]') }}</div>
                                                <div>{{ __('extensionReturnIndexes[]') }}</div>
                                                <div>{{ __('extensionReturnVerticalLengths[]') }}</div>
                                                <div>{{ __('extensionReturnConnectorLengths[]') }}</div>
                                                <div>{{ __('extensionReturnColors[]') }}</div>
                                                <div>{{ __('color') }}</div>
                                                <div>{{ __('zIndex') }}</div>
                                                <div>{{ __('dev') }}</div>
                                            </div>
                                        </flux:callout.text>
                                    </flux:callout>
                                </div>
                            </flux:card>
                        </flux:tab.panel>
                    </flux:tab.group>
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</div>
