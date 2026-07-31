{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings-table.blade.php --}}

<flux:card id="translation-workbench-findings">
    <x-ui.headers.card
        :title="__('Findings')"
        :description="__('Translation-capable code findings from the new Workbench data model.')"
    >
        <div class="flex flex-wrap items-center justify-end gap-2">
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
                    {{ __('Refresh') }}
                </flux:button>
            </x-ui.tooltip.simple>

            <x-ui.tooltip.simple
                :title="__('Refresh all findings tabs')"
                :text="__(
                    'Reloads the findings data and regenerates export, code update plan and apply dry-run reports.',
                )"
            >
                <flux:button
                    type="button"
                    size="sm"
                    variant="subtle"
                    color="cyan"
                    icon="rotate-cw"
                    wire:click="refreshFindingsAllTabs"
                    wire:loading.attr="disabled"
                    wire:target="refreshFindingsAllTabs"
                >
                    {{ __('ui.states.all') }}
                </flux:button>
            </x-ui.tooltip.simple>

            {{-- Reset Button --}}
            <flux:button
                type="button"
                size="sm"
                variant="{{ $findingFiltersActive ? 'primary' : 'subtle' }}"
                color="{{ $findingFiltersActive ? 'cyan' : 'zinc' }}"
                icon="rotate-ccw"
                wire:click="resetFindingFilters"
            >
                {{ __('Reset') }}
            </flux:button>
        </div>
    </x-ui.headers.card>

    @php
        $lastRunSummary = is_array($pipelineRunReport['summary'] ?? null) ? $pipelineRunReport['summary'] : [];
        $lastRunHasBlockers = (bool) ($lastRunSummary['has_blockers'] ?? false);
        $lastRunHasStaleReports = (bool) ($lastRunSummary['has_stale_reports'] ?? false);
        $lastRunNeedsAttention = $lastRunHasBlockers || $lastRunHasStaleReports || !$pipelineRunReport;
        $exportReportConflictCount = (int) ($langFileExportReport['values_conflicted'] ?? 0);
    @endphp

    <flux:tab.group class="mt-4">
        <flux:tabs wire:model.live="findingsActiveTab">
            <flux:tab name="work-findings">
                {{ __('Work findings') }}
            </flux:tab>
            <flux:tab name="review-last-edits">
                {{ __('Review last edits') }}
            </flux:tab>
            {{--
            TODO: Shared key candidates is currently handled inside Work findings. Keep this tab disabled until we decide whether the separate inbox view should be removed permanently. --}}
            {{-- <flux:tab name="shared-key-candidates">
                {{ __('Shared key candidates') }}
            </flux:tab> --}}
            <flux:tab
                class="{{ $exportReportConflictCount > 0 ? 'rounded-md bg-red-500/15 text-red-700 dark:bg-red-500/20 dark:text-red-300' : '' }}"
                name="export-report"
            >
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('Export report') }}</span>
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
                class="{{ $lastRunHasBlockers || !$pipelineRunReport ? 'rounded-md bg-red-500/15 text-red-700 dark:bg-red-500/20 dark:text-red-300' : ($lastRunHasStaleReports ? 'rounded-md bg-amber-500/15 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' : '') }}"
                name="last-run"
            >
                <span class="inline-flex items-center gap-1">
                    <span>{{ __('Last run') }}</span>
                    @if ($lastRunNeedsAttention)
                        <flux:badge
                            size="sm"
                            color="{{ $lastRunHasBlockers || !$pipelineRunReport ? 'red' : 'amber' }}"
                        >
                            {{ $lastRunHasBlockers || !$pipelineRunReport ? __('Error') : __('ui.stale') }}
                        </flux:badge>
                    @endif
                </span>
            </flux:tab>
            <flux:tab name="code-update-plan">
                {{ __('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.code_update_plan') }}
            </flux:tab>
        </flux:tabs>

        <flux:tab.panel name="work-findings">
            @include('translation-workbench::livewire.entries.findings.table-work-filters')
            @include('translation-workbench::livewire.entries.findings.table-work-findings')
        </flux:tab.panel>

        <flux:tab.panel name="review-last-edits">
            @include('translation-workbench::livewire.entries.findings.review-last-edits')
        </flux:tab.panel>

        {{--
        TODO: Shared key candidates is currently handled inside Work findings. Keep this panel disabled until we decide whether the separate inbox view should be removed permanently. --}}
        {{-- <flux:tab.panel name="shared-key-candidates">
            @include('translation-workbench::livewire.entries.findings.shared-key-candidates')
        </flux:tab.panel> --}}

        <flux:tab.panel name="export-report">
            @include('translation-workbench::livewire.entries.findings.export-report')
        </flux:tab.panel>

        <flux:tab.panel name="last-run">
            @include('translation-workbench::livewire.entries.findings.last-run')
        </flux:tab.panel>

        <flux:tab.panel name="code-update-plan">
            @include('translation-workbench::livewire.entries.findings.code-update-plan')
        </flux:tab.panel>
    </flux:tab.group>
</flux:card>
