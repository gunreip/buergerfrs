{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/key-paths.blade.php --}}

@php
    $rows = collect($keyPathRows ?? []);
    $context = $keyPathContext ?? [];
    $namespaceFilter = (string) ($context['namespace'] ?? 'all');
    $valueSearch = (string) ($context['value_search'] ?? '');
    $pathCount = $rows->sum(fn(array $row): int => count($row['paths'] ?? []));
@endphp

{{-- Muster treeview css --}}
<style>
    .tree-view,
    .tree-view ul,
    .tree-view li {
        position: relative;
    }

    .tree-view ul {
        list-style: none;
        padding-left: 32px;
    }

    .tree-view li::before,
    .tree-view li::after {
        content: "";
        position: absolute;
        left: -12px;
    }

    .tree-view li::before {
        border-top: 1px solid #fff;
        top: 12px;
        width: 8px;
        height: 0;
    }

    .tree-view li::after {
        border-left: 1px solid #fff;
        height: 100%;
        width: 0px;
        top: 4px;
    }

    .tree-view ul>li:last-child::after {
        height: 8px;
    }
</style>

<div class="mt-4 space-y-4">
    <flux:callout
        color="{{ $pathCount > 0 ? 'sky' : 'zinc' }}"
        icon="git-branch"
    >
        <flux:callout.heading>
            <span class="inline-flex items-center gap-2">
                <span>{{ __('Translation key paths') }}</span>
                <flux:badge
                    size="sm"
                    color="{{ $pathCount > 0 ? 'sky' : 'zinc' }}"
                >
                    {{ number_format($pathCount) }}
                </flux:badge>
            </span>
        </flux:callout.heading>
        <flux:callout.text>
            <span class="inline-flex flex-wrap items-center gap-2">
                <span>{{ __('Derived from active existing lang values.') }}</span>
                <flux:badge
                    size="sm"
                    color="{{ $namespaceFilter !== 'all' ? 'cyan' : 'zinc' }}"
                >
                    {{ __('Namespace') }}: {{ $namespaceFilter === 'all' ? __('All') : $namespaceFilter }}
                </flux:badge>
                <flux:badge
                    size="sm"
                    color="{{ $valueSearch !== '' ? 'cyan' : 'zinc' }}"
                >
                    {{ __('Value search') }}: {{ $valueSearch !== '' ? $valueSearch : __('Empty') }}
                </flux:badge>
                @if (($context['value_search_exact'] ?? false) || ($context['value_search_case_sensitive'] ?? false))
                    <flux:badge
                        size="sm"
                        color="amber"
                    >
                        {{ __('Exact or case-sensitive search active') }}
                    </flux:badge>
                @endif
            </span>
        </flux:callout.text>
    </flux:callout>

    @if ($pathCount === 0)
        <flux:callout
            color="zinc"
            icon="info"
        >
            <flux:callout.heading>{{ __('No key paths found') }}</flux:callout.heading>
            <flux:callout.text>
                {{ __('Select a namespace or enter a translation value search in Work findings to inspect existing active lang-value key paths for that context.') }}
            </flux:callout.text>
        </flux:callout>
    @else
        <flux:accordion variant="outline">
            @foreach ($rows as $namespaceRow)
                @php
                    $namespace = (string) ($namespaceRow['namespace'] ?? '');
                    $paths = collect($namespaceRow['paths'] ?? []);
                @endphp

                <flux:accordion.item :expanded="$loop->first">
                    <flux:accordion.heading>
                        <span class="inline-flex w-full items-center justify-between gap-3">
                            <span class="font-mono text-lg">{{ $namespace }}.</span>
                        </span>
                    </flux:accordion.heading>
                    <flux:accordion.content class="tree-view">
                        <ul class="text-zinc-800 dark:text-white/70">
                            @foreach ($namespaceRow['tree'] ?? [] as $node)
                                @include(
                                    'translation-workbench::livewire.entries.findings.key-paths-node',
                                    [
                                        'node' => $node,
                                        'depth' => 0,
                                        'search' => $valueSearch,
                                        'exact' => (bool) ($context['value_search_exact'] ?? false),
                                        'caseSensitive' => (bool) ($context['value_search_case_sensitive'] ?? false),
                                    ]
                                )
                            @endforeach
                        </ul>
                    </flux:accordion.content>
                </flux:accordion.item>
            @endforeach
        </flux:accordion>
    @endif
</div>

{{-- Muster treeview html --}}
{{-- <div class="clt">
    <ul>
        <li>
            Fruit
            <ul>
                <li>
                    Red
                    <ul>
                        <li>Cherry</li>
                        <li>Strawberry</li>
                    </ul>
                </li>
                <li>
                    Yellow
                    <ul>
                        <li>Banana</li>
                    </ul>
                </li>
            </ul>
        </li>
        <li>
            Meat
            <ul>
                <li>Beef</li>
                <li>Pork</li>
            </ul>
        </li>
    </ul>
</div> --}}
