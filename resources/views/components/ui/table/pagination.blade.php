{{-- resources/views/components/ui/table/pagination.blade.php --}}

@props(['paginator', 'scrollTo' => null, 'pageName' => 'page'])

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

    <div
        x-data="{
            scrollTo: @js($scrollTo),
            scrollAfterPagination() {
                if (!this.scrollTo) {
                    return;
                }

                window.setTimeout(() => {
                    const target = document.querySelector(this.scrollTo);

                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start',
                        });
                    }
                }, 150);
            },
        }"
        {{ $attributes->class('my-4 py-4') }}
    >
        <div class="flex items-center justify-between gap-4">
            <flux:text class="text-sm text-zinc-400">
                {{ __('admin.translation_list.filter.showing') }}
                {{ $paginator->firstItem() }}
                {{ __('ui.table.pagination.to') }}
                {{ $paginator->lastItem() }}
                {{ __('admin.translation_list.filter.of') }}
                {{ $paginator->total() }}
                {{ __('ui.table.pagination.results') }}
            </flux:text>

            <div class="flex items-center gap-3">
                <flux:button.group>
                    <flux:button
                        class="enabled:hover:bg-sky-300/30! enabled:hover:text-white!"
                        size="sm"
                        icon="chevrons-left"
                        wire:click="setPage(1, '{{ $pageName }}')"
                        x-on:click="scrollAfterPagination()"
                        :disabled="$paginator->onFirstPage()"
                    />

                    <flux:button
                        class="enabled:hover:bg-sky-300/30! enabled:hover:text-white!"
                        size="sm"
                        icon="chevron-left"
                        wire:click="previousPage('{{ $pageName }}')"
                        x-on:click="scrollAfterPagination()"
                        :disabled="$paginator->onFirstPage()"
                    />

                    @if ($windowStart > 1)
                        <flux:button
                            class="hover:bg-white/5! text-zinc-300"
                            size="sm"
                            wire:click="setPage(1, '{{ $pageName }}')"
                            x-on:click="scrollAfterPagination()"
                        >
                            1
                        </flux:button>

                        @if ($windowStart > 2)
                            <flux:button
                                class="text-zinc-500"
                                size="sm"
                                disabled
                                x-on:click="scrollAfterPagination()"
                            >
                                ...
                            </flux:button>
                        @endif
                    @endif

                    @for ($page = $windowStart; $page <= $windowEnd; $page++)
                        <flux:button
                            size="sm"
                            wire:click="setPage({{ $page }}, '{{ $pageName }}')"
                            x-on:click="scrollAfterPagination()"
                            @class([
                                'bg-sky-400/10! text-white! ring-1 ring-sky-400/20!' =>
                                    $page === $currentPage,
                                'text-zinc-300 hover:bg-sky-300/30! hover:text-white!' =>
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
                                x-on:click="scrollAfterPagination()"
                            >
                                ...
                            </flux:button>
                        @endif

                        <flux:button
                            class="hover:bg-white/5! text-zinc-300"
                            size="sm"
                            wire:click="setPage({{ $lastPage }}, '{{ $pageName }}')"
                            x-on:click="scrollAfterPagination()"
                        >
                            {{ $lastPage }}
                        </flux:button>
                    @endif

                    <flux:button
                        class="enabled:hover:bg-sky-300/30! enabled:hover:text-white!"
                        size="sm"
                        icon="chevron-right"
                        wire:click="nextPage('{{ $pageName }}')"
                        :disabled="! $paginator->hasMorePages()"
                        x-on:click="scrollAfterPagination()"
                    />

                    <flux:button
                        class="enabled:hover:bg-sky-300/30! enabled:hover:text-white!"
                        size="sm"
                        icon="chevrons-right"
                        wire:click="setPage({{ $lastPage }}, '{{ $pageName }}')"
                        :disabled="! $paginator->hasMorePages()"
                        x-on:click="scrollAfterPagination()"
                    />
                </flux:button.group>
            </div>
        </div>
    </div>
@endif
