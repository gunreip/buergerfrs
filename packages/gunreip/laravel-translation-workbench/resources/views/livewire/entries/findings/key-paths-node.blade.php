{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/key-paths-node.blade.php --}}

@php
    $node = is_array($node ?? null) ? $node : [];
    $depth = max(0, (int) ($depth ?? 0));
    $children = collect($node['children'] ?? []);
    $isLeaf = $children->isEmpty();
    $childCount = $children->count();
    $search = (string) ($search ?? '');
    $exact = (bool) ($exact ?? false);
    $caseSensitive = (bool) ($caseSensitive ?? false);
@endphp

<li>
    <div class="inline-flex items-center gap-2 whitespace-nowrap leading-6">
        <span class="{{ $isLeaf ? 'text-zinc-500 dark:text-zinc-400' : '' }}">
            <x-translation-workbench::text.highlight
                value="{{ $node['name'] ?? '' }}"
                search="{{ $search }}"
                :exact="$exact"
                :case-sensitive="$caseSensitive"
            />@if (! $isLeaf).@endif
        </span>

        @if (! $isLeaf)
            <flux:badge
                size="sm"
                color="sky"
            >
                {{ number_format($childCount) }}
            </flux:badge>
        @endif
    </div>

    @if ($children->isNotEmpty())
        <ul>
            @foreach ($children as $childNode)
                @include('translation-workbench::livewire.entries.findings.key-paths-node', [
                    'node' => $childNode,
                    'depth' => $depth + 1,
                    'search' => $search,
                    'exact' => $exact,
                    'caseSensitive' => $caseSensitive,
                ])
            @endforeach
        </ul>
    @endif
</li>
