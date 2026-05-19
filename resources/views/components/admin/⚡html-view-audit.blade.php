<div x-data="{
    statisticOpen: false,
    usageDetailsOpen: false,
}">
    <flux:card>
        <x-ui.headers.page
            :title="__('HTML-Tags-Check')"
            :description="__('Static audit for native HTML tags and selected Flux/custom Blade components.')"
        />

        @include('components.admin.partials.html-view-audit.⚡meta', [
            'audit' => $audit,
            'historyCounts' => $historyCounts,
            'filteredProblemCount' => $filteredProblemCount,
            'hasActiveFilters' => $hasActiveFilters,
        ])

        @include('components.admin.partials.html-view-audit.⚡filter')

        @include('components.admin.partials.html-view-audit.⚡statistic', [
            'statistics' => $statistics,
        ])

        @include('components.admin.partials.html-view-audit.⚡usage-audit', [
            'usageAudit' => $usageAudit,
        ])

        @include('components.admin.partials.html-view-audit.⚡table', [
            'problems' => $problems,
        ])

        @include('components.admin.partials.html-view-audit.⚡modal', [
            'selectedFinding' => $selectedFinding,
            'tableLegend' => $tableLegend,
        ])
    </flux:card>
</div>
