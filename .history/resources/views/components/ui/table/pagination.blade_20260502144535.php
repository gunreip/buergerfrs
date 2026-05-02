{{-- resources/views/components/ui/table/pagination.blade.php --}}

@props(['paginator'])

@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        $windowStart = max(1, $currentPage - 4);
        $windowEnd = min($lastPage, $currentPage + 4);

        if ($currentPage <= 5) {
            $windowStart = 1;
            $windowEnd = min($lastPage, 9);
        }

        if ($currentPage >= $lastPage - 4) {
            $windowStart = max(1, $lastPage - 8);
            $windowEnd = $lastPage;
        }
    @endphp

    <div {{ $attributes->class('my-4 py-4') }}>
        <div class="flex items-center justify-between gap-4">
            <flux:text class="text-sm text-zinc-400">
                {{ __('Showing') }}
                {{ $paginator->firstItem() }}
                {{ __('to') }}
                {{ $paginator->lastItem() }}
                {{ __('of') }}
                {{ $paginator->total() }}
                {{ __('results') }}
            </flux:text>

            <div class="flex items-center gap-3">
                <flux:button.group>
                    <flux:button
                        size="sm"
                        icon="chevrons-left"
                        wire:click="goToFirstPage"
                        :disabled="$paginator->onFirstPage()"
                    />

                    <flux:button
                        size="sm"
                        icon="chevron-left"
                        wire:click="goToPreviousPage"
                        :disabled="$paginator->onFirstPage()"
                    />

                    @if ($windowStart > 1)
                        <flux:button
                            class="hover:bg-white/5! text-zinc-300"
                            size="sm"
                            wire:click="goToPage(1)"
                        >
                            1
                        </flux:button>

                        @if ($windowStart > 2)
                            <flux:button
                                class="text-zinc-500"
                                size="sm"
                                disabled
                            >
                                ...
                            </flux:button>
                        @endif
                    @endif

                    @for ($page = $windowStart; $page <= $windowEnd; $page++)
                        <flux:button
                            size="sm"
                            wire:click="goToPage({{ $page }})"
                            @class([
                                'bg-sky-300/10! text-white! ring-1 ring-sky-300/20!' =>
                                    $page === $currentPage,
                                'text-zinc-300 hover:bg-sky-400/10! hover:text-white!' =>
                                    $page !== $currentPage,
                            ])
                        >
                            {{ $page }}
                        </flux:button>
                    @endfor

                    @if ($windowEnd < $lastPage)
                        @if ($windowEnd < $lastPage - 1)
                            <flux:button
                                class="text-zinc-500"
                                size="sm"
                                disabled
                            >
                                ...
                            </flux:button>
                        @endif

                        <flux:button
                            class="hover:bg-white/5! text-zinc-300"
                            size="sm"
                            wire:click="goToPage({{ $lastPage }})"
                        >
                            {{ $lastPage }}
                        </flux:button>
                    @endif

                    <flux:button
                        size="sm"
                        icon="chevron-right"
                        wire:click="goToNextPage"
                        :disabled="! $paginator->hasMorePages()"
                    />

                    <flux:button
                        size="sm"
                        icon="chevrons-right"
                        wire:click="goToLastPage"
                        :disabled="! $paginator->hasMorePages()"
                    />
                </flux:button.group>
            </div>
        </div>
    </div>
@endif
