{{-- packages/gunreip/laravel-translation-workbench/resources/views/pages/tw-graph/samples/documentation/idea-to-paper/03-strang-branch/01-branch.blade.php --}}

@php
    $dev = $dev ?? true;
    $coordinates = $coordinates ?? false;
    $graphId = $ideaToPaperGraphId ?? 'idea-to-paper-step-06-branch';
    $renderMode = $renderMode ?? 'documentation';
    $thisPath = '.../tw-graph/samples/documentation/idea-to-paper/03-strang-branch/01-branch.blade.php';
    $branchProps = [
        [
            'name' => 'attach-to / :anchor-start',
            'default' => 'anchor-start at 0/0',
            'effect' =>
                'Places the branch. Prefer attach-to when the branch belongs to a registered trunk, merge, or rekey anchor.',
        ],
        [
            'name' => 'entry-stem-length',
            'default' => '0rem',
            'effect' =>
                'Adds a short vertical entry before the first branch arc. Useful when the branch should clear the trunk node.',
        ],
        [
            'name' => 'bridge-length',
            'default' => 'bridge-length',
            'effect' =>
                'Controls the horizontal distance from the trunk before the branch turns into its vertical stem.',
        ],
        [
            'name' => 'stem-length',
            'default' => 'stem-length',
            'effect' => 'Controls the first branch stem after the arcs and optional step.',
        ],
        [
            'name' => ':step',
            'default' => 'null',
            'effect' => 'Places a centered step label between short stems, useful for status changes or decisions.',
        ],
        [
            'name' => ':stem-continuation',
            'default' => '[]',
            'effect' => 'Adds more branch stems. Each continuation can carry left/right labels and a custom length.',
        ],
        [
            'name' => ':branch-extension',
            'default' => '[]',
            'effect' => 'Adds secondary branch paths from a branch anchor without rebuilding the whole strand by hand.',
        ],
        [
            'name' => ':branch-return',
            'default' => '[]',
            'effect' =>
                'Adds a return path back toward another anchor. Default fallback=true keeps unresolved returns visible; fallback=false means the requested anchor is required and DEV warns when it is missing.',
        ],
        [
            'name' => ':node-labels',
            'default' => '[]',
            'effect' =>
                'Labels on supported branch anchors. Numeric labels map to existing branch anchors only; unsupported numeric keys are ignored and reported as nodeLabel-Mismatch in DEV mode.',
        ],
    ];
@endphp

@if ($renderMode === 'documentation')
    <section
        class="grid gap-4 lg:grid-cols-2"
        x-data="{ branchVariant: 'default' }"
    >
        <flux:callout
            color="rose"
            icon="git-branch"
        >
            <flux:callout.heading>
                {{ __('6. Branch') }}
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('A branch leaves an existing anchor and continues as its own side path. The preview keeps the trunk muted as a reference, but the documented element is the branch strand.') }}
            </flux:callout.text>

            @include('translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch._branch-code-tabs')

            <div class="mt-4 min-w-0 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <flux:table container:class="max-h-80">
                    <flux:table.columns
                        class="bg-white dark:bg-zinc-900"
                        sticky
                    >
                        <flux:table.column class="w-40">{{ __('Prop') }}</flux:table.column>
                        <flux:table.column class="w-40">{{ __('Default') }}</flux:table.column>
                        <flux:table.column class="min-w-0">{{ __('Purpose') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($branchProps as $prop)
                            <flux:table.row>
                                <flux:table.cell class="align-top">
                                    <code class="break-words text-xs">{{ $prop['name'] }}</code>
                                </flux:table.cell>
                                <flux:table.cell class="align-top">
                                    <code class="break-words text-xs">{{ $prop['default'] }}</code>
                                </flux:table.cell>
                                <flux:table.cell
                                    class="min-w-0 whitespace-normal break-words text-xs leading-5 text-zinc-600 dark:text-zinc-300"
                                >
                                    {{ $prop['effect'] }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:callout>

        <flux:callout
            color="zinc"
            icon="square-dashed-text"
        >
            <flux:callout.heading>
                <span class="flex w-full flex-wrap items-center justify-between gap-3">
                    <span>{{ __('Step 6 preview') }}</span>
                    <flux:badge
                        size="sm"
                        color="zinc"
                        x-show="branchVariant === 'default'"
                    >
                        {{ __('default') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="rose"
                        x-show="branchVariant === 'offset'"
                    >
                        {{ __('different anchors') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="amber"
                        x-show="branchVariant === 'step'"
                    >
                        {{ __('step') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="pink"
                        x-show="branchVariant === 'continuation'"
                    >
                        {{ __('continuation') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="orange"
                        x-show="branchVariant === 'return'"
                    >
                        {{ __('return') }}
                    </flux:badge>
                    <flux:badge
                        size="sm"
                        color="red"
                        x-show="branchVariant === 'mismatch'"
                    >
                        {{ __('mismatch') }}
                    </flux:badge>
                </span>
            </flux:callout.heading>

            @include(
                'translation-workbench::pages.tw-graph.samples.documentation.idea-to-paper.03-strang-branch._branch-preview-variants',
                [
                    'dev' => $dev,
                    'coordinates' => $coordinates,
                    'graphId' => $graphId,
                    'renderMode' => $renderMode,
                ]
            )
        </flux:callout>
    </section>
@endif
