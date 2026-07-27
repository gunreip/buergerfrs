{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings-table.blade.php --}}

<flux:card id="translation-workbench-findings">
    <x-ui.headers.card
        :title="__('Findings')"
        :description="__('Translation-capable code findings from the new Workbench data model.')"
    >
        <div class="flex flex-wrap items-center justify-end gap-2">
            <x-ui.tooltip.simple
                :title="__('Refresh current tab')"
                :text="__('Reloads the currently selected findings tab. Report tabs regenerate their dry-run report where applicable.')"
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
                :text="__('Reloads the findings data and regenerates export, code update plan and apply dry-run reports.')"
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

    <flux:tab.group class="mt-4">
        <flux:tabs wire:model.live="findingsActiveTab">
            <flux:tab name="work-findings">
                {{ __('Work findings') }}
            </flux:tab>
            <flux:tab name="review-last-edits">
                {{ __('Review last edits') }}
            </flux:tab>
            <flux:tab name="export-report">
                {{ __('Export report') }}
            </flux:tab>
            <flux:tab name="last-run">
                {{ __('Last run') }}
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
