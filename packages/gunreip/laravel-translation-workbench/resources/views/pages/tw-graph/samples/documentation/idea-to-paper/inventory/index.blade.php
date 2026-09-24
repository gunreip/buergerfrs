@php
    $inventory = new \Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ComponentInventory();
    $archive = isset($this) ? $this->inventoryArchive : false;
    $root = isset($this) ? $this->inventoryRoot : '';
    $rows = $inventory->rows($archive, $root);
    $pages = max(1, (int) ceil(count($rows) / 50));
    $page = min($pages, max(1, isset($this) ? $this->getPage('inventoryPage') : 1));
    $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
        array_slice($rows, ($page - 1) * 50, 50),
        count($rows),
        50,
        $page,
        ['pageName' => 'inventoryPage', 'path' => request()->url()],
    );
    $sourceName = isset($this) ? $this->inventorySource : '';
    $sourceComponent = $inventory->components[$sourceName] ?? null;
@endphp
<flux:heading size="lg">
    Inventory</flux:heading>
<flux:callout
    class="my-4"
    icon="chart-bar"
    color="sky"
>
    <flux:callout.heading>Project statistics · Snapshot 2026-09-18</flux:callout.heading>
    <flux:text class="mt-2"><strong>547 files</strong> · 69 directories containing files · <strong>120,229
            lines</strong> · 7.40 MiB</flux:text>
    <flux:text class="mt-2"><strong>36 PHP classes / 395 declared methods</strong> · 61 package Blade components,
        including 49 graph components: 18 Strang · 8 Paths · 5 Parts · 9 Segments · 9 Primitives.</flux:text>
    <flux:text class="mt-2">Implementation: 25,350 lines · Documentation, examples and pages: 65,266 · Tests: 15,561 ·
        Integration views: 7,297 · Archive / legacy: 6,755 lines in 79 files.</flux:text>
    <flux:text
        class="mt-2"
        size="sm"
    >{{ __('Fixed snapshot of the working tree, including uncommitted files, documentation, examples, tests and explicit
                                                                                                                                                                                                archive folders. Lines include comments, markup and blank lines; sizes include six example images. Dependencies,
                                                                                                                                                                                                generated assets, caches and shared application files are excluded. These totals are independent of the
                                                                                                                                                                                                inventory filters and do not update when refreshing the inventory.') }}
    </flux:text>
</flux:callout>
<flux:text class="mt-2">
    {{ __('Component chains from the current Blade source. One row per call path; skipped layers stay
                                                                                                                                                                                            empty. Calls within the same layer use →. Conditional calls and loops are included, not evaluated. General UI
                                                                                                                                                                                            helpers outside these five layers are omitted. Dynamic component names cannot be resolved by this static inventory.') }}
</flux:text>

<div class="my-4 flex flex-wrap items-end gap-4">
    <flux:select
        class="min-w-84 max-w-md"
        variant="listbox"
        wire:model.live="inventoryRoot"
        label="{{ __('Entry component') }}"
    >
        <flux:select.option value="">{{ __('All entry components') }}</flux:select.option>
        @foreach ($inventory->roots($archive) as $name)
            <flux:select.option :value="$name">{{ $name }}</flux:select.option>
        @endforeach
    </flux:select>

    <flux:field
        class="min-h-10 items-center"
        variant="inline"
    >
        <flux:switch
            class="switch-colored mr-3 hover:cursor-pointer"
            wire:model.live="inventoryArchive"
        />
        <flux:label>{{ __('Show archive (_old) instead') }}</flux:label>
    </flux:field>

    <flux:button
        class="ml-auto shrink-0"
        aria-label="{{ __('Refresh inventory') }}"
        wire:click="$refresh"
        icon="arrow-path"
    />
</div>
@if ($sourceComponent)
    <flux:callout
        class="mb-4 min-w-0"
        icon="code-bracket"
    >
        <flux:callout.heading>{{ $sourceName }}</flux:callout.heading>
        <flux:text class="break-all">{{ $sourceComponent['path'] }}</flux:text>
        <x-translation-workbench::ui.tw-graph.code-box
            class="mt-3">{{ $sourceComponent['source'] }}</x-translation-workbench::ui.tw-graph.code-box>
        <flux:button
            class="mt-2"
            size="sm"
            wire:click="$set('inventorySource', '')"
        >{{ __('Close source') }}</flux:button>
    </flux:callout>
@endif

<flux:callout color="yellow">

    <div class="overflow-hidden rounded-t-lg">

        {{-- Table Inventory --}}
        <flux:table
            container:class="max-h-240 app-table scrollbar-gutter-auto border-b-1 border-zinc-200 dark:border-zinc-700 mb-3 pb-2"
        >
            <flux:table.columns
                class="dark:bg-zinc-900"
                sticky
            >
                <flux:table.column
                    class="w-12"
                    align="center"
                >#</flux:table.column>
                @foreach (\Gunreip\TranslationWorkbench\Support\TwGraph\Documentation\ComponentInventory::LAYERS as $layer)
                    <flux:table.column>{{ ucfirst($layer) }}</flux:table.column>
                @endforeach
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($paginator as $row)
                    <flux:table.row>
                        <flux:table.cell
                            align="right"
                            :title="$row['chain']"
                        >{{ $paginator->firstItem() + $loop->index }}
                            @if ($row['note'])
                                <flux:badge color="amber">{{ $row['note'] }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        @foreach ($row['cells'] as $steps)
                            <flux:table.cell class="whitespace-normal align-top">
                                @foreach ($steps as $step)
                                    @if (!$loop->first)
                                        <span aria-hidden="true"> → </span>
                                    @endif
                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        wire:click="$set('inventorySource', '{{ $step['name'] }}')"
                                        :title="$step['name']"
                                    >{{ substr($step['name'], strpos($step['name'], '.') + 1) }}</flux:button>
                                    @if ($step['conditions'])
                                        <flux:badge
                                            size="sm"
                                            color="zinc"
                                            :title="implode('; ', $step['conditions']).
                                            ' in '.$step['caller']"
                                        >{{ __('conditional') }}</flux:badge>
                                        <flux:button
                                            variant="ghost"
                                            size="sm"
                                            wire:click="$set('inventorySource', '{{ $step['caller'] }}')"
                                            :title="implode('; ', $step['conditions'])"
                                        >L{{ $step['line'] }}</flux:button>
                                    @endif
                                @endforeach
                            </flux:table.cell>
                        @endforeach
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

    <flux:pagination
        class="mt-3"
        :paginator="$paginator"
    />
</flux:callout>
