{{-- Graph Preview v0 for the Timeline Chains --}}
<flux:callout
    class="mt-4"
    color="{{ $originRows->isNotEmpty() ? 'cyan' : 'zinc' }}"
    icon="git-merge"
>
    <flux:callout.heading>
        <span class="inline-flex flex-wrap items-center gap-2">
            <span>{{ __('v0. Canonical chain graph preview') }}</span>
            <flux:badge
                size="sm"
                color="{{ $originRows->isNotEmpty() ? 'cyan' : 'zinc' }}"
            >
                {{ number_format($originRows->count()) }}
            </flux:badge>
        </span>
    </flux:callout.heading>
    <flux:callout.text>
        {{ __('Experimental graph view v0 for the same origin data: each root stays in its own strand before merging into the continued shared key.') }}
    </flux:callout.text>

    @if ($originRows->isNotEmpty())
        <div class="mt-4 min-h-[34rem] overflow-visible pb-8">
            <div
                class="min-w-full space-y-4 overflow-visible"
                style="--tw-origin-columns: {{ max(1, $originRows->count()) }};"
            >
                <div class="tw-graph-dev-stack mt-4">
                    {{-- DEV-git-graph --}}
                    <div class="tw-graph-path-end">
                        <flux:badge color="green">
                            {{ __('Trunk') }}
                        </flux:badge>
                        <div class="tw-graph-path-body mt-2 h-1 w-6"></div>
                        <div class="tw-graph-path-body h-3 w-1.5"></div>
                        <div class="tw-graph-path-body h-3 w-1.5"></div>
                    </div>
                    <div class="tw-graph-node">
                        <div class="tw-graph-node-dot"></div>
                    </div>
                    <div class="tw-graph-path-main flex justify-center">
                        <div class="tw-graph-path-body h-16 w-1.5"></div>
                    </div>
                    <div class="tw-graph-node">
                        <div
                            class="tw-graph-inbound-arc-inner tw-graph-inbound-arc-inner-left"
                            aria-hidden="true"
                        ></div>
                        <div
                            class="tw-graph-inbound-connector tw-graph-inbound-connector-left"
                            aria-hidden="true"
                        ></div>
                        <div
                            class="tw-graph-inbound-arc-outer tw-graph-inbound-arc-outer-left"
                            aria-hidden="true"
                        ></div>
                        <div
                            class="tw-graph-inbound-arc-inner tw-graph-inbound-arc-inner-right"
                            aria-hidden="true"
                        ></div>
                        <div
                            class="tw-graph-inbound-connector tw-graph-inbound-connector-right"
                            aria-hidden="true"
                        ></div>
                        <div
                            class="tw-graph-inbound-arc-outer tw-graph-inbound-arc-outer-right"
                            aria-hidden="true"
                        ></div>
                        <div
                            class="tw-graph-outbound-arc-inner tw-graph-outbound-arc-inner-left"
                            aria-hidden="true"
                        ></div>
                        <div
                            class="tw-graph-outbound-connector tw-graph-outbound-connector-left"
                            aria-hidden="true"
                        ></div>
                        <div
                            class="tw-graph-outbound-arc-outer tw-graph-outbound-arc-outer-left"
                            aria-hidden="true"
                        ></div>
                        <div
                            class="tw-graph-outbound-arc-inner tw-graph-outbound-arc-inner-right"
                            aria-hidden="true"
                        ></div>
                        <div
                            class="tw-graph-outbound-connector tw-graph-outbound-connector-right"
                            aria-hidden="true"
                        ></div>
                        <div
                            class="tw-graph-outbound-arc-outer tw-graph-outbound-arc-outer-right"
                            aria-hidden="true"
                        ></div>
                        <div class="tw-graph-node-dot"></div>
                        <div class="tw-graph-node-label">
                            <div
                                class="tw-graph-node-line"
                                aria-hidden="true"
                            >
                                <div></div>
                            </div>
                            <flux:badge
                                size="sm"
                                color="green"
                            >
                                {{ $mainRow['translation_key'] }}
                            </flux:badge>
                        </div>
                    </div>
                    <div class="tw-graph-path-start">
                        <div class="tw-graph-path-body h-16 w-1.5"></div>
                        <flux:badge color="green">
                            {{ $originRows->first()['trunk'] ?? __('No root key') }}
                        </flux:badge>
                    </div>
                </div>
            </div>
        </div>
    @else
        <flux:text class="mt-3 text-sm text-zinc-500">
            {{ __('No graph preview can be derived until origin rows are available.') }}
        </flux:text>
    @endif
</flux:callout>
