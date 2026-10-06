<div
    data-tw-graph-performance
    data-running="{{ __('Running…') }}"
    data-failed="{{ __('Failed') }}"
    wire:ignore
    x-cloak
    x-show="previewDev"
    class="mt-2"
>
    <flux:text size="sm" class="flex flex-wrap gap-x-4 gap-y-1 font-mono tabular-nums">
        <flux:tooltip :content="__('Preview refresh click time; for other updates, the Livewire update start time.')">
            <span>{{ __('Refresh begin') }}: <span data-tw-graph-time="begin">—</span></span>
        </flux:tooltip>
        <flux:tooltip :content="__('Observed end: after Livewire rendering, two animation frames and 200 ms without another measured graph bounds pass. Failed requests stop at the error. Later interactions are not part of this measurement.')">
            <span>{{ __('Refresh end') }}: <span data-tw-graph-time="end">—</span></span>
        </flux:tooltip>
        <flux:tooltip :content="__('Elapsed time from refresh begin to the observed end, including the 200 ms settling interval. Partial measurements must not be added to this total.')">
            <span>{{ __('Total') }}: <span data-tw-graph-time="total">—</span></span>
        </flux:tooltip>
        <flux:tooltip :content="__('Time from Livewire request start until response headers arrive. Includes server and connection time, not the complete download.')">
            <span>{{ __('Response headers') }}: <span data-tw-graph-time="headers">—</span></span>
        </flux:tooltip>
        <flux:tooltip :content="__('Elapsed time between Livewire morph start and end, including work completed before the asynchronous morph finishes. This is not a pure JavaScript CPU measurement.')">
            <span>{{ __('DOM update') }}: <span data-tw-graph-time="morph">—</span></span>
        </flux:tooltip>
        <flux:tooltip :content="__('Accumulated graph bounds checks, CSS length resolution and DEV caption positioning since the last request. Includes synchronous layout work.')">
            <span>{{ __('Bounds') }}: <span data-tw-graph-time="bounds">—</span></span>
        </flux:tooltip>
        <flux:tooltip :content="__('Number of measured graph bounds passes in this Livewire component since the last request. Multiple graphs are counted together. These timings are not a total page-load time.')">
            <span>{{ __('Passes') }}: <span data-tw-graph-time="passes">0</span></span>
        </flux:tooltip>
    </flux:text>
    <flux:accordion class="mt-1">
        <flux:accordion.item>
            <flux:accordion.heading class="text-xs">{{ __('Refresh details') }}</flux:accordion.heading>
            <flux:accordion.content>
                <flux:text size="sm" class="mt-1 flex flex-wrap gap-x-4 gap-y-1 font-mono tabular-nums">
                    <flux:tooltip :content="__('From response headers to the decoded Livewire response: body transfer, JSON parsing and request callbacks.')">
                        <span>{{ __('Response body') }}: <span data-tw-graph-time="body">—</span></span>
                    </flux:tooltip>
                    <flux:tooltip :content="__('From the decoded response to the first DOM morph: state synchronization, effects and HTML preparation.')">
                        <span>{{ __('Before DOM') }}: <span data-tw-graph-time="prepare">—</span></span>
                    </flux:tooltip>
                    <flux:tooltip :content="__('From the final DOM morph to the observed refresh end. Includes subsequent observers, bounds passes, frames and the 200 ms settling interval. Overlaps the Bounds measurement.')">
                        <span>{{ __('After DOM') }}: <span data-tw-graph-time="after">—</span></span>
                    </flux:tooltip>
                    <flux:tooltip :content="__('Number of Livewire DOM morphs for this component during this refresh.')">
                        <span>{{ __('DOM passes') }}: <span data-tw-graph-time="morphs">0</span></span>
                    </flux:tooltip>
                    <flux:tooltip :content="__('Livewire element update visits, including unchanged or subsequently skipped elements. Counts hook events, not actual attribute changes.')">
                        <span>{{ __('Element visits') }}: <span data-tw-graph-time="visited">0</span></span>
                    </flux:tooltip>
                    <flux:tooltip :content="__('Livewire addition attempts and completed removals. Subtrees may be handled by one event; these are not counts of all descendant nodes.')">
                        <span>{{ __('Add / remove events') }}: <span data-tw-graph-time="added">0</span> / <span data-tw-graph-time="removed">0</span></span>
                    </flux:tooltip>
                </flux:text>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</div>
