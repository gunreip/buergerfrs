{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings-table.blade.php --}}

@php
    $pipelineRunStatus = $pipelineRunStatus ?? null;
    $pipelineRunActive = (bool) ($pipelineRunStatus['is_active'] ?? false);
@endphp

<flux:card id="translation-workbench-findings">
    <x-ui.headers.card
        :title="__('Findings')"
        :description="__('Translation-capable code findings from the new Workbench data model.')"
    >
        <div class="flex flex-wrap items-center justify-end gap-2">
            {{--
            TODO: Add a "Run complete" button to start the complete Translation Workbench pipeline in a background process. This will run the following command in the background: php artisan translation:workbench --complete. The current pipeline step will be tracked in the database and displayed in the "Last run" tab. --}}
            {{-- Run complete Translation Workbench pipeline in a background process. --}}
            {{-- <x-ui.tooltip.simple
                :title="__('Run complete workbench')"
                :text="__(
                    'Starts php artisan translation:workbench --complete in the background and tracks the current pipeline step in the database.',
                )"
            >
                <flux:button
                    type="button"
                    size="sm"
                    variant="primary"
                    color="{{ $pipelineRunActive ? 'zinc' : 'green' }}"
                    icon="{{ $pipelineRunActive ? 'loader-circle' : 'play' }}"
                    wire:click="startCompletePipelineRun"
                    wire:loading.attr="disabled"
                    wire:target="startCompletePipelineRun"
                    :disabled="$pipelineRunActive"
                >
                    {{ $pipelineRunActive ? __('Pipeline running') : __('Run complete') }}
                </flux:button>
            </x-ui.tooltip.simple>

            <flux:separator
                vertical
            /> --}}

            {{-- Refresh Tab Button, refreshes the currently selected findings table --}}
            <x-ui.tooltip.simple
                :title="__('Refresh current tab')"
                :text="__(
                    'Reloads the currently selected findings tab. Report tabs regenerate their dry-run report where applicable.',
                )"
            >
                <flux:button
                    type="button"
                    size="sm"
                    variant="subtle"
                    color="sky"
                    icon="refresh-cw"
                    wire:click="refreshFindingsCurrentTab"
                    wire:loading.attr="disabled"
                    wire:target="refreshFindingsCurrentTab"
                >
                    {{ __('Refresh table') }}
                </flux:button>
            </x-ui.tooltip.simple>

            {{-- Reset Filters Button, resets all filters for the currently selected findings table --}}
            <x-ui.tooltip.simple
                :title="__('Reset current tab filters')"
                :text="__('Resets only the filters that belong to the currently selected findings tab.')"
            >
                <flux:button
                    type="button"
                    size="sm"
                    variant="{{ $currentFindingsTabFiltersActive ? 'primary' : 'subtle' }}"
                    color="{{ $currentFindingsTabFiltersActive ? 'cyan' : 'zinc' }}"
                    icon="rotate-ccw"
                    wire:click="resetFindingFilters"
                >
                    {{ __('Reset filters') }}
                </flux:button>
            </x-ui.tooltip.simple>

            <flux:separator
                {{-- class="my-1" --}}
                vertical
            />

            {{-- Refresh all findings tabs: This button refreshes all findings tabs, table and filters --}}
            <x-ui.tooltip.simple
                :title="__('Refresh all findings tabs')"
                :text="__(
                    'Reloads all findings tabs and regenerates the related report data where applicable. A highlighted button indicates that at least one findings tab has active filters.',
                )"
            >
                <flux:button
                    type="button"
                    size="sm"
                    variant="{{ $findingsTabFiltersActive ? 'primary' : 'subtle' }}"
                    color="{{ $findingsTabFiltersActive ? 'cyan' : 'zinc' }}"
                    icon="rotate-cw"
                    wire:click="refreshFindingsAllTabs"
                    wire:loading.attr="disabled"
                    wire:target="refreshFindingsAllTabs"
                >
                    {{ __('Refresh all tabs') }}
                </flux:button>
            </x-ui.tooltip.simple>
        </div>
    </x-ui.headers.card>

    {{-- Unfortunately, this keeps causing a "Failed to open stream: Permission denied" error. --}}
    {{-- @include('translation-workbench::livewire.entries.findings.pipeline-run-progress') --}}

    @php
        $lastRunSummary = is_array($pipelineRunReport['summary'] ?? null) ? $pipelineRunReport['summary'] : [];
        $lastRunHasBlockers = (bool) ($lastRunSummary['has_blockers'] ?? false);
        $lastRunHasStaleReports = (bool) ($lastRunSummary['has_stale_reports'] ?? false);
        $lastRunNeedsAttention = $lastRunHasBlockers || $lastRunHasStaleReports || !$pipelineRunReport;
        $exportReportConflictCount = (int) ($langFileExportReport['values_conflicted'] ?? 0);
        $suspiciousKeyCount = (int) ($suspiciousKeyedAdditionsReport['summary']['suspicious'] ?? 0);
        $findingsTabDescriptions = [
            'work-findings' => __(
                'Main work list for scanner findings, review state, keys, literals and translation actions.',
            ),
            'review-last-edits' => __(
                'Compact review of recently completed translation edits with only the key source and target values.',
            ),
            'key-paths' => __(
                'Shows existing active lang-value translation key paths for the selected namespace and translation value search, without the final key segment.',
            ),
            'lang-cleanup' => __(
                'Lang-file values that are no longer backed by active source-code usage and may need cleanup review.',
            ),
            'cleanup-history' => __('ui.remove.removed.completed-lang-cleanup-actions-including-removed-or-moved-translation-values'),
            'scalar-review' => __(
                'Scalar translation keys that may need restructuring before they collide with deeper array keys.',
            ),
            'export-report' => __('Latest lang-file export report with missing values, conflicts and write readiness.'),
            'suspicious-keys' => __(
                'Code locations where a new literal may have been written as a translation key by mistake.',
            ),
            'last-run' => __('Most recent orchestrator run with step status, warnings and command results.'),
            'code-update-plan' => __(
                'Preview and review of source-code replacements before reviewed keys are written into code.',
            ),
        ];
    @endphp

    <flux:tab.group class="mt-4">
        <flux:tabs wire:model.live="findingsActiveTab">
            <flux:tab
                name="work-findings"
                wire:click="selectFindingsTab('work-findings')"
            >
                <span class="inline-flex items-center gap-1.5">
                    <span>{{ __('Work findings') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Work findings')"
                        :text="$findingsTabDescriptions['work-findings']"
                    />
                </span>
            </flux:tab>
            <flux:tab
                name="review-last-edits"
                wire:click="selectFindingsTab('review-last-edits')"
            >
                <span class="inline-flex items-center gap-1.5">
                    <span>{{ __('Review last edits') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Review last edits')"
                        :text="$findingsTabDescriptions['review-last-edits']"
                    />
                </span>
            </flux:tab>
            <flux:tab
                name="key-paths"
                wire:click="selectFindingsTab('key-paths')"
            >
                <span class="inline-flex items-center gap-1.5">
                    <span>{{ __('Key paths') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Key paths')"
                        :text="$findingsTabDescriptions['key-paths']"
                    />
                </span>
            </flux:tab>
            <flux:tab
                class="{{ ($langCleanupCandidateCount ?? 0) > 0 ? 'rounded-md bg-amber-500/15 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' : '' }}"
                name="lang-cleanup"
                wire:click="selectFindingsTab('lang-cleanup')"
            >
                <span class="inline-flex items-center gap-1.5">
                    <span>{{ __('Lang cleanup') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Lang cleanup')"
                        :text="$findingsTabDescriptions['lang-cleanup']"
                    />
                    @if (($langCleanupCandidateCount ?? 0) > 0)
                        <flux:badge
                            size="sm"
                            color="amber"
                        >
                            {{ number_format((int) $langCleanupCandidateCount) }}
                        </flux:badge>
                    @endif
                </span>
            </flux:tab>
            <flux:tab
                class="{{ ($cleanupHistoryCount ?? 0) > 0 ? 'rounded-md bg-green-500/15 text-green-700 dark:bg-green-500/20 dark:text-green-300' : '' }}"
                name="cleanup-history"
                wire:click="selectFindingsTab('cleanup-history')"
            >
                <span class="inline-flex items-center gap-1.5">
                    <span>{{ __('Cleanup history') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Cleanup history')"
                        :text="$findingsTabDescriptions['cleanup-history']"
                    />
                    @if (($cleanupHistoryCount ?? 0) > 0)
                        <flux:badge
                            size="sm"
                            color="green"
                        >
                            {{ number_format((int) $cleanupHistoryCount) }}
                        </flux:badge>
                    @endif
                </span>
            </flux:tab>
            <flux:tab
                class="{{ ($scalarReviewCount ?? 0) > 0 ? 'rounded-md bg-amber-500/15 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' : '' }}"
                name="scalar-review"
                wire:click="selectFindingsTab('scalar-review')"
            >
                <span class="inline-flex items-center gap-1.5">
                    <span>{{ __('Scalar review') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Scalar review')"
                        :text="$findingsTabDescriptions['scalar-review']"
                    />
                    @if (($scalarReviewCount ?? 0) > 0)
                        <flux:badge
                            size="sm"
                            color="amber"
                        >
                            {{ number_format((int) $scalarReviewCount) }}
                        </flux:badge>
                    @endif
                </span>
            </flux:tab>
            {{--
            TODO: Shared key candidates is currently handled inside Work findings. Keep this tab disabled until we decide whether the separate inbox view should be removed permanently. --}}
            {{-- <flux:tab name="shared-key-candidates">
                {{ __('Shared key candidates') }}
            </flux:tab> --}}
            <flux:tab
                class="{{ $exportReportConflictCount > 0 ? 'rounded-md bg-red-500/15 text-red-700 dark:bg-red-500/20 dark:text-red-300' : '' }}"
                name="export-report"
                wire:click="selectFindingsTab('export-report')"
            >
                <span class="inline-flex items-center gap-1.5">
                    <span>{{ __('Export report') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Export report')"
                        :text="$findingsTabDescriptions['export-report']"
                    />
                    @if ($exportReportConflictCount > 0)
                        <flux:badge
                            size="sm"
                            color="red"
                        >
                            {{ __('Conflicts') }}: {{ number_format($exportReportConflictCount) }}
                        </flux:badge>
                    @endif
                </span>
            </flux:tab>
            <flux:tab
                class="{{ $suspiciousKeyCount > 0 ? 'rounded-md bg-red-500/15 text-red-700 dark:bg-red-500/20 dark:text-red-300' : '' }}"
                name="suspicious-keys"
                wire:click="selectFindingsTab('suspicious-keys')"
            >
                <span class="inline-flex items-center gap-1.5">
                    <span>{{ __('Suspicious keys') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Suspicious keys')"
                        :text="$findingsTabDescriptions['suspicious-keys']"
                    />
                    @if ($suspiciousKeyCount > 0)
                        <flux:badge
                            size="sm"
                            color="red"
                        >
                            {{ number_format($suspiciousKeyCount) }}
                        </flux:badge>
                    @endif
                </span>
            </flux:tab>
            <flux:tab
                class="{{ $lastRunHasBlockers || !$pipelineRunReport ? 'rounded-md bg-red-500/15 text-red-700 dark:bg-red-500/20 dark:text-red-300' : ($lastRunHasStaleReports ? 'rounded-md bg-amber-500/15 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' : '') }}"
                name="last-run"
                wire:click="selectFindingsTab('last-run')"
            >
                <span class="inline-flex items-center gap-1.5">
                    <span>{{ __('Last run') }}</span>
                    <x-ui.tooltip.simple
                        :header="__('Last run')"
                        :text="$findingsTabDescriptions['last-run']"
                    />
                    @if ($lastRunNeedsAttention)
                        <flux:badge
                            size="sm"
                            color="{{ $lastRunHasBlockers || !$pipelineRunReport ? 'red' : 'amber' }}"
                        >
                            {{ $lastRunHasBlockers || !$pipelineRunReport ? __('Error') : __('ui.stale.stale') }}
                        </flux:badge>
                    @endif
                </span>
            </flux:tab>
            <flux:tab
                name="code-update-plan"
                wire:click="selectFindingsTab('code-update-plan')"
            >
                <span class="inline-flex items-center gap-1.5">
                    <span>{{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.code_update_plan') }}</span>
                    <x-ui.tooltip.simple
                        :header="__(
                            'packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.code_update_plan',
                        )"
                        :text="$findingsTabDescriptions['code-update-plan']"
                    />
                </span>
            </flux:tab>
        </flux:tabs>

        <flux:tab.panel name="work-findings">
            @include('translation-workbench::livewire.entries.findings.table-work-filters')
            @include('translation-workbench::livewire.entries.findings.table-work-findings')
        </flux:tab.panel>

        <flux:tab.panel name="review-last-edits">
            @if ($findingsTabLoaded['review-last-edits'] ?? false)
                @include('translation-workbench::livewire.entries.findings.review-last-edits')
            @else
                @include('translation-workbench::livewire.entries.findings.lazy-placeholder', [
                    'label' => __('Review last edits'),
                ])
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="key-paths">
            @if ($findingsTabLoaded['key-paths'] ?? false)
                @include('translation-workbench::livewire.entries.findings.key-paths')
            @else
                @include('translation-workbench::livewire.entries.findings.lazy-placeholder', [
                    'label' => __('Key paths'),
                ])
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="lang-cleanup">
            @if ($findingsTabLoaded['lang-cleanup'] ?? false)
                @include('translation-workbench::livewire.entries.findings.lang-cleanup')
            @else
                @include('translation-workbench::livewire.entries.findings.lazy-placeholder', [
                    'label' => __('Lang cleanup'),
                ])
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="cleanup-history">
            @if ($findingsTabLoaded['cleanup-history'] ?? false)
                @include('translation-workbench::livewire.entries.findings.cleanup-history')
            @else
                @include('translation-workbench::livewire.entries.findings.lazy-placeholder', [
                    'label' => __('Cleanup history'),
                ])
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="scalar-review">
            @if ($findingsTabLoaded['scalar-review'] ?? false)
                @include('translation-workbench::livewire.entries.findings.scalar-review')
            @else
                @include('translation-workbench::livewire.entries.findings.lazy-placeholder', [
                    'label' => __('Scalar review'),
                ])
            @endif
        </flux:tab.panel>

        {{--
        TODO: Shared key candidates is currently handled inside Work findings. Keep this panel disabled until we decide whether the separate inbox view should be removed permanently. --}}
        {{-- <flux:tab.panel name="shared-key-candidates">
            @include('translation-workbench::livewire.entries.findings.shared-key-candidates')
        </flux:tab.panel> --}}

        <flux:tab.panel name="export-report">
            @if ($findingsTabLoaded['export-report'] ?? false)
                @include('translation-workbench::livewire.entries.findings.export-report')
            @else
                @include('translation-workbench::livewire.entries.findings.lazy-placeholder', [
                    'label' => __('Export report'),
                ])
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="suspicious-keys">
            @if ($findingsTabLoaded['suspicious-keys'] ?? false)
                @include('translation-workbench::livewire.entries.findings.suspicious-keys')
            @else
                @include('translation-workbench::livewire.entries.findings.lazy-placeholder', [
                    'label' => __('Suspicious keys'),
                ])
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="last-run">
            @if ($findingsTabLoaded['last-run'] ?? false)
                @include('translation-workbench::livewire.entries.findings.last-run')
            @else
                @include('translation-workbench::livewire.entries.findings.lazy-placeholder', [
                    'label' => __('Last run'),
                ])
            @endif
        </flux:tab.panel>

        <flux:tab.panel name="code-update-plan">
            @if ($findingsTabLoaded['code-update-plan'] ?? false)
                @include('translation-workbench::livewire.entries.findings.code-update-plan')
            @else
                @include('translation-workbench::livewire.entries.findings.lazy-placeholder', [
                    'label' => __(
                        'packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.code_update_plan'),
                ])
            @endif
        </flux:tab.panel>
    </flux:tab.group>
</flux:card>
