{{-- packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-actions.blade.php --}}

{{-- Table Findings Cell Actions --}}
<flux:table.cell>
    <div class="flex items-center gap-1.5">
        @if ($bulkSelectableByLiteral || $bulkSelected)
            {{-- Checkbox Bulk Equalize Translation Key --}}
            <x-ui.tooltip.simple
                :title="__('Select for bulk key review')"
                :text="__(
                    'This literal appears :count times. Select matching findings to set one shared translation key after confirmation.',
                    ['count' => $bulkLiteralCount],
                )"
            >
                <flux:checkbox
                    value="{{ $finding->id }}"
                    wire:key="translation-workbench-bulk-equalize-checkbox-{{ $finding->id }}"
                    wire:model.live="bulkEqualizeSelectedFindingIds"
                    :disabled="!$hasKey || (!$bulkSelectableByLiteral && !$bulkSelected)"
                />
            </x-ui.tooltip.simple>
        @else
            <span class="h-4 w-4 shrink-0"></span>
        @endif
        {{-- Button Review Finding --}}
        <flux:button
            type="button"
            size="xs"
            variant="primary"
            color="sky"
            icon="badge-check"
            :aria-label="__('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.table_work_findings.cell_actions.review_finding')"
            wire:click="openReviewModal({{ $finding->id }})"
        />
        {{-- Button Open Modal Edit --}}
        <flux:button
            type="button"
            size="xs"
            variant="{{ $canOpenEditAction ? 'primary' : 'subtle' }}"
            color="{{ $editActionColor }}"
            icon="square-pen"
            :aria-label="$isDynamicFinding ? __(
                'packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings_table.edit_dynamic_translation_values'
            ) : __(
                'Edit translation values')"
            :disabled="!$canOpenEditAction"
            wire:click="openEditModal({{ $finding->id }})"
        />
        {{-- Button Open Modal Timeline --}}
        <flux:button
            type="button"
            size="xs"
            variant="{{ $hasHistory ? 'primary' : 'subtle' }}"
            color="{{ $hasHistory ? 'amber' : 'zinc' }}"
            icon="activity"
            :aria-label="__('packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings_table.show_timeline')"
            :disabled="!$hasHistory"
            wire:click="openTimelineModal({{ $finding->id }})"
        />
    </div>
</flux:table.cell>
