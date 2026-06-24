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
            class="hyphens-auto"
            color="sky"
            icon="code-xml"
            heading="{{ __('Files scanned') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $audit['files_scanned'] ?? 0 }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="hyphens-auto"
            color="rose"
            icon="bug"
            heading="{{ __('Open findings') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $historyCounts['open'] ?? 0 }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="hyphens-auto"
            color="amber"
            icon="code-xml"
            heading="{{ __('Native HTML') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $nativeSection['problem_count'] ?? 0 }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="hyphens-auto"
            color="violet"
            icon="component"
            heading="{{ __('Custom components') }}"
        >
            <flux:callout.text class="text-2xl! font-semibold tabular-nums">
                {{ $customSection['problem_count'] ?? 0 }}
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-2 min-h-32 hyphens-auto"
            color="zinc"
            icon="list-filter"
            heading="{{ __('Finding history') }}"
        >
            <flux:callout.text>
                <div class="flex flex-wrap items-center gap-2">
                    <flux:badge
                        color="red"
                        variant="subtle"
                        :label="__('admin.translation_list.filter.open').
                        ':'.$historyCounts['open']"
                    />
                    <flux:badge
                        color="amber"
                        variant="subtle"
                        :label="__('Changed').
                        ': '.($historyCounts['changed'] ?? 0)"
                    />
                    <flux:badge
                        color="green"
                        variant="subtle"
                        :label="__('Resolved').
                        ': '.($historyCounts['resolved'] ?? 0)"
                    />
                    <flux:badge
                        color="zinc"
                        variant="subtle"
                        :label="__('Ignored').
                        ': '.($historyCounts['ignored'] ?? 0)"
                    />
                    <flux:badge
                        variant="subtle"
                        :label="__('Total').
                        ': '.($historyCounts['total'] ?? 0)"
                    />
                </div>
            </flux:callout.text>
        </flux:callout>

        <flux:callout
            class="col-span-2 min-h-32 hyphens-auto"
            color="zinc"
            icon="cable"
            heading="{{ __('Audit file') }}"
        >
            <flux:callout.text>
                <div class="space-y-2">
                    <div class="gap-2">
                        <flux:field class="grid grid-cols-4 items-center">
                            <div class="font-semibold">
                                {{ __('admin.translation_list.modal.source') }}:
                            </div>
                            <div class="col-span-3">
                                {{ $audit['path'] ?? 'storage/audits/html/view-html-check.json' }}
                            </div>
                        </flux:field>

                        @if (!empty($audit['generated_at']))
                            <flux:field class="grid grid-cols-4 items-center">
                                <div class="font-semibold">
                                    {{ __('Generated') }}:
                                </div>
                                <div class="col-span-3">
                                    {{ $audit['generated_at'] }}
                                </div>
                            </flux:field>
                        @endif

                        @if ($hasActiveFilters)
                            <flux:field class="grid grid-cols-4 items-center">
                                <div class="col-span-1 font-semibold">
                                    {{ __('admin.translation_list.meta.filtered') }}:
                                </div>
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
                class="col-span-3 min-h-32 hyphens-auto"
                icon="code-xml"
                :color="($referenceIsMissing || $referenceHasFallback) ? 'amber' : 'green'"
                heading="{{ __('Native HTML reference source') }}"
            >
                <flux:callout.text>
                    <div class="grid gap-x-4 gap-y-1 text-sm md:grid-cols-[auto_1fr]">
                        <div class="font-semibold">
                            {{ __('admin.translation_list.modal.source') }}:
                        </div>
                        <div>
                            {{ $referenceDisplay['source_name'] ?? ($referenceDisplay['source'] ?? 'n/a') }}
                        </div>

                        @if (!empty($referenceDisplay['generated_at']))
                            <div class="font-semibold">
                                {{ __('Generated') }}:
                            </div>
                            <div>
                                {{ $referenceDisplay['generated_at'] }}
                            </div>
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
                class="col-span-3 min-h-32 hyphens-auto"
                icon="cable"
                :color="($referenceIsMissing || $referenceHasFallback) ? 'amber' : 'green'"
                heading="{{ __('Native HTML reference target') }}"
            >
                <flux:callout.text>
                    <div class="grid gap-x-4 gap-y-1 text-sm md:grid-cols-[auto_1fr]">
                        <div class="font-semibold">
                            {{ __('Target') }}:
                        </div>
                        <div>
                            {{ $referenceDisplay['path'] ?? ($referenceDisplay['source'] ?? 'n/a') }}
                        </div>

                        @if (!empty($referenceDisplay['file_written_at']))
                            <div class="font-semibold">
                                {{ __('File written') }}:
                            </div>
                            <div>
                                {{ $referenceDisplay['file_written_at'] }}
                            </div>
                        @endif
                    </div>
                </flux:callout.text>
            </flux:callout>

            <flux:callout
                class="col-span-2 min-h-32 hyphens-auto"
                icon="list-filter"
                :color="($referenceIsMissing || $referenceHasFallback) ? 'amber' : 'green'"
                heading="{{ __('Native HTML reference status') }}"
            >
                <flux:callout.text>
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:badge
                                variant="subtle"
                                :label="__('Normal tags').
                                ':'.$referenceDisplay['normal_count'] ?? 0"
                            />

                            <flux:badge
                                variant="subtle"
                                :label="__('Void tags ignored').
                                ':'.$referenceDisplay['void_count'] ?? 0"
                            />

                            @if (array_key_exists('total_count', $referenceDisplay))
                                <flux:badge
                                    variant="subtle"
                                    :label="__('Total tags').
                                    ':'.$referenceDisplay['total_count'] ?? 0"
                                />
                            @endif

                            <flux:badge
                                :color="($referenceIsMissing || $referenceHasFallback) ? 'amber' : 'green'"
                                variant="subtle"
                                :label="__('Fallback').
                                ':'.$referenceHasFallback ? __('yes') : __('no')"
                            />
                        </div>

                        @if ($referenceHasFallback)
                            <div class="text-sm text-amber-700 dark:text-amber-300">
                                {{ __('admin.translation_list.modal_history.reason') }}:
                                {{ $nativeReference['fallback_reason'] ?? 'n/a' }}
                            </div>
                        @endif

                    </div>
                </flux:callout.text>
            </flux:callout>
        @endif

    </div>
</flux:card>
