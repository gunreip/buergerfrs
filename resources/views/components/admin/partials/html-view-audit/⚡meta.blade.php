{{-- resources/views/components/admin/partials/html-view-audit/⚡meta.blade.php --}}

@php
    $nativeSection = $audit['sections']['native_html'] ?? [];
    $customSection = $audit['sections']['custom_components'] ?? [];
    $nativeReference = $audit['references']['native_html'] ?? null;
    $nativeReferenceFile = $nativeReferenceFile ?? null;
    $referenceDisplay = is_array($nativeReferenceFile) ? $nativeReferenceFile : $nativeReference;
    $referenceHasFallback = (bool) ($nativeReference['fallback'] ?? false);
    $referenceIsMissing = is_array($nativeReferenceFile) && !($nativeReferenceFile['exists'] ?? false);
    $historyCounts = $historyCounts ?? [
        'open' => 0,
        'changed' => 0,
        'resolved' => 0,
        'ignored' => 0,
        'total' => 0,
    ];
@endphp

<flux:card class="mt-6">
    <x-ui.headers.card
        :title="__('admin.permissions.overview.title')"
        :description="$audit['note'] ?? __('Current HTML / Blade view structure audit result.')"
    />

    <div class="grid gap-3 md:grid-cols-8">
        <flux:callout
            color="sky"
            icon="code-xml"
        >

            <flux:callout.heading>
                {{ __('Files scanned') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $audit['files_scanned'] ?? 0 }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="rose"
            icon="bug"
        >
            <flux:callout.heading>
                {{ __('Open findings') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $historyCounts['open'] ?? 0 }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="amber"
            icon="code-xml"
        >
            <flux:callout.heading>
                {{ __('Native HTML') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $nativeSection['problem_count'] ?? 0 }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            color="violet"
            icon="component"
        >
            <flux:callout.heading>
                {{ __('Custom components') }}
            </flux:callout.heading>

            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $customSection['problem_count'] ?? 0 }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-2 min-h-32"
            color="zinc"
            icon="list-filter"
        >
            <flux:callout.heading>
                {{ __('Finding history') }}
            </flux:callout.heading>

            <flux:callout.text>
                <div class="flex flex-wrap items-center gap-2">
                    <flux:badge
                        color="red"
                        variant="subtle"
                    >
                        {{ __('admin.translation_list.filter.open') }}: {{ $historyCounts['open'] ?? 0 }}
                    </flux:badge>

                    <flux:badge
                        color="amber"
                        variant="subtle"
                    >
                        {{ __('Changed') }}: {{ $historyCounts['changed'] ?? 0 }}
                    </flux:badge>

                    <flux:badge
                        color="green"
                        variant="subtle"
                    >
                        {{ __('Resolved') }}: {{ $historyCounts['resolved'] ?? 0 }}
                    </flux:badge>

                    <flux:badge
                        color="zinc"
                        variant="subtle"
                    >
                        {{ __('Ignored') }}: {{ $historyCounts['ignored'] ?? 0 }}
                    </flux:badge>

                    <flux:badge variant="subtle">
                        {{ __('Total') }}: {{ $historyCounts['total'] ?? 0 }}
                    </flux:badge>
                </div>
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-2 min-h-32"
            color="zinc"
            icon="cable"
        >
            <flux:callout.heading>
                {{ __('Audit file') }}
            </flux:callout.heading>

            <flux:callout.text>
                <div class="space-y-2">
                    <div class="gap-2">
                        <flux:field class="grid grid-cols-4 items-center">
                            <div class="font-semibold">{{ __('admin.translation_list.modal.source') }}:</div>
                            <div class="col-span-3">{{ $audit['path'] ?? 'storage/audits/html/view-html-check.json' }}
                            </div>
                        </flux:field>

                        @if (!empty($audit['generated_at']))
                            <flux:field class="grid grid-cols-4 items-center">
                                <div class="font-semibold">{{ __('Generated') }}:</div>
                                <div class="col-span-3">{{ $audit['generated_at'] }}</div>
                            </flux:field>
                        @endif

                        @if ($hasActiveFilters)
                            <flux:field class="grid grid-cols-4 items-center">
                                <div class="col-span-1 font-semibold">{{ __('admin.translation_list.meta.filtered') }}:</div>
                                <flux:badge
                                    color="amber"
                                    variant="subtle"
                                    size="sm"
                                >
                                    {{ $filteredProblemCount }}
                                </flux:badge>
                            </flux:field>
                        @endif
                    </div>

                    @if (!($audit['exists'] ?? false))
                        <div class="text-sm text-amber-700 dark:text-amber-300">
                            {{ __('Run php artisan html:check.') }}
                        </div>
                    @endif
                </div>
            </flux:callout.text>
        </flux:callout>

        @if (is_array($referenceDisplay))
            <flux:callout
                class="col-span-3 min-h-32"
                icon="code-xml"
                :color="($referenceIsMissing || $referenceHasFallback) ? 'amber' : 'green'"
            >
                <flux:callout.heading>
                    {{ __('Native HTML reference source') }}
                </flux:callout.heading>

                <flux:callout.text>
                    <div class="grid gap-x-4 gap-y-1 text-sm md:grid-cols-[auto_1fr]">
                        <div class="font-semibold">{{ __('admin.translation_list.modal.source') }}:</div>
                        <div>
                            {{ $referenceDisplay['source_name'] ?? ($referenceDisplay['source'] ?? 'n/a') }}
                        </div>

                        @if (!empty($referenceDisplay['generated_at']))
                            <div class="font-semibold">{{ __('Generated') }}:</div>
                            <div>{{ $referenceDisplay['generated_at'] }}</div>
                        @endif

                        @if (!empty($referenceDisplay['hint']) || !empty($nativeReference['fallback_hint']))
                            <div class="font-semibold">
                                {{ __('Hint') }}:
                            </div>
                            <div>
                                {{ $referenceDisplay['hint'] ?? $nativeReference['fallback_hint'] }}
                            </div>
                        @endif

                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-3 min-h-32"
                icon="cable"
                :color="($referenceIsMissing || $referenceHasFallback) ? 'amber' : 'green'"
            >
                <flux:callout.heading>
                    {{ __('Native HTML reference target') }}
                </flux:callout.heading>

                <flux:callout.text>
                    <div class="grid gap-x-4 gap-y-1 text-sm md:grid-cols-[auto_1fr]">
                        <div class="font-semibold">{{ __('Target') }}:</div>
                        <div>
                            {{ $referenceDisplay['path'] ?? ($referenceDisplay['source'] ?? 'n/a') }}
                        </div>

                        @if (!empty($referenceDisplay['file_written_at']))
                            <div class="font-semibold">{{ __('File written') }}:</div>
                            <div>{{ $referenceDisplay['file_written_at'] }}</div>
                        @endif
                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2 min-h-32"
                icon="list-filter"
                :color="($referenceIsMissing || $referenceHasFallback) ? 'amber' : 'green'"
            >
                <flux:callout.heading>
                    {{ __('Native HTML reference status') }}
                </flux:callout.heading>

                <flux:callout.text>
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:badge variant="subtle">
                                {{ __('Normal tags') }}: {{ $referenceDisplay['normal_count'] ?? 0 }}
                            </flux:badge>

                            <flux:badge variant="subtle">
                                {{ __('Void tags ignored') }}: {{ $referenceDisplay['void_count'] ?? 0 }}
                            </flux:badge>

                            @if (array_key_exists('total_count', $referenceDisplay))
                                <flux:badge variant="subtle">
                                    {{ __('Total tags') }}: {{ $referenceDisplay['total_count'] ?? 0 }}
                                </flux:badge>
                            @endif

                            <flux:badge
                                :color="($referenceIsMissing || $referenceHasFallback) ? 'amber' : 'green'"
                                variant="subtle"
                            >
                                {{ __('Fallback') }}: {{ $referenceHasFallback ? __('yes') : __('no') }}
                            </flux:badge>
                        </div>

                        @if ($referenceHasFallback)
                            <div class="text-sm text-amber-700 dark:text-amber-300">
                                {{ __('admin.translation_list.modal_history.reason') }}: {{ $nativeReference['fallback_reason'] ?? 'n/a' }}
                            </div>
                        @endif

                    </div>
                </flux:callout.text>
            </flux:callout>
        @endif

    </div>
</flux:card>
