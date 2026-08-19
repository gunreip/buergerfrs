<flux:callout
    class="mt-4"
    color="{{ $originRows->isNotEmpty() ? 'sky' : 'zinc' }}"
    icon="git-branch-plus"
>
    <flux:callout.heading>
        <span class="inline-flex flex-wrap items-center gap-2">
            <span>{{ __('Graph v2 prototype') }}</span>
            <flux:badge
                size="sm"
                color="{{ $originRows->isNotEmpty() ? 'sky' : 'zinc' }}"
            >
                {{ number_format($originRows->count()) }}
            </flux:badge>
        </span>
    </flux:callout.heading>
    <flux:callout.text>
        {{ __('Componentized trunk, merge and branch graph parts for the extended timeline prototype.') }}
    </flux:callout.text>

    @if ($originRows->isNotEmpty())
        <div class="mt-4 overflow-visible pb-8">
            <div
                class="w-max min-w-full space-y-4"
                style="--tw-origin-columns: {{ max(1, $originRows->count()) }};"
            >
                @include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview.graph-v2.plan')

                <div class="flex flex-wrap items-start justify-between gap-3 text-xs">
                    @include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview.graph-v2.status')
                @include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview.graph-v2.json')
                </div>

                @include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview.graph-v2.primitives-catalog')
                @include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview.graph-v2.segments-catalog')
                @include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview.graph-v2.path-catalog')
                @include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview.graph-v2.strang-catalog')
                @include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview.graph-v2.composition-catalog')
                @include('translation-workbench::livewire.raw-data.timeline-chains.graph-preview.graph-v2.renderer')

                {{-- Frozen graph references live in git history only. --}}
            </div>
        </div>
    @else
        <flux:text class="mt-3 text-sm text-zinc-500">
            {{ __('No graph preview can be derived until origin rows are available.') }}
        </flux:text>
    @endif
</flux:callout>
