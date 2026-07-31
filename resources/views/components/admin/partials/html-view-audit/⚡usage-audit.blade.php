{{-- resources/views/components/admin/partials/html-view-audit/⚡usage-audit.blade.php --}}

@php
    $usageAudit = $usageAudit ?? [
        'exists' => false,
        'path' => 'storage/audits/html/view-html-used.json',
        'generated_at' => null,
        'generated_at_formatted' => null,
        'scan' => [
            'files_scanned' => 0,
            'excluded_files' => [],
        ],
        'components' => [
            'source_paths' => [],
            'skipped_paths' => [],
        ],
        'native' => [
            'counts' => [
                'available' => 0,
                'used' => 0,
                'unused' => 0,
                'unknown' => 0,
            ],
            'used' => [],
            'top_used' => [],
            'unused' => [],
            'unknown' => [],
        ],
        'flux' => [
            'counts' => [
                'available' => 0,
                'used' => 0,
                'unused' => 0,
                'used_unknown' => 0,
            ],
            'used' => [],
            'top_used' => [],
            'unused' => [],
            'used_unknown' => [],
        ],
        'custom' => [
            'counts' => [
                'available' => 0,
                'used' => 0,
                'unused' => 0,
                'used_unknown' => 0,
            ],
            'used' => [],
            'top_used' => [],
            'unused' => [],
            'used_unknown' => [],
        ],
        'includes' => [
            'counts' => [
                'used' => 0,
            ],
        ],
        'livewire' => [
            'counts' => [
                'used' => 0,
            ],
        ],
        'note' => null,
    ];

    $usageNativeCounts = $usageAudit['native']['counts'] ?? [];
    $usageFluxCounts = $usageAudit['flux']['counts'] ?? [];
    $usageCustomCounts = $usageAudit['custom']['counts'] ?? [];
    $usageIncludeCounts = $usageAudit['includes']['counts'] ?? [];
    $usageLivewireCounts = $usageAudit['livewire']['counts'] ?? [];

    $usageUnknownTotal =
        (int) ($usageNativeCounts['unknown'] ?? 0) +
        (int) ($usageFluxCounts['used_unknown'] ?? 0) +
        (int) ($usageCustomCounts['used_unknown'] ?? 0);

    $usageTopSlice = static function (array $items, int $limit = 10): array {
        uasort($items, static function (array $left, array $right): int {
            return (int) ($right['count'] ?? 0) <=> (int) ($left['count'] ?? 0);
        });

        return array_slice($items, 0, $limit, true);
    };

    $usageNativeTopUsed = $usageTopSlice($usageAudit['native']['top_used'] ?? ($usageAudit['native']['used'] ?? []));
    $usageFluxTopUsed = $usageTopSlice($usageAudit['flux']['top_used'] ?? ($usageAudit['flux']['used'] ?? []));
    $usageCustomTopUsed = $usageTopSlice($usageAudit['custom']['top_used'] ?? ($usageAudit['custom']['used'] ?? []));

    $usageComponentSourcePaths = array_merge(
        $usageAudit['components']['source_paths']['custom'] ?? [],
        $usageAudit['components']['source_paths']['flux'] ?? [],
    );

    $usageComponentSkippedPaths = array_merge(
        $usageAudit['components']['skipped_paths']['custom'] ?? [],
        $usageAudit['components']['skipped_paths']['flux'] ?? [],
    );
@endphp

<div
    class="mt-4 grid gap-3 md:grid-cols-12"
    x-cloak
    x-show="statisticOpen"
    x-collapse
>
    <flux:callout
        class="md:col-span-12"
        color="emerald"
        icon="component"
    >
        <flux:callout.heading>
            {{ __('View usage audit') }}
        </flux:callout.heading>

        <flux:callout.text>
            <div class="grid gap-3 md:grid-cols-12">
                <div class="md:col-span-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                        {{ __('Audit file') }}
                    </div>

                    <div class="mt-1 text-sm">
                        <code class="elipsis-rtl block truncate">
                            {{ $usageAudit['path'] ?? 'storage/audits/html/view-html-used.json' }}
                        </code>
                    </div>

                    @if (!empty($usageAudit['generated_at_formatted'] ?? null))
                        <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                            {{ __('Generated') }}: {{ $usageAudit['generated_at_formatted'] }}
                        </div>
                    @endif

                    @if (!($usageAudit['exists'] ?? false))
                        <div class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                            {{ $usageAudit['note'] ?? __('Run php artisan html:check-view-html-used.') }}
                        </div>
                    @endif
                </div>

                <div class="md:col-span-9">
                    <div class="grid gap-2 md:grid-cols-5">
                        <flux:badge
                            variant="subtle"
                            color="sky"
                        >
                            {{ __('Files') }}: {{ $usageAudit['scan']['files_scanned'] ?? 0 }}
                        </flux:badge>

                        <flux:badge
                            variant="subtle"
                            color="amber"
                        >
                            {{ __('Native used') }}: {{ $usageNativeCounts['used'] ?? 0 }}
                        </flux:badge>

                        <flux:badge
                            variant="subtle"
                            color="violet"
                        >
                            {{ __('Flux used') }}: {{ $usageFluxCounts['used'] ?? 0 }}
                        </flux:badge>

                        <flux:badge
                            variant="subtle"
                            color="blue"
                        >
                            {{ __('Custom used') }}: {{ $usageCustomCounts['used'] ?? 0 }}
                        </flux:badge>

                        <flux:badge
                            variant="subtle"
                            :color="$usageUnknownTotal > 0 ? 'red' : 'green'"
                        >
                            {{ __('Unknown') }}:
                            {{ $usageUnknownTotal }}
                        </flux:badge>
                    </div>

                    <div class="mt-3 grid gap-2 md:grid-cols-3">

                        {{-- Native HTML --}}
                        <flux:callout
                            heading="{{ __('Native HTML') }}"
                            icon="code-xml"
                        >
                            <div class="mt-1 text-zinc-600 dark:text-zinc-300">
                                <x-ui.text.stat-value
                                    :label="__('ui.available')"
                                    :value="$usageNativeCounts['available'] ?? 0"
                                /> ·
                                <x-ui.text.stat-value
                                    :label="__('Unused')"
                                    :value="$usageNativeCounts['unused'] ?? 0"
                                /> ·
                                <x-ui.text.stat-value
                                    :label="__('Unknown')"
                                    :value="$usageNativeCounts['unknown'] ?? 0"
                                />
                            </div>
                        </flux:callout>

                        {{-- Flux components --}}
                        <flux:callout
                            heading="{{ __('Flux components') }}"
                            icon="grid-3x3"
                        >
                            <div class="mt-1 text-zinc-600 dark:text-zinc-300">
                                <x-ui.text.stat-value
                                    :label="__('ui.available')"
                                    :value="$usageFluxCounts['available'] ?? 0"
                                /> ·
                                <x-ui.text.stat-value
                                    :label="__('Unused')"
                                    :value="$usageFluxCounts['unused'] ?? 0"
                                /> ·
                                <x-ui.text.stat-value
                                    :label="__('Unknown')"
                                    :value="$usageFluxCounts['used_unknown'] ?? 0"
                                />
                            </div>
                        </flux:callout>

                        {{-- Custom components --}}
                        <flux:callout
                            heading="{{ __('Custom components') }}"
                            icon="columns-3-cog"
                        >
                            <div class="mt-1 text-zinc-600 dark:text-zinc-300">
                                <x-ui.text.stat-value
                                    :label="__('ui.available')"
                                    :value="$usageCustomCounts['available'] ?? 0"
                                /> ·
                                <x-ui.text.stat-value
                                    :label="__('Unused')"
                                    :value="$usageCustomCounts['unused'] ?? 0"
                                /> ·
                                <x-ui.text.stat-value
                                    :label="__('Unknown')"
                                    :value="$usageCustomCounts['used_unknown'] ?? 0"
                                />
                            </div>
                        </flux:callout>
                    </div>

                    @if ($usageUnknownTotal === 0)
                        <div
                            class="mt-3 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800 dark:border-emerald-800/60 dark:bg-emerald-950/40 dark:text-emerald-200">
                            {{ __('No unknown native tags, Flux components or custom components were found.') }}
                        </div>
                    @endif

                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:badge
                                variant="subtle"
                                color="red"
                            >
                                {{ __('Includes') }}: {{ $usageIncludeCounts['used'] ?? 0 }}
                            </flux:badge>

                            <flux:badge
                                variant="subtle"
                                color="green"
                            >
                                {{ __('Livewire') }}: {{ $usageLivewireCounts['used'] ?? 0 }}
                            </flux:badge>

                            <flux:badge
                                variant="subtle"
                                color="sky"
                            >
                                {{ __('Excluded files') }}: {{ count($usageAudit['scan']['excluded_files'] ?? []) }}
                            </flux:badge>

                            <flux:badge
                                variant="subtle"
                                color="teal"
                            >
                                {{ __('Source paths') }}:
                                {{ count($usageAudit['components']['source_paths']['custom'] ?? []) + count($usageAudit['components']['source_paths']['flux'] ?? []) }}
                            </flux:badge>

                            <flux:badge
                                variant="subtle"
                                color="fuchsia"
                            >
                                {{ __('Skipped paths') }}:
                                {{ count($usageAudit['components']['skipped_paths']['custom'] ?? []) + count($usageAudit['components']['skipped_paths']['flux'] ?? []) }}
                            </flux:badge>
                        </div>

                        <div class="ml-auto">
                            <x-ui.button.show-hide
                                state="usageDetailsOpen"
                                show-label="{{ __('ui.Show-usage-details') }}"
                                hide-label="{{ __('ui.Hide-usage-details') }}"
                                width="w-32"
                            />
                        </div>
                    </div>

                    @if (!empty($usageAudit['custom']['unused']))
                        <div class="mt-3 text-sm">
                            <div class="font-semibold">{{ __('Unused custom components') }}</div>

                            <div class="mt-1 flex flex-wrap gap-2">
                                @foreach ($usageAudit['custom']['unused'] as $componentName => $componentMeta)
                                    <flux:badge
                                        variant="subtle"
                                        color="amber"
                                    >
                                        {{ $componentName }}
                                    </flux:badge>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div
                        class="mt-4"
                        x-cloak
                        x-show="usageDetailsOpen"
                        x-collapse
                    >
                        <div class="grid gap-3 lg:grid-cols-3">

                            {{-- Top native HTML tags --}}
                            <flux:callout
                                heading="{{ __('Top native HTML tags') }}"
                                icon="code-xml"
                            >
                                <div class="mt-2 space-y-1">
                                    @forelse ($usageNativeTopUsed as $tagName => $tagMeta)
                                        <x-ui.text.stat-value-num
                                            :label="$tagName"
                                            :value="$tagMeta['count'] ?? null"
                                            color="zinc"
                                        />
                                    @empty
                                        <div class="text-zinc-500 dark:text-zinc-400">
                                            {{ __('No usage data available.') }}
                                        </div>
                                    @endforelse
                                </div>
                            </flux:callout>

                            {{-- Top flux components --}}
                            <flux:callout
                                heading="{{ __('Top Flux components') }}"
                                icon="cube"
                            >
                                <div class="mt-2 space-y-1">
                                    @forelse ($usageFluxTopUsed as $fluxName => $fluxMeta)
                                        <x-ui.text.stat-value-num
                                            :label="$fluxName"
                                            :value="$fluxMeta['count'] ?? null"
                                            color="violet"
                                        />
                                    @empty
                                        <div class="text-zinc-500 dark:text-zinc-400">
                                            {{ __('No usage data available.') }}
                                        </div>
                                    @endforelse
                                </div>
                            </flux:callout>

                            {{-- Top custom components --}}
                            <flux:callout
                                heading="{{ __('Top custom components') }}"
                                icon="columns-3-cog"
                            >
                                <div class="mt-2 space-y-1">
                                    @forelse ($usageCustomTopUsed as $customName => $customMeta)
                                        <x-ui.text.stat-value-num
                                            :label="$customName"
                                            :value="$customMeta['count'] ?? null"
                                            color="blue"
                                        />
                                    @empty
                                        <div class="text-zinc-500 dark:text-zinc-400">
                                            {{ __('No usage data available.') }}
                                        </div>
                                    @endforelse
                                </div>
                            </flux:callout>
                        </div>

                        {{-- Component source paths and skipped paths provide insights into which component files were included in the audit and which were skipped, along with their respective paths. This information can help identify potential gaps in the audit coverage and ensure that all relevant components are being analyzed for HTML usage. --}}
                        <div class="mt-3 grid gap-3 lg:grid-cols-2">

                            {{-- Component source path --}}
                            <flux:callout
                                heading="{{ __('Component source paths') }}"
                                icon="waypoints"
                            >
                                <div class="mt-2 space-y-1">
                                    @forelse ($usageComponentSourcePaths as $sourcePath)
                                        <x-ui.text.stat-value-code
                                            :label="$sourcePath['path'] ?? ''"
                                            :value="$sourcePath['prefix'] ?? ''"
                                            color="green"
                                        />
                                    @empty
                                        <div class="text-zinc-500 dark:text-zinc-400">
                                            {{ __('No source paths available.') }}
                                        </div>
                                    @endforelse
                                </div>
                            </flux:callout>

                            {{-- Skipped components --}}
                            <flux:callout
                                heading="{{ __('Skipped component paths') }}"
                                icon="route-off"
                            >
                                <div class="mt-2 space-y-1">
                                    @forelse ($usageComponentSkippedPaths as $skippedPath)
                                        <x-ui.text.stat-value-code
                                            :label="$skippedPath['path'] ?? ''"
                                            :value="$skippedPath['prefix'] ?? ''"
                                            color="amber"
                                        />
                                    @empty
                                        <div class="text-zinc-500 dark:text-zinc-400">
                                            {{ __('No skipped paths available.') }}
                                        </div>
                                    @endforelse
                                </div>
                            </flux:callout>
                        </div>
                    </div>
                </div>
            </div>
        </flux:callout.text>
    </flux:callout>
</div>
