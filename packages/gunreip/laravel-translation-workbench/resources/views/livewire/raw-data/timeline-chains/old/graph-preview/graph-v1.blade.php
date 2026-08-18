{{-- Graph Preview v1 for the Timeline Chains --}}
<flux:callout
    class="mt-4"
    color="{{ $originRows->isNotEmpty() ? 'cyan' : 'zinc' }}"
    icon="git-merge"
>
    <flux:callout.heading>
        <span class="inline-flex flex-wrap items-center gap-2">
            <span>{{ __('Graph v1 prototype') }}</span>
            <flux:badge
                size="sm"
                color="{{ $originRows->isNotEmpty() ? 'cyan' : 'zinc' }}"
            >
                {{ number_format($originRows->count()) }}
            </flux:badge>
        </span>
    </flux:callout.heading>
    <flux:callout.text>
        {{ __('First component graph prototype kept as a visual reference while v2 is refined.') }}
    </flux:callout.text>

    @if ($originRows->isNotEmpty())
        <div class="mt-4 overflow-x-auto overflow-y-visible pb-8">
            <div
                class="w-max min-w-full space-y-4 overflow-visible"
                style="--tw-origin-columns: {{ max(1, $originRows->count()) }};"
            >
                {{-- DEV-component-graph by components --}}
                {{-- Trunk Path --}}
                    <x-ui.tw-graph
                        class="mt-6"
                        color="green"
                        min-width="88rem"
                        min-height="52rem"
                    >
                        {{-- Trunk Path End Node --}}
                        <x-ui.tw-graph.path-end
                            text="{{ __('Trunk') }}"
                            length="h-10"
                            color="green"
                        />

                        <x-ui.tw-graph.node />

                        <x-ui.tw-graph.path-main />

                        <x-ui.tw-graph.node>
                            {{-- Node With Badge Right --}}
                            <x-ui.tw-graph.node-label
                                text="{{ $mainRow['translation_key'] }}"
                                badge-color="green"
                                length="2rem"
                            />
                    </x-ui.tw-graph.node>

                    <x-ui.tw-graph.path-main />

                    <x-ui.tw-graph.node />

                    <x-ui.tw-graph.path-main height="h-32" />

                    <x-ui.tw-graph.node>
                        {{-- Bottom Left To Trunk (Inbound) --}}
                        <x-ui.tw-graph.inbound
                            side="left"
                            color="amber"
                        >
                            <x-ui.tw-graph.inbound.arc-inner />
                            <x-ui.tw-graph.inbound.connector-horizontal length="8.5rem" />
                            <x-ui.tw-graph.inbound.arc-outer branch-offset="8.5rem" />
                            <x-ui.tw-graph.inbound.connector-vertical
                                length="3.0rem"
                                branch-offset="8.5rem"
                                text="{{ __('Inbound left') }}"
                                badge-color="amber"
                                origin-id="701"
                            />
                            <x-ui.tw-graph.inbound.join
                                side="left"
                                parent-offset="6.0rem"
                                length="5rem"
                                vertical-length="11rem"
                                text="{{ __('Nested left') }}"
                                labelSide="right"
                                badge-color="amber"
                                origin-id="702"
                            />
                            <x-ui.tw-graph.inbound.join
                                side="left"
                                parent-offset="13.5rem"
                                length="5rem"
                                vertical-length="12.0rem"
                                text="{{ __('Nested left') }}"
                                labelSide="left"
                                badge-color="amber"
                                origin-id="703"
                            />
                            <x-ui.tw-graph.inbound.join
                                side="left"
                                parent-offset="18rem"
                                length="5rem"
                                vertical-length="4.0rem"
                                text="{{ __('Nested left') }}"
                                labelSide="left"
                                badge-color="amber"
                                origin-id="704"
                            />
                        </x-ui.tw-graph.inbound>

                        {{-- Bottom Right To Trunk (Inbound) --}}
                        <x-ui.tw-graph.inbound color="cyan">
                            <x-ui.tw-graph.inbound.arc-inner />
                            <x-ui.tw-graph.inbound.connector-horizontal length="3rem" />
                            <x-ui.tw-graph.inbound.arc-outer branch-offset="3rem" />
                            <x-ui.tw-graph.inbound.connector-vertical
                                length="8rem"
                                branch-offset="3rem"
                                text="{{ __('Inbound right') }}"
                                labelSide="left"
                                badge-color="cyan"
                                origin-id="801"
                            />
                            <x-ui.tw-graph.inbound.join
                                parent-offset="1.0rem"
                                length="3rem"
                                vertical-length="3.0rem"
                                text="{{ __('Nested right') }}"
                                labelLength="2.0rem"
                                badge-color="cyan"
                                origin-id="802"
                            />
                            <x-ui.tw-graph.inbound.join
                                parent-offset="6.5rem"
                                length="10rem"
                                vertical-length="10.0rem"
                                text="{{ __('Nested right') }}"
                                labelSide="left"
                                badge-color="cyan"
                                origin-id="803"
                            />
                            <x-ui.tw-graph.inbound.join
                                parent-offset="12.5rem"
                                length="10rem"
                                vertical-length="12.0rem"
                                text="{{ __('Nested right') }}"
                                labelSide="right"
                                badge-color="cyan"
                                origin-id="804"
                            />
                        </x-ui.tw-graph.inbound>

                        {{-- Trunk To Top Left (Outbound) --}}
                        <x-ui.tw-graph.outbound
                            side="left"
                            color="pink"
                        >
                            <x-ui.tw-graph.outbound.arc-inner />
                            <x-ui.tw-graph.outbound.connector-horizontal length="0.5rem" />
                            <x-ui.tw-graph.outbound.arc-outer branch-offset="0.5rem" />
                            <x-ui.tw-graph.outbound.connector-vertical
                                length="2rem"
                                branch-offset="0.5rem"
                                text="{{ __('Outbound left') }}"
                                badge-color="pink"
                                labelLength="2rem"
                                labelSide="left"
                            />
                            <x-ui.tw-graph.outbound.extension
                                side="left"
                                branch-offset="30.6rem"
                                branch-length="2rem"
                                length="6rem"
                                text="{{ __('Outbound extended 3') }}"
                                label-side="left"
                                label-length="2rem"
                                badge-color="pink"
                                {{-- end-id="915" --}}
                            />
                            <x-ui.tw-graph.outbound.join
                                side="left"
                                parent-offset="12.0rem"
                                length="14rem"
                                vertical-length="2.5rem"
                                level-offset="0rem"
                                text="{{ __('Nested outbound left') }}"
                                label-side="left"
                                label-length="2rem"
                                badge-color="pink"
                                {{-- origin-id="901" --}}
                            />
                            <x-ui.tw-graph.outbound.extension
                                side="left"
                                branch-offset="0.5rem"
                                branch-length="2rem"
                                length="6rem"
                                text="{{ __('Outbound extended') }}"
                                label-side="left"
                                label-length="2rem"
                                badge-color="pink"
                                {{-- end-id="911" --}}
                            />
                            <x-ui.tw-graph.outbound.return
                                side="left"
                                branch-offset="0.5rem"
                                branch-length="2rem"
                                extension-length="6rem"
                                length="2rem"
                                text="{{ __('Return to trunk') }}"
                                label-side="left"
                                label-length="2rem"
                                badge-color="green"
                                {{-- color="emerald" --}}
                            />
                            <x-ui.tw-graph.outbound.join
                                side="left"
                                parent-offset="-1.5rem"
                                length="12rem"
                                vertical-length="2.5rem"
                                level-offset="0rem"
                                text="{{ __('Nested outbound left') }}"
                                label-side="left"
                                label-length="2rem"
                                badge-color="pink"
                                origin-id="901"
                            />
                        </x-ui.tw-graph.outbound>

                        {{-- Trunk To Top Right (Outbound) --}}
                        <x-ui.tw-graph.outbound color="fuchsia">
                            <x-ui.tw-graph.outbound.arc-inner />
                            <x-ui.tw-graph.outbound.connector-horizontal length="5rem" />
                            <x-ui.tw-graph.outbound.arc-outer branch-offset="5rem" />
                            <x-ui.tw-graph.outbound.connector-vertical
                                length="4rem"
                                branch-offset="5rem"
                                text="{{ __('Outbound right') }}"
                                badge-color="fuchsia"
                            />
                            <x-ui.tw-graph.outbound.extension
                                branch-offset="5rem"
                                branch-length="4rem"
                                length="8.0rem"
                                text="{{ __('Outbound extended') }}"
                                badge-color="fuchsia"
                                end-id="912"
                            />
                            <x-ui.tw-graph.outbound.join
                                parent-offset="3.0rem"
                                length="13rem"
                                vertical-length="3rem"
                                level-offset="0rem"
                                text="{{ __('Nested outbound right') }}"
                                badge-color="fuchsia"
                                origin-id="902"
                            />
                        </x-ui.tw-graph.outbound>

                        {{-- Node With Badge Right --}}
                        <x-ui.tw-graph.node-label
                            text="{{ $mainRow['translation_key'] }}"
                            badge-color="green"
                        />
                        {{-- Node With Badge Left --}}
                        <x-ui.tw-graph.node-label
                            text="{{ $mainRow['translation_key'] }}"
                            side="left"
                            length="8rem"
                            badge-color="green"
                        />
                        </x-ui.tw-graph.node>

                        <x-ui.tw-graph.path-main />

                        {{-- Trunk Path Start Node --}}
                        <x-ui.tw-graph.path-start
                            :text="[$originRows->first()['trunk'] ?? __('No root key'), __('shared Key')]"
                            color="green"
                        />
                    </x-ui.tw-graph>
            </div>
        </div>
    @else
        <flux:text class="mt-3 text-sm text-zinc-500">
            {{ __('No graph preview can be derived until origin rows are available.') }}
        </flux:text>
    @endif
</flux:callout>
